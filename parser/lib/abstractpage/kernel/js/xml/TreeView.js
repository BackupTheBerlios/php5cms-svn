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
 * @package xml
 */
 
/**
 * Constructor
 *
 * @access public
 */
TreeView = function( fm )
{
	this.Base = Base;
	this.Base();
};


TreeView.prototype = new Base();
TreeView.prototype.constructor = TreeView;
TreeView.superclass = Base.prototype;

/**
 * @constant
 */
TreeView.ELEMENT_NODE = 1;

/**
 * @constant
 */
TreeView.ATTRIBUTE_NODE = 2;

/**
 * @constant
 */
TreeView.NAMESPACE_NODE = 7; // 7 is for processing instruction

/**
 * @constant
 */
TreeView.TEXT_NODE = 3;

/**
 * @constant
 */
TreeView.COMMENT_NODE = 8;

/**
 * @constant
 */
TreeView.CDATA_NODE = 4;

/**
 * @constant
 */
TreeView.ENTITYREF_NODE = 5;

/**
 * Name of the attribute add to the tree to point to the span id.
 * @access public
 * @static
 */
TreeView.attID = "tv:TreeViewerID";

/**
 * Name of the namespace that will hold the attribute that is added.
 * @access public
 * @static
 */
TreeView.nsName = "msTreeViewer";

/**
 * The Tree Viewer namespace node.
 * @access public
 * @static
 */
TreeView.treeViewNS = "urn:tv";

/**
 * @access public
 * @static
 */
TreeView.imgPath = "../img/treeview/";

/**
 * @access public
 * @static
 */
TreeView.blue = "#0000FF";

/**
 * @access public
 * @static
 */
TreeView.black = "#000000";

/**
 * @access public
 * @static
 */
TreeView.attributeColor = "green";

/**
 * @access public
 * @static
 */
TreeView.textColor = "red";

/**
 * @access public
 * @static
 */
TreeView.commentColor = "purple";


/**
 * @access public
 * @static
 */
TreeView.windowLoad = function()
{
	TreeView.displayFrame = parent.display;

	TreeView.displayFrame.document.open();
	TreeView.displayFrame.document.write( "" );            
	TreeView.displayFrame.document.close();

	selectionString.value = "";
	XMLFile.focus();
};

/**
 * Automatically click button when user presses enter.
 *
 * @access public
 * @static
 */
TreeView.loadEnter = function()
{
	// 13 is keyCode for enter.
	if ( event.keyCode == 13 )
	{
		loadButton.click();
		selectionString.focus();
	}
};

/**
 * Automatically click button when user presses enter.
 *
 * @access public
 * @static
 */
TreeView.selectionEnter = function()
{
	if ( event.keyCode == 13 )
		selectionButton.click();
};

/**
 * @access public
 * @static
 */
TreeView.genericSelect = function()
{
	if ( xmldoc == null )
	{
		selectionString.value = "";
		return Base.raiseError( "No xml file has been loaded." );
	}
	else
	{
		// Only choices are nodeList, node or attributes.
		// This try...catch block needs JScript version 5 which comes with IE5.
		try
		{
			selection = eval( selectionString.value );
		}
		catch( e )
		{
			selection = null;
		}

		if ( selection == null )
		{
			return Base.raiseError( "Only element nodes, attribute nodes and node lists can be specified." );
		}
        else
		{
			// Unselect previously selected nodes.
			if ( TreeView.selectedNodesLength != 0 )
			{
				for ( var i = 0; i < TreeView.selectedNodesLength; i++ )
				{
					if ( TreeView.selectedNodesType[i] == "attribute" )
						parent.display.document.all.item( TreeView.selectedNodes[i] ).style.color = TreeView.attributeColor;
					else
						parent.display.document.all.item( TreeView.selectedNodes[i] ).style.color = TreeView.black;
				}
			}

			TreeView.selectedNodesLength = 0;

			// Call appropriate handler.
			if ( selection.length != null ) 
				TreeView.selectNodeList( selection );
			else if ( selection.nodeTypeString == "element" )  
				TreeView.selectNode( selection );
			else if ( selection.nodeTypeString == "attribute" )    
				TreeView.selectAtt( selection );
			else
				return Base.raiseError( "Only element nodes, attribute nodes and node lists can be specified." );
		}
	}
};

/**
 * Highlight an attribute node.
 *
 * @access public
 * @static
 */
TreeView.selectAtt = function( att )
{
	var elem = att.selectSingleNode( "ancestor(.)" );
	attNum = elem.attributes.length;

	// Find the offset for the attribute.
	for ( var i = 0; i < attNum; i++ )
	{
		if ( elem.attributes.item( i ).nodeName == att.nodeName )
			break;
	}
	
	if ( elem.attributes.getNamedItem( TreeView.attID ) != null )
	{
		var nodeID = elem.attributes.getNamedItem( TreeView.attID ).nodeValue + "_att_" + i;
		TreeView.selectedNodes[TreeView.selectedNodesLength] = nodeID;
        TreeView.selectedNodesType[TreeView.selectedNodesLength] = "attribute";
        TreeView.selectedNodesLength++;
        parent.frames( "display" ).document.all( nodeID ).style.color = TreeView.blue;
	}
};

