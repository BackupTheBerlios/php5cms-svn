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
 * @package locale
 */
 
class LanguageDetection extends PEAR
{
	/**
	 * Counter for how many languages are supported
	 * @access public
	 */
	var $langs;
	
	/**
	 * Language List [lang,q] pairs
	 * @access public
	 */
	var $lang_list;
  
	/**
	 * Original HTTP_ACCEPT_LANGUAGES
	 * @access private
	 */
	var $_accept_lang;
  
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function LanguageDetection()
	{
    	$this->_accept_lang = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
    	$this->_init();
    }
  

	/**
	 * Returns 1 if given locale PREFIX matches PRIMARY LANG PREFIX.
	 *
	 * @access public
	 */
  	function isPrimaryLang( $locale )
	{
    	$this->_reset() ;
    	
		$slist = explode( "-", $locale );
    	$lc    = $slist[0];
    	$slist = explode( "-", key( $this->lang_list ) );
    	$tlc   = $slist[0];
    
		if ( $lc == $tlc )
      		return true;
    	else
      		return false;
   	}
  
	/**
	 * Return full locale string for primary language.
	 *
	 * @access public
	 */
  	function getPrimaryLang()
	{
    	$this->_reset();
    	return key( $this->lang_list );
    }
  
	/**
	 * Return PREFIX string for primary language.
	 *
	 * @access public
	 */
  	function getPrimaryPrefix()
	{
    	$this->_reset();
    	$slist = explode( "-", key( $this->lang_list ) );
    
		return $slist[0];
    }

	/**
	 * Return Q value for the PREFIX in given locale.
	 *
	 * @access public
	 */  
	function findLang( $loc )
	{
		$found = 0;
		$this->_reset();
		$slist = explode( "-", $locale );
		$lc = $slist[0];
		
    	while ( $curr = each( $this->lang_list ) )
		{
      		$slist = explode( "-", $curr[0] );
      		$tlc   = $slist[0];
			
      		if ( $tlc == $lc )
				return $curr[1];
      	}
    
		return false;
    }

	/**
	 * Fills sublist with an ordered list of accepted languages from given array
     * based on each locale PREFIX... ordering is on Q DESC so first element in
     * the array is best match from the given list.
	 *
	 * @access public
	 */  
  	function getList( $arr, &$sublist )
	{
    	$sublist = array();
    	$this->_reset();
    
		while ( $curr = each( $this->lang_list ) )
		{
      		$slist = explode( "-", $curr[0] );
      		$loc   = $slist[0];
      
	  		if ( in_array( $loc, $arr ) )
        		$sublist += array( $loc => $curr[1] );
        }
    
		if ( count( $sublist ) )
		{
      		// This will order based on Q DESC
      		arsort( $sublist );
      		return true;
    	}
		else
		{
      		return false;
      	}
    }
	
  
  	// private methods
	
	/**
	 * Initialize language list.
	 *
	 * @access private
	 */
  	function _init()
  	{
    	$this->lang_list = array() ;
    	$list = explode( ",", $this->_accept_lang );
    
		for ( $i = 0; $i < count( $list ); $i++ )
		{
      		$pos = strchr( $list[$i], ";" );
      
	  		if ( $pos === false )
			{
        		// No Q it is only a locale...
        		$this->lang_list += array( $list[$i] => 100 );
        		$this->langs++;
      		}
			else
			{
        		// Has a Q rating        
        		$q   = explode( ";", $list[$i] );
        		$loc = $q[0];
        		$q   = explode( "=", $q[1] );
        
				$this->lang_list += array( $loc => ( $q[1] * 100 ) );
        		$this->langs++;
        	}
      	}
    
		return ( $this->langs );
    }

	/**
	 * @access public
	 */  
	function _reset()
	{
    	reset( $this->lang_list );
	}
} // END OF LanguageDetection

?>
