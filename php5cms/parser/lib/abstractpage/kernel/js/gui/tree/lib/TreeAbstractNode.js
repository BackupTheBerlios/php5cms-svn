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
TreeAbstractNode = function( sText, sAction )
{
	this.Base = Base;
	this.Base();
	
	this.childNodes  = [];
	this.id     = TreeHandler.getId();
	this.text   = sText   || TreeConfig.defaultText;
	this.action = sAction || TreeConfig.defaultAction;
	this._last  = false;
	
	TreeHandler.all[this.id] = this;
};


TreeAbstractNode.prototype = new Base();
TreeAbstractNode.prototype.constructor = TreeAbstractNode;
TreeAbstractNode.superclass = Base.prototype;

/**
 * To speed thing up if you're adding multiple nodes at once (after load)
 * use the bNoIdent parameter to prevent automatic re-indentation and call
 * the obj.ident() method manually once all nodes has been added.
 *
 * @access public
 */
TreeAbstractNode.prototype.add = function( node, bNoIdent )
{
	node.parentNode = this;
	this.childNodes[this.childNodes.length] = node;
	var root = this;
	
	if ( this.childNodes.length >= 2 )
		this.childNodes[this.childNodes.length -2]._last = false;
	
	while ( root.parentNode )
		root = root.parentNode;
		
	if ( root.rendered )
	{
		if ( this.childNodes.length >= 2 )
		{
			document.getElementById( this.childNodes[this.childNodes.length -2].id + '-plus' ).src = ( ( this.childNodes[this.childNodes.length -2].folder )? ( ( this.childNodes[this.childNodes.length -2].open )? TreeConfig.tMinusIcon : TreeConfig.tPlusIcon ) : TreeConfig.tIcon );
			
			if ( this.childNodes[this.childNodes.length - 2].folder )
			{
				this.childNodes[this.childNodes.length - 2].plusIcon  = TreeConfig.tPlusIcon;
				this.childNodes[this.childNodes.length - 2].minusIcon = TreeConfig.tMinusIcon;
			}
			
			this.childNodes[this.childNodes.length -2]._last = false;
		}
		
		this._last = true;
		var foo = this;
		
		while ( foo.parentNode )
		{
			for ( var i = 0; i < foo.parentNode.childNodes.length; i++ )
			{
				if ( foo.id == foo.parentNode.childNodes[i].id )
					break;
			}
			
			if ( ++i == foo.parentNode.childNodes.length )
				foo.parentNode._last = true;
			else
				foo.parentNode._last = false;
				
			foo = foo.parentNode;
		}
		
		TreeHandler.insertHTMLBeforeEnd( document.getElementById( this.id + '-cont' ), node.toHTML() );
		
		if ( ( !this.folder ) && ( !this.openIcon ) )
		{
			this.icon = TreeConfig.folderIcon;
			this.openIcon = TreeConfig.openFolderIcon;
		}
		
		if ( !this.folder )
		{
			this.folder = true;
			this.collapse( true );
		}
		
		if ( !bNoIdent )
			this.indent();
	}
	
	return node;
};

/**
 * @access public
 */
TreeAbstractNode.prototype.toggle = function()
{
	if ( this.folder )
	{
		if ( this.open )
			this.collapse();
		else
			this.expand();
	}
};

/**
 * @access public
 */
TreeAbstractNode.prototype.select = function()
{
	document.getElementById( this.id + '-anchor' ).focus();
};

/**
 * @access public
 */
TreeAbstractNode.prototype.deSelect = function()
{
	document.getElementById( this.id + '-anchor' ).className = '';
	TreeHandler.selected = null;
};

/**
 * @access public
 */
