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
 * PersistContext Class
 *
 * Persist JSCRIPT Objects to an XML formatted stream
 * that preserves datatypes and structure.
 *
 * @package util_persistence
 */
 
/**
 * Constructor
 *
 * @access public
 */
PersistContext = function()
{
	this.Base = Base;
	this.Base();
	
	this.Init();

	// 0: Type-major
	// 1: Name-major with types
	// 2: Name-major with no types
	this.iStyle = 0;
};


PersistContext.prototype = new Base();
PersistContext.prototype.constructor = PersistContext;
PersistContext.superclass = Base.prototype;

/**
 * @access public
 */
PersistContext.prototype.Init = function()
{
	this.st         = "";
	this.rgstName   = new Array;
	this.mapObjects = new Object;
	this.istName    = 0;
	this.idSerial   = PersistContext.idSerial++;
};

/**
 * @access public
 */
PersistContext.prototype.SetName = function( stName )
{
	this.rgstName[this.istName++] = stName;
};

/**
 * @access public
 */
PersistContext.prototype.StName = function()
{
	return this.rgstName[this.istName - 1];
};

/**
 * @access public
 */
PersistContext.prototype.PopName = function()
{
	this.istName--;
};

/**
 * @access public
 */
PersistContext.prototype.AddLine = function( st )
{
	var ich;
	var ichMax = ( this.istName - 1 ) * 2;

	for ( ich = 0; ich < ichMax; ich++ )
		this.st += " ";
		
	this.st += st + "\r";
};

/**
 * @access public
 */
PersistContext.prototype.StPath = function()
{
	var ist;
	var st = "";

	for ( ist = 0; ist < this.istName; ist++ )
	{
		if ( ist != 0 )
			st += ".";
			
		st += this.rgstName[ist];
	}
	
	return st;		
};

/**
 * @access public
 */
PersistContext.prototype.PersistOpen = function( obj )
{
	var stType = this.StType( obj );
	var st = PersistContext.StBuildParam( PersistContext.rgstObjectOpen[this.iStyle], stType, this.StName() );
	
	this.AddLine( st );
};

/**
 * @access public
 */
PersistContext.prototype.PersistClose = function( obj )
{
	var stType = this.StType( obj );
	var st = PersistContext.StBuildParam( PersistContext.rgstObjectClose[this.iStyle], stType, this.StName() );
	
	this.AddLine( st );
};

/**
 * @access public
 */
PersistContext.prototype.StType = function( obj )
{
	var stType = typeof( obj );

	if ( stType == "object" )
	{
		switch ( obj.constructor )
		{
			case Array:
				return "Array";
		
			case Object:
				return "Object";
		
			case Date:
				return "Date";
		
			case Function:
				return "Function";
		}
		
		stType = obj.constructor.toString();
		stType = stType.substr( 9, stType.indexOf( "(" ) - 9 );
	}
	else
	{
		stType = stType.substr( 0, 1 ).toUpperCase() + stType.substr( 1 );
	}
	
	return stType;
};

/**
 * @access public
 */
PersistContext.prototype.PersistValue = function( stType, value )
{
	this.AddLine( PersistContext.StBuildParam( PersistContext.rgstValue[this.iStyle], stType, this.StName(), value ) );
};

/**
 * @access public
 */
PersistContext.prototype.ParseXML = function( stXML )
{
	this.doc = new ActiveXObject( XMLParser.getControlPrefix() + ".DomDocument" );
	var fParsed = this.doc.loadXML( stXML );
	
	if ( !fParsed )
	{
		var pe   = this.doc.parseError;
		var code = ( pe.errorCode ^ 0x80000000 ) & 0xFFFFFFF;
		
		return Base.raiseError( "Error in line " + pe.line + ": " + pe.reason + "(Error code: " + code.toString( 16 ).toUpperCase() + ")." );
	}

	return this.ObjectNode( this.doc.documentElement );
};

/**
 * @access public
 */
PersistContext.prototype.StNameNode = function( node )
{
	if ( this.iStyle == 0 )
		return node.attributes.getNamedItem( "Name" ).nodeValue;
	
	return node.baseName;
};

/**
 * @access public
 */
PersistContext.prototype.StTypeNode = function( node )
{
	if ( this.iStyle == 0 )
		return node.baseName;

	var stType = node.attributes.getNamedItem( "Type" ).nodeValue;
	
	if ( this.iStyle == 1 )
		return stType;
		
	if ( !stType )
		stType = "Object";
	
	return stType;
};

/**
 * @access public
 */
PersistContext.prototype.StValueNode = function( node )
{
	var stValue;
	
	if ( this.iStyle == 0 )
		stValue = node.attributes.getNamedItem( "Value" ).nodeValue;
	else
		stValue = node.nodeValue;

	switch ( this.StTypeNode( node ) )
	{
		case "Number":
			return parseFloat( stValue );
	
		case "Boolean":
			return stValue == "true";
	}

	return stValue;
};

