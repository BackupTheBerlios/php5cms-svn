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
using( 'template.atl.ATL_Template' );


/**
 * @package template_atl_attributes_ap
 */
 
class ATL_Attribute_AP_Include extends ATL_Attribute
{
	/**
	 * @access public
	 */
    function activate( &$g, &$tag )
    {
        $exp = new ATL_Expression( $g, $tag, $this->expression );
        $temp = $g->newTemporaryVar();
        $exp->setPolicy( ATL_EXPRESSION_RECEIVER_IS_TEMP );
        $exp->setReceiver( $temp );
        $err = $exp->generate();
		
        if ( PEAR::isError( $err ) )
			return $err;
        
        $tpl = $g->newTemporaryVar();
        $g->doAffectResult( $tpl, 'new ATL_Template(' . $temp . ', $__tpl__->_repository, $__tpl__->_cacheDir)' );
        $g->execute( $tpl . '->setParent($__tpl__)'  );
        $g->execute( $tpl . '->setContext($__ctx__)' );
        $g->doPrintRes( $tpl . '->execute()', true   );
    }
} // END OF ATL_Attribute_AP_Include

?>
