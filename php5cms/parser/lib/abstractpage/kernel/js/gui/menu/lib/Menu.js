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
Menu = function()
{
	this.Base = Base;
	this.Base();
	
	this.items               = [];
	this.parentMenu          = null;
	this.parentMenuItem      = null;
	this.popup               = null;
	this.shownSubMenu        = null;
	this._aboutToShowSubMenu = false;
	
	this.selectedIndex       = -1;
	this._drawn              = false;
	this._scrollingMode      = false;
	this._showTimer          = null;
	this._closeTimer         = null;
	
	this._onCloseInterval    = null;
	this._closed             = true;
	this._closedAt           = 0;
	
	this._cachedSizes        = {};
	this._measureInvalid     = true;
};


Menu.prototype = new Base();
Menu.prototype.constructor = Menu;
Menu.superclass = Base.prototype;

/**
 * @access public
 */
Menu.prototype.cssFile = "css/winclassic.css";

/**
 * @access public
 */
Menu.prototype.mouseHoverDisabled = true;

/**
 * @access public
 */
Menu.prototype.showTimeout = 250;

/**
 * @access public
 */
Menu.prototype.closeTimeout = 250;


/**
 * @access public
 */
Menu.prototype.add = function( mi )
{
	this.items.push( mi );
	mi.parentMenu = this;
	mi.itemIndex  = this.items.length - 1;
	var sm = mi.subMenu;
	
	if ( sm )
	{
		sm.parentMenu = this;
		sm.parentMenuItem = mi;
	}
	
	return mi;
};

/**
 * @access public
 */
Menu.prototype.remove = function( mi )
{
	var res = [];
	var items = this.items;
	var l = items.length;
	
	for ( var i = 0; i < l; i++ )
	{
		if ( items[i] != mi )
		{
			res.push( items[i] );
			items[i].itemIndex = res.length - 1;
		}
	}
	
	this.items = res;
	mi.parentMenu = null;

	return mi;
};

/**
 * @access public
 */
Menu.prototype.toHtml = function()
{	
	var items = this.items;
	var l = items.length
	var itemsHtml = new Array( l );
	
	for ( var i = 0; i < l; i++ )
		itemsHtml[i] = items[i].toHtml();

	var html = "<html><head>" +
			"<link type=\"text/css\" rel=\"StyleSheet\" href=\"" + this.cssFile + "\" />" +
			"</head><body class=\"menu-body\">" +
			"<div class=\"outer-border\"><div class=\"inner-border\">" +
			"<table id=\"scroll-up-item\" cellspacing=\"0\" style=\"display: none\">" +
			"<tr class=\"disabled\"><td>" +
			"<span class=\"disabled-container\"><span class=\"disabled-container\">" +
			"5" +
			"</span></span>" + "</td></tr></table>" +
			"<div id=\"scroll-container\">" +
			"<table cellspacing=\"0\">" + itemsHtml.join( "" ) + "</table>" +
			"</div>" +
			"<table id=\"scroll-down-item\" cellspacing=\"0\" style=\"display: none\">" +
			"<tr><td>" +
			"<span class=\"disabled-container\"><span class=\"disabled-container\">" +
			"6" +
			"</span></span>" +
			"</td></tr></table>" +
			"</div></div>" +
			"</body></html>";
			
	html = Menu.outputCallbackFn( html );
	return html;
};

/**
 * @access public
 */
Menu.prototype.createPopup = function()
{
	var w;
	var pm = this.parentMenu;
	
	if ( pm == null )
		w = window;
	else
		w = pm.getDocument().parentWindow;

	this.popup = w.createPopup();
};

/**
 * @access public
 */
Menu.prototype.getMeasureDocument = function()
{
	if ( this.isShown() && this._drawn )
		return this.getDocument();

	var mf = Menu._measureFrame;

	if ( mf == null )
	{
		mf = Menu._measureFrame = document.createElement( "IFRAME" );
		var mfs = mf.style;
		mfs.position   = "absolute";
		mfs.visibility = "hidden";
		mfs.left       = "-100px";
		mfs.top        = "-100px";
		mfs.width      = "10px";
		mfs.height     = "10px";
		mf.frameBorder = 0;
		
		document.body.appendChild( mf );
	}
	
	var d = mf.contentWindow.document
	
	if ( Menu._measureMenu == this && !this._measureInvalid )
		return d;		
	
	d.open( "text/html", "replace" );
	d.write( this.toHtml() );
	d.close();
	
	Menu._measureMenu = this;
	this._measureInvalid = false;
	
	return d;
};

