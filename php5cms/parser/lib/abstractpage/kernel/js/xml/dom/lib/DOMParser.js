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


/**
 * @package xml_dom_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
DOMParser = function()
{
	this.doc = new Document();
	this.xml = null;

	this.openedTags   = new Array();
	this.contextNodes = new Array( this.doc );
};


/**
 * @access public
 */
DOMParser.prototype.parse = function( xml )
{
	this.xml = xml.trim();

	this.processProlog();
	this.processRootElement();
	this.processMisc();

	if ( this.xml.length != 0 )
		return Base.raiseError( "Illegal construct in XML document." );
};

/**
 * @access public
 */
DOMParser.prototype.processProlog = function()
{
	this.processXmlDecl();
	this.processMisc();
	this.processDoctypeDecl();
	this.processMisc();
};

/**
 * @access public
 */
DOMParser.prototype.processRootElement = function()
{
	var matches;
  
	if ( matches = this.xml.match( RegExp.$STag ) )
	{
		this.processSTag( matches );
		this.xml = this.xml.substring( matches[0].length );
		this.processContent();
	}
	else if ( matches = this.xml.match( RegExp.$EmptyElemTag ) )
	{
		this.processEmptyElemTag( matches );
		this.xml = this.xml.substring( matches[0].length );
	}
	else
	{
		return Base.raiseError( "Root element not found." );
	}
};

/**
 * @access public
 */
DOMParser.prototype.processMisc = function()
{
	var matches;
  
	while ( RegExp.$Misc.test( this.xml ) )
	{
		// white space
		if ( matches = this.xml.match( RegExp.$S ) )
		{
			;
		}
		// comment
		else if ( matches = this.xml.match( RegExp.$Comment ) )
		{
			var comment = this.doc.createComment( matches[1] );
 			this.doc.appendChild( comment );
		}
		// processing instruction
		else if ( matches = this.xml.match( RegExp.$PI ) )
		{
			var target = matches[1];
			var data = matches[3];
			var pi = this.doc.createProcessingInstruction( target, data );
      
			this.doc.appendChild( pi );
		}
		// unknown construct
		else
		{
			return Base.raiseError( "Illegal construct in XML document." );
		}
    
		this.xml = this.xml.substring( matches[0].length );
	}
};

/**
 * @access public
 */
DOMParser.prototype.processContent = function()
{
	var matches;

	while ( this.openedTags.length != 0  )
	{
		// start tag
		if ( matches = this.xml.match( RegExp.$STag ) )
		{
			this.processSTag( matches );
		}
		// end tag
		else if ( matches = this.xml.match( RegExp.$ETag ) )
		{
			this.processETag( matches );
		}
		// empty element
		else if ( matches = this.xml.match( RegExp.$EmptyElemTag ) )
		{
			this.processEmptyElemTag( matches );
		}
		// character data
		else if ( matches = this.xml.match( RegExp.$CharData ) )
		{
			this.processCharData( matches );
		}
		// entity reference
		else if ( matches = this.xml.match( RegExp.$Reference ) )
		{
			this.processReference( matches );
		}
		// CDATA section
		else if ( matches = this.xml.match( RegExp.$CDSect ) )
		{
			this.processCDSect( matches );
		}
		// processing instruction
		else if ( matches = this.xml.match( RegExp.$PI ) )
		{
			this.processPI( matches );
		}
		// comment
		else if ( matches = this.xml.match( RegExp.$Comment ) )
		{
			this.processComment( matches );
		}
		// unknown construct
		else
		{
			return Base.raiseError( "Illegal construct in XML document." );
		}

		this.xml = this.xml.substring( matches[0].length );
	}
};

/**
 * @access public
 */
DOMParser.prototype.processAttributes = function( attString )
{
	var matches;

	while ( matches = attString.match( RegExp.$Attribute ) )
	{
		var name  = matches[1];
		var value = matches[2].removeQuotes();
		var attr  = this.doc.createAttribute( name );
		var currentContext = this.contextNodes.lastItem();

		attr.setValue( value );
    
		attString = attString.substring( matches[0].length ).trim();
		currentContext.attributes.setNamedItem( attr );
	}
};

/**
 * @access public
 */
DOMParser.prototype.processXmlDecl = function()
{
	var matches;

	if ( matches = this.xml.match( RegExp.$XMLDecl ) )
		this.xml = this.xml.substring( matches[0].length );
};

/**
 * @access public
 */
