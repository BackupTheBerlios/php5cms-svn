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
TaminoResult = function( queryo, req, noBody )
{
	this.Base = Base;
	this.Base();
	
	this.queryObj   = queryo;
	this.REQ        = req;
	this.DOM        = null;
	this.lastQuery  = queryo.lastQuery;
	this.pageSize   = 5;
	this.user       = "";
	this.password   = "";
	this.errorNo    = 0;
	this.errorText  = ""; 
	this.securePath = queryo.securePath;
		
	if ( this.REQ )
	{
		var status = this.REQ.status;
		
		if ( status >= "300" )
		{	
			this.errorNo   = TaminoClient.transportErrorNo;
			this.errorText = TaminoClient.errorText( TaminoClient.transportErrorNo, status, this.REQ.statusText );
		
			if ( true )
			{
				var tran = this.queryObj.transaction;
				
				if ( tran )
				{
					if ( !tran.update( this ) )
					{
						this.errorNo   = tran.errorNo;
						this.errorText = tran.errorText;
					}
				}
			}
		}
		else 
		{ 	
			if ( noBody )
			{
				var tran = this.queryObj.transaction;
				
				if ( tran )
				{
					if ( !tran.update( this ) )
					{
						this.errorNo   = tran.errorNo;
						this.errorText = tran.errorText;
					}
				}
				
				return;
			}
			
			this.DOM = this.REQ.responseXML;
			var p = this.DOM.parseError;
			
			if	( p.errorCode != "0" )
			{
				this.errorNo   = TaminoClient.parserErrorNo;
				this.errorText = TaminoClient.errorText( TaminoClient.parserErrorNo, p.errorCode, p.reason );
				
				return;
			}
			
			if ( !this.DOM.documentElement )
			{
				this.errorNo = TaminoClient.noXMLErrorNo;
				var type;
				type = this.REQ.getResponseHeader( "Content-Type" );
				
				if ( !type )
					type = "";
				
				this.errorText = TaminoClient.errorText( TaminoClient.noXMLErrorNo, type );
				return;
			}
			else
			{
				var message = this.ErrorNode();
				
				if ( message )
				{
					this.errorNo = Number( message.getAttribute( "ino:returnvalue" ) );
					var node = message.selectSingleNode( "ino:messagetext" );
					var text = this.errorNo;
					
					if ( node ) 
						text = node.getAttribute( "ino:code" ) + " " + node.text;
						
					var lines = message.selectNodes( "ino:messageline" );
					
					if ( lines )
					{
						var l = lines.length;
						
						for ( var j = 0; j < l; j++ )
						{
							var line = lines.item( j );
							text = text + ', ' + line.text;
						}
					}
					
					this.errorText = text;
				}
						
				var tran = this.queryObj.transaction;
				
				if ( tran )
				{
					if ( !tran.update( this ) )
					{
						this.errorNo   = tran.errorNo;
						this.errorText = tran.errorText;
					}
				}
			}
		}
	}
};


TaminoResult.prototype = new Base();
TaminoResult.prototype.constructor = TaminoResult;
TaminoResult.superclass = Base.prototype;

/**
 * @access public
 */
TaminoResult.prototype.cursor = function( attr )
{
	var query = this.getCursorQuery( attr );
	
	if ( !query )
		return ( null );
		
	var m = query.match( /(\?.*=)(.*)/ );
	query = m[1] + this.queryObj.escape( m[2] );
	
	if ( query )
		return ( this.queryObj.queryCursor( query ) );
		
	return ( this ); 
};

/**
 * @access public
 */
