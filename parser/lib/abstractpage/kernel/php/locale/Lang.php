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
|Authors: Jon Parise <jon@horde.org>                                   |
|         Chuck Hagenbuch <chuck@horde.org>                            |
|         Jan Schneider <jan@horde.org>                                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * The Lang class provides common methods for handling language detection
 * and selection.
 *
 * @package locale
 */
 
class Lang extends PEAR
{
	/**
	 * The langauge to fall back on if we cannot determine one any other
     * way (user choice, preferences, HTTP_ACCEPT_LANGUAGE).
	 * @access private
	 */
	var $_defaults_language = 'en_US';
	
	/**
	 * The charset to fall back on if we cannot determine one any other
     * way (chosen language, HTTP_ACCEPT_CHARSETS)
	 * @access private
	 */
	var $_default_charset = 'ISO-8859-1';

	/**
	 * @access private
	 */
	var $_languages = array(
		'bg_BG' 		=>'Bulgarian',
		'ca_ES' 		=>'Catal&agrave;',
		'zh_CN' 		=>'Chinese (Simplified)',
		'zh_TW' 		=>'Chinese (Traditional)',
		'cs_CZ' 		=>'Czech',
		'da_DK' 		=>'Dansk',
		'de_DE' 		=>'Deutsch',
		'en_GB' 		=>'English (GB)',
		'en_US' 		=>'English (US)',
		'es_ES' 		=>'Espa&ntilde;ol',
		'et_EE' 		=>'Eesti',
		'fr_FR' 		=>'Fran&ccedil;ais',
		'el_GR' 		=>'Greek',
		'it_IT' 		=>'Italiano',
		'ja_JP' 		=>'Japanese',
		'ko_KR' 		=>'Korean',
		'lv_LV' 		=>'Latvie&scaron;u',
		'lt_LT' 		=>'Lietuviuk',
		'hu_HU' 		=>'Magyar',
		'nl_NL' 		=>'Nederlands',
		'nb_NO' 		=>'Norsk bokm&aring;l',
		'nn_NO' 		=>'Norsk nynorsk',
		'pl_PL' 		=>'Polski',
		'pt_PT' 		=>'Portugu&ecirc;s',
		'pt_BR' 		=>'Portugu&ecirc;s Brasileiro',
		'ro_RO' 		=>'Romana',
		'ru_RU' 		=>'Russian (Windows)',
		'ru_RU.KOI8-R' 	=>'Russian (KOI8-R)',
		'sk_SK' 		=>'Slovak',
		'sl_SI' 		=>'Slovenscina',
		'fi_FI' 		=>'Suomi',
		'sv_SE' 		=>'Svenska',
		'uk_UA' 		=>'Ukranian'
	);

	/**
	 * @access private
	 */
	var $_charsets = array(
		'bg_BG' 		=> 'windows-1251',
		'cs_CZ' 		=> 'ISO-8859-2',
		'el_GR' 		=> 'ISO-8859-7',
		'et_EE' 		=> 'ISO-8859-13',
		'hu_HU' 		=> 'ISO-8859-2',
		'ja_JP' 		=> 'SHIFT_JIS',
		'ko_KR' 		=> 'EUC-KR',
		'lt_LT' 		=> 'ISO-8859-13',
		'pl_PL' 		=> 'ISO-8859-2',
		'ru_RU' 		=> 'windows-1251',
		'ru_RU.KOI8-R' 	=> 'KOI8-R',
		'sk_SK'	 		=> 'ISO-8859-2',
		'sl_SI' 		=> 'ISO-8859-2',
		'uk_UA' 		=> 'KOI8-U',
		'zh_CN' 		=> 'GB2312',
		'zh_TW' 		=> 'BIG5'
	);

	/**
 	 * Aliases for languages with different browser and gettext codes
	 * @access private
 	 */
	var $_aliases = array(
		'bg' => 'bg_BG',
		'cs' => 'cs_CZ',
		'ca' => 'ca_ES',
		'da' => 'da_DK',
		'de' => 'de_DE',
		'el' => 'el_GR',
		'en' => 'en_US',
		'es' => 'es_ES',
		'et' => 'et_EE',
		'fi' => 'fi_FI',
		'fr' => 'fr_FR',
		'hu' => 'hu_HU',
		'it' => 'it_IT',
		'ja' => 'ja_JP',
		'ko' => 'ko_KR',
		'lt' => 'lt_LT',
		'nl' => 'nl_NL',
		'nn' => 'nn_NO',
		'no' => 'nb_NO',
		'pl' => 'pl_PL',
		'pt' => 'pt_PT',
		'ro' => 'ro_RO',
		'ru' => 'ru_RU',
		'sk' => 'sk_SK',
		'sl' => 'sl_SI',
		'sv' => 'sv_SE',
		'uk' => 'uk_UA'
	);

