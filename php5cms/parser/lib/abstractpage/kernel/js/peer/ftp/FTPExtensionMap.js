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
 * @package peer_ftp
 */
 
/**
 * Constructor
 *
 * @access public
 */
FTPExtensionMap = function()
{
	this.Base = Base;
	this.Base();
};


FTPExtensionMap.prototype = new Base();
FTPExtensionMap.prototype.constructor = FTPExtensionMap;
FTPExtensionMap.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
FTPExtensionMap.ascii_files = new Array
(
	"asp",
	"cfm",
	"cfml",
	"cgi",
	"css",
	"htm",
	"html",
	"js",
	"php",
	"rtf",
	"txt",
	"text"
);

/**
 * @access public
 * @static
 */
FTPExtensionMap.binary_files = new Array
(
	"aif",
	"aiff",
	"aifc",
	"bin",
	"bmp",
	"dcr",
	"doc",
	"dxr",
	"exe",
	"fla",
	"gif",
	"jpg",
	"js",
	"lbi",
	"mno",
	"mov",
	"mpg",
	"mpeg",
	"pdf",
	"pic",
	"pict",
	"png",
	"qt",
	"ra",
	"ram",
	"rm",
	"sea",
	"sit",
	"snd",
	"swf",
	"tif",
	"tiff",
	"wav",
	"zip"
);

/**
 * @access public
 * @static
 */
FTPExtensionMap.getMode = function( ext )
{
	if ( ext == null || typeof( ext ) != "string" )
		return null;
	
	if ( FTPExtensionMap.ascii_files.contains( ext.toLowerCase() ) )
		return "ascii";
	else if ( FTPExtensionMap.binary_files.contains( ext.toLowerCase() ) )
		return "binary";
	else
		return "unknown";
};
