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
 * @package gui_menu_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
MenuBar = function()
{
	this.Menu = Menu;
	this.Menu();
	
	this.items          = [];
	this.parentMenu     = null;
	this.parentMenuItem = null;
	this.shownSubMenu   = null;
	
	this._aboutToShowSubMenu = false;
	this.id = document.uniqueID;
	this.active = false;
};


MenuBar.prototype = new Menu();
MenuBar.prototype.constructor = MenuBar;
MenuBar.superclass = Menu.prototype;

/**
 * @access public
 */
MenuBar.prototype.toHtml = function()
{
	var items = this.items;
	var l = items.length;
	var itemsHtml = new Array( l );
	
	for ( var i = 0; i < l; i++ )
		itemsHtml[i] = items[i].toHtml();
		
	return "<div class=\"menu-bar\" id=\"" + this.id + "\">" + itemsHtml.join( "" ) + "</div>";
};

/**
 * @access public
 */
MenuBar.prototype.createPopup = function()
{
};

/**
 * @access public
 */
MenuBar.prototype.getPopup= function()
{
};

/**
 * @access public
 */
MenuBar.prototype.drawMenu = function()
{
};

/**
 * @access public
 */
MenuBar.prototype.getDocument = function()
{
	return document;
};

/**
 * @access public
 */
MenuBar.prototype.show = function( left, top, w, h )
{
};

/**
 * @access public
 */
MenuBar.prototype.isShown = function()
{
	return true;
};

/**
 * @access public
 */
MenuBar.prototype.fixSize = function()
{
};

/**
 * @access public
 */
MenuBar.prototype.getWidth = function()
{
	return this._htmlElement.offsetWidth;		
};

/**
 * @access public
 */
MenuBar.prototype.getHeight = function()
{
	return this._htmlElement.offsetHeight;
};

/**
 * @access public
 */
MenuBar.prototype.getPreferredWidth = function()
{
	var el = this._htmlElement;
	el.runtimStyle.whiteSpace = "nowrap";
	var sw = el.scrollWidth;
	el.runtimStyle.whiteSpace = "";

	return sw + parseInt( el.currentStyle.borderLeftWidth  ) +
				parseInt( el.currentStyle.borderRightWidth );
};

/**
 * @access public
 */
MenuBar.prototype.getPreferredHeight = function()
{
	var el = this._htmlElement;
	el.runtimStyle.whiteSpace = "nowrap";
	var sw = el.scrollHeight;
	el.runtimStyle.whiteSpace = "";

	return sw + parseInt( el.currentStyle.borderTopWidth    ) +
				parseInt( el.currentStyle.borderBottomWidth );
};

/**
 * @access public
 */
MenuBar.prototype.getLeft = function()
{
	return PosLib.getScreenLeft( this._htmlElement );
};

/**
 * @access public
 */
MenuBar.prototype.getTop = function()
{
	return PosLib.getScreenLeft( this._htmlElement );
};

/**
 * @access public
 */
MenuBar.prototype.setLeft = function( l )
{
};

/**
 * @access public
 */
MenuBar.prototype.setTop = function( t )
{
};

/**
 * @access public
 */
MenuBar.prototype.setLocation = function( l, t )
{
};

/**
 * @access public
 */
MenuBar.prototype.setRect = function( l, t, w, h )
{
};

/**
 * @access public
 */
MenuBar.prototype.getInsetLeft = function()
{
	return parseInt( this._htmlElement.currentStyle.borderLeftWidth );
};

/**
 * @access public
 */
MenuBar.prototype.getInsetRight = function()
{
	return parseInt( this._htmlElement.currentStyle.borderRightWidth );
};

/**
 * @access public
 */
MenuBar.prototype.getInsetTop = function()
{
	return parseInt( this._htmlElement.currentStyle.borderTopWidth );
};

/**
 * @access public
 */
MenuBar.prototype.getInsetBottom = function()
{
	return parseInt( this._htmlElement.currentStyle.borderBottomWidth );
};

