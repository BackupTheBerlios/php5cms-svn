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
 
class SpellCheck extends PEAR
{
	/**
	 * @access public
	 */
	var $pspell_handle;
	
	/**
	 * @access public
	 */
	var $pspell_cfg_handle; 
	
	/**
	 * @access public
	 */
	var $personal_path; 
	
	/**
	 * @access public
	 */
	var $skip_len; 
	
	/**
	 * @access public
	 */
	var $mode; 
	
	/**
	 * @access public
	 */
	var $personal = false;
   
   
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SpellCheck( $dict = "en", $pconfig = "" )
	{ 
		$this->personal_path = '/path/to/personal_dict/';
		$this->mode          = "PSPELL_NORMAL";
		$this->skip_len      = 3;
		
		$this->pspell_cfg_handle = pspell_config_create( $dict ); 

		pspell_config_ignore( $this->pspell_cfg_handle, $this->skip_len ); 
		pspell_config_mode( $this->pspell_cfg_handle, $this->mode ); 

		if ( $pconfig != "" )
		{ 
			$this->pspell_handle = pspell_config_personal( $this->pspell_cfg_handle, $this->personal_path . $pconfig . ".pws" ); 
			$this->personal = true; 
		} 

		$this->pspell_handle = pspell_new_config( $this->pspell_cfg_handle ); 
	} 
   
	
	/**
	 * @access public
	 */
	function check( $word )
	{ 
		return pspell_check( $this->pspell_handle, $word ); 
	} 

	/**
	 * @access public
	 */
	function suggest( $word )
	{ 
		return pspell_suggest( $this->pspell_handle, $word ); 
	} 

	/**
	 * @access public
	 */
	function add( $word )
	{ 
     	if ( !$this->personal )
			return false; 

		return pspell_add_to_personal( $this->pspell_handle, $word ); 
	} 

	/**
	 * @access public
	 */
	function close()
	{ 
		if ( !$this->personal )
			return; 

		return pspell_save_wordlist( $this->pspell_handle ); 
	} 
} // END OF SpellCheck

?>
