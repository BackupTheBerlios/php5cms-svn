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
 
class LocaleStrings extends PEAR
{
	var $codetable;
	
	
	/**
	 * Constructor
	 */
	function LocaleStrings()
	{
		$this->_populate();
	}
	
	
	function getFull( $short )
	{
		if ( !in_array( $short, $this->codetable ) )
			return false;
		else
			return $this->codetable[$short];
	}
	
	
	// private
	
	function _populate()
	{
		$this->codetable = array(
			"af" 	=> "Afrikaans",
			"sq"	=> "Albanian",
			"ar_SA" => "Arabic (Saudi Arabia)",
			"ar_IQ" => "Arabic (Iraq)",
			"ar_EG" => "Arabic (Egypt)",
			"ar_LY" => "Arabic (Libya)",
			"ar_DZ" => "Arabic (Algeria)",
			"ar_MA" => "Arabic (Morocco)",
			"ar_TN" => "Arabic (Tunisia)",
			"ar_OM" => "Arabic (Oman)",
			"ar_YE" => "Arabic (Yemen)",
			"ar_SY" => "Arabic (Syria)",
			"ar_JO" => "Arabic (Jordan)",
			"ar_LB" => "Arabic (Lebanon)", 
			"ar_KW" => "Arabic (Kuwait)",
			"ar_AE" => "Arabic (U.A.E.)",
			"ar_BH" => "Arabic (Bahrain)",
			"ar_QA" => "Arabic (Qatar)",
			"eu" 	=> "Basque",
			"bg" 	=> "Bulgarian",  
			"be" 	=> "Belarusian",
			"ca" 	=> "Catalan",
			"zh_TW" => "Chinese (Taiwan)",
			"zh_CN" => "Chinese (PRC)",
			"zh_HK" => "Chinese (Hong Kong SAR)",
			"zh_SG" => "Chinese (Singapore)",
			"hr" 	=> "Croatian",
			"cs" 	=> "Czech",
			"da" 	=> "Danish",
			"nl" 	=> "Dutch (Standard)",
			"nl_BE" => "Dutch (Belgium)",
			"en" 	=> "English",
			"en_US" => "English (United States)",
			"en_GB" => "English (Great Britain)",
			"en_AU" => "English (Australia)",
			"en_CA" => "English (Canada)",
			"en_NZ" => "English (New Zealand)",
			"en_IE" => "English (Ireland)",
			"en_ZA" => "English (South Africa)",
			"en_JM" => "English (Jamaica)",
			"en_BZ" => "English (Belize)",
			"en_TT" => "English (Trinidad)",
			"et" 	=> "Estonian",
			"fo" 	=> "Faeroese",
			"fa" 	=> "Farsi",
			"fi" 	=> "Finnish",
			"fr" 	=> "French (Standard)",
			"fr_BE" => "French (Belgium)",
			"fr_CA" => "French (Canada)",
			"fr_CH" => "French (Switzerland)",
			"fr_LU" => "French (Luxembourg)",
			"gd" 	=> "Gaelic (Scotland)",
			"gd_IE" => "Gaelic (Ireland)",
			"de" 	=> "German (Standard)",
			"de_CH" => "German (Switzerland)",  
			"de_AT" => "German (Austria)",
			"de_LU" => "German (Luxembourg)",  
			"de_LI" => "German (Liechtenstein)",
			"el" 	=> "Greek",
			"he" 	=> "Hebrew",
			"hi" 	=> "Hindi",
			"hu" 	=> "Hungarian",
			"is" 	=> "Icelandic",
			"in" 	=> "Indonesian",
			"it" 	=> "Italian (Standard)",  
			"it_CH" => "Italian (Switzerland)",
			"ja" 	=> "Japanese",
			"ko" 	=> "Korean",
			"ko" 	=> "Korean (Johab)",  
			"lv" 	=> "Latvian",
			"lt" 	=> "Lithuanian",  
			"mk" 	=> "FYRO Macedonian",
			"ms" 	=> "Malaysian",
			"mt" 	=> "Maltese",
			"no" 	=> "Norwegian",
			"pl" 	=> "Polish",
			"pt_BR" => "Portuguese (Brazil)",
			"pt" 	=> "Portuguese (Portugal)",
			"rm" 	=> "Rhaeto-Romanic",
			"ro" 	=> "Romanian",
			"ro_MO" => "Romanian (Moldavia)",
			"ru" 	=> "Russian",
			"ru_MO" => "Russian (Moldavia)",
			"sz" 	=> "Sami (Lappish)",
			"sr" 	=> "Serbian (Cyrillic)",
			"sr" 	=> "Serbian (Latin)",
			"sk" 	=> "Slovak",
			"sl" 	=> "Slovenian",
			"sb" 	=> "Sorbian",
			"es" 	=> "Spanish",
			"es_MX" => "Spanish (Mexico)",
			"es_GT" => "Spanish (Guatemala)",
			"es_CR" => "Spanish (Costa Rica)",
			"es_PA" => "Spanish (Panama)",
			"es_DO" => "Spanish (Dominican Republic)",
			"es_VE" => "Spanish (Venezuela)",
			"es_CO" => "Spanish (Colombia)",
			"es_PE" => "Spanish (Peru)",
			"es_AR" => "Spanish (Argentina)",
			"es_EC" => "Spanish (Ecuador)",
			"es_CL" => "Spanish (Chile)",
			"es_UY" => "Spanish (Uruguay)",
			"es_PY" => "Spanish (Paraguay)", 
			"es_BO" => "Spanish (Bolivia)",
			"es_SV" => "Spanish (El Salvador)",
			"es_HN" => "Spanish (Honduras)",
			"es_NI" => "Spanish (Nicaragua)", 
			"es_PR" => "Spanish (Puerto Rico)",
			"sx" 	=> "Sutu",
			"sv" 	=> "Swedish",
			"sv_FI" => "Swedish (Finland)",
			"th" 	=> "Thai",
			"ts" 	=> "Tsonga", 
			"tn" 	=> "Tswana",
			"tr" 	=> "Turkish", 
			"uk" 	=> "Ukrainian",
			"ur" 	=> "Urdu",
			"ve" 	=> "Venda",
			"vi" 	=> "Vietnamese",  
			"xh" 	=> "Xhosa",
			"ji" 	=> "Yiddish",
			"zu" 	=> "Zulu"	
		);
	}
} // END OF LocaleStrings

?>