/**
 * @access public
 */
Menu.prototype.getDocument = function()
{
	if ( this.popup )
		return this.popup.document;
	else
		return null;
};

/**
 * @access public
 */
Menu.prototype.getPopup = function()
{
	if ( this.popup == null )
		this.createPopup();

	return this.popup;
};

/**
 * @access public
 */
Menu.prototype.invalidate = function()
{
	this._drawn = false;
	this.resetSizeCache();
	this._measureInvalid = true;	
};

/**
 * @access public
 */
Menu.prototype.redrawMenu = function()
{
	this.invalidate();
	this.drawMenu();
};

/**
 * @access public
 */
Menu.prototype.drawMenu = function()
{	
	if ( this._drawn )
		return;

	this.getPopup();
	
	var d = this.getDocument();
	d.open( "text/html", "replace" );
	d.write( this.toHtml() );
	d.close();
	
	// set up scroll buttons
	var up   = d.getElementById( "scroll-up-item"   );
	var down = d.getElementById( "scroll-down-item" );
	var scrollContainer = d.getElementById( "scroll-container" );
	
	new ScrollButton( up,   scrollContainer, 8 );
	new ScrollButton( down, scrollContainer, 2 );

	// bind menu items to the table rows
	var rows  = scrollContainer.firstChild.tBodies[0].rows;
	var items = this.items;
	var l = rows.length;
	var mi;
	
	for ( var i = 0; i < l; i++ )
	{
		mi = items[i];
		rows[i]._menuItem = mi;
		mi._htmlElement = rows[i];
	}
	
	var oThis = this;

	// listen to the onscroll
	scrollContainer.onscroll = function()
	{
		oThis.fixScrollEnabledState();
	};
	
	// hook up mouse
	this.hookupMenu( d );
	
	this._drawn = true;
};

/**
 * @access public
 */
Menu.prototype.show = function( left, top, w, h )
{
	var pm = this.parentMenu;

	if ( pm )
		pm.closeAllSubs( this );

	this.drawMenu();
	
	if ( left == null )
		left = 0;
	
	if ( top == null )
		top = 0;
	
	w = w || this.getPreferredWidth();
	h = h || this.getPreferredHeight();

	this.popup.show( left, top, w, h );
	
	this.fixScrollButtons();
	this.fixScrollEnabledState();
	
	// clear selected item
	if ( this.selectedIndex != -1 )
	{
		if ( this.items[ this.selectedIndex ] )
			this.items[ this.selectedIndex ].setSelected( false );			
	}
	
	if ( pm )
	{
		pm.shownSubMenu = this;
		pm._aboutToShowSubMenu = false;
	}
	
	window.clearTimeout( this._showTimer  );
	window.clearTimeout( this._closeTimer );
	
	this._closed = false;
	this._startClosePoll();
};

/**
 * @access public
 */
Menu.prototype.isShown = function()
{
	return this.popup != null && this.popup.isOpen;
};

/**
 * @access public
 */
Menu.prototype.fixSize = function()
{
	var w = Math.min( window.screen.width, this.getPreferredWidth()   );
	var h = Math.min( window.screen.height, this.getPreferredHeight() );
	var l = Math.max( 0, this.getLeft() );
	var t = Math.max( 0, this.getTop()  );

	this.popup.show( l, t, w, h );
};

/**
 * @access public
 */
Menu.prototype.getWidth = function()
{
	var d = this.getDocument();
	
	if ( d != null )
		return d.body.offsetWidth;
	else
		return 0;
};

/**
 * @access public
 */
Menu.prototype.getHeight = function()
{
	var d = this.getDocument();
	
	if ( d != null )
		return d.body.offsetHeight;
	else
		return 0;
};

/**
 * @access public
 */
Menu.prototype.getPreferredWidth = function()
{
	this.updateSizeCache();
	return this._cachedSizes.preferredWidth;
};

/**
 * @access public
 */
Menu.prototype.getPreferredHeight = function()
{
	this.updateSizeCache();
	return this._cachedSizes.preferredHeight;
};

/**
 * @access public
 */
