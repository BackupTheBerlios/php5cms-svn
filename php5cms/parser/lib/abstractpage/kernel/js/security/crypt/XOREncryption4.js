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
 * @package security_crypt
 */
 
/**
 * Constructor
 *
 * @access public
 */
XOREncryption4 = function()
{
	this.Base = Base;
	this.Base();
};


XOREncryption4.prototype = new Base();
XOREncryption4.prototype.constructor = XOREncryption4;
XOREncryption4.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
XOREncryption4.encrypt = function( str, pwd )
{
	if ( pwd == null )
		return null;

	var prand = "";

	for ( var i = 0; i < pwd.length; i++ )
		prand += pwd.charCodeAt( i ).toString();
  
	var sPos = Math.floor( prand.length / 5 );
	var mult = parseInt( prand.charAt( sPos ) + prand.charAt( sPos * 2 ) + prand.charAt( sPos * 3 ) + prand.charAt( sPos * 4 ) + prand.charAt (sPos * 5 ) );
	var incr = Math.ceil( pwd.length / 2 );
	var modu = Math.pow( 2, 31 ) - 1;

	if ( mult < 2 )
		return Base.raiseError( "Algorithm cannot find a suitable hash. Please choose a different password. Possible considerations are to choose a more complex or longer password." );

	var salt = Math.round( Math.random() * 1000000000 ) % 100000000;
	prand += salt;
	
	while ( prand.length > 10 )
		prand = ( parseInt( prand.substring( 0, 10 ) ) + parseInt( prand.substring( 10, prand.length ) ) ).toString();
  
	prand = ( mult * prand + incr ) % modu;

	var enc_chr = "";
	var enc_str = "";

	for ( var i = 0; i < str.length; i++ )
	{
		enc_chr = parseInt( str.charCodeAt( i ) ^ Math.floor( ( prand / modu ) * 255 ) );
		
		if ( enc_chr < 16 )
			enc_str += "0" + enc_chr.toString( 16 );
		else
			enc_str += enc_chr.toString( 16 );

		prand = ( mult * prand + incr ) % modu;
	}

	salt = salt.toString( 16 );

	while ( salt.length < 8 )
		salt = "0" + salt;

	enc_str += salt;
	return enc_str;
};

/**
 * Note: min length for str = 8
 *
 * @access public
 * @static
 */
XOREncryption4.decrypt = function( str, pwd )
{
	if ( str == null || str.length < 8 )
		return;
		
	if ( pwd == null )
		return;
 
	var prand = "";

	for ( var i = 0; i < pwd.length; i++ )
		prand += pwd.charCodeAt( i ).toString();

	var sPos = Math.floor( prand.length / 5 );
	var mult = parseInt( prand.charAt( sPos ) + prand.charAt( sPos * 2 ) + prand.charAt( sPos * 3 ) + prand.charAt( sPos * 4 ) + prand.charAt( sPos * 5 ) );
	var incr = Math.round( pwd.length / 2 );
	var modu = Math.pow( 2, 31 ) - 1;
	var salt = parseInt( str.substring( str.length - 8, str.length ), 16 );
	str = str.substring( 0, str.length - 8 );

	prand += salt;

	while ( prand.length > 10 )
		prand = ( parseInt( prand.substring( 0, 10 ) ) + parseInt( prand.substring( 10, prand.length ) ) ).toString();
  
	prand = ( mult * prand + incr ) % modu;
	var enc_chr = "";
	var enc_str = "";

	for ( var i = 0; i < str.length; i += 2 )
	{
		enc_chr  = parseInt( parseInt( str.substring( i, i + 2 ), 16 ) ^ Math.floor( ( prand / modu ) * 255 ) );
		enc_str += String.fromCharCode( enc_chr );
		prand = ( mult * prand + incr ) % modu;
	}

	return enc_str;
};
