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
 * HTTPBuffer Class (IE implementation using default behaviour).
 *
 * @package peer_http
 */

/**
 * Constructor
 *
 * @access public
 */
HTTPBuffer = function( id )
{
	this.Base = Base;
	this.Base();
	
	me = this;
	
	this.id   = id || "httpbuffer";
	this.html = new String;
	
	this.onload = new Function;
	this.onbeforeload = new Function;

	document.body.insertAdjacentHTML(
		'beforeEnd',
		'<IE:DOWNLOAD ID="'+ this.id +'" STYLE="behavior:url(#default#download)" />'
	);
};


HTTPBuffer.prototype = new Base();
HTTPBuffer.prototype.constructor = HTTPBuffer;
HTTPBuffer.superclass = Base.prototype;

/**
 * @access public
 */
HTTPBuffer.prototype.getURL = function ( url )
{
	this.onbeforeload();

	// for some crazy reason IE forgets about the object
	__http__load = this.onload;
	__http__geth = this.getHTML;
	__http__getu = this.getURL;	
	__http__unlo = this.onbeforeload;
	__http__data = [this.id];
	__http__loha = this.loadHandler;

	eval( me.id ).startDownload( url, me.loadHandler );
};

/**
 * @access public
 */
HTTPBuffer.prototype.loadHandler = function ( s )
{
	me = this;

	this.html = s;

	// restore methods (why the hell does this happen?)
	this.onload       = __http__load;
	this.getHTML      = __http__geth;
	this.getURL       = __http__getu;
	this.onbeforeload = __http__unlo;
	this.id           = __http__data[0];
	this.loadHandler  = __http__loha;

	me.onload();
};

/**
 * @access public
 */
HTTPBuffer.prototype.getHTML = function( e )
{
	return ( this.html );
};
