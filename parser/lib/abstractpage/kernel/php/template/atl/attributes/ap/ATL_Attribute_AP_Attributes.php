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
 * ap:attributes attribute handler.
 *
 * This template attribute defines xhtml entity attributes.
 *
 * Assuming link variable :
 *
 * $link->href  = "http://www.google.fr";
 * $link->title = "google search engine";
 * $link->text  = "google";
 *
 * The template code :
 *
 * <a href="http://www.example.com"
 *    title="sample title"
 *    class="cssLink"
 *
 *    ap:attributes="href link/href; title link/title"
 *    ap:content="href link/text"
 *
 * >sample text</a>
 *
 * Will produce :
 *
 * <a class="cssLink"
 *    href="http://www.google.com"
 *    title="google search engine"
 * >google</a>
 *
 * As shown above, non overwritten attributes are keep in result xhtml.
 *
 * @package template_atl_attributes_ap
 */
 
class ATL_Attribute_AP_Attributes extends ATL_Attribute
{
	/**
	 * @access private
	 */
    var $_overwritten = array();
    

	/**
	 * @access public
	 */
    function activate( &$g, &$tag )
    {
        global $_atl_xhtml_boolean_attributes;

        $g->doPrintString( '<', $tag->name() );
        
        $atts = $tag->attributes();
        $exp = new ATL_Expression( $g, $tag, $this->expression );
        $exp->setPolicy( ATL_EXPRESSION_RECEIVER_IS_TEMP );
        $err = $exp->prepare();
        
		if ( PEAR::isError( $err ) ) 
			return $err;
        
        if ( $exp->countSubs() > 0 ) 
		{
            foreach ( $exp->subs() as $sub ) 
			{
                $err = $this->_attributeExpression( $g, $tag, $sub );
				
                if ( PEAR::isError( $err ) )
					return $err;
            }
        } 
		else 
		{
            $err = $this->_attributeExpression( $g, $tag, $exp );
			
            if ( PEAR::isError( $err ) ) 
				return $err;
        }
        
        // echo non overwritten xhtml attributes
        foreach ( $atts as $key => $value ) 
		{
            $test_key = strtolower( $key );
            
			if ( !in_array( $test_key, $this->_overwritten ) ) 
			{
                // boolean attributes
                if ( $tag->_parser->_outputMode() == ATL_TEMPLATE_XHTML && in_array( $test_key, $_atl_xhtml_boolean_attributes ) ) 
				{
                    $g->doPrintString( ' ' . $key );
                } 
				else 
				{
                    $g->doPrintString( ' '.$key . '="' );
                    $g->doPrintString( $value );
                    $g->doPrintString( '"' );
                }
            }
        }

        if ( $tag->_isXHTMLEmptyElement() ) 
		{
            $g->doPrintString( ' />' );
            return;
        }
        
        // continue tag show
        if ( $tag->hasContent() )
            $g->doPrintString( '>' );
        else
            $g->doPrintString( '/>' );

        $err = $tag->generateContent( $g );
		
        if ( PEAR::isError( $err ) )
			return $err;
       
        if ( $tag->hasContent() )
            $g->doPrintString( '</', $tag->name(), '>' );
    }

	
	// private methods

	/**
	 * @access private
	 */	
    function _printAttribute( &$g, &$tag, &$realName, &$varName, &$value, $default = false )
    {
        global $_atl_xhtml_boolean_attributes;
        
		// watch boolean attributes on XHTML mode
        if ( $tag->_parser->_outputMode() == ATL_TEMPLATE_XHTML && in_array( $realName, $_atl_xhtml_boolean_attributes ) ) 
		{
            $g->doIf( '!PEAR::isError($' . $varName . ') && $' . $varName );
            $g->doPrintString( ' ' . $realName );
            $g->endBlock();
        } 
		else if ( $default ) 
		{
            $g->doIf( '!PEAR::isError($' . $varName . ') && false !== $' . $varName . ' && null !== $' . $varName );
            $g->doPrintString( ' ' . $realName . '="' );
            $g->doPrintVar( $varName );
            $g->doPrintString( '"' );
            $g->endBlock();
        } 
		else 
		{
            $g->doPrintString( ' ' . $realName . '="' );
            $g->doPrintVar( $varName );
            $g->doPrintString( '"' );
        }
    }

	/**
	 * @access private
	 */
    function _attributeExpression( &$g, &$tag, &$sub ) 
    {
        $atts    = $tag->attributes();
        $default = false;
        $name    = strtolower( $sub->getReceiver() );

        // default in ap:attributes use default tag attributes
        if ( preg_match( '/\|\s*?\bdefault\b/sm', $sub->_src ) ) 
		{
            // ensure the attribute as a default value set in source
            // template
            $attrs = $tag->attributes();
            
			if ( !array_key_exists( $name, $atts ) )
			{
                $g->doAffectResult( '$__default__', 'false' );
            } 
			else 
			{
                // store the default value in __default__ variable
                $g->doAffectResult( '$__default__', '\''. str_replace('\'', '\\\'', $atts[$name]) . '\'' );
            }
			
            $default = true;
        }
                
        $real = str_replace( '__atl_es_dd__', ':', $name );
        $this->_overwritten[] = $real;
        $err = $sub->generate();
        
		if ( PEAR::isError( $err ) )
			return $err;
        
        $this->_printAttribute( $g, $tag, $real, $name, $value, $default );
    }
} // END OF ATL_Attribute_AP_Attributes

?>
