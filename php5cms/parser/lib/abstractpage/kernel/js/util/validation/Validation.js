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
 * @package util_validation
 */
 
/**
 * Constructor
 *
 * @access public
 */
Validation = function()
{
	this.Base = Base;
	this.Base();
};


Validation.prototype = new Base();
Validation.prototype.constructor = Validation;
Validation.superclass = Base.prototype;

/**
 * @access public
 */
Validation.prototype.isEmpty = function( val )
{
    return ( val == null ) || ( val.length == 0 );
};

/**
 * @access public
 */
Validation.prototype.isBlank = function( val )
{
	if ( val == null )
		return true;
		
	for ( var i = 0; i < val.length; i++ )
	{
		if ( ( val.charAt( i ) != ' ' ) && ( val.charAt( i ) != "\t" ) && ( val.charAt( i ) != "\n" ) )
			return false;
	}
	
	return true;
};

/**
 * Returns true if string s is empty or whitespace characters only.
 *
 * @return boolean
 * @access public
 */
Validation.prototype.isWhitespace = function( s )
{
	var i;

    // Is s empty?
	if ( this.isEmpty( s ) )
		return true;

    // Search through string's characters one by one
    // until we find a non-whitespace character.
    // When we do, return false; if we don't, return true.
    for ( i = 0; i < s.length; i++ )
    {   
		// Check that current character isn't whitespace.
		var c = s.charAt( i );

		if ( Validation.whitespace.indexOf( c ) == -1 )
			return false;
    }

    // All characters are whitespace.
    return true;
};

/**
 * @access public
 */
Validation.prototype.isInteger = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
		
	if ( Math.round( val ) == val )
		return true;
	else
		return false;
};

/**
 * @access public
 */
Validation.prototype.isSignedInteger = function( val )
{
	// skip leading + or -
	if ( ( val.charAt( 0 ) == "-" ) || ( val.charAt( 0 ) == "+" ) )
		startPos = 1;
		
	return ( this.isInteger( val.substring( startPos, val.length ) ) );
};

/**
 * @access public
 */
Validation.prototype.isPositiveInteger = function( val )
{
	return ( this.isSignedInteger( val ) && parseInt( val ) > 0 );
};

/**
 * @access public
 */
Validation.prototype.isNonPositiveInteger = function( val )
{
	return ( this.isSignedInteger( val ) && parseInt( val ) <= 0 );
};

/**
 * @access public
 */
Validation.prototype.isNegativeInteger = function( val )
{
	return ( this.isSignedInteger( val ) && parseInt( val ) < 0 );
};

/**
 * @access public
 */
Validation.prototype.isNonNegativeInteger = function( val )
{
	return ( this.isSignedInteger( val ) && parseInt( val ) >= 0 );
};

/**
 * @access public
 */
Validation.prototype.isFloat = function( val, seperator )
{
	var tmp;
	var sep = seperator || ".";
	
	if ( this.isEmpty( val ) )
		return false;
		
	if ( val.indexOf( sep ) != -1 )
	{
		tmp = val.split( sep );

		if ( this.isInteger( tmp[0] ) && this.isInteger( tmp[1] ) )
			return true;
		else
			return false;
	}
	else
	{
		return false;
	}
};

/**
 * @access public
 */
Validation.prototype.isSignedFloat = function( val )
{
	// skip leading + or -
	if ( ( val.charAt( 0 ) == "-" ) || ( val.charAt( 0 ) == "+" ) )
		startPos = 1;
		
	return ( this.isFloat( val.substring( startPos, val.length ) ) );
};

/**
 * @access public
 */
Validation.prototype.isDigit = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
	
	var anum = /(^\d+$)|(^\d+\.\d+$)/

	if ( anum.test( val.trim() ) )
		return true;
	else
		return false;
};

/**
 * @access public
 */
Validation.prototype.notIn = function( str1, str2 )
{
    var i = 0;
    var j = str2.length;
	
	for( ; i < j; i++ )
    {
		var str3 =  str2.charAt( i );
		
		if ( str1.indexOf( str3 ) != -1 )
			return false;
	}
	
	return true;
};

/**
 * @access public
 */
Validation.prototype.isAlpha = function( ch )
{
	if ( ( ( ch >= 'a' ) && ( ch <= 'z' ) ) || ( ( ch >= 'A' ) && ( ch <= 'Z' ) ) )
		return true;
	else
		return false;
};

/**
 * @access public
 */
Validation.prototype.isAlnum = function( ch )
{
	if ( this.isAlpha( ch ) || this.isDigit( ch ) )
		return true;
    else
		return false;
};

