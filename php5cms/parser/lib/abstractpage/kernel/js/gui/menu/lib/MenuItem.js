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
MenuItem = function( sLabelText, fAction, sIconSrc, oSubMenu )
{
	this.Base = Base;
	this.Base();
	
	this.icon     = sIconSrc || "";
	this.text     = sLabelText;
	this.action   = fAction;
	this.disabled = false;
	this.mnemonic = null;
	this.shortcut = null;
	this.toolTip  = "";
	this.target   = null;
	this.visible  = true;
	
	this.subMenu = oSubMenu;
	this.parentMenu = null;
	this.subMenuDirection = "horizontal";
	
	// private
	this._selected  = false;
	this._useInsets = true;	// should insets be taken into account when showing sub menu
};


MenuItem.prototype = new Base();
MenuItem.prototype.constructor = MenuItem;
MenuItem.superclass = Base.prototype;

/**
 * @access public
 */
MenuItem.prototype.toHtml = function()
{
	var cssClass = this.getCssClass();
	var toolTip  = this.getToolTip();
	
	return	"<tr" +
			( ( cssClass != "" )? " class=\"" + cssClass + "\"" : "" ) +
			( ( toolTip  != "" )? " title=\"" + toolTip  + "\"" : "" ) +
			( !this.visible? " style=\"display: none\"" : "" ) +
			">" +
			this.getIconCellHtml() +
			this.getTextCellHtml() +
			this.getShortcutCellHtml() +
			this.getSubMenuArrowCellHtml() +
			"</tr>";
};

/**
 * @access public
 */
MenuItem.prototype.getTextHtml = function()
{
	var s = this.text;
	
	if ( this.mnemonic == null )
		return s;
	
	// replace character with <u> character </u>
	var re = new RegExp( "(" + this.mnemonic + ")", "i" ); 
	var a  = re.exec( s );
	var c  = ( a != null )? a[1] : "";
	
	return s.replace( re, "<u>" + c + "</u>" );
};

/**
 * @access public
 */
MenuItem.prototype.getIconHtml = function()
{
	/*
	if ( this.icon.indexOf( "icon:" ) != -1 )
		return "<img class=\"icon\" handle=\"" + this.icon.substring( 5, this.icon.length ) + "\">";
	else
	*/
		return ( this.icon != "")? "<img src=\"" + this.icon + "\"  />" : "<span>&nbsp;</span>";
};

/**
 * @access public
 */
MenuItem.prototype.getTextCellHtml = function()
{
	return "<td class=\"label-cell\" nowrap=\"nowrap\">" + this.makeDisabledContainer( this.getTextHtml() ) + "</td>";
};

/**
 * @access public
 */
MenuItem.prototype.getIconCellHtml = function()
{
	return "<td class=\"" + ( ( this.icon != "" )? "icon-cell" : "empty-icon-cell" ) + "\">" +
		this.makeDisabledContainer( this.getIconHtml() ) + "</td>";
};

/**
 * @access public
 */
MenuItem.prototype.getCssClass = function()
{
	if ( this.disabled && this._selected )
		return "disabled-hover";
	else if ( this.disabled )
		return "disabled";
	else if ( this._selected )
		return "hover";
	
	return "";
};

/**
 * @access public
 */
MenuItem.prototype.getToolTip = function()
{
	return this.toolTip;
};

/**
 * @access public
 */
MenuItem.prototype.getShortcutHtml = function()
{
	if ( this.shortcut == null )
		return "&nbsp;";
	
	return this.shortcut;
};

/**
 * @access public
 */
MenuItem.prototype.getShortcutCellHtml = function()
{
	return "<td class=\"shortcut-cell\" nowrap=\"nowrap\">" +
		this.makeDisabledContainer( this.getShortcutHtml() ) + "</td>";
};

/**
 * @access public
 */
MenuItem.prototype.getSubMenuArrowHtml = function()
{
	if ( this.subMenu == null )
		return "&nbsp;";
	
	return 4; // right arrow using the marlett (or webdings) font
};

/**
 * @access public
 */
MenuItem.prototype.getSubMenuArrowCellHtml = function()
{
	return "<td class=\"arrow-cell\">" + this.makeDisabledContainer( this.getSubMenuArrowHtml() ) + "</td>";
};

/**
 * @access public
 */
MenuItem.prototype.makeDisabledContainer = function( s )
{
	if ( this.disabled )
		return	"<span class=\"disabled-container\"><span class=\"disabled-container\">" + s + "</span></span>";
	
	return s;
};

/**
 * @access public
 */
MenuItem.prototype.dispatchAction = function()
{
	if ( this.disabled )
		return;
	
	this.setSelected( true );
	
	if ( this.subMenu )
	{
		if ( !this.subMenu.isShown() )
			this.showSubMenu( false );
		
		return;
	}
	
	if ( typeof this.action == "function" )
	{
		this.setSelected( false );
		this.parentMenu.closeAllMenus();
		this.action();	
	}
	// href
	else if ( typeof this.action == "string" )
	{
		this.setSelected( false );
		this.parentMenu.closeAllMenus();
		
		if ( this.target != null )
			window.open( this.action, this.target );
		else
			document.location.href = this.action;
	}
};

/**
 * @access public
 */
MenuItem.prototype.setSelected = function( bSelected )
{
	if ( this._selected == bSelected )
		return;
	
	this._selected = Boolean( bSelected );

	var tr = this._htmlElement;
	
	if ( tr )
		tr.className = this.getCssClass();
	
	if ( !this._selected )
		this.closeSubMenu( true );

	var pm = this.parentMenu;

	if ( bSelected )
	{	
		pm.setSelectedIndex( this.itemIndex );
		this.scrollIntoView();
		
		// select item in parent menu as well
		if ( pm.parentMenuItem )
			pm.parentMenuItem.setSelected( true );
	}
	else
	{
		pm.setSelectedIndex( -1 );
	}
	
	if ( this._selected )
	{
		// clear timers for parent menu
		window.clearTimeout( pm._closeTimer );
	}
};

