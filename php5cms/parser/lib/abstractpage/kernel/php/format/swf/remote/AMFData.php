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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


/**
 * Taken from http://www.fontimages.org.uk/flash/amfdata_php.html
 *
 * @package format_swf_remote
 */
 
class AMFData extends PEAR
{	
	/**
	 * @access public
	 */
	var $fn;
	
	/**
	 * @access public
	 */
	var $fnseq;
	
	/**
	 * @access public
	 */
	var $fnargs;
	
	/**
	 * @access public
	 */
	var $hdrs;
	
	/**
	 * @access public
	 */
	var $byteorder;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function AMFData()
	{
		$this->hdrs   = array();
		$this->fnargs = array();
		$tmp = pack( "d", 1 );
		
		if ( $tmp == "\0\0\0\0\0\0\360\77" )
		{
			$this->byteorder = 'x86';
		}
		else if ( $tmp == "\77\360\0\0\0\0\0\0" )
		{
			$this->byteorder = 'ppc';
		}
		else
		{
			$this = new PEAR_Error( "Unknown byteorder." );
			return;
		}

		$d  = $GLOBALS[HTTP_RAW_POST_DATA];
		$l  = strlen( $d );
		$n  = 3;
		$nv = ord( $d[$n++] );
		
		while ( --$nv >= 0 )
		{	
			$key = $this->getStr( $d, $n );
			$n++;
			
			for ( $lo = 0, $c = 0 ; $c < 4 ; $c++ )
			{	
				$lo *= 256;
				$lo += ord( $d[$n++] );
			}
			
			$ch  = ord( $d[$n++] );
			$val = $this->parseItem( $ch, $d, $n );
			$this->$hdrs[$key] = $val;
		}
		
		$n++;
		
		if ( $ch = ord( $d[$n++] ) )
		{	
			$this->fn    = $this->getStr( $d, $n );
			$this->fnseq = $this->getStr( $d, $n );
			
			for ( $lo = 0, $c = 0 ; $c < 4 ; $c++ )
			{	
				$lo *= 256;
				$lo += ord( $d[$n++] );
			}
			
			$ch = ord( $d[$n++] ); 
			
			if ( $ch != 10 ) 
				print " ??";
			
			for ( $lo = 0, $c = 0 ; $c < 4 ; $c++ )
			{	
				$lo *= 256;
				$lo += ord( $d[$n++] );
			}
			
			for ( $ni = 0 ; $ni < $lo ; $ni++ )
			{	
				$ch = ord( $d[$n++] );
				$this->fnargs[] = $this->parseItem( $ch, $d, $n );
			}
		}
		
		return $this;
	}


	/**
	 * @access public
	 */		
	function parseItem( $ch, $d, &$n )
	{	
		switch($ch)
		{	
			case 1: // boolean
				$ch = ord( $d[$n++] );
				// settype( $ch, BOOL );
				return $ch;
				break;
				
			case 0:
				return $this->getNum( $d, $n );
				break;
				
			case 3:
				return $this->getObj( $d, $n );
				break;
				
			case 10:
				return $this->getArr( $d, $n );
				break;
				
			case 2:
				return $this->getStr( $d, $n );
				break;
				
			case 5:
				return null;
			
			default:
				print "xxx $ch ";
		}
	}

	/**
	 * @access public
	 */
	function nargs()
	{	
		return count( $this->fnargs );
	}
	
	/**
	 * @access public
	 */
	function getNum( $d, &$n )
	{	
		$ibf = "";
		
		switch ( $this->byteorder )
		{	
			case 'x86':
				for ( $nc = 7 ; $nc >= 0 ; $nc-- )
					$ibf .= $d[$n+$nc];

				break;

			case 'ppc':
				$ibf = substr( $d, $n, 8 );
				break;
		}
		
		$n  += 8;
		$zz  = unpack( "dflt", $ibf );
		
		return $zz['flt'];
	}

	/**
	 * @access public
	 */	
	function getStr( $d, &$n )
	{
		$hi   = ord( $d[$n++] );
		$lo   = ord( $d[$n++] );
		$lo  += 256 * $hi;
		$val  = substr( $d, $n, $lo );
		$n   += $lo;
		
		return $val;
	}
	
	/**
	 * @access public
	 */
	function getArr( $d, &$n )
	{	
		$ret = array();
		
		for ( $lo = 0, $c = 0 ; $c < 4 ; $c++ )
		{	
			$lo *= 256;
			$lo += ord( $d[$n++] );
		}
		
		for ( $ni = 0 ; $ni < $lo ; $ni++ )
		{	
			$ch    = ord( $d[$n++] );
			$ret[] = $this->parseItem( $ch, $d, $n );
		}
		
		return $ret;
	}

	/**
	 * @access public
	 */	
	function getObj( $d, &$n )
	{	
		$ret = array();
		
		while ( $key = $this->getStr( $d, $n ) )
		{	
			$ch  = ord( $d[$n++] );
			$val = $this->parseItem( $ch, $d, $n );
			$ret[$key] = $val;
		}
		
		$ch = ord( $d[$n++] );
		
		if ( $ch != 9 ) 
			print "obj?? $ch ";
		
		return $ret;
	}

	/**
	 * @access public
	 */
	function sendNum( $val )
	{	
		$b = pack( "d", $val );
		
		switch ( $this->byteorder )
		{	
			case 'x86':
				$r = "";
				
				for ( $n = 7 ; $n >= 0 ; $n-- )
					$r .= $b[$n];
					
				return $r;
			
			case 'ppc':
				return $b;
		}
	}
	
	/**
	 * @access public
	 */
	function sendStr( $str )
	{	
		return pack( "n", strlen( $str ) ) . $str;
	}
	
	/**
	 * @access public
	 */
	function sendObj( $val )
	{	
		if ( is_array( $val ) || is_object( $val ) )
		{	
			$first = 1;
			$num_array = 1;
			
			foreach ( $val as $key => $data )
			{	
				if ( !is_int( $key ) || ( $key < 0 ) )
				{	
					$num_array = 0;
					break;
				}
				
				if ( $first )
				{	
					$lo = $hi = $key;
					$first = 0;
				}
				else if ( $key < $lo )
				{
					$lo = $key;
				}
				else if ( $key > $hi )
				{
					$hi = $key;
				}
			}
			
			if ( $num_array )
			{	
				$ret = "\12" . pack( "N", $hi + 1 );
				
				for ( $n = 0 ; $n <= $hi ; $n++ )
					$ret .= $this->sendObj( $val[$n] );
			}
			else
			{	
				$ret = "\3";
			
				foreach ( $val as $key => $data )
				{	
					$ret .= $this->sendStr( $key  );
					$ret .= $this->sendObj( $data );
				}
				
				$ret .= $this->sendStr( '' ) . "\11";
			}
			
			return $ret;
		}
		else if ( is_integer( $val ) || is_numeric( $val ) )
		{
			return "\0" . $this->sendNum( $val );
		}
		else
		{
			return "\2" . $this->sendStr( $val );
		}
	}

	/**
	 * @access public
	 */	
	function sendResult( $data )
	{	
		header( 'Content-type: application/x-amf' );
		print "\0\0\0\0\0\1" . $this->sendStr( $this->fnseq . "/onResult" ) . $this->sendStr( "null" ) . pack( "N", -1 ) . $this->sendObj( $data );
	}
} // END OF AMFData

?>
