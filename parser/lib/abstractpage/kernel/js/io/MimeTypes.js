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
 * @package io
 */
 
/**
 * Constructor
 *
 * @access public
 */
MimeTypes = function()
{
	this.Dictionary = Dictionary;
	this.Dictionary();
	
	this.defaultType = "application/octet-stream";
	this._populate();
};


MimeTypes.prototype = new Dictionary();
MimeTypes.prototype.constructor = MimeTypes;
MimeTypes.superclass = Base.prototype;

/**
 * @access public
 */
MimeTypes.prototype.getMimeType = function( fileName )
{
	if ( fileName == null )
		return this.defaultType;
		
	var ext = fileName.substring( fileName.lastIndexOf( "." ) + 1, fileName.length );

	if ( ext == -1 )
	{
		return this.defaultType;
	}
	else
	{
		if ( this.contains( ext ) )
			return this.get( ext );
		else
			return this.defaultType;
	}
};

/**
 * @access public
 */
MimeTypes.prototype.guessType = function( ext )
{
	// TODO
};

/**
 * @access public
 */
MimeTypes.prototype.guessExtension = function( mime )
{
	// TODO
};


// private methods

/**
 * @access private
 */
MimeTypes.prototype._populate = function()
{
	this.add( "ez",			"application/andrew-inset"		);
	this.add( "hqx",		"application/mac-binhex40"		);
	this.add( "cpt",		"application/mac-compactpro"	);
	this.add( "doc",		"application/msword"			);
	this.add( "bin",		"application/octet-stream"		);
	this.add( "dms",		"application/octet-stream"		);
	this.add( "lha",		"application/octet-stream"		);
	this.add( "lzh",		"application/octet-stream"		);
	this.add( "exe",		"application/octet-stream"		);
	this.add( "class",		"application/octet-stream"		);
	this.add( "oda",		"application/oda"				);
	this.add( "pdf",		"application/pdf"				);
	this.add( "ai",			"application/postscript"		);
	this.add( "eps",		"application/postscript"		);
	this.add( "ps",			"application/postscript"		);
	this.add( "rtf",		"application/rtf"				);
	this.add( "smi",		"application/smil"				);
	this.add( "smil",		"application/smil"				);
	this.add( "mif",		"application/vnd.mif"			);
	this.add( "ppt",		"application/vnd.ms-powerpoint"	);
	this.add( "bcpio",		"application/x-bcpio"			);
	this.add( "vcd",		"application/x-cdlink"			);
	this.add( "pgn",		"application/x-chess-pgn"		);
	this.add( "cpio",		"application/x-cpio"			);
	this.add( "csh",		"application/x-csh"				);
	this.add( "dcr",		"application/x-director"		);
	this.add( "dir",		"application/x-director"		);
	this.add( "dxr",		"application/x-director"		);
	this.add( "dvi",		"application/x-dvi"				);
	this.add( "spl",		"application/x-futuresplash"	);
	this.add( "gtar",		"application/x-gtar"			);
	this.add( "hdf",		"application/x-hdf"				);
	this.add( "js",			"application/x-javascript"		);
	this.add( "skp",		"application/x-koan"			);
	this.add( "skd",		"application/x-koan"			);
	this.add( "skt",		"application/x-koan"			);
	this.add( "skm",		"application/x-koan"			);
	this.add( "latex",		"application/x-latex"			);
	this.add( "nc",			"application/x-netcdf"			);
	this.add( "cdf",		"application/x-netcdf"			);
	this.add( "rpm",		"application/x-rpm"				);
	this.add( "sh",			"application/x-sh"				);
	this.add( "shar",		"application/x-shar"			);
	this.add( "swf",		"application/x-shockwave-flash"	);
	this.add( "sit",		"application/x-stuffit"			);
	this.add( "sv4cpio",	"application/x-sv4cpio"			);
	this.add( "sv4crc",		"application/x-sv4crc"			);
	this.add( "tar",		"application/x-tar"				);
	this.add( "tcl",		"application/x-tcl"				);
	this.add( "tex",		"application/x-tex"				);
	this.add( "texinfo",	"application/x-texinfo"			);
	this.add( "texi",		"application/x-texinfo"			);
	this.add( "t",			"application/x-troff"			);
	this.add( "tr",			"application/x-troff"			);
	this.add( "roff",		"application/x-troff"			);
	this.add( "man",		"application/x-troff-man"		);
	this.add( "me",			"application/x-troff-me"		);
	this.add( "ms",			"application/x-troff-ms"		);
	this.add( "ustar",		"application/x-ustar"			);
	this.add( "src",		"application/x-wais-source"		);
	this.add( "zip",		"application/zip"				);
	this.add( "au",			"audio/basic"					);
	this.add( "snd",		"audio/basic"					);
	this.add( "mid",		"audio/midi"					);
	this.add( "midi",		"audio/midi"					);
	this.add( "kar",		"audio/midi"					);
	this.add( "mpga",		"audio/mpeg"					);
	this.add( "mp2",		"audio/mpeg"					);
	this.add( "mp3",		"audio/mpeg"					);
	this.add( "aif",		"audio/x-aiff"					);
	this.add( "aiff",		"audio/x-aiff"					);
	this.add( "aifc",		"audio/x-aiff"					);
	this.add( "ram",		"audio/x-pn-realaudio"			);
	this.add( "rm",			"audio/x-pn-realaudio"			);
	this.add( "ra",			"audio/x-realaudio"				);
	this.add( "wav",		"audio/x-wav"					);
	this.add( "pdb",		"chemical/x-pdb"				);
	this.add( "xyz",		"chemical/x-pdb"				);
	this.add( "gif",		"image/gif"						);
	this.add( "ief",		"image/ief"						);
	this.add( "jpeg",		"image/jpeg"					);
	this.add( "jpg",		"image/jpeg"					);
	this.add( "jpe",		"image/jpeg"					);
	this.add( "png",		"image/png"						);
	this.add( "tiff",		"image/tiff"					);
	this.add( "tif",		"image/tiff"					);
	this.add( "ras",		"image/x-cmu-raster"			);
	this.add( "pnm",		"image/x-portable-anymap"		);
	this.add( "pbm",		"image/x-portable-bitmap"		);
	this.add( "pgm",		"image/x-portable-graymap"		);
	this.add( "ppm",		"image/x-portable-pixmap"		);
	this.add( "rgb",		"image/x-rgb"					);
	this.add( "xbm",		"image/x-xbitmap"				);
	this.add( "xpm",		"image/x-xpixmap"				);
	this.add( "xwd",		"image/x-xwindowdump"			);
	this.add( "igs",		"model/iges"					);
	this.add( "iges",		"model/iges"					);
	this.add( "msh",		"model/mesh"					);
	this.add( "mesh",		"model/mesh"					);
	this.add( "silo",		"model/mesh"					);
	this.add( "wrl",		"model/vrml"					);
	this.add( "vrml",		"model/vrml"					);
	this.add( "css",		"text/css"						);
	this.add( "asc",		"text/plain"					);
	this.add( "txt",		"text/plain"					);
	this.add( "rtx",		"text/richtext"					);
	this.add( "rtf",		"text/rtf"						);
	this.add( "sgml",		"text/sgml"						);
	this.add( "sgm",		"text/sgml"						);
	this.add( "tsv",		"text/tab-separated-values"		);
	this.add( "etx",		"text/x-setext"					);
	this.add( "xml",		"text/xml"						);
	this.add( "mpeg",		"video/mpeg"					);
	this.add( "mpg",		"video/mpeg"					);
	this.add( "mpe",		"video/mpeg"					);
	this.add( "qt",			"video/quicktime"				);
	this.add( "mov",		"video/quicktime"				);
	this.add( "avi",		"video/x-msvideo"				);
	this.add( "movie",		"video/x-sgi-movie"				);
	this.add( "ice",		"x-conference/x-cooltalk"		);
	this.add( "html",		"text/html"						);
	this.add( "htm",		"text/html"						);
};
