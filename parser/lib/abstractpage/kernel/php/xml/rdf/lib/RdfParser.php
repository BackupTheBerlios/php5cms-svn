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
|Authors: Luis Argerich <lrargerich@yahoo.com>                         |
|         Chris Bizer <chris@bizer.de>                                 |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.rdf.lib.util.RdfMemoryModel' );
using( 'xml.rdf.lib.util.Literal' );
using( 'xml.rdf.lib.util.Resource' );
using( 'xml.rdf.lib.util.BlankNode' );
using( 'xml.rdf.lib.util.Statement' );


define( "RDFPARSER_NAMESPACE_SEPARATOR_CHAR",       '^' );
define( "RDFPARSER_NAMESPACE_SEPARATOR_STRING",     "^" );

define( "RDFPARSER_IN_TOP_LEVEL",                     0 );
define( "RDFPARSER_IN_RDF",                           1 );
define( "RDFPARSER_IN_DESCRIPTION",                   2 );
define( "RDFPARSER_IN_PROPERTY_UNKNOWN_OBJECT",       3 );
define( "RDFPARSER_IN_PROPERTY_RESOURCE",             4 );
define( "RDFPARSER_IN_PROPERTY_EMPTY_RESOURCE",       5 );
define( "RDFPARSER_IN_PROPERTY_LITERAL",              6 );
define( "RDFPARSER_IN_PROPERTY_PARSE_TYPE_LITERAL",   7 );
define( "RDFPARSER_IN_PROPERTY_PARSE_TYPE_RESOURCE",  8 );
define( "RDFPARSER_IN_XML",                           9 );
define( "RDFPARSER_IN_UNKNOWN",                      10 );

define( "RDFPARSER_RDFAPI_SUBJECT_TYPE_URI",             0 );
define( "RDFPARSER_RDFAPI_SUBJECT_TYPE_DISTRIBUTED",     1 );
define( "RDFPARSER_RDFAPI_SUBJECT_TYPE_PREFIX",          2 );
define( "RDFPARSER_RDFAPI_SUBJECT_TYPE_ANONYMOUS",       3 );
define( "RDFPARSER_RDFAPI_SUBJECT_TYPE_BNODE",           4 );

define( "RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE",         0 );
define( "RDFPARSER_RDFAPI_OBJECT_TYPE_LITERAL",          1 );
define( "RDFPARSER_RDFAPI_OBJECT_TYPE_XML",              2 );
define( "RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE",            3 );

define( "RDFPARSER_XML_NAMESPACE_URI", "http://www.w3.org/XML/1998/namespace" );
define( "RDFPARSER_VALUE", "value" );


/**
 * An RDF paser. 
 * This class reads RDF data from files or URIs and generates models out of it. All valid
 * RDF XML syntaxes defined by the W3C in Resource Description Framework (RDF) Model and 
 * Syntax Specification - W3C Recommendation 22 February 1999
 * (http://www.w3.org/TR/1999/REC-rdf-syntax-19990222/) are supported.
 * In addition to the 1999 RDF Syntax Specification, the parser supports rdf:datatype and 
 * rdf:nodeID elements according to RDF/XML Syntax Specification (Revised), W3C Working Draft 
 * 8 November 2002  * (http://www.w3.org/TR/2002/WD-rdf-syntax-grammar-20021108/). 
 * The parser is based on the PHP version of repat 
 * (http://phpxmlclasses.sourceforge.net/show_doc.php?class=class_rdf_parser.html) 
 * by Luis Argerich (lrargerich@yahoo.com).
 *
 * @package xml_rdf_lib
 */   
  
class RdfParser extends PEAR
{
	/**
	 * @access public
	 */
	var $rdf_parser;
	
	/**
	 * @access public
	 */
	var $model;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function RdfParser()
	{
	}
	
	
	/**
	 * Generates a new RdfMemoryModel from a URI, a file or from memory.
	 * If you want to parse an RDF document, pass the URI or location in the filesystem 
	 * of the RDF document. You can also pass RDF code direct to the function. If you pass
	 * RDF code directly to the parser and there is no xml:base included, you should set 
	 * the base URI manually using model->setBaseURI(). 
	 * Make sure that here are proper namespace declarations in your input document.
	 *
	 * @access	public 
	 * @param   string	 $base
	 * @return	object RdfMemoryModel
	 */  
	function &generateModel( $base )
	{
		// Check if $base is a URI or filename or a string containing RDF code.
		if ( substr( ltrim( $base ), 0, 1 ) != "<" )
		{
			$fp = fopen( $base, "r" );
			$result = '';
        
			while ( !feof( $fp ) )
				$result .= fread( $fp, 512 );
			
			fclose( $fp );
			$base = $result;
		}
		
		$this->model = new RdfMemoryModel();
		$this->rdf_parser_create( null );
		$this->rdf_set_base( null );
	
		if ( ! $this->rdf_parse( $base, strlen( $base ), true ) )
		{
			$err_code = xml_get_error_code( $this->rdf_get_xml_parser());
			$line     = xml_get_current_line_number( $this->rdf_get_xml_parser() );
			$errmsg   = "RDFAPI error (class: parser; method: generateModel): XML-parser-error " . $err_code . " in Line " . $line . " of input document.";
			
			trigger_error( $errmsg, E_USER_ERROR ); 
		}

		// base_uri could have changed while parsing
		$this->model->setBaseURI( $this->rdf_parser["base_uri"] );
		$this->rdf_parser_free();
	
		return $this->model;
	}

	/**
	 * @param   string $encoding
 	 * @access	public 
	 */ 
	function rdf_parser_create( $encoding )
	{
		$parser = xml_parser_create_ns( $encoding, RDFPARSER_NAMESPACE_SEPARATOR_CHAR );
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		$this->rdf_parser["xml_parser"] = $parser;

		xml_set_object( $this->rdf_parser["xml_parser"], $this );
		xml_set_element_handler( $this->rdf_parser["xml_parser"], "_start_element_handler", "_end_element_handler" );
		xml_set_character_data_handler( $this->rdf_parser["xml_parser"], "_character_data_handler" );

		return $this->rdf_parser;
	}

	/**
	 * @access	public 
	 */ 
	function rdf_parser_free()
	{
    	$z = 3;
		$this->rdf_parser["base_uri"] = '';
		$this->_delete_elements( $this->rdf_parser );
		unset( $this->rdf_parser );
	}

	/**
	 * @param	string	 $s
	 * @param	string	 $len
	 * @param	string	 $is_final
	 * @access	public 
	 */ 
	function rdf_parse( $s, $len, $is_final )
	{
		return xml_parse( $this->rdf_parser["xml_parser"], $s, $is_final );
	}

	/**
	 * @access	public 
	 */ 
	function rdf_get_xml_parser()
	{
		return ( $this->rdf_parser["xml_parser"] );
	}

	/**
	 * @param   string	 $base
	 * @access	public 
	 */ 
	function rdf_set_base( $base )
	{   
		$this->rdf_parser["base_uri"] = $base;
		$c = substr( $base, strlen( $base ) - 1 ,1 );
	
		if ( !( $c == "#" || $c == ":" || $c == "/" || $c == "\\" ) )
			$this->rdf_parser["normalized_base_uri"] = $base . "#";
		else
			$this->rdf_parser["normalized_base_uri"] = $base; 

		return 0;
	}

