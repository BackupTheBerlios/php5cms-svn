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


using( 'template.atl.util.ATL_String' );


/**
 * Represents an xhtml entity.
 *
 * To obtain a fine granularity of code generation the Tag code generation 
 * process is divided into logical sub sequences.
 * 
 * surround head                // call surround attributes
 *    replace                   // call replace and skip head -> foo sequence
 *    || head                   // echo tag start (see disableHeadFoot())
 *            content           // call content replace and skip cdata seq
 *            || cdata && child // echo tag content and generate children code
 *       foot                   // echo tag end   (see disableHeadFoot())
 * surround foot                // call surround attributes
 *  
 *
 * Some attributes like 'omit' requires to disable the head / foot sequences 
 * whithout touching content sequences, the disableHeadFoot() method must be
 * used in that aim.
 *
 * @package template_atl
 */
 
class ATL_Tag extends PEAR
{
    /**
     * @var int $line The template line which produced this tag.
	 * @access public
     */
    var $line = 0;
    
    /* tree properties */
	
	/**
	 * @access private
	 */
    var $_name = "#cdata";
	
	/**
	 * @access private
	 */
    var $_attrs = array();
	
	/**
	 * @access private
	 */
    var $_children = array();

	/**
	 * @access private
	 */
    var $_content = "";
	
	/**
	 * @access private
	 */
    var $_parent = null;


    /* template system properties */
	
	/**
	 * @access private
	 */
    var $_parser;
	
	/**
	 * @access private
	 */
    var $_replace_attributes = array();
	
	/**
	 * @access private
	 */
    var $_content_attributes = array();
	
	/**
	 * @access private
	 */
    var $_surround_attributes = array();
	
	/**
	 * @access private
	 */
    var $_head_foot_disabled = false;


    /**
     * Constructor
     *
     * @param  string $name
     *         The tag name.
     *
     * @param  hashtable $attrs
     *         Tag xhtml attributes.
	 * @access public
     */
    function ATL_Tag( &$parser, $name, $attrs )
    {
        $this->_parser =& $parser;
        $this->_name   =  $name;
        $this->_attrs  =  $attrs;
    }

	
	/**
	 * @access public
	 */
    function appendTemplateAttribute( &$hdlr )
    {
        switch ( $hdlr->_atl_type )
        {
            case ATL_TEMPLATE_REPLACE:
                $this->_replace_attributes[] =& $hdlr;
                break;
            
			case ATL_TEMPLATE_CONTENT:
                $this->_content_attributes[] =& $hdlr;
                break;
            
			case ATL_TEMPLATE_SURROUND:
                $this->_surround_attributes[] =& $hdlr;
                break;
            
			default:
                return PEAR::raiseError( "ATL internal error : bad attribute type : " . get_class( $hdlr ) );
        }
    }

	/**
	 * @access public
	 */
    function name()
    {
        return $this->_name;
    }

	/**
	 * @access public
	 */
    function isData()
    {
        return $this->_name == "#cdata";
    }

	/**
	 * @access public
	 */
    function attributes()
    {
        return $this->_attrs;
    }

	/**
	 * @access public
	 */
    function setParent( &$node )
    {
        $this->_parent =& $node;
    }

	/**
	 * @access public
	 */
    function &getParent()
    {
        return $this->_parent;
    }
    
	/**
	 * @access public
	 */
    function addChild( &$node )
    {
        $node->setParent( $this );
        $this->_children[] = &$node;
    }

	/**
	 * @access public
	 */    
    function setContent( $str )
    {
        $this->_content = $str;
    }

	/**
	 * @access public
	 */
    function hasContent()
    {
        return ( count( $this->_content_attributes ) == 0 ||  
				 count( $this->_children ) == 0 ||  
				 strlen( $this->_content ) == 0 );
    }

	/**
	 * @access public
	 */
    function toString( $tab = "" )
    {
        $buf = new ATL_String();
        $buf->appendln( $tab, '+ node ', $this->name() );
		
        for ( $i = 0; $i < count( $this->_children ); $i++ ) 
		{
            $child =& $this->_children[$i];
            $buf->append( $child->toString( $tab . "  " ) );
            unset( $child );
        }
		
        return $buf->toString();
    }    

