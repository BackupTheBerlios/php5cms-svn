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


using( 'io.File' );
using( 'util.Util' );
    
	
/**
 * Represents a temporary file.
 *
 * Example:
 *
 * $f= &new TempFile();
 * $f->open( FILE_MODE_WRITE );
 * $f->write( 'Hello' );
 * $f->close();
 * printf( 'Created temporary file "%s"', $f->getURI() );
 *
 * Note: The temporary file is not deleted when the file
 * handle is closed (e.g., a call to close()), this will have
 * to be done manually.
 *
 * @package io
 */

class TempFile extends File
{  
    /**
     * Constructor
     *
     * @access  public
     * @param   string prefix default "tmp"
     */
    function TempFile( $prefix = 'tmp' ) 
	{
      	$this->File( tempnam( Util::getTempDir(), $prefix . uniqid( (double)microtime() ) ) );
    }
} // END OF TempFile

?>
