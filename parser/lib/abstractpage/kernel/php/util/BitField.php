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
 
 
define( "BITFIELD_0" , 0          );  
define( "BITFIELD_1" , 1          );  
define( "BITFIELD_2" , 2          );  
define( "BITFIELD_3" , 4          );  
define( "BITFIELD_4" , 8          );  
define( "BITFIELD_5" , 16         );  
define( "BITFIELD_6" , 32         );  
define( "BITFIELD_7" , 64         );  
define( "BITFIELD_8" , 128        );  
define( "BITFIELD_9" , 256        );  
define( "BITFIELD_10", 512        );  
define( "BITFIELD_11", 1024       );  
define( "BITFIELD_12", 2048       );  
define( "BITFIELD_13", 4096       );  
define( "BITFIELD_14", 8192       );  
define( "BITFIELD_15", 16384      );  
define( "BITFIELD_16", 32768      );  
define( "BITFIELD_17", 65536      );  
define( "BITFIELD_18", 131072     );  
define( "BITFIELD_19", 262144     );  
define( "BITFIELD_20", 524288     );  
define( "BITFIELD_21", 1048576    );  
define( "BITFIELD_22", 2097152    );  
define( "BITFIELD_23", 4194304    );  
define( "BITFIELD_24", 8388608    );  
define( "BITFIELD_25", 16777216   );  
define( "BITFIELD_26", 33554432   );  
define( "BITFIELD_27", 67108864   );  
define( "BITFIELD_28", 134217728  );  
define( "BITFIELD_29", 268435456  );  
define( "BITFIELD_30", 536870912  );  
define( "BITFIELD_31", 1073741824 );  
		

/**
 * Bit Manipulation Class
 * 
 * Think of this class as a kind of array of up to 32 true
 * or false values, squished into a tiny amount of space.
 *
 * @package util
 */
 
class BitField extends PEAR
{ 
	/**
	 * @access public
	 */
	var $bitfield = 0; 


	/**
	 * Constructor
	 *
	 * @access public
	 */	
	function BitField()
	{	
		$this->bitfield = ( $this->bitfield | 0 ); 
  	} 


	/**
	 * @access public
	 */
	function QueryBit( $bit )
	{ 
    	if ( ( $this->bitfield & $bit ) > 0 ) 
      		return true; 
    	else 
      		return false;  
	}

	/**
	 * @access public
	 */
	function SetBit( $bit, $boolean )
	{ 
    	if ( $boolean == 1 ) 
      		$this->bitfield |= $bit; 
    	else 
      		$this->bitfield &= ~$bit;  
	} 

	/**
	 * @access public
	 */
	function FlipBit( $bit )
	{ 
		$this->bitfield ^= $bit; 
	}
} // END OF BitField

?>
