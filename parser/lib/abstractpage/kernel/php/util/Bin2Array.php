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
 * @package util
 */
 
class Bin2Array extends PEAR
{
	/**
	 * array representation (BE)
	 * @access public
	 */
	var $abin;
	
	/**
	 * string binary
	 * @access public
	 */
    var $sbin;
	
	/**
	 * string hexa
	 * @access public
	 */
    var $hbin;

	/**
	 * integer
	 * @access public
	 */
    var $ibin;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Bin2Array( $p1 )
	{
		$fret = 1;
		$fret = $this->etraduce( gettype( $p1 ), $p1 );
		
		return $fret;
    }


	/**
	 * @access public
	 */
    function erange( $val )
	{
		$fret = 1;
		
		if ( ( $val == 1 || $val == 0 ) )
		{
			$fret = 0;
			$this->error = "Bit out of rage.";
		}
		
		return $fret;
    }

	/**
	 * @access public
	 */
	function ehex( $v )
	{
		// HEX-INT
		switch ( strtolower( $v ) )
		{
			case "f":
				$iv = 15;
				break;
				
			case "e":
				$iv = 14;
				break;
				
			case "d":
				$iv = 13;
				break;
				
			case "c":
				$iv = 12;
				break;
				
			case "b":
				$iv = 11;
				break;
				
			case "a":
				$iv = 10;
				break;
				
			default:
				$iv = $v;
		}
		
		return $iv;
    }

	/**
	 * @access public
	 */
    function etable_hex2bin( $hstr )
	{
		$vi = 0;
		
		for ( $i = 0; $i < strlen( $hstr ); $i++ )
			$vi += $this->ehex( $hstr[$i] ) * ( pow( 16, strlen( $hstr ) - 1 - $i ) );
 		
		return sprintf( "%b", $vi );
    }

	/**
	 * @access public
	 */
	function emake_arr( $bstr )
	{
		$a = array();
		
		for ( $i = 0; $i < strlen( $bstr ); $i++ )
			$a[$i] = $bstr[$i];
             
		return $a;
    }
	
	/**
	 * @access public
	 */
    function etable_bin2hex( $bstr )
	{
		$vi = 0;
		$vi = $this->etable_bin2int( $bstr );
		
		return sprintf( "%X", $vi );
    }

	/**
	 * @access public
	 */
    function etable_bin2int( $bstr )
	{
		$vi = 0;
		
		for ( $i = 0; $i < strlen( $bstr ); $i++ )
			$vi += $bstr[$i] * ( pow( 2, strlen( $bstr ) - 1 - $i ) );
             
		return $vi;
    }

	/**
	 * @access public
	 */
    function emake_str( $abin )
	{
		$vs = "";
		
		for ( $i = 0; $i < count( $abin ); $i++ )
			$vs .= ( ( $abin[$i] )? "1" : "0" );
		
		return $vs;
    }

	/**
	 * @access public
	 */
    function echeck_arr( $parr )
	{
		for ( $i = 0; $i < count( $parr ); $i++ )
		{
			if ( $parr[$i] )
				$parr[$i] = 1;
            else
				$parr[$i] = 0;
		}
		
		return true;
    }

	/**
	 * @access public
	 */
	function etraduce( $from, $dat )
	{
		$fret = 1;
		
		switch ( $from )
		{
			case "array":
				if ( $this->echeck_arr( &$dat ) )
				{
					$this->abin = $dat;
					$this->sbin = $this->emake_str( $this->abin );
					$this->hbin = $this->etable_bin2hex( $this->sbin );
					$this->ibin = $this->etable_bin2int( $this->sbin );
				}
				else
				{
					$this->error = "Array contains invalid values.";
					$fret = 0;
                }
				
				break;
			
			case "integer":
				$this->sbin = sprintf( "%b", $dat );
				$this->hbin = $this->etable_bin2hex( $this->sbin );
				$this->ibin = $this->etable_bin2int( $this->sbin );
				$this->abin = $this->emake_arr( $this->sbin );
				
				break;
          	
			case "double":
                $this->etraduce( gettype( intval( $dat ) ), intval( $dat ) );
                break;
				
			case "boolean":
				$dat = ( $dat )? 1 : 0;
                $this->sbin = $dat? "1" : "0";
                $this->hbin = $dat? "1" : "0";
                $this->ibin = $dat;
                $this->abin[0] = $dat;
                
				break;
				
          case "string":
                if ( eregi( "[^0-9a-f]", $dat ) )
				{
					$this->error = "String contains invalid characters, only 0-9a-f.";
					$fret = 0;
				}
				else
				{
					$dat = eregi_replace( "[h-z]", "", $dat );
                   
				   	if ( eregi( "[^0-9]", $dat ) )
					{
                      	$this->hbin = $dat;
                      	$this->sbin = $this->etable_hex2bin( $dat );
					}
					else if ( eregi( "[^0-1]", $dat ) )
					{
						$this->ibin = intval( $dat );
						$this->sbin = sprintf( "%b", $dat );
						$this->hbin = $this->etable_bin2hex( $this->sbin );
						$this->ibin = $this->etable_bin2int( $this->sbin );
						$this->abin = $this->emake_arr( $this->sbin );
					}
					else
					{
						$this->hbin = $this->etable_bin2hex( $dat );
						$this->sbin = $dat;
					}
					
					$this->ibin = $this->etable_bin2int( $this->sbin );
					$this->abin = $this->emake_arr( $this->sbin );
				}
                
				break;
			
			case "object":
				if ( !( @$dat->isBin2Array() ) )
				{
					$this->error = "Initialization error.";
					$fret = 0;
				}
				else
				{
					$this->abin = $dat->abin;
					$this->sbin = $dat->sbin;
					$this->hbin = $dat->hbin;
					$this->ibin = $dat->ibin;
				}
				
				break;
			
			case "resource":
				
			default:
				$this->error = "Initialization error.";
                $fret = 0;
				
                break;
       }
	   
       return $fret;
	}

	/**
	 * @access public
	 */
	function egetinteger( $dat )
	{
		$otmp = new Bin2Array( $dat );
		$ival = $otmp->ibin;
		$otmp->destroy();
		unset( $otmp );
		
		return $ival;
	}

	/**
	 * @access public
	 */
	function op( $opcode, $data )
	{
		switch ( strtolower( $opcode ) )
		{
			case "add":
				$this->add( $data );
				break;
				
			case "mul":
				$this->mul( $data );
				break;
				
			case "div":
				$this->div( $data );
				break;
			
			case "nul":
				return false;
				break;
		}
	}

	/**
	 * @access public
	 */
	function add( $dat )
	{
		$ival=$this->egetinteger( $dat );
		$this->etraduce( gettype( $this->ibin + $ival ), $this->ibin + $ival );
    }

	/**
	 * @access public
	 */
    function mul( $dat )
	{
		$ival = $this->egetinteger( $dat );
		$this->etraduce( gettype( $this->ibin * $ival ), $this->ibin * $ival );
    }

	/**
	 * @access public
	 */
	function div( $dat )
	{
		$ival = $this->egetinteger( $dat );
       	$this->etraduce( gettype( $this->ibin / $ival ), $this->ibin / $ival );
    }

	/**
	 * @access public
	 */
	function sub( $dat )
	{
       	$ival = $this->egetinteger( $dat );
       	$this->etraduce( gettype( $this->ibin - $ival ), $this->ibin - $ival );
    }

	/**
	 * @access public
	 */
	function isBin2Array()
	{
		return true;
	}

	/**
	 * @access public
	 */
	function destroy()
	{
		return true;
    }
} // END OF Bin2Array

?>
