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
 * Class representing a Folder.
 *
 * Example:
 *
 * $d = new Folder( '/etc/' );
 * while ( ( $entry = $d->getEntry() ) !== false )
 *		printf( "%s/%s\n", $d->uri, $entry );
 * $d->close();
 *
 * @package io
 */

class Folder extends PEAR
{
	/**
	 * @access public
	 */
    var $uri = '';
	
	/**
	 * @access public
	 */
	var $dirname = '';
	
	/**
	 * @access public
	 */
	var $path = '';
    
	/**
	 * @access private
	 */
    var $_hdir = false;
      
	  
    /**
     * Constructor
     *
     * @access  public
     * @param   string dirname the folders name
     */
    function Folder( $dirname = null ) 
	{
	  	if ( null != $dirname ) 
			$this->setURI( $dirname );
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function _Folder()
	{
      	$this->close();
    }
    
    /**
     * Close directory.
     *
     * @access  public
     */
    function close()
	{
      	if ( false != $this->_hdir ) 
			$this->_hdir->close();
      
	  	$this->_hdir = null;
    }

    /**
     * Set URI.
     *
     * @access  public
     * @param   string uri the complete path name
     */
    function setURI( $uri ) 
	{
      	$this->uri = realpath( $uri );
      
      	// Bug in real_path if file is not existant
      	if ( '' == $this->uri && $uri != $this->uri ) 
			$this->uri = $uri;
      
      	// Add trailing / (or \, or whatever else DIRECTORY_SEPARATOR is defined to)
      	// if necessary
      	if ( DIRECTORY_SEPARATOR != substr( $this->uri, -1 * strlen( DIRECTORY_SEPARATOR ) ) )
        	$this->uri .= DIRECTORY_SEPARATOR;
      
      	$this->path    = dirname( $uri );
      	$this->dirname = basename( $uri );
    }
    
    /**
     * Get URI.
     *
     * @access public
     * @return uri of this folder
     */    
    function getURI()
	{
      	return $this->uri;
    }
    
    /**
     * Create this directory, recursively, if needed.
     *
     * @access  public
     * @param   int permissions default 0700
     * @return  bool true in case the creation succeeded or the directory already exists
     * @throws  Error
     */
    function create( $permissions = 0700 ) 
	{
      	if ( is_dir( $this->uri ) ) 
			return true;
      
	  	$i = 0;
      	$umask = umask( 000 );
      
	  	while ( ( $i = strpos( $this->uri, DIRECTORY_SEPARATOR, $i ) ) !== false ) 
		{
        	if ( is_dir( $d = substr( $this->uri, 0, ++$i ) ) ) 
				continue;
        
			if ( mkdir( $d, $permissions ) === false ) 
			{
          		umask( $umask );
		  		return PEAR::raiseError( sprintf( 'mkdir( "%s", %d ) failed.', $d, $permissions ) );
        	}
      	}
      
	  	umask( $umask );
      	return true;
    }
    
    /**
     * Delete this folder and all its subentries recursively
     * Warning: Stops at the first element that can't be deleted!
     *
     * @access  public
     * @return  bool success
     * @throws  Error
     */
    function unlink( $uri = null ) 
	{
      	if ( $uri === null ) 
			$uri = $this->uri; // We also use this recursively
      
      	if ( ( $d= dir( $uri ) ) === false )
        	return PEAR::raiseError( 'Folder ' . $uri . ' does not exist.' );
      
      	while ( ( $e = $d->read() ) !== false ) 
		{
        	if ( '.' == $e || '..' == $e ) 
				continue;
        
        	$fn = $d->path . $e;
        
			if ( !is_dir( $fn ) ) 
				$ret = unlink( $fn );
        	else
          		$ret = $this->unlink( $fn . DIRECTORY_SEPARATOR );
        
        	if ( $ret === false ) 
				return PEAR::raiseError( sprintf( 'Unlink of "%s" failed.', $fn ) );
      	}
      
	  	$d->close();

      	if ( rmdir( $uri ) === false ) 
			return PEAR::raiseError( sprintf( 'Unlink of "%s" failed.', $uri ) );
      
      	return true;
    }

    /**
     * Move this directory.
     *
     * Warning: Open directories cannot be moved. Use the close() method to
     * close the directory first
     *
     * @access  public
     * @return  bool success
     * @throws  Error
     */
    function move( $target ) 
	{
      	if ( is_resource( $this->_hdir ) )
        	return PEAR::raiseError( 'Folder still open.' );
      
      	if ( rename( $this->uri, $target ) === false )
        	return PEAR::raiseError( 'Cannot move directory ' . $this->uri . ' to ' . $target );
      
      	return true;
    }

    /**
     * Returns whether this directory exists.
     *
     * @access  public
     * @return  bool true in case the directory exists
     */
    function exists()
	{
      	return is_dir( $this->uri );
    }
    
    /**
     * Read through the contents of the directory, ommitting the entries "." and "..".
     *
     * @access  public
     * @return  string entry directory entry (w/o path!), false, if no more entries are left
     * @throws  Error
     */
    function getEntry()
	{
      	if ( ( $this->_hdir === false ) && ( ( $this->_hdir = dir( $this->uri ) ) === false ) )
        	return PEAR::raiseError( 'Cannot open directory "' . $this->uri . '"' );
      
      	while ( ( $entry = $this->_hdir->read() ) !== false )
		{
        	if ( $entry != '.' && $entry != '..' ) 
				return $entry;
		}
		
      	return false;
    }
   
    /**
     * Rewinds the directory to the beginning.
     *
     * @access  public
     * @throws  Error
     */
    function rewind()
	{
      	if ( $this->_hdir === false )
        	return PEAR::raiseError( 'Cannot rewind non-open folder.' );
      
      	rewinddir( $this->_hdir );
    }
} // END OF Folder

?>
