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
XSLTransform = function( xmlfile, xslfile, parser, errorCallback )
{
	this.Base = Base;
	this.Base();
	
	this.xml = new XMLParser( parser );
	this.xml.setASync( false );

	this.xsl = new XMLParser( parser );
	this.xsl.setASync( false );
	
	this.onerror = ( errorCallback != null && typeof( errorCallback ) == "function" )? errorCallback : new Function;
	this.setFiles( xmlfile, xslfile );
};


XSLTransform.prototype = new Base();
XSLTransform.prototype.constructor = XSLTransform;
XSLTransform.superclass = Base.prototype;

/**
 * @access public
 */
XSLTransform.prototype.getTransformed = function()
{
	try
	{
		return this.xml.parser.documentElement.transformNode( this.xsl.parser );
	}
	catch ( e )
	{
		this.onerror( this.xml.getErrorCode(), this.xml.getErrorLine(), this.xml.getErrorReason() );
		return "";
	}
};

/**
 * @access public
 */
XSLTransform.prototype.setFiles = function( xmlfile, xslfile )
{
	this.xml.loadXML( xmlfile );
	this.xsl.loadXML( xslfile );
};
