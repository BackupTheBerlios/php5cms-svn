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
 * @package gui
 */
 
/**
 * Constructor
 *
 * @access public
 */
ColorPicker = function()
{
	this.Base = Base;
	this.Base();
};


ColorPicker.prototype = new Base();
ColorPicker.prototype.constructor = ColorPicker;
ColorPicker.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
ColorPicker.ns6X = 0;

/**
 * @access public
 * @static
 */
ColorPicker.ns6Y = 0;

/**
 * @access public
 * @static
 */
ColorPicker.actualColor = "";

/**
 * @access public
 * @static
 */
ColorPicker.onselectcolor = new Function;

/**
 * @access public
 * @static
 */
ColorPicker.pixel_path = "../img/misc/pixel.gif";


/**
 * @access public
 * @static
 */
ColorPicker.init = function()
{
	if ( Browser.ns6 )
	{
  		cpicker = document.getElementById( "pickcolour" );
  		cpicker.addEventListener( "click", ColorPicker.ns6Get, false );
	}
};

/**
 * @access public
 * @static
 */
ColorPicker.ns6Get = function( e )
{
	ColorPicker.ns6X = e.layerX;
	ColorPicker.ns6Y = e.layerY;
	
	ColorPicker.showColor( null, null );
};

/**
 * @access public
 * @static
 */
ColorPicker.changeColor = function( wcolour )
{
	if ( Browser.ie )
	{
		document.all["pickcolour"].style.backgroundColor = '#' + wcolour;
		document.all["colorbox"].style.visibility = 'hidden';
	}
	
	if ( Browser.ns4 )
	{
		document.layers["pickcolour"].bgColor  = '#' + wcolour;
		document.layers["colorbox"].visibility = false;
	}
	
	if ( Browser.ns6 )
	{
		document.getElementById("pickcolour").style.backgroundColor = '#' + wcolour;
		document.getElementById("colorbox").style.visibility = 'hidden';
	}
	
	ColorPicker.actualColor = wcolour;
	ColorPicker.onselectcolor();
};

/**
 * @access public
 * @static
 */
ColorPicker.showColor = function( posX, posY )
{
	if ( Browser.ie )
	{
		e = window.event;
		
		with ( document.all["colorbox"] )
		{
			style.left = e.clientX;
			style.top  = e.clientY;
			
			style.visibility = 'visible';
		}
	}
	
	if ( Browser.ns4 )
	{
		with ( document.layers["colorbox"] )
		{
			top  = posY + 18;
			left = posX + 17;
			
			visibility = true;
		}
	}
	
	if ( Browser.ns6 )
	{
		with ( document.getElementById( "colorbox" ).style )
		{
			top  = ColorPicker.ns6Y;
			left = ColorPicker.ns6X;
			
			visibility = 'visible';
		}
	}
};

/**
 * @access public
 * @static
 */
ColorPicker.getColor = function()
{
	return ColorPicker.actualColor;
}

/**
 * @access public
 * @static
 */
ColorPicker.getGreyscaleTable = function()
{
	var str = '';
	
	str += '<table border="0" cellpadding="0" cellspacing="0" width="112" bgcolor="#000000"><tr><td>\n';
	str += '<table border="0" bordercolor="#000000" width="112" cellpadding="0" cellspacing="1"><tbody><tr>\n';
		
	var a = new Array();
				
	a['0']  = "0";
	a['1']  = "1";
	a['2']  = "2";
	a['3']  = "3";
	a['4']  = "4";
	a['5']  = "5";
	a['6']  = "6";
	a['7']  = "7";
	a['8']  = "8";
	a['9']  = "9";
	a['10'] = "a";
	a['11'] = "b";
	a['12'] = "c";
	a['13'] = "d";
	a['14'] = "e";
	a['15'] = "f";
				
	for ( var i = 0; i <= 15; i++ )
	{
		if ( i > 0 )
			str += '</tr>\n<tr>\n';

		for ( var n = 0; n <= 15; n++ )
		{
			color = a[i] + a[n] + a[i] + a[n] + a[i] + a[n];
			str  += '<td bgcolor="#' + color + '" onMouseOver="status=\'#' + color + '\';return true" width="7" class="text"><a href="#" onClick="ColorPicker.changeColor(\'' + color + '\')"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="a' + i + n + '" border="0"></td>\n';
		}
	}

	str += '</tr></tbody></table></table></td></tr></table>';
	return str;
};

