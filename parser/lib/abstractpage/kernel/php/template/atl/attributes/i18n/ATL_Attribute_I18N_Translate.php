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


using( 'template.atl.ATL_Attribute' );


/**
 * @package template_atl_attributes_i18n
 */
 
class ATL_Attribute_I18N_Translate extends ATL_Attribute
{
	/**
	 * @access public
	 */
    function activate( &$g, &$tag )
    {
        $g->requireGettext();

        if ( strlen( $this->expression ) == 0 )
            $key = $this->_preparseGetTextKey( $tag );
        else
            $key = $this->expression;
        
        // children may contains i18n:name attributes,
        // we ignore output but parse content before calling translation.
        $g->doOBStart();
        
		foreach ( $tag->_children as $child )
            $child->generateCode( $g );
        
        $g->doOBClean();
        $code = sprintf( '$__tpl__->_translate(\'%s\')', $key );
        $g->doPrintRes( $code, true );
    }

	
	// private methods
	
	/**
	 * @access private
	 */
    function _preparseGetTextKey( &$tag )
    {
        $key = "";
        foreach ( $tag->_children as $child ) 
		{
            if ( $child->isData() ) 
			{
                $str  = preg_replace( '/\s+/sm', ' ', $child->_content );
                $key .= trim( $str ) . ' ';
            } 
			else 
			{
                $is_i18n_name = false;
                
				// look for i18n:name
                foreach ( $child->_surround_attributes as $att ) 
				{
                    if ( get_class( $att ) == strtolower( "ATL_Attribute_I18N_Name" ) ) 
					{
                        $key .= '${' . $att->expression . '}';
                        $is_i18n_name = true;
                    }
                }
				
                // recursive call to preparse key for non i18n:name tags
                if ( !$is_i18n_name )
                    $key .= ATL_Attribute_I18N_Translate::_preparseGetTextKey( $child ) . ' ';
            }
        }

        // remove every thing that has more than 1 space
        $key = preg_replace( '/\s+/sm', ' ', $key );
        $key = trim( $key );
		
        return $key;
    }
} // END OF ATL_Attribute_I18N_Translate

?>
