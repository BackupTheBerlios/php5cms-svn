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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


/**
 * @package db_tamino
 */
 
/**
 * Constructor
 *
 * @access public
 */
TaminoTransaction = function( tid, key, http )
{
	this.Base = Base;
	this.Base();
	
	this.tid       = tid;
	this.key       = key;
	this.http      = http;
	this.errorNo   = 0;
	this.errorText = "";
};


TaminoTransaction.prototype = new Base();
TaminoTransaction.prototype.constructor = TaminoTransaction;
TaminoTransaction.superclass = Base.prototype;

/**
 * @access public
 */
TaminoTransaction.prototype.encoded = function( REQ )
{
	if	( this.http && REQ )
	{	
		REQ.setRequestHeader( TaminoClient.XINOSESSIONID,  this.tid );
		REQ.setRequestHeader( TaminoClient.XINOSESSIONKEY, this.key );
		
		return( "" );
	}
	
	return( "_sessionid=" + this.tid + "&_sessionkey=" + this.key );
};

/**
 * @access public
 */
TaminoTransaction.prototype.update = function( thisResult )
{
	var REQ     = thisResult.REQ;
	var tid     = REQ.getResponseHeader( TaminoClient.XINOSESSIONID  );
	var key     = REQ.getResponseHeader( TaminoClient.XINOSESSIONKEY );
	var version = REQ.getResponseHeader( TaminoClient.XINOVERSION    );
	
	if	( !version && ( !tid || !key ) && thisResult.DOM )
	{
		var node = thisResult.DOM;
		var root = node.getElementsByTagName( "ino:response" ).item( 0 );
		
		if	( root )
		{
			tid = root.getAttribute( "ino:sessionid"  );
			key = root.getAttribute( "ino:sessionkey" );
		}
	}
	else
	{
		// ("headers received, tid="+tid+", key="+key);
		// this.http = true;
	}
	
	if ( tid && this.tid == tid )
	{
		this.tid       = tid;
		this.key       = key;
		this.errorNo   = 0;
		this.errorText = "";
		
		return ( true ); 
	}
	else
	{
		tid = "Bad TID";
	}
	
	this.errorNo   = TaminoClient.sessionIdErrorNo;
	this.errorText = TaminoClient.errorText( TaminoClient.sessionIdErrorNo, tid, key );
	
	return ( false );
};
