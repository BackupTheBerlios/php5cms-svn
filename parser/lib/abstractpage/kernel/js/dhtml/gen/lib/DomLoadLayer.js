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
DomLoadLayer = function( id, x, y, w, h, bg, url, msg, fnt, col, mbg )
{
	this.DomLayer = DomLayer;
	this.DomLayer( id, x, y, w, h, bg );

	this.msg = new DomLayer( null, 0, 0, w, h, mbg, false, 1 );
	
	if ( msg )
		this.msg.css.padding = 5 + 'px';
		
	if ( fnt )
		this.msg.css.font = fnt;
		
	if ( col )
		this.msg.css.color = col;
		
	if ( msg )
		this.msg.setHTML( msg, true );
		
	this.iframename = this.id + "LOADLAYER";
	
	if ( DomDoc.browser.ie50 )
	{
		this.setHTML( '<IFRAME NAME="' + this.iframename + '" STYLE="visibility:hidden;display:none;"></IFRAME>', true );
	}
	else
	{
		this.iframe = GenLib.elm.createElement( 'IFRAME' );
		this.iframe.name = this.iframe.id = this.iframename;
		this.iframe.style.position = 'absolute';
		this.iframe.style.top = this.iframe.style.left = '0px';
		this.iframe.style.visibility = 'hidden';
		this.elm.appendChild( this.iframe );
		this.iframe.domLayer = this;
	}
	
	this.add( this.msg );
	
	if ( url )
		setTimeout( this.toString() + ".setURL('" + url + "')", 50 );
};


DomLoadLayer.prototype = new DomLayer();
DomLoadLayer.prototype.constructor = DomLoadLayer;
DomLoadLayer.superclass = DomLayer.prototype;

/**
 * @access public
 */
DomLoadLayer.prototype.setURL = function( url )
{
	this.url = url || '';
	
	if ( this.created )
	{
		this.html = "";
		this.invokeEvent( 'beforeload' );
		this.msg.setVisible( true );

		if ( DomDoc.browser.ie )
		{
			this.iframe = frames[this.iframename];
			this.iframe.document.open()
			this.iframe.document.write( "<body onload=\"self.domLayer.finish()\">" + "\n" );
			this.iframe.document.write( "<iframe name=loader src=\""+url+"\">" );
			this.iframe.document.close();
			this.iframe.domLayer = this
			this.iframe.document.isLoading = true;
		}
		else
		{
			this.loaded = false;
			frames[this.iframename].location.replace( url );
			
			this.iframe.onload = function()
			{
				this.domLayer.loaded = true;
				this.onload = null;
				this.domLayer.finish();
			}
		}
	}
	else
	{
		var l = new DomEventListener();
		
		l.oncreate = function( e )
		{
			var s = e.source;
			s.setURL( s.url );
		}
		
		this.addEventListener( l );
	}
};

/**
 * @access public
 */
DomLoadLayer.prototype.wfrm = function()
{
	if ( !this.iframe.document.isLoading && ( this.iframe.document.readyState == 'interactive' || this.iframe.document.readyState == 'complete') )
		this.finish();
	else
		setTimeout( this.toString() + ".wfrm()",50 );
};

/**
 * @access public
 */
DomLoadLayer.prototype.finish = function()
{
	var lp   = DomDoc.browser.ie? frames[this.iframename].frames['loader'] : frames[this.iframename];
	var html = lp.document.body.innerHTML;
	
	this.setHTML( unescape( DomLoadLayer.correctLocations( this.url, html ) ) );
	DomLoadLayer.mannageLINK( this );

	externalfn = lp.externalFunctions;
	
	if ( externalfn )
	{
		for ( k in externalfn )
		{
			var t = typeof( externalfn[k] );
			
			if ( k != "onload" )
			{
				if ( t == "function" )
					eval( k + "=" + externalfn[k].toString() );
				else if ( t == "object" )
					eval( k + "=" + externalfn[k] );
				else if ( t == "string" )
					eval( k + "=\"" + externalfn[k].replace( /\"/ig ,"\\\"").replace( /\\/ig ,"\\\\") + "\"" );
			}
			else
			{
				eval( this.toString() + ".tmpfn=" + externalfn[k].toString() );
				eval( this.toString() + ".tmpfn()" );
				eval( "delete " + this.toString() + ".tmpfn" );
			}
		}
	}

	frames[this.iframename].document.location.replace( "about:blank" );
	setTimeout( this.toString() + '.msg.setVisible(false)', 20 );
	this.invokeEvent( 'load' );
};


/**
 * @access public
 * @static
 */
DomLoadLayer.mannageLINK = function( el )
{
	var links = el.elm.getElementsByTagName( 'A' );
	
	if ( links.length > 0 )
	{
		for ( var i = 0; i < links.length; i++ )
		{
			var l = links[i];
			l.onmouseover  = new Function( 'top.window.status="Link to..."; return true' );
			var LayerExist = ( l.target != null && l.target != "" && l.target.substr( 0, 1 ) != "_" )? GenLib.all[l.target] : null;
			
			if ( l.target == "" || l.target == null || l.target == "_self" || LayerExist )
			{ 
				if ( LayerExist )
					l.target = "";
					
				if ( !( l.href.toString().toLowerCase().indexOf( "javascript:" ) != -1 || l.href.toString().toLowerCase().indexOf( "mailto:" ) != -1 ) )
				{
					l.onmouseout = new Function( 'top.window.status=top.window.defaultStatus; return true' );
					l.onmouseup  = new Function( 'top.window.status=top.window.defaultStatus; return true' );
					l.onfocus    = new Function( 'top.window.status="";this.blur();return false' );
					
					elName = LayerExist? LayerExist.toString() : el.toString();
					
					if ( self.location.pathname.indexOf( l.pathname ) != -1 && l.hash != '' )
					{ 
						pos = el.url.indexOf( '#' );
						pos = ( pos != -1 )? pos : el.url.length;
						l.href = 'javascript:' + elName + '.setURL("' + el.url.substr( 0, pos ) + l.hash + '")';
					}
					else 
					{
						l.href = 'javascript:' + elName + '.setURL("' + l.href + '");';
					}
				}
			}
		}
	}
};

/**
 * @access public
 * @static
 */
DomLoadLayer.correctLocations = function( url, html )
{
	var outCode, re;
	url = url.substr( 0, url.lastIndexOf( "/" ) + 1 );
	
	outCode = html.toString()
	outCode = outCode.replace( /src\=([\"|\'])([^\/|#])/ig , 'src=$1'+url+'$2');
	outCode = outCode.replace( /href\=([\"|\'])([^\/|http\:|#|javascript\:|mailto\:])/ig , 'href=$1'+url+'$2');
	
	return outCode;
};