DOMParser.prototype.processDoctypeDecl = function()
{
	var matches;
  
	if ( matches = this.xml.match( RegExp.$doctypedecl ) )
	{
		var name = matches[1];
		var doctype = new DocumentType( this.doc, name );

		this.doc.appendChild( doctype );
		this.doc.doctype = doctype;
    
		this.xml = this.xml.substring( matches[0].length );
	}
};

/**
 * @access public
 */
DOMParser.prototype.processSTag = function( matches )
{
	var element = this.doc.createElement( matches[1] );
	var attString = matches[2];
  
	this.openedTags.push( matches[1] );
	this.addNode( element );
  
	if ( attString )
		this.processAttributes( attString.trim() );
};

/**
 * @access public
 */
DOMParser.prototype.processETag = function( matches )
{
	this.contextNodes.pop();
  
	if ( this.openedTags.pop() != matches[1] )
		return Base.raiseError( "End tag does not match opening tag." );
};

/**
 * @access public
 */
DOMParser.prototype.processEmptyElemTag = function( matches )
{
	var element = this.doc.createElement( matches[1] );
	var attString = matches[2];
  
	this.addNode( element );
  
	if ( attString )
		this.processAttributes( attString.trim() );
  
	this.contextNodes.pop();
};

/**
 * @access public
 */
DOMParser.prototype.processCharData = function( matches )
{
	if ( matches[0].trim() != "" )
	{
		var text = this.doc.createTextNode( matches[0] );
		this.addNode( text );
	}
};

/**
 * @access public
 */
DOMParser.prototype.processReference = function( matches )
{
	var reference = this.doc.createEntityReference( matches[0] );
	this.addNode( reference );
};

/**
 * @access public
 */
DOMParser.prototype.processCDSect = function( matches )
{
	var cdsect = this.doc.createCDATASection( matches[1] );
	this.addNode( cdsect );
};

/**
 * @access public
 */
DOMParser.prototype.processPI = function( matches )
{
	var target = matches[1];
	var data = matches[3];
	var pi = this.doc.createProcessingInstruction( target, data );
	
	this.addNode( pi );
};

/**
 * @access public
 */
DOMParser.prototype.processComment = function( matches )
{
	var comment = this.doc.createComment( matches[1] );
	this.addNode( comment );
};

/**
 * @access public
 */
DOMParser.prototype.addNode = function( node )
{
	var currentContext = this.contextNodes.lastItem();
	currentContext.appendChild( node );
  
	if ( node.nodeType == Node.ELEMENT_NODE )
	{
		var contextNode = currentContext.childNodes.item( -1 );
		this.contextNodes.push( contextNode );
    
		if ( !this.doc.documentElement )
			this.doc.documentElement = this.contextNodes.lastItem();
	}
};


/**
 * @access public
 * @static
 */
DOMParser.VERSION = 1.0;


// Productions

// Character Range
String.$Char          = "(?: \\u0009 | \\u000A | \\u000D | " +
                        "[\\u0020-\\uD7FF] | [\\uE000-\\uFFFD] | " +
                        "[\\u10000-\\u10FFFF] )";

// White Space
String.$S             = "(?: (?: \\u0020 | \\u0009 | \\u000D | \\u000A )+ )";

// Names and matches
String.$NameChar      = "(?: $Letter | $Digit | \\. | \\- | _ | : | " +
                        "$CombiningChar | $Extender )";
String.$Name          = "(?: $Letter | _ | : ) $NameChar*";

// Literals
String.$AttValue      = "(?: \" (?: [^<&\"] | $Reference )* \" ) | " + 
                        "(?: ' (?: [^<&'] | $Reference )* ' )";
String.$SystemLiteral = "(?: (?: \" [^\"]* \") | (?: ' [^']* '))";
String.$PubidLiteral  = "(?: (?: \" $PubidChar* \") | " +
                        "(?: ' (?: (?!')$PubidChar)* '))";
String.$PubidChar     = "(?: \\u0020 | \\u000D | \\u000A | [a-zA-Z0-9] | " +
                        "[-'()+,./:=?;!*#@$_%])";

// Character Data
// String.$CharData   = "(?![^<&]*]]>[^<&]*)[^<&]*";
String.$CharData      = "[^<&]+";

// Comments
String.$Comment       = "<!-- ( (?: (?: (?!- ) $Char ) | " +
                        "(?: - (?: (?!- ) $Char ) ) )* ) -->";