	/**
 	 * Multi-language spelling support
	 * @access private
 	 */
	var $_spelling = array(
		'cs_CZ' => '-T latin2 -d czech',
		'da_DK' => '-d dansk',
		'de_DE' => '-T latin1 -d deutsch',
		'el_GR' => '-T latin1 -d ellinika',
		'en_GB' => '-d british',
		'en_US' => '-d american',
		'es_ES' => '-d espanol',
		'fr_FR' => '-d francais',
		'it_IT' => '-T latin1 -d italian',
		'nl_NL' => '-d nederlands',
		'pl_PL' => '-d polish',
		'pt_BR' => '-d br',
		'pt_PT' => '-T latin1 -d portuguese',
		'ru_RU' => '-d russian',
		'sv_SE' => '-d svenska'
	);
	
	
    /**
     * Selects the most preferred language for the current client session.
     *
     * @return string        The selected language abbreviation.
     * @access public
     */
    function select()
    {
		// instance of Preferences
        global $prefs;

        $lang = Util::getFormData( 'new_lang' );

        // First, check if language pref is locked and if so set it to its value.
        if ( isset( $prefs ) && $prefs->isLocked( 'language' ) ) 
		{
            $language = $prefs->getValue( 'language' );
        } 
		// Check if the user selected a language from the login screen.
		else if ( !empty( $lang ) ) 
		{
            $language = $lang;
        } 
		// Check if we have a language set in a cookie.
		else if ( isset( $_SESSION['ap_language'] ) ) 
		{
            $language = $_SESSION['ap_language'];
        } 
		// Try browser-accepted languages, then default.
		else if ( !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) 
		{
            // The browser supplies a list, so return the first valid one.
            $browser_langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
			
            foreach ( $browser_langs as $lang ) 
			{
                $lang = Lang::_map( trim( $lang ) );
                
				if ( Lang::isValid( $lang ) ) 
				{
                    $language = $lang;
                    break;
                } 
				else if ( Lang::isValid( Lang::_map( substr( $lang, 0, 2 ) ) ) ) 
				{
                    $language = Lang::_map( substr( $lang, 0, 2 ) );
                    break;
                }
            }
        }

        // No dice auto-detecting, so give them the server default.
        if ( !isset( $language ) )
            $language = $this->_defaults_language;

        return basename( $language );
    }

    /**
     * Sets the language.
     *
     * @param string $lang          (optional) The language abbriviation
     * @access public
     */
    function setLang( $lang = null )
    {
        if ( empty( $lang ) || !Lang::isValid( $lang ) )
            $lang = Lang::select();
        
        $GLOBALS['language'] = $lang;
        putenv( 'LANG=' . $lang );
        putenv( 'LANGUAGE=' . $lang );
        setlocale( LC_ALL, $lang );
    }

    /**
     * Sets the gettext domain.
     *
     * @param string $app           The application name
     * @param string $directory     The directory where the application's
     *                              LC_MESSAGES directory resides
     * @param string $charset       The charset
     */
    function setTextdomain( $app, $directory, $charset )
    {
		// requires something like locale/de_DE/LC_MESSAGES/abstractpage.mo
		// How can this be done?
		
        bindtextdomain( $app, $directory );
        textdomain( $app );
        
		if ( function_exists( 'bind_textdomain_codeset' ) )
            bind_textdomain_codeset( $app, $charset );
        
        if ( !headers_sent() )
            header( 'Content-Type: text/html; charset=' . $charset );
    }

    /**
     * Determines whether the supplied language is valid.
     *
     * @param string $language         The abbreviated name of the language.
     *
     * @return  boolean         True if the language is valid, false if it's
     *                          not valid or unknown.
     * @access public
     */
    function isValid( $language )
    {
        return !empty( $this->_languages[$language] );
    }
	
    /**
     * Return the charset for the current language.
     *
     * @return string The character set that should be used with the
     * current locale settings.
     */
    function getCharset()
    {
        return !empty( $this->_charsets[$GLOBALS['language']] )? $this->_charsets[$GLOBALS['language']] : $this->_default_charset;
    }

	
	// private methods
	
    /**
     * Maps languages with common two-letter codes (such as nl) to the
     * full gettext code (in this case, nl_NL). Returns the language
     * unmodified if it isn't an alias.
     *
     * @param string $language   The language code to map.
     * @return string            The mapped language code.
     * @access private
     */

    function _map( $language )
    {
        $aliases = &$this->_aliases;

        // First check if the untranslated language can be found
        if ( !empty( $aliases[$language] ) )
            return $aliases[$language];

        // Translate the $language to get broader matches
        // eg. de-DE should match de_DE
        $trans_lang = str_replace( '-', '_', $language );
        $lang_parts = explode( '_', $trans_lang );
        $trans_lang = strtolower( $lang_parts[0] );
        
		if ( isset( $lang_parts[1] ) ) 
			$trans_lang .= '_' . strtoupper( $lang_parts[1] );

        // See if we get a match for this
        if ( !empty( $aliases[$trans_lang] ) )
            return $aliases[$trans_lang];

        // If we get that far down, the language cannot be found.
        // Return $trans_lang
        return $trans_lang;
    }
} // END OF Lang

?>
