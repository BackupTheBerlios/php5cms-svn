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
 * @package peer_wddx
 */
 
/**
 * Constructor
 *
 * @access public
 */
WDDXSerializer = function()
{
	this.Base = Base;
	this.Base();
	
	// compatibility section
	if ( navigator.appVersion != "" && navigator.appVersion.indexOf( "MSIE 3." ) == -1 )
	{
    	// encoding table    
        var et = new Array();
    
    	// Numbers to characters table and 
    	// characters to numbers table.
        var n2c = new Array();
        var c2n = new Array();
    
        for ( var i = 0; i < 256; ++i )
        {
        	// Build a character from octal code.
        	var d1 = Math.floor( i / 64 );
        	var d2 = Math.floor( ( i % 64 ) / 8 );
        	var d3 = i % 8;
        	var c  = eval( "\"\\" + d1.toString( 10 ) + d2.toString( 10 ) + d3.toString( 10 ) + "\"" );
    
    		// Modify character-code conversion tables.
        	n2c[i] = c;
            c2n[c] = i; 
            
    		// Modify encoding table.
    		if ( i < 32 && i != 9 && i != 10 && i != 13 )
            {
            	// Control characters that are not tabs, newlines, and carriage returns.
                
            	// Create a two-character hex code representation.
            	var hex = i.toString( 16 );
				
                if ( hex.length == 1 )
					hex = "0" + hex;
                
    	    	et[n2c[i]] = "<char code='" + hex + "'/>";
            }
            else if ( i < 128 )
            {
            	// Low characters that are not special control characters.
    	    	et[n2c[i]] = n2c[i];
            }
            else
            {
            	// High characters.
    	    	et[n2c[i]] = "&#x" + i.toString( 16 ) + ";";
            }
        }    
    
    	// Special escapes.
        et["<"] = "&lt;";
        et[">"] = "&gt;";
        et["&"] = "&amp;";
        
    	// Store tables.
        this.n2c = n2c;
        this.c2n = c2n;
        this.et  = et;    
        
   		// The browser is not MSIE 3.x.
		this.serializeString = function( s )
		{
			this.write( "<string>" );
			
			for ( var i = 0; i < s.length; ++i )
    			this.write( this.et[s.charAt(i)] );
    
			this.write( "</string>" );
		}
	}
	else
	{
		// The browser is most likely MSIE 3.x, it is NS 2.0 compatible.
		this.serializeString = function( s )
		{
			this.write( "<string><![CDATA[" );
			pos = s.indexOf( "]]>" );
	
			if ( pos != -1 )
			{
				startPos = 0;
		
				while ( pos != -1 )
				{
					this.write( s.substring( startPos, pos ) + "]]>]]&gt;<![CDATA[" );
					startPos = pos + 3;
			
					if ( startPos < s.length )
					{
						pos = s.indexOf( "]]>", startPos );
					}
					else
					{
						// Work around bug in indexOf()
						// "" will be returned instead of -1 if startPos > length
						pos = -1;
					}                               
				}
		
				this.write( s.substring( startPos, s.length ) );
			}
			else
			{
				this.write( s );
			}
			
			this.write( "]]></string>" );
		}
	}
    
	// Setup timezone information.
    var tzOffset = ( new Date() ).getTimezoneOffset();

	// Invert timezone offset to convert local time to UTC time.
    if ( tzOffset >= 0 )
    	this.timezoneString = '-';
    else
    	this.timezoneString = '+';
    
    this.timezoneString += Math.floor( Math.abs( tzOffset ) / 60 ) + ":" + ( Math.abs( tzOffset ) % 60 );
    
	// Common properties.
	this.preserveVarCase = false;
    this.useTimezoneInfo = true;

    // Common functions
	this.serialize = wddxSerializer_serialize;
	this.serializeValue = wddxSerializer_serializeValue;
	this.serializeVariable = wddxSerializer_serializeVariable;
	this.write = wddxSerializer_write;
};


