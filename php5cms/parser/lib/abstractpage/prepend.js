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
 * Base Class
 */
 
/**
 * Constructor
 *
 * @access public
 */
Base = function()
{
	if ( this.constructor != Base )
		this.setID( "Base" + ( Base.cnt++ ) );
};

/**
 * @access public
 */
Base.prototype.toString = function()
{
	return "Base.all." + this._id;	
};

/**
 * @access public
 */
Base.prototype.getClass = function()
{
	return this.constructor;
};

/**
 * @access public
 */
Base.prototype.setID = function( id )
{
	this._id = id;
	Base.all[this._id] = this;
};


/**
 * @access public
 * @static
 */
Base.cnt = 0;

/**
 * @access public
 * @static
 */
Base.all = [];

/**
 * @access public
 * @static
 */
Base.isError = function( obj )
{
	return ( ( ( typeof obj == "object" ) && ( obj.constructor == ErrorObject ) )? true : false );
};

/**
 * @access public
 * @static
 */
Base.raiseError = function( msg, type, file, number, line, description, mode, callback )
{
	/* native error passed to ErrorObject */
	if ( typeof obj == "object" && obj.number )
	{
		return new ErrorObject( obj.message, obj.name, null, obj.number, null, obj.description );
	}
	// custom error
	else
	{
		return new ErrorObject( msg, type, file, number, line, description, mode, callback );
	}
};



/**
 * Error Class (inspired by PEAR)
 */

/* Constants */
var ERROR_RETURN   = 1;
var ERROR_THROW    = 2;
var ERROR_QUEUE    = 4;
var ERROR_CALLBACK = 8;
var ERROR_ALERT    = 16;
var ERROR_STATUS   = 32;
var ERROR_PRINT    = 64;

/* prevents from bubbling errors to browser */
var ERROR_BUBBLING = false;


/**
 * Constructor
 *
 * @access public
 */
ErrorObject = function( msg, type, file, number, line, description, mode, callback )
{
	if ( msg )
		ErrorObject.raise( msg, type, file, number, line, description, mode, callback );
};

/**
 * @access public
 * @static
 */
ErrorObject.message = "";

/**
 * @access public
 * @static
 */
ErrorObject.description = "";

/**
 * @access public
 * @static
 */
ErrorObject.filename = "";

/**
 * @access public
 * @static
 */
ErrorObject.type = "";

/**
 * @access public
 * @static
 */
ErrorObject.number = 0;

/**
 * @access public
 * @static
 */
ErrorObject.line = 0;

/**
 * @access public
 * @static
 */
ErrorObject.lastError = null;

/**
 * @access public
 * @static
 */
ErrorObject.callback = new Function;

/**
 * @access public
 * @static
 */
ErrorObject.queue = new Array();

/**
 * @access public
 * @static
 */
ErrorObject.silent = false;

/**
 * @access public
 * @static
 */
ErrorObject.clrf = "\n";

/**
 * @access public
 * @static
 */
ErrorObject.mode = "";

/**
 * @access public
 * @static
 */
ErrorObject.mode_default = ERROR_ALERT;


/**
 * @access public
 * @static
 */