	/**
	 * @access public
	 */
    function generateCode( &$g )
    {
        // if text node, just print the content and return
        if ( $this->_name == "#cdata" ) 
		{
            $g->doPrintString( $this->_content );
            return;
        }

        if ( $this->_name == "#root" )
            return $this->generateContent( $g );
        
        // if replace attributes exists, they will handle
        // this tag rendering
        if ( count( $this->_replace_attributes ) > 0 ) 
		{
            $err = $this->surroundHead( $g );
			
            if ( PEAR::isError( $err ) )
				return $err;
            
            for ( $i = 0; $i < count( $this->_replace_attributes ); $i++ ) 
			{
                $h   =& $this->_replace_attributes[$i];
                $err =  $h->activate( $g, $this );
                
				if ( PEAR::isError( $err ) )
					return $err;
					
                unset( $h );
            }
			
            return $this->surroundFoot( $g );
        }

        $err = $this->surroundHead( $g );
		
        if ( PEAR::isError( $err ) )
			return $err;

        $this->printHead( $g );
        
        $err = $this->generateContent( $g );
		
        if ( PEAR::isError( $err ) )
			return $err;
  
        $this->printFoot( $g );

        $err = $this->surroundFoot( $g );
		 
        if ( PEAR::isError( $err ) )
			return $err;
    }

	/**
	 * @access public
	 */
    function printHead( &$g )
    {
        if ( $this->headFootDisabled() ) 
			return;

        $g->doPrintString( '<', $this->_name );

        $this->printAttributes( $g );
        
        if ( $this->hasContent() && !$this->_isXHTMLEmptyElement() )
            $g->doPrintString( '>' );
        else
            $g->doPrintString( '/>' );
    }

	/**
	 * @access public
	 */
    function printFoot( &$g )
    { 
        if ( $this->headFootDisabled() ) 
			return;
        
        if ( $this->hasContent() && !$this->_isXHTMLEmptyElement() )
            $g->doPrintString( '</', $this->_name, '>' );
    }

	/**
	 * @access public
	 */    
    function printAttributes( &$g )
    {
        global $_atl_xhtml_boolean_attributes;
		
        foreach ( $this->_attrs as $key => $value ) 
		{
            if ( $this->_parser->_outputMode() == ATL_TEMPLATE_XHTML && in_array( strtolower( $key ), $_atl_xhtml_boolean_attributes ) )
                $g->doPrintString( " $key" );
            else
                $g->doPrintString( " $key=\"$value\"" );
        }
    }

	/**
	 * @access public
	 */
    function surroundHead( &$g )
    {
        // if some surround attributes, we activate
        // their header method
        for ( $i = 0; $i < count( $this->_surround_attributes ); $i++ ) 
		{
            $h   =& $this->_surround_attributes[$i];
            $err =  $h->start( $g, $this );
			
            if ( PEAR::isError( $err ) )
				return $err;
				
            unset( $h );
        }
    }

	/**
	 * @access public
	 */
    function surroundFoot( &$g )
    {
        // close surrounders in reverse order of course
        for ( $i = ( count( $this->_surround_attributes ) - 1 ); $i >= 0; $i-- ) 
		{
            $err = $this->_surround_attributes[$i]->end( $g, $this );
			
            if ( PEAR::isError( $err ) )
				return $err;
        }
    }

	/**
	 * @access public
	 */    
    function generateContent( &$g )
    {
        if ( count( $this->_content_attributes ) > 0 ) 
		{
            // time for content attributes, 
            foreach ( $this->_content_attributes as $h ) 
			{
                $err = $h->activate( $g, $this );
				
                if ( PEAR::isError( $err ) )
					return $err;
            }
        } 
		else 
		{
            // if none, we just ask children to generate their code
            foreach ( $this->_children as $child ) 
			{
                $err = $child->generateCode( $g );
				
                if ( PEAR::isError( $err ) )
					return $err;
            }
        }
    }

	/**
	 * @access public
	 */
    function disableHeadFoot()
    {
        $this->_head_foot_disabled = true;
    }

	/**
	 * @access public
	 */
    function headFootDisabled()
    {
        return $this->_head_foot_disabled;
    }

	
	// private methods

	/**
	 * @access private
	 */	
    function _isXHTMLEmptyElement()
    {
        global $_atl_xhtml_empty_tags;
		
        if ( $this->_parser->_outputMode() != ATL_TEMPLATE_XHTML )
            return false;
        
        return in_array( strtoupper( $this->name() ), $_atl_xhtml_empty_tags );
    }
} // END OF ATL_Tag

?>
