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
|Authors: Laurent Bedubourg <laurent.bedubourg@free.fr>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.text.gettext.lib.GetText_NativeSupport' );
using( 'util.text.gettext.lib.GetText_PHPSupport' );


define( 'GETTEXT_NATIVE', 1 );
define( 'GETTEXT_PHP',    2 );


/**
 * Generic gettext static class.
 *
 * This class allows gettext usage with php even if the gettext support is 
 * not compiled in php.
 *
 * The developper can choose between the GETTEXT_NATIVE support and the
 * GETTEXT_PHP support on initialisation. If native is not supported, the
 * system will fall back to PHP support.
 *
 * On both systems, this package add a variable interpolation system so you can
 * translate entire dynamic sentences in stead of peace of sentences.
 *
 * Example:
 * 
 * <?php
 * GetText::init();
 * GetText::setLanguage( 'fr_Fr' );      // may throw Error
 * GetText::addDomain( 'myAppDomain' );  // may throw Error
 * GetText::setVar( 'login', $login );   
 * GetText::setVar( 'name', $name );
 * 
 * // may throw Error
 * echo GetText::gettext('Welcome ${name}, you\'re connected with login ${login}');
 * 
 * // should echo something like :
 * //
 * // "Bienvenue Jean-Claude, vous êtes connecté en tant qu'utilisateur jcaccount"
 * // 
 * // or if fr_FR translation does not exists
 * //
 * // "Welcome Jean-Claude, you're connected with login jcaccount"
 * 
 * ?>
 *
 * A gettext mini-howto should be provided with this package, if you're new 
 * to gettext usage, please read it to learn how to build a gettext 
 * translation directory (locale).
 * 
 * @todo    Tools to manage gettext files in php.
 * 
 *          - non traducted domains / keys
 *          - modification of keys
 *          - domain creation, preparation, delete, ...
 *          - tool to extract required messages from TOF templates
 *
 * @package util_text_gettext_lib
 */

class GetText extends PEAR
{
    /**
     * Initialize gettext package.
     *
     * This method instantiate the gettext support depending on managerType
     * value. 
     *
     * GETTEXT_NATIVE try to use gettext php support and fail back to PHP
     * support if not installed.
     *
     * GETTEXT_PHP explicitely request the usage of PHP support.
     *
     * @param  int $managerType
     *         Gettext support type.
     *         
     * @access public
     * @static
     */
    function init( $managerType = GETTEXT_NATIVE )
    {
        if ( $managerType == GETTEXT_NATIVE ) 
		{
            if ( function_exists( 'gettext' ) )
                return GetText::_support( new GetText_NativeSupport );
        }
		
        // fail back to php support 
        return GetText::_support( new GetText_PHPSupport );
    }
    
    /**
     * Set the language to use for traduction.
     *
     * @param string $langCode
     *        The language code usually defined as ll_CC, ll is the two letter
     *        language code and CC is the two letter country code.
     *
     * @throws Error if language is not supported by your system.
     */
    function setLanguage( $langCode )
    {
        $support =& GetText::_support();
        return $support->setLanguage( $langCode );
    }
    
    /**
     * Add a translation domain.
     *
     * The domain name is usually the name of the .po file you wish to use. 
     * For example, if you created a file 'locale/ll_CC/LC_MESSAGES/myapp.po',
     * you'll use 'myapp' as the domain name.
     *
     * @param string $domain
     *        The domain name.
     *
     * @param string $path optional
     *        The path to the locale directory (ie: /path/to/locale/) which
     *        contains ll_CC directories.
     */
    function addDomain( $domain, $path = false )
    {
        $support =& GetText::_support();
        return $support->addDomain( $domain, $path );
    }
    
    /**
     * Retrieve the translation for specified key.
     *
     * @param string $key
     *        String to translate using gettext support.
     */
    function gettext( $key )
    { 
        $support =& GetText::_support();
        return $support->gettext( $key );
    }
	
	function _($key) {
        $support =& GetText::_support();
        return $support->gettext( $key );
	}
   
    /**
     * Add a variable to gettext interpolation system.
     *
     * @param string $key
     *        The variable name.
     *
     * @param string $value
     *        The variable value.
     */
    function setVar( $key, $value )
    {
        $support =& GetText::_support();
        return $support->setVar( $key, $value );
    }

    /**
     * Add an hashtable of variables.
     *
     * @param hashtable $hash 
     *        PHP associative array of variables.
     */
    function setVars( $hash )
    {
        $support =& GetText::_support();
        return $support->setVars( $hash );
    }

    /**
     * Reset interpolation variables.
     */
    function reset()
    {
        $support =& GetText::_support();
        return $support->reset();
    }
	
	
	// private methods
	
    /**
     * This method returns current gettext support class.
     *
     * @return GetText_Support
     * @static
     * @access private
     */
    function &_support( $set = false )
    { 
        static $supportObject;
        
		if ( $set !== false ) 
		{ 
            $supportObject = $set; 
        } 
		else if ( !isset( $supportObject ) ) 
		{
            trigger_error( "GetText not initialized !" . ENDLINE .
                           "Please call GetText::init() before calling " .
                           "any GetText function !" . ENDLINE, E_USER_ERROR );
        }
		
        return $supportObject;
    }
} // END OF GetText

?>
