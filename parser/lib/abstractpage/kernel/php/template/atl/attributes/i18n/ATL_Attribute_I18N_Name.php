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
 
class ATL_Attribute_I18N_Name extends ATL_Attribute
{	
	/**
	 * @access public
	 */
    function start( &$g, &$tag )
    {
        $g->requireGettext();
        $g->doOBStart();
    }

	/**
	 * @access public
	 */    
    function end( &$g, &$tag )
    {
        $temp = $g->newTemporaryVar();
        $g->doOBEnd( $temp );
        $code = sprintf( '$__tpl__->_setTranslateVar(\'%s\', %s)', $this->expression, $temp );
        $g->execute( $code );
    }
} // END OF ATL_Attribute_I18N_Name

?>
