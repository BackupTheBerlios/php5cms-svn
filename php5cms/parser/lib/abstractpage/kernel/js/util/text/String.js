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
 * @package util_text
 */
 
/**
 * @access public
 */
String.prototype.clone = function()
{
	return new String( this );
};

/**
 * @access public
 */
String.prototype.toBoolean = function()
{
	var val = new String( this.toLowerCase() );
	
	if ( val == "true" || val == "yes" || val == "on" || val == "t" || val == "1" )
		return new Boolean( true );
	else if ( val == "false" || val == "no" || val == "off" || val == "f" || val == "0" )
		return new Boolean( false );
	
	return new Boolean( false );
};

/**
 * @access public
 */
String.prototype.reverse = function()
{
	var tmp = this.clone();
	var str = "";
	
	for ( i = tmp.length; i >= 0; i-- )
		str += tmp.charAt( i );
		
	return str;
};

/**
 * @access public
 */
String.prototype.ucwords = function()
{
	var ch;
	var await = true;
	var retString = "";
	
	for ( var i = 0; i < this.length; i++ )
	{
		if ( this.charAt( i ) == this.isEmpty() )
		{
			await = true;
			retString += this.charAt( i );
			
			continue;
		}
		
		if ( await == true )
		{
			ch = this.charAt( i );
		
			for ( var j = 0; j < String.lowercase.length; j++ )
			{
				if ( ch == String.lowercase.charAt( j )  )
					ch = String.uppercase.charAt( j );
			}
			
			retString += ch;
			await = false;
		}
		else
		{
			retString += this.charAt( i );
		}
	}
	
	return retString;
};

/**
 * @access public
 */
String.prototype.lcwords = function()
{
	var ch;
	var await = true;
	var retString = "";
	
	for ( var i = 0; i < this.length; i++ )
	{
		if ( this.charAt( i ) == this.isEmpty() )
		{
			await = true;
			retString += this.charAt( i );
			
			continue;
		}
		
		if ( await == true )
		{
			ch = this.charAt( i );
		
			for ( var j = 0; j < String.lowercase.length; j++ )
			{
				if ( ch == String.uppercase.charAt( j )  )
					ch = String.lowercase.charAt( j );
			}
			
			retString += ch;
			await = false;
		}
		else
		{
			retString += this.charAt( i );
		}
	}
	
	return retString;
};

/**
 * @access public
 */
String.prototype.abbreviate = function( length, recognizeBlank )
{
	if ( recognizeBlank != null )
	{
		var seq = this.substring( 0, length - String.abbrevSeq.length );
		
		if ( seq.lastIndexOf( String.fromCharCode( 32 ) ) > 0 )
			return seq.substring( 0, seq.lastIndexOf( String.fromCharCode( 32 ) ) ) + String.abbrevSeq;
	}
	
	return this.substring( 0, length - String.abbrevSeq.length ) + String.abbrevSeq;
};

/**
 * @access public
 */
String.prototype.nl2br = function() 
{
	var returnString = "";
	
	for ( i = 0; i < this.length; i++ )
	{
		if ( this.charAt( i ) == "\n" )
			returnString += "<br>";
		else
			returnString += this.charAt( i );
	}
	
	return returnString;
};

/**
 * This is the javascript method equivalent to PHP's rawurlencode(),
 * however it doesn't encode utf-8 multibyte characters correctly in most browsers. 
 * Use this instead of escape() to ensure a correctly encoded url string (RFC1738).
 *
 * @access public
 */
String.prototype.urlEncode = function()
{ 
    var len = this.length; 
    var res = new String(); 
    var charOrd = new Number(); 
     
    for ( var i = 0; i < len; i++ )
	{ 
        charOrd = this.charCodeAt(i); 
        
		if ( ( charOrd >= 65 && charOrd <=  90 ) ||
			 ( charOrd >= 97 && charOrd <= 122 ) ||
			 ( charOrd >= 48 && charOrd <=  57 ) ||
			 ( charOrd == 33 ) ||
			 ( charOrd == 36 ) ||
			 ( charOrd == 95 ) )
		{ 
			// this is alphanumeric or $-_.+!*'(), which according to RFC1738 we don't escape 
			res += this.charAt( i ); 
        } 
        else
		{ 
            res += '%'; 
            
			if ( charOrd > 255 )
				res += 'u'; 
            
			hexValStr = charOrd.toString(16); 
            
			if ( ( hexValStr.length ) % 2 == 1 )
				hexValStr = '0' + hexValStr; 
            
			res += hexValStr; 
        } 
    } 

    return res; 
};

/**
 * @access public
 */
