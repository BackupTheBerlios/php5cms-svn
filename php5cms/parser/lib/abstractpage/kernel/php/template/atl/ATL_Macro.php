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


using( 'template.atl.ATL_Template' );


/**
 * Macro invoker.
 *
 * @package template_atl
 */
 
class ATL_Macro extends ATL_Template
{
	/**
	 * @access private
	 */	
	var $_name;
	
	/**
	 * @access private
	 */	
    var $_path = false;
    
	
    /**
     * Constructor
     *
     * @param ATL_Template $caller 
     *        The macro caller may be a template or a macro.
     * @param string $path 
     *        The macro path/name
	 *
	 * @access public
     */
    function ATL_Macro( &$caller, $path )
    {
        $this->_name = $path;
        
        // extract macro path part, if none found, we'll assume macro is 
        // in the caller template.
        if ( preg_match( '/(.*?)\/([a-zA-Z0-9_]*?)$/', $this->_name, $match ) )
            list(, $this->_path, $this->_name) = $match;
        else
            $this->_sourceFile = $caller->_sourceFile;

        // call parent constructor
        $this->ATL_Template( $this->_path, $caller->_repository, $caller->_cacheDir );
        
        $this->setParent( $caller );
        $this->setEncoding( $caller->getEncoding() );
    }
    
	
    /**
     * Execute macro with caller context.
     *
     * @return string
	 * @access public
     */
    function execute()
    {
        if ( $this->_path !== false ) 
		{
            $err = $this->_prepare();
            
			if ( PEAR::isError( $err ) )
                return $err;
        }
		
        return $this->_cacheManager->macro( $this, $this->_sourceFile, $this->_name, $this->_parent->getContext() );
    }

	
	// private methods
	
    /**
     * Really process macro parsing/invocation.
     *
     * @return string
	 * @access private
     */
    function _process()
    {
        if ( $this->_path !== false ) 
		{
            $err = $this->_load();
            
			if ( PEAR::isError( $err ) )
                return $err;
        } 
		else 
		{
            $this->_funcName = $this->_parent->_funcName;
        }            
        
        $func = $this->_funcName . '_' . $this->_name;
        
        if ( !function_exists( $func ) ) 
		{
            $err = "Unknown macro '$this->_name'";
            return PEAR::raiseError( $err );
        }
        
        return $func( $this->_parent );
    }
} // END OF ATL_Macro

?>