/**
 * @access public
 */
MenuBar.prototype.fixScrollButtons = function()
{
};

/**
 * @access public
 */
MenuBar.prototype.fixScrollEnabledState = function()
{
};

/**
 * @access public
 */
MenuBar.prototype.hookupMenu = function( element )
{
	// create shortcut to html element
	this._htmlElement = element;
	element.unselectable = "on";
	
	// and same for menu buttons
	var cs = element.childNodes;
	var items = this.items;
	var l = cs.length;

	for ( var i = 0; i < l; i++ )
	{
		items[i]._htmlElement = cs[i];
		cs[i]._menuItem = items[i];
	}
	
	var oThis = this;
	
	// hook up events
	element.attachEvent( "onmouseover", function ()
	{
		var e = window.event;
		var fromEl = Menu.getMenuItemElement( e.fromElement );
		var toEl   = Menu.getMenuItemElement( e.toElement   );
		
		if ( toEl != null && toEl != fromEl )
		{	
			var mb = toEl._menuItem;
			var m  = mb.parentMenu;
			
			if ( m.getActiveState() == "open" )
			{
				window.setTimeout( function()
				{
					mb.dispatchAction();
				}, 1 );
			}
			else if ( m.getActiveState() == "active" )
			{
				mb.setSelected( true );			
			}
			else
			{
				mb._hover = true;
				toEl.className = mb.getCssClass();
			}
		}
	} );
	element.attachEvent( "onmouseout", function()
	{
		var e = window.event;
		var fromEl = Menu.getMenuItemElement( e.fromElement );
		var toEl   = Menu.getMenuItemElement( e.toElement   );
		
		if ( fromEl != null && toEl != fromEl )
		{
			var mb = fromEl._menuItem;
			mb._hover = false;
			fromEl.className = mb.getCssClass();
		}
	} );
	element.attachEvent( "onmousedown", function()
	{
		var e = window.event;
		
		if ( e.button != MenuBar.leftMouseButton )
			return;
		
		var el = Menu.getMenuItemElement( e.srcElement );
		
		if ( el != null )
		{
			var mb = el._menuItem;
			
			if ( mb.subMenu )
			{
				mb.subMenu._checkCloseState();
				
				// longer than the time to do the hide
				if ( new Date() - mb.subMenu._closedAt > 100 )
				{
					mb.dispatchAction();
				}
				else
				{
					mb._hover = true;
					mb._htmlElement.className = mb.getCssClass();
				}
			}
		}
	} );
	document.attachEvent( "onkeydown", function()
	{
		oThis.handleKeyEvent( window.event );
	} );
};

/**
 * @access public
 */
MenuBar.prototype.write = function()
{
	document.write( this.toHtml() );
	var el = document.getElementById( this.id );
	this.hookupMenu( el );
};

/**
 * @access public
 */
MenuBar.prototype.create = function()
{
	var dummyDiv = document.createElement( "DIV" );
	dummyDiv.innerHTML = this.toHtml();
	var el = dummyDiv.removeChild( dummyDiv.firstChild );
	this.hookupMenu( el );

	return el;
};

/**
 * @access public
 */
MenuBar.prototype.paint = function()
{
	document.body.appendChild( this.create() );
};

/**
 * @access public
 */
