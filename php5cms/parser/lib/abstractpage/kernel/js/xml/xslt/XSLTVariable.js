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
XSLTVariable = function()
{
	this.Base = Base;
	this.Base();
};


XSLTVariable.prototype = new Base();
XSLTVariable.prototype.constructor = XSLTVariable;
XSLTVariable.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
XSLTVariable.addXSLTVariable = function( varName, expression, bModified )
{
	// Create a new xsl:variable.
	if ( !bModified )
	{
		var nodeVariable = theStylesheet.createNode( 1, "xsl:variable", XSLTVariable.xsltNamespaceURI );
		nodeVariable.setAttribute( "name",   varName    );
		nodeVariable.setAttribute( "select", expression );
			
		nodeStylesheet.insertBefore( nodeVariable, nodeStylesheetParam );	
	}
	// Modify an existing xsl:variable -- a new value for @select.
	else
	{
		var nodeVariable = theStylesheet.selectSingleNode( "/xsl:stylesheet/xsl:variable[@name='" + varName + "']" );
		nodeVariable.setAttribute( "select", expression );
	}
};

/**
 * @access public
 * @static
 */
XSLTVariable.modifyXSLTVariable = function( varName, newName, newValue )
{
	var nodeVariable = theStylesheet.selectSingleNode( "/xsl:stylesheet/xsl:variable[@name='" + varName + "']" );
	nodeVariable.setAttribute( "name",   newName  );
	nodeVariable.setAttribute( "select", newValue );
};

/**
 * @access public
 * @static
 */
XSLTVariable.deleteXSLTVariable = function( varName )
{
	var nodeVariable = theStylesheet.selectSingleNode( "/xsl:stylesheet/xsl:variable[@name='" + varName + "']" );
	nodeVariable = nodeStylesheet.removeChild( nodeVariable );
	nodeVariable = null;
};

/**
 * @access public
 * @static
 */
XSLTVariable.removeXSLTVariables = function( ExistingVariables )
{
	var i = ExistingVariables.length - 1;
		
	while ( i >= 0 )
	{
		var varEntry = ExistingVariables[i];
		var idx  = varEntry.indexOf( XSLTVariable.theDelim );
		var name = varEntry.substr( 0, idx );

		XSLTVariable.deleteXSLTVariable(name);
		i--;
	}
};

/**
 * @access public
 * @static
 */
XSLTVariable.restoreXSLTVariables = function( ExistingVariables )
{
	var i = ExistingVariables.length - 1;
		
	while ( i >= 0 )
	{
		var varEntry = ExistingVariables[i];
		var idx   = varEntry.indexOf( XSLTVariable.theDelim );
		var name  = varEntry.substr( 0, idx  );
		var value = varEntry.substr( idx + 1 );

		XSLTVariable.addXSLTVariable( name, value, false );
		i--;
	}
};


/**
 * @access public
 * @static
 */
XSLTVariable.theDelim = "\r";

/**
 * @access public
 * @static
 */
XSLTVariable.xsltNamespaceURI = "http://www.w3.org/1999/XSL/Transform";