Menu.prototype.getLeft = function()
{
	var d = this.getDocument();

	if ( d != null )
		return d.parentWindow.screenLeft;
	else
		return 0;
};

/**
 * @access public
 */
Menu.prototype.getTop = function()
{
	var d = this.getDocument();

	if ( d != null )
		return d.parentWindow.screenTop;
	else
		return 0;
};

/**
 * Depreciated. Use show instead.
 *
 * @access public
 */
Menu.prototype.setLeft = function( l )
{
	return Base.raiseError( "Depreciated. Use show instead." );
	
	// var t = this.getTop();
	// this.setLocation( l, t );
};

/**
 * Depreciated. Use show instead.
 *
 * @access public
 */
Menu.prototype.setTop = function( t )
{
	return Base.raiseError( "Depreciated. Use show instead." );

	// var l = this.getLeft();
	// this.setLocation( l, t );
};

/**
 * Depreciated. Use show instead.
 *
 * @access public
 */
Menu.prototype.setLocation = function( l, t )
{
	return Base.raiseError( "Depreciated. Use show instead." );
	
	// var w = this.getWidth();
	// var h = this.getHeight();
	// this.popup.show( l, t, w, h );
};

/**
 * Depreciated. Use show instead.
 *
 * @access public
 */
Menu.prototype.setRect = function( l, t, w, h )
{
	return Base.raiseError( "Depreciated. Use show instead." );
	// this.popup.show( l, t, w, h );
};

/**
 * @access public
 */
Menu.prototype.getInsetLeft = function()
{
	this.updateSizeCache();
	return this._cachedSizes.insetLeft;
};

/**
 * @access public
 */
Menu.prototype.getInsetRight = function()
{
	this.updateSizeCache();
	return this._cachedSizes.insetRight;
};

/**
 * @access public
 */
Menu.prototype.getInsetTop = function()
{
	this.updateSizeCache();
	return this._cachedSizes.insetTop;
};

/**
 * @access public
 */
Menu.prototype.getInsetBottom = function()
{
	this.updateSizeCache();
	return this._cachedSizes.insetBottom;
};

/**
 * @access public
 */
Menu.prototype.areSizesCached = function()
{
	var cs = this._cachedSizes;

	return this._drawn &&
		"preferredWidth"  in cs &&
		"preferredHeight" in cs &&
		"insetLeft"       in cs &&
		"insetRight"      in cs &&
		"insetTop"        in cs &&
		"insetBottom"     in cs;
};

/**
 * @access public
 */
Menu.prototype.cacheSizes = function( bForce )
{
	return updateSizeCache( bForce );
};

/**
 * @access public
 */
Menu.prototype.resetSizeCache = function()
{
	this._cachedSizes = {};
};

/**
 * @access public
 */
Menu.prototype.updateSizeCache = function( bForce )
{
	if ( this.areSizesCached() && !bForce )
		return;
	
	var d = this.getMeasureDocument();
	var body = d.body;
	var cs = this._cachedSizes = {}; // reset
	var scrollContainer = d.getElementById( "scroll-container" );
	
	// preferred width
	cs.preferredWidth = d.body.scrollWidth;

	// preferred height
	scrollContainer.style.overflow = "visible";
	cs.preferredHeight = body.scrollHeight;
	scrollContainer.style.overflow = "hidden";
	
	// inset left
	cs.insetLeft = PosLib.getLeft( scrollContainer );

	// inset right
	cs.insetRight = body.offsetWidth - PosLib.getLeft( scrollContainer ) - scrollContainer.offsetWidth;

	// inset top
	var up = d.getElementById( "scroll-up-item" );
	
	if ( up.currentStyle.display == "none" )
		cs.insetTop = PosLib.getTop( scrollContainer );
	else
		cs.insetTop = PosLib.getTop( up );
		
	// inset bottom
	var down = d.getElementById( "scroll-down-item" );
	
	if ( down.currentStyle.display == "none" )
		cs.insetBottom = body.offsetHeight - PosLib.getTop( scrollContainer ) - scrollContainer.offsetHeight;
	else
		cs.insetBottom = body.offsetHeight - PosLib.getTop( down ) - down.offsetHeight;
};

/**
 * @access public
 */
