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
 * Constructor
 *
 * @access public
 */
LengthConversion = function()
{
	this.Base = Base;
	this.Base();
};


LengthConversion.prototype = new Base();
LengthConversion.prototype.constructor = LengthConversion;
LengthConversion.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
LengthConversion.units = new Array(
	"Centimeters",
	"Inches",
	"Feets",
	"Yards",
	"Meters",
	"Chains",
	"Kilometers",
	"Miles"
);

/**
 * @access public
 * @static
 */
LengthConversion.num = new ExtendedArray( "4b", "5b", "8b", "8b" );
LengthConversion.num[0] = "~01~10.3937~20.03281~30.01094~40.01~50.0004971~60.00001~70.000006214~8";		// Centimeters
LengthConversion.num[1] = "~02.540~11~20.08333~30.02778~40.0254~50.001263~60.0000254~70.00001578~8";	// Inches
LengthConversion.num[2] = "~030.48~112~21~30.3333~40.3048~50.01515~60.0003048~70.0001894~8";			// Feets
LengthConversion.num[3] = "~091.44~136~23~31~40.9144~50.04545~60.0009144~70.0005682~8";					// Yards
LengthConversion.num[4] = "~0100~139.37~23.281~31.0936~41~50.04971~60.001~70.0006214~8";				// Meters
LengthConversion.num[5] = "~02012~1792~266~322~420.12~51~60.0212~70.0125~8";							// Chains
LengthConversion.num[6] = "~0100000~139370~23281~31093.6~41000~549.71~61~70.6214~8";					// Kilometers
LengthConversion.num[7] = "~0160934~163360~25280~31760~41609~580~61.609~71~8";							// Miles

/**
 * @access public
 * @static
 */
LengthConversion.get = function( val, from, to )
{
	switch ( from )
	{
		case "Centimeters":
			leni = 0;
			break;
			
		case "Inches":
			leni = 1;
			break;
			
		case "Feets":
			leni = 2;
			break;
			
		case "Yards":
			leni = 3;
			break;
			
		case "Meters":
			leni = 4;
			break;
			
		case "Chains":
			leni = 5;
			break;
			
		case "Kilometers":
			leni = 6;
			break;
			
		default:
			leni = 7; // Miles
	}
	
	switch ( to )
	{
		case "Centimeters":
			leno = 0;
			break;
			
		case "Inches":
			leno = 1;
			break;
			
		case "Feets":
			leno = 2;
			break;
			
		case "Yards":
			leno = 3;
			break;
			
		case "Meters":
			leno = 4;
			break;
			
		case "Chains":
			leno = 5;
			break;
			
		case "Kilometers":
			leno = 6;
			break;
			
		default:
			leno = 7; // Miles
	}
	
	mulstr = LengthConversion.num[leni];
	picker = "~" + leno;
	ps     = mulstr.indexOf( picker );
	
	leno++;

	picker  = "~" + leno;
	ps1     = mulstr.indexOf( picker );
	mulstr  = mulstr.substring( ( ps + 2 ), ps1 );
	ps      = ( val * mulstr );
	picker  = "";
	picker += ps;
	ps1     = picker.indexOf( "." );

	if ( ps1 > -1 )
	{
		ps      = ps + .000001;
		picker  = "";
		picker += ps;
		ps2     = picker.indexOf( "e" );

		if ( ps2 < 0 )
			picker = picker.substring( 0, ( ps1 + 6 ) );

		if ( ps2 == 0 || ps2 > 0 )
		{
			ps3 = picker.indexOf( "00000" );

			if ( ps3 > 0 )
				picker = picker.substring( 0, ps3 + 1 ) + picker.substring( ps2, picker.length );
   		}
	}
	
	return picker;
};
