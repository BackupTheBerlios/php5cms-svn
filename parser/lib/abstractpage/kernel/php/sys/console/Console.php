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
 * Class representing system console.
 *
 * @package sys_console
 */

class Console extends PEAR
{
    /**
     * Flush output buffer.
     *
     * @static
     * @access  public
     */
    function flush()
	{
      	fflush( STDOUT );
    }

    /**
     * Write a string to standard output.
     *
     * @static
     * @access  public
     * @param   mixed* args
     */
    function write()
	{
      	$a = func_get_args();
      	fwrite( STDOUT, implode( '', $a ) );
    }
    
    /**
     * Write a string to standard output and append a newline.
     *
     * @static
     * @access  public
     * @param   mixed* args
     */
    function writeLine()
	{
      	$a = func_get_args();
      	fwrite( STDOUT, implode( '', $a ) . "\n" );
    }
    
    /**
     * Write a formatted string to standard output.
     *
     * @static
     * @access  public
     * @param   string format
     * @param   mixed* args
     * @see     php://printf
     */
    function writef()
	{
      	$a = func_get_args();
      	fwrite( STDOUT, vsprintf( array_shift( $a ), $a ) );
    }

    /**
     * Write a formatted string to standard output and append a newline.
     *
     * @static
     * @access  public
     * @param   string format
     * @param   mixed* args
     */
    function writeLinef()
	{
      	$a = func_get_args();
      	fwrite( STDOUT, vsprintf( array_shift( $a ), $a ) . "\n" );
    }
} // END OF Console

?>
