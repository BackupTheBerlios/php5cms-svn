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
MenuLoadHelper = function()
{
	this.Base = Base;
	this.Base();
};


MenuLoadHelper.prototype = new Base();
MenuLoadHelper.prototype.constructor = MenuLoadHelper;
MenuLoadHelper.superclass = Base.prototype;

/**
 * creates the xmlhttp object and starts the load of the xml document.
 *
 * @access public
 * @static
 */
MenuLoadHelper.startLoadXmlTree = function( sSrc, jsNode )
{
	if ( jsNode.loading || jsNode.loaded )
		return;
		
	jsNode.loading = true;
	
	var parser  = new XMLParser();
	var success = parser.loadXMLFailsafe( sSrc, function( xmlparser )
	{
		XLoadTreeHelper.xmlFileLoaded( xmlparser, jsNode );
	} );
};

/**
 * Converts an xml tree to a js tree.
 *
 * @access public
 * @static
 */
MenuLoadHelper.xmlTreeToJsTree = function( oNode )
{
	// retrieve attributes
	var text       = oNode.getAttribute( "text"     );
	var action     = oNode.getAttribute( "action"   );
	var parent     = null;
	var icon       = oNode.getAttribute( "icon"     );
	var openIcon   = oNode.getAttribute( "openIcon" );
	var src        = oNode.getAttribute( "src"      );
	var objectID   = oNode.getAttribute( "objectid" ); // Abstractpage extension : object id
	var helpHandle = oNode.getAttribute( "handle"   ); // Abstractpage extension : help handle
	
	// create jsNode
	var jsNode;
	
	if ( src != null && src != "" )
		jsNode = new XLoadTreeItem( text, src, action, parent, icon, openIcon, objectID, helpHandle );
	else
		jsNode = new XTreeItem( text, action, parent, icon, openIcon, objectID, helpHandle );
		
	// go through childNodes
	var cs = oNode.childNodes;
	var l  = cs.length;
	
	for ( var i = 0; i < l; i++ )
	{
		if ( cs[i].tagName == "tree" )
			jsNode.add( XLoadTreeHelper.xmlTreeToJsTree( cs[i] ), true );
	}
	
	return jsNode;
};

/**
 * Inserts an xml document as a subtree to the provided node.
 *
 * @access public
 * @static
 */
MenuLoadHelper.xmlFileLoaded = function( oXmlDoc, jsParentNode )
{
	if ( jsParentNode.loaded )
		return;

	var bIndent      = false;
	var bAnyChildren = false;
	
	jsParentNode.loaded  = true;
	jsParentNode.loading = false;

	// check that the load of the xml file went well
	if ( oXmlDoc == null || oXmlDoc.documentElement == null )
	{
		jsParentNode.errorText = XLoadTreeHelper.parseTemplateString( XTreeConfig.loadErrorTextTemplate, jsParentNode.src );
	}
	else
	{
		// there is one extra level of tree elements
		var root = oXmlDoc.documentElement;

		// loop through all tree children
		var cs = root.childNodes;
		var l  = cs.length;
		
		for ( var i = 0; i < l; i++ )
		{
			if ( cs[i].tagName == "tree" )
			{
				bAnyChildren = true;
				bIndent = true;
				
				jsParentNode.add( XLoadTreeHelper.xmlTreeToJsTree( cs[i] ), true );
			}
		}

		// if no children we got an error
		if ( !bAnyChildren )
			jsParentNode.errorText = XLoadTreeHelper.parseTemplateString( XTreeConfig.emptyErrorTextTemplate, jsParentNode.src );
	}
	
	// remove dummy
	if ( jsParentNode._loadingItem != null )
	{
		jsParentNode._loadingItem.remove();
		bIndent = true;
	}
	
	if ( bIndent )
	{
		// indent now that all items are added
		jsParentNode.indent();
	}
};

/**
 * Parses a string and replaces %n% with argument nr n.
 *
 * @access public
 * @static
 */
MenuLoadHelper.parseTemplateString = function( sTemplate )
{
	var args = arguments;
	var s = sTemplate;
	
	s = s.replace(/\%\%/g, "%");
	
	for ( var i = 1; i < args.length; i++ )
		s = s.replace( new RegExp( "\%" + i + "\%", "g" ), args[i] )
	
	return s;
};