ErrorObject.raise = function( msg, type, file, number, line, desc, mode, callback )
{
	ErrorObject.message     = "Error: " + msg || "unknown message";
	ErrorObject.type        = type   || "GeneralError";
	ErrorObject.filename    = file   || "unknown file";
	ErrorObject.number      = number || 0;
    ErrorObject.line        = line   || 0;
	ErrorObject.description = desc   || "no description given";
	ErrorObject.mode        = mode   || ( ( ErrorObject.mode != "" )? ErrorObject.mode : ErrorObject.mode_default );
	ErrorObject.callback    = ( callback && typeof callback == "function" )? callback : ( typeof ErrorObject.callback == "function" )? ErrorObject.callback : new Function;

	if ( ErrorObject.silent == true )
	{
		if ( Browser.apopt )
		{
			/* in case of ie, this means 'silent' */
			window.event.returnValue  = false;
			window.event.cancelBubble = true;
		
			return this;
		}
	}
	
	var e_obj = new Error();
	e_obj.name        = ErrorObject.type;
	e_obj.message     = ErrorObject.message;
	e_obj.number      = ErrorObject.number;
	e_obj.description = ErrorObject.description;
	
	var e_obj_advanced = new Object();
	e_obj_advanced.name        = ErrorObject.type;
	e_obj_advanced.message     = ErrorObject.message;
	e_obj_advanced.number      = ErrorObject.number;
	e_obj_advanced.description = ErrorObject.description;
	e_obj_advanced.filename    = ErrorObject.filename;
	e_obj_advanced.line        = ErrorObject.line;
		
	/* store last error */
	ErrorObject.lastError = e_obj;
	
	if ( ErrorObject.mode & ERROR_THROW )
		throw e_obj;
	
	if ( ErrorObject.mode & ERROR_QUEUE )
		ErrorObject.queue[ErrorObject.queue.length] = e_obj_advanced;
	
	if ( ErrorObject.mode & ERROR_CALLBACK )
		ErrorObject.callback( e_obj_advanced );
	
	if ( ErrorObject.mode & ERROR_ALERT )
		alert( ErrorObject.asString() );
	
	if ( ErrorObject.mode & ERROR_STATUS )
		window.status = ErrorObject.asString( " - " );
	
	if ( ErrorObject.mode & ERROR_PRINT )
	{
		document.open();
		document.write( ErrorObject.asString( "<br>\n" ) );
		document.close();
		
		// document.body.insertAdjacentHTML( 'beforeEnd', ErrorObject.asString( "<br>\n" ) );
	}
	
	// reset mode
	ErrorObject.mode = "";
	
	return this;
};

/**
 * @access public
 * @static
 */
ErrorObject.getQueue = function()
{
	var queue = new Array();
	
	// make a hard copy
	for ( var i = 0; i < ErrorObject.queue.length; i++ )
		queue[queue.length] = ErrorObject.queue[i];
		
	ErrorObject.queue.length = 0;
	return queue;
};

/**
 * @access public
 * @static
 */
ErrorObject.dumpQueue = function( clrf )
{
	var str = "";
	
	for ( var i in ErrorObject.queue )
		str += ErrorObject.queue[i] + ( clrf || ErrorObject.clrf );
		
	return str;
};

/**
 * @access public
 * @static
 */
ErrorObject.asString = function( sep )
{
	if ( sep == null )
		sep = ErrorObject.clrf;
		
	var str =
		"type: "        + ErrorObject.type        + sep +
		"message: "     + ErrorObject.message     + sep +
		"number: "      + ErrorObject.number      + sep +
		"description: " + ErrorObject.description + sep +
		"filename: "    + ErrorObject.filename    + sep +
		"line: "        + ErrorObject.line;
		
	return str;
};

/**
 * @access public
 * @static
 */
ErrorObject.getMode = function()
{
	return ErrorObject.mode;
};

/**
 * @access public
 * @static
 */
ErrorObject.getMessage = function()
{
	return ErrorObject.message;
};

/**
 * @access public
 * @static
 */
ErrorObject.getFilename = function()
{
	return ErrorObject.filename;
};

/**
 * @access public
 * @static
 */
ErrorObject.getLine = function()
{
	return ErrorObject.linenumber;
};

/**
 * @access public
 * @static
 */
ErrorObject.getDescription = function()
{
	return ErrorObject.description;
};

/**
 * @access public
 * @static
 */
ErrorObject.getNumber = function()
{
	return ErrorObject.number;
};

/**
 * @access public
 * @static
 */
ErrorObject.getLastError = function()
{
	return ErrorObject.lastError;
};



/**
 * ObjectLoader Class
 */
 
