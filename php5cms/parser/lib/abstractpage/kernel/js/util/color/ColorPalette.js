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
 * @package util_color
 */
 
/**
 * Constructor
 *
 * @access public
 */
ColorPalette = function()
{
	this.Base = Base;
	this.Base();
};


ColorPalette.prototype = new Base();
ColorPalette.prototype.constructor = ColorPalette;
ColorPalette.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
ColorPalette.html = new Array(
	"aliceblue", 
	"antiquewhite", 
	"aqua", 
	"aquamarine", 
	"azure", 
	"beige", 
	"bisque", 
	"black",
	"blanchedalmond", 
	"blue", 
	"blueviolet", 
	"brown", 
	"burlywood", 
	"cadetblue", 
	"chartreuse",
	"chocolate", 
	"coral", 
	"cornflowerblue", 
	"cornsilk", 
	"crimson", 
	"cyan", 
	"darkblue", 
	"darkcyan",
	"darkgoldenrod", 
	"darkgray", 
	"darkgreen", 
	"darkkhaki", 
	"darkmagenta", 
	"darkolivegreen",
	"darkorange", 
	"darkorchid", 
	"darkred", 
	"darksalmon", 
	"darkseagreen", 
	"darkslateblue",
	"darkslategray", 
	"darkturquoise", 
	"darkviolet", 
	"deeppink", 
	"deepskyblue", 
	"dimgray",
	"dodgerblue", 
	"firebrick", 
	"floralwhite", 
	"forestgreen", 
	"fuchsia", 
	"gainsboro", 
	"ghostwhite",
	"gold", 
	"goldenrod", 
	"gray", 
	"green", 
	"greenyellow", 
	"honeydew", 
	"hotpink", 
	"indianred",
	"indigo", 
	"ivory", 
	"khaki", 
	"lavender", 
	"lavenderblush", 
	"lawngreen", 
	"lemonchiffon",
	"lightblue", 
	"lightcoral", 
	"lightcyan",
	"lightgoldenrodyellow",
	"lightgreen", 
	"lightgray",
	"lightpink", 
	"lightsalmon", 
	"lightseagreen", 
	"lightskyblue", 
	"lightslategray", 
	"lightsteelblue",
	"lightyellow", 
	"lime", 
	"limegreen", 
	"linen", 
	"magenta", 
	"maroon", 
	"mediumaquamarine",
	"mediumblue", 
	"mediumorchid", 
	"mediumpurple", 
	"mediumseagreen", 
	"mediumslateblue", 
	"mediumspringgreen", 
	"mediumturquoise", 
	"mediumvioletred", 
	"midnightblue", 
	"mintcream",
	"mistyrose", 
	"moccasin", 
	"navajowhite", 
	"navy", 
	"oldlace", 
	"olive", 
	"olivedrab", 
	"orange",
	"orangered", 
	"orchid", 
	"palegoldenrod", 
	"palegreen", 
	"paleturquoise", 
	"palevioletred",
	"papayawhip", 
	"peachpuff", 
	"peru", 
	"pink", 
	"plum", 
	"powderblue", 
	"purple", 
	"red", 
	"rosybrown",
	"royalblue", 
	"saddlebrown", 
	"salmon", 
	"sandybrown", 
	"seagreen", 
	"seashell", 
	"sienna",
	"silver", 
	"skyblue",
	"slateblue", 
	"slategray", 
	"snow", 
	"springgreen",
	"steelblue", 
	"tan",
	"teal", 
	"thistle", 
	"tomato", 
	"turquoise", 
	"violet", 
	"wheat", 
	"white", 
	"whitesmoke",
	"yellow", 
	"yellowgreen"
);

/**
 * @access public
 * @static
 */
ColorPalette.winsys = new Array(
	"activeborder", 
	"activecaption", 
	"appworkspace", 
	"background", 
	"buttonface", 
	"buttonhighlight",
	"buttonshadow", 
	"buttontext", 
	"captiontext", 
	"graytext", 
	"highlight", 
	"highlighttext",
	"inactiveborder", 
	"inactivecaption", 
	"inactivecaptiontext", 
	"infobackground", 
	"infotext",
	"menu", 
	"menutext", 
	"scrollbar", 
	"threeddarkshadow", 
	"threedface", 
	"threedhighlight",
	"threedlightshadow", 
	"threedshadow", 
	"window", 
	"windowframe", 
	"windowtext"
);

/**
 * @access public
 * @static
 */
ColorPalette.isHTMLColor = function( col )
{
	for ( var i in ColorPalette.html )
	{
		if ( ColorPalette.html[i] == col )
			return true;
	}
	
	return false;
};

/**
 * @access public
 * @static
 */
ColorPalette.isWinColor = function( col )
{
	for ( var i in ColorPalette.winsys )
	{
		if ( ColorPalette.winsys[i] == col )
			return true;
	}
	
	return false;
};

/**
 * @access public
 * @static
 */
ColorPalette.hasColor = function( col )
{
	if ( ColorPalette.isHTMLColor( col ) || ColorPalette.isWinColor( col ) )
		return true;
	else
		return false;
};
