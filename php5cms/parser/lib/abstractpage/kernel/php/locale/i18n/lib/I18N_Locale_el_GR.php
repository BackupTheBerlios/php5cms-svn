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
 
class I18N_Locale_el_GR extends I18N_Locale
{	
	/**
	 * Constructor
	 */
	function I18N_Locale_el_GR()
	{
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->regionalSettings = array(
			I18N_REGIONAL_CHARSET				=> '',
			I18N_REGIONAL_COUNTRY				=> 'Greece',
			I18N_REGIONAL_LANGUAGE_NAME			=> 'Greek',
			I18N_REGIONAL_INTL_LANGUAGE_NAME	=> 'Greek'
		);
		
	    $this->days = array( 
			'曖����',
			'�������',
			'��',
			'����',
			'ܠ�ݙ�',
			'܇�������',
			'����'
		);

	    $this->daysAbbreviated = array(
			'曖',
			'���',
			'',
			'',
			'܌�',
			'܇�',
			''
		);

	    $this->months = array(
			'����������',
			'�����������',
			'퇖����',
			'�ݖ�����',
			'퇧��',
			'�������',
			'�������',
			'盋������',
			'�ݙ�������',
			'쐙������',
			'ꕌ������',
			'����������'
		);
	
   	 	$this->monthsAbbreviated = array(
			'���',
			'���',
			'퇖',
			'�ݖ',
			'퇎',
			'����',
			'����',
			'盋',
			'��',
			'쐙',
			'ꕌ',
			'���'
		);

    	/**
     	 * @var    array
         */
    	$this->currencyFormats =  array(
			// warning: incorrect iso
			I18N_CURRENCY_LOCAL  		=> array( '% Euro', '2', ',', '.' ),
			I18N_CURRENCY_SYMBOL_INTL  	=> array( '% EUR',  '2', ',', '.' ),
			I18N_CURRENCY_SYMBOL 		=> array( '% �',    '2', ',', '.' )
		);	
	}
} // END OF I18N_Locale_el_GR

?>
