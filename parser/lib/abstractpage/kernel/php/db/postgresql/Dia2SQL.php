<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * There are few programs on Linux to convert UML diagrams into SQL instructions.
 * This way, I decided to write a php class to read a DIA diagram and generate
 * the correspondent SQL instructions to create Database.
 *
 * Usage:
 *
 * $dia2sql = new Dia2SQL( 'example.dia' );
 *
 * A "sql" directory will be created with respective files containing the Database
 * creation instructions.
 *
 * Features:
 *
 * DIA (http://hans.breuer.org/dia) is a gtk+ program to create diagrams. It can be used
 * to draw many sort of diagrams, including UML diagrams. However, as the most of programs,
 * DIA generates only the diagram, It doesn't export SQL instructions.
 * The Dia2SQL class is a tool that convert this UML diagram into SQL. As Dia files are
 * saved in XML format, is possible to read these files and create a DataBase structure easily.
 * This first version supports only PostgreSQL.
 *
 * Configuration:
 *
 * In order to use the class the right way is needed to foolow some conventions during the
 * diagram creation. This first version supports only classes (tables) and associations
 * (references). You gotta save the diagram in XML format.
 *
 * Classes:
 *
 * - The class name is always the table name.
 * - The table fields are inserted as attributes.
 * - The attributes (fields) can be:
 *		'+' -> 'Public': Simple field.
 *		'#' -> 'Protected': Primary key;
 *		'-' -> 'Privated': Creates an individual index and another one grouped with all others
 *				marked with this option.
 *		' ' -> 'Implementation': Creates a unique index grouped with another ones marked with 
 *				this option and one index to each individual field;
 * - The Option Class Scope is used to define whether the field is a sequence or not;
 *
 * Associations:
 *
 * - The references are done with "Associate to UML" option. You must link two fields of
 *   tables and show the origin and the target of the relation. It's important to stablish
 *   the link of the fields exactly at the points near of each field.
 *
 * @package db_postgresql
 */

class Dia2SQL extends PEAR
{
	/**
	 * @access public
	 */
	var $path = 'sql';
	
	/**
	 * @access public
	 */
	var $UML  = array(
		0  => 'Class',             // implemented
		1  => 'Association',       // implemented
		2  => 'Generalization',
		3  => 'Dependency',
		4  => 'Ralizes',
		5  => 'Implements',
		6  => 'Note',
		7  => 'Constraint',
		8  => 'SmallPackage',
		9  => 'LargePackage',
		10 => 'Actor',
		11 => 'Usecase',
		12 => 'Lifeline',
		14 => 'Object',
		15 => 'Message',
		16 => 'Component',
		17 => 'Node',
		18 => 'Classicon',
		19 => 'State',
		20 => 'Branch'
	);
	
	/**
	 * @access public
	 */
    var $file;
	
	/**
	 * @access public
	 */
    var $string;
	
	/**
	 * @access public
	 */
    var $struct;
	
	/**
	 * @access public
	 */
    var $sql;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function Dia2SQL( $file )
    {
        $this->file = $file;

        $this->generate();
        $this->collectStruct();
    }


	/**
	 * @access public
	 */	
    function generate()
    {
        $fp = fopen( $this->file, 'r' );
		
        while ( !feof( $fp ) )
            $this->string[] = fgets( $fp, 4096 );
        
		fclose( $fp );
    }

	/**
	 * @access public
	 */
    function collectStruct()
    {
        foreach ( $this->string as $s )
        {
            // UML types definition
            $key_idobject = true? stristr( $s, 'object type' ) : false;
            
			if ( $key_idobject )
            {
                $id = substr( $s, strpos( $s, 'id' ) + 4, strrpos( $s, '">' ) - strpos( $s, 'id' ) - 4 );
                $type_struct = substr( $s, strpos( $s, 'UML' ) + 6, strrpos( $s, 'version' ) - strpos( $s, 'UML' ) - 8 );
                $this->struct[$id]['Type'] = $type_struct;
                $end_obj = false;
            }

            // Type Class
            if ( $key_class )
			{
                if ( stristr( $s,'object' ) )
                    $key_class = false;
			}
			
            if ( stristr( $s, 'UML - Class' ) || $key_class )
            {
                $key_class = true;

                if ( $name_key && !$attributes_key)
                    $name = substr( $s, strpos( $s, '#' ) + 1, strrpos( $s, '#' ) - strpos( $s, '#' ) - 1 );

                $name_key = stristr( $s, 'name="name"' )? true : false;

                if ( $name )
                {
                    if ( stristr( $s, 'composite type="umlattribute"' ) )
                        $attributes_key = true;
                    
					if ( stristr( $s, 'composite>' ) )
                        $attributes_key = false;

                    if ( $attributes_key )
                    {
                        // fields
                        if ( $field_key )
                            $field = substr( $s, strpos( $s, '#' ) + 1, strrpos( $s, '#' ) - strpos( $s, '#' ) - 1 );

                        $field_key = stristr( $s, 'name="name"' )? true : false;

                        // types
                        if ( $type_key )
                        {
                            $type = substr( $s, strpos( $s, '#' ) + 1, strrpos( $s, '#' ) - strpos( $s, '#' ) - 1 );
                            
							// default type is text
                            if ( !$type )
                                $type = 'text';
                        }
						
                        $type_key = stristr( $s, 'name="type"' )? true : false;

                        // values
                        if ( $value_key )
                        {
                            $value = substr( $s, strpos( $s, '#' ) + 1, strrpos( $s, '#' ) - strpos( $s, '#' ) - 1 );
                            
							if ( !$value || stristr( $value, 'string/>' ) )
                                $value = ' ';
                        }
						
                        $value_key = stristr( $s, 'name="value"' )? true : false;

                        // sequences
                        if ( $sequence_key )
							$sequence = substr( $s, strpos( $s, '"' ) + 1, strrpos( $s, '"' ) - strpos( $s, '"' ) - 1 );
                        
                        $sequence_key = stristr( $s, 'name="class_scope"' )? true : false;

                        // type of field (primary key, refrences ... )
                        if ( $type_field_key )
                        {
                            $type_field  = substr( $s, strpos( $s, '"' ) + 1, strrpos( $s, '"' ) - strpos( $s, '"' ) - 1 );
                            $primary_key = $primary_key? $primary_key : 'false';
                            $index = $index? $index : 'false';
                            $index_unique = $index_unique? $index_unique : 'false';
                            
							switch ( $type_field )
                            {
                                case '1' :
                                    $index_unique = 'true';
                                    break;
                                
								case '2' :
                                    $primary_key = 'true';
                                    break;
                                
								case '3' :
                                    $index = 'true';
                                    break;
                            }
                        }
						
                        $type_field_key = stristr( $s, 'name="visibility"' )? true : false;

                        if ( $id && $name && $field && $type && $value && $sequence )
                        {
                            $this->struct[$id][$name][$field]['type']         = $type;
                            $this->struct[$id][$name][$field]['value']        = $value;
                            $this->struct[$id][$name][$field]['sequence']     = $sequence;
                            $this->struct[$id][$name][$field]['primary_key']  = $primary_key;
                            $this->struct[$id][$name][$field]['index']        = $index;
                            $this->struct[$id][$name][$field]['index_unique'] = $index_unique;
							
                            unset(
								$field,
								$type,
								$value,
								$sequence,
								$index,
								$primary_key,
								$index,
								$index_unique
							);
                        }
                    }
                }
            }
			
            // Type Association
            if ( $key_association )
			{
                if ( stristr($s,'object') )
                    $key_association = false;
			}
			
            if ( stristr( $s, 'UML - Association' ) || $key_association )
            {
                $key_association = true;

                if ( $key_con )
				{
                    if ( stristr( $s, 'connections' ) )
                        $key_con = false;
				}

                if ( $key_direction )
                    $direction = substr( $s, strpos( $s, 'val=' ) + 5, strrpos( $s, '"' ) - strpos( $s, 'val=' ) - 5 );

                $key_direction = stristr( $s, 'name="direction"' )? true : false;

                if ( stristr( $s, 'connection=' ) || $key_con )
                {
                    $key_con = true;
                    $handle  = substr( $s, strpos( $s, 'handle' ) + 8, 1 );
                    $to = substr( $s, strpos( $s, 'to' ) + 4, strpos( $s, 'connection=' ) - strpos( $s, 'to' ) - 6 );
                    $connection = substr( $s, strpos( $s, 'connection=' ) + 12, strrpos( $s, '"' ) - strpos( $s, 'connection=' ) - 12 );

                    $this->struct[$id][$handle]['to'] = $to;
                    $this->struct[$id][$handle]['connection'] = $connection;
                    $this->struct[$id]['direction'] = $direction;
                }
            }
        }

        $this->generateClass();
    }

	/**
	 * @access public
	 */
    function generateClass()
    {
        if ( ! file_exists($this->path) )
            mkdir ( $this->path , 0777 );

        foreach ( $this->struct as $id => $s )
        {
			$key_class = false;

			foreach ( $s as $name => $array )
            {
				if ( $key_class )
                {
					$key_class = false;

                    $this->sql[$id] .=
						"----------------------------------------------------------------------\n" .
						"-- --\n" .
						"--\n" .
						"-- Table: $name\n" .
						"-- Purpose:\n" .
						"--\n" .
						"-- --\n" .
						"----------------------------------------------------------------------\n\n";
					
					foreach ( $array as $field => $types )
                    {
                        if ( $types['sequence'] == 'true' )
							$this->sql[$id] .= "-- DROP SEQUENCE seq_{$field};\n" . "CREATE SEQUENCE seq_{$field};\n\n";
                    }

                    $this->sql[$id] .= "-- DROP TABLE $name;\n" . "CREATE TABLE $name \n" . "(\n";

                    foreach ( $array as $field => $types )
                    {
                        if ( strlen( $types['type'] ) > $count_t )
                            $count_t = strlen( $types['type'] );
							
                        if ( strlen( $field ) > $count_c )
                            $count_c = strlen( $field );
                    }

                    unset( $e );
                    $count = 8;
					
                    foreach ( $array as $field => $types )
                    {
                        $this->sql[$id] .= "    $field";
						
                        for ( $x = 0; $x < ( $count_c - ( strlen( $field ) ) ); $x++ )
                            $this->sql[$id] .= ' ';
							
                        $this->sql[$id] .= "    {$types['type']}";
                        
						for ( $x = 0; $x < ( $count_t - ( strlen( $types['type'] ) ) ); $x++ )
                            $this->sql[$id] .= ' ';

                        if ( $types['sequence'] == 'true' )
                            $default = 'default nextval(\'seq_' . $field . '\')';
                        else if ( stristr( $types['value'], 'not' ) || stristr( $types['value'], 'null' ) )
                            $default = $types['value'];
                        else
                            $default = 'default ' . $types['value'];

                        if ( $types['primary_key'] == 'true' )
                        {
                            $this->sql[$id] .= "    primary key\n";
                            
							for ( $x = 0; $x < ( $count_t + $count_c + 8 ); $x++ )
                                $this->sql[$id] .= ' ';
                        }

                        $references = $this->checkAssociation( $id, $count++, $name, $field );
                        $count++;
                        $e++;

                        if ( $references )
                        {
                            $this->sql[$id] .= "    $references,\n";
                        }
                        else
                        {
                            if ( $e >= count($array) )
                            {
                                if ( ( $types['value'] && $types['value']!=' ' ) || $types['sequence'] == 'true' )
                                    $this->sql[$id] .= "    $default\n);\n";
                                else
                                    $this->sql[$id] .= "    null\n);\n";
                            }
                            else
                            {
                                if ( ( $types['value'] && $types['value']!=' ' ) || $types['sequence'] == 'true' )
                                    $this->sql[$id] .= "    $default,\n";
                                else
                                    $this->sql[$id] .= "    null,\n";
                            }
                        }
                    }

                    unset(
						$fil,
						$fil1,
						$name_index,
						$name_index1
					);
                    
					foreach ( $array as $field => $types )
                    {
                        if ( $types['index_unique'] == 'true' )
                        {
                            $this->sql[$id] .= "\n-- DROP INDEX {$field}_idx;\n" . "CREATE UNIQUE INDEX {$field}_idx ON {$name}($field);\n";
                            $fil[] = $field;
                        }
                        if ( $types['index'] == 'true' )
                        {
                            $this->sql[$id] .= "\n-- DROP INDEX {$field}_idx;\n" . "CREATE INDEX {$field}_idx ON {$name}($field);\n";
                            $fil1[] = $field;
                        }
                    }
					
                    if ( count( $fil ) > 1 )
                    {
                        $i = 0;
						
                        foreach ( $fil as $fl )
                        {
                            if ( $i < 1 )
                                $string = "($fl";
                            else if ( $i < count( $fil ) )
                                $string .= ",$fl";
								
                            $i++;
                            $name_index .= $fl . '_';
                        }
						
                        $this->sql[$id] .= "\n-- DROP INDEX {$name_index}idx;\n" . "CREATE UNIQUE INDEX {$name_index}idx ON {$name}$string);\n";
                    }
					
                    if ( count( $fil1 ) > 1 )
                    {
                        $i = 0;
						
                        foreach ( $fil1 as $fl1 )
                        {
                            if ( $i < 1 )
                                $string = "($fl1";
                            else if ( $i < count( $fil1 ) )
                                $string .= ",$fl1";
								
                            $i++;
                            $name_index1 .= $fl1 . '_';
                        }
						
                        $this->sql[$id] .= "\n-- DROP INDEX {$name_index1}idx;\n" . "CREATE INDEX {$name_index1}idx ON {$name}$string);\n";
                    }

                    $this->generateFile( $name . '.sql', $this->sql[$id] );

                }
                else if ( $name == 'Type' && $array == 'Class' )
                {
                    $key_class = true;
                }
            }
        }
    }

	/**
	 * @access public
	 */
    function checkAssociation( $id_table, $id_field, $name_table, $name_field )
    {
        foreach ( $this->struct as $id => $i )
        {
            if ( $i['Type'] == 'Association' )
            {
                switch ( $i['direction'] )
                {
                    case '1' :
                        if ( $i['0']['to'] == $id_table && ( $i['0']['connection'] == $id_field || $i['0']['connection'] == ( $id_field + 1 ) ) )
                        {
                           foreach ( $this->struct["{$i['1']['to']}"] as $id_ => $i_ )
                            {
                                if ( is_array($i_) )
                                {
                                    $e = 8;
									
                                    foreach ( $i_ as $iss_ => $rrr )
                                    {
                                        if ( ( $e == $i['1']['connection'] ) || ( ($e+1) == $i['1']['connection'] ) )
                                            return "references {$id_}({$iss_})";
											
                                        $e += 2;
                                    }
                                }
                            }
                        }
						
                        break;
						
                    case '2' :
                        if ( $i['1']['to'] == $id_table && ( $i['1']['connection'] == $id_field || $i['1']['connection'] == ( $id_field + 1 ) ) )
                        {
                            foreach ( $this->struct["{$i['0']['to']}"] as $id_ => $i_ )
                            {
                                if ( is_array($i_) )
                                {
                                    $e = 8;
									
                                    foreach ( $i_ as $iss_ => $rrr )
                                    {
                                        if ( ( $e == $i['0']['connection'] ) || ( ( $e + 1 ) == $i['0']['connection'] ) )
                                            return "references {$id_}({$iss_})";
											
                                        $e += 2;
                                    }
                                }
                            }
                        }
						
                        break;
                    
					case '' :
                        return null;
                        break;
                }
            }
        }
    }

	/**
	 * @access public
	 */
    function generateFile( $file, $content )
    {
        $fp = fopen( $this->path . DIRECTORY_SEPARATOR . $file, 'w' );
        fputs( $fp, $content );
        fclose( $fp );
    }
} // END OF Dia2SQL

?>
