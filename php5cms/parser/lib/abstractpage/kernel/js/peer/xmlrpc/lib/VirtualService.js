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
 * @package peer_xmlrpc_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VirtualService = function( servername, oRPC )
{
	this.Base = Base;
	this.Base();
	
	this.URL = servername;
	this.multicall = false;
	this.autoroute = true;
	this.onerror   = null;
	
	this.rpc = oRPC;
	this.receive = {};
};


VirtualService.prototype = new Base();
VirtualService.prototype.constructor = VirtualService;
VirtualService.superclass = Base.prototype;

/**
 * @access public
 */
VirtualService.prototype.purge = function( receive )
{
	return this.rpc.purge( this, receive );
};	

/**
 * @access public
 */
VirtualService.prototype.revert = function()
{	
	this.rpc.revert( this );
};

/**
 * @access public
 */
VirtualService.prototype.add = function( name, alias, receive )
{
	this.rpc.validateMethodName();
		
	if ( this.rpc.stop )
	{
		this.rpc.stop = false;
		return false;
	}
		
	if ( receive )
		this.receive[name] = receive;
		
	this[( alias || name )] = new Function( 'var args = new Array(), i;for(i=0;i<arguments.length;i++){args.push(arguments[i]);};return this.call("' + name + '", args);' );
	return true;
};	

/**
 * @access public
 */
VirtualService.prototype.call = function( name, args )
{
	var info = this.rpc.send( this.URL, name, args, this.receive[name], this.multicall, this.autoroute );
		
	if ( info )
	{
		if ( !this.multicall )
			this.autoroute = info[0];
			
		return info[1];
	}
	else
	{
		if ( this.onerror )
			this.onerror( XMLRPC.lastError );
				
		return false;
	}
};
	

// Additions to inbuilt Objects

/**
 * @access public
 */
Object.prototype.toXMLRPC = function()
{
	var wo = this.valueOf();
	
	if ( wo.toXMLRPC == this.toXMLRPC )
	{
		retstr = "<struct>";
		
		for ( prop in this )
		{
			if ( typeof wo[prop] != "function" )
				retstr += "<member><name>" + prop + "</name><value>" + XMLRPC.getXML( wo[prop] ) + "</value></member>";
		}
		
		retstr += "</struct>";
		return retstr;
	}
	else
	{
		return wo.toXMLRPC();
	}
};

/**
 * @access public
 */
String.prototype.toXMLRPC = function()
{
	return "<string><![CDATA[" + this.replace(/\]\]/g, "] ]") + "]]></string>";
};

/**
 * @access public
 */
Number.prototype.toXMLRPC = function()
{
	if ( this == parseInt( this ) )
		return "<int>" + this + "</int>";
	else if ( this == parseFloat( this ) )
		return "<double>" + this + "</double>";
	else
		return false.toXMLRPC();
};

/**
 * @access public
 */
Boolean.prototype.toXMLRPC = function()
{
	if ( this )
		return "<boolean>1</boolean>";
	else
		return "<boolean>0</boolean>";
};

/**
 * @access public
 */
Date.prototype.toXMLRPC = function()
{
	// Could build in possibilities to express dates in weeks or other iso8601 possibillities
	// hmmmm ????
	// 19980717T14:08:55
	return "<dateTime.iso8601>" + 
		doYear( this.getUTCYear() ) + 
		doZero( this.getMonth()   ) + 
		doZero( this.getUTCDate() ) +
		"T" + doZero( this.getHours()   ) + 
		":" + doZero( this.getMinutes() ) + 
		":" + doZero( this.getSeconds() ) + 
		"</dateTime.iso8601>";
	
	function doZero( nr )
	{
		nr = String( "0" + nr );
		return nr.substr( nr.length - 2, 2 );
	}
	
	function doYear( year )
	{
		if ( year > 9999 || year < 0 ) 
			XMLRPC.handleError( new Error( "Unsupported year: " + year ) );
			
		year = String( "0000" + year )
		return year.substr( year.length - 4, 4 );
	}
};

/**
 * @access public
 */
Array.prototype.toXMLRPC = function()
{
	var retstr = "<array><data>";
	
	for ( var i = 0; i < this.length; i++ )
		retstr += "<value>" + XMLRPC.getXML( this[i] ) + "</value>";
	
	return retstr + "</data></array>";
};
