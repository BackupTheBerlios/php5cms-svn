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
 * JSRS Class (IE implementation)
 *
 * @package peer_js
 */
 
/**
 * Constructor
 *
 * @access public
 */
JSRS = function( contextID )
{
	this.Base = Base;
	this.Base();
};


JSRS.prototype = new Base();
JSRS.prototype.constructor = JSRS;
JSRS.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
JSRS.ContextPoolSize = 0;

/**
 * @access public
 * @static
 */
JSRS.ContextMaxPool = 10;

/**
 * @access public
 * @static
 */
JSRS.ContextPool = new Array();


/**
 * @access public
 * @static
 */
JSRS.GetContextID = function()
{
	var contextObj;

	for ( var i = 1; i <= JSRS.ContextPoolSize; i++ )
	{
    	contextObj = JSRS.ContextPool[ 'jsrs' + i ];
    
		if ( !contextObj.busy )
		{
			contextObj.busy = true;      
			return contextObj.id;
		}
	}

	// if we got here, there are no existing free contexts
	if ( JSRS.ContextPoolSize <= JSRS.ContextMaxPool )
	{
		// create new context
		var contextID = "jsrs" + (++JSRS.ContextPoolSize);
		JSRS.ContextPool[ contextID ] = new JSRSContext( contextID );
		
		return contextID;
	}
	else
	{
		Base.raiseError( "Context pool full." );
		return null;
	}	
};

/**
 * Call a server routine from client code.
 *
 * @param  string   rspage      href to asp file
 * @param  string   callback    function to call on return or null if no return needed (passes returned string to callback)
 * @param  string   func        sub or function name  to call
 * @param  mixed    parms       string parameter to function or array of string parameters if more than one
 * @param  boolean  visibility  optional boolean to make container visible for debugging
 * @access public
 * @static
 */
JSRS.Execute = function( rspage, callback, func, parms, visibility )
{
	// get context
	var contextObj = JSRS.ContextPool[ JSRS.GetContextID() ];
	contextObj.callback = callback;

	var vis = ( visibility == null )? false : visibility;
	contextObj.setVisibility( vis );

	// build URL to call
	var URL = rspage;

	// always send context
	URL += "?C=" + contextObj.id;

	// func and parms are optional
	if ( func != null )
	{
		URL += "&F=" + escape( func );

		if ( parms != null )
		{
			if ( typeof( parms ) == "string" )
			{
				// single parameter
				URL += "&P0=[" + escape( parms + '' ) + "]";
			}
			else
			{
				// assume parms is array of strings
				for ( var i = 0; i < parms.length; i++ )
					URL += "&P" + i + "=[" + escape( parms[i] + '' ) + "]";
			}
		}
	}

	// unique string to defeat cache
	var d = new Date();
	URL += "&U=" + d.getTime();
 
	// make the call
	contextObj.callURL( URL );
	return contextObj.id;
};

/**
 * @access public
 * @static
 */
JSRS.Loaded = function( contextID )
{
	// get context object and invoke callback
	var contextObj = JSRS.ContextPool[ contextID ];
	
	if ( contextObj.callback != null )
		contextObj.callback( JSRS.Unescape( contextObj.getPayload() ), contextID );
		
	// clean up and return context to pool
	contextObj.callback = null;
	contextObj.busy = false;	
};

/**
 * @access public
 * @static
 */
JSRS.Error = function( contextID, str )
{
	JSRS.ContextPool[contextID].busy = false;
	return Base.raiseError( unescape( str ) );
};

/**
 * @access public
 * @static
 */
JSRS.Unescape = function( str )
{
	// payload has slashes escaped with whacks
	return str.replace( /\\\//g, "/" );	
};

/**
 * @access public
 * @static
 */
JSRS.ArrayFromString = function( s, delim )
{
	// rebuild an array returned from server as string
	// optional delimiter defaults to ~
	var d = ( delim == null )? '~' : delim;
	return s.split( d );
};

/**
 * @access public
 * @static
 */
JSRS.DebugInfo = function()
{
	var doc = window.open().document;
	
	doc.open;
	doc.write( 'Pool Size: ' + JSRS.ContextPoolSize );

  	for ( var i in JSRS.ContextPool )
	{
		var contextObj = JSRS.ContextPool[i];
		
		doc.write( '<hr>' + contextObj.id + ' : ' + ( contextObj.busy? 'busy' : 'available' ) + '<br>' );
		doc.write( 'Full Query: ' + contextObj.container.document.location.pathname + contextObj.container.document.location.search + '<br><br>' );
		doc.write( '<table border="1"><tr><td>' + contextObj.container.document.body.innerHTML + '</td></tr></table><br>' );
	}

	doc.write( '<hr>' );
	doc.close();
	
	return false;	
};