/**
 * Highlight an element node.
 *
 * @access public
 * @static
 */
TreeView.selectNode = function( node )
{
	var nodeID;
	
	if ( node.attributes.getNamedItem( TreeView.attID ) != null )
	{
		nodeID = node.attributes.getNamedItem( TreeView.attID ).nodeValue;
		TreeView.selectedNodes[TreeView.selectedNodesLength] = nodeID;
        TreeView.selectedNodesType[TreeView.selectedNodesLength] = "element"; 
        TreeView.selectedNodesLength++;
        parent.display.document.all( nodeID ).style.color = TreeView.blue;
	}
};

/**
 * Highlight a node list.
 *
 * @access public
 * @static
 */
TreeView.selectNodeList = function( list )
{
	TreeView.selectedNodesLength = list.length;

	for ( var i = 0; i < list.length; i++ )
	{
		// Only highlight elems and attrs (other stuff isn't in tree).
		if ( list( i ).nodeTypeString == "element" )
			TreeView.selectNode( list( i ) );
		else if ( list( i ).nodeTypeString == "attribute" )    
			TreeView.selectAtt( list( i ) );
	}
};

/**
 * Create the msxml object and load the specified xml file.
 *
 * @access public
 * @static
 */
TreeView.loadXML = function()
{
	if ( XMLFile.value == "" )
	{
		return Base.raiseError( "An XML file must be specified for loading to occur." );
	}
	else
	{
		xmldoc.async = false;
		xmldoc.load( XMLFile.value );

		if ( xmldoc.parseError.errorCode != 0 )
		{
			TreeView.windowLoad(); 
			return Base.raiseError( errtxt );
		}

		if ( xmldoc.documentElement != null )
		{
			TreeView.displayTree();
			selectionString.value = "xmldoc.documentElement";
		}
	}
};

/**
 * This function isolates the process of add the xml to the tree view.
 *
 * @access public
 * @static
 */
TreeView.addhtml = function( text )
{
	TreeView.strStruct += text;
};

/**
 * Use the recursive function buildTree to build the html
 * version of the tree. Display the tree in a seperate frame.
 *
 * @access public
 * @static
 */
TreeView.displayTree = function()
{
	if ( xmldoc == null )
	{
		return Base.raiseError( "No xml file has been loaded." );
	}
	else
	{
		TreeView.selectedNodes       = new Array();
		TreeView.selectedNodesType   = new Array();
		TreeView.selectedNodesLength = 0;

		// TreeView.displayFrame = parent.display;
		// TreeView.displayFrame.document.open();
		
		TreeView.strStruct  = "";
		TreeView.elementNum = 0;
		TreeView.code       = new Array();

        TreeView.buildTree( xmldoc.documentElement, 0, 0 );
		
        TreeView.displayFrame.document.open();
        TreeView.displayFrame.document.write( TreeView.strStruct );
        TreeView.displayFrame.document.close();
	}
};

/**
 * Note: buildTree is recursive.
 *
 * @param  node
 * @param  int      level  is the depth of the tree.
 * @param  boolean  last   boolean to tell the element if it's the last one
 *                         in the list. 1=true, 0=false. The last one needs corner.gif.
 *
 * @access public
 * @static
 */
