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
using( 'template.atl.ATL_Expression' );


/**
 * @package template_atl_attributes_ap
 */
 
class ATL_Attribute_AP_Define extends ATL_Attribute
{
	/**
	 * @access public
	 */
    function activate( &$g, &$tag )
    {
        if ( preg_match( '/\|\s*?\bdefault\b/sm', $this->expression ) ) 
		{
            $g->doOBStart();
            $err = $tag->generateContent( $g );
            
			if ( PEAR::isError( $err ) )
				return $err;
				
            $g->doOBEnd( '$__default__' );
            $default = true;
        }
        
        // use node content to set variable
        if ( preg_match( '/^[a-z0-9_]+$/i', $this->expression ) ) 
		{
            $g->doOBStart();
            $err = $tag->generateContent( $g );
			
            if ( PEAR::isError( $err ) ) 
				return $err;
				
            $g->doOBEndInContext( $this->expression );
        } 
		else 
		{
            $exp = new ATL_Expression( $g, $tag, $this->expression );
            $exp->setPolicy( ATL_EXPRESSION_RECEIVER_IS_CONTEXT );
            $err = $exp->generate();
			
            if ( PEAR::isError( $err ) ) 
				return $err;
        }
		
        if ( isset( $default ) ) 
			$g->execute( 'unset($__default__)' );
    }
} // END OF ATL_Attribute_AP_Define

?>