/**
 * @access public
 */
Validation.prototype.isString = function( val )
{
	var tmp;
	
	if ( this.isEmpty( val ) )
		return false;
		
	val = val.toLowerCase();

	var valid = "abcdefghijklmnopqrstuvwxyz"
	var ok    = "yes";

	for ( var i = 0; i < val.length; i++ )
	{
		tmp = "" + val.substring( i, i + 1 );
		
		if ( valid.indexOf( tmp ) == -1 )
			return false;
	}
	
	return true;
};

/**
 * @access public
 */
Validation.prototype.isEMail = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
	
	// basic checking	
	var AtSym  = val.indexOf( '@' );
	var Period = val.lastIndexOf( '.' );
	var Space  = val.indexOf( ' ' );
	var Length = val.length - 1;
	
	if ( ( AtSym < 1 ) || ( Period <= AtSym + 1 ) || ( Period == Length ) || ( Space != -1 ) )
		return false;
	
	// valid country code?
	if ( country == true )
	{
		var isCC = false;
	
		if ( this.isCountryCode( val.substring( val.lastIndexOf( '.' ) + 1, val.length ), true ) )
			isCC = true;
	}
	
	return ( country == true )? isCC : true;
};

/**
 * @access public
 */
Validation.prototype.isEMailRegExp = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
		
	var temp = val.replace( /\s/g, "" );
	return ( temp.match( /^[\w\.\-]+\x40[\w\.\-]+\.\w{3}$/) ) && ( temp.charAt( 0 ) != "." ) && !( temp.match(/\.\./) );
};

/**
 * @access public
 */
Validation.prototype.isIP = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
		
	var arr = val.split( '.' );

	if ( arr.length != 4 )
		return false;
		
	for ( var i in arr )
	{
		if ( !this.isInteger( arr[i] ) )
			return false;
	}
	
	return true;
};

/**
 * @access public
 */
Validation.prototype.isURL = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
		
	var arr = val.split( ':' );
	
	// check for valid protocol...
	if ( arr[0] != "http" && arr[0] != "https" && arr[0] != "ftp" )
		return false;

	// followed by double slash
	if ( arr[1].substring( 0, 2 ) != "//" )
		return false;
	
	return true;
};

/**
 * @access public
 */
Validation.prototype.isCreditcardFormat = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
		
	if ( val.length != 16 )
		return false;

	if ( !this.isInteger( val ) )
		return false;
		
	return true;
};

/**
 * @access public
 */
Validation.prototype.isCountryCode = function( val )
{
	var isCC = false;
	
	if ( this.isEmpty( val ) )
		return false;
				
	for ( var i in Validation.countrycodes )
	{
		if ( val == Validation.countrycodes[i] )
			isCC = true;
	}
	
	return isCC;
};

/**
 * @access public
 */
Validation.prototype.isPhoneNumber = function( val )
{
	var pos;
	
	if ( this.isEmpty( val ) )
		return false;
	
	if ( val.length < 3 )
		return false;

	for ( var i = 0; i < val.length; i++ )
	{
		pos = val.substring( i, i + 1 );

		if ( !this.isInteger( pos ) && ( pos != "-" ) && ( pos != " " ) && ( pos != "/" ) )
			return false;
	}
	
	return true;
};

/**
 * @access public
 */
Validation.prototype.hasValidFileExtension = function( file, ext )
{
	if ( file == null || ext == null )
		return false;		 

	fileext = file.trim().substring( file.lastIndexOf( '.' ) + 1, file.length );

	if ( typeof( ext ) == "object" )
	{
		for ( var i in ext )
		{
			if ( fileext == ext[i] )
				return true;
		}
	}
	else
	{
		return ( fileext == ext )? true : false;
	}
	
	return false;
};

/**
 * @access public
 */
Validation.prototype.isZip = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
		
	if ( val.length != size )
		return false;

	if ( !this.isInteger( val ) )
		return false;
		
	return true;
};

/**
 * @access public
 */
Validation.prototype.isAge = function( val )
{
	if ( this.isEmpty( val ) )
		return false;
	
	if ( val.length > 2 )
		return false;

	if ( !this.isInteger( val ) )
		return false;
		
	return true;
};

/**
 * @access public
 */
