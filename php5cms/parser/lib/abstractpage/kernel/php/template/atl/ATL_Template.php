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


using( 'template.atl.util.ATL_Array' );
using( 'template.atl.ATL_Context' );
using( 'template.atl.ATL_SourceResolver' );
using( 'template.atl.ATL_Cache' );
using( 'template.atl.ATL_Parser' );
using( 'template.atl.ATL_I18N' );
using( 'template.atl.ATL_Filter' );
using( 'template.atl.ATL_Macro' );
using( 'template.atl.ATL_LoopControler' );
using( 'template.atl.ATL_DBResultIterator' );
using( 'template.atl.ATL_OutputControl' );


define( 'ATL_TEMPLATE_VERSION', '1.0' );
define( 'ATL_TEMPLATE_MARK', str_replace( '.', '_', ATL_TEMPLATE_VERSION ) . '_' );
define( 'ATL_TEMPLATE_DEFAULT_CACHE_DIR', '/tmp/' );

/**
 * Cache file prefix.
 */
define( 'ATL_TEMPLATE_CACHE_FILE_PREFIX', 'atl_' );

/**
 * This define is used to select the templates output format.
 *
 * There's few differences between XHTML and XML but they these differences can
 * break some browsers output.
 *
 * Default ATL output mode is XHTML.
 */
define( 'ATL_TEMPLATE_XHTML', 1 );

/**
 * This define is used to select the templates output format.
 *
 * The XML mode does not worry about XHTML specificity and echo every entity
 * in a <entity></entity> format.
 */
define( 'ATL_TEMPLATE_XML', 2 );

/**
 * @var _atl_namespaces
 * @var  array
 *
 * This array contains the list of all known attribute namespaces, if an
 * attribute belonging to one of this namespaces is not recognized by ATL,
 * an exception will be raised.
 * 
 * These namespaces will be drop from resulting xml/xhtml unless the parser 
 * is told to keep them.
 *
 * @access private
 * @static
 */
global $_atl_namespaces;
$_atl_namespaces = array(
	'AP', 
	'METAL', 
	'I18N', 
);


define( 'ATL_TEMPLATE_SURROUND', 1 );
define( 'ATL_TEMPLATE_REPLACE',  2 );
define( 'ATL_TEMPLATE_CONTENT',  3 );

/**
 * @var   _atl_dictionary
 * @var   hashtable
 * 
 * This dictionary contains ALL known ATL attributes. Unknown attributes 
 * will be echoed in result as xhtml/xml ones.
 * 
 * The value define how and when the attribute handler will be called during
 * code generation.
 * 
 * @access private 
 * @static
 */ 
global $_atl_dictionary;
$_atl_dictionary = array(
    'AP:DEFINE'          => ATL_TEMPLATE_REPLACE,  // set a context variable
    'AP:CONDITION'       => ATL_TEMPLATE_SURROUND, // print tag content only when condition true
    'AP:REPEAT'          => ATL_TEMPLATE_SURROUND, // repeat over an iterable
    'AP:CONTENT'         => ATL_TEMPLATE_CONTENT,  // replace tag content
    'AP:REPLACE'         => ATL_TEMPLATE_REPLACE,  // replace entire tag
    'AP:ATTRIBUTES'      => ATL_TEMPLATE_REPLACE,  // dynamically set tag attributes
    'AP:OMIT-TAG'        => ATL_TEMPLATE_SURROUND, // omit to print tag but not its content
    'AP:COMMENT'         => ATL_TEMPLATE_SURROUND, // do nothing
    'AP:ON-ERROR'        => ATL_TEMPLATE_SURROUND, // replace content with this if error occurs
    'AP:INCLUDE'     	 => ATL_TEMPLATE_REPLACE,  // include an external template 
    'AP:SRC-INCLUDE' 	 => ATL_TEMPLATE_CONTENT,  // include external file without parsing
	
    'METAL:DEFINE-MACRO' => ATL_TEMPLATE_SURROUND, // define a template macro
    'METAL:USE-MACRO'    => ATL_TEMPLATE_REPLACE,  // use a template macro
    'METAL:DEFINE-SLOT'  => ATL_TEMPLATE_SURROUND, // define a macro slot
    'METAL:FILL-SLOT'    => ATL_TEMPLATE_SURROUND, // fill a macro slot 

	
    'I18N:TRANSLATE'     => ATL_TEMPLATE_CONTENT,  // translate some data using GetText package
    'I18N:NAME'          => ATL_TEMPLATE_SURROUND, // prepare a translation name
	'I18N:ATTRIBUTES'    => ATL_TEMPLATE_REPLACE,
);

