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
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.XML' );
  
  
/**
 * XSL Processor.
 * 
 * Example
 *
 * $proc = &new XSLProcessor();
 * $proc->setXSLFile( 'test.xml' );
 * $proc->setXMLFile( 'test.xsl' );
 * $proc->run();
 *
 * @link http://www.gingerall.com - Sablotron
 * @package xml_xslt
 */

class XSLProcessor extends XML
{
	/**
	 * @access public
	 */
    var $processor = null;
	
	/**
	 * @access public
	 */
	var $stylesheet = '';
	
	/**
	 * @access public
	 */
	var $buffer = array();
	
	/**
	 * @access public
	 */
	var $params = array();
	
	/**
	 * @access public
	 */
	var $output = '';


    /**
     * Constructor
     *
     * @access public
     * @param  array  params  default null
     */
    function XSLProcessor( $params = null ) 
	{
      	$this->XML( $params );
		
      	$this->processor = xslt_create();
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct()
	{
      	if ( $this->processor ) 
		{
        	xslt_free( $this->processor );
        	$this->processor = null;
      	}
      
	  	parent::__destruct();
    }
	
	
    /**
     * Set an error handler.
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_error_handler
     */
    function setErrorHandler( $funcName ) 
	{
      	xslt_set_error_handler( $this->processor, $funcName );
    }

    /**
     * Set a scheme handler.
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    function setSchemeHandler( $defines ) 
	{
      	xslt_set_scheme_handlers( $this->processor, $defines );
    }

    /**
     * Set base directory.
     *
     * @access  public
     * @param   string dir
     */
    function setBase( $dir, $proto = 'file://' ) 
	{
      	if ( '/' != $dir[strlen( $dir ) - 1] ) 
			$dir .= '/';
      
	  	xslt_set_base( $this->processor, $proto . $dir );
    }

    /**
     * Set XSL file.
     *
     * @access  public
     * @param   string file file name
     */
    function setXSLFile( $file ) 
	{
      	$this->stylesheet = array( $file, null );
    }
    
    /**
     * Set XSL buffer.
     *
     * @access  public
     * @param   string xsl the XSL as a string
     */
    function setXSLBuf( $xsl ) 
	{
      	$this->stylesheet = array( 'arg:/_xsl', $xsl );
    }

    /**
     * Set XML file.
     *
     * @access  public
     * @param   string file file name
     */
    function setXMLFile( $file ) 
	{
      	$this->buffer = array( $file, null );
    }
    
    /**
     * Set XML buffer.
     *
     * @access  public
     * @param   string xml the XML as a string
     */
    function setXMLBuf( $xml ) 
	{
     	$this->buffer = array( 'arg:/_xml', $xml );
    }

    /**
     * Set XSL transformation parameters.
     *
     * @access  public
     * @param   array params associative array { param_name => param_value }
     */
    function setParams( $params ) 
	{
      	$this->params = $params;
    }
    
    /**
     * Set XSL transformation parameter.
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    function setParam( $name, $val ) 
	{
      	$this->params[$name] = $val;
    }

    /**
     * Run the XSL transformation.
     *
     * @access  public
     * @return  bool success
     * @throws  Error
     */
    function run( $buffers = array() ) 
	{
      	if ( null != $this->buffer[1] ) 
			$buffers['/_xml'] = &$this->buffer[1];
      	
		if ( null != $this->stylesheet[1] ) 
			$buffers['/_xsl'] = &$this->stylesheet[1];
      
      	if ( ( $this->output = xslt_process( $this->processor, $this->buffer[0], $this->stylesheet[0], null, $buffers, $this->params ) ) === false )
        	return PEAR::raiseError( 'Transformation failed.' );
      
      	return true;
    }

    /**
     * Retrieve the transformation's result.
     *
     * @access  public
     * @return  string
     */
    function output()
	{
      	return $this->output;
    }
} // END OF XSLProcessor

?>