Validation.prototype.isMonth = function( val )
{
	val = val+"";
	val = val.toLowerCase();
	
	if ( ( val == "jan" ) || ( val == "feb" ) || ( val == "mar" ) || ( val == "apr" ) || ( val == "may" ) || ( val == "jun" ) ||
	     ( val == "jul" ) || ( val == "aug" ) || ( val == "sep" ) || ( val == "oct" ) || ( val == "nov" ) || ( val == "dec" ) )
	{
		return true;
	}
	
	if ( ( val == "january" )  || ( val == "february" ) || ( val == "march" )  || ( val == "april" )     || ( val == "may" )     ||
	     ( val == "june" )     || ( val == "july" )     || ( val == "august" ) || ( val == "september" ) || ( val == "october" ) ||
	     ( val == "november" ) || ( val == "december" ) )
	{
	    return true;
	}
	
	return false;
};

/**
 * @access public
 */
Validation.prototype.isStateAbbr = function( val )
{
	if ( val.length != 2 )
		return false;
		
	val = val+"";
	
	if ( val.charAt( 0 ) == ' ' || val.charAt( 1 ) == ' ' )
		return false;
		
	if ( this.isUSStateAbbr( val ) )
		return true;
		
	if ( this.isCanadianStateAbbr( val ) )
		return true;
		
	return false;
};

/**
 * @access public
 */
Validation.prototype.isUSStateAbbr = function( val )
{
	val = val+"";
	
	if ( val.length != 2 )
		return false;
		
	if ( val.charAt( 0 ) == ' ' || val.charAt( 1 ) == ' ' )
		return false;
		
	var string = "AK AL AR AZ CA CO CT DC DE FL GA HI IA ID IL IN KS KY LA MA MD ME MI MN MO MS MT NC ND NE NH NJ NM NV NY OH OK OR PA PR RI SC SD TN TX UT VA VI VT WA WI WV WY";
	
	if ( string.indexOf( val.toUpperCase() ) != -1 )
		return true;

	return false;
};

/**
 * @access public
 */
Validation.prototype.isCanadianStateAbbr = function( val )
{
	val = val+"";
	
	if ( val.length != 2 )
		return false;
		
	if ( val.charAt( 0 ) == ' ' || val.charAt( 1 ) == ' ' )
		return false;
		
	var string = "AB BC EI MB NB NF NS NT NU ON PQ SK YK";
	
	if ( string.indexOf( val.toUpperCase() ) != -1 )
		return true;

	return false;
};

/**
 * @access public
 */
Validation.prototype.setNullIfBlank = function( obj )
{
	if ( this.isBlank( obj.value ) )
		obj.value = "";
};

/**
 * @access public
 */
Validation.prototype.setFieldsToUpperCase = function()
{
	for ( var i = 0; i < arguments.length; i++ )
	{
		var obj = arguments[i];
		obj.value = obj.value.toUpperCase();
	}
};

/**
 * @access public
 */