TreeAbstractNode.prototype.focus = function()
{
	if ( ( TreeHandler.selected ) && ( TreeHandler.selected != this ) )
		TreeHandler.selected.deSelect();
		
	TreeHandler.selected = this;
	
	if ( ( this.openIcon ) && ( TreeHandler.behavior != 'classic' ) )
		document.getElementById( this.id + '-icon' ).src = this.openIcon;
		
	document.getElementById( this.id + '-anchor' ).className = 'selected';
	document.getElementById( this.id + '-anchor' ).focus();
	
	if ( TreeHandler.onSelect )
		TreeHandler.onSelect( this );
};

/**
 * @access public
 */
TreeAbstractNode.prototype.blur = function()
{
	if ( ( this.openIcon ) && ( TreeHandler.behavior != 'classic' ) )
		document.getElementById( this.id + '-icon' ).src = this.icon;
		
	document.getElementById( this.id + '-anchor' ).className = 'selected-inactive';
};

/**
 * @access public
 */
TreeAbstractNode.prototype.doExpand = function()
{
	if ( TreeHandler.behavior == 'classic' )
		document.getElementById( this.id + '-icon' ).src = this.openIcon;
		
	if ( this.childNodes.length )
		document.getElementById( this.id + '-cont' ).style.display = 'block';
		
	this.open = true;
	CookieUtil.save( this.id.substr( 18, this.id.length - 18 ), '1' );
};

/**
 * @access public
 */
TreeAbstractNode.prototype.doCollapse = function()
{
	if ( TreeHandler.behavior == 'classic' )
		document.getElementById( this.id + '-icon' ).src = this.icon;
		
	if ( this.childNodes.length )
		document.getElementById( this.id + '-cont' ).style.display = 'none';
		
	this.open = false;
	CookieUtil.save( this.id.substr( 18, this.id.length - 18 ), '0' );
};

/**
 * @access public
 */
TreeAbstractNode.prototype.expandAll = function()
{
	this.expandChildren();
	
	if ( ( this.folder ) && ( !this.open ) )
		this.expand();
};

/**
 * @access public
 */
TreeAbstractNode.prototype.expandChildren = function()
{
	for ( var i = 0; i < this.childNodes.length; i++ )
		this.childNodes[i].expandAll();
};

/**
 * @access public
 */
TreeAbstractNode.prototype.collapseAll = function()
{
	this.collapseChildren();
	
	if ( ( this.folder ) && ( this.open ) )
		this.collapse( true );
};

/**
 * @access public
 */
TreeAbstractNode.prototype.collapseChildren = function()
{
	for ( var i = 0; i < this.childNodes.length; i++ )
		this.childNodes[i].collapseAll();
};

/**
 * @access public
 */
TreeAbstractNode.prototype.indent = function( lvl, del, last, level )
{
	// Since we only want to modify items one level below ourself,
	// and since the rightmost indentation position is occupied by
	// the plus icon we set this to -2
	
	if ( lvl == null )
		lvl = -2;
		
	var state = 0;
	
	for ( var i = this.childNodes.length - 1; i >= 0 ; i-- )
	{
		state = this.childNodes[i].indent( lvl + 1, del, last, level );
		
		if ( state )
			return;
	}
	
	if ( del )
	{
		if ( ( level >= this._level ) && ( document.getElementById( this.id + '-plus' ) ) )
		{
			if ( this.folder )
			{
				document.getElementById( this.id + '-plus' ).src = ( this.open )? TreeConfig.lMinusIcon : TreeConfig.lPlusIcon;
				
				this.plusIcon  = TreeConfig.lPlusIcon;
				this.minusIcon = TreeConfig.lMinusIcon;
			}
			else
			{
				document.getElementById( this.id + '-plus' ).src = TreeConfig.lIcon;
			}
			
			return 1;
		}
	}
	
	var foo = document.getElementById( this.id + '-indent-' + lvl );
	
	if ( foo )
	{
		if ( ( del ) && ( last ) )
			foo._last = true;
			
		if ( foo._last )
			foo.src = TreeConfig.blankIcon;
		else
			foo.src = TreeConfig.iIcon;
	}

	return 0;
};
