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


define( I18N_NUMBER_FLOAT,                  1 );
define( I18N_NUMBER_INTEGER,                2 );

define( I18N_REGIONAL_CHARSET,              1 );
define( I18N_REGIONAL_COUNTRY,              2 );
define( I18N_REGIONAL_LANGUAGE_NAME,        3 );
define( I18N_REGIONAL_INTL_LANGUAGE_NAME,   4 );

define( I18N_CURRENCY_LOCAL,                1 );
define( I18N_CURRENCY_SYMBOL_INTL,          2 );
define( I18N_CURRENCY_SYMBOL,               3 );

define( I18N_DATETIME_SHORT,                1 );
define( I18N_DATETIME_DEFAULT,              2 );
define( I18N_DATETIME_MEDIUM,               3 );
define( I18N_DATETIME_LONG,                 4 );
define( I18N_DATETIME_FULL,                 5 );

define( I18N_CUSTOM_FORMATS_OFFSET,       100 );


/**
 * @package locale_i18n_lib
 */
 
class I18N_Locale extends PEAR
{
	/**
	 * @access public
	 */
 	var $regionalSettings = null;
	
	/**
	 * @access public
	 */
	var $days = null;
	
	/**
	 * @access public
	 */
	var $daysAbbreviated = null;
	
	/**
	 * @access public
	 */
	var $months = null;
	
	/**
	 * @access public
	 */
	var $monthsAbbreviated = null;
	
	/**
	 * @access public
	 */
	var $dateFormats = null;
	
	/**
	 * @access public
	 */
	var $timeFormats = null;
	
	/**
	 * @access public
	 */
	var $numberFormat = null;
	
	/**
	 * @access public
	 */
	var $mondayFirst = false;
	
    /**
     * This var contains the current locale this instace works with.
     *
     * @access protected
     * @var    string  this is a string like 'de_DE' or 'en_US', etc.
     */
    var $_locale = '';

    /**
     * The locale object which contains all the formatting specs.
     *
     * @access protected
     * @var    object
     */
    var $_localeObj = null;
	
	/**
     * @access protected
     * @var    string
     */
	var $_defaultFormat = 'en_US';
	
