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


define( 'BIG_ENDIAN',    0x0000 );
define( 'LITTLE_ENDIAN', 0x0001 );


/**
 * Intel's 80x86 processors and their clones are little endian. Sun's 
 * SPARC, Motorola's 68K, and the PowerPC families are all big endian. 
 *
 * @link http://www.netrino.com/Publications/Glossary/Endianness.html
 * @package sys
 */

class EndiannessUtil
{  
    /**
     * Retrieves the name of a byteorder.
     *
     * Example:
     * var_dump( EndiannessUtil::nameOf( EndiannessUtil::nativeOrder() ) );
     *
     * @static
     * @access  public
     * @param   int order
     * @return  string name
     */
    function nameOf( $order ) 
	{
      	switch ( $order ) 
		{
        	case BIG_ENDIAN: 
				return 'BIG_ENDIAN';
        
			case LITTLE_ENDIAN: 
				return 'LITTLE_ENDIAN';
      	}
      
	  	return '(unknown)';
    }

    /**
     * Retrieves this system's native byte order.
     *
     * @static
     * @access  public
     * @return  int either BIG_ENDIAN or LITTLE_ENDIAN
     * @throws  Error in case the byte order cannot be determined
     */
    function nativeOrder()
	{
      	switch ( pack( 'd', 1 ) ) 
		{
        	case "\0\0\0\0\0\0\360\77": 
				return LITTLE_ENDIAN;
        
			case "\77\360\0\0\0\0\0\0": 
				return BIG_ENDIAN;
      	}

		return PEAR::raiseError( 'Unexpected result: ' . addcslashes( pack( 'd', 1 ), "\0..\17" ) );
    }
    
    /**
     * Returns the network byte order.
     *
     * @access  static
     * @return  int network byte order
     * @link    http://www.hyperdictionary.com/computing/network+byte+order
     */
    function networkOrder()
	{
      	return BIG_ENDIAN;
    }
} // END OF EndiannessUtil

?>
