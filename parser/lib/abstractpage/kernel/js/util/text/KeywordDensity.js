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
KeywordDensity = function()
{
	this.Base = Base;
	this.Base();
};


KeywordDensity.prototype = new Base();
KeywordDensity.prototype.constructor = KeywordDensity;
KeywordDensity.superclass = Base;

/**
 * @access public
 */
KeywordDensity.prototype.check = function( bodyt, keyword )
{
	if ( bodyt == null || bodyt == "" )
		return 0;
	
	index    = 1;
	pindex   = 0;
	endwhile = 0
	bodyl    = bodyt.toLowerCase()
	finalfs  = bodyl.indexOf( ".", bodyl.length - 1 );

	if ( finalfs >= 0 )
		bodyl = bodyl.substring( 0, bodyl.length - 1 );

	while ( index >= 0 )
	{
		index = bodyl.indexOf( "<", 0 );
		
		if ( index >= 0 )
		{
			pindex  = bodyl.indexOf( ">", index );
			bodyank = bodyl.substring( index, pindex + 1 );
			bodyl   = bodyl.replace( bodyank, " " );
		}
	}

	// then, these, they, this, was, were, with.
	bodyl = bodyl.replace( "and",   " " );
	bodyl = bodyl.replace( "but",   " " );
	bodyl = bodyl.replace( "for",   " " );
	bodyl = bodyl.replace( "from",  " " );
	bodyl = bodyl.replace( "here",  " " );
	bodyl = bodyl.replace( "her",   " " );
	bodyl = bodyl.replace( "his",   " " );
	bodyl = bodyl.replace( "how",   " " );
	bodyl = bodyl.replace( "not",   " " );
	bodyl = bodyl.replace( "than",  " " );
	bodyl = bodyl.replace( "that",  " " );
	bodyl = bodyl.replace( "the",   " " );
	bodyl = bodyl.replace( "them",  " " );
	bodyl = bodyl.replace( "then",  " " );
	bodyl = bodyl.replace( "these", " " );
	bodyl = bodyl.replace( "they",  " " );
	bodyl = bodyl.replace( "this",  " " );
	bodyl = bodyl.replace( "was",   " " );
	bodyl = bodyl.replace( "were",  " " );
	bodyl = bodyl.replace( "with",  " " );
	bodyl = bodyl.replace( ". ",    " " );
	bodyl = bodyl.replace( "@ ",    " " );

	rm_ext_char = "";
	real_chr    = "";

	for ( i = 0; i < bodyl.length; i++ )
	{
		one_chr = bodyl.charCodeAt( i );

		if ( ( one_chr >= 65 && one_chr <= 90 ) || ( one_chr >= 97 && one_chr <= 122 ) || ( one_chr >= 48 && one_chr <= 57 ) )
			real_chr = bodyl.charAt( i );
		else
			real_chr = " ";
	
		rm_ext_char = rm_ext_char + real_chr;
	}

	bodyl     = rm_ext_char.split( " " );
	rm_s_d    = "";
	full_text = "";
	count     = 0;

	for ( i = 0; i < bodyl.length; i++ )
	{
		rm_s_d = bodyl[i];

		if ( rm_s_d.length > 2 )
		{
			count = count + 1;
			full_text = full_text + " " + rm_s_d;
		}
	}

	if ( keyword != "" )
	{
		kf          = keyword;
		rm_ext_char = "";
		real_chr    = "";
		
		for ( i = 0; i < kf.length; i++ )
		{
			one_chr = kf.charCodeAt( i );

			if ( ( one_chr >= 65 && one_chr <= 90 ) || ( one_chr >= 97 && one_chr <= 122 ) || ( one_chr >= 48 && one_chr <= 57 ) )
				real_chr = kf.charAt( i );
			else
				real_chr = " ";

			rm_ext_char = rm_ext_char + real_chr;
		}

		kf = rm_ext_char;
		kslen = kf.split( " " );
		
		if ( kslen.length > 1 )
		{
			kf = kf.toLowerCase();
			countkf = 0.1;
			kftotal = 0;

			while ( countkf >= 0 )
			{
				if ( countkf >= 0.1 && countkf < 0.2 )
				{
					countkf = full_text.indexOf( kf, 0 );
				}
				else
				{
					end = countkf + kf.length;
					countkf = full_text.indexOf( kf, end );
				}

				if ( countkf >= 0 )
					kftotal = kftotal + 1;
			}
			
			if ( kslen.length > count )
				return 0;
			else
				return this._math_round( kftotal / ( count + 1 - kslen.length ) * 100, 1 );
		}
		else
		{
			if ( keyword != "" )
			{
				k1 = keyword;
				kl = k1.toLowerCase();
				ks = k1 + "s";
				countk1 = 0;
				
				for ( i = 0; i < bodyl.length; i++ )
				{
					rm_s_d = bodyl[i];

					if ( rm_s_d.length > 1 )
					{
						if ( rm_s_d == kl || rm_s_d == ks )
							countk1 = countk1 + 1;
  					}
				}

				return this._math_round( countk1 / count * 100, 1 );
			}
		}
	}
	
	return 0;
};


// private methods

/**
 * @access private
 */
KeywordDensity.prototype._math_round = function( num, places ) 
{
	if ( places > 0 ) 
	{
		if ( ( num.toString().length - num.toString().lastIndexOf( '.' ) ) > ( places + 1 ) ) 
		{
			var rounder = Math.pow( 10, places );
			return Math.round( num * rounder ) / rounder;
		}
		else 
		{
			return num;
		}
	}
	else 
	{
		return Math.round( num );
	}
};