MenuBar.prototype.handleKeyEvent = function( e )
{
	if ( this.getActiveState() == "open" )
		return;

	var nKeyCode = e.keyCode;

	if ( this.active && e[ Menu.keyboardAccelProperty ] )
	{
		e.returnValue = false;
		e.keyCode = 0;
	}

	if ( nKeyCode == Menu.keyboardAccelKey )
	{
		if ( !e.repeat )
			this.toggleActive();
		
		e.returnValue = false;
		e.keyCode = 0;
		
		return;
	}

	if ( !this.active )
		return;
		
	switch ( nKeyCode )
	{
		case 39:	// right
			this.goToNextMenuItem();
			break;
			
		case 37:	// left
			this.goToPreviousMenuItem();
			break;
		
		case 40:	// down
		case 38:	// up
		case 13:	// enter
			var mi = this.items[ this.getSelectedIndex() ];
			
			if ( mi )
			{
				mi.dispatchAction();
				
				if ( mi.subMenu )
					mi.subMenu.setSelectedIndex( 0 );
			}
			
			break;
		
		case 27:	// esc
			// we need to make sure that the menu bar looses its current 
			// keyboard activation state
			this.setActive( false );
			break;
		
		default:
			// find any mnemonic that matches
			var c = String.fromCharCode( nKeyCode ).toLowerCase();
			var items = this.items;
			var l = items.length;
			
			for ( var i = 0; i < l; i++ )
			{
				if ( items[i].mnemonic == c )
				{
					items[i].dispatchAction();
					break;
				}					
			}
	}
};

/**
 * @access public
 */
MenuBar.prototype.getMenuBar = function()
{
	return this;
};

/**
 * @access public
 */
MenuBar.prototype.setActive = function( bActive )
{
	if ( this.active != bActive )
	{
		this.active = Boolean( bActive );
		
		if ( this.active )
		{
			this.setSelectedIndex( 0 );
			this.backupFocused();
			window.focus();
		}
		else
		{
			this.setSelectedIndex( -1 );
			this.restoreFocused();
		}
	}
};

/**
 * @access public
 */
MenuBar.prototype.toggleActive = function()
{
	if ( this.getActiveState() == "active" )
		this.setActive( false );
	else if ( this.getActiveState() == "inactive" )
		this.setActive( true );
};
/**
 * Returns active, inactive or open.
 *
 * @access public
 */
MenuBar.prototype.getActiveState = function()
{
	if ( this.shownSubMenu != null || this._aboutToShowSubMenu )
		return "open";
	else if ( this.active )
		return "active";
	else
		return "inactive";
};

/**
 * @access public
 */
MenuBar.prototype.backupFocused = function()
{
	this._activeElement = document.activeElement;
};

/**
 * @access public
 */
MenuBar.prototype.restoreFocused = function()
{
	try
	{
		this._activeElement.focus();
	}
	catch ( ex )
	{
	}
	
	delete this._activeElement;
};


/**
 * @access public
 */
MenuBar.prototype._menu_goToNextMenuItem = Menu.prototype.goToNextMenuItem;

/**
 * @access public
 */
MenuBar.prototype.goToNextMenuItem = function()
{
	var expand = this.getActiveState() == "open";
	this._menu_goToNextMenuItem();
	var mi = this.items[ this.getSelectedIndex() ];

	if ( expand && mi != null )
	{
		window.setTimeout( function()
		{
			mi.dispatchAction();
			
			if ( mi.subMenu )
				mi.subMenu.setSelectedIndex( 0 );
		}, 1 );
	}	
};


/**
 * @access public
 */
MenuBar.prototype._menu_goToPreviousMenuItem = Menu.prototype.goToPreviousMenuItem;

/**
 * @access public
 */
MenuBar.prototype.goToPreviousMenuItem = function()
{
	var expand = this.getActiveState() == "open";
	this._menu_goToPreviousMenuItem();
	var mi = this.items[ this.getSelectedIndex() ];

	if ( expand && mi != null )
	{
		window.setTimeout( function()
		{
			mi.dispatchAction();
		
			if ( mi.subMenu )
				mi.subMenu.setSelectedIndex( 0 );
		}, 1 );
	}
};

/**
 * @access public
 */
MenuBar.prototype._menu_setSelectedIndex = Menu.prototype.setSelectedIndex;

/**
 * @access public
 */
MenuBar.prototype.setSelectedIndex = function( nIndex )
{
	this._menu_setSelectedIndex( nIndex );
	this.active = nIndex != -1;
};


/**
 * @access public
 * @static
 */
MenuBar.leftMouseButton = 1;