/**
 * @var   _atl_aliases
 * @var   hashtable
 *
 * Create aliases for attributes. If an alias is found during parsing, the
 * matching atl attribute will be used.
 *
 * @access private
 * @static
 */
global $_atl_aliases;
$_atl_aliases = array(      
);

/**
 * @var   _atl_rules_order
 * @var   hashtable
 * 
 * This rule associative array represents both ordering and exclusion 
 * mechanism for template attributes.
 *
 * All known attributes must appear here and must be associated with 
 * an occurence priority.
 *
 * When more than one atl attribute appear in the same tag, they 
 * will execute in following order.
 * 
 * @access private
 * @static
 */ 
global $_atl_rules_order;
$_atl_rules_order = array(
    'AP:OMIT-TAG'			=> 0,    // surround -> $tag->disableHeadFootPrint()

    'AP:ON-ERROR'			=> 1,    // surround

    'METAL:DEFINE-MACRO'	=> 3,    // surround
    'AP:DEFINE'				=> 3,    // replace
    'I18N:NAME'				=> 3,    // replace
    'I18N:TRANSLATE'		=> 3,    // content

    'AP:CONDITION'			=> 4,    // surround

    'AP:REPEAT'				=> 5,    // surround

	'I18N:ATTRIBUTES'		=> 6,    // replace
    'AP:ATTRIBUTES'			=> 6,    // replace
    'AP:REPLACE'			=> 6,    // replace
    'METAL:USE-MACRO'		=> 6,    // replace
    'AP:SRC-INCLUDE'		=> 6,    // replace
    'AP:INCLUDE'			=> 6,    // replace
    'METAL:DEFINE-SLOT'		=> 6,    // replace
    'METAL:FILL-SLOT'		=> 6,    // replace
    
    'AP:CONTENT'			=> 7,    // content

    'AP:COMMENT'			=> 8,    // surround
);

/**
 * @var _ATL_TEMPLATE_XHTML_content_free_tags
 * @var  array
 *
 * This array contains XHTML tags that must be echoed in a &lt;tag/&gt; form
 * instead of the &lt;tag&gt;&lt;/tag&gt; form.
 *
 * In fact, some browsers does not support the later form so ATL 
 * ensure these tags are correctly echoed.
 */
global $_atl_xhtml_empty_tags;
$_atl_xhtml_empty_tags = array(
    'AREA',
    'BASE',
    'BASEFONT',
    'BR',
    'COL',
    'FRAME',
    'HR',
    'IMG',
    'INPUT',
    'ISINDEX',
    'LINK',
    'META',
    'PARAM',
);

/**
 * @var _ATL_TEMPLATE_XHTML_boolean_attributes
 * @var  array
 *
 * This array contains XHTML attributes that must be echoed in a minimized
 * form. Some browsers (non HTML4 compliants are unable to interpret those
 * attributes.
 *
 * The output will definitively not be an xml document !!
 * PreFilters should be set to modify xhtml input containing these attributes.
 */
global $_atl_xhtml_boolean_attributes;
$_atl_xhtml_boolean_attributes = array(
    'compact',
    'nowrap',
    'ismap',
    'declare',
    'noshade',
    'checked',
    'disabled',
    'readonly',
    'multiple',
    'selected',
    'noresize',
    'defer'
);


/**
 * @package template_atl
 */
 
class ATL_Template extends PEAR
{
	/**
	 * @access private
	 */
    var $_ctx;
	
	/**
	 * @access private
	 */
    var $_code;
	
	/**
	 * @access private
	 */
    var $_codeFile;
	
	/**
	 * @access private
	 */
    var $_funcName;
	
	/**
	 * @access private
	 */
    var $_sourceFile;
	
	/**
	 * @access private
	 */
	var $_cacheManager;
	
	/**
	 * @access private
	 */
    var $_inputFilters;
	
	/**
	 * @access private
	 */
    var $_outputFilters;
	
	/**
	 * @access private
	 */
    var $_resolvers;
	
	/**
	 * @access private
	 */
    var $_locator;
	
	/**
	 * @access private
	 */
    var $_translator;
	
	/**
	 * @access private
	 */
    var $_error = false;
	
	/**
	 * @access private
	 */
    var $_repository = false;
	
	/**
	 * @access private
	 */
    var $_cacheDir = false;
	
	/**
	 * @access private
	 */
    var $_parent = false;
	
	/**
	 * @access private
	 */
    var $_parentPath = false;
	
	/**
	 * @access private
	 */
    var $_prepared = false;

	/**
	 * @access private
	 */
    var $_outputMode = ATL_TEMPLATE_XHTML;
	
