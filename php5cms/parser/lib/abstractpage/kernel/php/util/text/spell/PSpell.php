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
 * @package util_text_spell
 */
 
class PSpell extends PEAR 
{
	/**
	 * @access public
	 */
    var $result;
	
	/**
	 * @access public
	 */
    var $pspell_link; 


	/**
	 * Constructor
	 *
	 * @access public
	 */
    function PSpell() 
	{
        if ( !$this->pspell_link = pspell_new( APP_LANGUAGE, "", "", "", ( PSPELL_FAST | PSPELL_RUN_TOGETHER ) ) )  
            $this->dummy = true; 
    } 

     
	/**
	 * @access public
	 */
    function checkphrase( $str ) 
	{ 
        $words = split( " ", $str ); 
       
	    foreach ( $words as $key => $word ) 
		{ 
            $this->result["word"][]    = $word; 
            $this->result["check"][]   = $this->check( $word ); 
            $this->result["suggest"][] = $this->suggest( $word ); 
        } 
		
        return true; 
    } 

	/**
	 * @access public
	 */     
    function check( $word ) 
	{ 
        if ( $this->dummy ) 
		{ 
            return true; 
        } 
		else 
		{ 
            if ( pspell_check( $this->pspell_link, $word ) )  
                return true; 
            else  
                return false; 
        } 
    } 

	/**
	 * @access public
	 */     
    function suggest( $word ) 
	{ 
        if ( $this->dummy ) 
		{ 
            return false; 
        } 
		else 
		{ 
            if ( $sug = pspell_suggest( $this->pspell_link, $word ) )  
                return $sug; 
            else  
                return false; 
        } 
    } 
} // END OF PSpell

?>
