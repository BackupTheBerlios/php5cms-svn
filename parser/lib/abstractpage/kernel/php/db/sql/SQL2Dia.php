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
 * This class creates a diagram from your sql file.
 *
 * Usage:
 *
 * $array = array(
 *	"table1" =>	array(
 *		"id"			=> "int",
 *		"text"			=> "varchar(240)",
 *		"description"	=> "text"
 *	),
 *	"table2" =>	array(
 *		"id"			=> "int",
 *		"date"			=> "date",
 *		"visible"		=> "bool",
 *		"value"			=> "float",
 *		"obs"			=> "text"
 *	)
 * );
 *
 * $object = new SQL2Dia;
 * $object->Generate( $array, 'example.dia' );
 *
 * @package db_sql
 */

class SQL2Dia extends PEAR
{
	/**
	 * @access public
	 */
	var $XML;
	
	/**
	 * @access public
	 */
	var $file;
	
	/**
	 * @access public
	 */
	var $count = 0;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SQL2Dia()
	{
		$this->BeginHeader();
	}


	/**
	 * @access public
	 */	
	function Generate( $array = null, $file = null )
	{
		$this->file = $file;

		foreach ( $array as $table => $fields )
		{
			if ( $tab != $table )
			{
				if ( $key )
				{
					$this->EndBody();
					unset( $key );
				}
				
				$key = TRUE;
				$this->BeginBody( $table );
			}
			
			foreach ( $fields as $type => $field )
				$this->GenerateStruct( $field, $type );
		}

		$this->EndBody();
		$this->EndHeader();
		$this->GenerateFile();
	}

	/**
	 * @access public
	 */
	function BeginHeader()
	{
		$this->XML .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
			"  <dia:diagram xmlns:dia=\"http://www.lysator.liu.se/~alla/dia/\">\n" .
			"    <dia:layer name=\"Segundo Plano\" visible=\"true\">\n";
	}

	/**
	 * @access public
	 */
	function EndHeader()
	{
		$this->XML .=	"    </dia:layer>\n" .
			"  </dia:diagram>\n";
	}

	/**
	 * @access public
	 */
	function BeginBody( $table = null )
	{
		$this->XML .=	"      <dia:object type=\"UML - Class\" version=\"0\" id=\"O" . $this->count++ . "\">\n" .
			"        <dia:attribute name=\"name\">\n" .
			"          <dia:string>#$table#</dia:string>\n" .
			"        </dia:attribute>\n" .
			"        <dia:attribute name=\"visible_attributes\">\n" .
			"          <dia:boolean val=\"true\"/>\n" .
			"        </dia:attribute>\n" .
			"        <dia:attribute name=\"attributes\">\n";
	}

	/**
	 * @access public
	 */
	function EndBody()
	{
		$this->XML .=	"        </dia:attribute>\n" .
			"      </dia:object>\n";
	}

	/**
	 * @access public
	 */
	function GenerateStruct( $field = null, $type = null )
	{
		$this->XML .=	"        <dia:composite type=\"umlattribute\">\n" .
			"          <dia:attribute name=\"name\">\n" .
			"            <dia:string>#$field#</dia:string>\n" .
			"          </dia:attribute>\n" .
			"          <dia:attribute name=\"type\">\n" .
			"            <dia:string>#$type#</dia:string>\n" .
			"          </dia:attribute>\n" .
			"        </dia:composite>\n";
	}

	/**
	 * @access public
	 */
	function GenerateFile()
	{
		$fp = fopen( "{$this->file}" , 'w' );
		fputs( $fp, $this->XML );
		fclose( $fp );
	}
} // END OF SQL2Dia

?>