/**
 * Constructor
 *
 * @access public
 */
ObjectLoader = function()
{
	this.Base = Base;
	this.Base();
};


ObjectLoader.prototype = new Base();
ObjectLoader.prototype.constructor = ObjectLoader;
ObjectLoader.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
ObjectLoader.basePath = 'kernel/js/';

/**
 * @access public
 * @static
 */
ObjectLoader.objects = new Array();

/**
 * @access public
 * @static
 */
ObjectLoader.load = function( pckg )
{
	if ( ObjectLoader.isRegistered( pckg ) )
		return;
	
	ObjectLoader.objects[ObjectLoader.objects.length] = pckg;
	
	var toTheLeft, toTheRight, path;
	var fromString = ".";
	var toString   = "/";

	if ( toString.indexOf( fromString ) == -1 )
	{
		while ( pckg.indexOf( fromString ) != -1 )
		{
			toTheLeft  = pckg.substring( 0, pckg.indexOf( fromString ) );
			toTheRight = pckg.substring( pckg.indexOf( fromString ) + fromString.length, pckg.length );
			pckg       = toTheLeft + toString + toTheRight;
      	}
	}
	
	path = ObjectLoader.basePath + pckg + ".js";
	
	document.open();
	document.write( '<script type="text/javascript" src="' + path + '"></' + 'script>' );
	document.close();
};

/**
 * @access public
 * @static
 */
ObjectLoader.isRegistered = function( path )
{
	for ( var i in ObjectLoader.objects )
	{
		if ( ObjectLoader.objects[i] == path )
			return true;
	}
	
	return false;
};


// private methods

/**
 * @access private
 * @static
 */
ObjectLoader._convertToPath = function( pckg )
{
	var toTheLeft, toTheRight;
	
	var fromString = ".";
	var toString   = "/";

	if ( toString.indexOf( fromString ) == -1 )
	{
		while ( pckg.indexOf( fromString ) != -1 )
		{
			toTheLeft  = pckg.substring( 0, pckg.indexOf( fromString ) );
			toTheRight = pckg.substring( pckg.indexOf( fromString ) + fromString.length, pckg.length );
			pckg       = toTheLeft + toString + toTheRight;
      	}
	}
	
	return ObjectLoader.basePath + pckg + ".js";
};

/**
 * @access private
 * @static
 */
ObjectLoader._extractObjectName = function( pckg )
{
	var ele = pckg.split( "." );
	var obj = ele[ele.length - 1];
	
	return obj;
};


/**
 * @access public
 */
function using( path )
{
	if ( typeof path == "string" )
		path = path.split( "," );
	
	for ( var i = 0; i < path.length; i++ )
		ObjectLoader.load( path[i].replace( /^[ \n\r\t]+|[ \n\r\t]+$/g, "" ) );
};

/**
 * @access public
 */
function set_library_path( path )
{
	ObjectLoader.basePath = path;
};

/**
 * @access public
 */
function object_registered( path )
{
	return ObjectLoader.isRegistered( path );
};

/**
 * @access public
 */
function check_imports()
{
	var exists, obj;
	var success = new Array();
	var failed  = new Array();
	
	for ( var i = 0; i < ObjectLoader.objects; i++ )
	{
		obj = ObjectLoader._extractObjectName( ObjectLoader.objects[i] )
		
		try
		{
			exists = eval( obj );
		}
		catch ( e )
		{
			exists = false;
		}
		
		// object exists
		if ( exists )
			success[success.length] = ObjectLoader.objects[i];
		else
			failed[failed.length]   = ObjectLoader.objects[i];
	}
	
	// we return a two-dimensional array
	return new Array( success, failed );
};



/**
 * Browser Object
 */
 
/**
 * Constructor
 *
 * @access public
 */
