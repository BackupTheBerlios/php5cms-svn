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
 * @package dhtml_gen_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
DomDoc = function( frame )
{
	this.id     = 'GenLib.document';
	this.elm    = frame.document.body;
	this.doc    = frame.document;
	this.css    = this.elm.style;
	this.frame  = frame;
	this.parent = null;
	
	this.doc.domLayer = this.elm.domLayer = this;
	this.childrens = [];
	
	this.contentW = function()
	{
		return DomDoc.browser.ie? this.elm.clientWidth : this.frame.innerWidth;
	}
	this.contentH = function()
	{
		return DomDoc.browser.ie? this.elm.clientHeight : this.frame.innerHeight;
	}
};


DomDoc.prototype = new DomLayer();
DomDoc.prototype.constructor = DomDoc;
DomDoc.superclass = DomLayer.prototype;


/**
 * @access public
 * @static
 */
DomDoc.browser = new Object();
DomDoc.browser.ie   = document.all? 1 : 0;
DomDoc.browser.dom  = document.getElementById? 1 : 0;
DomDoc.browser.ie6  = ( navigator.userAgent.indexOf( "MSIE 6.0" ) != -1 );
DomDoc.browser.ie50 = ( navigator.userAgent.indexOf( "MSIE 5.0" ) != -1 );