Menu.prototype.fixScrollButtons = function()
{
	var d  = this.getDocument();
	var up = d.getElementById( "scroll-up-item" );
	var down = d.getElementById( "scroll-down-item" );
	var scrollContainer = d.getElementById( "scroll-container" );
	var scs = scrollContainer.style;
	
	if ( scrollContainer.scrollHeight > this.getHeight() )
	{
		up.style.display   = "";
		down.style.display = "";

		scs.height = "";
		scs.overflow = "visible";
		scs.height = Math.max( 0, this.getHeight() - ( d.body.scrollHeight - scrollContainer.offsetHeight ) ) + "px";
		scs.overflow = "hidden";
	
		this._scrollingMode = true;
	}
	else
	{
		up.style.display   = "none";
		down.style.display = "none";
		scs.overflow = "visible";
		scs.height = "";
		
		this._scrollingMode = false;
	}
};

/**
 * @access public
 */
Menu.prototype.fixScrollEnabledState = function()
{
	var d  = this.getDocument();
	var up = d.getElementById( "scroll-up-item" );
	var down = d.getElementById( "scroll-down-item" );
	var scrollContainer = d.getElementById( "scroll-container" );
	var tr;
	
	tr = up.rows[0];
	
	if ( scrollContainer.scrollTop == 0 )
	{
		if ( tr.className == "hover" || tr.className == "disabled-hover" )
			tr.className = "disabled-hover";
		else
			tr.className = "disabled";
	}
	else
	{
		if ( tr.className == "disabled-hover" || tr.className == "hover" )
			tr.className = "hover";
		else
			tr.className = "";
	}
		
	tr = down.rows[0];
	
	if ( scrollContainer.scrollHeight - scrollContainer.clientHeight <= scrollContainer.scrollTop )
	{		
		if ( tr.className == "hover" || tr.className == "disabled-hover" )
			tr.className = "disabled-hover";
		else
			tr.className = "disabled";
	}
	else
	{
		if ( tr.className == "disabled-hover" || tr.className == "hover" )
			tr.className = "hover";
		else
			tr.className = "";		
	}
};

/**
 * @access public
 */
Menu.prototype.closeAllMenus = function()
{
	if ( this.parentMenu )
		this.parentMenu.closeAllMenus();
	else
		this.close();
};

/**
 * @access public
 */
Menu.prototype.close = function()
{
	this.closeAllSubs();
	
	window.clearTimeout( this._showTimer  );
	window.clearTimeout( this._closeTimer );
	
	if ( this.popup )
		this.popup.hide();
	
	var pm = this.parentMenu;

	if ( pm && pm.shownSubMenu == this )
		pm.shownSubMenu = null;
		
	this.setSelectedIndex( -1 );
	this._checkCloseState();
};

/**
 * @access public
 */
Menu.prototype.closeAllSubs = function( oNotThisSub )
{
	// go through items and check for sub menus
	var items = this.items;
	var l = items.length;
	
	for ( var i = 0; i < l; i++ )
	{
		if ( items[i].subMenu != null && items[i].subMenu != oNotThisSub )
			items[i].subMenu.close();
	}
};

/**
 * @access public
 */
Menu.prototype.getSelectedIndex = function()
{
	return this.selectedIndex;
};

/**
 * @access public
 */
Menu.prototype.setSelectedIndex = function( nIndex )
{
	if ( this.selectedIndex == nIndex )
		return;
	
	if ( nIndex >= this.items.length )
		nIndex = -1;
	
	var mi;
	
	// deselect old
	if ( this.selectedIndex != -1 )
	{
		mi = this.items[ this.selectedIndex ];
		mi.setSelected( false );
	}
	
	this.selectedIndex = nIndex;
	mi = this.items[ this.selectedIndex ];
	
	if ( mi != null )
		mi.setSelected( true );
};

/**
 * @access public
 */
Menu.prototype.goToNextMenuItem = function()
{
	var i = 0;
	var items  = this.items;
	var length = items.length;
	var index  = this.getSelectedIndex();
	var tmp;
	
	// TODO: this is a do until
	// while ( true ) {
	do
	{
		if ( index == -1 || index >= length )
			index = 0;
		else 
			index++;
		
		i++;
		tmp = items[index]
	} while ( !( tmp != null && tmp instanceof MenuItem && !( tmp instanceof MenuItem_separator ) || i >= length ) )
	
	if ( tmp != null )
		this.setSelectedIndex( index );
};