	/**
	 * @access	public 
	 */ 
	function rdf_get_base()
	{
		return $this->rdf_parser["base_uri"];
	}

	/**
	 * @param		string	 $uri_reference
	 * @param		string	 &$buffer
	 * @access	public 
	 */ 
	function rdf_resolve_uri( $uri_reference, &$buffer )
	{
		$this->_resolve_uri_reference( $this->rdf_parser["base_uri"], $uri_reference, $buffer, strlen( $buffer ) );
	}


	// private methods

	/**
	 * @access	private 
	 */  
	function _new_element()
	{
    	$e["parent"] = array(); // Parent is a blank Array
		$e["state"] = 0;
		$e["has_property_atributes"] = 0;
		$e["has_member_attributes"] = 0;
		$e["subject_type"] = 0;
		$e["subject"] = '';
		$e["predicate"] = '';
		$e["ordinal"] = 0;
		$e["members"] = 0;
		$e["data"] = '';
		$e["xml_lang"] = '';
		$e["bag_id"] = '';
		$e["statements"] = 0;
		$e["statement_id"] = '';
		$e["datatype"] = '';
		
		return $e;
	}

	/**
	 * @param string $source
	 * @param string &$destination
	 *
	 * @access	private 
	 */  
	function _copy_element( $source, &$destination )
	{
    	if ( $source )
    	{
        	$destination["parent"]   = $source;
        	$destination["state"]    = $source["state"];
        	$destination["xml_lang"] = $source["xml_lang"];
    	}
	}

	/**
	 * @param string &$e
	 * @access	private 
	 */  
	function _clear_element( &$e )
	{
        $e["subject"] = '';
		$e["predicate"] = '';
        $e["data"] = '';
        $e["bag_id"] = '';
        $e["statement_id"] = '';

		if ( isset( $e["parent"] ) )
		{
			if ( $e["parent"] )
			{
				if ( $e["parent"]["xml_lang"] != $e["xml_lang"] )
					$e["xml_lang"]='';
			}
			else
			{
				$e["xml_lang"] = '';
			}
		}
		else
		{
			$e["xml_lang"] = '';
        }
		
        $e["parent"] = array();
        $e["state"] = 0;
        $e["has_property_attributes"] = 0;
        $e["has_member_attributes"] = 0;
        $e["subject_type"] = 0;
        $e["subject"] = '';
        $e["predicate"] = '';
        $e["ordinal"] = 0;
        $e["members"] = 0;
        $e["data"] = '';
        $e["xml_lang"] = '';
        $e["bag_id"] = '';
        $e["statements"] = 0;
        $e["statement_id"] = '';
		$e["datatype"] = '';
	}

	/**
 	 * @access	private 
 	 */  
	function _push_element()
	{
    	if ( !isset( $this->rdf_parser["free"] ) )
			$this->rdf_parser["free"] = array();
    
    	if ( count( $this->rdf_parser["free"] ) > 0 )
		{
			$e = $this->rdf_parser["free"];
			
			if ( isset( $e["parent"] ) )
				$this->rdf_parser["free"] = $e["parent"];
			else
				$this->rdf_parser["free"] = $this->_new_element();
		}
		else
		{
			$e = $this->_new_element();
		}
		
		if ( !isset( $this->rdf_parser["top"] ) )
			$this->rdf_parser["top"] = array();
    
		$this->_copy_element( $this->rdf_parser["top"], $e );
		$this->rdf_parser["top"] = $e;
	}

	/**
	 * @access	private 
	 */  
	function _pop_element()
	{
		$e = $this->rdf_parser["top"];
		$this->rdf_parser["top"] = $e["parent"];
		$this->_clear_element( $e );
		$this->rdf_parser["free"] = $e;
	}
	
	/**
	 * @access	private 
	 */  
	function _delete_elements()
	{
	}

	/**
	 * @param string $local_name
	 * @access	private 
	 */  
	function _is_rdf_property_attribute_resource( $local_name )
	{
		return ( $local_name == RDFAPI_TYPE );
	}
	
	/**
	 * @param string $local_name
	 * @access	private 
	 */  
	function _is_rdf_property_attribute_literal( $local_name )
	{
    	return ( $local_name == RDFPARSER_VALUE );
	}
	
	/**
	 * @param string $local_name
	 * @access	private 
	 */  
	function _is_rdf_ordinal( $local_name )
	{
		$ordinal = -1;

		if ( $local_name{0} ==  '_'  )
			$ordinal =  substr( $local_name, 1 ) + 1 ;

		return ( $ordinal > 0 )? $ordinal : 0;
	}
	
	/**
	 * @param string $local_name
	 * @access	private 
	 */  
	function _is_rdf_property_attribute( $local_name )
	{
		return $this->_is_rdf_property_attribute_resource( $local_name ) || $this->_is_rdf_property_attribute_literal( $local_name );
	}
	
	/**
	 * @param string $local_name
	 * @access	private 
	 */  
	function _is_rdf_property_element( $local_name )
	{
    	return ( $local_name == RDFAPI_TYPE      )
			|| ( $local_name == RDFAPI_SUBJECT   )
			|| ( $local_name == RDFAPI_PREDICATE )
			|| ( $local_name == RDFAPI_OBJECT    )
			|| ( $local_name == RDFPARSER_VALUE     )
			|| ( $local_name == RDFAPI_LI        )
			|| ( $local_name == RDFAPI_SEEALSO   )
			|| ( $local_name{0} == '_'  );
	}
	
	/**
	 * @param string $val
	 * @access	private 
	 */  
	function _istalnum( $val )
	{
		return ereg( "[A-Za-z0-9]", $val );
	}
	
	/**
	 * @param string $val
	 * @access	private 
	 */  
	function _istalpha( $val )
	{
		return ereg( "[A-Za-z]", $val );
	}

	/**
	 * @param string $uri
	 * @access	private 
	 */  
	function _is_absolute_uri( $uri )
	{
    	$result = false;
        $uri_p  = 0;
		
		if ( $uri && $this->_istalpha( $uri{$uri_p} ) )
		{
			++$uri_p;

			while ( ($uri_p < strlen( $uri ) ) && ( $this->_istalnum( $uri{$uri_p} ) || ( $uri{$uri_p} == '+' ) || ( $uri{$uri_p} == '-'  ) || ( $uri{$uri_p} ==  '.'  ) ) )
				++$uri_p;

			$result = ( $uri{$uri_p} == ':'  );
		}
		
		return $result;
	}

	/**
	 * @param string $uri
	 * @param string $buffer
	 * @param string $len
	 * @param string &$scheme
	 * @param string &$authority
	 * @param string &$path
	 * @param string &$query
	 * @param string &$fragment
	 *
	 * @access	private 
	 */  
	function _parse_uri( $uri, $buffer, $len, &$scheme, &$authority, &$path, &$query, &$fragment )
	{
		$parsed = parse_url( $uri );
		
		if ( isset( $parsed["scheme"] ) )
			$scheme = $parsed["scheme"];
		else
			$scheme='';
		
		if ( isset( $parsed["host"] ) )
			$host = $parsed["host"];
		else 
			$host = '';
		
		if ( isset( $parsed["host"] ) )
			$authority = $parsed["host"];
		else
			$authority = '';
		
		if ( isset( $parsed["path"] ) )
			$path = $parsed["path"];
		else
			$path = '';
		
		if ( isset( $parsed["query"] ) )
			$query = $parsed["query"];
		else 
			$query = '';
		
		if ( isset( $parsed["fragment"] ) )
			$fragment = $parsed["fragment"];
		else
			$fragment = '';
	}

