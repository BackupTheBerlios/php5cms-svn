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
 * @package gui_toolbar
 */
 
/**
 * Constructor
 *
 * @access public
 */
Toolbar = function()
{
	this.Base = Base;
	this.Base();
	
	this.allBUTTONs = toolbar.children;
	this.maxWidth   = 0;
	
	for ( i = 0; i < this.allBUTTONs.length; i++ )
	{
		tSpan = this.allBUTTONs( i );
		
		tSpan.img     = tSpan.children( 0 );
		tSpan.oversrc = "img/" + tSpan.innerText + "_on.gif";
		tSpan.outsrc  = "img/" + tSpan.innerText + "_off.gif";

		this.maxWidth = Math.max( this.maxWidth, tSpan.offsetWidth );

		tSpan.onselectstart = function()
		{
			return false;
		}
		tSpan.onmouseover = function()
		{
			this.style.border = "1px buttonhighlight outset";
			this.img.src = this.oversrc;
		}
		tSpan.onmouseout = function()
		{
			this.style.border = "1px buttonface solid";
			this.img.src = this.outsrc;
		}
		tSpan.onmousedown = function()
		{
			this.style.border = "1px buttonhighlight inset";
		}
		tSpan.onmouseup = function()
		{
			this.style.border = "1px buttonhighlight outset";
		}
		tSpan.onclick = function()
		{
			disp.innerHTML = this.innerText + " was clicked!";
			window.focus();
		}
	}

	for ( i = 0; i < this.allBUTTONs.length; i++ )
	{
		tSpan = this.allBUTTONs( i );
		tSpan.style.pixelWidth = this.maxWidth;
	}
};


Toolbar.prototype = new Base();
Toolbar.prototype.constructor = Toolbar;
Toolbar.superclass = Base.prototype;