/**
 * @access public
 */
Menu.prototype.goToPreviousMenuItem = function()
{
	var i = 0;
	var items  = this.items;
	var length = items.length;
	var index  = this.getSelectedIndex();
	var tmp;
	
	// TODO: this is a do until
	// while ( true ) {
	do
	{
		if ( index == -1 || index >= length )
			index = length - 1;
		else 
			index--;
		
		i++;
		tmp = items[index]
	} while ( !( tmp != null && tmp instanceof MenuItem && !( tmp instanceof MenuItem_separator ) || i >= length ) )
	
	if ( tmp != null )
		this.setSelectedIndex( index );
};

/**
 * @access public
 */
Menu.prototype.goToNextMenu = function()
{
	var index = this.getSelectedIndex();
	var mi = this.items[ index ];
	
	if ( mi && mi.subMenu && !mi.disabled )
	{
		mi.subMenu.setSelectedIndex( 0 );
		mi.showSubMenu( false );
	}
	else
	{
		// go up to root and select next
		var mb = this.getMenuBar();
		
		if ( mb != null )
			mb.goToNextMenuItem();
	}
};

/**
 * @access public
 */
Menu.prototype.goToPreviousMenu = function()
{
	if ( this.parentMenuItem && this.parentMenuItem instanceof MenuBar_button )
		this.parentMenu.goToPreviousMenuItem();
	else if ( this.parentMenuItem )
		this.close();
};

/**
 * @access public
 */
Menu.prototype.getMenuBar = function()
{
	if ( this.parentMenu == null )
		return null;
	
	return this.parentMenu.getMenuBar();
};

/**
 * @access public
 */
Menu.prototype.hookupMenu = function( d )
{
	var oThis = this;
	var d = this.getDocument();
	var w = d.parentWindow;
	
	d.attachEvent( "onmouseover", function()
	{
		var fromEl = Menu.getTrElement( w.event.fromElement );
		var toEl   = Menu.getTrElement( w.event.toElement );
		
		if ( toEl != null && toEl != fromEl )
		{
			var mi = toEl._menuItem;
			
			if ( mi )
			{
				if ( !mi.disabled || oThis.mouseHoverDisabled )
				{
					mi.setSelected( true );
					mi.showSubMenu( true );
				}
			}
			// scroll button
			else
			{
				if ( toEl.className == "disabled" || toEl.className == "disabled-hover" )
					toEl.className = "disabled-hover";
				else
					toEl.className = "hover";
				
				oThis.selectedIndex = -1;
			}
		}
	} );
	d.attachEvent( "onmouseout", function()
	{
		var fromEl = Menu.getTrElement( w.event.fromElement );
		var toEl   = Menu.getTrElement( w.event.toElement );
		
		if ( fromEl != null && toEl != fromEl )
		{	
			var id = fromEl.parentNode.parentNode.id;
			var mi = fromEl._menuItem;
			
			if ( id == "scroll-up-item" || id == "scroll-down-item" )
			{
				if (fromEl.className == "disabled-hover" || fromEl.className == "disabled" )
					fromEl.className = "disabled";
				else
					fromEl.className = "";

				oThis.selectedIndex = -1;
			}
			else if ( mi && ( toEl != null || mi.subMenu == null || mi.disabled ) )
			{		
				mi.setSelected( false );
			}
		}
	} );
	d.attachEvent( "onmouseup", function()
	{
		var srcEl = Menu.getMenuItemElement( w.event.srcElement );
		
		if ( srcEl != null )
		{
			var id = srcEl.parentNode.parentNode.id;
			
			if ( id == "scroll-up-item" || id == "scroll-down-item" )
				return;
			
			oThis.selectedIndex = srcEl.rowIndex;
			var menuItem = oThis.items[ oThis.selectedIndex ];
			menuItem.dispatchAction();
		}
	} );
	d.attachEvent( "onmousewheel", function()
	{
		var scrollContainer = d.getElementById( "scroll-container" );
		scrollContainer.scrollTop -= 3 * w.event.wheelDelta / 120 * ScrollButton.scrollAmount;
	} );

	// if css file is not loaded we need to wait for it to load.
	// Once loaded fix the size	
	var linkEl = d.getElementsByTagName( "LINK" )[0];
	
	if ( linkEl.readyState != "complete" )
	{
		linkEl.attachEvent( "onreadystatechange", function()
		{
			if ( linkEl.readyState == "complete" )
			{
				// reset sizes
				oThis.resetSizeCache();
				
				oThis.fixSize();
				oThis.fixScrollButtons();
			}
		} );
	}
	
	d.attachEvent( "onkeydown", function()
	{
		oThis.handleKeyEvent( w.event );
	} );
	d.attachEvent( "oncontextmenu", function()
	{
		w.event.returnValue = false;
	} );
	// prevent IE to keep menu open when navigating away
	window.attachEvent( "onbeforeunload", function()
	{
		oThis.closeAllMenus();
	} );

	var all = d.all;
	var l = all.length;

	for ( var i = 0; i < l; i++ )
		all[i].unselectable = "on";
};