/**
 * @access public
 */
PersistContext.prototype.ObjectNode = function( node )
{
	var i,obj;
	var nodes = node.childNodes;

	if ( node.attributes.getNamedItem( "Ref" ) )
	{
		obj = this.mapObjects[node.attributes.getNamedItem( "Ref" ).nodeValue];
		return obj;
	}

	for ( i = 0; i < nodes.length; i++ )
	{
		if ( nodes(i).nodeType == 1 )
		{
			if ( !obj )
			{
				obj = eval( "new " + this.StTypeNode( node ) );
				this.SetName( this.StNameNode( node ) );
				this.mapObjects[this.StPath()] = obj;
			}

			var objChild = this.ObjectNode( nodes( i ) );
			obj[this.StNameNode( nodes( i ) )] = objChild;
		}
	}

	if ( obj )
	{
		this.PopName();
		return obj;
	}

	return this.StValueNode( node );
};


/**
 * @access public
 * @static
 */
PersistContext.idSerial = 0;

/**
 * Format for Type, Name, Value
 * @access public
 * @static
 */
PersistContext.rgstValue = new Array(
	"<^1 Name=^2.a Value=^3.a/>",
	"<^2 Type=^1.a>^3</^2>",
	"<^2>^3</^2>"
);

/**
 * Format for Class, Name, [Ref]
 * @access public
 * @static
 */
PersistContext.rgstObjectOpen = new Array(
	"<^1 Name=^2.a>",
	"<^2 Type=^1.a>",
	"<^2>"
);

/**
 * @access public
 * @static
 */
PersistContext.rgstObjectClose = new Array(
	"</^1>",
	"</^2>",
	"</^2>"
);

/**
 * @access public
 * @static
 */
PersistContext.rgstObjectRef = new Array(
	"<^1 Name=^2.a Ref=^3.a/>",
	"<^2 Ref=^3.a/>",
	"<^2 Ref=^3.a/>"
);

/**
 * @access public
 * @static
 */
PersistContext.mapExcludeProps = new Object;
PersistContext.mapExcludeProps.stPersistPath = true;
PersistContext.mapExcludeProps.idSerial = true;

/**
 * @access public
 * @static
 */
PersistContext.ScalarPersist = function( pc )
{
	if ( PersistContext.mapExcludeProps[pc.StName()] )
		return;
	
	pc.PersistValue( pc.StType( this.valueOf() ), this.valueOf() );
};

/**
 * @access public
 * @static
 */
PersistContext.ObjectPersist = function( pc )
{
	var stType = pc.StType( this );

	if ( stType == "Function" )
		return;

	if ( this.idSerial == pc.idSerial )
	{
		var st = PersistContext.StBuildParam( PersistContext.rgstObjectRef[pc.iStyle], stType, pc.StName(), this.stPersistPath );
		pc.AddLine( st );
		
		return;
	}
	
	this.idSerial = pc.idSerial;
	this.stPersistPath = pc.StPath();
	
	pc.PersistOpen( this );
	
	for ( prop in this )
	{
		pc.SetName( prop );
		this[prop].Persist( pc );
		pc.PopName();
	}
	
	pc.PersistClose( this );
};

/**
 * @access public
 * @static
 */
PersistContext.StAttribute = function( stAttr )
{
	return "\"" + stAttr + "\"";
};

/**
 * @access public
 * @static
 */
PersistContext.StAttrQuote = function( st )
{
	return '"' + PersistContext.StAttrQuoteInner( st ) + '"';
};

/**
 * @access public
 * @static
 */
PersistContext.StAttrQuoteInner = function( st )
{
	st = st.toString();
	st = st.replace(/&/g, '&amp;');
	st = st.replace(/\"/g, '&quot;');
	st = st.replace(/\r/g, '&#13;');
	
	return st;
};

/**
 * @access public
 * @static
 */
PersistContext.StBuildParam = function( stPattern )
{
	var re, i;
	
	for ( i = 1; i < PersistContext.StBuildParam.arguments.length; i++ )
	{
		re = new RegExp( "\\^" + i + "\.a", "g" );
		stPattern = stPattern.replace( re, PersistContext.StAttrQuote( PersistContext.StBuildParam.arguments[i] ) );
		
		re = new RegExp( "\\^" + i, "g" );
		stPattern = stPattern.replace( re, PersistContext.StBuildParam.arguments[i] );
	}
	
	return stPattern;
};


Number.prototype.Persist  = PersistContext.ScalarPersist;
String.prototype.Persist  = PersistContext.ScalarPersist;
Boolean.prototype.Persist = PersistContext.ScalarPersist;
Object.prototype.Persist  = PersistContext.ObjectPersist;