/**
 * @access public
 */
MenuItem.prototype.getSelected = function()
{
	return this.itemIndex == this.parentMenu.selectedIndex;
};

/**
 * @access public
 */
MenuItem.prototype.showSubMenu = function( bDelayed )
{
	var sm = this.subMenu;
	var pm = this.parentMenu;
	
	if ( sm && !this.disabled )
	{	
		pm._aboutToShowSubMenu = true;
		
		window.clearTimeout( sm._showTimer  );
		window.clearTimeout( sm._closeTimer );
		
		var showTimeout = bDelayed? sm.showTimeout : 0;
		var oThis = this;
		
		sm._showTimer = window.setTimeout( function()
		{
			var selectedIndex = sm.getSelectedIndex();
			pm.closeAllSubs( sm );
			
			window.setTimeout( function()
			{
				oThis.positionSubMenu();
				sm.setSelectedIndex( selectedIndex );
				oThis.setSelected( true );
			}, 1 );
		}, showTimeout );
	}
};

/**
 * @access public
 */
MenuItem.prototype.closeSubMenu = function( bDelay )
{
	var sm = this.subMenu;
	
	if ( sm )
	{
		window.clearTimeout( sm._showTimer  );
		window.clearTimeout( sm._closeTimer );

		if ( sm.popup )
		{
			if ( !bDelay )
			{
				sm.close();
			}
			else
			{
				var oThis = this;
				sm._closeTimer = window.setTimeout( function()
				{
					sm.close();
				}, sm.closeTimeout );
			}
		}
	}
};

/**
 * @access public
 */
MenuItem.prototype.scrollIntoView = function()
{
	if ( this.parentMenu._scrollingMode )
	{
		var d  = this.parentMenu.getDocument();
		var sc = d.getElementById( "scroll-container" );
		
		var scrollTop    = sc.scrollTop;
		var clientHeight = sc.clientHeight;
		var offsetTop    = this._htmlElement.offsetTop;
		var offsetHeight = this._htmlElement.offsetHeight;
		
		if ( offsetTop < scrollTop )
			sc.scrollTop = offsetTop;
		else if ( offsetTop + offsetHeight > scrollTop + clientHeight )
			sc.scrollTop = offsetTop + offsetHeight - clientHeight;
	}
};

/**
 * @access public
 */
MenuItem.prototype.positionSubMenu = function()
{
	var dir = this.subMenuDirection;
	var el  = this._htmlElement;
	var useInsets = this._useInsets;
	var sm = this.subMenu;
	
	// find parent item rectangle
	var rect =
	{
		left:	PosLib.getScreenLeft( el ),
		top:	PosLib.getScreenTop( el ),
		width:	el.offsetWidth,
		height:	el.offsetHeight
	};

	var menuRect =
	{
		left:			sm.getLeft(),
		top:			sm.getTop(),
		width:			sm.getPreferredWidth(),
		height:			sm.getPreferredHeight(),
		insetLeft:		useInsets? sm.getInsetLeft()   : 0,
		insetRight:		useInsets? sm.getInsetRight()  : 0,
		insetTop:		useInsets? sm.getInsetTop()    : 0,
		insetBottom:	useInsets? sm.getInsetBottom() : 0
	};

	var left, top, width = menuRect.width, height = menuRect.height;

	if ( dir == "vertical" )
	{	
		if ( rect.left + menuRect.width <= screen.width )
			left = rect.left;
		else if ( screen.width >= menuRect.width )
			left = screen.width - menuRect.width;
		else
			left = 0;
			
		if ( rect.top + rect.height + menuRect.height <= screen.height )
		{
			top = rect.top + rect.height;
		}
		else if ( rect.top - menuRect.height >= 0 )
		{
			top = rect.top - menuRect.height;
		}
		else
		{
			// use largest and resize
			var sizeAbove = rect.top;
			var sizeBelow = screen.height - rect.top - rect.height;
			
			if ( sizeBelow >= sizeAbove )
			{
				top = rect.top + rect.height;
				height = sizeBelow;			
			}
			else
			{
				top = 0;
				height = sizeAbove;			
			}
		}
	}
	else
	{	
		if ( rect.top + menuRect.height - menuRect.insetTop <= screen.height )
		{
			top = rect.top - menuRect.insetTop;
		}
		else if ( rect.top + rect.height - menuRect.height + menuRect.insetBottom >= 0 )
		{
			// BUGFIX
			top = rect.top + rect.height - menuRect.height;
			
			// top = rect.top + rect.height - menuRect.height + menuRect.insetBottom;
		}
		else if ( screen.height >= menuRect.height )
		{
			top = screen.height - menuRect.height;
		}
		else
		{
			top = 0;
			height = screen.height
		}
	
		if ( rect.left + rect.width + menuRect.width - menuRect.insetLeft <= screen.width )
		{
			left = rect.left + rect.width - menuRect.insetLeft;
		}
		else if ( rect.left - menuRect.width + menuRect.insetRight >= 0 )
		{
			// BUGFIX
			left = rect.left - menuRect.width;
			
			// left = rect.left - menuRect.width + menuRect.insetRight;
		}
		else if ( screen.width >= menuRect.width )
		{
			
			left = screen.width - menuRect.width;
		}
		else
		{
			left = 0;
		}
	}

	var scrollBefore = sm._scrollingMode;
	sm.show( left, top, width, height );

	if ( sm._scrollingMode != scrollBefore )
		this.positionSubMenu();
};
