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


using( 'io.Stream' );
  
  
// Mode constants for open() method
define( 'FILE_MODE_READ',      'rb'  ); // Read
define( 'FILE_MODE_READWRITE', 'rb+' ); // Read/Write
define( 'FILE_MODE_WRITE',     'wb'  ); // Write
define( 'FILE_MODE_REWRITE',   'wb+' ); // Read/Write, truncate on open
define( 'FILE_MODE_APPEND',    'ab'  ); // Append (Read-only)
define( 'FILE_MODE_READAPPEND','ab+' ); // Append (Read/Write)

    
/**
 * Instances of the file class serve as an opaque handle to the underlying machine-
 * specific structure representing an open file.
 *
 * @package io
 */

class File extends Stream
{
	/**
	 * @access public
	 */
    var $uri = '';
	
	/**
	 * @access public
	 */
	var $filename = '';
	
	/**
	 * @access public
	 */
	var $path = '';
	
	/**
	 * @access public
	 */
	var $extension = '';
	
	/**
	 * @access public
	 */
	var $mode = FILE_MODE_READ;

	/**
	 * @access private
	 */    
    var $_fd = null;
    
	
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed file either a filename or a resource (as returned from fopen)
     */
    function File( $file ) 
	{
      	if ( is_resource( $file ) ) 
		{
        	$this->uri = null;
        	$this->_fd = $file;
      	} 
		else 
		{
        	$this->setURI( $file );
      	}
    }
    
	
    /**
     * Returns the URI of the file.
     *
     * @access public
     * @return string uri
     */
    function getURI()
	{
      	return $this->uri;
    }
    
    /**
     * Returns the filename of the file.
     *
     * @access public
     * @return string filename
     */
    function getFileName()
	{
      	return $this->filename;
    }

    /**
     * Get Path.
     *
     * @access  public
     * @return  string
     */
    function getPath()
	{
      	return $this->path;
    }

    /**
     * Get Extension.
     *
     * @access  public
     * @return  string
     */
    function getExtension()
	{
      	return $this->extension;
    }

    /**
     * Set this file's URI.
     *
     * @access  private
     * @param   string uri
     */
    function setURI( $uri ) 
	{
      	// PHP-Scheme
      	if ( 'php://' == substr( $uri, 0, 6 ) ) 
		{
        	$this->path      = null;
        	$this->extension = null;
        	$this->filename  = $this->uri = $uri;
        	
			return;
      	}
      
      	$this->uri = realpath( $uri );
      
      	// Bug in real_path when file does not exist
      	if ( '' == $this->uri && $uri != $this->uri ) 
			$this->uri = $uri;
      
      	$this->path      = dirname( $uri );
      	$this->filename  = basename( $uri );
      	$this->extension = null;
      
	  	if ( preg_match( '/\.(.+)$/', $this->filename, $regs ) ) 
			$this->extension = $regs[1];
    }

    /**
     * Open the file.
     *
     * @access  public
     * @param   string mode one of the FILE_MODE_* constants
     */
    function open( $mode = FILE_MODE_READ ) 
	{
      	$this->mode = $mode;
      
	  	if ( ( 'php://' != substr( $this->uri, 0, 6 ) ) && ( $mode == FILE_MODE_READ ) && ( !$this->exists() ) ) 
			return PEAR::raiseError( 'File not found.' );
      
      	$this->_fd = fopen( $this->uri, $this->mode );
      
	  	if ( !$this->_fd ) 
			return PEAR::raiseError( 'Cannot open ' . $this->uri . ' mode ' . $this->mode );
      
      	return true;
    }
    
    /**
     * Returns whether this file is open.
     *
     * @access  public
     * @return  bool true, when the file is open
     */
    function isOpen()
	{
      	return $this->_fd;
    }
    
    /**
     * Returns whether this file exists.
     *
     * @access  public
     * @return  bool true in case the file exists
     */
    function exists()
	{
      	return file_exists( $this->uri );
    }
    
    /**
     * Retrieve the file's size in bytes.
     *
     * @access  public
     * @return  int size filesize in bytes
     * @throws  Error
     */
    function size()
	{
      	$size = filesize( $this->uri );
      
	  	if ( $size === false ) 
			return PEAR::raiseError( 'Cannot get filesize for ' . $this->uri );
      
	  	return $size;
    }
    
    /**
     * Truncate the file to the specified length.
     *
     * @access  public
     * @param   int size default 0 New size in bytes
     * @throws  Error
     */
    function truncate( $size = 0 ) 
	{
      	$return = ftruncate( $this->_fd, $size );
      
	  	if ( $return === false ) 
			return PEAR::raiseError( 'Cannot truncate ' . $this->uri );
      
	  	return $return;
    }

