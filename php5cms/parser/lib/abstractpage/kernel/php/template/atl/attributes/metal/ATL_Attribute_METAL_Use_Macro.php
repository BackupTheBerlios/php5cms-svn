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
using( 'template.atl.ATL_ES_PHP_Parser' );
using( 'template.atl.ATL_Macro' );


/**
 * @package template_atl_attributes_metal
 */
 
class ATL_Attribute_METAL_Use_Macro extends ATL_Attribute
{
	/**
	 * @access public
	 */
    function activate( &$g, &$tag )
    {
        $g->doOBStart();
        $err = $tag->generateContent( $g );
		
        if ( PEAR::isError( $err ) )
			return $err;
			
        $g->doOBClean();
        $path = $g->newTemporaryVar();
        $g->doAffectResult( $path, "'". ATL_ES_path_in_string( $this->expression, "'" ) . "'" );
        $temp = $g->newTemporaryVar();
        
        // push error
        $g->execute( '$__old_error = $__ctx__->_errorRaised' );
        $g->execute( '$__ctx__->_errorRaised = false' );
        
        $g->doAffectResult( $temp, 'new ATL_Macro($__tpl__, '. $path .')' );
        $g->doAffectResult( $temp, $temp . '->execute($__tpl__)' );
        
        $g->doIf( 'PEAR::isError(' . $temp . ')' );
        $g->execute( '$__ctx__->_errorRaised = ' . $temp );
        $g->endBlock();
        
        $g->doPrintVar( $temp, true );

        // restore error
        $g->doIf( '!$__ctx__->_errorRaised' );
        $g->execute( '$__ctx__->_errorRaised = $__old_error' );
        $g->endBlock();
    }
} // END OF ATL_Attribute_METAL_Use_Macro

?>