String.prototype.escapeProperly = function() 
{
	var plusPos;
	
	var strOut = "";
	var ix = 0;
	
	while ( -1 != ( plusPos = this.indexOf( "+", ix ) ) )
	{
		// add the escaped version of any characters that preceded
		// the + to the output string
		strOut += escape( this.slice( ix, plusPos ) );

		// add the escaped version of the + sign
		strOut += "%2b";

		// advance to next index
		ix = plusPos + 1;
	}

	// pick up anything left
	strOut += escape( this.slice( ix ) );

	return strOut;
};

/**
 * Removes all characters which appear in string bag.
 *
 * @access public
 */
String.prototype.stripCharsInBag = function( bag )
{
	var i;
    var returnString = "";

    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for ( i = 0; i < this.length; i++ )
    {   
		// Check that current character isn't whitespace.
        var c = this.charAt( i );
		
        if ( bag.indexOf( c ) == -1 )
			returnString += c;
    }

    return returnString;
};

/**
 * Removes all characters which do NOT appear in string bag.
 *
 * @access public
 */
String.prototype.stripCharsNotInBag = function( bag )
{
	var i;
    var returnString = "";

    // Search through string's characters one by one.
    // If character is in bag, append to returnString.
    for ( i = 0; i < this.length; i++ )
    {   
        // Check that current character isn't whitespace.
        var c = this.charAt( i );
		
        if ( bag.indexOf( c ) != -1 )
			returnString += c;
    }

    return returnString;
};

/**
 * @access public
 */
String.prototype.eatWhitespace = function()
{
	return this.stripCharsInBag( String.whitespace );
};

/**
 * @access public
 */
String.prototype.wordwrap = function( width, br )
{
	if ( br == null )
		br = "\n";
		
	var returnString = "";
	
	var rn = 1;
	for ( i = 0; i < this.length; i++ )
	{
		if ( i / ( width ) == rn )
		{
			returnString += ( br + this.charAt( i ) );
			rn++;
		}
		else
		{
			returnString += this.charAt( i );
		}
	}
	
	return returnString;
};

/**
 * @access public
 */
String.prototype.uppercaseFirst = function()
{
	var first = this.charAt( 0 );
	
	for ( var i = 0; i < String.lowercase.length; i++ )
	{
		if ( first == String.lowercase.charAt( i )  )
			first = String.uppercase.charAt( i );
	}
	
	return first + this.substring( 1, this.length );
};

/**
 * @access public
 */