TreeView.buildTree = function( node, level, last )
{ 
	if ( level != 0 )
	{
		for ( var j = 0; j < ( level - 1 ); j++ )
		{
			if ( TreeView.code[j] == 0 )
				TreeView.addhtml( "<IMG SRC=\"" + TreeView.imgPath + "vertical.gif\" ALIGN=\"absbottom\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" );
			else
				TreeView.addhtml( "<IMG SRC=\"" + TreeView.imgPath + "blank.gif\" ALIGN=\"absbottom\">&nbsp;&nbsp;&nbsp;" );
		}

		// Add the appropriate corner piece.
		if ( last == 1 )
			TreeView.addhtml( "<IMG SRC=\"" + TreeView.imgPath + "corner.gif\" ALIGN=\"absbottom\">&nbsp;" );
		else
			TreeView.addhtml( "<IMG SRC=\"" + TreeView.imgPath + "continue.gif\" ALIGN=\"absbottom\">&nbsp;" );
	}

	// Process a text node.
	if ( node.nodeType == TreeView.TEXT_NODE )
	{
		TreeView.addhtml( "<FONT COLOR=\"" + TreeView.textColor + "\">" + node.text + "</FONT>" );
	}
	else if ( node.nodeType == TreeView.COMMENT_NODE )
	{
		TreeView.addhtml( "<FONT COLOR=\"" + TreeView.commentColor + "\">" + node.text + "</FONT>" );
	}
	else
	{
		var elementID = "TreeViewer_" + TreeView.elementNum;
		TreeView.addhtml( "<SPAN ID=\"" + elementID + "\">" + node.nodeName + "</SPAN>" );
		TreeView.elementNum++;

		var tvNode = xmldoc.createNode( "attribute", TreeView.attID, TreeView.treeViewNS );
		tvNode.text = elementID;
		node.attributes.setNamedItem( tvNode );

        // Display attributes.
        var attNum = node.attributes.length;

		if ( attNum > 0 )
		{
			for ( var i = 0; i < attNum; i++ )
			{
				// Don't display attributes from the Tree Viewer namespace.
				if ( node.attributes.item(i).namespace != null )
				{
					if ( node.attributes.item(i).namespace != TreeView.treeViewNS )
						TreeView.addhtml( "<B> ; </B><SPAN ID=\"" + elementID + "_att_" + i + "\" STYLE=\"color:green\">" + node.attributes.item( i ).nodeName + "</SPAN>" );
				}
				else
				{
					TreeView.addhtml( "<B> ; </B><SPAN ID=\"" + elementID + "_att_" + i + "\" STYLE=\"color:green\" >" + node.attributes.item( i ).nodeName + "</SPAN>" );
				}
			}
		}
	}

	TreeView.addhtml( "<BR>" );

	// If there are children under this node, call buildTree on them.
	var children = node.childNodes.length;
	
	if ( children != 0 )
	{
		for ( var i = 0; i < children; i++ )
		{
			if ( i == ( children - 1 ) )
			{
				// This case means we are at the last element.
				TreeView.code[level] = 1;
				TreeView.buildTree( node.childNodes.item( i ), ( level + 1 ), 1 );
			}
			else
			{
				TreeView.code[level] = 0;
				TreeView.buildTree( node.childNodes.item( i ), ( level + 1 ), 0 );
			}
		}
	}
};

/** 
 * Displaying XML in a seperate window functions.
 *
 * @access public
 * @static
 */
TreeView.showXML = function()
{
	if ( xmldoc == null )
	{
		return Base.raiseError( "No xml file has been loaded." );
	}
	else
	{
		var txtToShow = TreeView.dumpTree( xmldoc.documentElement, 0 );
		var wNew = window.open();
		wNew.document.body.innerHTML = "<B>" + txtToShow + "</B>";
	}
};

/**
 * Format the XML into HTML.
 *
 * @access public
 * @static
 */
TreeView.dumpTree = function( node, i )
{
	var result = "<DL class=xml><DD>";

	if ( node != null )
	{
		if ( node.nodeTypeString == "comment" )
		{
			result += "<span class=comment>&lt;!--" + node.text + "--&gt;</span>" + "</DD></DL>";
			return result;
		}

		result += "<span class=tag>&lt;" + node.nodeName + "</span>";
		var num;

		// process the attributes
		if ( node.attributes.length > 0 )
		{
			var a, i, l;
			l = node.attributes.length;
			
			for ( i = 0; i < l; i++ )
			{
				a = node.attributes.item( i );
				
				// Don't display attributes from the Tree Viewer namespace.
				if ( a.namespace != TreeView.treeViewNS )
					result += "<span class=attr> " + a.nodeName + "=\"" + a.text + "\"</span>"
			}
		}    

		if ( node.childNodes != null )
			num = node.childNodes.length;
		else
			num = 0;

		// close the element tag (if empty, use shorthand)

		// tag is empty
		if ( num == 0 )
			result += "<span class=tag>/&gt;</span>";
		else
			result += "<span class=tag>&gt;</span>";

		// process the children of the element if it has any

		// tag has children
        if ( num > 0 )
		{
			if ( TreeView.isMixed( node, num ) > 0 )
			{ 
				result += node.text;
			}
			else
			{
				var j;
				
				for ( j = 0; j < num; j++ )
				{
					result += "\n";
					var child = node.childNodes.item( j );

					result += TreeView.dumpTree( child, i + 1 );
				}
			}

			result += "<span class=tag>&lt;/" + node.nodeName + "&gt;</span>\n";
		}
	}

	result += "</DD></DL>"
	return result;
};

/**
 * Checks to see if all children of the element are the same node type.
 *
 * @access public
 * @static
 */
TreeView.isMixed = function( node, num )
{
	var j;

	for ( j = 0; j < num; j++ )
	{
		var child = node.childNodes.item( j );
		var type  = child.nodeTypeString;
		
		if ( type == "text" || type == "cdata_section" || type == "entity_reference" )
			return 1;
	}

	return 0;
};