	/**
	 * @param string $base_uri
	 * @param string $reference_uri
	 * @param string &$buffer
	 * @param string $length
	 * @access	private 
	 */ 
	function _resolve_uri_reference( $base_uri, $reference_uri, &$buffer, $length )
	{
		$base_buffer      = '';
		$reference_buffer = '';
		$path_buffer      = '';
		$buffer           = '';

		$this->_parse_uri( $reference_uri, $reference_buffer, strlen( $reference_buffer ), $reference_scheme, $reference_authority, $reference_path, $reference_query, $reference_fragment );

		if ( $reference_scheme == '' && $reference_authority == '' && $reference_path == '' && $reference_query == '' )
		{
			$buffer = $base_uri;

			if ( $reference_fragment != '' )
			{
				$c = substr( $base_uri, strlen( $base_uri ) - 1 ,1 );
				
				if ( !( $c == "#" || $c == ":" || $c == "/" || $c == "\\" ) )
					$buffer .= "#" ;
				
				$buffer .= $reference_fragment;
			}
		}
		else if ( $reference_scheme != '' )
		{
			$buffer = $reference_uri;
		}
		else
		{
			$this->_parse_uri(
				$base_uri,
				$base_buffer,
				strlen( $base_buffer ),
				$base_scheme,
				$base_authority,
				$base_path,
				$base_query,
				$base_fragment
			);

			$result_scheme = $base_scheme;

			if ( $reference_authority != '' )
			{
				$result_authority = $reference_authority;
			}
			else
			{
				$result_authority = $base_authority;

				if ( $reference_path != '' && ( ( $reference_path{0} == '/' ) || ( $reference_path{0} ==  '\\' ) ) )
				{
					$result_path = $reference_path;
				}
				else
				{
					$p = '';
					$result_path = $path_buffer;
					$path_buffer = '';
					$p = strstr( $base_path,  '/'  );

					if ( !$p )
						$p = strstr( $base_path, '\\'  );

					if ( $p )
						$path_buffer .= $base_path;

					if ( $reference_path != '' )
						$path_buffer .= $reference_path;

					$path_buffer = preg_replace( "/\/\.\//",            "/", $path_buffer );
					$path_buffer = preg_replace( "/\/([^\/\.])*\/..$/", "/", $path_buffer );
						
					while ( preg_match( "/\.\./", $path_buffer ) )
						$path_buffer = preg_replace( "/\/([^\/\.]*)\/..\//", "/", $path_buffer );
                    
					$path_buffer = preg_replace( "/\.$/", "", $path_buffer );
				}
			}

			$result_path = $path_buffer;
			
			if ( $result_scheme != '' )
			{
				$buffer  = $result_scheme;
				$buffer .= ":";
			}

			if ( $result_authority != '' )
			{
				$buffer .= "//";
				$buffer .= $result_authority;
			}

			if ( $result_path != '' )
				$buffer .= $result_path;
				
			if ( $reference_query != '' )
			{
				$buffer .= "?";
				$buffer .= $reference_query;
			}

			if ( $reference_fragment != '' )
			{
				$buffer .= "#";
				$buffer .= $reference_fragment;
			}
		}
	}

	/**
	 * @param string $id	 
	 * @access	private 
	 */ 
	function is_valid_id( $id )
	{
    	$result = false;
    	$p      = $id;
    	$p_p    = 0;

    	if ( $id != '' )
    	{
        	if ( $this->_istalpha( $p ) || $p{0} == '_' || $p{0} == ':'  )
			{
				$result = true;

				while ( $result != false && ( $p{++$p_p} != 0 ) )
				{
					if( ! ( $this->_istalnum( $p{$p_p} ) || $p{$p_p} == '.' || $p{$p_p} == '-' || $p{$p_p} == '_' || $p{$p_p} == ':'  ) )
						$result = false;
				}
			}
		}

		return $result;
	}
	
	/**
	 * @param string $id
	 * @param string &$buffer
	 * @param string $length
	 * @access	private 
	 */ 
	function _resolve_id( $id, &$buffer, $length )
	{
    	$id_buffer='';

		if ( $this->is_valid_id( $id ) == true )
			$id_buffer = "#$id";
		else
			trigger_error( "RDFAPI error (class: parser): bad ID attribute: " . $id_buffer . "#_bad_ID_attribute_.", E_USER_WARNING );

		$this->_resolve_uri_reference( $this->rdf_parser["base_uri"], $id_buffer, $buffer, $length );
	}
	
	/**
	 * @param string $name
	 * @param string &$buffer
	 * @param string $len
	 * @param string &$namespace_uri
	 * @param string &$local_name
	 * @access	private 
	 */ 
	function _split_name( $name, &$buffer, $len, &$namespace_uri, &$local_name )
	{
		static $nul = 0;
		$buffer = $name;

		if ( strstr( $buffer, RDFPARSER_NAMESPACE_SEPARATOR_CHAR ) )
		{
			$cosas         = explode( RDFPARSER_NAMESPACE_SEPARATOR_CHAR, $buffer );
			$namespace_uri = $cosas[0];
			$local_name    = $cosas[1];
        }
        else
        {
			if ( ( $buffer{ 0 } ==  'x' ) && ( $buffer{ 1 } ==  'm' ) && ( $buffer{ 2 } ==  'l' ) && ( $buffer{ 3 } ==  ':' ) )
			{
				$namespace_uri = RDFPARSER_XML_NAMESPACE_URI;
				$local_name = substr( $buffer, 4 );
			}
			else
			{
				$namespace_uri = '';
				$local_name = $buffer;
			}
		}
	}
	
	/**
	 * @param string &$buf
	 * @param string $len
	 * @access	private 
	 */ 
	function _generate_anonymous_uri( &$buf, $len )
	{
    	$id = '';
		
    	if ( !isset( $this->rdf_parser["anonymous_id"] ) )
			$this->rdf_parser["anonymous_id"] = 0;
    
		$this->rdf_parser["anonymous_id"]++;
		$buf =  RDFAPI_BNODE_PREFIX . $this->rdf_parser["anonymous_id"];
	}
	
