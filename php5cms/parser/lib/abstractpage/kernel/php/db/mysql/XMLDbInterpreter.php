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
 * @package db_mysql
 */
 
class XMLDbInterpreter extends PEAR
{
	/**
	 * @access public
	 */
	var $data;
	
	/**
	 * @access public
	 */
	var $temp_sql;
	
	/**
	 * @access public
	 */
 	var $table_options;
	
	/**
	 * @access public
	 */
	var $sql = array();
	
	/**
	 * @access public
	 */
	var $fields = array();
	
	/**
	 * @access public
	 */
 	var $sql_errors = array();


	/**
	 * @access public
	 */
	function getSQL()
	{
		return $this->sql;
	}

	/**
	 * @access public
	 */
	function getSQLErrors()
	{
		return $this->sql_errors;
	}

	/**
	 * @access public
	 */
	function getData()
	{
		return $this->data;
	}

	/**
	 * @access public
	 */
	function setSchema( $data, $is_file )
	{
		if ( $is_file == true )
		{
			if ( $fp = fopen( $data, 'r' ) )
			{
				$this->data = fread( $fp, filesize( $data ) );
				fclose( $fp );
			}
			else
			{
				return PEAR::raiseError( "Unable to open file." );
			}
		}
		else
		{
			$this->data = $data;
		}
	}

	/**
	 * @access public
	 */
	function parseSchema()
	{
		$parser = xml_parser_create();
		xml_set_object( $parser, &$this );
		
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, true );
		xml_set_element_handler( $parser, "startElement", "endElement" );
		xml_parse( $parser, $this->data );
	}

	/**
	 * @access public
	 */
	function create( $host, $user, $pass, $database = '' )
	{
		$connection = mysql_connect( $host, $user, $pass );

		if ( $database != '' )
			mysql_select_db( $database, $connection );

		for ( $i = 0; $i < count( $this->sql ); $i++ )
			mysql_query( $this->sql[$i], $connection ) || $this->sql_errors[] = mysql_error( $connection );
	}
	
	
	// private methods
	
	/**
	 * @access public
	 */
	function startElement( $xml, $element, $attributes )
	{
		switch( $element )
		{
			case 'DATABASE' : 
				if ( isset( $attributes['IF_NOT_EXISTS'] ) && $attributes['IF_NOT_EXISTS'] == 'yes' )
					$if_not_exists = 'IF NOT EXISTS ';
				else
					$if_not_exists = '';

				$this->sql[] = 'CREATE DATABASE ' . $if_not_exists.$attributes['NAME'];
				$this->sql[] = 'USE ' . $attributes['NAME'];
				break;

			case 'TABLE' :
				if ( isset( $attributes['TEMPORARY'] ) && $attributes['TEMPORARY'] == 'yes' )
					$temporary = 'TEMPORARY ';
				else
					$temporary = '';
					
				if ( isset( $attributes['IF_NOT_EXISTS'] ) && $attributes['IF_NOT_EXISTS'] == 'yes' )
					$if_not_exists = 'IF NOT EXISTS ';
				else
					$if_not_exists = '';
					
				if ( isset( $attributes['OPTIONS'] ) && $attributes['OPTIONS'] != '' )
					$this->table_options = ' ' . $attributes['OPTIONS'];
				else
					$this->table_options = '';
 
 				$this->temp_sql = 'CREATE ' . $temporary . 'TABLE ' . $if_not_exists . $attributes['NAME'] . ' (';
				break;

			case 'FIELD' :
				$temp_var = $attributes['NAME'] . ' ' . $attributes['TYPE'] . '(' . $attributes['SIZE'] . ')';

				if ( isset( $attributes['NULL'] ) && $attributes['NULL'] == 'no' )
					$temp_var .= ' NOT NULL';
				else
					$temp_var .= ' NULL';

				if ( isset( $attributes['DEFAULT'] ) )
					$temp_var .= " DEFAULT '" . $attributes['DEFAULT'] . "'";

				if ( isset( $attributes['EXTRA'] ) )
					$temp_var .= ' '.$attributes['EXTRA'];

				$this->fields[] = $temp_var;
				break;

			case 'KEY' :
				if ( isset( $attributes['TYPE'] ) && $attributes['TYPE'] == 'primary' )
					$this->fields[] = 'PRIMARY KEY (' . $attributes['FIELD'] . ')';
 
 				if ( isset( $attributes['TYPE'] ) && $attributes['TYPE'] == 'unique' )
					$this->fields[] = 'UNIQUE ' . $attributes['NAME'] . ' (' . $attributes['FIELD'] . ')';

				if ( isset( $attributes['TYPE'] ) && $attributes['TYPE'] == 'index' )
					$this->fields[] = 'INDEX ' . $attributes['NAME'] . ' (' . $attributes['FIELD'] . ')';
				
				break;

			default :
				break;
		}
	}

	/**
	 * @access public
	 */
	function endElement( $xml, $element )
	{
		switch ( $element )
		{
			case 'TABLE' :
				$this->sql[] = $this->temp_sql . implode( ', ', $this->fields ) . ')' . $this->table_options;
				
				$this->temp_sql = '';
				$this->fields   = array();	
				break;

			default:
				break;
		}
	}
} // END OF XMLDbInterpreter

?>
