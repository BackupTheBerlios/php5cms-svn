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
 
class ATL_Attribute_AP_Src_include extends ATL_Attribute
{
	/**
	 * @access public
	 */
    function activate( &$g, &$tag )
    {
        $temp = $g->newTemporaryVar();
        
        $exp = new ATL_Expression( $g, $tag, $this->expression );
        $exp->setPolicy( ATL_EXPRESSION_RECEIVER_IS_TEMP );
        $exp->setReceiver( $temp );
        $err = $exp->generate();
        
		if ( PEAR::isError( $err ) )
			return $err;

        $g->doAffectResult( $temp, '$__tpl__->realpath(' . $temp . ')' );
        $g->doIf( 'PEAR::isError(' . $temp . ')' );
        $g->execute( '$__ctx__->_errorRaised = true' );
        $g->doPrintVar( $temp );
        $g->doElse();
        $g->doPrintRes( 'join("", file('.$temp.'))', true );
        $g->endBlock();
    }
} // END OF ATL_Attribute_AP_Src_include

?>
