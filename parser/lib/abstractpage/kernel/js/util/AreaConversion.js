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
AreaConversion = function()
{
	this.Base = Base;
	this.Base();
};


AreaConversion.prototype = new Base();
AreaConversion.prototype.constructor = AreaConversion;
AreaConversion.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
AreaConversion.units = new Array(
	"Square Meters",
	"Square Inches",
	"Square Feet",
	"Square Yards",
	"Square Rods",
	"Square Chains",
	"Roods",
	"Acres",
	"Square Miles"
);

/**
 * @access public
 * @static
 */
AreaConversion.num = new ExtendedArray( "4b", "5b", "8b", "8b" );
AreaConversion.num[0] = "~01~11550~210.76~31.196~40.0395~50.002471~60.0009884~70.0002471~80.0000003861~9";							// Square Meters
AreaConversion.num[1] = "~00.0006452~11~20.006944~30.0007716~40.00002551~50.000001594~60.0000006377~70.0000001594~82.291e-10~9";	// Square Inches
AreaConversion.num[2] = "~00.09290~1144~21~30.1111~40.003673~50.0002296~60.00009183~70.00002296~83.587e-8~9";						// Square Feet
AreaConversion.num[3] = "~00.8361~11296~29~31~40.03306~50.002066~60.0008264~70.0002066~83.228e-7~9";								// Square Yards
AreaConversion.num[4] = "~025.29~139204~2272.25~330.25~41~50.0625~60.025~70.00625~89.766e-6~9";										// Square Rods
AreaConversion.num[5] = "~0404.7~1627264~24356~3484~416~51~60.4~70.1~80.00015625~9";												// Square Chains
AreaConversion.num[6] = "~01012~11568160~210890~31210~440~52.5~61~70.25~80.000390625~9";											// Roods
AreaConversion.num[7] = "~04047~16272640~243560~34840~4160~510~64~71~80.0015625~9";													// Acres
AreaConversion.num[8] = "~02589988~14013355318~227878400~33097600~4102400~56400~62560~7640~81~9";									// Square Miles

/**
 * @access public
 * @static
 */
AreaConversion.get = function( val, from, to )
{
	switch ( from )
	{
		case "Square Meters":
			leni = 0;
			break;
			
		case "Square Inches":
			leni = 1;
			break;
			
		case "Square Feet":
			leni = 2;
			break;
			
		case "Square Yards":
			leni = 3;
			break;
			
		case "Square Rods":
			leni = 4;
			break;
			
		case "Square Chains":
			leni = 5;
			break;
			
		case "Roods":
			leni = 6;
			break;

		case "Acres":
			leni = 7;
			break;
						
		default:
			leni = 8; // Square Miles
	}
	
	switch ( to )
	{
		case "Square Meters":
			leno = 0;
			break;
			
		case "Square Inches":
			leno = 1;
			break;
			
		case "Square Feet":
			leno = 2;
			break;
			
		case "Square Yards":
			leno = 3;
			break;
			
		case "Square Rods":
			leno = 4;
			break;
			
		case "Square Chains":
			leno = 5;
			break;
			
		case "Roods":
			leno = 6;
			break;
			
		case "Acres":
			leno = 7;
			break;
				
		default:
			leno = 8; // Square Miles
	}
	
	mulstr = AreaConversion.num[leni];
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
