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


/**
 * Interface to gettext native support.
 *
 * @package util_text_gettext_lib
 */
 
class GetText_NativeSupport extends PEAR
{
	/**
	 * @access private
	 */
    var $_interpolationVars = array();
	
	
    /**
     * Set gettext language code.
	 *
     * @throws Error
	 * @access public
     */
    function setLanguage( $langCode )
    {
        putenv( "LANG=$langCode"     );
        putenv( "LC_ALL=$langCode"   );
        putenv( "LANGUAGE=$langCode" );
		
        $set = setlocale( LC_ALL, "$langCode" );
		
        if ( $set === false ) 
			return PEAR::raiseError( sprintf( 'Language code "%s" not supported by your system', $langCode ) );
    }
    
    /**
     * Add a translation domain.
	 *
	 * @access public
     */
    function addDomain( $domain, $path = false )
    {
        if ( $path === false )
            bindtextdomain( $domain, "./locale/" );
        else 
            bindtextdomain( $domain, $path );
        
        textdomain($domain);
    }

    /**
     * Reset interpolation variables.
	 *
	 * @access public
     */
    function reset()
    {
        $this->_interpolationVars = array();
    }
    
    /**
     * Set an interpolation variable.
	 *
	 * @access public
     */
    function setVar( $key, $value )
    {
        $this->_interpolationVars[$key] = $value;
    }

    /**
     * Set an associative array of interpolation variables.
	 *
	 * @access public
     */
    function setVars( $hash )
    {
        $this->_interpolationVars = array_merge( $this->_interpolationVars, $hash );
    }
    
    /**
     * Retrieve translation for specified key.
     *
     * @param  string $key  -- gettext msgid
     * @throws Error
	 * @access public
     */
    function gettext( $key )
    {
        $value = $this->_getTranslation( $key );
		
        if ( $value === false ) 
			return PEAR::raiseError( sprintf( 'Unable to locate gettext key "%s"', $key ) );
        
        while ( preg_match( '/\$\{(.*?)\}/sm', $value, $m ) ) 
		{
            list( $src, $var ) = $m;

            // retrieve variable to interpolate in context, throw an exception
            // if not found.
            $varValue = $this->_getVar( $var );
			
            if ( $varValue === false ) 
                return PEAR::raiseError( sprintf( 'Interpolation error, var "%s" not set', $var ) );
			
            $value = str_replace( $src, $varValue, $value );
        }
		
        return $value;
    }

	
	// private methods
	
    /**
     * Retrieve translation for specified key.
     *
     * @access private
     */
    function _getTranslation( $key )
    {
        return gettext( $key );
    }
	
    /**
     * Retrieve an interpolation variable value.
     * 
     * @return mixed
     * @access private
     */
    function _getVar( $name )
    {
        if ( !array_key_exists( $name, $this->_interpolationVars ) )
            return false;
        
        return $this->_interpolationVars[$name];
    }
} // END OF GetText_NativeSupport

?>
