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
 * @package db_dbm
 */
 
class AccessDBM extends PEAR
{
	/**
	 * @access public
	 */
	var $dbm;
	
	/**
	 * @access public
	 */
	var $count = 0;
	
	/**
	 * @access public
	 */
	var $values = array();
	
	/**
	 * @access public
	 */
	var $file = "";
	
	/**
	 * @access public
	 */
	var $exists = false;
	
	/**
	 * @access public
	 */
	var $static = false;
	
	/**
	 * @access public
	 */
	var $exact = false;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function AccessDBM( $dbmFile, $static = 0 )
	{
		$this->initialize( $dbmFile, $static );
	}


	/**
	 * @access public
	 */
	function initialize( $dbmFile, $static )
	{
		if ( !empty( $dbmFile ) )
		{
			if ( file_exists( $dbmFile ) )
				$this->exists = true;
			
			if ( $static != 0 )
				$this->static = true;
			
			$this->file = $dbmFile;
		}
		
		return;
	}

	/**
	 * @access public
	 */
	function add_entry( $key, $val )
	{
		$results = 0;
		$dbm = $this->open_dbm();
		
		if ( PEAR::isError( $dbm ) )
			return false;

		if ( !( dbmreplace( $dbm, $key, $val ) ) )
		{
			if ( !( dbmexists( $dbm, $key ) ) )
			{
				$this->close_dbm( $dbm );
				return PEAR::raiseError( "Could not replace key with value." );
			}
		}
		
		$this->close_dbm( $dbm );
		return true;		
	}

	/**
	 * @access public
	 */
	function remove_entry( $Key )
	{
		$removed = false;
		$dbm = $this->open_dbm();
		
		if ( PEAR::isError( $dbm ) )
			return false;

		if ( dbmexists( $dbm, $Key ) )
		{
			if ( !dbmdelete( $dbm, $Key ) )
			{
				if ( dbmexists( $dbm, $Key ) )
				{
					$this->close_dbm( $dbm );
					return PEAR::raiseError( "Unable to remove key." );
				}
			}
			else
			{
				$this->close_dbm( $dbm );
				$removed = true;
			}
		}
		else
		{
			$this->close_dbm( $dbm );
			return PEAR::raiseError( "Key does not exist." );
		}
		
		return true;
	}

	/**
	 * @access public
	 */
	function get_value( $Key )
	{
		$val      = "";
		$readOnly = true;
		$dbm      = $this->open_dbm( $readOnly );

		if ( PEAR::isError( $dbm ) )
			return false;

		if ( dbmexists( $dbm, $Key ) )
			$val = dbmfetch( $dbm, $Key );
		
		$this->close_dbm( $dbm );
		return $val;
	}

	/**
	 * @access public
	 */
	function open_dbm( $readOnly = false )
	{
		if ( $this->static )
		{
			if ( !( empty( $this->dbm ) ) )
			{
				$dbm = $this->dbm;
				return( $dbm );
			}
		}

		$fileName = $this->file;

		if ( !$this->exists )
		{
			$dbm = @dbmopen( $fileName, "n" );
		}
		else
		{
			if ( !$readOnly )
				$dbm = dbmopen( $fileName, "w" );
			else
				$dbm = @dbmopen( $fileName, "r" );
		}
		
		if ( ( !$dbm ) || ( empty( $dbm ) ) )
		{
			$this->exists = false;
			$this->static = false;
			
			return PEAR::raiseError( "Unable to open file." );
		}
		
		$this->exists = true;
		
		if ( $this->static )
			$this->dbm = $dbm;

		return ( $dbm );
	}

	/**
	 * @access public
	 */
	function find_key( $search )
	{
		$val = "";
		$dbm = $this->open_dbm( 1 );
		
		if ( PEAR::isError( $dbm ) )
			return false;
		
		if ( dbmexists( $dbm, $search ) )
		{
			// Wow, an exact match.
			$val = dbmfetch( $dbm, $search );
			$this->close_dbm( $dbm );
			$this->exact = true;
			
			return $val;
		}
		else
		{
			$this->exact = false;
			$key = dbmfirstkey( $dbm );
			
			while ( $key )
			{
				// Strip the first whitespace char and everything after it.
				$test = ereg_replace( " .*", "", $key );
				
				if ( eregi( "^$test", $search ) )
				{
					$val = dbmfetch( $dbm, $key );
					$this->close_dbm( $dbm );
					
					return $val;
				}
				
				$key = dbmnextkey( $dbm, $key );
			}
		}
		
		// didn't find it
		$this->close_dbm( $dbm );
		return false;
	}

	/**
	 * Returns the key.
	 *
	 * @access public
	 */
	function find_val( $search )
	{
		$this->exact = false;
		$Dbase = $this->get_all();
		
		if ( empty( $Dbase ) )
			return PEAR::raiseError( "Database is empty." );
		
		while ( list ( $key, $val ) = each ($Dbase) )
		{
			if ( $search == $val )
			{
				$this->exact = true;
				return $key;
			}
			else
			{
				// Strip the first whitespace char and everything after it.
				$test = ereg_replace( " .*", "", $val );

				if ( eregi( "^$test", $search ) )
				{
					$this->exact = false;
					return $key;
				}
			}
		}
		
		// didn't find it
		return false;
	}

	/**
	 * @access public
	 */
	function get_all()
	{
		$values   = array();
		$count    = 0;
		$readOnly = true;
		$dbm      = $this->open_dbm( $readOnly );
		
		if ( PEAR::isError( $dbm ) )
			return false;

		$key = dbmfirstkey( $dbm );

		while ( $key )
		{
			$val = dbmfetch( $dbm, $key );
			$values[$key] = $val;
			$count++;
			$key = dbmnextkey( $dbm, $key );
		}
		
		$this->count  = $count;
		$this->values = $values;
		$this->close_dbm( $dbm );
		
		return $values;
	}

	/**
	 * @access public
	 */
	function close_dbm( $dbm )
	{
		$results = false;

		if ( !$this->static )
			$results = dbmclose( $dbm );
		
		return $results;
	}

	/**
	 * @access public
	 */
	function static_close()
	{
		$results = false;

		if ( !$this->dbm )
			return PEAR::raiseError( "No static database to close." );
		
		$dbm     = $this->dbm;
		$results = dbmclose( $dbm );
		
		unset( $this->dbm );
		return $results;
	}
} // END OF AccessDBM

?>
