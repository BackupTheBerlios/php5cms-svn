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
 * Constructor
 *
 * @access public
 */
Soundex = function()
{
	this.Base = Base;
	this.Base();
	
	this.snd_ex = new Soundex._makesoundex();
};


Soundex.prototype = new Base();
Soundex.prototype.constructor = Soundex;
Soundex.superclass = Base.prototype;

/**
 * @access public
 */
Soundex.prototype.soundex = function( word ) 
{
	var result = "";
	
	if ( !this.isSurname( word ) ) 
		return;

	var stage1 = this.collapse( word.toLowerCase() );
	result += stage1.charAt( 0 ).toUpperCase();
	result += "-";

	var stage2 = stage1.substring( 1, stage1.length );
	var count  = 0;
	
	for ( var i = 0; i < stage2.length && count < 3; i++ ) 
	{
		if ( this.snd_ex[stage2.charAt( i )] > 0 ) 
		{
			result += this.snd_ex[stage2.charAt( i )];
			count++;
		}
	}

	for (; count < 3; count++ ) 
		result += "0";
		
	return result;
};

/**
 * @access public
 */
Soundex.prototype.isSurname = function( name ) 
{
	if ( name == "" || name == null ) 
		return false;
	
	return true;
};

/**
 * @access public
 */
Soundex.prototype.collapse = function( surname ) 
{
	if ( surname.length <= 1 ) 
		return surname;
		
	var right = this.collapse( surname.substring( 1, surname.length ) );

	if ( this.snd_ex[surname.charAt( 0 )] == this.snd_ex[right.charAt( 0 )] )
		return surname.charAt( 0 ) + right.substring( 1, right.length );

	return surname.charAt( 0 ) + right;
};


/**
 * @access private
 * @static
 */
Soundex._makesoundex = function() 
{
	this.a = -1;
	this.b =  1;
	this.c =  2;
	this.d =  3;
	this.e = -1;
	this.f =  1;
	this.g =  2;
	this.h = -1;
	this.i = -1;
	this.j =  2;
	this.k =  2;
	this.l =  4;
	this.m =  5;
	this.n =  5;
	this.o = -1;
	this.p =  1;
	this.q =  2;
	this.r =  6;
	this.s =  2;
	this.t =  3;
	this.u = -1;
	this.v =  1;
	this.w = -1;
	this.x =  2;
	this.y = -1;
	this.z =  2;
};
