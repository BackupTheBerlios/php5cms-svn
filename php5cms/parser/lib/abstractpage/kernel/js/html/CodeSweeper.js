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
 * @package html
 */
 
/**
 * Constructor
 *
 * @access public
 */
CodeSweeper = function()
{
	this.Base = Base;
	this.Base();
};


CodeSweeper.prototype = new Base();
CodeSweeper.prototype.constructor = CodeSweeper;
CodeSweeper.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
CodeSweeper.sweep = function( html )
{
	if ( html == null )
		return "";

	html = CodeSweeper._replaceCharacters( html );
	html = CodeSweeper._replaceAbsoluteUrls( html );
	// html = CodeSweeper._removeEmptyTags( html );
	html = CodeSweeper._replaceTags( [["strong", "B"], ["em", "I"]], html );
	
	return html;
};


// private methods

/**
 * @access private
 * @static
 */
CodeSweeper._replaceCharacters = function( html )
{
	// Special characters and their HTML equivelent.
	var set = [
		[ "€","‘","’","’","“","”","–","—","¡","¢","£","£","¤","¥","¦","§","¨","©","ª","«","¬","­","®","¯","°","±","²","³","´","µ","¶","·","¸","¹","º","»","¼","½","¾","¿","À","Á","Â","Ã","Ä","Å","Æ","Ç","È","É","Ê","Ë","Ì","Í","Î","Ï","Ð","Ñ","Ò","Ó","Ô","Õ","Ö","×","Ø","Ù","Ú","Û","Ü","Ý","Þ","ß","à","á","â","ã","ä","å","æ","ç","è","é","ê","ë","ì","í","î","ï","ð","ñ","ò","ó","ô","õ","ö","÷","ø","ù","ú","û","ü","ü","ý","þ","ÿ" ],
		[ "&euro;","&lsquo;","&rsquo;","&rsquo;","&ldquo;","&rdquo;","&ndash;","&mdash;","&iexcl;","&cent;","&pound;","&pound;","&curren;","&yen;","&brvbar;","&sect;","&uml;","&copy;","&ordf;","&laquo;","&not;","­","&reg;","&macr;","&deg;","&plusmn;","&sup2;","&sup3;","&acute;","&micro;","&para;","&middot;","&cedil;","&sup1;","&ordm;","&raquo;","&frac14;","&frac12;","&frac34;","&iquest;","&Agrave;","&Aacute;","&Acirc;","&Atilde;","&Auml;","&Aring;","&AElig;","&Ccedil;","&Egrave;","&Eacute;","&Ecirc;","&Euml;","&Igrave;","&Iacute;","&Icirc;","&Iuml;","&ETH;","&Ntilde;","&Ograve;","&Oacute;","&Ocirc;","&Otilde;","&Ouml;","&times;","&Oslash;","&Ugrave;","&Uacute;","&Ucirc;","&Uuml;","&Yacute;","&THORN;","&szlig;","&agrave;","&aacute;","&acirc;","&atilde;","&auml;","&aring;","&aelig;","&ccedil;","&egrave;","&eacute;","&ecirc;","&euml;","&igrave;","&iacute;","&icirc;","&iuml;","&eth;","&ntilde;","&ograve;","&oacute;","&ocirc;","&otilde;","&ouml;","&divide;","&oslash;","&ugrave;","&uacute;","&ucirc;","&uuml;","&uuml;","&yacute;","&thorn;","&yuml;" ]
	];

	// Replace each instance of one of the above special characters with it's HTML equivelent.
	if ( html )
	{
		for ( var j = 0; j < set[0].length; j++ )
			html = html.replace( eval( "/" + set[0][j] + "/g" ), set[1][j] );
	}

	// Return the HTML or an empty string if no HTML was supplied.
	return html || "";
};

/**
 * Removes empty tags and tags with only non-breaking-spaces unlimited levels.
 *
 * @access private
 * @static
 */
CodeSweeper._removeEmptyTags = function( html )
{
	var re = /<[^(>|\/)]+>[ |	]*<\/[^>]+>/gi;
	
	while ( re.test( html ) )
	{
		html = html.replace( re, "" );
		
		while ( re.test( html ) )
			html = html.replace( re, "" );
	}
	
	return html;
};

/**
 * Replaces absolute URL's with relative urls
 * assuming the editor is in a level equal-to or above the image.
 *
 * @access private
 * @static
 */
CodeSweeper._replaceAbsoluteUrls = function( html )
{
	var docLoc = document.location.toString();
	docLoc = docLoc.substring( 0, docLoc.lastIndexOf( "/" ) + 1 );
	docLoc = docLoc.replace(/\//gi,"\\\/");
	var re = eval( "/" + docLoc + "/gi" );
	
	return html.replace( re, "" );
};

/**
 * Replace tags for better formatting
 * set: [[tag,replacement],[tag,replacm....
 *
 * @access private
 * @static
 */
CodeSweeper._replaceTags = function( set, html )
{
	var re;
	
	for ( var i = 0; i < set.length; i++ )
	{
		re   = eval( "/(<[\/]{0,1})" + set[i][0] + ">/gi" );
		html = html.replace( re, "$1" + set[i][1] + ">" );
	}
	
	return html;
};