	/**
	 * @param string $subject_type 
	 * @param string $subject
	 * @param string $predicate
	 * @param string $ordinal
	 * @param string $object_type
	 * @param string $object
	 * @param string $xml_lang
	 * @param string $bag_id
	 * @param string $statements
	 * @param string $statement_id
	 * @access	private 
	 */ 
	function _report_statement( $subject_type, $subject, $predicate, $ordinal, $object_type,  $object, $xml_lang, $bag_id, $statements, $statement_id, $datatype )
	{
		$statement_id_type   = RDFPARSER_RDFAPI_SUBJECT_TYPE_URI;
		$statement_id_buffer = '';
		$predicate_buffer    = '';
	
		// call add statement
		$this->add_statement_to_model( $this->rdf_parser["user_data"], $subject_type, $subject, $predicate, $ordinal, $object_type, $object, $xml_lang, $datatype );

		if ( $bag_id )
		{
			if ( $statements == '' )
			{
				$this->_report_statement(
					RDFPARSER_RDFAPI_SUBJECT_TYPE_URI,
					$bag_id,
                    RDFAPI_NAMESPACE_URI . RDFAPI_TYPE,
                    0,
                    RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE,
                    RDFAPI_NAMESPACE_URI.RDFAPI_BAG,
                    '',
                    '',
                    '',
                    '',
					$datatype
				);
            }

            if ( ! $statement_id )
            {
				$statement_id_type = RDFPARSER_RDFAPI_SUBJECT_TYPE_BNODE;
				
				$this->_generate_anonymous_uri(
					$statement_id_buffer,
                    strlen( $statement_id_buffer )
				);
				
                $statement_id = $statement_id_buffer;
			}
			
            $statements++;
            $predicate_buffer = "RDFAPI_NAMESPACE_URI_" . $statements;

            $this->_report_statement(
                RDFPARSER_RDFAPI_SUBJECT_TYPE_URI,
                $bag_id,
                $predicate_buffer,
                $statements,
                RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE,
                $statement_id,
                '',
                '',
                '',
                '',
				$datatype
			);
        }

        if ( $statement_id )
        {
			// rdf:type = rdf:Statement
            $this->_report_statement(
                $statement_id_type,
                $statement_id,
                RDFAPI_NAMESPACE_URI . RDFAPI_TYPE,
                0,
                RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE,
                RDFAPI_NAMESPACE_URI . RDFAPI_STATEMENT,
                '',
                '',
                '',
                '',
				$datatype
			);

            // rdf:subject
            $this->_report_statement(
                $statement_id_type,
                $statement_id,
                RDFAPI_NAMESPACE_URI . RDFAPI_SUBJECT,
                0,
                RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE,
                $subject,
                '',
                '',
                '',
                '',
				$datatype
			);

            // rdf:predicate
            $this->_report_statement(
                $statement_id_type,
                $statement_id,
                RDFAPI_NAMESPACE_URI . RDFAPI_PREDICATE,
                0,
                RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE,
                $predicate,
                '',
                '',
                '',
                '',
				$datatype
			);

            // rdf:object
            $this->_report_statement(
                $statement_id_type,
                $statement_id,
                RDFAPI_NAMESPACE_URI . RDFAPI_OBJECT,
                0,
                $object_type,
                $object,
                '',
                '',
                '',
                '',
				$datatype
			);
		}
	}
	
	/**
	 * @param string $subject_type
	 * @param string $subject
	 * @param string $attributes
	 * @param string $xml_lang
	 * @param string $bag_id
	 * @param string $statements
	 * @access	private 
	 */ 
	function _handle_property_attributes( $subject_type, $subject, $attributes, $xml_lang, $bag_id, $statements )
	{
    	$i = 0;
    	$attribute = '';
    	$predicate = '';
		$attribute_namespace_uri = '';
		$attribute_local_name = '';
		$attribute_value = '';
		$ordinal = 0;

		for ( $i = 0; isset( $attributes[ $i ] ); $i += 2 )
		{
			$this->_split_name(
				$attributes[ $i ],
				$attribute,
				strlen( $attribute ),
				$attribute_namespace_uri,
				$attribute_local_name
			);

			$attribute_value = $attributes[ $i + 1 ];
			$predicate  = $attribute_namespace_uri;
			$predicate .= $attribute_local_name;

			if ( RDFAPI_NAMESPACE_URI == $attribute_namespace_uri )
			{
            	if ( $this->_is_rdf_property_attribute_literal( $attribute_local_name ) )
				{
					$this->_report_statement(
						$subject_type,
						$subject,
						$predicate,
						0,
						RDFPARSER_RDFAPI_OBJECT_TYPE_LITERAL,
						$attribute_value,
						$xml_lang,
						$bag_id,
						$statements,
						'',
						''
					);
				}
				else if ( $this->_is_rdf_property_attribute_resource( $attribute_local_name ) )
				{
					$this->_report_statement(
						$subject_type,
						$subject,
						$predicate,
						0,
						RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE,
						$attribute_value,
						'',
						$bag_id,
						$statements,
						'',
						''
					);
				}
				else if ( ( $ordinal = $this->_is_rdf_ordinal( $attribute_local_name ) ) != 0 )
				{
					$this->_report_statement(
						$subject_type,
						$subject,
						$predicate,
						$ordinal,
						RDFPARSER_RDFAPI_OBJECT_TYPE_LITERAL,
						$attribute_value,
						$xml_lang,
						$bag_id,
						$statements,
						'',
						''
					);
				}
			}
			else if ( RDFPARSER_XML_NAMESPACE_URI == $attribute_namespace_uri )
			{
				// do nothing
			}
			else if ( $attribute_namespace_uri )
			{
				// is it required that property attributes be in an explicit namespace?

				$this->_report_statement(
					$subject_type,
					$subject,
					$predicate,
					0,
					RDFPARSER_RDFAPI_OBJECT_TYPE_LITERAL,
					$attribute_value,
					$xml_lang,
					$bag_id,
					$statements,
					'',
					''
				);
			}
		}
	}
	
