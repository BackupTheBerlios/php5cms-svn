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
using( 'template.atl.ATL_LoopControler' );


/**
 * @package template_atl_attributes_ap
 */
 
class ATL_Attribute_AP_Repeat extends ATL_Attribute
{
	/**
	 * @access public
	 */
    var $in  = "__in__";
	
	/**
	 * @access public
	 */
    var $out = "__out__";
    
	
	/**
	 * @access public
	 */
    function start( &$g, &$tag )
    {
        $g->setSource( $tag->name(), $tag->line );
        
        $g->doComment( 'new loop' );
        $exp = new ATL_Expression( $g, $tag, $this->expression );
        $exp->setPolicy( ATL_EXPRESSION_RECEIVER_IS_TEMP );
        $err = $exp->prepare();
        
		if ( PEAR::isError( $err ) ) 
			return $err;
        
        $this->out = $exp->getReceiver();
        $temp = $g->newTemporaryVar();
        $exp->setReceiver( $temp );
        $err = $exp->generate(); // now $temp points to the loop data
        
		if ( PEAR::isError( $err ) ) 
			return $err; 

        $loop = $g->newTemporaryVar();
        $this->loop = $loop;
        $g->doAffectResult( $loop, '& new ATL_LoopControler($__ctx__, "' . $this->out . '", ' . $temp . ');' );
        $g->doIf( 'PEAR::isError(' . $loop . '->_error)' );
        $g->doPrintVar( $loop . '->_error' );
        $g->doElse();
        $g->doWhile( $loop . '->isValid()' );   
    }

	/**
	 * @access public
	 */
    function end( &$g, &$tag )
    {
        $g->execute( $this->loop . '->next()' );
        $g->endBlock();
        $g->endBlock();
        $g->doComment( 'end loop' );
    }
} // END OF ATL_Attribute_AP_Repeat

?>
