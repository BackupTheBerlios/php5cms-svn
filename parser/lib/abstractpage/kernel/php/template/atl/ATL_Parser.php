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


using( 'template.atl.ATL_TEMPLATE_XML_Parser' );
using( 'template.atl.ATL_Generator' );
using( 'template.atl.ATL_Tag' );

using( 'template.atl.util.ATL_String' );

using( 'template.atl.attributes.ap.ATL_Attribute_AP_Attributes' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Comment' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Condition' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Content' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Define' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Omit_tag' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_On_Error' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Repeat' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Replace' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Include' );
using( 'template.atl.attributes.ap.ATL_Attribute_AP_Src_include' );

using( 'template.atl.attributes.i18n.ATL_Attribute_I18N_Attributes' );
using( 'template.atl.attributes.i18n.ATL_Attribute_I18N_Name' );
using( 'template.atl.attributes.i18n.ATL_Attribute_I18N_Translate' );

using( 'template.atl.attributes.metal.ATL_Attribute_METAL_Define_slot' );
using( 'template.atl.attributes.metal.ATL_Attribute_METAL_Fill_slot' );
using( 'template.atl.attributes.metal.ATL_Attribute_METAL_Use_Macro' );
using( 'template.atl.attributes.metal.ATL_Attribute_METAL_Define_Macro' );


/**
 * ATL template parser.
 * 
 * This object implements ATL_TEMPLATE_XML_Parser interface and will accept only 
 * well formed xml templates.
 * 
 * Parser object has two aims :
 *
 * - generate the template structure tree
 * - generate the php source code this structure represents
 *
 * Once this job is accomplished, the parser object should be destroyed and
 * MUST NOT be used to parse another template. It's a one time and drop 
 * object.
 * 
 * Note about code generation:
 *
 * The final source code is ready to write into a php file.
 * 
 * The code generation process requires a function name which should represent
 * the template unique id (Template class makes an md5 over the source file
 * path to create this id).
 *
 * @package template_atl
 */
 
class ATL_Parser extends ATL_TEMPLATE_XML_Parser
{
    /**
	 * root tag
	 * @access private
	 */
    var $_root;

    /**
	 * current tag
	 * @access private
	 */
    var $_current;

    /**
	 * source file name
	 * @access private
	 */
    var $_file;

    /**
	 * activate xhtml empty tags lookup
	 * @access private
	 */
    var $_outputFormat = ATL_TEMPLATE_XHTML;

    /**
	 * keep xmlns:* attributes?
	 * @access private
	 */
    var $_keepXMLNS = false;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ATL_Parser()
	{
		$this->ATL_TEMPLATE_XML_Parser();
	}
	
	
    /**
     * Parse a template string.
     * 
     * @param string $file
     *        The template source file.
     * @param string $src
     *        The template source.
     *
     * @throws Error
	 * @access public
     */
    function parse( $file, $src )
    {
        $this->_file = $file;
        $this->_initRoot();
		
        return $this->parseString( $src, true );
    }

    /**
     * Generate php code.
     * 
     * After template parsing, this method must be called to generate php code
     * from the template tree.
     * 
     * @param  string func_name 
     *         The template function name
     *         
     * @access public
     * @return string
     */
    function generateCode( $func_name )
    {
        $gen = new ATL_Generator( $this->_file, $func_name );
        $err = $this->_root->generateCode( $gen );
		
        if ( PEAR::isError( $err ) )
            return $err;
        
        return $gen->getCode();
    }

    /**
     * Return a string representation of the underlying xhtml tree.
     *
     * This method is for debug purpose.
     *
     * @access public
     * @return string
     */
    function toString()
    {
        $buf = new ATL_String();
        $buf->appendln( 'Template tree [\'', $this->_file, '\']' );
        $buf->append( $this->_root->toString() );
		
        return $buf->toString();
    }


    // private methods

    /**
     * Initialize root node.
     *
     * @access private
     */
    function _initRoot()
    {
        $this->_root    =  new ATL_Tag( $this, "#root", array() );
        $this->_current =& $this->_root;
    }

    /**
     * Push a node as the current one.
     *
     * @param  ATL_Node tag
     * @access private
     */
    function _push( &$tag )
    {
        $this->_current->addChild( $tag );
        unset( $this->_current );
        $this->_current =& $tag;
    }

    /**
     * Push a node into the current one.
     *
     * @param  ATL_Node tag
     * @access private
     */
    function _pushChild( &$tag )
    {
        $this->_current->addChild( $tag );
    }
    
    /**
     * Pop the last node (go up a level in tree).
     *
     * @access private
     */
    function _pop()
    {
        $temp =& $this->_current;
        unset( $this->_current );
		
        if ( $temp != $this->_root )
            $this->_current =& $temp->getParent();
        else
            $this->_current =& $this->_root;
    }

    /*
     * getter/setter for the output mode.
     *
     * @param int $mode optional
     *        ATL_TEMPLATE_XML or ATL_TEMPLATE_XHTML
     */
    function _outputMode( $mode = false )
    {
        if ( $mode !== false )
            $this->_outputFormat = $mode;
        
        return $this->_outputFormat;
    }


    // XML callbacks methods

    /**
     * xml callback
     * 
     * @access private
     */
    function onElementStart( $name, $attributes )
    {
        // separate atl attributes from xhtml ones
        // if an attribute is not found, an error is raised.
        $split = ATL_Parser::templateAttributes( $attributes );

        if ( PEAR::isError( $split ) )
            return $split;
        
        // no error, the returned value is a tuple 
        list( $atl, $attributes ) = $split;
        
        // sort atl attributes
        $atl = ATL_Parser::orderTemplateAttributes( $atl );
		
        if ( PEAR::isError( $atl ) )
            return $atl;
        
        // create the tag and add its template attributes
        $tag = new ATL_Tag( $this, $name, $attributes );
		
        foreach ( $atl as $t ) 
		{ 
            $tag->appendTemplateAttribute( $t ); 
            unset( $t ); // $t is appended by reference 
        }
        
        $tag->line = $this->getLineNumber();
        $this->_push( $tag );
    }

    /**
     * xml callback
     * 
     * @access private
     */
    function onElementData( $data )
    {
        // ${xxxx} variables are evaluated during code
        // generation whithin the CodeGenerator under the 
        // printString() method.
        $tag = new ATL_Tag( $this, "#cdata", array() );
        $tag->setContent( $data );
        $this->_pushChild( $tag );
    }

    /**
     * xml callback
     * 
     * @access private
     */
    function onSpecific( $data )
    {
        $tag = new ATL_Tag( $this, "#cdata", array() );
        $tag->setContent( $data );
        $this->_pushChild( $tag );
    }

    /**
     * xml callback
     * 
     * @access private
     */
    function onElementClose( $name )
    {
        if ( $this->_current == null )
            return $this->_raiseNoTagExpected( $name );
        
        if ( $this->_current->name() != $name )
            return $this->_raiseUnexpectedTagClosure( $name );
        
        $this->_pop();
    }


    // static methods

    /**
     * Lookup template attributes in given hashtable.
     *
     * This method separate xml attributes from template attributes
     * and return an array composed of the array of formers and the array of
     * laters.
     * 
     * @access private 
     * @static
     * 
     * @param   hashtable attrs 
     *          Attributes hash
     *          
     * @return  array
     */
    function templateAttributes( $attrs )
    {
        global $_atl_dictionary;
		global $_atl_aliases;
		global $_atl_namespaces;
        
		$atl = array();
        $att    = array();
        
        foreach ( $attrs as $key => $exp ) 
		{    
            $test_key = strtoupper( $key );
            $ns  = preg_replace( '/(:.*?)$/', '', $test_key );
            $sns = preg_replace( '/^(.*?:)/', '', $test_key );

            // dictionary lookup
            if ( array_key_exists( $test_key, $_atl_dictionary ) ) 
                $atl[$test_key] = $exp;
            // alias lookup
            else if ( array_key_exists( $test_key, $_atl_aliases ) ) 
                $atl[ $_atl_aliases[$test_key] ] = $exp;
            // the namespace is known but the the attribute is not
            else if ( in_array( $ns, $_atl_namespaces ) ) 
                return $this->_raiseUnknownAttribute( $test_key );
            // regular xml/xhtml attribute (skip namespaces declaration)
            else if ( $ns !== 'XMLNS' || $this->_keepXMLNS || !in_array( $sns, $_atl_namespaces ) ) 
                $att[$key] = $exp;
        }
		
        return array( $atl, $att );
    }

    /**
     * Order atl attributes array using $_atl_rules_order array.
     * 
     * @static  1
     * @access  private
     * 
     * @param   array atl 
     *          Array of atl attributes (will be modified)
     */
    function orderTemplateAttributes( &$atl )
    {
        global $_atl_rules_order;
		global $_atl_dictionary;
        
        // order elements by their name using the rule table       
        $result = array();
        foreach ( $atl as $akey => $exp ) 
		{    
            // retrieve attribute handler class
            $class = "ATL_ATTRIBUTE_" . str_replace( ":", "_", $akey );
            $class = str_replace( "-", "_", $class );
            
			if ( !class_exists( $class ) )
                return $this->_raiseAttributeNotFound( $akey, $class );

            $hdl = new $class( $exp );
            $hdl->name = $akey;
            $hdl->_atl_type = $_atl_dictionary[$akey];

            // resolve attributes conflict
            $pos = $_atl_rules_order[$akey];
            
			if ( array_key_exists( $pos, $result ) )
                return $this->_raiseAttConflict( $akey, $result[$pos]->name );
            
            // order elements by their order rule
            $result[$_atl_rules_order[$akey]] = $hdl;
            unset( $hdl );
        }
		
        return $result;
    }


    // errors raising methods

    function _raiseAttributeNotFound( $att, $class )
    {
        $str = sprintf( "Attribute '%s' exists in dictionary but class '%s' " .
			"was not found",
			$att, 
			$class
		);
		
        return PEAR::raiseError( $str );
    }
    
    function _raiseUnknownAttribute( $att )
    {
        $str = sprintf( "Unknown ATL attribute '%s' in %s at line %d",
			$att, 
			$this->_file, 
			$this->getLineNumber()
		);
		
        return PEAR::raiseError( $str );
    }

    function _raiseUnexpectedTagClosure( $name )
    {
        $str = sprintf( "Non matching tag '%s' error in xml file '%s' at line %d" 
			. ATL_STRING_LINEFEED . "waiting for end of tag '%s' declared at line %d.",
			$name, 
			$this->_file, 
			$this->getLineNumber(), 
			$this->_current->name(), 
			$this->_current->line
		);
		
        return PEAR::raiseError( $str );
    }

    function _raiseNoTagExpected( $name )
    {
        $str = sprintf( "Bad xml error in xml file '%s' at line %d" . ATL_STRING_LINEFEED
			. "Found closing tag '%s' while no current tag is waited.",
			$this->_file, 
			$this->getLineNumber(), 
			$name
		);
		
        return PEAR::raiseError( $str );
    }

    function _raiseAttConflict( $a2, $a1 )
    {
        $str = sprintf( 'Template Attribute conflict in \'%s\' at line %d' . ATL_STRING_LINEFEED
			. ' %s must not be used in the same tag as %s', 
			$this->_file, 
			$this->getLineNumber(), 
			$a1,
			$a2
		);
		
        return PEAR::raiseError( $str );
    }
} // END OF ATL_Parser

?>