	/**
	 * @param string $namespace_uri
	 * @param string $local_name
	 * @param string $attributes
	 * @param string $parent
	 * @access	private 
	 */ 
	function _handle_resource_element( $namespace_uri, $local_name, $attributes, $parent )
	{
		$subjects_found = 0;
		$aux  = $attributes;
		$aux2 = array();
		
		foreach ( $attributes as $atkey => $atvalue )
		{
			$aux2[] = $atkey;
			$aux2[] = $atvalue;
    	}
		
		$attributes = $aux2;
		$id = '';
		$about = '';
		$about_each = '';
		$about_each_prefix = '';
		$bag_id = '';
		$node_id = '';
		$datatype = '';
		$i = 0;
		$attribute = '';
		$attribute_namespace_uri = '';
		$attribute_local_name = '';
		$attribute_value = '';
		$id_buffer = '';
		$type = '';

		$this->rdf_parser["top"]["has_property_attributes"] = false;
		$this->rdf_parser["top"]["has_member_attributes"]   = false;

		// examine each attribute for the standard RDF "keywords"
		for ( $i = 0; isset( $attributes[$i] ); $i += 2 )
		{
			$this->_split_name(
				$attributes[ $i ],
				$attribute,
				strlen( $attribute ),
				$attribute_namespace_uri,
				$attribute_local_name
			);

			$attribute_value = $attributes[$i + 1];

			// if the attribute is not in any namespace
			// or the attribute is in the RDF namespace
			if ( ( $attribute_namespace_uri == '' ) || (  $attribute_namespace_uri == RDFAPI_NAMESPACE_URI ) )
			{
				if ( $attribute_local_name == RDFAPI_ID )
				{
					$id = $attribute_value;
					++$subjects_found;
				}
				else if ( $attribute_local_name == RDFAPI_ABOUT )
				{
					$about = $attribute_value;
					++$subjects_found;
				}
				else if ( $attribute_local_name == RDFAPI_NODEID )
				{
					$node_id = $attribute_value;
					++$subjects_found;
				}
				else if (  $attribute_local_name == RDFAPI_ABOUT_EACH )
				{
					$about_each = $attribute_value;
					++$subjects_found;
				}
				else if ( $attribute_local_name == RDFAPI_ABOUT_EACH_PREFIX )
				{
					$about_each_prefix = $attribute_value;
					++$subjects_found;
				}
				else if ( $attribute_local_name == RDFAPI_BAG_ID)
				{
					$bag_id = $attribute_value;
				}
				else if ( $attribute_local_name == RDFAPI_DATATYPE)
				{
					$datatype = $attribute_value;
				}
				else if ( $this->_is_rdf_property_attribute( $attribute_local_name ) )
				{
					$this->rdf_parser["top"]["has_property_attributes"] = true;
				}
				else if ( $this->_is_rdf_ordinal( $attribute_local_name ) )
				{
					$this->rdf_parser["top"]["has_property_attributes"] = true;
					$this->rdf_parser["top"]["has_member_attributes"]   = true;
				}
				else
				{
					trigger_error( "RDFAPI error (class: parser): unknown or out of context rdf attribute:" . $attribute_local_name . ".", E_USER_WARNING );
				}
			}
			else if (  $attribute_namespace_uri == RDFPARSER_XML_NAMESPACE_URI )
			{
				if ( $attribute_local_name == RDFAPI_XML_LANG )
					$this->rdf_parser["top"]["xml_lang"] = $attribute_value;
			}
			else if ( $attribute_namespace_uri )
			{
				$this->rdf_parser["top"]["has_property_attributes"] = true;
			}
		}

		// if no subjects were found, generate one.
		if ( $subjects_found == 0 )
		{
			$this->_generate_anonymous_uri( $id_buffer, strlen( $id_buffer ) );
			$this->rdf_parser["top"]["subject"] = $id_buffer;
			$this->rdf_parser["top"]["subject_type"] = RDFPARSER_RDFAPI_SUBJECT_TYPE_BNODE;
		}
		else if ( $subjects_found > 1 )
		{
			trigger_error( "RDFAPI error (class: parser): ID, about, aboutEach, nodeID and aboutEachPrefix are mutually exclusive.", E_USER_WARNING );
			return;
		}
		else if ( $id )
		{
			$this->_resolve_id( $id, $id_buffer, strlen( $id_buffer ) );
			$this->rdf_parser["top"]["subject_type"] = RDFPARSER_RDFAPI_SUBJECT_TYPE_URI;
			$this->rdf_parser["top"]["subject"] = $id_buffer;
		}
		else if ( $about )
		{
			$this->_resolve_uri_reference( $this->rdf_parser["base_uri"], $about, $id_buffer, strlen( $id_buffer ) );
			$this->rdf_parser["top"]["subject_type"] = RDFPARSER_RDFAPI_SUBJECT_TYPE_URI;
			$this->rdf_parser["top"]["subject"] = $id_buffer;
		}
		else if ( $about_each )
		{
			$this->rdf_parser["top"]["subject_type"] = RDFPARSER_RDFAPI_SUBJECT_TYPE_DISTRIBUTED;
			$this->rdf_parser["top"]["subject"] = $about_each;
		}
		else if ( $about_each_prefix )
		{
			$this->rdf_parser["top"]["subject_type"] = RDFPARSER_RDFAPI_SUBJECT_TYPE_PREFIX;
			$this->rdf_parser["top"]["subject"] = $about_each_prefix;
		}
		else if ( $node_id )
		{
			$this->rdf_parser["top"]["subject_type"] = RDFPARSER_RDFAPI_SUBJECT_TYPE_BNODE;
			$this->rdf_parser["top"]["subject"] = $node_id;
		}
	
		// if the subject is empty, assign it the document uri
		if ( $this->rdf_parser["top"]["subject"] == '' )
    	{
        	$len = 0;
			$this->rdf_parser["top"]["subject"] = $this->rdf_parser["base_uri"];

			// now remove the trailing '#'

			$len = strlen( $this->rdf_parser["top"]["subject"]);

			if ( $len > 0 )
			{
				// $rdf_parser["top"]["subject"][" len - 1 "] = 0;
			}
		}
		
		if ( $bag_id )
		{
			$this->_resolve_id( $bag_id, $id_buffer, strlen( $id_buffer ) );
			$this->rdf_parser["top"]["bag_id"] = $id_buffer;
		}

		// only report the type for non-rdf:Description elements.
		if ( ( $local_name != RDFAPI_DESCRIPTION ) || ( $namespace_uri != RDFAPI_NAMESPACE_URI ) )
		{
			$type  = $namespace_uri;
			$type .= $local_name;

			$this->_report_statement(
				$this->rdf_parser["top"]["subject_type"],
				$this->rdf_parser["top"]["subject"],
				RDFAPI_NAMESPACE_URI . RDFAPI_TYPE,
				0,
				RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE,
				$type,
				'',
				$this->rdf_parser["top"]["bag_id"],
				$this->rdf_parser["top"]["statements"],
				'', 
				$datatype
			);
		}

		// if this element is the child of some property,
		// report the appropriate statement.
		if ( $parent )
		{
			if ( $this->rdf_parser["top"]["subject_type"] == RDFPARSER_RDFAPI_SUBJECT_TYPE_BNODE )
				$objtype = RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE; 
			else
				$objtype = RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE;
        
			$this->_report_statement(
				$parent["parent"]["subject_type"],
				$parent["parent"]["subject"],
				$parent["predicate"],
				$parent["ordinal"],
				$objtype,
				$this->rdf_parser["top"]["subject"],
				'',
				$parent["parent"]["bag_id"],
				$parent["parent"]["statements"],
				$parent["statement_id"], 
				$parent["datatype"]
			);
		}

		if ( $this->rdf_parser["top"]["has_property_attributes"] )
		{
			$this->_handle_property_attributes(
				$this->rdf_parser["top"]["subject_type"],
				$this->rdf_parser["top"]["subject"],
				$attributes,
				$this->rdf_parser["top"]["xml_lang"],
				$this->rdf_parser["top"]["bag_id"],
				$this->rdf_parser["top"]["statements"]
			);
		}
	}
	
