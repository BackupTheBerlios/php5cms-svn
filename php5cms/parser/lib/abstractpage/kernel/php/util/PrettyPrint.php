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
 * This class is used to print out the contents of PHP variables and expressions
 * in a pretty-printed format.
 *
 * @package util
 */

class PrettyPrint extends PEAR
{
	/**
	 * @access public
	 */
	var $compact;
	
	/**
	 * @access public
	 */
	var $tab;


	/**
	 * Constructor
	 *
	 * The compact and tab object vars will be set based on the passed parameters.
	 *
	 * @param	$_compact  boolean signifying if the spacing separating the
	 * 			contents of the variable should be compact or be the spacing
	 * 			specified in the $tab object var
	 * @param	$_tab  spacing that will separate the contents of the variable that
	 * 			that will be printed out provided the $compact object var is FALSE
	 * @access  public
	 */
	function PrettyPrint( $_compact = false, $_tab = '' )
	{
		$this->compact = $_compact;
		
		if ( !$this->compact )
			$this->tab = $_tab? $_tab : '    ';
		else
			$this->tab = '';
	}

	/**
	 * This public function calls the _pod_encode() method which will return the
	 * contents of the passed parameter $_ref in a pretty-printed format.
	 *
	 * @param	$_ref  variable containing number, string, or array
	 * @return	pretty-printed format of the contents of the passed
	 * 			parameter $_ref.
	 * @access public
	 */
	function Out( $_ref )
	{
		return $this->_pod_encode( $_ref );
	}


	// private methods
	
	/**
	 * This private function returns the value of the passed variable, in single
	 * quotes if the variable is a string.
	 *
	 * @param	$str  variable containing string or number
	 * @return	passed variable (in single quotes if the variable is a string)
	 * @access  private
	 */
	function _pod_quote( &$str )
	{
		if ( !isset( $str ) )
			return 'undef';
  
		if ( !is_string( $str ) )
			return $str;
  
		$str = strtr( $str, "'", "\\'" );
		return "'" . $str . "'";
	}

	/**
	 * This private function returns the contents of the passed parameter $r in a
	 * pretty-printed format.
	 *
	 * @param	$r  variable containing number, string, or array
	 * @param	$level  level of recursion based on array index whose
	 *			turn it is to have its contents formatted, does not
	 *			apply if $r is not an array
	 * @param	$inden  string to supplement tab object var with, which
	 *			will separate the contents of the passed parameter $r.
	 * @return	pretty-printed format of the contents of the passed
	 * 			parameter $r.
	 * @access  private
	 */
	function _pod_encode( &$r, $level = 0, $inden = '' )
	{
		$inden .= $this->tab;

		if ( !isset( $r ) )
		{
			echo( "undefined argument" );
			return 'undef';
		}
		
		if ( $level > 20 )
		{
			echo( "recursive structure" );
			return '';
		}
		
		$result = '';
		
		if ( !is_array( $r ) )
		{
			$result .=  $this->_pod_quote( $r );
		}
		else if ( !isset( $r[0] ) )
		{
   			if ( count( $r ) == 0 )
			{
				$result .= "{ },";
			}
			else
			{
				$result .= "{\n$inden";
				
				while ( list( $k, $v ) = each( $r ) )
				{
					$qk = $this->_pod_quote( $k );
    				$result .=  "$qk => ";
					
					if ( !isset( $r[$k] ) )
					{
						$result .=  "undef,\n$inden";
					}
					else
					{
						$el = $r[$k];
						
						if ( is_array( $el ) )
						{
							$result .=  $this->_pod_encode( $el, $level + 1, $inden ) . "\n$inden";
						}
						else
						{
							$qel = $this->_pod_quote( $el );
							$result .=  "$qel,\n$inden";
						}
					}
				}
				
				$result .=  "},";
			}
		}
		else
		{	
			if ( count( $r ) == 0 )
			{
				$result .= '[ ],';
			}
			else if ( count( $r ) < 10 && $this->_is_flat( $r ) )
			{
				$vals = array();
				
				foreach ( $r as $el )
					$vals[] = $this->_pod_quote( $el );

				$result .= '[ ' . join( ', ', $vals ) . ' ],';
			}
			else
			{
				$result .=  "[\n$inden";
				
				for ( $i = 0; $i < count( $r ); $i++ )
				{
					$el = $r[$i];
					
					if ( !isset( $el ) )
					{
						$result .=  "undef,\n$inden";
					}
					else if ( is_array( $el ) )
					{
						$result .=  $this->_pod_encode( $el, $level + 1, $inden ) . "\n$inden";
					}
					else
					{
						$qel = $this->_pod_quote( $el );
						$result .=  "$qel,\n$inden";
					}
				}
				
				$result .=  "],";
			}
		} 
		
		return $result;
	}

	/**
	 * This private function returns TRUE if the contents of the passed array does
	 * not contain any arrays within itself, FALSE otherwise.
	 *
	 * @param	$r  array
	 * @return	TRUE/FALSE signifying if the contents of the passed array
	 * 			does not contain any arrays within itself.
	 * @access  private
	 */
	function _is_flat( $r )
	{
		foreach ( $r as $el )
		{
			if ( is_array( $el ) )
				return false;
		}
		
		return true;
	}
} // END OF PrettyPrint

?>