/**
 * @access public
 */
Menu.prototype.handleKeyEvent = function( oEvent )
{
	if ( this.shownSubMenu )
	{
		// sub menu handles key event
		return;
	}

	var nKeyCode = oEvent.keyCode;
	
	if ( oEvent[ Menu.keyboardAccelProperty ] )
	{
		oEvent.returnValue = false;
		oEvent.keyCode = 0;
	}
	
	switch ( nKeyCode )
	{
		case 40:	// down
			this.goToNextMenuItem();
			break;
			
		case 38:	// up
			this.goToPreviousMenuItem();
			break;
		
		case 39:	// right
			this.goToNextMenu();
			break;
		
		case 37:	// left
			this.goToPreviousMenu();
			break;
		
		case 13:	// enter
			var mi = this.items[ this.getSelectedIndex() ];
			
			if ( mi )
				mi.dispatchAction();
			
			break;
		
		case 27:	// esc
			this.close();
			
			// should close menu and go to parent menu item
			break;
				
		case Menu.keyboardAccelKey:
			this.closeAllMenus();
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
Menu.prototype.deselectGroup = function( name )
{
	var items = this.items;
	var l = items.length;
		
	for ( var i = 0; i < l; i++ )
	{
		if ( ( items[i] instanceof MenuItem_radiobutton ) && ( items[i].radioGroupName == name ) )
			items[i].checked = false;
	}
	
	this.invalidate();
};


// private methods

/**
 * Poll close state and when closed call _onclose
 *
 * @access private
 */
Menu.prototype._startClosePoll = function()
{
	var oThis = this;
	window.clearInterval( this._onCloseInterval );
	
	this._onCloseInterval = window.setInterval( function()
	{
		oThis._checkCloseState();
	}, 100 );
};

/**
 * @access private
 */
Menu.prototype._checkCloseState = function()
{
	var closed = this.popup == null || !this.popup.isOpen;
	
	if ( closed && this._closed != closed )
	{
		this._closed = closed;
		this._closedAt = new Date().valueOf();
		window.clearInterval( this._onCloseInterval );
		
		if ( typeof this._onclose == "function" )
		{
			var e = this.getDocument().parentWindow.event;
			
			if ( e != null && e.keyCode == 27 )
				this._closeReason = "escape";
			else
				this._closeReason = "unknown";
			
			this._onclose();
		}
	}
};


/**
 * The keyCode for the key tp activate the menubar
 * @access public
 * @static
 */
Menu.keyboardAccelKey = 27;

/**
 * When this property is true default actions will be canceled on a menu
 * @access public
 * @static
 */
Menu.keyboardAccelProperty = "ctrlKey";
// Use -1 to disable keyboard invoke of the menubar
// Use "" to allow all normal keyboard commands inside the menus

/**
 * @access public
 * @static
 */
Menu.setCSSPath = function( cssFile )
{
	Menu.prototype.cssFile = cssFile;
};

/**
 * @access public
 * @static
 */
Menu.getMenuItemElement = function( el ) {
	while ( el != null && el._menuItem == null)
		el = el.parentNode;
	return el;
};

/**
 * @access public
 * @static
 */
Menu.getTrElement = function( el ) {
	while ( el != null && el.tagName != "TR" )
		el = el.parentNode;
	return el;
};

/** 
 * Dummy callback -- can be used for localization
 *
 * @access public
 * @static
 */
Menu.outputCallbackFn = function( html )
{
	return html;
};
