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
DockBar = function()
{
	this.Base = Base;
	this.Base();
};


DockBar.prototype = new Base();
DockBar.prototype.constructor = DockBar;
DockBar.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
DockBar.floatWidth = 200;

/**
 * Note: extra 4 for the border
 * @access public
 * @static
 */
DockBar.floatHeight = 24;

/**
 * @access public
 * @static
 */
DockBar.snapHorizSize = 50;

/**
 * @access public
 * @static
 */
DockBar.snapVertSize = 20;

/**
 * when docked to the left or right
 * @access public
 * @static
 */
DockBar.horizDockWidth = 50;

/**
 * @access public
 * @static
 */
DockBar.vertDockHeight = 20;

/**
 * start at this position
 * @access public
 * @static
 */
DockBar.toolbarPos = "top";

/**
 * @access public
 * @static
 */
DockBar.errorInSetup = false;

/**
 * @access public
 * @static
 */
DockBar.dragging = false;


/**
 * @access public
 * @static
 */
DockBar.fixSize = function()
{
	if ( toolbar == null || handle == null || contentDiv == null )
	{
		if ( !DockBar.errorInSetup )
		{
			DockBar.errorInSetup = true;
			return Base.raiseError( "The setup of this page is not correct." );
		}
	}
	
	switch ( DockBar.toolbarPos )
	{
		case "top" :
			toolbar.style.border    = "0 solid buttonface";
			toolbar.style.width     = "100%";
			toolbar.style.height    = DockBar.vertDockHeight;
			toolbar.style.top       = 0;
			toolbar.style.left      = 0;
			
			contentDiv.style.top    = DockBar.vertDockHeight;
			contentDiv.style.left   = 0;
			contentDiv.style.height = document.body.clientHeight - DockBar.vertDockHeight;
			contentDiv.style.width  = "100%";

			handle.style.height     = DockBar.vertDockHeight - 2;
			handle.style.width      = 3;
			
			break;
	
		case "bottom" :
			toolbar.style.border    = "0 solid buttonface";
			toolbar.style.width     = "100%";
			toolbar.style.height    = DockBar.vertDockHeight;
			toolbar.style.top       = document.body.clientHeight - DockBar.vertDockHeight;
			toolbar.style.left      = 0;
		
			contentDiv.style.top    = 0;
			contentDiv.style.left   = 0;
			contentDiv.style.height = document.body.clientHeight - DockBar.vertDockHeight;
			contentDiv.style.width  = "100%";
	
			handle.style.height     = DockBar.vertDockHeight - 2;
			handle.style.width      = 3;
			
			break;
	
		case "left" :
			toolbar.style.border    = "0 solid buttonface";
			toolbar.style.width     = DockBar.horizDockWidth;
			toolbar.style.height    = "100%";
			toolbar.style.top       = 0;
			toolbar.style.left      = 0;
	
			contentDiv.style.top    = 0;
			contentDiv.style.left   = DockBar.horizDockWidth;
			contentDiv.style.height = "100%";
			contentDiv.style.width  = document.body.clientWidth - DockBar.horizDockWidth;
			
			handle.style.height     = 3;
			handle.style.width      = DockBar.horizDockWidth -2;
			
			break;

		case "right" :
			toolbar.style.border    = "0 solid buttonface";
			toolbar.style.width     = DockBar.horizDockWidth;
			toolbar.style.height    = "100%";
			toolbar.style.top       = 0;
			toolbar.style.left      = document.body.clientWidth - DockBar.horizDockWidth;
			
			contentDiv.style.top    = 0;
			contentDiv.style.left   = 0;
			contentDiv.style.height = "100%";
			contentDiv.style.width  = document.body.clientWidth - DockBar.horizDockWidth;
		
			handle.style.height     = 3;
			handle.style.width      = DockBar.horizDockWidth -2;
			
			break;
		
		case "float" :
			toolbar.style.width     = DockBar.floatWidth;
			toolbar.style.height    = DockBar.floatHeight;
			toolbar.style.border    = "2px outset white";
			
			contentDiv.style.top    = 0;
			contentDiv.style.left   = 0;
			contentDiv.style.height = "100%";
			contentDiv.style.width  = "100%";
	
			handle.style.height     = DockBar.floatHeight - 6;
			handle.style.width      = 3;
	}
	
	if ( toolbar.ondock != null )
	{
		if ( typeof( toolbar.ondock ) == "function" )
			toolbar.ondock();
		else
			eval( toolbar.ondock );
	}
};

/**
 * @access public
 * @static
 */
DockBar.down = function()
{
	if ( window.event.srcElement.id == "handle" )
	{
		DockBar.ty = ( window.event.clientY - toolbar.style.pixelTop  );
		DockBar.tx = ( window.event.clientX - toolbar.style.pixelLeft );

		DockBar.dragging = true;
		
		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	}
	else
	{
		DockBar.dragging = false;
	}
};

/**
 * @access public
 * @static
 */
DockBar.up = function()
{
	DockBar.dragging = false;
};

/**
 * @access public
 * @static
 */
DockBar.move = function()
{
	if ( DockBar.dragging )
	{
		// top
		if ( ( window.event.clientY ) <= DockBar.snapVertSize && DockBar.toolbarPos != "left" && DockBar.toolbarPos != "right" )
		{
			DockBar.toolbarPos = "top";
			DockBar.fixSize();
		}
		// bottom
		else if ( window.event.clientY >= document.body.clientHeight - DockBar.snapVertSize && DockBar.toolbarPos != "left" && DockBar.toolbarPos != "right" )
		{
			DockBar.toolbarPos = "bottom";
			DockBar.fixSize();
		}
		// left
		else if ( window.event.clientX <= DockBar.snapHorizSize && DockBar.toolbarPos != "top" && DockBar.toolbarPos != "bottom" )
		{
			DockBar.toolbarPos = "left";
			DockBar.fixSize();
		}
		// right
		else if ( window.event.clientX >= document.body.clientWidth - DockBar.snapHorizSize && DockBar.toolbarPos != "top" && DockBar.toolbarPos != "bottom" )
		{
			DockBar.toolbarPos = "right";
			DockBar.fixSize();
		}
		else
		{
			toolbar.style.left = window.event.clientX;
			toolbar.style.top  = window.event.clientY - DockBar.ty;
			DockBar.toolbarPos = "float";
			
			DockBar.fixSize();
		}

		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	}
};