    /**
     * Retrieve last access time.
     *
     * Note: 
     * The atime of a file is supposed to change whenever the data blocks of a file 
     * are being read. This can be costly performancewise when an application 
     * regularly accesses a very large number of files or directories. Some Unix 
     * filesystems can be mounted with atime updates disabled to increase the 
     * performance of such applications; USENET news spools are a common example. 
     * On such filesystems this function will be useless. 
     *
     * @access  public
     * @return  int The date the file was last accessed as a unix-timestamp
     * @throws  Error
     */
    function lastAccessed()
	{
      	$atime = fileatime( $this->uri );
      
	  	if ( $atime === false ) 
			return PEAR::raiseError( 'Cannot get atime for ' . $this->uri );
      
	  	return $atime;
    }
    
    /**
     * Retrieve last modification time.
     *
     * @access  public
     * @return  int The date the file was last modified as a unix-timestamp
     * @throws  Error
     */
    function lastModified()
	{
      	$mtime = filemtime( $this->uri );
		
      	if ( $mtime === false ) 
			return PEAR::raiseError( 'Cannot get mtime for ' . $this->uri );
      
	  	return $mtime;
    }
    
    /**
     * Set last modification time.
     *
     * @access  public
     * @param   int time default -1 Unix-timestamp
     * @return  bool success
     * @throws  Error
     */
    function touch( $time = -1 ) 
	{
      	if ( -1 == $time ) 
			$time = time();
      
	  	if ( touch( $this->uri, $time ) === false )
        	return PEAR::raiseError( 'Cannot set mtime for ' . $this->uri );
      
      	return true;
    }

    /**
     * Retrieve when the file was created.
     *
     * @access  public
     * @return  int The date the file was created as a unix-timestamp
     * @throws  Error
     */
    function createdAt()
	{
      	if ( ( $mtime = filectime( $this->uri ) ) === false )
        	return PEAR::raiseError( 'Cannot get mtime for ' . $this->uri );
      
      	return $mtime;
    }

    /**
     * Read one line and chop off trailing CR and LF characters.
     *
     * Returns a string of up to length - 1 bytes read from the file. 
     * Reading ends when length - 1 bytes have been read, on a newline (which is 
     * included in the return value), or on EOF (whichever comes first). 
     *
     * @access  public
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     */
    function readLine( $bytes = 4096 ) 
	{
      	return chop( $this->gets( $bytes ) );
    }
    
    /**
     * Read one char.
     *
     * @access  public
     * @return  char the character read
	 * @throws  error
     */
    function readChar()
	{
      	if ( ( $result = fgetc( $this->_fd ) ) && !feof( $this->_fd ) === false )
        	return PEAR::raiseError( 'Cannot read 1 byte from ' . $this->uri );
      
      	return $result;
    }

    /**
     * Read a line.
     *
     * This function is identical to readLine except that trailing CR and LF characters
     * will be included in its return value
     *
     * @access  public
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     * @throws  Error
     */
    function gets( $bytes = 4096 ) 
	{
      	if ( ( $result = fgets( $this->_fd, $bytes ) ) && !feof( $this->_fd ) === false )
        	return PEAR::raiseError( 'Cannot read ' . $bytes . ' bytes from ' . $this->uri );
      
      	return $result;
    }

    /**
     * Read (binary-safe).
     *
     * @access  public
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     * @throws  Error
     */
    function read( $bytes = 4096 ) 
	{
      	if ( ( $result = fread( $this->_fd, $bytes ) ) && !feof( $this->_fd ) === false )
        	return PEAR::raiseError( 'Cannot read ' . $bytes . ' bytes from ' . $this->uri );
      
      	return $result;
    }

    /**
     * Write.
     *
     * @access  public
     * @param   string string data to write
     * @return  bool success
     * @throws  Error
     */
    function write( $string ) 
	{
      	if ( ( $result = fwrite( $this->_fd, $string ) ) === false )
	  		return PEAR::raiseError( 'Cannot write ' . strlen( $string ) . ' bytes to ' . $this->uri );
      
      	return $result;
    }

    /**
     * Write a line and append a LF (\n) character.
     *
     * @access  public
     * @param   string string data to write
     * @return  bool success
     * @throws  Error
     */
    function writeLine( $string ) 
	{
      	if ( ( $result = fputs( $this->_fd, $string . "\n" ) ) === false )
	  		return PEAR::raiseError( 'Cannot write ' . ( strlen( $string ) + 1 ) . ' bytes to ' . $this->uri );
      
      	return $result;
    }
    
    /**
     * Returns whether the file pointer is at the end of the file.
     *
     * Hint:
     * Use isOpen() to check if the file is open
     *
     * @see     php://feof
     * @access  public
     * @return  bool true when the end of the file is reached
     * @throws  Error
     */
    function eof()
	{
      	$result = feof( $this->_fd );
      
	  	if ( !$result )
	  		return PEAR::raiseError( 'Cannot determine eof of ' . $this->uri );
      
      	return $result;
    }
    
    /**
     * Sets the file position indicator for fp to the beginning of the 
     * file stream. 
     * 
     * This function is identical to a call of $f->seek(0, SEEK_SET)
     *
     * @access  public
     * @throws  Error
     */
    function rewind()
	{
      	if ( ( $result = rewind( $this->_fd ) ) === false )
	  		return PEAR::raiseError( 'Cannot rewind file pointer.' );
      
      	return true;
    }
    