	/**
	 * @param string &$namespace_uri
	 * @param string &$local_name
	 * @param string &$attributes
	 * @access	private 
	 */ 
	function _handle_property_element( &$namespace_uri, &$local_name, &$attributes )
	{
    	$buffer = '';
		$i      = 0;
		$aux    = $attributes;
		$aux2   = array();
		
		foreach ( $attributes as $atkey => $atvalue )
		{
			$aux2[] = $atkey;
			$aux2[] = $atvalue;
		}
		
		$attributes = $aux2;
		$attribute_namespace_uri = '';
		$attribute_local_name = '';
		$attribute_value = '';
		$resource = '';
		$statement_id = '';
		$bag_id = '';
		$parse_type = '';
		$node_id = '';
		$datatype = ''; 

		$this->rdf_parser["top"]["ordinal"] = 0;

		if ( $namespace_uri == RDFAPI_NAMESPACE_URI )
		{
			if ( ($this->rdf_parser["top"]["ordinal"] = ( $this->_is_rdf_ordinal( $local_name ) ) != 0 ) )
			{
				if ( $this->rdf_parser["top"]["ordinal"] > $this->rdf_parser["top"]["parent"]["members"] )
					$this->rdf_parser["top"]["parent"]["members"] = $this->rdf_parser["top"]["ordinal"];
			}
			else if ( ! $this->_is_rdf_property_element( $local_name ) )
			{
				trigger_error( "RDFAPI error (class: parser): unknown or out of context rdf property element: " . $local_name . ".", E_USER_WARNING );
				return;
			}
		}

		$buffer = $namespace_uri;

		if( ( $namespace_uri == RDFAPI_NAMESPACE_URI ) && ( $local_name == RDFAPI_LI ) )
		{
			$this->rdf_parser["top"]["parent"]["members"]++;
			$this->rdf_parser["top"]["ordinal"] = $this->rdf_parser["top"]["parent"]["members"];
			$this->rdf_parser["top"]["ordinal"] = $this->rdf_parser["top"]["ordinal"];

			$buffer .= '_' . $this->rdf_parser["top"]["ordinal"];
		}
		else
		{
			$buffer .= $local_name;
		}

		$this->rdf_parser["top"]["predicate"] = $buffer;
		$this->rdf_parser["top"]["has_property_attributes"] = false;
		$this->rdf_parser["top"]["has_member_attributes"]   = false;

		for ( $i = 0; isset( $attributes[$i] ); $i += 2 )
		{
			$this->_split_name(
				$attributes[$i],
				$buffer,
				strlen( $buffer ),
				$attribute_namespace_uri,
				$attribute_local_name
			);

			$attribute_value = $attributes[$i + 1];

			// if the attribute is not in any namespace
			// or the attribute is in the RDF namespace
			if ( ( $attribute_namespace_uri == '' ) || (  $attribute_namespace_uri == RDFAPI_NAMESPACE_URI ) )
			{
				if ( ( $attribute_local_name == RDFAPI_ID )  )
				{
					$statement_id = $attribute_value;
				}
				else if ( $attribute_local_name == RDFAPI_PARSE_TYPE )
				{
					$parse_type = $attribute_value;
				}
				else if (  $attribute_local_name == RDFAPI_RESOURCE )
				{
					$resource = $attribute_value;
				}
				else if (  $attribute_local_name == RDFAPI_NODEID )
				{
					$node_id = $attribute_value;		
				}
				else if (  $attribute_local_name == RDFAPI_BAG_ID )
				{
					$bag_id = $attribute_value;
				}
				else if ( $attribute_local_name == RDFAPI_DATATYPE )
				{
					$datatype = $attribute_value;
					$this->rdf_parser["top"]["datatype"] = $attribute_value;
				}
				else if ( $this->_is_rdf_property_attribute( $attribute_local_name ) )
				{
					$this->rdf_parser["top"]["has_property_attributes"] = true;
				}
				else
				{
					trigger_error( "RDFAPI error (class: parser): unknown rdf attribute: " . $attribute_local_name . ".", E_USER_WARNING );
					return;
				}
			}
			else if ( $attribute_namespace_uri == RDFPARSER_XML_NAMESPACE_URI )
			{
				if ( $attribute_local_name == RDFAPI_XML_LANG )
				$this->rdf_parser["top"]["xml_lang"] = $attribute_value;
			}
			else if ( $attribute_namespace_uri )
			{
				$this->rdf_parser["top"]["has_property_attributes"] = true;
			}
		}
		
		// this isn't allowed by the M&S but I think it should be
		if ( $statement_id && $resource )
		{
			trigger_error( "RDFAPI error (class: parser): rdf:ID and rdf:resource are mutually exclusive.", E_USER_WARNING );
			return;
		}

		if ( $statement_id )
		{
			$this->_resolve_id( $statement_id, $buffer, strlen( $buffer ) );
			$this->rdf_parser["top"]["statement_id"] = $buffer;
		}
	
		if ( $node_id )
		{
			$this->_report_statement(
				$this->rdf_parser["top"]["parent"]["subject_type"],
				$this->rdf_parser["top"]["parent"]["subject"],
				$this->rdf_parser["top"]["predicate"],
				$this->rdf_parser["top"]["ordinal"],
				RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE,
				$node_id,
				'',
				$this->rdf_parser["top"]["parent"]["bag_id"],
				$this->rdf_parser["top"]["parent"]["statements"],
				'',
				$datatype
			);
			
			$this->rdf_parser["top"]["state"] = RDFPARSER_IN_PROPERTY_EMPTY_RESOURCE;
		}	
		   
		if ( $parse_type )
		{
			if ( $resource )
			{
				trigger_error( "RDFAPI error (class: parser): property elements with rdf:parseType do not allow rdf:resource.", E_USER_WARNING );
				return;
			}

			if ( $bag_id )
			{	
				trigger_error( "RDFAPI error (class: parser): property elements with rdf:parseType do not allow rdf:bagID.", E_USER_WARNING );
				return;
			}

			if ( $this->rdf_parser["top"]["has_property_attributes"] )
			{
				trigger_error( "RDFAPI error (class: parser): property elements with rdf:parseType do not allow property attributes.", E_USER_WARNING );
				return;
			}
		
			if (  $attribute_value == RDFAPI_PARSE_TYPE_RESOURCE )
			{
				$this->_generate_anonymous_uri( $buffer, strlen( $buffer ) );
				
				// since we are sure that this is now a resource property we can report it
				$this->_report_statement(
					$this->rdf_parser["top"]["parent"]["subject_type"],
					$this->rdf_parser["top"]["parent"]["subject"],
					$this->rdf_parser["top"]["predicate"],
					0,
					RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE,
					$buffer,
					'',
					$this->rdf_parser["top"]["parent"]["bag_id"],
					$this->rdf_parser["top"]["parent"]["statements"],
					$statement_id,
					$datatype
				);

				$this->_push_element();

				$this->rdf_parser["top"]["state"]        = RDFPARSER_IN_PROPERTY_PARSE_TYPE_RESOURCE;
				$this->rdf_parser["top"]["subject_type"] = RDFPARSER_RDFAPI_SUBJECT_TYPE_BNODE;
				$this->rdf_parser["top"]["subject"]      = $buffer;
				$this->rdf_parser["top"]["bag_id"]       = '';
				$this->rdf_parser["top"]["datatype"]     = $datatype;
			}
			else
			{
				$this->_report_statement(
					$this->rdf_parser["top"]["parent"]["subject_type"],
					$this->rdf_parser["top"]["parent"]["subject"],
					$this->rdf_parser["top"]["predicate"],
					0,
					RDFPARSER_RDFAPI_OBJECT_TYPE_XML,
					'',
					'',
					$this->rdf_parser["top"]["parent"]["bag_id"],
					$this->rdf_parser["top"]["parent"]["statements"],
					$statement_id, 
					$datatype
				);

				$this->rdf_parser["top"]["state"] = RDFPARSER_IN_PROPERTY_PARSE_TYPE_LITERAL;
			}
		}
		else if ( $resource || $bag_id || $this->rdf_parser["top"]["has_property_attributes"] )
		{
			if ( $resource != '' )
			{
				$subject_type = RDFPARSER_RDFAPI_SUBJECT_TYPE_URI;
				$this->_resolve_uri_reference( $this->rdf_parser["base_uri"], $resource, $buffer, strlen( $buffer ) );
				$object_type = RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE;
			}
			else
			{
				$subject_type = RDFPARSER_RDFAPI_SUBJECT_TYPE_BNODE;
				$this->_generate_anonymous_uri( $buffer, strlen( $buffer ) );
				$object_type = RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE;
			}
			
			$this->rdf_parser["top"]["state"] = RDFPARSER_IN_PROPERTY_EMPTY_RESOURCE;

			// since we are sure that this is now a resource property we can report it.
			$this->_report_statement(
				$this->rdf_parser["top"]["parent"]["subject_type"],
				$this->rdf_parser["top"]["parent"]["subject"],
				$this->rdf_parser["top"]["predicate"],
				$this->rdf_parser["top"]["ordinal"],
				$object_type,
				$buffer,
				'',
				$this->rdf_parser["top"]["parent"]["bag_id"],
				$this->rdf_parser["top"]["parent"]["statements"],
				'',
				$datatype
			); // should we allow IDs?
			
			if( $bag_id )
			{
				$this->_resolve_id( $bag_id, $buffer, strlen( $buffer ) );
				$this->rdf_parser["top"]["bag_id"] = $buffer;
			}

			if ( $this->rdf_parser["top"]["has_property_attributes"] )
			{
				$this->_handle_property_attributes(
					$subject_type,
					$buffer,
					$attributes,
					$this->rdf_parser["top"]["xml_lang"],
					$this->rdf_parser["top"]["bag_id"],
					$this->rdf_parser["top"]["statements"]
				);
			}
		} 
	}

