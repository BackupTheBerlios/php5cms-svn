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


using( 'util.persistence.shelve.Unpickle' );
using( 'util.persistence.shelve.Pickle' );
using( 'io.File' );


/**
 * @package util_persistence_shelve
 */
 
class Shelve extends PEAR
{
	/**
	 * @access private
	 */	
	var $_shlef;
	
	/**
	 * @access private
	 */	
	var $_keys = array();
	
	/**
	 * @access private
	 */	
	var $_objects = array();
	
	/**
	 * @access private
	 */	
	var $_deleted = array();

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Shelve( $shelf, $shelves_path = "." )
	{
		$this->_shelf = $shelves_path . DIRECTORY_SEPARATOR . $shelf;
		
		if( !is_dir( $this->_shelf ) )
		{
			mkdir( $this->_shelf, 0700 );
			$this->_save_index();
		}
		
		$p = new Unpickle( new File( $this->_shelf . DIRECTORY_SEPARATOR . "shelf.idx" ) );
		$this->_keys =& $p->load();
	}
	
	
	/**
	 * @access public
	 */	
	function &get( $key )
	{
		if ( in_array( $key, $this->_keys ) )
		{
			if ( !in_array( $key, array_keys( $this->_objects ) ) ) 
			{
			    $unpickle = new Unpickle( new File( $this->_shelf . DIRECTORY_SEPARATOR . $key ) );
				return $unpickle->load();
			}
			
			return $this->_objects[$key];
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @access public
	 */	
	function add( $key, &$obj )
	{
		if ( !in_array( $key, $this->_keys ) )
		{
			$this->_keys[] = $key;
			$this->_objects[$key] =& $obj;
			
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @access public
	 */	
	function put( $key, &$obj )
	{
		if ( in_array( $key, $this->_keys ) )
		{
			$this->_objects[$key] =& $obj;
			return true;
		}
		else
		{
			return $this->add( $key, $obj );
		}
	}

	/**
	 * @access public
	 */	
	function keys()
	{
		return $this->_keys;
	}
	
	/**
	 * @access public
	 */	
	function destroy()
	{
		unlink( $this->_shelf . DIRECTORY_SEPARATOR . "shelf.idx" );
		
		foreach ( $this->_keys as $key )
		{
		    if ( is_file( $this->_shelf . DIRECTORY_SEPARATOR . $key ) )
				unlink( $this->_shelf . DIRECTORY_SEPARATOR . $key );
		}
		
		return rmdir( $this->_shelf );
	}

	/**
	 * @access public
	 */	
	function close()
	{
	    foreach ( $this->_keys as $key )
	    	$this->_save_object( $key, $this->_objects[$key] );
	    
	    $this->_save_index();

	    foreach( $this->_deleted as $key )
	    {
			$f = new File( $this->_shelf . DIRECTORY_SEPARATOR . $key );
			$f->delete();
	    }
	}

	/**
	 * @access public
	 */		
	function del( $key )
	{
		unset( $this->_keys[array_search( $key, $this->_keys)] );
		unset( $this->_objects[$key] );
		
		$this->_deleted[] = $key;
	}
	
		
	// private methods
	
	/**
	 * @access private
	 */	
	function _save_object( $key, &$obj )
	{
		$p = new Pickle( new File( $this->_shelf . DIRECTORY_SEPARATOR . $key ) );
		$p->dump( $obj );
	}

	/**
	 * @access private
	 */	
	function _save_index()
	{
		$p = new Pickle( new File( $this->_shelf . DIRECTORY_SEPARATOR . "shelf.idx" ) );
		$p->dump( $this->_keys );
	}
} // END OF Shelve

?>