	/**
	 * @access private
	 */
    var $_encoding = 'UTF-8';
    
	
    /**
     * Constructor
     *
     * @param  string $file                 The source file name
     * @param  string $repository optional  Your templates root.
     * @param  string $cache_dir optional   Intermediate php code repository.
	 * @access public
     */
    function ATL_Template( $file, $repository = false, $cache_dir = false )
    {
        $this->_sourceFile = $file;
        $this->_repository = $repository;

        // deduce intermediate php code cache directory
        if ( !$cache_dir ) 
			$cache_dir = ATL_TEMPLATE_DEFAULT_CACHE_DIR;
		
        $this->_cacheDir = $cache_dir;

        // instantiate a new context for this template
        // !!! this context may be overwritten by a parent context
        $this->_ctx = new ATL_Context();
        
        // create resolver vector and the default filesystem resolver
        $this->_resolvers = new ATL_Array();
        $this->_resolvers->push( new ATL_SourceResolver );
        
        // vector for source filters
        $this->_inputFilters  = new ATL_Array();
        $this->_outputFilters = new ATL_Array();
        
        // if no cache manager set, we instantiate default dummy one
        if ( !isset( $this->_cacheManager ) )
            $this->_cacheManager = new ATL_Cache;
    }

	
    /**
     * Set template ouput type.
     *
     * Default output is XHTML, so you'll have to call this method only for
     * specific xml documents  with ATL_TEMPLATE_XML parameter.
     * 
     * @param  int  $mode  output mode (ATL_TEMPLATE_XML) as default system use XHTML
	 * @access public
     */
    function setOutputMode( $mode )
    {
        $this->_outputMode = $mode;
    }
    
    /**
     * Replace template context with specified hashtable.
     *
     * @param  hash  hashtable  Associative array.
	 * @access public
     */
    function setAll( $hash )
    {
        $this->_ctx = new ATL_Context( $hash );
    }
    
    /**
     * Set a template context value.
     * 
     * @param  string $key    The context key
     * @param  mixed  $value  The context value
	 * @access public
     */
    function set( $name, $value )
    {
        $this->_ctx->set( $name, $value );
    }

    /**
     * Set a template context value by reference.
     * 
     * @param  string  $name   The template context key
     * @param  mixed   $value  The template context value
	 * @access public
     */
    function setRef( $name, &$value )
    {
        $this->_ctx->setRef( $name, $value );
    }

    /**
     * Retrieve template context object.
     *
     * @return ATL_Context
	 * @access public
     */
    function &getContext()
    {
        return $this->_ctx;
    }

    /**
     * Set the template context object.
     *
     * @param ATL_Context $ctx  The context object
	 * @access public
     */
    function setContext( &$ctx )
    {
        $this->_ctx =& $ctx;
    }

    /**
     * Set the cache manager to use for Template an Macro calls.
     *
     * @param ATL_Cache $mngr  Cache object that will be used to cache
     *                         template and macros results.
	 * @access public
     */
    function setCacheManager( &$mngr )
    {
        $this->_cacheManager =& $mngr;
    }

    /**
     * Retrieve the cache manager used in this template.
     *
     * @return ATL_Cache
	 * @access public
     */
    function &getCacheManager()
    {
        return $this->_cacheManager;
    }

    /**
     * Set the I18N implementation to use in this template.
     *
     * @param  ATL_I18N  $tr  I18N implementation
	 * @access public
     */
    function setTranslator( &$tr )
    {
        $this->_translator =& $tr;
    }
    
    /**
     * The translator used by this template.
     *
     * @return ATL_I18N
	 * @access public
     */
    function &getTranslator()
    {
        return $this->_translator;
    }
    
    /**
     * Test if the template file exists.
	 *
     * @deprecated use isValid() instead
     * @return boolean
	 * @access public
     */
    function fileExists()
    {
        return $this->isValid();
    }

    /**
     * Test if the template resource exists.
     * 
     * @return boolean
	 * @access public
     */    
    function isValid()
    {
        if ( isset( $this->_locator ) )
            return true;
        
        // use template resolvers to locate template source data
        // in most cases, there will be only one resolver in the
        // resolvers list (the default one) which look on the file 
        // system.

        $i = $this->_resolvers->getNewIterator();
        while ( $i->isValid() ) 
		{
            $resolver =& $i->value();
            $locator  =& $resolver->resolve( $this->_sourceFile, $this->_repository, $this->_parentPath );
            
			if ( $locator && !PEAR::isError( $locator ) ) 
			{
                $this->_locator   =& $locator;
                $this->_real_path =  $this->_locator->realPath();
                
				return true;
            }
			
            $i->next();
        }
		
        return false;
    }