Browser = new function()
{
	this.appName   = navigator.appName;
	this.userAgent = navigator.userAgent;
	this.version   = navigator.appVersion;
	this.platform  = navigator.platform;
	
	if ( this.appName == "Netscape" )
		this.b = "ns";
	else if ( this.appName == "Microsoft Internet Explorer" || /MSIE/.test( this.userAgent ) )
		this.b = "ie";
	
	var ua = this.userAgent.toLowerCase();
	
	if ( ua.indexOf( "win" ) > -1 )
		this.os = "win32";
	else if ( ua.indexOf( "mac" ) > -1 )
		this.os = "mac";
	else
		this.os = "other";
		
	this.v = parseInt( this.version );
	
	this.ns    = ( this.b == "ns" && this.v >= 4 );
	this.ns4   = ( this.b == "ns" && this.v == 4 );
	this.ns6   = ( this.b == "ns" && this.v == 5 );
	this.ie    = ( this.b == "ie" && this.v >= 4 );
	this.ie4   = ( this.version.indexOf( 'MSIE 4' )   > 0 );
	this.ie4up = ( document.all != null );
	this.ie5   = ( this.version.indexOf( 'MSIE 5' )   > 0 );
	this.ie55  = ( this.version.indexOf( 'MSIE 5.5' ) > 0 );
	this.ie6   = ( this.version.indexOf( 'MSIE 6' )   > 0 );
	this.ie6up = this.ie && /MSIE [6789]/.test( this.userAgent );
	this.opera = /opera [56789]|opera\/[56789]/i.test( this.userAgent );
	this.dom   = ( document.createElement && document.appendChild && document.getElementsByTagName )? true : false;
	this.def   = ( this.ie || this.dom );
	this.moz   = !this.opera && /gecko/i.test( this.userAgent );
	this.apopt = /MSIE ((5\.[56789])|([6789]))/.test( this.userAgent ) && this.platform == "Win32";
};



/**
 * Util Class (static helpers)
 */
 
/**
 * Constructor
 *
 * @access public
 */
Util = function()
{
};

/**
 * @access public
 * @static
 */
Util.preload = function( sourceArray, pre, pro )
{
	imageArray = new Array();
   
	if ( pre == null )
		pre = "";
   
	if ( pro == null )
		pro = "";
   
	for ( var i in sourceArray )
	{
		imageArray[i] = new Image();
		imageArray[i].src = pre + sourceArray[i] + pro;
	}
};

/**
 * @access public
 * @static
 */
Util.getReal = function( el, type, value )
{
	temp = el;
	
	while ( ( temp != null ) && ( temp.tagName != "BODY" ) )
	{
		if ( eval( "temp." + type ) == value )
		{
			el = temp;
			return el;
		}
		
		temp = temp.parentElement;
	}
	
	return el;
};

/**
 * @access public
 * @static
 */
Util.swapClass = function( obj, cls )
{
	obj.className = cls;
};

/**
 * @access public
 * @static
 */
Util.swapNode = function( id1, id2 )
{
	document.getElementById( id1 ).swapNode( document.getElementById( id2 ) );
};

/**
 * @access public
 * @static
 */
Util.removeFromArray = function( array, index, id )
{
	var which = ( typeof( index ) == "object" )? index : array[index];
	
	if ( id )
	{
		delete array[which.id];
	}
	else
	{
		for ( var i = 0; i < array.length; i++ )
		{
			if ( array[i] == which )
			{
				if ( array.splice )
				{
					array.splice( i, 1 );
				}
				else
				{
					for ( var x = i; x < array.length - 1; x++ )
						array[x] = array[x+1];
						
					array.length -= 1;
				}
			
				break;
			}
		}
	}	
	
	return array;
};

/**
 * @access public
 * @static
 */
Util.is_bool = function( val )
{
	if ( typeof val == "boolean" || 
		 val == true    ||
		 val == "true"  ||
		 val == "yes"   ||
		 val == "on"    ||
		 val == "t"     ||
		 val == 1       || 
		 val == false   ||
		 val == "false" ||
		 val == "no"    ||
		 val == "off"   ||
		 val == "f"     ||
		 val == 0 ) return true;
	
	return false; 
};

