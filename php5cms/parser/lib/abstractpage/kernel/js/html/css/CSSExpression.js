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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


/**
 * @package html_css
 */
 
/**
 * Constructor
 *
 * @access public
 */
CSSExpression = function()
{
	this.Base = Base;
	this.Base();
};


CSSExpression.prototype = new Base();
CSSExpression.prototype.constructor = CSSExpression;
CSSExpression.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
CSSExpression.constExpression = function( x )
{
	return x;
};

/**
 * @access public
 * @static
 */
CSSExpression.simplifyCSSExpression = function()
{
	try
	{
		var ss, sl, rs, rl;
		ss = document.styleSheets;
		sl = ss.length
	
		for ( var i = 0; i < sl; i++ )
			CSSExpression.simplifyCSSBlock( ss[i] );
	}
	catch ( exc )
	{
		return Base.raiseError( "Got an error while processing CSS. The page should still work but might be a bit slower." );
	}
};

/**
 * @access public
 * @static
 */
CSSExpression.simplifyCSSBlock = function( ss )
{
	var rs, rl;
	
	// go through imports
	for ( var i = 0; i < ss.imports.length; i++ )
		CSSExpression.simplifyCSSBlock( ss.imports[i] );

	// if no constExpression we don't have to continue
	if ( ss.cssText.indexOf( "expression( CSSExpression.constExpression( " ) == -1 )
		return;

	rs = ss.rules;
	rl = rs.length;
	
	for ( var j = 0; j < rl; j++ )
		CSSExpression.simplifyCSSRule( rs[j] );
};

/**
 * @access public
 * @static
 */
CSSExpression.simplifyCSSRule = function( r )
{
	var str  = r.style.cssText;
	var str2 = str;
	var lastStr;

	// update string until the updates does not change the string
	do
	{
		lastStr = str2;
		str2 = CSSExpression.simplifyCSSRuleHelper( lastStr );
	} while ( str2 != lastStr )

	if ( str2 != str )
		r.style.cssText = str2;
};

/**
 * @access public
 * @static
 */
CSSExpression.simplifyCSSRuleHelper = function( str )
{
	var i, i2;
	i = str.indexOf( "expression( CSSExpression.constExpression(" );
	
	if ( i == -1 )
		return str;
	
	i2 = str.indexOf( " ) )", i );
	
	var hd  = str.substring( 0, i );
	var tl  = str.substring( i2 + 4 );
	var exp = str.substring( i + 43, i2 );
	var val = eval( exp );
	
	return hd + val + tl;
};