Validation.prototype.disallowBlank = function( obj )
{
	var msg;
	var dofocus;
	
	if ( arguments.length > 1 )
		msg = arguments[1];
		
	if ( arguments.length > 2 )
		dofocus = arguments[2];
	else 
		dofocus = false;
		
	if ( this.isBlank( obj.value ) )
	{
		if ( !this.isBlank( msg ) )
			return Base.raiseError( msg );
		
		if ( dofocus )
		{
			obj.select();
			obj.focus();
		}
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
Validation.prototype.disallowModify = function( obj )
{
	var msg;
	var dofocus;
	
	if ( arguments.length > 1 )
		msg = arguments[1];
		
	if ( arguments.length > 2 )
		dofocus = arguments[2];
	else
		dofocus = false;
		
	if ( this.getInputValue( obj ) != this.getInputDefaultValue( obj ) )
	{
		if ( !this.isBlank( msg ) )
			return Base.raiseError( msg );
		
		if ( dofocus )
		{
			obj.select();
			obj.focus();
		}
		
		this.setInputValue( obj, this.getInputDefaultValue( obj ) );
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
Validation.prototype.isChanged = function( obj )
{
	if ( ( typeof obj.type != "string" ) && ( obj.length > 0 ) && ( obj[0] != null ) && ( obj[0].type == "radio" ) )
	{
		for ( var i = 0; i < obj.length; i++ )
		{
			if ( obj[i].checked != obj[i].defaultChecked )
				return true;
		}
		
		return false;
	}
	
	if ( ( obj.type == "text" ) || ( obj.type == "hidden" ) || ( obj.type == "textarea" ) )
		return ( obj.value != obj.defaultValue );
		
	if ( obj.type == "checkbox" )
		return ( obj.checked != obj.defaultChecked );
	
	if ( obj.type == "select-one" )
	{ 
		if ( obj.options.length > 0 )
		{
			var x = 0;
			
			for ( var i = 0; i < obj.options.length; i++ )
			{
				if ( obj.options[i].defaultSelected)
					x++;
			}
			
			if ( x == 0 && obj.selectedIndex == 0 )
				return false;
				
			for ( var i = 0; i < obj.options.length; i++ )
			{
				if ( obj.options[i].selected != obj.options[i].defaultSelected )
					return true;
			}
		}
		
		return false;
	}
	
	if ( obj.type == "select-multiple" )
	{
		if ( obj.options.length > 0 )
		{
			for ( var i = 0; i < obj.options.length; i++ )
			{
				if ( obj.options[i].selected != obj.options[i].defaultSelected )
					return true;
			}
		}
		
		return false;
	}

	// return false for all other input types (button, image, etc)
	return false;
};

/**
 * @access public
 */
Validation.prototype.getInputValue = function( obj )
{
	if ( ( typeof obj.type != "string" ) && ( obj.length > 0 ) && ( obj[0] != null ) && ( obj[0].type == "radio" ) )
	{
		for ( var i = 0; i < obj.length; i++ )
		{
			if ( obj[i].checked == true )
				return obj[i].value;
		}
		
		return "";
	}
	
	if ( obj.type == "text" ) 
		return obj.value;
		
	if ( obj.type == "hidden" ) 
		return obj.value;
	
	if ( obj.type == "textarea" ) 
		return obj.value;
	
	if ( obj.type == "checkbox" )
	{
		if ( obj.checked == true )
			return obj.value;
		
		return "";
	}
	
	if ( obj.type == "select-one" )
	{ 
		if ( obj.options.length > 0 )
			return obj.options[obj.selectedIndex].value;
		else
			return "";
	}
	
	if ( obj.type == "select-multiple" )
	{ 
		var val = "";
		
		for ( var i = 0; i < obj.options.length; i++ )
		{
			if ( obj.options[i].selected )
				val = val + "" + obj.options[i].value + ",";
		}
		
		if ( val.length > 0 )
		{
			// remove trailing comma
			val = val.substring( 0, val.length - 1 );
		}
		
		return val;
		
	}
	
	return "";
};

/**
 * @access public
 */
Validation.prototype.getInputDefaultValue = function( obj )
{
	if ( ( typeof obj.type != "string" ) && ( obj.length > 0 ) && ( obj[0] != null ) && ( obj[0].type == "radio" ) )
	{
		for ( var i = 0; i < obj.length; i++ )
		{
			if ( obj[i].defaultChecked == true )
				return obj[i].value;
		}
		
		return "";
	}
	
	if ( obj.type == "text" ) 
		return obj.defaultValue;
		
	if ( obj.type == "hidden" ) 
		return obj.defaultValue;
	
	if ( obj.type == "textarea" ) 
		return obj.defaultValue;
	
	if ( obj.type == "checkbox" )
	{
		if ( obj.defaultChecked == true )
			return obj.value;
		
		return "";
	}
	
	if ( obj.type == "select-one" )
	{
		if ( obj.options.length > 0 )
		{
			for ( var i = 0; i < obj.options.length; i++ )
			{
				if ( obj.options[i].defaultSelected )
					return obj.options[i].value;
			}
		}
		
		return "";
	}
	
	if ( obj.type == "select-multiple" )
	{ 
		var val = "";
		
		for ( var i = 0; i < obj.options.length; i++ )
		{
			if ( obj.options[i].defaultSelected )
				val = val + "" + obj.options[i].value + ",";
		}
		
		if ( val.length > 0 )
		{
			// remove trailing comma
			val = val.substring( 0, val.length - 1 );
		}
		
		return val;
	}
	
	return "";
};

/**
 * @access public
 */
Validation.prototype.setInputValue = function( obj, val )
{
	if ( ( typeof obj.type != "string" ) && ( obj.length > 0 ) && ( obj[0] != null ) && ( obj[0].type == "radio" ) )
	{
		for ( var i = 0; i < obj.length; i++ )
		{
			if ( obj[i].value == val ) 
				obj[i].checked = true;
			else
				obj[i].checked = false;
		}
	}
	
	if ( obj.type == "text" ) 
		obj.value = val;
	
	if ( obj.type == "hidden" ) 
		obj.value = val;
	
	if ( obj.type == "textarea" ) 
		obj.value = val;
	
	if ( obj.type == "checkbox" )
	{
		if ( obj.value == val )
			obj.checked = true;
		else
			obj.checked = false;
	}
	
	if ( ( obj.type == "select-one" ) || ( obj.type == "select-multiple" ) )
	{
		for ( var i = 0; i < obj.options.length; i++ )
		{
			if ( obj.options[i].value == val )
				obj.options[i].selected = true;
			else
				obj.options[i].selected = false;
		}
	}
};

/**
 * @access public
 */
Validation.prototype.isFormModified = function( theform, hidden_fields, ignore_fields )
{
	if ( hidden_fields == null )
		hidden_fields = "";
		
	if ( ignore_fields == null )
		ignore_fields = "";
	
	var i, field;
	var hiddenFields = new Object();
	var ignoreFields = new Object();
	
	var hidden_fields_array = hidden_fields.split(',');
	for ( i = 0; i < hidden_fields_array.length; i++ )
		hiddenFields[hidden_fields_array[i].trim()] = true;
	
	var ignore_fields_array = ignore_fields.split(',');
	for ( i = 0; i < ignore_fields_array.length; i++ )
		ignoreFields[ignore_fields_array[i].trim()] = true;
	
	for ( i = 0; i < theform.elements.length; i++ )
	{
		var changed = false;
		var name = theform.elements[i].name;
		
		if ( !isBlank( name ) )
		{
			var type = theform[name].type;
			
			if ( !ignoreFields[name] )
			{
				if ( type == "hidden" && hiddenFields[name] )
					changed = this.isChanged( theform[name] );
				else if ( type == "hidden" )
					changed = false;
				else
					changed = this.isChanged( theform[name] );
			}
		}
		
		if ( changed ) 
			return true;
	}
	
	return false;
};


/**
 * @access public
 * @static
 */
Validation.whitespace = "\n\r\t ";

/**
 * @access public
 * @static
 */
Validation.countrycodes = new Array(
	// big six
	"com", "edu", "net", "org", "biz", "info", 
	
	// country codes
	"ad", "ae", "af", "ag", "ai", "al", "am", "an", "ao", "aq", "ar", "as", "at", "au", "aw", "az", 
	"ba", "bb", "bd", "be", "bf", "bg", "bh", "bi", "bj", "bm", "bn", "bo", "br", "bs", "bt", "bv", "bw", "by", "bz", 
	"ca", "cc", "cd", "cf", "cg", "ch", "ci", "ck", "cl", "cm", "cn", "co", "cr", "cs", "cu", "cv", "cx", "cy", "cz", 
	"de", "dj", "dk", "dm", "do", "dz", 
	"ec", "ee", "eg", "eh", "er", "es", "et", 
	"fi", "fj", "fk", "fm", "fo", "fr", "fx", 
	"ga", "gb", "gd", "ge", "gf", "gh", "gi", "gl", "gm", "gn", "gp", "gq", "gr", "gs", "gt", "gu", "gw", "gy", 
	"hk", "hm", "hn", "hr", "ht", "hu", 
	"id", "ie", "il", "in", "io", "iq", "ir", "is", "it", 
	"jm", "jo", "jp", 
	"ke", "kg", "kh", "ki", "km", "kn", "kp", "kr", "kw", "ky", "kz", 
	"la", "lb", "lc", "li", "lk", "lr", "ls", "lt", "lu", "lv", "ly", 
	"ma", "mc", "md", "mg", "mh", "mk", "ml", "mm", "mn", "mo", "mp", "mq", "mr", "ms", "mt", "mu", "mv", "mw", "mx", "my", "mz", 
	"na", "nc", "ne", "nf", "ng", "ni", "nl", "no", "np", "nr", "nt", "nu", "nz", 
	"om", 
	"pa", "pe", "pf", "pg", "ph", "pk", "pl", "pm", "pn", "pr", "pt", "pw", "py", 
	"qa", 
	"re", "ro", "ru", "rw", 
	"sa", "sb", "sc", "sd", "se", "sg", "sh", "si", "sj", "sk", "sl", "sm", "sn", "so", "sr", "st", "sv", "sy", "sz", 
	"tc", "td", "tf", "tg", "th", "tj", "tk", "tm", "tn", "to", "tp", "tr", "tt", "tv", "tw", "tz", 
	"ua", "ug", "uk", "um", "us", "uy", "uz", 
	"va", "vc", "ve", "vg", "vi", "vn", "vu", 
	"wf", "ws", 
	"ye", "yt", "yu", 
	"za", "zm", "zr", "zw"
);

/**
 * @access public
 * @static
 */
Validation.getFormField = function( fm, field )
{
	return eval( "document." + fm + "[\"" + field + "\"].value" );
};