// Processing Instructions
String.$PI            = "<\\? ( $PITarget ) ( $S $Char*? )? \\?>";
String.$PITarget      = "(?: (?: \\b( $Letter | _ | : ) " +
                        "(?: $NameChar ){0,1}\\b ) | " +
                        "(?: (?! [Xx][Mm][Ll] ) (?: $Letter | _ | : ) " +
                        "(?: $NameChar ){2} | (?: $Letter | _ | : ) " +
                        "(?: $NameChar ){3,} ) )";

// CDATA Sections
String.$CDSect        = "<!\\[CDATA\\[ ( $Char*? ) ]]>";

// Prolog
String.$prolog        = "(?: $XMLDecl? $Misc* (?: $doctypedecl $Misc* )? )";
String.$XMLDecl       = "<\\?xml $VersionInfo $EncodingDecl? " +
                        "$SDDecl? $S? \\?>";
String.$VersionInfo   = "(?: $S version $Eq ( ' $VersionNum ' | " +
                        "\" $VersionNum \" ) )";
String.$Eq            = "(?: $S? = $S? )";
String.$VersionNum    = "(?: (?: [a-zA-Z0-9_.:] | - )+ )";
String.$Misc          = "(?: $Comment | $PI | $S )";

// Document Type Definition
String.$doctypedecl   = "<!DOCTYPE $S ( $Name ) (?: $S $ExternalID)? $S? " +
                        "(?: \\[ [^]]* \\] )? $S? >";

// Standalone Document Declaration
String.$SDDecl        = "(?: $S standalone $Eq ( (?: \"(?: yes|no )\" ) | " +
                        "(?: '(?: yes|no)' ) ) )";

// Start-tag
String.$STag          = "< ( $Name ) ( (?: $S $Attribute )* ) $S? >";
String.$Attribute     = "( $Name ) $Eq ( $AttValue )";

// End-tag
String.$ETag          = "</ ( $Name ) $S? >";

// Tags for Empty Elements
String.$EmptyElemTag  = "< ( $Name ) ( (?: $S $Attribute )* ) $S? />";

// Character Reference
String.$CharRef       = "(?: &#[0-9]+; | &#x[0-9a-fA-F]+; )";

// Entity Reference
String.$Reference     = "(?: $EntityRef | $CharRef )";
String.$EntityRef     = "& $Name ;";

// External Entity Declaration
String.$ExternalID    = "(?: (?: SYSTEM $S ( $SystemLiteral ) ) | " +
                        "(?: PUBLIC $S ( $PubidLiteral ) $S " +
                        "( $SystemLiteral ) ) )";

// Encoding Declaration
String.$EncodingDecl  = "(?: $S encoding $Eq ( ' $EncName ' | " +
                        "\" $EncName \" ) )";
String.$EncName       = "[A-Za-z](?: [A-Za-z0-9._]|- )*";

// Characters
String.$Letter        = "(?: $BaseChar | $Ideographic )";

// if I18N is included
if ( Boolean.$I18N != true ) 
{
	String.$BaseChar      = "(?: [\\u0041-\\u005A] | [\\u0061-\\u007A] )";
	String.$Ideographic   = "\\u0000";
	String.$CombiningChar = "\\u0000";
	String.$Digit         = "[\\u0030-\\u0039]";
	String.$Extender      = "\\u0000";
}

  
/* Compile regular expressions */
RegExp.$prolog       = new RegExp( "^" + String.$prolog.resolve()       );
RegExp.$XMLDecl      = new RegExp( "^" + String.$XMLDecl.resolve()      );
RegExp.$doctypedecl  = new RegExp( "^" + String.$doctypedecl.resolve()  );
RegExp.$STag         = new RegExp( "^" + String.$STag.resolve()         );
RegExp.$ETag         = new RegExp( "^" + String.$ETag.resolve()         );
RegExp.$EmptyElemTag = new RegExp( "^" + String.$EmptyElemTag.resolve() );
RegExp.$Attribute    = new RegExp( "^" + String.$Attribute.resolve()    );
RegExp.$CDSect       = new RegExp( "^" + String.$CDSect.resolve()       );
RegExp.$CharData     = new RegExp( "^" + String.$CharData.resolve()     );
RegExp.$Reference    = new RegExp( "^" + String.$Reference.resolve()    );
RegExp.$PI           = new RegExp( "^" + String.$PI.resolve()           );
RegExp.$Comment      = new RegExp( "^" + String.$Comment.resolve()      );
RegExp.$Misc         = new RegExp( "^" + String.$Misc.resolve()         );
RegExp.$S            = new RegExp( "^" + String.$S.resolve()            );
