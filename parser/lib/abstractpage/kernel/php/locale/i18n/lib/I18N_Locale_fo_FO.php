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
 
class I18N_Locale_fo_FO extends I18N_Locale
{	
	/**
	 * Constructor
	 */
	function I18N_Locale_fo_FO()
	{
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->regionalSettings = array(
			I18N_REGIONAL_CHARSET				=> '',
			I18N_REGIONAL_COUNTRY				=> 'Faroe Islands',
			I18N_REGIONAL_LANGUAGE_NAME			=> 'Faeroese',
			I18N_REGIONAL_INTL_LANGUAGE_NAME	=> 'Faeroese'
		);
		
    	$this->days = array( 
			'Sunnudagur',
			'M�nadagur',
			'T�sdagur',
			'Mikudagur',
			'H�sdagur',
			'Fr�ggjadagur',
			'Leygardagur'
		);

    	$this->months = array(
			'Januar',
			'Februar',
			'Mars',
			'Apr�l',
			'Mai',
			'Juni',
			'Juli',
			'August',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		
		$this->dateFormats = array(
			I18N_DATETIME_SHORT     => 'd-m-y',
			I18N_DATETIME_DEFAULT   => 'd-M-Y',
			I18N_DATETIME_MEDIUM    => 'd-M-Y',
			I18N_DATETIME_LONG      => 'd. F Y',
			I18N_DATETIME_FULL      => 'l, d. F Y'
		);
	
    	$this->timeFormats = array(
			I18N_DATETIME_SHORT     => 'H.i',
			I18N_DATETIME_DEFAULT   => 'H.i.s',
			I18N_DATETIME_MEDIUM    => 'H.i.s',
			I18N_DATETIME_LONG      => 'H.i.s',
			I18N_DATETIME_FULL      => 'H.i.s'
		);

    	/**
     	 * @var    array   the same parameters as they have to be passed to the number_format-function
     	 */
    	$this->numberFormat = array(
			I18N_NUMBER_FLOAT   => array( '3', ',', '.' ),
			I18N_NUMBER_INTEGER => array( '0', ',', '.' )
		);
		
		/**
     	 * @var    array
     	 */
    	$this->currencyFormats = array(
			I18N_CURRENCY_LOCAL  		=> array( 'kr %', '2', ',', '.' ),
			I18N_CURRENCY_SYMBOL_INTL  	=> array( 'kr %', '2', ',', '.' ),
			I18N_CURRENCY_SYMBOL 		=> array( 'kr %', '2', ',', '.' )
		);
	}
} // END OF I18N_Locale_fo_FO

?>
