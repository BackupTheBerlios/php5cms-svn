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
 * @package xml_xslt
 */
 
/**
 * Constructor
 *
 * @access public
 */
XSLTKey = function()
{
	this.Base = Base;
	this.Base();
};


XSLTKey.prototype = new Base();
XSLTKey.prototype.constructor = XSLTKey;
XSLTKey.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
XSLTKey.addXSLTKey = function(Name, Match, Use, bModified)
{
	// Create a new xsl:key.
	if ( !bModified )
	{
		var nodeKey = theStylesheet.createNode( 1, "xsl:key", XSLTKey.xsltNamespaceURI );
		nodeKey.setAttribute( "name",  Name  );
		nodeKey.setAttribute( "match", Match );
		nodeKey.setAttribute( "use",   Use   );
			
		nodeStylesheet.insertBefore( nodeKey, nodeStylesheetParam );	
	}
	// Modify an existing xsl:key -- new values for @match, @use
	else
	{
		XSLTKey.modifyXSLTKey( Name, Name, Match, Use );
		
		var nodeKey = theStylesheet.selectSingleNode( "/xsl:stylesheet/xsl:key[@name='" + Name + "']" );
		nodeKey.setAttribute( "match", Match );
		nodeKey.setAttribute( "use",   Use   );
	}
};

/**
 * @access public
 * @static
 */
XSLTKey.modifyXSLTKey = function( Name, newName, newMatch, newUse )
{
	var nodeKey = theStylesheet.selectSingleNode( "/xsl:stylesheet/xsl:key[@name='" + Name + "']" );
	nodeKey.setAttribute( "name",  newName  );
	nodeKey.setAttribute( "match", newMatch );
	nodeKey.setAttribute( "use",   newUse   );
};

/**
 * @access public
 * @static
 */
XSLTKey.deleteXSLTKey = function( varName )
{
	var nodeKey = theStylesheet.selectSingleNode( "/xsl:stylesheet/xsl:key[@name='" + varName + "']" );
	nodeKey = nodeStylesheet.removeChild( nodeKey );
	nodeKey = null;
};

/**
 * @access public
 * @static
 */
XSLTKey.removeXSLTKeys = function( ExistingKeys )
{
	var i = ExistingKeys.length - 1;
		
	while ( i >= 0 )
	{
		var varEntry = ExistingKeys[i];
		var idx  = varEntry.indexOf( XSLTKey.theDelim );
		var name = varEntry.substr( 0, idx );

		XSLTKey.deleteXSLTKey(name);
		i--;
	}
};

/**
 * @access public
 * @static
 */
XSLTKey.restoreXSLTKeys = function( ExistingKeys )
{
	var i = ExistingKeys.length - 1;
		
	while ( i >= 0 )
	{
		var varEntry = ExistingKeys[i];
		var arrCells = new Array( 3 );
		arrCells  = varEntry.split( XSLTKey.theDelim );
		var name  = arrCells[0];
		var match = arrCells[1];
		var use   = arrCells[2];

		XSLTKey.addXSLTKey(name, match, use, false);
		i--;
	}
};


/**
 * @access public
 * @static
 */
XSLTKey.theDelim = "\r";

/**
 * @access public
 * @static
 */
XSLTKey.xsltNamespaceURI = "http://www.w3.org/1999/XSL/Transform";
