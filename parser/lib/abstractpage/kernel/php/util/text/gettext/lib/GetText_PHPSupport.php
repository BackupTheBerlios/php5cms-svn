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
using( 'util.text.gettext.lib.GetText_Domain' );
using( 'util.text.gettext.lib.GetText_PHPSupport_Parser' );
using( 'util.text.gettext.lib.GetText_PHPSupport_Compiler' );


/**
 * Implementation of GetText support for PHP.
 *
 * This implementation is abble to cache .po files into php files returning the
 * domain translation hashtable.
 *
 * @package util_text_gettext_lib
 */
 
class GetText_PHPSupport extends GetText_NativeSupport
{
	/**
	 * @access private
	 */
    var $_path = 'locale/';
	
	/**
	 * @access private
	 */
    var $_langCode = false;
	
	/**
	 * @access private
	 */
    var $_domains = array();
	
	/**
	 * @access private
	 */
    var $_end = -1;
	
	/**
	 * @access private
	 */
    var $_jobs = array();
	
	
    /**
     * Set the translation domain.
     *
     * @param  string $langCode -- language code
     * @throws Error
     */
    function setLanguage( $langCode )
    {
        // if language already set, try to reload domains
        if ( $this->_langCode !== false && $this->_langCode != $langCode ) 
		{
            foreach ( $this->_domains as $domain )
                $this->_jobs[] = array( $domain->name, $domain->path );
            
            $this->_domains = array();
            $this->_end = -1;
        }
        
        $this->_langCode = $langCode;

        // this allow us to set the language code after 
        // domain list.
        while ( count( $this->_jobs ) > 0 ) 
		{
            list( $domain, $path ) = array_shift( $this->_jobs );
            $err = $this->addDomain( $domain, $path );

            // error raised, break jobs
            if ( PEAR::isError( $err ) )
                return $err;
        }
    }
    
    /**
     * Add a translation domain.
     *
     * @param string $domain        -- Domain name
     * @param string $path optional -- Repository path
     * @throws Error
     */
    function addDomain( $domain, $path = "./locale/" )
    {   
        if ( array_key_exists( $domain, $this->_domains ) ) 
            return; 
        
        if ( !$this->_langCode ) 
		{ 
            $this->_jobs[] = array( $domain, $path ); 
            return;
        }

        $err = $this->_loadDomain( $domain, $path );
        
		if ( PEAR::isError( $err ) )
            return $err;

        $this->_end++;
    }

	
	// private methods
	
    /**
     * Load a translation domain file.
     *
     * This method cache the translation hash into a php file unless
     * GETTEXT_NO_CACHE is defined.
     * 
     * @param  string $domain        -- Domain name
     * @param  string $path optional -- Repository
     * @throws Error
     * @access private
     */
    function _loadDomain( $domain, $path = "./locale" )
    {
        $srcDomain = $path . "/$this->_langCode/LC_MESSAGES/$domain.po";
        $phpDomain = $path . "/$this->_langCode/LC_MESSAGES/$domain.php";
        
        if ( !file_exists( $srcDomain ) ) 
			return PEAR::raiseError( sprintf( 'Domain file "%s" not found.', $srcDomain ) );
        
        $d = new GetText_Domain;
        $d->name = $domain;
        $d->path = $path;
        
        if ( !file_exists( $phpDomain ) || ( filemtime( $phpDomain ) < filemtime( $srcDomain) ) ) 
		{    
            // parse and compile translation table
            $parser = new GetText_PHPSupport_Parser;
            $hash   = $parser->parse( $srcDomain );
			
            if ( !defined( 'GETTEXT_NO_CACHE' ) ) 
			{
                $comp = new GetText_PHPSupport_Compiler;
                $err  = $comp->compile( $hash, $srcDomain );
				
                if ( PEAR::isError( $err ) ) 
                    return $err; 
            }
			
            $d->_keys = $hash;
        } 
		else 
		{
            $d->_keys = include $phpDomain;
        }
		
        $this->_domains[] =& $d;
    }
    
    /**
     * Implementation of gettext message retrieval.
     */
    function _getTranslation( $key )
    {
        for ( $i = $this->_end; $i >= 0; $i-- ) 
		{
            if ( $this->_domains[$i]->hasKey( $key ) )
                return $this->_domains[$i]->get( $key );
        }
		
        return $key;
    }
} // END OF GetText_PHPSupport

?>