    /**
     * Move file pointer to a new position.
     *
     * @access  public
     * @param   int position default 0 The new position
     * @param   int mode default SEEK_SET 
     * @see     php://fseek
     * @throws  Error
     * @return  bool success
     */
    function seek( $position = 0, $mode = SEEK_SET ) 
	{
      	if ( 0 != ( $result = fseek( $this->_fd, $position, $mode ) ) )
			return PEAR::raiseError( 'Seek error, position ' . $position . ' in mode ' . $mode );
      
      	return true;
    }
    
    /**
     * Retrieve file pointer position.
     *
     * @access  public
     * @throws  Error
     * @return  int position
     */
    function tell()
	{
      	$result = ftell( $this->_fd );
      
	  	if ( $result === false ) 
	  		return PEAR::raiseError( 'Cannot retrieve file pointers position.' );

      	return $result;
    }
    
    /**
     * Acquire a shared lock (reader).
     *
     * @access  public
     * @see     File#_lock
     */
    function lockShared( $lock = null ) 
	{
      	return $this->_lock( LOCK_SH, $lock );
    }
    
    /**
     * Acquire an exclusive lock (writer).
     *
     * @access  public
     * @see     File#_lock
     */
    function lockExclusive( $lock = null ) 
	{
      	return $this->_lock( LOCK_EX, $lock );
    }
    
    /**
     * Release a lock (shared or exclusive).
     *
     * @access  public
     * @see     File#_lock
     */
    function unLock()
	{
      	return $this->_lock( LOCK_UN );
    }

    /**
     * Close this file.
     *
     * @access  public
     * @return  bool success
     */
    function close()
	{
      	if ( fclose( $this->_fd ) === false )
	  		return PEAR::raiseError( 'Cannot close file ' . $this->uri );
      
      	$this->_fd = null;
      	return true;
    }
    
    /**
     * Delete this file.
     *
     * Warning: Open files cannot be deleted. Use the close() method to
     * close the file first
     *
     * @access  public
     * @return  bool success
     * @throws  Error
     */
    function unlink()
	{
      	if ( is_resource( $this->_fd ) )
        	return PEAR::raiseError( 'File still open.' );
      
      	if ( unlink( $this->uri ) === false )
	  		return PEAR::raiseError( 'Cannot delete file ' . $this->uri );
      
      	return true;
    }
    
    /**
     * Move this file.
     *
     * Warning: Open files cannot be moved. Use the close() method to
     * close the file first
     *
     * @access  public
     * @param   string target where to move the file to
     * @return  bool success
     * @throws  Error
     */
    function move( $target ) 
	{
      	if ( is_resource( $this->_fd ) )
        	return PEAR::raiseError( 'File still open.' );
      
      	if ( rename( $this->uri, $target ) === false )
	  		return PEAR::raiseError( 'Cannot move file ' . $this->uri . ' to ' . $target );
      
      	$this->setURI( $target );
      	return true;
    }
    
    /**
     * Copy this file.
     *
     * Warning: Open files cannot be copied. Use the close() method to
     * close the file first
     *
     * @access  public
     * @param   string target where to copy the file to
     * @return  bool success
     * @throws  Error
     */
    function copy( $target ) 
	{
      	if ( is_resource( $this->_fd ) )
        	return PEAR::raiseError( 'File still open.' );
      
      	if ( copy( $this->uri, $target ) === false )
        	return PEAR::raiseError( 'Cannot copy file ' . $this->uri . ' to ' . $target );
      
      	return true;
    }
	
	
	// private methods
	
    /**
     * Private wrapper function for locking.
     *
     * Warning:
     * flock() will not work on NFS and many other networked file systems. Check your 
     * operating system documentation for more details. On some operating systems flock() 
     * is implemented at the process level. When using a multithreaded server API like 
     * ISAPI you may not be able to rely on flock() to protect files against other PHP 
     * scripts running in parallel threads of the same server instance! flock() is not 
     * supported on antiquated filesystems like FAT and its derivates and will therefore 
     * always return false under this environments (this is especially true for Windows 98 
     * users). 
     *
     * The optional second argument is set to true if the lock would block (EWOULDBLOCK 
     * errno condition).
     *
     * @access  private
     * @param   int op operation (one of the predefined LOCK_* constants)
     * @param   int block
     * @throws  Error
     * @return  bool success
     * @see     php://flock
     */
    function _lock( $op, $block = null ) 
	{
      	$result = flock( $this->_fd, $op, $block );
      
	  	if ( $result === false ) 
		{
        	$os = '';
        
			foreach ( array(
          		LOCK_SH => 'LOCK_SH', 
          		LOCK_EX => 'LOCK_EX', 
          		LOCK_UN => 'LOCK_UN', 
          		LOCK_NB => 'LOCK_NB' ) as $o => $s ) 
			{
          		if ( $op & $o ) 
					$os .= ' | ' . $s;
        	}
			
			return PEAR::raiseError( 'Cannot lock file ' . $this->uri . ' w/ ' . substr( $os, 3 ) );
      	}
      
	  	return $result;
    }
} // END OF File

?>