/**
 * @access public
 * @static
 */
ColorPicker.getWebsafeTable = function()
{
	var str = '';
	
	str += '<table border="0" cellpadding="0" cellspacing="0" width="252" bgcolor="#000000"><tr><td>\n';
	str += '<table border="0" bordercolor="#000000" cellpadding="0" cellspacing="1"><tbody><tr>\n';

	var c = new Array();
				
	c[1] = "FF";
	c[2] = "CC";
	c[3] = "99";
	c[4] = "66";
	c[5] = "33";
	c[6] = "00";
	
	d = 0;
				
	for ( var i = 1; i <= 6; i++ )
	{
		if ( i > 1 )
			str += '</tr>\n<tr>\n';

		for ( var m = 1; m <= 6; m++ )
		{		
			for ( var n = 1; n <= 6; n++ )
			{	
				d++;
				
				color = c[i] + c[m] + c[n];
				str  += '<td bgcolor="#' + color + '" onMouseOver="status=\'#' + color + '\';return true" width="7" class="text"><a href="#" onClick="ColorPicker.changeColor(\'' + color + '\')"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="' + d + '" border="0"></td>\n';
			}
		}
	}
		
	str += '</tr></tbody></table></table></td></tr></table>';
	return str;
};

/**
 * @access public
 * @static
 */
ColorPicker.apGetGreyscaleTable = function()
{
	var str = '';
	
	str += '<table border="0" cellpadding="0" cellspacing="0" width="112" bgcolor="#000000"><tr><td>\n';
	str += '<table border="0" bordercolor="#000000" width="112" cellpadding="0" cellspacing="1"><tbody><tr>\n';
		
	var a = new Array();
				
	a['0']  = "0";
	a['1']  = "1";
	a['2']  = "2";
	a['3']  = "3";
	a['4']  = "4";
	a['5']  = "5";
	a['6']  = "6";
	a['7']  = "7";
	a['8']  = "8";
	a['9']  = "9";
	a['10'] = "a";
	a['11'] = "b";
	a['12'] = "c";
	a['13'] = "d";
	a['14'] = "e";
	a['15'] = "f";
				
	for ( var i = 0; i <= 15; i++ )
	{
		if ( i > 0 )
			str += '</tr>\n<tr>\n';

		for ( var n = 0; n <= 15; n++ )
		{
			color = a[i] + a[n] + a[i] + a[n] + a[i] + a[n];
			str  += '<td bgcolor="#' + color + '" width="7" class="text"><a href="#" onMouseOver="display.innerHTML=\'#' + color + '\'" onClick="window.opener.Main.setColorByRGB(\'' + color + '\');window.close();"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="a' + i + n + '" border="0"></td>\n';
		}
	}

	str += '</tr></tbody></table></table></td></tr></table>';
	return str;
};

/**
 * @access public
 * @static
 */
