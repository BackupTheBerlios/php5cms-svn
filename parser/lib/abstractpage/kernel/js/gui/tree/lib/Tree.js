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
 * Constructor
 *
 * @access public
 */
Tree = function( sText, sAction, sBehavior, sIcon, sOpenIcon )
{
	this.TreeAbstractNode = TreeAbstractNode;
	this.TreeAbstractNode( sText, sAction );

	this.icon      = sIcon     || TreeConfig.rootIcon;
	this.openIcon  = sOpenIcon || TreeConfig.openRootIcon;

	// defaults to open
	this.open      = ( CookieUtil.read( this.id.substr( 18, this.id.length - 18 ) ) == '0' )? false : true;
	this.folder    = true;
	this.rendered  = false;
	this.onSelect  = null;
	
	if ( !TreeHandler.behavior )
		TreeHandler.behavior = sBehavior || TreeConfig.defaultBehavior;
};


Tree.prototype = new TreeAbstractNode();
Tree.prototype.constructor = Tree;
Tree.superclass = TreeAbstractNode.prototype;

/**
 * @access public
 */
Tree.prototype.setBehavior = function( sBehavior )
{
	TreeHandler.behavior =  sBehavior;
};

/**
 * @access public
 */
Tree.prototype.getBehavior = function( sBehavior )
{
	return TreeHandler.behavior;
};

/**
 * @access public
 */
Tree.prototype.getSelected = function()
{
	if ( TreeHandler.selected )
		return TreeHandler.selected;
	else
		return null;
};

/**
 * @access public
 */
Tree.prototype.remove = function()
{
};

/**
 * @access public
 */
Tree.prototype.expand = function()
{
	this.doExpand();
};

/**
 * @access public
 */
Tree.prototype.collapse = function( b )
{
	if ( !b )
		this.focus();
		
	this.doCollapse();
};

/**
 * @access public
 */
Tree.prototype.getFirst = function()
{
	return null;
};

/**
 * @access public
 */
Tree.prototype.getLast = function()
{
	return null;
};

/**
 * @access public
 */
Tree.prototype.getNextSibling = function()
{
	return null;
};

/**
 * @access public
 */
Tree.prototype.getPreviousSibling = function()
{
	return null;
};

/**
 * @access public
 */
Tree.prototype.keydown = function( key )
{
	if ( TreeConfig.allowKeyboardNavigation )
	{
		if ( key == 39 )
		{
			if ( !this.open )
				this.expand();
			else if ( this.childNodes.length )
				this.childNodes[0].select();
			
			return false;
		}
	
		if ( key == 37 )
		{
			this.collapse();
			return false;
		}
	
		if ( ( key == 40 ) && ( this.open ) && ( this.childNodes.length ) )
		{
			this.childNodes[0].select();
			return false;
		}
	
		return true;
	}
	else
	{
		return false;
	}
};

/**
 * @access public
 */
Tree.prototype.toHTML = function()
{
	var str = "<div id=\"" + this.id + "\" ondblclick=\"TreeHandler.toggle(this);\" class=\"tree-item\" onkeydown=\"return TreeHandler.keydown(this, event)\">";	
	str += "<img id=\"" + this.id + "-icon\" class=\"tree-icon\" src=\"" + ( ( TreeHandler.behavior == 'classic' && this.open )? this.openIcon:this.icon ) + "\" onclick=\"TreeHandler.select(this);\"><a href=\"#\" onclick=\"" + this.action + ";return Tree.cancelEvent();\" id=\"" + this.id + "-anchor\" onfocus=\"TreeHandler.focus(this);\" onblur=\"TreeHandler.blur(this);\">" + this.text + "</a></div>";
	str += "<div id=\"" + this.id + "-cont\" class=\"tree-container\" style=\"display: " + ( ( this.open )? 'block' : 'none' ) + ";\">";
	
	for ( var i = 0; i < this.childNodes.length; i++ )
		str += this.childNodes[i].toHTML( i, this.childNodes.length );
	
	str += "</div>";
	this.rendered = true;
	
	return str;
};

/**
 * @access public
 */
Tree.prototype.paint = function()
{
	document.body.insertAdjacentHTML( "afterBegin", this.toHTML() );
};


/**
 * @access public
 * @static
 */
Tree.cancelEvent = function()
{
	window.event.returnValue  = false;
	window.event.cancelBubble = true;
				
	return false;
};
