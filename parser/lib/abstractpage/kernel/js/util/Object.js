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
 * @package util
 */
 
/**
 * @access public
 * @static
 */
Object.dump = function( obj, linebreak )
{
	var out = "";
	
	for ( prop in obj )
		out += prop + ": " + obj[prop] + ( linebreak || "\n" );
	
	return out;
};

/**
 * @access public
 * @static
 */
Object.extract = function( obj, prefix )
{
	if ( obj == null )
		return false;
	
	var value;
	
	for ( prop in obj )
	{
		switch ( typeof( obj[prop] ) )
		{
			case "string":
				value = "'" + obj[prop] + "'";
				break;
		
			default:
				value = obj[prop];
		}
		
		eval( prop + " = " + ( prefix || "" ) + value + ";" );
	}
};

/**
 * @access public
 * @static
 */
Object.clone = function( obj )
{
	return new Object( obj );
};

/**
 * @access public
 * @static
 */
Object.toArray = function( obj )
{
	var propVal;
	var ret = new Array();	
	
	for ( prop in obj )
		ret[ret.length] = new Array( prop, obj[prop] );

	return ret;
};

/**
 * @access public
 * @static
 */
Object.dump = function( o )
{
	// argument is not an object
	if ( o == null || ( typeof o != "object" ) )
		return Base.raiseError( "Non-object argument." );

	var ret = "<ul>";
	
	var prop;
	var propVal;
	var type;
	var str;
	
	// dump contents using an unordered list
	for ( prop in o )
	{
		propVal = o[prop];
		type = typeof propVal;

		// print the property name and its value
 		ret += "<li><b>" + prop + "</b> = ";

		if ( propVal != null && ( type == "object" ) )
		{
  			// element is an object
   			ret += "<i>Object</i>";

			// output string representation of object
  			if ( propVal.toString )
			{
				str = propVal.toString();
 
 				if ( str != null )
					ret += ": " + str.fixed();
  			}
 
 			// Do object dump on property unless property is a reference to object -
 			// prevents infinite recursion (in Netscape).
  			if ( propVal != o )
				Object.dump( propVal );
		} 
		else
		{
 			// non-object element
   			if ( type == "string" )
				ret += propVal.fixed();
			else
				ret += "<tt>" + propVal + "</tt>";
		}

		ret += "</li>";
	}

	ret += "</ul>";
	return ret;
};

/**
 * @access public
 * @static
 */
Object.toCSV = function()
{
	// TODO
};

/**
 * @access public
 * @static
 */
Object.toXML = function()
{
	// TODO
};