ColorPicker.apGetWebsafeTable = function()
{
	var str = '';
	
	str += '<table border="0" cellpadding="0" cellspacing="0" width="252" bgcolor="#000000"><tr><td>\n';
	str += '<table border="0" bordercolor="#000000" cellpadding="0" cellspacing="1"><tbody><tr>\n';

	var c = new Array();
				
	c[1] = "FF";
	c[2] = "CC";
	c[3] = "99";
	c[4] = "66";
	c[5] = "33";
	c[6] = "00";
	
	d = 0;
				
	for ( var i = 1; i <= 6; i++ )
	{
		if ( i > 1 )
			str += '</tr>\n<tr>\n';

		for ( var m = 1; m <= 6; m++ )
		{		
			for ( var n = 1; n <= 6; n++ )
			{	
				d++;
				
				color = c[i] + c[m] + c[n];
				str  += '<td bgcolor="#' + color + '" width="7" class="text"><a href="#" onMouseOver="display.innerHTML=\'#' + color + '\'" onClick="window.opener.Main.setColorByRGB(\'' + color + '\');window.close();"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="' + d + '" border="0"></td>\n';
			}
		}
	}
		
	str += '</tr></tbody></table></table></td></tr></table>';
	return str;
};

/**
 * @access public
 * @static
 */
ColorPicker.apGetCustomTable = function( arr )
{
	if ( arr == null )
		return '';
	
	var d = 0;	
	var str = '';
	
	str += '<table border="0" cellpadding="0" cellspacing="0" bgcolor="#000000"><tr><td>\n';
	str += '<table border="0" bordercolor="#000000" cellpadding="0" cellspacing="1"><tbody><tr>\n';

	switch ( arr.length )
	{
		case 16:
			for ( var i = 1; i <= 8; i++ )
			{
				if ( i > 1 )
					str += '</tr>\n<tr>\n';

				for ( var n = 1; n <= 2; n++ )
				{	
					color = arr[d];
					str  += '<td bgcolor="#' + color + '" width="7"><a href="#" onMouseOver="display.innerHTML=\'#' + color + '\'" onClick="window.opener.Main.setColorByRGB(\'' + color + '\');window.close();"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="' + d + '" border="0"></a></td>\n';
			
					d++;
				}
			}
			
			break;
			
		case 216:
			for ( var i = 1; i <= 6; i++ )
			{
				if ( i > 1 )
					str += '</tr>\n<tr>\n';

				for ( var m = 1; m <= 6; m++ )
				{		
					for ( var n = 1; n <= 6; n++ )
					{	
						color = arr[d];
						str  += '<td bgcolor="#' + color + '" width="7"><a href="#" onMouseOver="display.innerHTML=\'#' + color + '\'" onClick="window.opener.Main.setColorByRGB(\'' + color + '\');window.close();"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="' + d + '" border="0"></a></td>\n';
			
						d++;
					}
				}
			}
			
			break;
			
		case 256:
			for ( var i = 1; i <= 8; i++ )
			{
				if ( i > 1 )
					str += '</tr>\n<tr>\n';

				for ( var m = 1; m <= 8; m++ )
				{		
					for ( var n = 1; n <= 4; n++ )
					{	
						color = arr[d];
						str  += '<td bgcolor="#' + color + '" width="7"><a href="#" onMouseOver="display.innerHTML=\'#' + color + '\'" onClick="window.opener.Main.setColorByRGB(\'' + color + '\');window.close();"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="' + d + '" border="0"></a></td>\n';
			
						d++;
					}
				}
			}
			
			break;

		default:
			for ( var i = 1; i <= arr.length / 8; i++ )
			{
				if ( i > 1 )
					str += '</tr>\n<tr>\n';

				if ( arr[d] )
				{
					for ( var n = 1; n <= arr.length / 8; n++ )
					{	
						color = arr[d];
					
						if ( color )
							str  += '<td bgcolor="#' + color + '" width="7"><a href="#" onMouseOver="display.innerHTML=\'#' + color + '\'" onClick="window.opener.Main.setColorByRGB(\'' + color + '\');window.close();"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="' + d + '" border="0"></a></td>\n';
						else
							str  += '<td width="7"><img src="' + ColorPicker.pixel_path + '" width="7" height="7" name="' + d + '" border="0"></td>\n'
			
						d++;
					}
				}
			}
			
			break;
	}
	
	str += '</tr></tbody></table></table></td></tr></table>';
	return str;
};
