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
TreeItem = function( sText, sAction, eParent, sIcon, sOpenIcon )
{
	this.TreeAbstractNode = TreeAbstractNode;
	this.TreeAbstractNode( sText, sAction );

	// defaults to close
	this.open = ( CookieUtil.read( this.id.substr( 18, this.id.length - 18 ) ) == '1' )? true : false;
	
	if ( sIcon )
		this.icon = sIcon;
		
	if ( sOpenIcon )
		this.openIcon = sOpenIcon;
	
	if ( eParent )
		eParent.add( this );
};


TreeItem.prototype = new TreeAbstractNode();
TreeItem.prototype.constructor = TreeItem;
TreeItem.superclass = TreeAbstractNode.prototype;

/**
 * @access public
 */
TreeItem.prototype.remove = function()
{
	var iconSrc     = document.getElementById( this.id + '-plus' ).src;
	var parentNode  = this.parentNode;
	var prevSibling = this.getPreviousSibling( true );
	var nextSibling = this.getNextSibling( true );
	var folder      = this.parentNode.folder;
	var last        = ( ( nextSibling ) && ( nextSibling.parentNode ) && ( nextSibling.parentNode.id == parentNode.id ) )? false : true;
	
	this.getPreviousSibling().focus();
	this._remove();
	
	if ( parentNode.childNodes.length == 0 )
	{
		parentNode.folder = false;
		parentNode.open   = false;
	}
	
	if ( !nextSibling )
		parentNode.indent( null, true, last, this._level );
		
	if ( ( prevSibling == parentNode ) && !( parentNode.childNodes.length ) )
	{
		prevSibling.folder = false;
		prevSibling.open   = false;
		
		iconSrc = document.getElementById( prevSibling.id + '-plus' ).src;
		iconSrc = iconSrc.replace( 'minus', '' ).replace( 'plus', '' );
		
		document.getElementById( prevSibling.id + '-plus' ).src = iconSrc;
		document.getElementById( prevSibling.id + '-icon' ).src = TreeConfig.fileIcon;
	}
	
	if ( document.getElementById( prevSibling.id + '-plus' ) )
	{
		if ( parentNode == prevSibling.parentNode )
		{
			iconSrc = iconSrc.replace( 'minus', '' ).replace( 'plus', '' );
			document.getElementById( prevSibling.id + '-plus' ).src = iconSrc;
		}
	}
};

/**
 * @access public
 */
TreeItem.prototype.expand = function()
{
	this.doExpand();
	document.getElementById( this.id + '-plus' ).src = this.minusIcon;
};

/**
 * @access public
 */
TreeItem.prototype.collapse = function( b )
{
	if ( !b )
		this.focus();
		
	this.doCollapse();
	document.getElementById( this.id + '-plus' ).src = this.plusIcon;
};

/**
 * @access public
 */
TreeItem.prototype.getFirst = function()
{
	return this.childNodes[0];
};

/**
 * @access public
 */
TreeItem.prototype.getLast = function()
{
	if ( this.childNodes[this.childNodes.length - 1].open )
		return this.childNodes[this.childNodes.length - 1].getLast();
	else
		return this.childNodes[this.childNodes.length - 1];
};

/**
 * @access public
 */
TreeItem.prototype.getNextSibling = function()
{
	for ( var i = 0; i < this.parentNode.childNodes.length; i++ )
	{
		if ( this == this.parentNode.childNodes[i] )
			break;
	}
	
	if ( ++i == this.parentNode.childNodes.length )
		return this.parentNode.getNextSibling();
	else
		return this.parentNode.childNodes[i];
};

/**
 * @access public
 */
TreeItem.prototype.getPreviousSibling = function( b )
{
	for ( var i = 0; i < this.parentNode.childNodes.length; i++ )
	{
		if ( this == this.parentNode.childNodes[i] )
			break;
	}
	
	if ( i == 0 )
	{
		return this.parentNode;
	}
	else
	{
		if ( ( this.parentNode.childNodes[--i].open ) || ( b && this.parentNode.childNodes[i].folder ) )
			return this.parentNode.childNodes[i].getLast();
		else
			return this.parentNode.childNodes[i];
	}
};

/**
 * @access public
 */
TreeItem.prototype.keydown = function( key )
{
	if ( TreeConfig.allowKeyboardNavigation )
	{
		if ( ( key == 39 ) && ( this.folder ) )
		{
			if ( !this.open )
				this.expand();
			else
				this.getFirst().select();
			
			return false;
		}
		else if ( key == 37 )
		{
			if ( this.open )
				this.collapse();
			else
				this.parentNode.select();
			
			return false;
		}
		else if ( key == 40 )
		{
			if ( this.open )
			{
				this.getFirst().select();
			}
			else
			{
				var sib = this.getNextSibling();
			
				if ( sib )
					sib.select();
			}
		
			return false;
		}
		else if ( key == 38 )
		{
			this.getPreviousSibling().select();
			return false;
		}
	
		return true;
	}
	else
	{
		return true;
	}
};

/**
 * @access public
 */
