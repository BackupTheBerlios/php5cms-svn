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
 * @package gui_panel
 */
 
/**
 * Constructor
 *
 * @access public
 */
CollapsiblePanel = function( title, collapsed, speed, steps )
{
	this.Base = Base;
	this.Base();
	
	this.id    = "cpanel" + CollapsiblePanel.count++;
	this.title = title || "[not defined]";
	
	this.speed = speed || CollapsiblePanel.defaultSpeed;
	this.steps = steps || CollapsiblePanel.defaultSteps;

	this.initiallyOpenSub = collapsed || false;
	this.entries          = new Array();
	this.timer            = null; 
	this.sl               = null;
	this.ableToCloseSub   = true; 
	this.objOpen          = null; 
	this.obj              = null; 
	this.isWorking        = false; 
	this.tmpNeedOpen      = 0; 
	this.tmpNeedClose     = 0; 
	this.toOpen           = -1; 
	this.caller           = null; 
	this.callerOpen       = null; 
	this.fs               = false; 
	this.callerName       = ""; 

	CollapsiblePanel.panels[CollapsiblePanel.panels.length] = this;
};


CollapsiblePanel.prototype = new Base();
CollapsiblePanel.prototype.constructor = CollapsiblePanel;
CollapsiblePanel.superclass = Base.prototype;

/**
 * @access public
 */
CollapsiblePanel.prototype.addItem = function( label, fn, icon )
{
	if ( ( label == null ) || ( label == "" ) )
		return false;
		
	if ( fn == null )
		href = "javascript:void(0);"

	var entry = new Object();
	entry.label  = label;
	entry.fn     = fn;
	entry.img    = icon? icon : CollapsiblePanel.defaultImagePath + CollapsiblePanel.defaultIcon;
	
	this.entries[this.entries.length] = entry;
	return true;
};

/**
 * @access public
 */
CollapsiblePanel.prototype.init = function()
{
	var obj;
	
	// dynamically bind functions to panel items
	for ( var i in this.entries )
	{
		obj = document.getElementById( this.id + "_" + i );
		obj.onclick = this.entries[i].fn
	}
	
	// expand if set
	if ( this.initiallyOpenSub == true )
		this.toggle( true );
};

/**
 * @access public
 */
CollapsiblePanel.prototype.expand = function()
{
	if ( this.isExpanded() )
		return;
		
	this.toggle();
};

/**
 * @access public
 */
CollapsiblePanel.prototype.collapse = function()
{
	if ( this.isCollapsed() )
		return;
		
	this.toggle();
};

/**
 * @access public
 */
CollapsiblePanel.prototype.toggle = function( noslide ) 
{ 
	this.sl     = true; 
	this.caller = document.getElementById( this.id ); 
	this.toOpen = parseInt( this.getContentHeight() ); 
	
	if ( this.isWorking == false ) 
	{ 
		this.obj = document.getElementById( this.id + "_sub" ); 

		if ( this.objOpen != null ) 
			this.tmpNeedClose = parseInt( this.objOpen.style.height ); 
		
		this.timer = window.setInterval( "CollapsiblePanel._slide( \"" + this.id + "\", " + noslide + " )", this.speed ); 
	} 
};

/**
 * @access public
 */
CollapsiblePanel.prototype.isExpanded = function()
{
	return ( this.objOpen )? true : false;
};

/**
 * @access public
 */
CollapsiblePanel.prototype.isCollapsed = function()
{
	return ( this.objOpen )? false : true;
};

/**
 * @access public
 */
CollapsiblePanel.prototype.getContentHeight = function()
{
	return ( this.entries.length * 22 );
};

/**
 * @access public
 */
CollapsiblePanel.prototype.toHTML = function()
{
	var entry;
	var str    = "";

	str += "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	str += "<tr>\n";
	str += "	<td>\n";
	str += "	<div id=\"" + this.id + "\" class=\"" + CollapsiblePanel.cssPrefix + "p\" onselectstart=\"return false;\" onclick='CollapsiblePanel.toggle(\"" + this.id + "\", this )' onmouseover='CollapsiblePanel._mouseHandler_Header(\"" + this.id + "\", this, \"" + CollapsiblePanel.cssPrefix + "po\" )' onmouseout='CollapsiblePanel._mouseHandler_Header(\"" + this.id+ "\", this, \"" + CollapsiblePanel.cssPrefix + "p\")'>" + this.title + "</div>\n";
	str += "		<div id=\"" + this.id + "_sub\" style=\"width:223; height:0; display:none; position:relative; top:0; overflow:hidden;\" class=\"" + CollapsiblePanel.cssPrefix + "cb\">\n";
	
	for ( var i in this.entries )
	{
		entry  = this.entries[i];
		str   += "			<div id=\""+ this.id + "_" + i + "\" class=\"" + CollapsiblePanel.cssPrefix + "c\" onselectstart=\"return false;\" onmouseover='CollapsiblePanel._mouseHandler_Item(\"" + this.id + "_" + i + "\", \"" + CollapsiblePanel.cssPrefix + "co\")' onmouseout='CollapsiblePanel._mouseHandler_Item(\"" + this.id + "_" + i + "\", \"" + CollapsiblePanel.cssPrefix + "c\")'><img src=\"" + entry.img + "\" border=\"0\" align=\"absmiddle\" hspace=\"6\" width=\"16\" height=\"16\" >" + entry.label + "</div>\n";
	}

	str += "			<div class=\"" + CollapsiblePanel.cssPrefix + "emptyDiv\"></div>\n";
	str += "		</div>\n";
	str += "	</div>\n";
	str += "	<div style=\"width:223; height:14; position:relative; top:0; overflow:hidden;\"><img src=\"" + CollapsiblePanel.defaultImagePath + CollapsiblePanel.spacerPic + "\" width=\"223\" height=\"14\" border=\"0\"></div>\n";
	str += "	</td>\n";
	str += "</tr>\n";
	str += "</table>\n";

	return str;
};