    /**
     * Add a source resolver to the template.
     *
     * @param  ATL_SourceResolver $resolver
     *         The source resolver.
	 * @access public
     */
    function addSourceResolver( &$resolver )
    {
        $this->_resolvers->pushRef( $resolver );
    }

    /**
     * Add filter to this template input filters list.
     *
     * @param  ATL_Filter $filter 
     *         A filter which will be invoked on template source.
	 * @access public
     */
    function addInputFilter( &$filter )
    {
        $this->_inputFilters->pushRef( $filter );
    }

    /**
     * Add an output filter to this template output filters list.
     *
     * @param  ATL_Filter $filter
     *         A filter which will be invoked on template output.
	 * @access public
     */
    function addOutputFilter( &$filter )
    {
        $this->_outputFilters->pushRef( $filter );
    }
    
    /**
     * Retrieve the source template real path.
     *
     * This method store its result internally if no $file attribute is
     * specified (work on template internals).
     *
     * If a file name is specified, this method will try to locate it
     * exploring current path (PWD), the current template location, 
     * the repository and parent template location.
     * 
     * @param string $file optional 
     *        some file name to locate.
     *        
     * @throws Error 
     * @return string
	 * @access public
     */
    function realpath( $file = false )
    {
        // real template path
        if ( !$file ) 
		{
            if ( $this->isValid() ) 
                return $this->_real_path;
            else
				return PEAR::raiseError( $this->_sourceFile . ' not found.' );
        }
        
        // path to some file relative to this template
        $i = $this->_resolvers->getNewIterator();
        while ( $i->isValid() ) 
		{
            $resolver =& $i->value();
            $locator  =& $resolver->resolve( $file, $this->_repository, $this->_real_path );
			
            if ( $locator )
                return $locator->realPath();
            
            $i->next();
        }

		return PEAR::raiseError( $this->_sourceFile . ' not found.' );
    }

    /**
     * Set the template result encoding.
     *
     * Changing this encoding will change htmlentities behaviour.
     *
     * Example:
     *
     * $tpl->setEncoding('ISO-8859-1");
     *
     * See http://fr2.php.net/manual/en/function.htmlentities.php for a list of
     * supported encodings.
     * 
     * @param  $enc string Template encoding
	 * @access public
     */
    function setEncoding( $enc )
    {
        $this->_encoding = $enc;
    }

    /**
     * Retrieve the template result encoding.
     *
     * @return string
	 * @access public
     */
    function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Set the called template. (internal)
     *
     * @access public
     */
    function setParent( &$tpl )
    {
        $this->_parent       =& $tpl;
        $this->_resolvers    =  $tpl->_resolvers;
        $this->_inputFilters =  $tpl->_inputFilters;
        $this->_parentPath   =  $tpl->realPath();
        $this->_cacheManager =& $tpl->getCacheManager();
        $this->_translator   =& $tpl->_translator;
        
		$this->setOutputMode( $tpl->_outputMode );
    }

    /**
     * Execute template with prepared context.
     *
     * This method execute the template file and returns the produced string.
     * 
     * @return string
     * @throws Error
	 * @access public
     */
    function execute()
    {
        $err = $this->_prepare();
        
		if ( PEAR::isError( $err ) ) 
		{
            $this->_ctx->_errorRaised = true;
            return $err;
        }
		
        return $this->_cacheManager->template( $this, $this->_sourceFile, $this->_ctx );
    }
	
    /**
     * Generate a string representation of specified variable.
     *
     * @param  mixed  $var  variable to represent in a string form.
     * @static
	 * @access public
     */
    function toString( &$var )
    {
        if ( is_object( $var ) ) 
		{
            return ATL_Template::_objToString( $var );
        } 
		else if ( is_array( $var ) ) 
		{
            if ( array_key_exists( 0, $var ) || count( $var ) == 0 )
                return ATL_Template::_arrayToString( $var );
            else
                return ATL_Template::_hashToString( $var );
        } 
		else if ( is_resource( $var ) ) 
		{
            return '#' . gettype( $var ) . '#';
        }
		
        return $var;
    }
	
	
	// private methods
	
    /**
     * Prepare template execution.
     *
     * @access private
     */
    function _prepare()
    {
        if ( $this->_prepared ) 
			return;
        
		$this->_sourceFile = $this->realpath();
        
        // ensure that no error remain
        if ( PEAR::isError( $this->_sourceFile ) )
            return $this->_sourceFile; 
        
        $this->_funcName = ATL_TEMPLATE_CACHE_FILE_PREFIX . ATL_TEMPLATE_MARK . md5( $this->_sourceFile );
        $this->_codeFile = $this->_cacheDir . $this->_funcName . '.php';
        $this->_prepared = true;
    }
    