	/**
	 * Array of available formats.
	 *
	 * @access private
	 * @var    array
	 */
	/*
	var $_availableFormats = array(
		"af",	 // Afrikaans
		"sq",	 // Albanisch
		"ar",	 // Arabisch
		"ar-SA", // Arabisch (Saudi-Arabien)
		"ar-IQ", // Arabisch (Irak)
		"ar-EG", // Arabisch (Ägypten)
		"ar-LY", // Arabisch (Libyen)
		"ar-DZ", // Arabisch (Algerien)
		"ar-MA", // Arabisch (Marokko)
		"ar-TN", // Arabisch (Tunesien)
	 	"ar-OM", // Arabisch (Oman)
		"ar-YE", // Arabisch (Jemen)
		"ar-SY", // Arabisch (Syrien)
		"ar-JO", // Arabisch (Jordanien)
		"ar-LB", // Arabisch (Libanon)
		"ar-KW", // Arabisch (Kuwait)
		"ar-AE", // Arabisch (V.A.E.)
		"ar-BH", // Arabisch (Bahrain)
		"ar-QA", // Arabisch (Katar)
		"eu",	 // Baskisch
		"bg",	 // Bulgarisch
		"be",	 // Belarussisch
		"ca",	 // Katalanisch
		"zh",	 // Chinesisch
		"zh-TW", // Chinesisch (Taiwan)
		"zh-CN", // Chinesisch (VRC)
		"zh-HK", // Chinesisch (Hongkong)
		"zh-SG", // Chinesisch (Singapur)
		"hr",	 // Kroatisch
		"cs",	 // Tschechisch
		"da",	 // Dänisch
		"nl",	 // Niederländisch (Niederlande)
		"nl-BE", // Niederländisch (Belgien)
		"en",	 // Englisch
		"en-US", // Englisch (USA)
		"en-GB", // Englisch (Vereinigtes Königreich)
		"en-AU", // Englisch (Australien)
		"en-CA", // Englisch (Kanada)
		"en-NZ", // Englisch (Neuseeland)
		"en-IE", // Englisch (Irland)
		"en-ZA", // Englisch (Südafrika)
		"en-JM", // Englisch (Jamaika)
		"en-BZ", // Englisch (Belize)
		"en-TT", // Englisch (Trinidad)
		"et",	 // Estnisch
		"fo",	 // Färöisch
		"fa",	 // Farsi
		"fi",	 // Finnisch
		"fr",	 // Französisch (Frankreich)
		"fr-BE", // Französisch (Belgien)
		"fr-CA", // Französisch (Kanada)
		"fr-CH", // Französisch (Schweiz)
		"fr-LU", // Französisch (Luxemburg)
		"gd",	 // Gälisch
		"de",	 // Deutsch (Deutschland)
		"de-CH", // Deutsch (Schweiz)
		"de-AT", // Deutsch (Österreich)
		"de-LU", // Deutsch (Luxemburg)
		"de-LI", // Deutsch (Liechtenstein)
		"el",	 // Griechisch
		"he",	 // Hebräisch
		"hi",	 // Hindi
		"hu",	 // Ungarisch
		"is",	 // Isländisch
		"in",	 // Indonesisch
		"it",	 // Italienisch (Italien)
		"it-CH", // Italienisch (Schweiz)
		"ja",	 // Japanisch
		"ko",	 // Koreanisch
		"lv",	 // Lettisch
		"lt",	 // Litauisch
		"mk",	 // Mazedonisch
		"ms",	 // Malaiisch (Malaysia)
		"mt",	 // Maltesisch
		"no",	 // Norwegisch (Bokmal)
		"no",	 // Norwegisch (Nynorsk)
		"pl",	 // Polnisch
		"pt-BR", // Portugiesisch (Brasilien)
		"pt",	 // Portugiesisch (Portugal)
		"rm",	 // Rätoromanisch
		"ro",	 // Rumänisch
		"ro-MO", // Rumänisch (Moldawien)
		"ru",	 // Russisch
		"ru-MO", // Russisch (Moldawien)
		"sr",	 // Serbisch (Kyrillisch)
		"sr",	 // Serbisch (Lateinisch)
		"sk",	 // Slowakisch
		"sl",	 // Slowenisch
		"sb",	 // Sorbisch
		"es",	 // Spanisch (Traditionell)
		"es-MX", // Spanisch (Mexico)
		"es",	 // Spanisch (Modern)
		"es-GT", // Spanisch (Guatemala)
		"es-CR", // Spanisch (Costa Rica)
		"es-PA", // Spanisch (Panama)
		"es-DO", // Spanisch (Dominikanische Republik)
		"es-VE", // Spanisch (Venezuela)
		"es-CO", // Spanisch (Kolumbien)
		"es-PE", // Spanisch (Peru)
		"es-AR", // Spanisch (Argentinien)
		"es-EC", // Spanisch (Ecuador)
		"es-CL", // Spanisch (Chile)
		"es-UY", // Spanisch (Uruguay)
		"es-PY", // Spanisch (Paraguay)
		"es-BO", // Spanisch (Bolivien)
		"es-SV", // Spanisch (El Salvador)
		"es-HN", // Spanisch (Honduras)
		"es-NI", // Spanisch (Nicaragua)
		"es-PR", // Spanisch (Puerto Rico)
		"sx",	 // Sutu
		"sv",	 // Schwedisch
		"sv-FI", // Schwedisch (Finnland)
		"th",	 // Thailändisch
		"ts",	 // Tsonga
		"tn",	 // Tswana
		"tr",	 // Türkisch
		"uk",	 // Ukrainisch
		"ur",	 // Urdu
		"vi",	 // Vietnamesisch
		"xh",	 // Xhosa
		"ji",	 // Jiddisch
		"zu"	 // Zulu	
	);
	*/
	

