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
 * @package peer_http
 */
 
/**
 * Constructor
 *
 * @access public
 */
XMLHTTP = function()
{
	this.Base = Base;
	this.Base();
};


XMLHTTP.prototype = new Base();
XMLHTTP.prototype.constructor = XMLHTTP;
XMLHTTP.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
XMLHTTP.create = function()
{
	try
	{
		if ( window.XMLHttpRequest )
		{
			var req = new XMLHttpRequest();
			
			// some versions of Moz do not support the readyState property
			// and the onreadystate event so we patch it!
			if ( req.readyState == null )
			{
				req.readyState = 1;
				req.addEventListener( "load", function()
				{
					req.readyState = 4;
					
					if ( typeof req.onreadystatechange == "function" )
						req.onreadystatechange();
				}, false );
			}
			
			return req;
		}
		
		if ( window.ActiveXObject )
			return new ActiveXObject( XMLParser.getControlPrefix() + ".XmlHttp" );
	}
	catch ( ex )
	{
		return Base.raiseError( "Your browser does not support XmlHttp objects." );
	}
};