WDDXSerializer.prototype = new Base();
WDDXSerializer.prototype.constructor = WDDXSerializer;
WDDXSerializer.superclass = Base.prototype;

/**
 * SerializeValue() serializes any value that can be serialized.
 *
 * @param  boolean
 * @access public
 */
WDDXSerializer.prototype.serializeValue = function( obj )
{
	var bSuccess = true;

	if ( typeof( obj ) == "string" )
	{
		// String value.
		this.serializeString( obj );
	}
	else if ( typeof( obj ) == "number" )
	{
		// Number value.
		this.write( "<number>" + obj + "</number>" );
	}
	else if ( typeof( obj ) == "boolean" )
	{
		// Boolean value.
		this.write( "<boolean value='" + obj + "'/>" );
	}
	else if ( typeof( obj ) == "object" )
	{
		if ( obj == null )
		{
			// Null values become empty strings.
			this.write( "<string></string>" );
		}
		else if ( typeof( obj.wddxSerialize ) == "function" )
		{
			// Object knows how to serialize itself.
			bSuccess = obj.wddxSerialize( this );
		}
		else if ( typeof( obj.join    ) == "function" &&
				  typeof( obj.reverse ) == "function" &&
				  typeof( obj.sort    ) == "function" &&
				  typeof( obj.length  ) == "number" )
		{
			this.write( "<array length='" + obj.length + "'>" );
			
			for ( var i = 0; bSuccess && i < obj.length; ++i )
				bSuccess = this.serializeValue( obj[i] );
			
			this.write( "</array>" );
		}
		else if ( typeof( obj.getTimezoneOffset ) == "function" && typeof( obj.toGMTString ) == "function" )
		{
			// Possible Date.
			this.write(	"<dateTime>" + ( ( obj.getYear() < 100 )? 1900 + obj.getYear() : obj.getYear()) + "-" + ( obj.getMonth() + 1 ) + "-" + obj.getDate() + "T" + obj.getHours() + ":" + obj.getMinutes() + ":" + obj.getSeconds() );
            
			if ( this.useTimezoneInfo )
            	this.write( this.timezoneString );
            
            this.write( "</dateTime>" );
		}
		else
		{
			// Some generic object, treat it as a structure.
			// Use the wddxSerializationType property as a guide as to its type.
			
			if ( typeof( obj.wddxSerializationType ) == 'string' )
				this.write( '<struct type="' + obj.wddxSerializationType + '">' );
			else
				this.write( "<struct>" );
						
			for ( var prop in obj )
			{  
				if ( prop != 'wddxSerializationType' )
				{                          
				    bSuccess = this.serializeVariable( prop, obj[prop] );
					
					if ( !bSuccess )
						break;
				}
			}
			
			this.write( "</struct>" );
		}
	}
	else
	{
		// Error: undefined values or functions.
		bSuccess = false;
	}

	// Successful serialization.
	return bSuccess;
};

/**
 * serializeVariable() serializes a property of a structure.
 *
 * @param  boolean
 * @access public
 */
WDDXSerializer.prototype.serializeVariable = function( name, obj )
{
	var bSuccess = true;
	
	if ( typeof(obj) != "function" )
	{
		this.write( "<var name='" + ( this.preserveVarCase? name : name.toLowerCase() ) + "'>" );
		bSuccess = this.serializeValue( obj );
		this.write( "</var>" );
	}

	return bSuccess;
};

/**
 * write() appends text to the wddxPacket buffer.
 *
 * @access public
 */
WDDXSerializer.prototype.write = function( str )
{
	this.wddxPacket += str;
};

/**
 * serialize() creates a WDDX packet for a given object
 * 
 * @return  mixed
 * @access public
 */
WDDXSerializer.prototype.serialize = function( rootObj )
{
	this.wddxPacket = "";
	this.write( "<wddxPacket version='0.9'><header/><data>" );
	var bSuccess = this.serializeValue( rootObj );
	this.write( "</data></wddxPacket>" );

	if ( bSuccess )
		return this.wddxPacket;
	else
		return null;
};