/**
 * @access public
 * @static
 */
Util.is_int = function( val )
{
	if ( Math.round( val ) == val )
		return true;
	else
		return false;
};

/**
 * @access public
 * @static
 */
Util.is_float = function( val )
{
	var dp = false;
	
	for ( var i = 0; i < val.length; i++ )
	{
		if ( !Util.is_digit( val.charAt( i ) ) )
		{ 
			if ( val.charAt(i) == '.' )
			{
				if ( dp == true )
					return false; // already saw a decimal point
				else
					dp = true;
			}
			else
			{
				return false; 
			}
		}
	}
	
	return true;
};

/**
 * @access public
 * @static
 */
Util.is_range = function( val )
{
	if ( val >= -100 && val <= 100 )
		return true;
	else
		return false;
};

/**
 * @access public
 * @static
 */
Util.is_percent = function( val )
{
	if ( val.indexOf( "%" ) == -1 )
		return false;
		
	val = val.substring( 0, val.indexOf( "%" ) );

	if ( val >= 0 && val <= 100 )
		return true;
	else
		return false;
};

/**
 * @access public
 * @static
 */
Util.is_a = function( obj, classname )
{
	var exists;

	try
	{
		exists = eval( classname );
		
		if ( !exists )
			return false;
		
		if ( typeof obj == "object" && obj.constructor && obj.constructor == exists )
			return true;
	}
	catch ( e )
	{
		return false;
	}
};

/**
 * @access public
 * @static
 */
Util.is_subclass_of = function( obj, classname )
{
	var superclass;
	
	try
	{
		do
		{
			superclass = obj = obj.constructor.superclass;

			if ( superclass.constructor == eval( classname ) )
				return true;
		} while ( obj )
		
		return false;
	}
	catch ( e )
	{
		return false;
	}
};


/**
 * Env Class
 */
 
/**
 * Constructor
 *
 * @access public
 */
Env = new function()
{
	/* properties */
	
	/**
	 * @type   Boolean
	 * @access public
	 */
	this.loaded = false;
	
	/**
	 * @type   Boolean
	 * @access public
	 */
	this.created = false;

	/**
	 * @access public
	 */
	this.hookOnLoad = window.onload;
	
	/**
	 * @access public
	 */
	this.hookOnUnLoad = window.onunload;

	/**
	 * @type   Array
	 * @access public
	 */
	this.loadedObjects = [];

	/**
	 * @type   Array
	 * @access public
	 */
	this.onLoadCodes = [];
	
	/**
	 * @type   Array
	 * @access public
	 */
	this.onUnLoadCodes = [];
	
	/**
	 * @type   Array
	 * @access public
	 */
	this.onResizeCodes = [];


	/* methods */

	/**
	 * @access public
	 */
	this.addLoadFunction = function( f )
	{
		this.onLoadCodes[this.onLoadCodes.length] = f;
	};
	
	/**
	 * @access public
	 */
	this.addUnLoadFunction = function( f )
	{
		this.onUnLoadCodes[this.onUnLoadCodes.length] = f;
	};
	
	/**
	 * @access public
	 */
	this.addResizeFunction = function( f )
	{
		this.onResizeCodes[this.onResizeCodes.length] = f;
	};
	
	/**
	 * @access public
	 */
	this.loadHandler = function()
	{
		this.created = true;
		eval( this.onLoadCodes.join( ";" ) );
		
		if ( this.onLoad )
			this.onLoad();
	
		this.loaded = true;
		eval( this.hookOnLoad );
	};
	
	/**
	 * @access public
	 */
	this.unloadHandler = function()
	{
		/* call destructors */
		for ( var i in Base.all )
		{
			obj = eval( Base.all[i] )

			if ( obj.finalize && typeof obj.finalize == "function" )
				obj.finalize();
		}
	
		eval( this.onUnLoadCodes.join( ";" ) );
		
		if ( this.onUnload )
			this.onUnload();
		
		eval( this.hookOnUnLoad );
	};
	
	/**
	 * @access public
	 */
	this.resizeHandler = function()
	{
		eval( this.onResizeCodes.join( ";" ) );
		
		if ( this.onResize )
			this.onResize();
	};
};

