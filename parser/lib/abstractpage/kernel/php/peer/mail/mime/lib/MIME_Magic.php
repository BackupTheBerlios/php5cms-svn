<?php

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
|Authors: Chuck Hagenbuch <chuck@horde.org>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * The MIME_Magic class provides an interface to determine a
 * MIME type for various content, if it provided with different
 * levels of information.
 *
 * Currently, it can map a file extension to a MIME type, but
 * future ideas include using Apache's mod_mime_magic (if available).
 *
 * @package peer_mail_mime_lib
 */
 
class MIME_Magic extends PEAR
{
    /**
     * Attempt to convert a file extension to a MIME type.
     *
     * If we cannot map the file extension to a specific type, then
     * we fall back to a custom MIME handler x-extension/type, which
     * can be used as a normal MIME type internally.
     *
     * @access public
     *
     * @param string $ext  The file extension to be mapped to a MIME type.
     *
     * @return string  The MIME type of the file extension.
     */
    function extToMIME( $ext )
    {
        if ( empty( $ext ) ) 
		{
           return 'text/plain';
        } 
		else 
		{
            $ext = strtolower( $ext );
            $map = MIME_Magic::_getMimeExtensionMap();
			
            if ( !array_key_exists( $ext, $map ) )
                return "x-extension/$ext";
            else
                return $map[$ext];
        }
    }

    /**
     * Attempt to convert a filename to a MIME type.
     *
     * Unlike extToMIME(), this function will return
     * 'application/octet-stream' for any unknown or empty extension.
     *
     * @access public
     *
     * @param string $filename  The filename to be mapped to a MIME type.
     *
     * @return string  The MIME type of the filename.
     */
    function filenameToMIME( $filename )
    {
        $pos = strrpos( $filename, '.' );
        
		if ( !empty( $pos ) ) 
		{
            $type = MIME_Magic::extToMIME( substr( $filename, $pos + 1 ) );
            
			if ( !stristr( $type, 'x-extension' ) )
                return $type;
        }

        return 'application/octet-stream';
    }

    /**
     * Attempt to convert a MIME type to a file extension.
     *
     * If we cannot map the type to a file extension, we return false.
     *
     * @access public
     *
     * @param string $type The MIME type to be mapped to a file extension
     * @return string      The file extension of the MIME type
     */
    function MIMEToExt( $type )
    {
        $key = array_search( $type, MIME_Magic::_getMimeExtensionMap() );
		
        if ( empty( $type ) || ( $key === false ) || ( $key === null ) ) 
		{
            list( $major, $minor ) = explode( '/', $type );
			
            if ( $major == 'x-extension' )
                return $minor;
            
            return false;
        } 
		else 
		{
            return $key;
        }
    }
	
	
	// private methods
	
