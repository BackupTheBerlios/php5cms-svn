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


/*
 * Define default filehandles
 */
define( 'STDIN',  fopen( 'php://stdin',  'r' ) );          
define( 'STDOUT', fopen( 'php://stdout', 'w' ) );          
define( 'STDERR', fopen( 'php://stderr', 'w' ) );         


/**
 * Standard I/O streams.
 *
 * @link http://www.opengroup.org/onlinepubs/007908799/xsh/stdin.html
 * @package util
 */

class StdStream extends PEAR
{  
    /**
     * Retrieve a file object.
     *
     * Example:
     *
	 * $stdout = &StdStream::get( STDOUT );
     * $stdout->write('Hello');
     *
     * @static
     * @access  public
     * @param   resource handle one of STDIN | STDOUT | STDERR
     * @return  &File
     */
    function &get( $handle ) 
	{
      	static $f = array();
      
      	if ( !isset( $f[$handle] ) )
        	$f[$handle] = &new File( $handle );
      
      	return $f[$handle];
    }
} // END OF StdStream

?>
