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
|Authors: Vincent Oostindië <eclipse@sunlight.tmfweb.nl>               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.Iterator' );


/**
 * Class <code>FileIterator</code> provides a simple iterator for traversing the
 * lines in a text file.
 * <p>
 *   On construction a filename must be passed, and the contents can then be
 *   iterated over like any other <code>Iterator</code>. If the iterator is used
 *   normally (from front to back completely), the internal file pointer is
 *   closed automatically when the end of the file is reached. If the standard
 *   iteration loop is not completed, the file pointer should be closed
 *   explicitly by calling <code>close()</code> on the iterator. For example:
 * </p>
 * <pre>
 *   $closed =  true;
 *   $it     =& new FileIterator( 'example.txt' );
 *   for ( ; $it->isValid(); $it->next())
 *   {
 *       $line =& $it->getCurrent();
 *       if ($line == 'STOP!')
 *       {
 *           $closed = false;
 *           break;
 *       }
 *       echo $line, "\n";
 *   }
 *   if (!$closed)
 *   {
 *       $it->close();
 *   }
 * </pre>
 * <p>
 *   If the file to be read can't be opened for one reason or another, this
 *   class will not fail. Instead, it will simply 'iterate over nothing'. Of
 *   course, PHP will still log warnings and/or errors somewhere, depending on
 *   how this is configured.
 * </p>
 *
 * @package io
 */
 
class FileIterator extends Iterator
{
    /**
     * The name of the text file
     * @var  string
     */
    var $filename;

    /**
     * The number of characters to read at once (at most!)
     * @var  int
     */
    var $bufferSize;
        
    /**
     * The file pointer for the opened file
     * @var  filepointer
     */
    var $pointer;
        
    /**
     * The current line in the file
     * @var  string
     */
    var $line;
    
    
	/**
	 * Constructor
	 */
    function FileIterator( $filename, $bufferSize = 4096 ) 
    {
        $this->filename   = $filename;
        $this->bufferSize = $bufferSize;
        $this->pointer    = false;
        $this->line       = false;
        
		$this->reset();
    }
    
        
    /**
     * Close the internal file pointer. This method must be called if the 
     * iteration is stopped before it is completed (that is: before the end of
     * the file is reached).
     * @return void
     */
    function close() 
    {
        if ( $this->pointer !== false ) 
        {
            fclose( $this->pointer );
            
			$this->pointer = false;
            $this->line    = false;
        }
    }

    /**
     * Read a single line from the file.
     * @return string
     * @access  private
     */
    function readLine() 
    {
        return fgets( $this->pointer, $this->bufferSize );
    }

    /**
     * @return void
     */
    function reset() 
    {
        if ( $this->pointer ) 
            rewind( $this->pointer );
        else 
            $this->pointer = fopen( $this->filename, 'r' );
        
        $this->line = ( $this->pointer !== false )? $this->readLine() : false;
    }

    /**
     * @return void
     */
    function next() 
    {
        $this->line = $this->readLine();
    }
   
    /**
     * @return bool
     */
    function isValid() 
    {
        if ( $this->pointer === false || feof( $this->pointer ) ) 
        {
            $this->close();
            return false;
        }
		
        return true;
    }

    /**
     * @return string
     */
    function &getCurrent()
    {
        return $this->line;
    }
} // END OF FileIterator

?>