    /**
     * Generate php code from template source.
     * 
     * @access private
     * @throws Error
     */
    function _generateCode()
    {
        $parser = new ATL_Parser();
        $parser->_outputMode( $this->_outputMode );
        $data   = $this->_locator->data();

        // activate prefilters on data
        $i = $this->_inputFilters->getNewIterator();
        while ( $i->isValid() )
		{
            $filter =& $i->value();
            $data   =  $filter->filter( $data );
			
            $i->next();
        }

        // parse source
        $result = $parser->parse( $this->_real_path, $data );
		
        if ( PEAR::isError( $result ) )
            return $result;

        // generate and store intermediate php code
        $this->_code = $parser->generateCode( $this->_funcName );
		
        if ( PEAR::isError( $this->_code ) )
            return $this->_code;
    }

    /**
     * Load cached php code.
     * 
     * @access private
     */
    function _loadCachedCode()
    {
        include_once( $this->_codeFile );
        $this->_code = "#loaded";    
    }

    /**
     * Cache generated php code.
     * 
     * @access private
     */
    function _cacheCode()
    {
        $fp = @fopen( $this->_codeFile, "w" );
        
		if ( !$fp )
            return PEAR::raiseError( "Cannot read from cache." );
        
        fwrite( $fp, $this->_code );
        fclose( $fp );
    }
    
    /**
     * Load or generate php code.
     * 
     * @access private
     */
    function _load()
    {
        if ( isset( $this->_code ) && !PEAR::isError( $this->_code ) ) 
            return; 
        
        if ( !defined( 'ATL_NO_CACHE' ) && file_exists( $this->_codeFile ) && filemtime( $this->_codeFile ) >= $this->_locator->lastModified() )
            return $this->_loadCachedCode();
        
        $err = $this->_generateCode();
        
		if ( PEAR::isError( $err ) )
            return $err;
        
        $err = $this->_cacheCode();
        
		if ( PEAR::isError( $err ) )
            return $err;

        $err = $this->_loadCachedCode();
        
		if ( PEAR::isError( $err ) ) 
            return $err;
    }

    /**
     * Really load/parse/execute the template and process output filters.
     *
     * This method is called by cache manager to retrieve the real template
     * execution value.
     *
     * IMPORTANT : The result is post-filtered here !
     * 
     * @return string
     * @access private
     */
    function _process()
    {
        $err = $this->_load();
        
		if ( PEAR::isError( $err ) ) 
		{ 
            $this->_ctx->_errorRaised = true;
            return $err;
        }

        $this->_ctx->_errorRaised = false;
        $func = $this->_funcName;
        
		if ( !function_exists( $func ) ) 
            return PEAR::raiseError( "Template function '$func' not found (template source: $this->_sourceFile" );
        
        // ensure translator exists
        if ( !isset( $this->_translator ) )
            $this->_translator = new ATL_I18N;
        
        $res = $func( $this );
        
        // activate post filters
        $i = $this->_outputFilters->getNewIterator();
        while ( $i->isValid() ) 
		{
            $filter =& $i->value();
            $res    =  $filter->filter( $this, $res, ATL_FILTER_POST );
			
            $i->next();
        }
		
        return $res;
    }

	/**
	 * @access private
	 */
    function _translate( $key )
    {
        return $this->_translator->translate( $key );
    }

	/**
	 * @access private
	 */
    function _setTranslateVar( $name, $value )
    {
        if ( is_object( $value ) )
            $value = $value->toString();
        
        $this->_translator->set( $name, $value );
    }

    /**
     * Generate a string representation of an object calling its toString
     * method of using its class name.
     * 
     * @access protected
     * @static
     */
    function _objToString( &$var )
    {
        if ( method_exists( $var, "toString" ) )
            return $var->toString();
        else
            return '<' . get_class( $var ) . ' instance>';   
    }

    /**
     * Generate a string representation of a php array.
     * 
     * @access protected
     * @static
     */
    function _arrayToString( &$var )
    {
        $values = array();
		
        foreach ( $var as $val )
            $values[] = ATL_Template::toString( $val );
        
        return '[' . join( ', ', $values ) . ']';        
    }

    /**
     * Generate a string representation of an associative array.
     *
     * @access protected
     * @static 
     */
    function _hashToString( &$var )
    {
        $values = array();
		
        foreach ( $var as $key=>$val )
            $values[] = '\''. $key . '\': ' . ATL_Template::toString( $val );
        
        return '{' . join( ', ', $values ) . '}';        
    }
} // END OF ATL_Template

?>
