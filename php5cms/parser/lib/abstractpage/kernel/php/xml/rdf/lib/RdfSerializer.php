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
|Authors: Chris Bizer <chris@bizer.de>                                 |
|         Boris Motik <motik@fzi.de>                                   |
|         Daniel Westphal <dawe@gmx.de>                                |
|         Leandro Mariano Lopez <llopez@xinergiaargentina.com>         |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.rdf.RdfUtil' );


define( "RDFSERIALIZER_DEFAULT_ENCODING", "UTF-8" );

define( "RDFSERIALIZER_USE_ENTITIES",      false );
define( "RDFSERIALIZER_USE_ATTRIBUTES",    false );
define( "RDFSERIALIZER_SORT_MODEL",        true  );
define( "RDFSERIALIZER_RDF_QNAMES",        true  );
define( "RDFSERIALIZER_XML_DECLARATION",   true  );

define( "RDFSERIALIZER_GENERAL_PREFIX_BASE",       "ns"   );
define( "RDFSERIALIZER_MAX_ALLOWED_ABBREV_LENGTH", 60     );
define( "RDFSERIALIZER_USE_ANY_QUOTE",             0      );
define( "RDFSERIALIZER_USE_CDATA",                 1      );
define( "RDFSERIALIZER_SCHEMA_PREFIX",             "rdfs" );

define( "RDFSERIALIZER_SUBCLASSOF",    "subClassOf"    );
define( "RDFSERIALIZER_SUBPROPERTYOF", "subPropertyOf" );

define( "RDFSERIALIZER_XML_NAMESPACE_PREFIX", "xml" );
define( "RDFSERIALIZER_XML_NAMESPACE_DECL_PREFIX", "xmlns" );
define( "RDFSERIALIZER_NAMESPACE_PREFIX", "rdf" );


/**
 * An RDF seralizer.
 * Seralizes models to RDF syntax. It supports the xml:base, xml:lang, rdf:datatype and
 * rdf:nodeID directive.
 * You can choose between different output syntaxes by using the configuration methods
 * or changing the configuration default values in constants.php.
 * This class is based on the java class edu.unika.aifb.rdf.api.syntax.RDFSerializer by Boris Motik.
 *
 * @package xml_rdf_lib
 */
 
class RdfSerializer extends PEAR
{
	/**
	 * @access public
	 */
    var $use_entities;
	
	/**
	 * @access public
	 */
    var $use_attributes;
	
	/**
	 * @access public
	 */
    var $sort_model;
	
	/**
	 * @access public
	 */
    var $rdf_qnames;
	
	/**
	 * @access public
	 */
    var $use_xml_declaration;

	/**
	 * @access public
	 */
    var $m_out;
	
	/**
	 * @access public
	 */
    var $m_baseURI;
	
	/**
	 * @access public
	 */
    var $m_currentSubject;
	
	/**
	 * @access public
	 */
    var $m_rdfIDElementText;
	
	/**
	 * @access public
	 */
    var $m_rdfAboutElementText;
	
	/**
	 * @access public
	 */
    var $m_rdfResourceElementText;
	
	/**
	 * @access public
	 */
    var $m_groupTypeStatement;
	
	/**
	 * @access public
	 */
    var $rdf_qname_prefix;
	
	/**
	 * @access public
	 */
	var $m_nextAutomaticPrefixIndex;
	
	/**
	 * @access public
	 */
    var $m_defaultNamespaces = array();
	
	/**
	 * @access public
	 */
    var $m_namespaces = array();
	
	/**
	 * @access public
	 */
    var $m_statements = array();
	
	/**
	 * @access public
	 */
    var $m_attributeStatements = array();
	
	/**
	 * @access public
	 */
	var $m_contentStatements = array();

	
	/**
	 * Constructor
     *
     * @access   public
     */
    function RdfSerializer()
	{
        // default serializer configuration
        $this->use_entities        = RDFSERIALIZER_USE_ENTITIES;
        $this->use_attributes      = RDFSERIALIZER_USE_ATTRIBUTES;
        $this->sort_model          = RDFSERIALIZER_SORT_MODEL;
        $this->rdf_qnames          = RDFSERIALIZER_RDF_QNAMES;
        $this->use_xml_declaration = RDFSERIALIZER_XML_DECLARATION;

        // add default namespaces
        $this->addNamespacePrefix( RDFSERIALIZER_NAMESPACE_PREFIX, RDFAPI_NAMESPACE_URI );
        $this->addNamespacePrefix( RDFSERIALIZER_SCHEMA_PREFIX, RDFAPI_SCHEMA_URI );
    }

	
	/**
	 * Serializer congiguration: Sort Model
	 * Flag if the serializer should sort the model by subject before serializing.
	 * true makes the RDF code more compact.
	 * true is default. Default can be changed in constants.php.
	 *
	 * @param     boolean
	 * @access    public
	 */
	function configSortModel( $bool ) 
	{
		$this->sort_model = $bool;
    }

