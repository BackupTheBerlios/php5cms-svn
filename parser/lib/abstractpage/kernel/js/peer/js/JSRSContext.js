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
 * JSRSContext Class (IE implementation)
 *
 * @package peer_js
 */

/**
 * Constructor
 *
 * @access public
 */
JSRSContext = function( contextID )
{
	this.Base = Base;
	this.Base();
	
	this.id        = contextID;
	this.busy      = true;
	this.callback  = null;
	this.container = this.contextCreateContainer( contextID );
};


JSRSContext.prototype = new Base();
JSRSContext.prototype.constructor = JSRSContext;
JSRSContext.superclass = Base.prototype;

/**
 * @access public
 */
JSRSContext.prototype.contextCreateContainer = function( containerName )
{
	// creates hidden container to receive server data 
	var container;
	
	document.body.insertAdjacentHTML( "afterBegin", '<span id="SPAN' + containerName + '"></span>' );
	var span = document.all( "SPAN" + containerName );
	var html = '<iframe name="' + containerName + '" src=""></iframe>';
	span.innerHTML = html;
	span.style.display = 'none';
	container = window.frames[containerName];
	
	return container;	
};

/**
 * @access public
 */
JSRSContext.prototype.callURL = function( URL )
{
	this.container.document.location.replace( URL );	
};

/**
 * @access public
 */
JSRSContext.prototype.getPayload = function()
{
	return this.container.document.forms['jsrs_Form']['jsrs_Payload'].value;
};

/**
 * @access public
 */
JSRSContext.prototype.setVisibility = function( vis )
{
	document.all( "SPAN" + this.id ).style.display = vis? '' : 'none';
};