	/**
	 * @param string $parser
	 * @param string $name
	 * @param string $attributes
	 * @access	private 
	*/ 
	function _start_element_handler( $parser, $name, $attributes )
	{
		$buffer        = '';
		$namespace_uri = '';
		$local_name    = '';

		$this->_push_element();

		$this->_split_name(
			$name,
			$buffer,
			strlen( $buffer ),
			$namespace_uri,
			$local_name
		);

		switch ( $this->rdf_parser["top"]["state"] )
		{
			case RDFPARSER_IN_TOP_LEVEL:
				// set base_uri, if possible 
				foreach ( $attributes as $key => $value )
				{
					if ( $key == RDFPARSER_XML_NAMESPACE_URI . RDFPARSER_NAMESPACE_SEPARATOR_CHAR . "base" )
					{ 
						$this->rdf_parser["base_uri"] = $value;
						$c = substr( $value, strlen( $value ) - 1 , 1 );
						
						if ( !( $c == "#" || $c == ":" || $c == "/" || $c == "\\"))
							$this->rdf_parser["normalized_base_uri"] = $value . "#";
						else
							$this->rdf_parser["normalized_base_uri"] = $value; 
					}
				}
				
				if ( RDFAPI_NAMESPACE_URI.RDFPARSER_NAMESPACE_SEPARATOR_STRING.RDFAPI_RDF == $name )
				{            
					$this->rdf_parser["top"]["state"] = RDFPARSER_IN_RDF;
				}
				else
				{
					// $this->_report_start_element( $name, $attributes );
				}
				
				break;
			
			case RDFPARSER_IN_RDF:
				$this->rdf_parser["top"]["state"] = RDFPARSER_IN_DESCRIPTION;
				$this->_handle_resource_element( $namespace_uri, $local_name, $attributes, '' );
				
				break;
				
			case RDFPARSER_IN_DESCRIPTION:
			
			case RDFPARSER_IN_PROPERTY_PARSE_TYPE_RESOURCE:
				$this->rdf_parser["top"]["state"] = RDFPARSER_IN_PROPERTY_UNKNOWN_OBJECT;
				$this->_handle_property_element( $namespace_uri, $local_name, $attributes );
				
				break;
			
			case RDFPARSER_IN_PROPERTY_UNKNOWN_OBJECT:
				/* 
				if we're in a property with an unknown object type and we encounter
				an element, the object must be a resource, 
				*/
				$this->rdf_parser["top"]["data"] = '';
				$this->rdf_parser["top"]["parent"]["state"] = RDFPARSER_IN_PROPERTY_RESOURCE;
				$this->rdf_parser["top"]["state"] = RDFPARSER_IN_DESCRIPTION;
				
				$this->_handle_resource_element(
					$namespace_uri,
					$local_name,
					$attributes,
					$this->rdf_parser["top"]["parent"]
				);
				
				break;
			
			case RDFPARSER_IN_PROPERTY_LITERAL:
				trigger_error( "RDFAPI error (class: parser): no markup allowed in literals.", E_USER_WARNING );
				break;
			
			case RDFPARSER_IN_PROPERTY_PARSE_TYPE_LITERAL:
				$this->rdf_parser["top"]["state"] = RDFPARSER_IN_XML;
				/* fall through */
			
			case RDFPARSER_IN_XML:
				echo $name, $attributes[1]."<p>";
				// $this->_report_start_element( $name, $attributes );
				
				break;
			
			case RDFPARSER_IN_PROPERTY_RESOURCE:
				trigger_error( "RDFAPI error (class: parser): only one element allowed inside a property element.", E_USER_WARNING );
				break;
			
			case RDFPARSER_IN_PROPERTY_EMPTY_RESOURCE:
				trigger_error( "RDFAPI error (class: parser): no content allowed in property with rdf:resource, rdf:bagID, or property attributes.", E_USER_WARNING );
				break;
			
			case RDFPARSER_IN_UNKNOWN:
				break;
		}
	}

	/**
	 * This is only called when we're in the RDFPARSER_IN_PROPERTY_UNKNOWN_OBJECT state.
	 * the only time we won't know what type of object a statement has is
	 * when we encounter property statements without property attributes or
	 * content:
	 *
	 * <foo:property />
	 * <foo:property ></foo:property>
	 * <foo:property>    </foo:property>
	 * 
	 * Notice that the state doesn't switch to RDFPARSER_IN_PROPERTY_LITERAL when
	 * there is only whitespace between the start and end tags. this isn't
	 * a very useful statement since the object is anonymous and can't
	 * have any statements with it as the subject but it is allowed.
	 *
	 * @access	private 
	 */ 
	function _end_empty_resource_property()
	{
    	$buffer='';

		$this->_generate_anonymous_uri( $buffer, strlen( $buffer ) );

		$this->_report_statement(
			$this->rdf_parser["top"]["parent"]["subject_type"],
			$this->rdf_parser["top"]["parent"]["subject"],
			$this->rdf_parser["top"]["predicate"],
			$this->rdf_parser["top"]["ordinal"],
			RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE,
			$buffer,
			$this->rdf_parser["top"]["xml_lang"],
			$this->rdf_parser["top"]["parent"]["bag_id"],
			$this->rdf_parser["top"]["parent"]["statements"],
			$this->rdf_parser["top"]["statement_id"],
			$this->rdf_parser["top"]["datatype"]
		);
	}

