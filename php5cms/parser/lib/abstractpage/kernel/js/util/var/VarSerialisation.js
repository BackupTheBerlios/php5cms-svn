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
 * @package util_var
 */
 
/**
 * Constructor
 *
 * @access public
 */
VarSerialisation = function()
{
	this.Base = Base;
	this.Base();
};


VarSerialisation.prototype = new Base();
VarSerialisation.prototype.constructor = VarSerialisation;
VarSerialisation.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
VarSerialisation.VAR_UNSERIALISE_I = 0;

/**
 * This is a dummy fn to get the copy of the value then pass that copy by 
 * reference to VarSerialisation._serialize() fn that may alter the var with escaping.
 *
 * @access public
 * @static
 */
VarSerialisation.serialize = function( value ) 
{
	return VarSerialisation._serialize( value );
};

/**
 * This is a dummy fn to get the copy of the var then pass that copy by 
 * reference to _unserialize() fn that may alter the var with escaping.
 *
 * @access public
 * @static
 */
VarSerialisation.unserialize = function( str ) 
{
	var lines_str = str.replace( /\r\n/g, '\n' );
	lines_str = lines_str.replace( /\r/g, '\n' );
	
	// if the last char is a new line remove it
	if ( lines_str.charAt( lines_str.length - 1 ) == "\n" )
		lines_str = lines_str.substr( 0, lines_str.length - 1 );
	
	var lines = lines_str.split( "\n" );
	VarSerialisation.VAR_UNSERIALISE_I = 0;
	var results = VarSerialisation._unserialize( lines );
	
	return results[0];
};

/**
 * @access public
 * @static
 */
VarSerialisation.gettype = function( value ) 
{
	if ( value == null ) 
		return 'NULL';
	
	var type = typeof( value );

	switch ( type ) 
	{
		case "number" :
			var str_value = value.toString();
			
			//this is an double
			if ( str_value.indexOf( "." ) >= 0 ) 
				type = "double";
			else
				type = "integer";
			
			break;

		case "object" :
			type = "array";
			break;
	}

	return type;
};

/**
 * @access public
 * @static
 */
VarSerialisation.settype = function( value, type ) 
{
	var val = null;

	switch( type ) 
	{
		case "integer" :
			val = parseInt( value );
			break;
		
		case "double" :
			val = parseFloat( value );
			break;

		case "boolean" :
			val = value? true : false;
			break;

		case "string" :
			val = value;
			
			// if this is a string then we need to reverse the escaping process
			val = val.replace( /<cr>/g, "\r" );
			val = val.replace( /<lf>/g, "\n" );
			val = val.replace( /~g~/g,  '>'  );
			val = val.replace( /~l~/g,  '<'  );
			val = val.replace( /~~/g,   '~'  );
		
			break;

		default : 
			val = value;
	}
	
	return val;
};


// private methods

/**
 * The fn that actually does the unserialising
 * returns an arrey with the value and the name of the variable.
 *
 * @access private
 * @static
 */
VarSerialisation._unserialize = function( lines, indent ) 
{
	if ( indent == null ) 
		indent = '';

	var str = lines[VarSerialisation.VAR_UNSERIALISE_I];

	// if it's blank then return null
	if ( str == "" ) 
		return Array( null, null );

	var name_type = "";
	var name      = null;
	var re        = new RegExp( '^' + indent + '<name_type>(.*)<\/name_type><name>(.*)<\/name>(.*)$' );
	var matches   = re.exec(str);
	
	if ( matches != null ) 
	{
		name_type = matches[1];
		name      = VarSerialisation._settype( matches[2], name_type );
		str       = matches[3];
	}

	// OK so it's an array
	if ( str == '<val_type>array</val_type>' ) 
	{
		var indent_len = indent.length;
		VarSerialisation.VAR_UNSERIALISE_I++;
		var val = new Array();
		
		// just incase some bastard has set up some prototype vars, nullify them
		// then at least we can test for them
		for ( var key in val ) 
			val[key] = null;
		
		// while the indent is still the same unserialise our contents
		while ( lines[VarSerialisation.VAR_UNSERIALISE_I] != null && indent + ' ' == lines[VarSerialisation.VAR_UNSERIALISE_I].substr( 0, indent_len + 1 ) ) 
		{
			var results = VarSerialisation._unserialize( lines, indent + ' ' );
			val[results[1]] = results[0];
			VarSerialisation.VAR_UNSERIALISE_I++;
		}
		
		VarSerialisation.VAR_UNSERIALISE_I--;
		return new Array( val, name );
	}

	val_type = "";
	val      = null;
	re       = new RegExp( '^<val_type>(.*)<\/val_type><val>(.*)<\/val>$' );
	matches  = re.exec( str );
	
	if ( matches != null ) 
	{
		val_type = matches[1];
		val = VarSerialisation._settype( matches[2], val_type );
	}

	return new Array( val, name );
};

/**
 * @access private
 * @static
 */
VarSerialisation._serialize = function( value, name, indent ) 
{
	if ( indent == null ) 
		indent = '';

	var str  = "";
	var type = VarSerialisation._gettype( value );

	switch ( type ) 
	{
		// normal vars
		case "string" :
			value = value.replace( /~/g,  '~~'   );
			value = value.replace( /</g,  '~l~'  );
			value = value.replace( />/g,  '~g~'  );
			value = value.replace( /\n/g, '<lf>' );
			value = value.replace( /\r/g, '<cr>' );

		case "integer":

		case "double" :
			if ( name != null )
				str += indent + '<name_type>' + VarSerialisation._gettype( name ) + '</name_type><name>' + name + '</name>';
			
			str += '<val_type>' + type + '</val_type><val>' + value + '</val>\n';
			break;

		case "boolean":
			if ( name != null )
				str += indent + '<name_type>' + VarSerialisation._gettype( name ) + '</name_type><name>' + name + '</name>';
			
			str += '<val_type>' + type + '</val_type><val>' + ( ( value )? 1 : 0 ) + '</val>\n';
			break;

		// recursive vars
		case "array":
			if ( name != null )
				str += indent + '<name_type>' + VarSerialisation._gettype( name ) + '</name_type><name>' + name + '</name>';
			
			str += '<val_type>' + type + '</val_type>\n';
			
			for ( var k in value )
				str += VarSerialisation._serialize( value[k], k, indent + ' ' );

			break;

		case "NULL" :
			if ( name != null )
				str += indent + '<name_type>' + VarSerialisation._gettype( name ) + '</name_type><name>' + name + '</name>';
			
			str += '\n';
			break;
	}
	
	return str;
};