    /**
     * Returns a copy of the MIME extension map.
     *
     * @access private
     *
     * @return array  The MIME extension map.
     */
    function _getMimeExtensionMap()
    {
        static $mime_extension_map;

 		$mime_extension_map = array(
 			'Z'	 		=> 'application/x-compress',
 			'ai'	 	=> 'application/postscript',
 			'aif'	 	=> 'audio/x-aiff',
 			'aifc'	 	=> 'audio/x-aiff',
 			'aiff'	 	=> 'audio/x-aiff',
 			'asc'	 	=> 'text/plain',
 			'au'	 	=> 'audio/basic',
 			'avi'	 	=> 'video/x-msvideo',
 			'bcpio'	 	=> 'application/x-bcpio',
 			'bin'	 	=> 'application/octet-stream',
 			'bmp'	 	=> 'image/bmp',
 			'cdf'	 	=> 'application/x-netcdf',
 			'class'	 	=> 'application/octet-stream',
 			'cpio'	 	=> 'application/x-cpio',
 			'cpt'	 	=> 'application/mac-compactpro',
 			'csh'	 	=> 'application/x-csh',
 			'css'	 	=> 'text/css',
 			'dcr'	 	=> 'application/x-director',
 			'dir'	 	=> 'application/x-director',
 			'djv'	 	=> 'image/vnd.djvu',
 			'djvu'	 	=> 'image/vnd.djvu',
 			'dll'	 	=> 'application/octet-stream',
 			'dms'	 	=> 'application/octet-stream',
 			'doc'	 	=> 'application/msword',
 			'dvi'	 	=> 'application/x-dvi',
 			'dxr'	 	=> 'application/x-director',
 			'eps'	 	=> 'application/postscript',
 			'etx'	 	=> 'text/x-setext',
 			'exe'	 	=> 'application/octet-stream',
 			'ez'	 	=> 'application/andrew-inset',
 			'gif'	 	=> 'image/gif',
 			'gtar'	 	=> 'application/x-gtar',
 			'gz'	 	=> 'application/x-gzip',
 			'hdf'	 	=> 'application/x-hdf',
 			'hqx'	 	=> 'application/mac-binhex40',
 			'htm'	 	=> 'text/html',
 			'html'	 	=> 'text/html',
 			'ice'	 	=> 'x-conference/x-cooltalk',
 			'ief'	 	=> 'image/ief',
 			'iges'	 	=> 'model/iges',
 			'igs'	 	=> 'model/iges',
 			'jpe'	 	=> 'image/jpeg',
 			'jpeg'	 	=> 'image/jpeg',
 			'jpg'	 	=> 'image/jpeg',
 			'js'	 	=> 'application/x-javascript',
 			'kar'	 	=> 'audio/midi',
 			'latex'	 	=> 'application/x-latex',
 			'lha'	 	=> 'application/octet-stream',
 			'lzh'	 	=> 'application/octet-stream',
 			'm3u'	 	=> 'audio/x-mpegurl',
 			'man'	 	=> 'application/x-troff-man',
 			'me'	 	=> 'application/x-troff-me',
 			'mesh'	 	=> 'model/mesh',
 			'mid'	 	=> 'audio/midi',
 			'midi'	 	=> 'audio/midi',
 			'mif'	 	=> 'application/vnd.mif',
 			'mov'	 	=> 'video/quicktime',
 			'movie'	 	=> 'video/x-sgi-movie',
 			'mp2'	 	=> 'audio/mpeg',
 			'mp3'	 	=> 'audio/mpeg',
 			'mpe'	 	=> 'video/mpeg',
 			'mpeg'	 	=> 'video/mpeg',
 			'mpg'	 	=> 'video/mpeg',
 			'mpga'	 	=> 'audio/mpeg',
 			'ms'	 	=> 'application/x-troff-ms',
 			'msh'	 	=> 'model/mesh',
 			'mxu'	 	=> 'video/vnd.mpegurl',
 			'nc'	 	=> 'application/x-netcdf',
 			'oda'	 	=> 'application/oda',
 			'pbm'	 	=> 'image/x-portable-bitmap',
 			'pdb'	 	=> 'chemical/x-pdb',
 			'pdf'	 	=> 'application/pdf',
 			'pgm'	 	=> 'image/x-portable-graymap',
 			'pgn'	 	=> 'application/x-chess-pgn',
 			'php'	 	=> 'application/x-httpd-php',
 			'php3'	 	=> 'application/x-httpd-php3',
 			'png'	 	=> 'image/png',
 			'pnm'	 	=> 'image/x-portable-anymap',
 			'ppm'	 	=> 'image/x-portable-pixmap',
 			'ppt'	 	=> 'application/vnd.ms-powerpoint',
 			'ps'	 	=> 'application/postscript',
 			'qt'	 	=> 'video/quicktime',
 			'ra'	 	=> 'audio/x-realaudio',
 			'ram'	 	=> 'audio/x-pn-realaudio',
 			'ras'	 	=> 'image/x-cmu-raster',
 			'rgb'	 	=> 'image/x-rgb',
 			'rm'	 	=> 'audio/x-pn-realaudio',
 			'roff'	 	=> 'application/x-troff',
 			'rpm'	 	=> 'audio/x-pn-realaudio-plugin',
 			'rtf'	 	=> 'text/rtf',
 			'rtx'	 	=> 'text/richtext',
 			'sgm'	 	=> 'text/sgml',
 			'sgml'	 	=> 'text/sgml',
 			'sh'	 	=> 'application/x-sh',
 			'shar'	 	=> 'application/x-shar',
 			'silo'	 	=> 'model/mesh',
 			'sit'	 	=> 'application/x-stuffit',
 			'skd'	 	=> 'application/x-koan',
 			'skm'	 	=> 'application/x-koan',
 			'skp'	 	=> 'application/x-koan',
 			'skt'	 	=> 'application/x-koan',
 			'smi'	 	=> 'application/smil',
 			'smil'	 	=> 'application/smil',
 			'snd'	 	=> 'audio/basic',
 			'so'	 	=> 'application/octet-stream',
 			'spl'	 	=> 'application/x-futuresplash',
 			'src'	 	=> 'application/x-wais-source',
 			'sv4cpio'	=> 'application/x-sv4cpio',
 			'sv4crc'	=> 'application/x-sv4crc',
 			'swf'	 	=> 'application/x-shockwave-flash',
 			't'	 		=> 'application/x-troff',
 			'tar'	 	=> 'application/x-tar',
 			'tcl'	 	=> 'application/x-tcl',
 			'tex'	 	=> 'application/x-tex',
 			'texi'	 	=> 'application/x-texinfo',
 			'texinfo'	=> 'application/x-texinfo',
 			'tif'	 	=> 'image/tiff',
 			'tiff'	 	=> 'image/tiff',
 			'tr'	 	=> 'application/x-troff',
 			'tsv'	 	=> 'text/tab-separated-values',
 			'txt'	 	=> 'text/plain',
 			'ustar'	 	=> 'application/x-ustar',
 			'vcd'	 	=> 'application/x-cdlink',
 			'vcf'	 	=> 'text/x-vcard',
 			'vrml'	 	=> 'model/vrml',
 			'vsd'	 	=> 'application/vnd.visio',
 			'wav'	 	=> 'audio/x-wav',
 			'wbmp'	 	=> 'image/vnd.wap.wbmp',
 			'wbxml'	 	=> 'application/vnd.wap.wbxml',
 			'wml'	 	=> 'text/vnd.wap.wml',
 			'wmlc'	 	=> 'application/vnd.wap.wmlc',
 			'wmls'	 	=> 'text/vnd.wap.wmlscript',
 			'wmlsc'	 	=> 'application/vnd.wap.wmlscriptc',
 			'wrl'	 	=> 'model/vrml',
 			'xbm'	 	=> 'image/x-xbitmap',
 			'xht'	 	=> 'application/xhtml+xml',
 			'xhtml'	 	=> 'application/xhtml+xml',
 			'xls'	 	=> 'application/vnd.ms-excel',
 			'xml'	 	=> 'text/xml',
 			'xpm'	 	=> 'image/x-xpixmap',
 			'xsl'	 	=> 'text/xml',
 			'xwd'	 	=> 'image/x-xwindowdump',
 			'xyz'	 	=> 'chemical/x-xyz',
 			'zip'	 	=> 'application/zip'
		);

        return $mime_extension_map;
    }
} // END OF MIME_Magic

?>
