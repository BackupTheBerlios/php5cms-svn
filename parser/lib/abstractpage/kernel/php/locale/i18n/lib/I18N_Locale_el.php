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


using( 'locale.i18n.lib.I18N_Locale' );


/**
 * @package locale_i18n_lib
 */
 
class I18N_Locale_el extends I18N_Locale
{	
	/**
	 * Constructor
	 */
	function I18N_Locale_el()
	{
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->regionalSettings = array(
			I18N_REGIONAL_CHARSET				=> '',
			I18N_REGIONAL_COUNTRY				=> '',
			I18N_REGIONAL_LANGUAGE_NAME			=> 'Greek',
			I18N_REGIONAL_INTL_LANGUAGE_NAME	=> 'Greek'
		);
		
	    $this->days = array( 
			'æ›–Ž‡Þ',
			'€Œ›™ –‡',
			'ï–§™',
			'ïŒ™†–™',
			'Ü “Ý™',
			'Ü‡–‡—Œà',
			'î†‰‰‡™•'
		);

	    $this->daysAbbreviated = array(
			'æ›–',
			'€Œ›',
			'ï–Ž',
			'ïŒ™',
			'ÜŒ“',
			'Ü‡–',
			'î‡‰'
		);

	    $this->months = array(
			'ƒ‡’•›‡–§•›',
			'…Œ‰–•›‡–§•›',
			'í‡–™§•›',
			'çÝ–Ž‘§•›',
			'í‡§•›',
			'ƒ•›’§•›',
			'ƒ•›‘§•›',
			'ç›‹•à—™•›',
			'îŒÝ™Œ“‰–§•›',
			'ì™‰–§•›',
			'ê•Œ“‰–§•›',
			'€ŒŒ“‰–§•›'
		);
	
   	 	$this->monthsAbbreviated = array(
			'ƒ‡’',
			'…Œ‰',
			'í‡–',
			'çÝ–',
			'í‡Ž',
			'ƒ•›’',
			'ƒ•›‘',
			'ç›‹',
			'îŒÝ',
			'ì™',
			'ê•Œ',
			'€Œ'
		);
	}
} // END OF I18N_Locale_el

?>