String.prototype.removeQuotes = function()
{
	return this.replace( /^['"]|['"]$/g, "" );
};

/**
 * @access public
 */
String.prototype.isEmpty = function()
{
	for ( var i = 0; i < this.length; i++ )
	{
		if ( ( this.charAt( i ) != ' ' ) && ( this.charAt( i ) != "\t" ) && ( this.charAt( i ) != "\n" ) )
			return false;
	}
	
	return true;
};

/**
 * @access public
 */
String.prototype.tokenize = function( s )
{
	var i;
	var sep    = s || "\n";
	var result = new Array();
	var begin  = 0;
	
	for ( i = 0; i < this.length; i++ )
	{
		var ch = this.charAt( i );

		if ( ch == sep )
		{
			result[result.length] = this.substring( begin, i );
			begin = i;
		}
		
		// Don´t forget the guys from the last row!
		if ( ( i == this.length - 1 ) && ( this.substring( begin, i + 1 ) != sep ) )
			result[result.length] = this.substring( begin, i + 1 );
	}

	return ( result.length == 0 )? new Array( this ) : result;
};

/**
 * @access public
 */
String.prototype.isUSAscii = function() 
{
	var i, ch; 
	var result = true; 

	for ( i = 0; i < this.length; i++ )
	{ 
		ch = this.charAt( i ); 
		
		if ( ch < ' ' || '~' < ch )
		{ 
			if ( ( ch != '\t' ) && ( ch != '\n' ) && ( ch != '\r' ) ) 
				result = false; 
		}
	}

	return result; 
};

/**
 * @access public
 */
String.prototype.containsSpaces = function() 
{
	// check for null string
	if ( this == null )
		return false;

	// scan from right to left for blanks
	for ( var i = 0; i < this.length; i++ )
	{
		if ( this[i] == " " )
			return true;
    }

	return false;
};

/**
 * @access public
 */
String.prototype.atBegin = function( str )
{
	return ( this.substring( 0, str.length ) == str );
};

/**
 * @access public
 */
String.prototype.atEnd = function()
{
	var bOk = false;
	
	for ( var i = 0; i < arguments.length; i++ )
	{
		if ( this.indexOf( arguments[i] ) == this.length - arguments[i].length )
		{
			bOk = true;
			break;
		}
	}
	
	return bOk;
};

/**
 * @access public
 */
String.prototype.ltrim = function()
{
	return this.trim( true, false );
};

/**
 * @access public
 */
String.prototype.rtrim = function()
{
	return this.trim( false, true );
};

/**
 * @access public
 */
String.prototype.trim = function( leftTrim, rightTrim )
{
	// return this.replace( /^[ \n\r\t]+|[ \n\r\t]+$/g, "" );
	
    if ( leftTrim == null )
        leftTrim = true;

    if ( rightTrim == null )
        rightTrim = true;

    var left  = 0;
    var right = 0;
	
    var i = 0;
    var k = 0;

    // modified to properly handle strings that are all whitespace
    if ( leftTrim == true )
	{
        while ( ( i < this.length ) && ( String.whitespace.indexOf( this.charAt( i++ ) ) != -1 ) )
            left++;
    }
	
    if ( rightTrim == true )
	{
        k = this.length - 1;
        
		while ( ( k >= left ) && ( String.whitespace.indexOf( this.charAt( k-- ) ) != -1 ) )
            right++;
    }
	
    return this.substring( left, this.length - right );
};

/**
 * @access public
 */
String.prototype.convertGermanUmlaute = function()
{
	var i, ch; 
	var stringnew = "";

	if ( this == null )
		return null;
	
	if ( this.length == 0 )
		return "";

	for ( i = 0; i < this.length; i++ ) 
	{ 
		ch = this.charAt( i ); 
		
		if ( ch == 'ä' )
			stringnew += 'ae';
		else if ( ch == 'ö' )
			stringnew += 'oe';
		else if ( ch == 'ü' )
			stringnew += 'ue';
		else if ( ch == 'Ä' )
			stringnew += 'Ae';
		else if ( ch == 'Ö' )
			stringnew += 'Oe';
		else if ( ch == 'Ü' )
			stringnew += 'Ue';
		else if ( ch == 'ß' )
			stringnew += 'ss';
		else
			stringnew += ch;
	} 

	return stringnew;
};

/**
 * @access public
 */
String.prototype.convertToEscapes = function()
{
	var i, ch; 
	var stringnew = "";

	if ( this == null )
		return null;
	
	if ( this.length == 0 )
		return "";

	for ( i = 0; i < this.length; i++ ) 
	{ 
		ch = this.charAt( i ); 
		
		if ( ch == '<' )
			stringnew += '&lt;';
		else if ( ch == '>' )
			stringnew += '&gt;';
		else if ( ch == '&' )
			stringnew += '&amp;';
		else
			stringnew += ch;
	} 

	return stringnew;
};

/**
 * @access public
 */
String.prototype.convertSpecialChars = function()
{
	var i, ch; 
	var stringnew = "";

	if ( this == null )
		return null;
	
	if ( this.length == 0 )
		return "";

	for ( i = 0; i < this.length; i++ ) 
	{ 
		ch = this.charAt( i ); 
		
		if ( ch == '¡' )
			stringnew += '&iexcl;';
		else if ( ch == '¢' )
			stringnew += '&cent;';
		else if ( ch == '£' )
			stringnew += '&pound;';
		else if ( ch == '¤' )
			stringnew += '&curren;';	
		else if ( ch == '¥' )
			stringnew += '&yen;';
		else if ( ch == '¦' )
			stringnew += '&brvbar;';
		else if ( ch == '§' )
			stringnew += '&sect;';
		else if ( ch == '¨' )
			stringnew += '&uml;';
		else if ( ch == '©' )
			stringnew += '&copy;';
		else if ( ch == 'ª' )
			stringnew += '&ordf;';
		else if ( ch == '«' )
			stringnew += '&laquo;';
		else if ( ch == '¬' )
			stringnew += '&not;';
		else if ( ch == '®' )
			stringnew += '&reg;';
		else if ( ch == '¯' )
			stringnew += '&macr;';
		else if ( ch == '°' )
			stringnew += '&deg;';
		else if ( ch == '±' )
			stringnew += '&plusmn;';
		else if ( ch == '²' )
			stringnew += '&sup2;';
		else if ( ch == '³' )
			stringnew += '&sup3;';
		else if ( ch == '´' )
			stringnew += '&acute;';
		else if ( ch == 'µ' )
			stringnew += '&micro;';
		else if ( ch == '¶' )
			stringnew += '&para;';
		else if ( ch == '·' )
			stringnew += '&middot;';
		else if ( ch == '¸' )
			stringnew += '&cedil;';
		else if ( ch == '¹' )
			stringnew += '&sup1;';
		else if ( ch == 'º' )
			stringnew += '&ordm;';
		else if ( ch == '»' )
			stringnew += '&raquo;';
		else if ( ch == '¼' )
			stringnew += '&frac14;';
		else if ( ch == '½' )
			stringnew += '&frac12;';
		else if ( ch == '¾' )
			stringnew += '&frac34;';
		else if ( ch == '¿' )
			stringnew += '&iquest;';
		else if ( ch == 'À' )
			stringnew += '&Agrave;';
		else if ( ch == 'Á' )
			stringnew += '&Aacute;';
		else if ( ch == 'Â' )
			stringnew += '&Acirc;';
		else if ( ch == 'Ã' )
			stringnew += '&Atilde;';
		else if ( ch == 'Ä' )
			stringnew += '&Auml;';
		else if ( ch == 'Å' )
			stringnew += '&Aring;';
		else if ( ch == 'Æ' )
			stringnew += '&AElig;';	
		else if ( ch == 'Ç' )
			stringnew += '&Ccedil;';
		else if ( ch == 'È' )
			stringnew += '&Egrave;';
		else if ( ch == 'É' )
			stringnew += '&Eacute;';
		else if ( ch == 'Ê' )
			stringnew += '&Ecirc;';
		else if ( ch == 'Ë' )
			stringnew += '&Euml;';
		else if ( ch == 'Ì' )
			stringnew += '&Igrave;';
		else if ( ch == 'Í' )
			stringnew += '&Iacute;';
		else if ( ch == 'Î' )
			stringnew += '&Icirc;';
		else if ( ch == 'Ï' )
			stringnew += '&Iuml;';
		else if ( ch == 'Ð' )
			stringnew += '&ETH;';
		else if ( ch == 'Ñ' )
			stringnew += '&Ntilde;';
		else if ( ch == 'Ò' )
			stringnew += '&Ograve;';
		else if ( ch == 'Ó' )
			stringnew += '&Oacute;';
		else if ( ch == 'Ô' )
			stringnew += '&Ocirc;';
		else if ( ch == 'Õ' )
			stringnew += '&Otilde;';
		else if ( ch == 'Ö' )
			stringnew += '&Ouml;';
		else if ( ch == '×' )
			stringnew += '&times;';
		else if ( ch == 'Ø' )
			stringnew += '&Oslash;';
		else if ( ch == 'Ù' )
			stringnew += '&Ugrave;';
		else if ( ch == 'Ú' )
			stringnew += '&Uacute;';
		else if ( ch == 'Û' )
			stringnew += '&Ucirc;';
		else if ( ch == 'Ü' )
			stringnew += '&Uuml;';	
		else if ( ch == 'Ý' )
			stringnew += '&Yacute;';
		else if ( ch == 'Þ' )
			stringnew += '&THORN;';
		else if ( ch == 'ß' )
			stringnew += '&szlig;';
		else if ( ch == 'à' )
			stringnew += '&agrave;';
		else if ( ch == 'á' )
			stringnew += '&aacute;';
		else if ( ch == 'â' )
			stringnew += '&acirc;';
		else if ( ch == 'ã' )
			stringnew += '&atilde;';
		else if ( ch == 'ä' )
			stringnew += '&auml;';
		else if ( ch == 'å' )
			stringnew += '&aring;';
		else if ( ch == 'æ' )
			stringnew += '&aelig;';
		else if ( ch == 'ç' )
			stringnew += '&ccedil;';
		else if ( ch == 'è' )
			stringnew += '&egrave;';
		else if ( ch == 'é' )
			stringnew += '&eacute;';
		else if ( ch == 'ê' )
			stringnew += '&ecirc;';
		else if ( ch == 'ë' )
			stringnew += '&euml;';
		else if ( ch == 'ì' )
			stringnew += '&igrave;';
		else if ( ch == 'í' )
			stringnew += '&iacute;';
		else if ( ch == 'î' )
			stringnew += '&icirc;';
		else if ( ch == 'ï' )
			stringnew += '&iuml;';
		else if ( ch == 'ð' )
			stringnew += '&eth;';
		else if ( ch == 'ñ' )
			stringnew += '&ntilde;';
		else if ( ch == 'ò' )
			stringnew += '&ograve;';	
		else if ( ch == 'ó' )
			stringnew += '&oacute;';
		else if ( ch == 'ô' )
			stringnew += '&ocirc;';
		else if ( ch == 'õ' )
			stringnew += '&otilde;';
		else if ( ch == 'ö' )
			stringnew += '&ouml;';
		else if ( ch == '÷' )
			stringnew += '&divide;';
		else if ( ch == 'ø' )
			stringnew += '&oslash;';
		else if ( ch == 'ù' )
			stringnew += '&ugrave;';
		else if ( ch == 'ú' )
			stringnew += '&uacute;';
		else if ( ch == 'û' )
			stringnew += '&ucirc;';
		else if ( ch == 'ü' )
			stringnew += '&uuml;';
		else if ( ch == 'ý' )
			stringnew += '&yacute;';
		else if ( ch == 'þ' )
			stringnew += '&thorn;';
		else if ( ch == 'ÿ' )
			stringnew += '&yuml;';
		else
			stringnew += ch;
	} 
	
	return stringnew;
};

/**
 * Goes through the String and replaces every occurrence of fromString with toString.
 *
 * @access public
 */
String.prototype.replaceSubstring = function( fromString, toString )
{
	var temp = this;
	
	if ( fromString == "" )
		return this;
   
	if ( toString.indexOf( fromString ) == -1 )
	{
		while ( temp.indexOf( fromString ) != -1 )
		{
			var toTheLeft  = temp.substring( 0, temp.indexOf( fromString ) );
			var toTheRight = temp.substring( temp.indexOf( fromString ) + fromString.length, temp.length );
			temp = toTheLeft + toString + toTheRight;
      	}
	}
	// String being replaced is part of replacement string (like "+" being replaced with "++") - prevent an infinite loop.
	else
	{
		var midStrings   = new Array( "~", "`", "_", "^", "#" );
		var midStringLen = 1;
		var midString    = "";
		
		// Find a string that doesn't exist in the String to be used as an "inbetween" string.
		while ( midString == "" )
		{
			for ( var i = 0; i < midStrings.length; i++ )
			{
				var tempMidString = "";
				
				for ( var j = 0; j < midStringLen; j++ )
					tempMidString += midStrings[i];
					
				if ( fromString.indexOf( tempMidString ) == -1 )
				{
					midString = tempMidString;
					i = midStrings.length + 1;
				}
			}
		}
		
		// Now go through and do two replaces - first, replace the "fromString" with the "inbetween" string.
		while ( temp.indexOf( fromString ) != -1 )
		{
			var toTheLeft  = temp.substring( 0, temp.indexOf( fromString ) );
			var toTheRight = temp.substring( temp.indexOf( fromString ) + fromString.length, temp.length );
			temp = toTheLeft + midString + toTheRight;
		}
		
		// Next, replace the "inbetween" string with the "toString".
		while ( temp.indexOf( midString ) != -1 )
		{
			var toTheLeft  = temp.substring( 0, temp.indexOf( midString ) );
			var toTheRight = temp.substring( temp.indexOf( midString ) + midString.length, temp.length );
			temp = toTheLeft + toString + toTheRight;
		}
	}
   
	return temp;
};

/**
 * Translates a string into a regex. Replaces identifiers
 * (beginning with "$") with corresponding regex fragment
 *
 * @access public
 */
String.prototype.resolve = function()
{
	var resolved = this;
	var regex = /(\$[a-zA-Z0-9]+)/;
  
	while ( regex.test( resolved ) )
		resolved = resolved.replace( RegExp.$1, String[ RegExp.$1 ] );
  
	return resolved.replace( / /g,"" );
};

/**
 * We really like the @-fomula language's ability to pull out part of a string
 * based on another string. For example, @Right("Hello", "e") returns "H".
 * Here are a few JavaScript functions to mimic the @-function capabilities. 
 *
 * @access public
 */
String.prototype.rightString = function( subString )
{
	if ( this.indexOf( subString ) == -1 )
		return this;
	else
		return ( this.substring( this.indexOf( subString ) + subString.length, this.length ) );
};

/**
 * @access public
 */
String.prototype.rightBackString = function( subString )
{
	if ( this.lastIndexOf( subString ) == -1 )
		return this;
	else
		return this.substring( this.lastIndexOf( subString ) + 1, this.length );
};

/**
 * @access public
 */
String.prototype.middleString = function( startString, endString )
{
	if ( fullString.indexOf( startString ) == -1 )
	{
		return this;
	}
	else
	{
		var sub = this.substring( this.indexOf( startString ) + startString.length, this.length );
		
		if ( sub.indexOf( endString ) == -1 )
			return sub;
		else
			return ( sub.substring( 0, sub.indexOf( endString ) ) );
   }
};

/**
 * @access public
 */
String.prototype.middleBackString = function( startString, endString )
{
	if ( this.lastIndexOf( startString ) == -1 )
	{
		return this;
	}
	else
	{
		var sub = this.substring( 0, this.lastIndexOf( startString ) );
		
		if ( sub.indexOf( endString ) == -1 )
			return sub;
		else
			return ( sub.substring( sub.indexOf( endString ) + endString.length, sub.length ) );
   }
};

/**
 * @access public
 */
String.prototype.leftString = function( subString )
{
	if ( this.indexOf( subString ) == -1 )
		return this;
	else
		return ( this.substring( 0, this.indexOf( subString ) ) );
};

/**
 * @access public
 */
String.prototype.leftBackString = function( subString )
{
   if ( this.lastIndexOf( subString ) == -1 )
		return this;
	else
		return this.substring( 0, this.lastIndexOf( subString ) );
};


// XPath

/**
 * @access public
 */
String.prototype.after = function( parMatch, blnCaseSensitive )
{
	var retVal = '';
	var strMatch = String( parMatch );
	
	if ( this.length == 0 || strMatch.length == 0 ) 
		return retVal;
	
	blnCaseSensitive = blnCaseSensitive | ( String( blnCaseSensitive ) == 'undefined' ); //default is true

	var lngFind = 0;	
	
	if ( blnCaseSensitive )
		lngFind = this.indexOf( strMatch );
	else 
		lngFind = this.toUpperCase().indexOf( strMatch.toUpperCase() );
	
	if ( lngFind >= 0 ) 
	{
		if ( lngFind <= ( this.length - strMatch.length ) )
			retVal = this.substr( lngFind + strMatch.length );
	}
	 
	return retVal;
};

/**
 * @access public
 */
String.prototype.afterRev = function( parMatch, blnCaseSensitive )
{
	var retVal = '';
	var strMatch = String(parMatch);
	
	if ( this.length == 0 || strMatch.length == 0 ) 
		return retVal;
		
	blnCaseSensitive = blnCaseSensitive | ( String( blnCaseSensitive ) == 'undefined' );

	var lngFind = 0;
	
	if ( blnCaseSensitive )
		lngFind = this.lastIndexOf( strMatch );
	else 
		lngFind = this.toUpperCase().lastIndexOf( strMatch.toUpperCase() );
	
	if (lngFind >= 0) 
	{
		if ( lngFind <= ( this.length - strMatch.length ) )
			retVal = this.substr( lngFind + strMatch.length );
	}

	return retVal;
};

/**
 * @access public
 */
String.prototype.before = function( parMatch, blnCaseSensitive )
{
	// returns the string to the left of the first (from left) MatchString
	
	var retVal = '';
	var strMatch = String( parMatch );
	
	if ( this.length == 0 || strMatch.length == 0 )
		return retVal;
	
	blnCaseSensitive = blnCaseSensitive | ( String( blnCaseSensitive ) == 'undefined' );

	var lngFind = 0;
	
	if ( blnCaseSensitive )
		lngFind = this.indexOf( strMatch );
	else
		lngFind = this.toUpperCase().indexOf( strMatch.toUpperCase() );
	
	if ( lngFind > 0 )
		retVal = this.substr( 0, lngFind );
	 
	 return retVal;
};

/**
 * @access public
 */
String.prototype.beforeRev = function( parMatch, blnCaseSensitive )
{
	// returns the string to the left of the first (from left) MatchString
	
	var retVal = '';
	var strMatch = String( parMatch );
	
	if ( this.length == 0 || strMatch.length == 0 )
		return retVal;
	
	blnCaseSensitive = blnCaseSensitive | ( String( blnCaseSensitive ) == 'undefined' );

	var lngFind = 0;
	
	if ( blnCaseSensitive )
		lngFind = this.lastIndexOf( strMatch );
	else
		lngFind = this.toUpperCase().lastIndexOf( strMatch.toUpperCase() );

	if ( lngFind > 0 )
		retVal = this.substr( 0, lngFind );
	 
	 return retVal;
};

/**
 * @access public
 */
String.prototype.contains = function( parMatch, blnCaseSensitive )
{
	var retVal = false;
	var strMatch = String( parMatch );
	
	if ( strMatch.length == 0 )
		return true; //per XPath spec
	
	blnCaseSensitive = blnCaseSensitive | ( String( blnCaseSensitive ) == 'undefined' );
	
	if ( blnCaseSensitive ) 
		retVal = ( this.indexOf( strMatch ) > -1 );
	else
		retVal = ( this.toUpperCase().indexOf( strMatch.toUpperCase() ) > -1 )

	return retVal;
};

/**
 * @access public
 */
String.prototype.startsWith = function( parMatch, blnCaseSensitive )
{
	var retVal = false;
	var strMatch = String( parMatch );

	if ( strMatch.length == 0 )
		return true; //per XPath spec

	if ( strMatch.length > this.length ) 
		return false;

	blnCaseSensitive = blnCaseSensitive | ( String( blnCaseSensitive ) == 'undefined' );

	if ( blnCaseSensitive ) 
		retVal = ( this.indexOf( strMatch ) == 0 );
	else
		retVal = ( this.toUpperCase().indexOf( strMatch.toUpperCase() ) == 0 );

	return retVal;
};

/**
 * @access public
 */
String.prototype.endsWith = function( parMatch, blnCaseSensitive )
{
	var retVal = false;
	var strMatch = String( parMatch );
	
	if ( strMatch.length == 0 )
		return true; //per XPath spec
	
	if ( strMatch.length > this.length ) 
		return false;
	
	blnCaseSensitive = blnCaseSensitive | ( String( blnCaseSensitive ) == 'undefined' );
	
	if ( blnCaseSensitive )
	{
		var strSlice = this.slice( this.length - strMatch.length );
		retVal = (strSlice == strMatch);
	}
	else
	{
		var strSlice = this.toUpperCase().slice( this.length - strMatch.length );
		retVal = ( strSlice == upMatch );
	}
	
	return retVal;
};

/**
 * @access public
 */
String.prototype.normalize = function()
{
	// Normalize per the XPath spec

	// trim leading and trailing spaces
	strCopy = this.replace( /(^\s+)|(\s+$)/g, '' );

	// reduce runs of more than one space to one space only
	strCopy = strCopy.replace( /(\s+)/g, '\x20' ); // a space
	
	return strCopy;
};

/**
 * @access public
 */
String.prototype.translate = function( parFrom, parTo, blnCaseSensitive )
{
	var strFrom   = String( parFrom );
	var strTo     = String( parTo );
	var strCopy   = '';
	var strBuffer = '';
	var lngPos    = 0;	
	
	// defines a value for the "empty" char
	var strEmpty = String.fromCharCode( 1 );

	// flesh out the translation string if necessary	
	if ( strFrom.length > strTo.length )
	{
		for ( var i = 1; i <= ( strTo.length - strFrom.length ); i++ )
			strTo = strTo + strEmpty;
	}

	blnCaseSensitive = blnCaseSensitive | ( String( blnCaseSensitive ) == 'undefined' );
	
	// loop through each character in this
	for ( var i = 0; i < this.length; i++ ) 
	{
		strBuffer = this.substr( i, 1 );
		
		if ( blnCaseSensitive ) 
			lngPos = strFrom.indexOf( strBuffer );
		else
			lngPos = ( strFrom.toUpperCase() ).indexOf( ( strBuffer.toUpperCase() ) );
			
		if ( lngPos > -1 )
			strCopy = strCopy + strTo.charAt( lngPos );
		else
			strCopy = strCopy + strBuffer;
	}
	
	// finally remove "empty" characters
	return strCopy.replace( strEmpty, '' );
};


/**
 * @access public
 * @static
 */
String.whitespace = "\n\r\t ";

/**
 * @access public
 * @static
 */
String.uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

/**
 * @access public
 * @static
 */
String.lowercase = "abcdefghijklmnopqrstuvwxyz";

/**
 * @access public
 * @static
 */
String.abbrevSeq = "...";


/**
 * @access public
 * @static
 */
String.printlnf = function( format )
{
	var matches;
	var counter = 0;
	var re1 = /%s/;

	while ( re1.test( format ) )
	{
        if ( counter++ > 1000 )
			break;
			
		matches = re1.exec( format );
		format  = format.replace( matches[0], arguments[counter] );
	}
	
	return format;
};

/**
 * Returns an array of byterepresenting dezimal numbers.
 *
 * @access public
 * @static
 */
String.toCharCode = function( t )
{
	if ( t == null || typeof( t ) != "string" )
		return false;
		
	var arr = new Array();
	
	for ( var i = 0; i < t.length; i++ )
		arr[i] = t.charCodeAt( i );
	
	return arr;
};

/**
 * @access public
 * @static
 */
String.stripTags = function( str )
{
	var len = str.length;
	var a   = str.indexOf( "<" );
	var b   = str.indexOf( ">" );
	var c   = str.substring( 0, a );

	if ( b == -1 )
		b = a;

	var d = str.substring( ( b + 1 ), len );

	var word = c + d;
	var tmp  = word.indexOf( "<" );

	if ( tmp != -1 )
		word = String.stripTags( word );

	return word;
};

/**
 * @access public
 * @static
 */
String.stripComments = function( str )
{
    var i = 0;
    
	while ( ( i = str.indexOf( "/*", i ) ) != -1 ) 
        str = str.substring( 0, i ) + str.substring( ( str.indexOf( "*/", i ) + 1 || str.length ) + 1 );  
    
	i = 0;
    
	while ( ( i = str.indexOf( "//", i ) ) != -1 ) 
        str = str.substring( 0, i ) + str.substring( str.indexOf( "\n", i ) + 1 || str.length );
    
	while ( str.charAt( 0 ) == "\n" || str.charAt(0) == " " )
        str = str.substring( 1 );
    
	while ( str.charAt( str.length - 1 ) == "\n" || str.charAt( str.length - 1 ) == " " )
        str = str.substring( 0, str.length - 1 );
    
	return str;
};

/**
 * @access public
 * @static
 */
String.htmlspecialchars = function( str ) 
{
	s = new String( str );

	s = s.replace( /\&/g, '&amp;'  );
	s = s.replace( /\"/g, '&quot;' );
	s = s.replace( /</g,  '&lt;'   );
	s = s.replace( />/g,  '&gt;'   );

	return s;
};

/**
 * @access public
 * @static
 */
String.un_htmlspecialchars = function( str ) 
{
	s = new String( str );

	s = s.replace( /\&amp;/g,  '&' );
	s = s.replace( /\&quot;/g, '"' );
	s = s.replace( /\&lt;/g,   '<' );
	s = s.replace( /&gt;/g,    '>' );

	return s;
};

/**
 * @access public
 * @static
 */
String.cleanWordHTML = function( html ) 
{
	html = html.replace( /<\/?o:p>/gi, '' );
	html = html.replace( /(<td.*?>)\s*<p.*?>(.*?)<\/p>\s*<\/td>/gi, '$1$2</td>' );
	html = html.replace( /<span.*?>(.*?)<\/span>/gi, '$1' );
	html = html.replace( /<t((?:body|r|d|able)\s.*?)style=".*?"(.*?)>/gi, '<t$1$2>' );

	return html;
};

/**
 * @access public
 * @static
 */
String.sprintf = function()
{
	if ( !arguments || arguments.length < 1 || !RegExp )
		return null;
	
	var str = arguments[0];
	var re  = /([^%]*)%('.|0|\x20)?(-)?(\d+)?(\.\d+)?(%|b|c|d|u|f|o|s|x|X)(.*)/;
	var a   = b = [], numSubstitutions = 0, numMatches = 0;
	
	while ( a = re.exec( str ) )
	{
		var leftpart   = a[1], pPad  = a[2], pJustify  = a[3], pMinLength = a[4];
		var pPrecision = a[5], pType = a[6], rightPart = a[7];

		numMatches++;
		
		if ( pType == '%' )
		{
			subst = '%';
		}
		else
		{
			numSubstitutions++;

			// Too few arguments	
			if ( numSubstitutions >= arguments.length )
				return null;
			
			var param = arguments[numSubstitutions];
			var pad   = '';
			
			if ( pPad && pPad.substr( 0, 1 ) == "'" ) 
				pad = leftpart.substr( 1, 1 );
			else if ( pPad ) 
				pad = pPad;
			
			var justifyRight = true;
			
			if ( pJustify && pJustify === "-" ) 
				justifyRight = false;
			
			var minLength = -1;
			
			if ( pMinLength ) 
				minLength = parseInt( pMinLength );
			
			var precision = -1;
			
			if ( pPrecision && pType == 'f' ) 
				precision = parseInt( pPrecision.substring( 1 ) );
			
			var subst = param;
			
			if ( pType == 'b' ) 
				subst = parseInt( param ).toString( 2 );
			else if ( pType == 'c' ) 
				subst = String.fromCharCode( parseInt( param ) );
			else if ( pType == 'd' ) 
				subst = parseInt( param ) ? parseInt( param ) : 0;
			else if ( pType == 'u' ) 
				subst = Math.abs( param );
			else if ( pType == 'f' ) 
				subst = ( precision > -1 )? Math.round( parseFloat( param ) * Math.pow( 10, precision ) ) / Math.pow( 10, precision ): parseFloat( param );
			else if ( pType == 'o' ) 
				subst = parseInt( param ).toString( 8 );
			else if ( pType == 's' ) 
				subst = param;
			else if ( pType == 'x' ) 
				subst = ( '' + parseInt( param ).toString( 16 ) ).toLowerCase();
			else if ( pType == 'X' ) 
				subst = ( '' + parseInt( param ).toString( 16 ) ).toUpperCase();
		}
		
		str = leftpart + subst + rightPart;
	}
	
	return str;
};