TreeItem.prototype.toHTML = function( nItem, nItemCount )
{
	var foo = this.parentNode;
	var indent = '';
	
	if ( nItem + 1 == nItemCount )
		this.parentNode._last = true;
		
	var i = 0;
	
	while ( foo.parentNode )
	{
		foo    = foo.parentNode;
		indent = "<img id=\"" + this.id + "-indent-" + i + "\" src=\"" + ( ( foo._last )? TreeConfig.blankIcon : TreeConfig.iIcon ) + "\">" + indent;
		
		i++;
	}
	
	this._level = i;
	
	if ( this.childNodes.length )
		this.folder = 1;
	else
		this.open = false;
		
	if ( ( this.folder ) || ( TreeHandler.behavior != 'classic' ) )
	{
		if ( !this.icon )
			this.icon = TreeConfig.folderIcon;
			
		if ( !this.openIcon )
			this.openIcon = TreeConfig.openFolderIcon;
	}
	else if ( !this.icon )
	{
		this.icon = TreeConfig.fileIcon;
	}
	
	var label = this.text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
	var str = "<div id=\"" + this.id + "\" ondblclick=\"TreeHandler.toggle(this);\" " + ( !this.folder? " item=\"true\"" : "" ) + " class=\"tree-item\" onkeydown=\"return TreeHandler.keydown(this, event)\">";
	str += indent;
	str += "<img id=\"" + this.id + "-plus\" src=\"" + ( ( this.folder )? ( ( this.open )? ( ( this.parentNode._last )? TreeConfig.lMinusIcon : TreeConfig.tMinusIcon) : ( ( this.parentNode._last )? TreeConfig.lPlusIcon : TreeConfig.tPlusIcon ) ) : ( ( this.parentNode._last )? TreeConfig.lIcon : TreeConfig.tIcon ) ) + "\" onclick=\"TreeHandler.toggle(this);\">"
	
	if ( TreeConfig.shortcutMode && !this.open )
	{
		str += "<img id=\"" + this.id + "-icon\" class=\"tree-icon\" src=\"" + ( ( TreeHandler.behavior == 'classic' && this.open )? this.openIcon : this.icon ) + "\" onclick=\"TreeHandler.select(this);\" style=\"margin-right:-16px\"><img id=\"" + this.id + "-icon\" class=\"tree-icon\" src=\"" + TreeConfig.shortcutIcon + "\" onclick=\"TreeHandler.select(this);\"><a href=\"#\" onclick=\"" + this.action + ";return Tree.cancelEvent();\" id=\"" + this.id + "-anchor\"" + ( TreeConfig.enableContextMenu? " oncontextmenu=\"TreeConfig.contextMenuCallbackFn(null,this);\"" : "" ) + " onfocus=\"TreeHandler.focus(this);\" onblur=\"TreeHandler.blur(this);\">" + label + "</a></div>";

	}
	else
	{
		str += "<img id=\"" + this.id + "-icon\" class=\"tree-icon\" src=\"" + ( ( TreeHandler.behavior == 'classic' && this.open )? this.openIcon : this.icon ) + "\" onclick=\"TreeHandler.select(this);\"><a href=\"#\" onclick=\"" + this.action + ";return Tree.cancelEvent();\" id=\"" + this.id + "-anchor\"" + ( TreeConfig.enableContextMenu? " oncontextmenu=\"TreeConfig.contextMenuCallbackFn(null,this);\"" : "" ) + " onfocus=\"TreeHandler.focus(this);\" onblur=\"TreeHandler.blur(this);\">" + label + "</a></div>";
	}
	
	str += "<div id=\"" + this.id + "-cont\" class=\"tree-container\" style=\"display: " + ( ( this.open )? 'block' : 'none' ) + ";\">";

	for ( var i = 0; i < this.childNodes.length; i++ )
		str += this.childNodes[i].toHTML( i, this.childNodes.length );
	
	str += "</div>";
	
	this.plusIcon  = ( ( this.parentNode._last )? TreeConfig.lPlusIcon  : TreeConfig.tPlusIcon  );
	this.minusIcon = ( ( this.parentNode._last )? TreeConfig.lMinusIcon : TreeConfig.tMinusIcon );
	
	return str;
};


// private methods

/**
 * @access private
 */
TreeItem.prototype._remove = function()
{
	for ( var i = this.childNodes.length - 1; i >= 0; i-- )
		this.childNodes[i]._remove();
 	
	for ( var i = 0; i < this.parentNode.childNodes.length; i++ )
	{
		if ( this == this.parentNode.childNodes[i] )
		{
			for ( var j = i; j < this.parentNode.childNodes.length; j++ )
				this.parentNode.childNodes[j] = this.parentNode.childNodes[j+1];
			
			this.parentNode.childNodes.length -= 1;
			
			if ( i + 1 == this.parentNode.childNodes.length )
				this.parentNode._last = true;
				
			break;
		}
	}
	
	TreeHandler.all[this.id] = null;
	
	if ( document.getElementById( this.id ) )
	{
		var tmp = document.getElementById( this.id );
		tmp.parentNode.removeChild( tmp );
	}
};