	/**
     * property elements with text only as content set the state to
	 * RDFPARSER_IN_PROPERTY_LITERAL. as character data is received from expat,
	 * it is saved in a buffer and reported when the end tag is
	 * received.
	 *
	 * @access	private 
	 */ 
	function _end_literal_property()
	{
    	if ( !isset( $this->rdf_parser["top"]["statement_id"] ) )
			$this->rdf_parser["top"]["statement_id"] = '';
    
		if ( !isset( $this->rdf_parser["top"]["parent"]["subject_type"] ) )
			$this->rdf_parser["top"]["parent"]["subject_type"] = '';
    
		if ( !isset( $this->rdf_parser["top"]["parent"]["subject"] ) )
			$this->rdf_parser["top"]["parent"]["subject"] = '';
    
		if ( !isset( $this->rdf_parser["top"]["parent"]["bag_id"] ) )
			$this->rdf_parser["top"]["parent"]["bag_id"] = '';
    
		if ( !isset( $this->rdf_parser["top"]["parent"]["statements"] ) )
			$this->rdf_parser["top"]["parent"]["statements"] = 0;
    
		if ( !isset( $this->rdf_parser["top"]["predicate"] ) )
			$this->rdf_parser["top"]["predicate"] = '';
    
		if ( !isset( $this->rdf_parser["top"]["datatype"] ) )
			$this->rdf_parser["top"]["datatype"] = '';
    
		if ( !isset( $this->rdf_parser["top"]["ordinal"] ) )
			$this->rdf_parser["top"]["ordinal"] = 0;
    
		$this->_report_statement(
			$this->rdf_parser["top"]["parent"]["subject_type"],
			$this->rdf_parser["top"]["parent"]["subject"],
			$this->rdf_parser["top"]["predicate"],
			$this->rdf_parser["top"]["ordinal"],
			RDFPARSER_RDFAPI_OBJECT_TYPE_LITERAL,
			$this->rdf_parser["top"]["data"],
			$this->rdf_parser["top"]["xml_lang"],
			$this->rdf_parser["top"]["parent"]["bag_id"],
			$this->rdf_parser["top"]["parent"]["statements"],
			$this->rdf_parser["top"]["statement_id"], 
			$this->rdf_parser["top"]["datatype"]
		);
	}

	/**
	 * @param string $parser
	 * @param string $name
	 * @access	private 
	 */ 
	function _end_element_handler( $parser, $name )
	{
		switch ( $this->rdf_parser["top"]["state"] )
		{
			case RDFPARSER_IN_TOP_LEVEL:
				/* fall through */
			
			case RDFPARSER_IN_XML:
				// $this->_report_end_element( $name );
				break;
			
			case RDFPARSER_IN_PROPERTY_UNKNOWN_OBJECT:
				$this->_end_empty_resource_property();
				break;
			
			case RDFPARSER_IN_PROPERTY_LITERAL:
				$this->_end_literal_property( );
				break;
			
			case RDFPARSER_IN_PROPERTY_PARSE_TYPE_RESOURCE:
				$this->_pop_element();
				break;
			
			case RDFPARSER_IN_PROPERTY_PARSE_TYPE_LITERAL:
				// $this->_report_end_parse_type_literal();
				break;
			
			case RDFPARSER_IN_RDF:
			
			case RDFPARSER_IN_DESCRIPTION:
			
			case RDFPARSER_IN_PROPERTY_RESOURCE:
			
			case RDFPARSER_IN_PROPERTY_EMPTY_RESOURCE:
			
			case RDFPARSER_IN_UNKNOWN:
				break;
		}

		$this->_pop_element();
	}

	/**
	 * @param string $parser
	 * @param string $s
	 * @access	private 
	*/ 
	function _character_data_handler( $parser, $s )
	{
		$len = strlen( $s );
		
		switch ( $this->rdf_parser["top"]["state"] )
		{
			case RDFPARSER_IN_PROPERTY_LITERAL:
			
			case RDFPARSER_IN_PROPERTY_UNKNOWN_OBJECT:
				if ( isset( $this->rdf_parser["top"]["data"] ) )
				{
					$n = strlen( $this->rdf_parser["top"]["data"] );
					$this->rdf_parser["top"]["data"] .= $s;
				}
				else
				{
					$this->rdf_parser["top"]["data"] = $s;
				}

				if ( $this->rdf_parser["top"]["state"] == RDFPARSER_IN_PROPERTY_UNKNOWN_OBJECT )
				{
					/* look for non-whitespace */
					for ( $i = 0; (( $i < $len ) && ( ereg(" |\n|\t",$s{ $i } ) ) ); $i++ );            
					
					/* if we found non-whitespace, this is a literal */
					if( $i < $len )
						$this->rdf_parser["top"]["state"] = RDFPARSER_IN_PROPERTY_LITERAL;
				}

				break;
			
			case RDFPARSER_IN_TOP_LEVEL:
			
			case RDFPARSER_IN_PROPERTY_PARSE_TYPE_LITERAL:
			
			case RDFPARSER_IN_XML:
				// $this->_report_character_data( $s, strlen( $s ) );
				break;
			
			case RDFPARSER_IN_RDF:
			
			case RDFPARSER_IN_DESCRIPTION:
			
			case RDFPARSER_IN_PROPERTY_RESOURCE:
			
			case RDFPARSER_IN_PROPERTY_EMPTY_RESOURCE:
			
			case RDFPARSER_IN_PROPERTY_PARSE_TYPE_RESOURCE:
			
			case RDFPARSER_IN_UNKNOWN:
				break;
		}
	}
	
	/**
	 * Adds a new statement to the model
	 * This method is called by generateModel().
	 *
	 * @access	private 
	 * @param	string	&$user_data
	 * @param	string	$subject_type
	 * @param	string	$subject
	 * @param	string	$predicate
	 * @param	string	$ordinal
	 * @param	string	$object_type
	 * @param	string	$object
	 * @param	string	$xml_lang )
	 * @return	object  RdfMemoryModel
	 */  
	function add_statement_to_model( &$user_data, $subject_type, $subject, $predicate, $ordinal, $object_type, $object, $xml_lang, $datatype )
	{
		// create subject
		if ( $subject_type == RDFPARSER_RDFAPI_SUBJECT_TYPE_BNODE ) 
			$objsub = new BlankNode( $subject );
	    else
			$objsub = new Resource( $subject );
		 
		// create predicate	
		$objpred = new Resource( $predicate );
	
		// create object
		if ( ( $object_type == RDFPARSER_RDFAPI_OBJECT_TYPE_RESOURCE ) || ( $object_type == RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE ) )
		{
			if ( $object_type == RDFPARSER_RDFAPI_OBJECT_TYPE_BNODE ) 
				$objobj = new BlankNode( $object );
			else
				$objobj = new Resource( $object );		
		}
		else
		{
			if ( $xml_lang == "" )
				$objobj = new Literal( $object );
			else 
				$objobj = new Literal( $object, $xml_lang );
			
			if ( $datatype != '' )
				$objobj->setDatatype( $datatype );
		}
	
		// create statement
		$statement = new Statement( $objsub, $objpred, $objobj );
		
		// add statement to model
		$this->model->add( $statement );
	}
} // END OF RdfParser

 ?>