	/**
	 * Serializer congiguration: Use Entities
	 * Flag if the serializer should use entities for URIs.
	 * true makes the RDF code more compact.
	 * false is default. Default can be changed in constants.php.
	 *
	 * @param     boolean
	 * @access    public
	 */
    function configUseEntities( $bool ) 
	{
        $this->use_entities = $bool;
    }

	/**
	 * Serializer congiguration: Use Attributes
	 * Flag if the serializer should serialize triples as XML attributes where possible.
	 * true makes the RDF code more compact.
	 * false is default. Default can be changed in constants.php.
	 *
	 * @param     boolean
	 * @access    public
	 */
    function configUseAttributes( $bool ) 
	{
        $this->use_attributes = $bool;
    }

	/**
	 * Serializer congiguration: Use Qnames
	 * Flag if the serializer should use qualified names for RDF reserved words.
	 * true makes the RDF code more compact.
	 * true is default. Default can be changed in constants.php.
	 *
	 * @param     boolean
	 * @access    public
	 */
	function configUseQnames( $bool ) 
	{
		$this->rdf_qnames = $bool;
    }

	/**
	 * Serializer congiguration: Use XML Declaration
	 * Flag if the serializer should start documents with the xml declaration
	 * <?xml version="1.0" encoding="UTF-8" ?>.
	 * true is default. Default can be changed in constants.php.
	 *
	 * @param             boolean
	 * @access    public
	 */
	function configUseXmlDeclaration( $bool ) 
	{
		$this->use_xml_declaration = $bool;
    }

	/**
	 * Adds a new prefix/namespace combination.
	 *
	 * @param     String $prefix
	 * @param     String $namespace
	 * @access    public
	 */
	function addNamespacePrefix( $prefix, $namespace ) 
	{
		$this->m_defaultNamespaces[$prefix] = $namespace;
    }

	/**
	 * Serializes a model to RDF syntax.
	 * RDF syntax can be changed by config_use_attributes($boolean), config_use_entities($boolean),
	 * config_sort_model($boolean).
	 *
	 * @param     object RdfMemoryModel $model
	 * @param     String $encoding
	 * @return    string
	 * @access    public
	 */
    function &serialize( &$model, $encoding = RDFSERIALIZER_DEFAULT_ENCODING ) 
	{
		// define rdf prefix (qname or not)
		if ( $this->rdf_qnames )
			$this->rdf_qname_prefix = RDFSERIALIZER_NAMESPACE_PREFIX . ":";
		else
			$this->rdf_qname_prefix = "";

		// check if model is empty
		if ( $model->size() == 0 ) 
			return "<" . $this->rdf_qname_prefix . RDFAPI_RDF . " />";

		// copy default namespaces
		foreach ( $this->m_defaultNamespaces as $prefix => $namespace )
			$this->m_namespaces[$prefix] = $namespace;

		// set base URI
		if ( $model->getBaseURI() == null )
			$this->m_baseURI = "opaque:uri";
		else
			$this->m_baseURI = $model->getBaseURI();
    
		if ( $this->sort_model ) 
		{
			// sort the array of statements
				
			foreach ( $model->triples as $key => $statement ) 
			{
				$stmkey = $statement->subj->getURI() .
						  $statement->pred->getURI() .
						  $statement->obj->getLabel();
				
				$this->m_statements[$stmkey] = $statement;
			}
			
			ksort( $this->m_statements );
				
			/*
			// Sort using the PHP usort() function. Slower :-(
			$this->m_statements = $model->triples;
			usort( $this->m_statements, "statementsorter" );
			*/
		} 
		else 
		{
			$this->m_statements = $model->triples;
		}
		
		// collects namespaces
		$this->m_nextAutomaticPrefixIndex = 0;
		$this->collectNamespaces( $model );

		// start writing the contents
		if ( $this->use_xml_declaration )
			$this->m_out = "<?xml version='1.0' encoding='" . $encoding . "'?>" . RDFAPI_LINEFEED;

		// write entitie declarations
		if ( $this->use_entities ) 
		{
			$this->m_out .= "<!DOCTYPE " . $this->rdf_qname_prefix . RDFAPI_RDF." [" . RDFAPI_LINEFEED;
			$this->writeEntityDeclarations();
			$this->m_out .= RDFAPI_LINEFEED . "]>" . RDFAPI_LINEFEED;
		}

		// start the RDF text
		$this->m_out .= "<" . $this->rdf_qname_prefix . RDFAPI_RDF;

		// write the xml:base
		if ( $model->getBaseURI() != null )
			$this->m_out .= RDFAPI_LINEFEED . RDFAPI_INDENTATION . "xml:base=\"" . $model->getBaseURI() . "\"";

		// write namespaces declarations
		$this->writeNamespaceDeclarations();
		$this->m_out .= ">" . RDFAPI_LINEFEED;

		// write triples
		$this->writeDescriptions();

		$this->m_out .= RDFAPI_LINEFEED;
		$this->m_out .= "</" . $this->rdf_qname_prefix . RDFAPI_RDF . ">";

		$this->m_namespaces             = null;
		$this->m_statements             = null;
		$this->m_currentSubject         = null;
		$this->m_groupTypeStatement     = null;
		$this->m_attributeStatements    = null;
		$this->m_contentStatements      = null;
		$this->m_rdfResourceElementText = null;

		return $this->m_out;
	}