    /**
     * Attempts to return a concrete I18N_Locale instance based on $locale.
     *
     * @param mixed $locale  The type of concrete I18N_Locale subclass to return.
     *                       This is based on the storage driver ($locale). The
     *                       code is dynamically included.
     *
     * @return object I18N_Locale The newly created concrete I18N_Locale instance, or
     *                      false an error.
     * @access public
     */
	function &factory( $locale, $use_default = true )
	{
		if ( strlen( $locale ) == 2 )
			$locale = strtolower( $locale ) . "_" . strtoupper( $locale );
		
		$locale      = str_replace( "-", "_", $locale );
		$lang_parts  = explode( '_', $locale );
		
		$first_part  = strtolower( $lang_parts[0] );
		$second_part = strtoupper( $lang_parts[1] );
		
		$locale = $first_part . "_" . $second_part;
		
		using( 'locale.i18n.lib.I18N_Locale_' . $locale );
		
		if ( class_registered( 'I18N_Locale_' . $locale ) )
		{
            $class = "I18N_Locale_$locale";
			$this->_locale    = $locale;
            $this->_localeObj = new $class();
			
			return $this->_localeObj;
        }
		
		// degrade gracefully and try again
        $locale = $first_part;
		
		using( 'locale.i18n.lib.I18N_Locale_' . $locale );
		
		if ( class_registered( 'I18N_Locale_' . $locale ) )
		{
            $class = "I18N_Locale_$locale";
			$this->_locale    = $locale;
            $this->_localeObj = new $class();
			
			return $this->_localeObj;
        }
		
		if ( $use_default == true )
		{
			$locale = $this->_defaultFormat;
			
			using( 'locale.i18n.lib.I18N_Locale_' . $locale );
			
			if ( class_registered( 'I18N_Locale_' . $locale ) )
			{
		 		$class = "I18N_Locale_$locale";
				$this->_locale    = $locale;
				$this->_localeObj = new $class();
					
				return $this->_localeObj;
			}
		}
		
		return PEAR::raiseError( "Locale not implemented." );
	}
	
    /**
     * Attempts to return a reference to a concrete I18N_Locale instance
     * based on $locale. It will only create a new instance if no
     * I18N_Locale instance with the same parameters currently exists.
     *
     * This method must be invoked as: $var = &I18N_Locale::singleton()
     *
     * @param mixed $locale  The type of concrete I18N_Locale subclass to return.
     *                       This is based on the storage driver ($locale). The
     *                       code is dynamically included.
     *
     * @return object I18N_Locale  The concrete I18N_Locale reference, or false on an
     *                       error.
     * @access public
     */
    function &singleton( $locale, $use_default = true )
    {
        static $instances;
        
        if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $locale ) )
            $localetag = implode( ':', $locale );
        else
            $localetag = $locale;
        
        $signature = md5( strtolower( $localetag ) . '][' . implode( '][', $use_default ) );

        if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &I18N_Locale::factory( $locale, $use_default );

        return $instances[$signature];
    }
	
	/**
	 * @access public
	 */
	function hasRegionalSettings()
	{
		return ( is_null( $this->regionalSettings )? false : true );
	}

	/**
	 * @access public
	 */	
	function getRegionalSettings()
	{
		return $this->regionalSettings;
	}
	
	/**
	 * @access public
	 */
	function hasDays()
	{
		return ( is_null( $this->days )? false : true );
	}

	/**
	 * @access public
	 */	
	function getDays()
	{
		return $this->days;
	}

	/**
	 * @access public
	 */	
	function hasDaysAbbreviated()
	{
		return ( is_null( $this->daysAbbreviated )? false : true );
	}
	
	/**
	 * @access public
	 */
	function getDaysAbbreviated()
	{
		return $this->daysAbbreviated;
	}

	/**
	 * @access public
	 */	
	function hasMonths()
	{
		return ( is_null( $this->months )? false : true );
	}
	
	/**
	 * @access public
	 */
	function getMonths()
	{
		return $this->months;
	}

	/**
	 * @access public
	 */	
	function hasMonthsAbbreviated()
	{
		return ( is_null( $this->monthsAbbreviated )? false : true );
	}

	/**
	 * @access public
	 */	
	function getMonthsAbbreviated()
	{
		return $this->monthsAbbreviated;
	}

	/**
	 * @access public
	 */	
	function hasDateFormats()
	{
		return ( is_null( $this->dateFormats )? false : true );
	}

	/**
	 * @access public
	 */	
	function getDateFormats()
	{
		return $this->dateFormats;
	}
	
	/**
	 * @access public
	 */
	function hasTimeFormats()
	{
		return ( is_null( $this->timeFormats )? false : true );
	}
	
	/**
	 * @access public
	 */
	function getTimeFormats()
	{
		return $this->timeFormats;
	}

	/**
	 * @access public
	 */	
	function hasNumberFormat()
	{
		return ( is_null( $this->numberFormat )? false : true );
	}

	/**
	 * @access public
	 */	
	function getNumberFormat()
	{
		return $this->numberFormat;
	}

	/**
	 * @access public
	 */	
	function isMondayFirst()
	{
		return $this->mondayFirst;
	}
	
	/**
	 * @access public
	 */	
	function formatCurrency()
	{
		// TODO
	}
	
	/**
	 * @access public
	 */	
	function formatNumber()
	{
		// TODO
	}
	
	
	// private methods

	/**
	 * @access private
	 */	
	function _populate()
	{
		return false;
	}
} // END OF I18N_Locale

?>
