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
 * @package io
 */
 
class SerializedDataFile extends PEAR
{
	/**
	 * @access public
	 */
	var $filename = '';
	
	/**
	 * @access public
	 */
	var $data = array();
	
	/**
	 * @access public
	 */
	var $fields = array();
	
	
	/**
	 * @access public
	 */
	function setFile( $filename = '' )
	{
		if ( $filename == '' )
		{
			return PEAR::raiseError( "File not set." );
		}
		else if ( file_exists( $filename ) )
		{
			$this->setFileName( $filename );
			$this->loadData();
		}
		else
		{
			return PEAR::raiseError( "File not found." );
		}
	}

	/**
	 * @access public
	 */
	function setFileName( $filename )
	{
		$this->filename = $filename;
	}

	/**
	 * @access public
	 */
	function getFileName()
	{
		return (string)$this->filename;
	}

	/**
	 * @access public
	 */
	function createFile( $filename = '', $fields = array() )
	{
		if ( $filename == '' )
		{
			return PEAR::raiseError( "File not set." );
		}
		else if ( file_exists( $filename ) )
		{
			return PEAR::raiseError( "File allredy exists." );
		}
		else
		{
			if ( count( $fields ) == 0 )
			{
				return PEAR::raiseError( "Fields not defined." );
			}
			else
			{
				$res = @fopen( $filename, "w" );
				
				if ( $res === false )
				{
					return PEAR::raiseError( "Unable to create file." );
				}
				else
				{
					$file = array( $fields );
					fwrite( $res, serialize( $file ) );
					@fclose( $res );
					$this->setFileName( $filename );
				}
			}
		}
	}

	/**
	 * @access public
	 */
	function destroyFile()
	{
		if ( $this->getFileName() == '' )
		{
			return PEAR::raiseError( "File not set." );
		}
		else if ( file_exists( $this->getFileName() ) === false )
		{
			return PEAR::raiseError( "File not found." );
		}
		else
		{
			copy( $this->getFileName(), $this->getFileName() . ".bkp" );
			unlink( $this->getFileName() );
			
			return true;
		}
	}

	/**
	 * @access public
	 */
	function dropData()
	{
		if ( $this->getFileName() == '' )
		{
			return PEAR::raiseError( "File not set." );
		}
		else if ( file_exists( $this->getFileName()) === false )
		{
			return PEAR::raiseError( "File not found." );
		}
		else
		{
			copy( $this->getFileName(), $this->getFileName() . ".bkp" );
			$res = @fopen( $this->getFileName(), "w" );
			
			if ( $res === false )
			{
				return PEAR::raiseError( "Unable to drop file, possible, data have been lost." );
			}
			else
			{
				$file = array( $this->getFields() );
				fwrite( $res, serialize( $file ) );
				@fclose( $res );
				$this->loadData();
			}
		}
	}

	/**
	 * @access public
	 */
	function setFields( $array )
	{
		if ( is_array( $array ) )
			$this->fields = $array;
		else
			return PEAR::raiseError( "File damaged, no fields." );
	}

	/**
	 * @access public
	 */
	function getFields()
	{
		return (array)$this->fields;
	}

	/**
	 * @access public
	 */
	function getFieldCount()
	{
		return (integer)count( $this->getFields() );
	}

	/**
	 * @access public
	 */
	function setData( $arr )
	{
		$this->data = $arr;
	}

	/**
	 * @access public
	 */
	function getData()
	{
		return (array)$this->data;
	}

	/**
	 * @access public
	 */
	function getDataCount()
	{
		return (integer)count( $this->getData() );
	}

	/**
	 * @access public
	 */
	function loadData()
	{
		$f = fopen( $this->getFileName(), "r" );
		$contents = fread( $f, filesize( $this->getFileName() ) );
		fclose( $f );
		$file = @unserialize( $contents );
		$this->setFields( $file[0] );
		reset( $file );
		next( $file );
		$arr = array();
		
		while ( list( , $value ) = each( $file ) )
		{
			$mem = $value;
			
			if ( $mem === false )
				return PEAR::raiseError( "File damaged, no fields." );
			else
				$arr[] = $mem;
		}
		
		$this->setData( $arr );
	}

	/**
	 * @access public
	 */
	function unloadFile()
	{
		copy( $this->getFileName(), $this->getFileName() . ".bkp" );
		
		$f    = fopen( $this->getFileName(), "w" );
		$file = array( $this->getFields() );
		$row  = $this->getData();
		
		while ( list( , $value ) = each( $row ) )
			$file[] = $value;
		
		fwrite( $f, serialize( $file ) );
		fclose( $f );
	}

	/**
	 * @access public
	 */
	function arrays( $fields, $values, $default = '' )
	{
		while ( list( , $field ) = each( $fields ) )
		{
			$keys = array_keys( $values );
			
			if ( in_array( $field,$keys ) )
			{
				$arr[$field] = $values[$field];
			}
			else
			{
				if ( $default == '' )
					$arr[$field] = '';
				else
					$arr[$field] = $default[$field];
			}
		}
		
		return $arr;
	}

	/**
	 * @access public
	 */
	function insertData( $array )
	{
		$row   = $this->getData();
		$row[] = $this->arrays( $this->getFields(), $array );
		
		$this->setData( $row );
		$this->unloadFile();
	}

	/**
	 * @access public
	 */
	function updateData( $id, $array )
	{
		if ( ( is_array( $id ) ) && ( count( $id ) == 1 ) )
		{
			$field = array_keys( $id );
			$value = array_values( $id );
			$row   = $this->getData();
			
			for ( $i = 0; $i < count( $row ); $i++ )
			{
				if ( $row[$i][$field[0]] == $value[0] )
					$row[$i] = $this->arrays( $this->getFields(), $array, $row[$i] );
			}
			
			$this->setData( $row );
			$this->unloadFile();
		}
		else
		{
			return PEAR::raiseError( "ID not set properly." );
		}
	}

	/**
	 * @access public
	 */
	function deleteData( $id )
	{
		if ( ( is_array( $id ) ) && ( count( $id ) == 1 ) )
		{
			$field = array_keys( $id );
			$value = array_values( $id );
			$row   = $this->getData();
			
			for ( $i = 0; $i < count( $row ); $i++ )
			{
				if ( $row[$i][$field[0]] != $value[0] )
					$res[] = $row[$i];
			}
			
			$this->setData( $res );
			$this->unloadFile();
		}
		else
		{
			return PEAR::raiseError( "ID not set properly." );
		}
	}
} // END OF SerializedDataFile

?>