	/**
	 * Serializes a model and saves it into a file.
	 * Returns false if the model couldn't be saved to the file.
	 *
	 * @param     object RdfMemoryModel $model
	 * @param     String $encoding
	 * @return    boolean
	 * @access    public
	 */
    function saveAs( &$model, $filename, $encoding = RDFSERIALIZER_DEFAULT_ENCODING ) 
	{	
		// serialize model
		$RDF = $this->serialize( $model, $encoding = RDFSERIALIZER_DEFAULT_ENCODING );

		// write serialized model to file
		$file_handle = @fopen( $filename, 'w' );
		
		if ( $file_handle ) 
		{
			fwrite( $file_handle, $RDF );	
			fclose( $file_handle );
			
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	/**
	 * @access   private
	 */
	function writeEntityDeclarations()
	{
		foreach ( $this->m_namespaces as $prefix => $namespace )
			$this->m_out .= RDFAPI_INDENTATION . "<!ENTITY " . $prefix . " '" . $namespace . "'>" . RDFAPI_LINEFEED;
    }

	/**
	 * @access   private
	 */
	function writeNamespaceDeclarations()
	{
		foreach ( $this->m_namespaces as $prefix => $namespace )
		{
			if ( $prefix == RDFSERIALIZER_NAMESPACE_PREFIX && !$this->rdf_qnames ) 
			{
				if ( $this->use_entities ) 
                    $this->m_out .= RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFSERIALIZER_XML_NAMESPACE_DECL_PREFIX . "=\"&" . $prefix . ";\"";
				else 
                    $this->m_out .= RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFSERIALIZER_XML_NAMESPACE_DECL_PREFIX . "=\""  . $namespace . "\"";
            } 
			else 
			{
                if ( $this->use_entities ) 
                    $this->m_out .= RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFSERIALIZER_XML_NAMESPACE_DECL_PREFIX . ":" . $prefix . "=\"&" . $prefix . ";\"";
				else 
                    $this->m_out .= RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFSERIALIZER_XML_NAMESPACE_DECL_PREFIX . ":" . $prefix . "=\""  . $namespace . "\"";
            }
        }
    }

	/**
	 * @access   private
	 */
    function writeDescriptions()
	{
        $this->m_groupTypeStatement  = null;
        $this->m_attributeStatements = array();
        $this->m_contentStatements   = array();
		$this->m_currentSubject      = null;

        foreach ( $this->m_statements as $key => $statement ) 
		{
			$subject   = $statement->getSubject();
			$predicate = $statement->getPredicate();
			$object    = $statement->getobject();

            // write Group and update current subject if nessesary
            if ( $this->m_currentSubject == null || !$this->m_currentSubject->equals( $subject ) ) 
			{
                $res = $this->writeGroup();
				
				if ( PEAR::isError( $res ) )
					return $res;
					
                $this->m_currentSubject = $subject;
            }

            // classify the statement
            if ( ( $predicate->getURI() == RDFAPI_NAMESPACE_URI . RDFAPI_TYPE ) && is_a( $object, "Resource" ) )
			{
                $this->m_groupTypeStatement = $statement;
			}
            else if ( $this->canAbbreviateValue( $object ) && $this->use_attributes && $this->checkForDoubleAttributes( $predicate ) )
			{
				if ( is_a( $object, "Literal" ) ) 
				{
					if ( $object->getDatatype() == null ) 
						$this->m_attributeStatements[] = $statement;
					else 
						$this->m_contentStatements[] = $statement;
				} 
				else 
				{
					$this->m_attributeStatements[] = $statement;
				}
			} 
			else 
			{
				$this->m_contentStatements[] = $statement;
			}
		}
		
		return $this->writeGroup();
    }

	/**
	 * @access   private
	 */
	function writeGroup()
	{
        if ( $this->m_currentSubject == null || ( $this->m_groupTypeStatement == null && ( count( $this->m_attributeStatements ) == 0 ) && ( count( $this->m_contentStatements ) == 0 ) ) )
			return;
		
		if ( $this->m_groupTypeStatement != null )
		{
			$outerElementName = $this->getElementText( $this->m_groupTypeStatement->obj->getURI() );
			
			if ( PEAR::isError( $outerElementName ) )
				return $outerElementName;
		}
		else
		{
			$outerElementName = $this->rdf_qname_prefix . RDFAPI_DESCRIPTION;
		}
        
		$this->m_out .= RDFAPI_LINEFEED . "<";
        $this->m_out .= $outerElementName;
        $this->m_out .= " ";

        $this->writeSubjectURI( $this->m_currentSubject );

        // attribute statements
        if ( $this->use_attributes )
		{
             $res = $this->writeAttributeStatements();
			 
			 if ( PEAR::isError( $res ) )
			 	$res;
		}

        if ( count( $this->m_contentStatements ) == 0 )
		{
            $this->m_out .= "/>" . RDFAPI_LINEFEED;
		}
        else
		{
            $this->m_out .= ">" . RDFAPI_LINEFEED;

            // content statements
            $res = $this->writeContentStatements();
			
			if ( PEAR::isError( $res ) )
			 	$res;

            $this->m_out .= "</";
            $this->m_out .= $outerElementName;
            $this->m_out .= '>' . RDFAPI_LINEFEED;
        }
		
        $this->m_groupTypeStatement  = null;
		$this->m_attributeStatements = array();
        $this->m_contentStatements   = array();
	}

	/**
	 * @param  object RdfNode $predicate
	 * @access private
	 */
	function checkForDoubleAttributes( $predicate ) 
	{
		foreach ( $this->m_attributeStatements as $key => $statement ) 
		{
			if ( $statement->pred->equals( $predicate ) )
                return false;
        }
		
        return true;
    }

	/**
	 * @param  STRING $uri
	 * @access private
	 */
	function relativizeURI( $uri ) 
	{
		$uri_namespace = RdfUtil::guessNamespace( $uri );
        
		if ( $uri_namespace == $this->m_baseURI ) 
			return RdfUtil::guessName($uri);
		else 
			return $uri;
    }

	/**
	 * @param object RdfNode $subject_node
	 *
	 * @access   private
	 */
    function writeSubjectURI( $subject_node ) 
	{
		$currentSubjectURI = $subject_node->getURI();
		$relativizedURI    = $this->relativizeURI( $currentSubjectURI );

		// if submitted subject ist a blank node, use rdf:nodeID
		if ( is_a( $this->m_currentSubject, "BlankNode" ) ) 
		{
			$this->m_out .= $this->rdf_qname_prefix . RDFAPI_NODEID;
			$this->m_out .= "=\"";
			$this->m_out .= $relativizedURI;
		} 
		else 
		{
			if ( !( $relativizedURI == $currentSubjectURI ) ) 
			{
				$this->m_out .= $this->rdf_qname_prefix . RDFAPI_ID;
				$this->m_out .= "=\"";
				$this->m_out .= $relativizedURI;
			} 
			else 
			{
				$this->m_out .= $this->rdf_qname_prefix . RDFAPI_ABOUT;
				$this->m_out .= "=\"";
				
				$this->writeAbsoluteResourceReference( $relativizedURI );
			}
		}
		
		$this->m_out .= "\"";
    }

	/**
	 * @access   private
	 */
	function writeAttributeStatements()
	{
		foreach ( $this->m_attributeStatements as $key => $statement ) 
		{
			$this->m_out .= RDFAPI_LINEFEED;
            $this->m_out .= RDFAPI_INDENTATION;
			
            $res = $this->getElementText( $statement->pred->getURI() );
            
			if ( PEAR::isError( $res ) )
				return $res;
			
			$this->m_out .= $res;
			$this->m_out .= "=";
			
            $value = $statement->obj->getLabel();
            $quote = $this->getValueQuoteType( $value );
			
            $this->m_out .= $quote;
            $this->m_out .= RdfUtil::escapeValue( $value );
            $this->m_out .= $quote;
        }
    }

	/**
	 * @access   private
	 */
	function writeContentStatements()
	{
		foreach ( $this->m_contentStatements as $key => $statement ) 
		{
			$this->m_out .= RDFAPI_INDENTATION;
            $this->m_out .= '<';
			
            $predicateElementText = $this->getElementText( $statement->pred->getURI() );
			
			if ( PEAR::isError( $predicateElementText ) )
				return $predicateElementText;
				
            $this->m_out .= $predicateElementText;

            if ( is_a( $statement->obj, "Resource" ) ) 
			{
                $this->writeResourceReference( $statement->obj );
                $this->m_out .= "/>" . RDFAPI_LINEFEED;
            } 
			else 
			{
                if ( is_a( $statement->obj, "Literal" ) ) 
				{
                    if ( $statement->obj->getLanguage() != null )
                        $this->m_out .= " " . RDFSERIALIZER_XML_NAMESPACE_PREFIX . ":" . RDFAPI_XML_LANG . "=\"" . $statement->obj->getLanguage() . "\"";
						
                    if ( $statement->obj->getDatatype() != null )
                        $this->m_out .= " " . RDFSERIALIZER_NAMESPACE_PREFIX . ":" . RDFAPI_DATATYPE . "=\"" . $statement->obj->getDatatype() . "\"";
                }
				
                $this->m_out .= '>';
                $this->writeTextValue( $statement->obj->getLabel() );
                $this->m_out .= "</";
                $this->m_out .= $predicateElementText;
                $this->m_out .= '>' . RDFAPI_LINEFEED;
            }
        }
    }

	/**
	 * @param Object $object_node
	 * @access   private
	 */
	function writeResourceReference( $object_node )
	{
		$rebaseURI = $object_node->getURI();
		$this->m_out .= ' ';
		
		if ( is_a( $object_node, "BlankNode" ) )
			$this->m_out .= $this->rdf_qname_prefix . RDFAPI_NODEID;
  		else
			$this->m_out .= $this->rdf_qname_prefix . RDFAPI_RESOURCE;

		$this->m_out .= "=\"";
		$relativizedURI = $this->relativizeURI( $rebaseURI );
		
		if ( !( $relativizedURI == $rebaseURI ) )
		{
			if ( !is_a( $object_node, "BlankNode" ) )
				$this->m_out .= "#" . $relativizedURI;
			else
				$this->m_out .=  $relativizedURI;
		}
		else
		{
			$this->writeAbsoluteResourceReference( $rebaseURI );
		}
		
		$this->m_out .= "\"";
    }

	/**
	 * @param String $rebaseURI
	 * @access   private
	 */
	function writeAbsoluteResourceReference( $rebaseURI ) 
	{
		$namespace = RdfUtil::guessNamespace( $rebaseURI );
		$localName = RdfUtil::guessName( $rebaseURI );
		$text      = $rebaseURI;
		
        if ( $namespace!="" && $this->use_entities ) 
		{
			$prefix = array_search( $namespace, $this->m_namespaces );
			$text   = "&" . $prefix . ";" . $localName;
        } 
		else 
		{
			$text   = RdfUtil::escapeValue( $text );
		}
		
        $this->m_out .= $text;
    }

	/**
	 * @param STRING $textValue
	 * @access   private
	 */
	function writeTextValue( $textValue ) 
	{
        if ( $this->getValueQuoteType( $textValue ) == RDFSERIALIZER_USE_CDATA )
            $this->writeEscapedCDATA( $textValue );
        else
            $this->m_out .= RdfUtil::escapeValue( $textValue );
    }

	/**
	 * @param STRING $textValue
	 * @access   private
	 */
	function writeEscapedCDATA( $textValue ) 
	{
		$this->m_out .= "<![CDATA[" . $textValue . "]]>";
	}

	/**
	 * @param STRING $textValue
	 * @access   private
	 */
	function getValueQuoteType( $textValue ) 
	{
		$quote = RDFSERIALIZER_USE_ANY_QUOTE;
        $hasBreaks = false;
        $whiteSpaceOnly = true;
		
        for ( $i = 0; $i < strlen( $textValue ); $i++ ) 
		{
			$c = $textValue{$i};
			
            if ( $c == RDFAPI_LINEFEED )
                $hasBreaks = true;
				
            if ( $c == "\"" || $c == "\'" ) 
			{
                if ( $quote == RDFSERIALIZER_USE_ANY_QUOTE )
                    $quote = ( $c == "\"" )? "\'" : "\"";
                else if ( $c == $quote )
                    return RDFSERIALIZER_USE_CDATA;
            }
			
            if ( !( $c == " " ) )
                $whiteSpaceOnly = false;
        }
		
        if ( $whiteSpaceOnly || $hasBreaks )
            return RDFSERIALIZER_USE_CDATA;
			
        return ( $quote == RDFSERIALIZER_USE_ANY_QUOTE )? '"' : $quote;
    }

	/**
	 * @param  object RdfNode $node
	 * @access private
	 */
	function canAbbreviateValue( $node ) 
	{
        if ( is_a( $node, "Literal" ) ) 
		{
            $value = $node->getLabel();
            
			if ( strlen( $value ) < RDFSERIALIZER_MAX_ALLOWED_ABBREV_LENGTH ) 
			{
                $c = $this->getValueQuoteType( $value );
                return $c == '"' || $c == '\'';
            }
        }
		
        return false;
    }
	
	/**
	 * @param  STRING $elementName
	 * @access private
	 */
	function getElementText( $elementName )
	{
		$namespace = RdfUtil::guessNamespace( $elementName );
		$localName = RdfUtil::guessName( $elementName );
		
        if ( $namespace == "" )
            return $localName;
			
        $prefix = array_search( $namespace, $this->m_namespaces );

        if ( $prefix === false )
			return PEAR::raiseError( "Prefix for element '" . $elementName . "' cannot be found." );
		
        if ( $prefix != RDFSERIALIZER_NAMESPACE_PREFIX )
             return $prefix . ":" . $localName;
        else
             return $this->rdf_qname_prefix . $localName;
    }

	/**
	 * @param  object RdfMemoryModel $model
	 * @access private
	 */
	function collectNamespaces( $model )
	{
		foreach ( $model->triples as $key => $value )
		{
			if ( $this->use_entities ) 
			{
				$this->collectNamespace( $value->getSubject() );
				
				if ( !is_a( $value->getObject(), "Literal" ) )
					$this->collectNamespace( $value->getObject() );
			} 
			else 
			{
				if ( $value->pred->getURI() == RDFAPI_NAMESPACE_URI . RDFAPI_TYPE )
				{
					$this->collectNamespace( $value->getObject() );
				}
                else if ( ( $value->pred->getURI() == RDFAPI_NAMESPACE_URI . RDFSERIALIZER_SUBCLASSOF ) || ( $value->pred->getURI() == RDFAPI_NAMESPACE_URI . RDFSERIALIZER_SUBPROPERTYOF ) ) 
				{
					$this->collectNamespace( $value->getSubject() );
                    $this->collectNamespace( $value->getObject() );
                }
            }

			$this->collectNamespace( $value->getPredicate() );
		}
	}

	/**
	 * @param object Resource $resource
	 * @access   private
	 */
	function collectNamespace( $resource )
	{
		$namespace = RdfUtil::getNamespace( $resource );
		
		if ( !in_array( $namespace, $this->m_namespaces ) ) 
		{
			$prefix = array_search( $namespace, $this->m_defaultNamespaces );
			
			if ( $prefix === false )
                $prefix = $this->getNextNamespacePrefix();
            
			$this->m_namespaces[$prefix] = $namespace;
        }
    }

	/**
	 * @access   private
	 */
	function getNextNamespacePrefix()
	{
		$this->m_nextAutomaticPrefixIndex++;
		return RDFSERIALIZER_GENERAL_PREFIX_BASE . $this->m_nextAutomaticPrefixIndex;
    }
} // END OF RdfSerializer

?>