/**
 * Flag for a browser.
 * The member is true, if the script runs within a browser environment.
 *
 * @type   Boolean
 * @access public
 */
Env.isBrowser = this.window? true : false;

/**
 * Flag for Microsoft JScript.
 * The member is true, if the script runs in the Microsoft JScript engine.
 *
 * @type   Boolean
 * @access public
 */
Env.isJScript = this.ScriptEngine? true : false;

/**
 * Flag for Microsoft Windows Scripting Host.
 * The member is true, if the script runs in the Microsoft Windows Scripting Host.
 *
 * @type   Boolean
 * @access public
 */
Env.isWSH = this.WScript? true : false;

/**
 * Flag for Netscape Enterprise Server (iPlanet) engine.
 * The member is true, if the script runs in the iPlanet as SSJS.
 *
 * @type   Boolean
 * @access public
 */
Env.isNSServer = this.Packages && !this.importPackage;

/**
 * Flag for Rhino.
 * The member is true, if the script runs in Rhino of Mozilla.org.
 *
 * @type   Boolean
 * @access public
 */
Env.isRhino = this.importPackage? true : false;

/**
 * Flag for a Mozilla JavaScript shell.
 * The member is true, if the script runs in a command line shell of a
 * Mozilla.org script engine (either SpiderMonkey or Rhino).
 *
 * @type   Boolean
 * @access public
 */
Env.isMozillaShell = this.load? true : false;

/**
 * Flag for a command line shell.
 * The member is true, if the script runs in a command line shell.
 *
 * @type   Boolean
 * @access public
 */
Env.isShell = Env.isMozillaShell || Env.isWSH;

/**
 * @param  String  str  The script to load.
 * @access public
 */
Env.load = function( script )
{
	var ret = "true";
	
	if ( Env.isMozillaShell )
	{
		load( script );
	}
	else if ( Env.isWSH )
	{
		var fso  = new ActiveXObject( "Scripting.FileSystemObject" );
		var file = fso.OpenTextFile( script, 1 );
		ret = file.ReadAll();
		file.Close();
	}
	
	return ret;
};

/**
 * Prints a complete text line incl. line feed. Works for command line
 * shells WSH, Rhino and SpiderMonkey.
 *
 * @param  String str The line to print.
 * @access public
 */
Env.print = function( str )
{
	if ( Env.isMozillaShell )
		print( str );
	else if ( Env.isBrowser )
		document.writeln( str );
	else if ( Env.isWSH )
		WScript.Echo( str );
};

/**
 * Quits the JavaScript engine.
 * Stops current JavaScript engine and returns an exit code. Works for 
 * command line shells WSH, Rhino and SpiderMonkey.
 *
 * @param  Number  exit  The exit code.
 * @access public
 */
Env.quit = function( exit )
{
	if ( Env.isMozillaShell )
		quit( exit );
	else if ( Env.isWSH )
		WScript.Quit( exit );
};



window.onload = function()
{
	Env.loadHandler();
};
window.onunload = function()
{
	Env.unloadHandler();
};
window.onresize = function()
{
	Env.resizeHandler();
};
window.onerror = function( msg, url, line )
{
	/*
	if ( ERROR_BUBBLING == false )
	{
		// Base.raiseError( msg, null, url, null, line );

		window.event.returnValue  = false;
		window.event.cancelBubble = true;
		
		return true;
	}
	else
	{
		return Base.raiseError( msg, null, url, null, line );
	}
	*/
};