// private methods

/**
 * @access private
 */
CollapsiblePanel.prototype._mouseHandler_Header = function( obj, style ) 
{ 
	if ( this.callerName.length > 0 )
	{
		if ( obj == document.getElementById( this.callerName ) ) 
			return; 
	}
				
	if ( ( obj == this.callerOpen && this.fs == false ) ) 
		return; 
	
	if ( style != "" )
		obj.className = style;
};

/**
 * @access private
 */
CollapsiblePanel.prototype._slide = function( noslide ) 
{ 
	steps = noslide? this.getContentHeight() : this.steps;
		
	if ( this.objOpen == null ) 
	{ 
		this._slideOut( steps ); 
	} 
	else if ( this.objOpen == this.obj ) 
	{ 
		if ( this.ableToCloseSub == true )
			this._slideIn( steps ); 
		else 
			window.clearInterval( this.timer ); 
	}
};

/**
 * @access private
 */
CollapsiblePanel.prototype._slideOut = function( steps ) 
{  
	if ( this.sl == 0 ) 
	{ 
		this._mouseHandler_Header( this.caller, CollapsiblePanel.cssPrefix + "ps", "" ); 
		this.callerOpen = this.caller; 
		window.clearInterval( this.timer ); 
		this.sl = 1; 
		
		return; 
	} 
	
	this.isWorking = true; 
	
	if ( this.toOpen > 0 ) 
		this.obj.style.display = "block";
	
	if ( this.tmpNeedOpen + steps <= this.toOpen ) 
	{ 
		this.obj.style.height = this.tmpNeedOpen + steps; 
		this.tmpNeedOpen      = this.tmpNeedOpen + steps; 
	} 
	else 
	{ 
		window.clearInterval( this.timer ); 
		this.obj.style.height = this.toOpen;
		this._mouseHandler_Header( this.caller, CollapsiblePanel.cssPrefix + "ps", "" ); 
		
		if ( this.callerOpen != null ) 
		{ 
			this.fs = true; 
			this._mouseHandler_Header( this.callerOpen, CollapsiblePanel.cssPrefix + "p", "" ); 
			this.fs = false; 
		} 
		
		this.callerOpen  = this.caller; 
		this.objOpen     = this.obj; 
		this.tmpNeedOpen = 0; 
		this.isWorking   = false; 
		this.toOpen      = -1; 
	} 
};

/**
 * @access private
 */
CollapsiblePanel.prototype._slideIn = function( steps ) 
{ 
	if ( this.sl == 0 ) 
	{ 
		window.clearInterval( this.timer ); 
		this.sl = 1; 
		
		return; 
	} 
	
	this.isWorking = true; 
	
	if ( this.tmpNeedClose - steps < steps ) 
	{ 
		window.clearInterval( this.timer ); 
		
		this.objOpen.style.display = "none"; 
		this.objOpen.style.height  = 1; 
		this.objOpen               = null; 
		this.isWorking             = false; 
		this.tmpNeedClose          = 0; 
		this.fs                    = true; 
		
		this._mouseHandler_Header( this.callerOpen, CollapsiblePanel.cssPrefix + "p", "" ); 
		this.callerOpen = null; 
		this.fs = false; 
	} 
	else 
	{ 
		this.objOpen.style.height = this.tmpNeedClose - steps; 
	} 
	
	this.tmpNeedClose = this.tmpNeedClose - steps; 
};


/**
 * @access public
 * @static
 */
CollapsiblePanel.defaultImagePath = "img/";

/**
 * @access public
 * @static
 */
CollapsiblePanel.defaultIcon = "spacer.gif";

/**
 * @access public
 * @static
 */
CollapsiblePanel.spacerPic = "spacer.gif";

/**
 * @access public
 * @static
 */
CollapsiblePanel.defaultSpeed = 20;

/**
 * @access public
 * @static
 */
CollapsiblePanel.defaultSteps = 15;

/**
 * @access public
 * @static
 */
CollapsiblePanel.cssPrefix = "collapsible-panel-";

/**
 * @access public
 * @static
 */
CollapsiblePanel.count = 0;

/**
 * @access public
 * @static
 */
CollapsiblePanel.panels = new Array();


/**
 * @access public
 * @static
 */
CollapsiblePanel.toggle = function( id )
{
	var obj = CollapsiblePanel.getRef( id );
	obj.toggle();
};

/**
 * @access public
 * @static
 */
CollapsiblePanel.getRef = function( id )
{
	for ( var i in CollapsiblePanel.panels )
	{
		if ( CollapsiblePanel.panels[i].id == id )
			return CollapsiblePanel.panels[i];
	}
};

/**
 * @access private
 * @static
 */
CollapsiblePanel._mouseHandler_Header = function( id, obj, style )
{
	var obj = CollapsiblePanel.getRef( id );
	obj._mouseHandler_Header( obj, style );
};

/**
 * @access private
 * @static
 */
CollapsiblePanel._mouseHandler_Item = function( id, style )
{
	var obj = document.getElementById( id );
	obj.className = style;
};

/**
 * @access private
 * @static
 */
CollapsiblePanel._slide = function( id, noslide )
{
	var obj = CollapsiblePanel.getRef( id );
	obj._slide( noslide );
};