TaminoResult.prototype.getTaminoVersion = function()
{
	if ( this.REQ )
		return ( this.REQ.getResponseHeader( TaminoClient.XINOVERSION ) );
		
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getServer = function()
{
	if ( this.REQ )
		return ( this.REQ.getResponseHeader( "server" ) );
		
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getLastModified = function()
{
	if ( this.REQ )
	{
		var lastModified = this.REQ.getResponseHeader( "Last-Modified" );
		
		if ( lastModified ) 
			return ( new Date( lastModified ) );
				
		return ( null );
	}
	
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getContentType = function()
{
	if ( this.REQ )
		return ( this.REQ.getResponseHeader( "Content-Type" ) );
		
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getTaminoAPIVersion = function()
{
	if ( !this.REQ )	
		return ( null );
		
	var messageNode = this.DOM.selectSingleNode( '//ino:messageline[@ino:subject="apiVersion"]' );
	
	if ( !messageNode ) 
		return ( "1.0" );
	
	return ( messageNode.firstChild.nodeValue );
};

/**
 * @access public
 */
TaminoResult.prototype.rObject = function()
{
	if ( !this.DOM && this.REQ ) 
		return ( this.REQ.getResponseHeader( TaminoClient.XINOID ) );
		
	var rObjNodelist = this.DOM.getElementsByTagName( "ino:object" );
	
	if ( rObjNodelist && rObjNodelist.length )
		return ( rObjNodelist.item( 0 ) );
		
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getInoId = function()
{
	var rObj = this.rObject();
	
	if ( rObj ) 
		return ( rObj.getAttribute( "ino:id" ) );
		
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getDocType = function()
{
	var rObj = this.rObject();
	
	if ( rObj )	
		return ( rObj.getAttribute( "ino:doctype" ) );
		
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getCollection = function()
{
	var rObj = this.rObject();
	
	if ( rObj )	
		return ( rObj.getAttribute( "ino:collection" ) );
		
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getLocation = function()
{
	var location = this.REQ.getResponseHeader( "Location" );
	
	if ( location ) 
		return ( location );
		
	var collection = this.getCollection();
	var docType    = this.getDocType();
	var inoId      = this.getInoId();
	
	if ( collection && docType && inoId )		
		return ( this.queryObj.XMLDB + "/" + collection + "/" + docType + "/@" + inoId );

	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.ErrorNode = function()
{
	var messages = this.DOM.getElementsByTagName( "ino:message" );
	var messagesLength = messages.length;

	var message;
	var i;
	var returnCode;

	for ( i = 0; i < messagesLength; i++ )
	{
		message    = messages.item( i );
		returnCode = message.getAttribute( "ino:returnvalue" );
		
		if ( returnCode != "0" ) 
			return ( message );
	}

	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.nodes = function()
{
	var result = this.DOM.getElementsByTagName( "xql:result" ).item( 0 );
	
	if ( result ) 
		return ( result.childNodes );
	
	return ( null );
};

/**
 * @access public
 */
TaminoResult.prototype.getResult = function()
{
	var result = this.DOM.getElementsByTagName( "xql:result" ).item( 0 );
	return ( result );
};

/**
 * @access public
 */
TaminoResult.prototype.getFirst = function()
{
	return ( this.cursor( "first" ) );
};

/**
 * @access public
 */
TaminoResult.prototype.getPrev = function()
{
	return ( this.cursor( "prev" ) );
};

/**
 * @access public
 */
TaminoResult.prototype.getNext = function()
{	
	return ( this.cursor( "next" ) );
};

/**
 * @access public
 */
TaminoResult.prototype.getLast = function()
{
	return ( this.cursor( "last" ) );
};

/**
 * @access public
 */
TaminoResult.prototype.refresh = function()
{
	if ( !this.lastCursor ) 
		return ( this );
		
	return ( this.queryObj.queryCursor( this.lastCursor ) );
};

/**
 * @access public
 */
TaminoResult.prototype.getCursorQuery = function( attr )
{
	var link = this.DOM.getElementsByTagName( "ino:" + attr ).item( 0 );
	
	if ( link ) 
		return ( link.getAttribute( "ino:href" ) );
		
	return ( "" );
};

/**
 * @access public
 */
TaminoResult.prototype.getTotalCount = function()
{
	var doc;
	var cursorList;
	var nodes;
	
	if ( doc = this.DOM )
	{
		if ( cursorList = doc.getElementsByTagName( "ino:cursor" ) )
			return ( cursorList.item( 0 ).getAttribute( "ino:count" ) );
	}
	
	return ( null );
};
