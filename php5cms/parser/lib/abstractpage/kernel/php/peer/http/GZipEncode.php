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
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * A class to gzip encode php output.
 *
 * How to use:
 * 1. Output buffering has to be turned on. You can do this with ob_start()
 *    <http://php.net/manual/function.ob-start.php> or in the php config
 *    file. Nothing bad happens if output buffering isn't turned on, your
 *    page just won't get compressed.
 * 2. Include the class file.
 * 3. At the _very_ end of your script create an instance of the encode
 *    class.
 *
 * eg:
 *    ------------Start of file----------
 *    |<?php
 *    | ob_start();
 *    | include('class.gzip_encode.php');
 *    |?>
 *    |<HTML>
 *    |... the page ...
 *    |</HTML>
 *    |<?php
 *    | new gzip_encode();
 *    |?>
 *    -------------End of file-----------
 *
 * Things to note:
 * 1. There is no space before the beginning of the file and the '<?php' tag
 * 2. The ob_start() line is optional if output buffering is turned on in
 *    the main config file.
 * 3. Turning on and off output buffering just won't work.
 * 4. There must be nothing after the last '?>' tag at the end of the file.
 *    Be careful of a space hiding there.
 * 5. There are better ways to compress served content but I think this is
 *    the only way to compress php output.
 * 6. Your auto_prepend_file is a good place for the ob_start() and
 *    your auto_append_file is a good place for new gzip_encode().
 * 7. If you put new gzip_encode() in your auto.append file then you can
 *    call ob_end_flush() in your script to disable compression.
 *
 * This was written from scratch from info freely available on the web.
 *
 * These site(s) were useful to me:
 *    http://www.php.net/manual/
 *    http://www.ietf.org/rfc/rfc2616.txt (Sections: 3.5, 14.3, 14.11)
 *
 * @package peer_http
 */
 
class GZipEncode extends PEAR
{
	/**
	 * Compression level
	 * @access public
	 */
    var $level;
	
	/**
	 * Encoding type
	 * @access public
	 */
    var $encoding;
	
	/**
	 * crc of the output
	 * @access public
	 */
    var $crc;
	
	/**
	 * size of the uncompressed content
	 * @access public
	 */
    var $size;
	
	/**
	 * size of the compressed content
	 * @access public
	 */
    var $gzsize;
	
	/**
	 * Masquerade
	 * @access public
	 */
	var $encoder;

	
    /**
     * Constructor
	 *
	 * Gzip encodes the current output buffer if the browser supports it.
     * Note: all arguments are optionial.
     *
     * You can specify one of the following for the first argument:
     *	0:	  No compression
     *	1:	  Min compression
     *	...	  Some compression (integer from 1 to 9)
     *	9:	  Max compression
     *	true: Determin the compression level from the system load. The
     *		  higher the load the less the compression.
     *
     * You can specify one of the following for the second argument:
     *	true:	Don't actully output the compressed form but run as if it
     *		    had. Used for debugging.
	 *
	 * @access public
     */
    function GZipEncode( $level = 3 )
	{
		$this->encoder = ap_ini_get( "agent_name", "settings" );
		
		if ( !function_exists( 'gzcompress' ) )
		{
			$this = new PEAR_Error( "gzcompress not found, zlib needs to be installed." );
			return;
		}
	
		if ( !function_exists( 'crc32' ) )
		{
			$this = new PEAR_Error( "crc32() not found, PHP >= 4.0.1 needed." );
			return;
		}
	
		if ( headers_sent() )
			return;
	
		if ( connection_aborted() )
			return;
	
		if ( connection_timeout() )
			return;
	
		$encoding = $this->gzip_accepted();
	
		if ( !$encoding )
			return;
	
		$this->encoding = $encoding;

		if ( $level === true )
			$level = $this->get_complevel();
			
		$this->level = $level;

		$contents = ob_get_contents();
	
		if ( $contents === false )
			return;

		// gzip header
		$gzdata  = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		$size    = strlen( $contents );
		$crc     = crc32( $contents );
		$gzdata .= gzcompress( $contents, $level );
		
		// fix crc bug
		$gzdata  = substr( $gzdata, 0, strlen( $gzdata ) - 4 );
		
		$gzdata .= pack( "V", $crc ) . pack( "V", $size );

		$this->size   = $size;
		$this->crc    = $crc;
		$this->gzsize = strlen( $gzdata );

		ob_end_clean();
		header( 'Content-Encoding: ' . $encoding );
		header( 'Content-Length: ' . strlen( $gzdata ) );
		header( 'X-Content-Encoded-By: ' . $this->encoder );

		echo( $gzdata );
	}
   

    /**
     * gzip_accepted() - Test headers for Accept-Encoding: gzip
     *
     * Returns: if proper headers aren't found: false
     *          if proper headers are found: 'gzip' or 'x-gzip'
     *
     * Tip: using this function you can test if the class will gzip the output
     *      without actually compressing it yet, eg:
     *      if ( GZipEncode::gzip_accepted() ) {
     *         echo "Page will be gziped";
     *      }
     *  Note the double colon syntax, I don't know where it is documented but
     *  somehow it got in my brain.
	 *
	 * @access public
     */
    function gzip_accepted()
	{
		if ( strpos( $_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip'   ) === false )
			return false;
	
		if ( strpos( $_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip' ) === false )
			$encoding = 'gzip';
		else
			$encoding = 'x-gzip';

		// Test file type. I wish I could get HTTP response headers.
		$magic = substr( ob_get_contents(), 0, 4 );
	
		if ( substr( $magic, 0, 2 ) === '^_' )
		{
	    	// gzip data
	    	$encoding = false;
		}
		else if ( substr( $magic, 0, 3 ) === 'GIF' )
		{
	    	// gif images
	    	$encoding = false;
		}
		else if ( substr( $magic, 0, 2 ) === "\xFF\xD8" )
		{
	    	// jpeg images
	    	$encoding = false;
		}
		else if ( substr( $magic, 0, 4 ) === "\x89PNG" )
		{
	    	// png images
	    	$encoding = false;
		}
		else if ( substr( $magic, 0, 3 ) === 'FWS' )
		{
	    	// Don't gzip Shockwave Flash files. Flash on windows incorrectly
	    	// claims it accepts gzip'd content.
	    	$encoding = false;
		}
		else if ( substr( $magic, 0, 2 ) === 'PK' )
		{
	    	// pk zip file
	    	$encoding = false;
		}

		return $encoding;
	}

    /**
     * get_complevel() - The level of compression we should use.
     *
     * Returns an int between 0 and 9 inclusive.
     *
     * Tip: $gzleve = GZipEncode::get_complevel(); to get the compression level
     *      that will be used with out actually compressing the output.
	 *
	 * @access public
     */
    function get_complevel()
	{
		$uname = posix_uname();
	
		switch ( $uname['sysname'] )
		{
	    	case 'Linux' :
				$cl = ( 1 - $this->_loadavg_linux() ) * 10;
				$level = (int)max( min( 9, $cl ), 0 );
				
				break;
	    
			case 'FreeBSD' :
				$cl = ( 1 - $this->_loadavg_freebsd() ) * 10;
				$level = (int)max( min( 9, $cl ), 0 );
				
				break;
	    
			default :
				$level = 3;
				break;
		}
	
		return $level;
	}


	// private methods
	
    /**
     * _loadavg_linux() - Gets the max() system load average from /proc/loadavg
     * The max() Load Average will be returned.
	 *
	 * @access public
     */
    function _loadavg_linux()
	{
		$buffer = "0 0 0";
		$f = fopen( ap_ini_get( "file_loadavg", "file" ), "r" );
	
		if ( !feof( $f ) )
			$buffer = fgets( $f, 1024 );
			
		fclose( $f );
		$load = explode( " ", $buffer );
		
		return max( (float)$load[0], (float)$load[1], (float)$load[2] );
	}

    /**
     * _loadavg_freebsd() - Gets the max() system load average from uname(1)
     *
     * The max() Load Average will be returned.
     * I've been told the code below will work on solaris too, anyone wanna test it?
     */
    function _loadavg_freebsd()
	{
		$buffer= `uptime`;
		ereg( "averag(es|e): ([0-9][.][0-9][0-9]), ([0-9][.][0-9][0-9]), ([0-9][.][0-9][0-9]*)", $buffer, $load );

		return max( (float)$load[2], (float)$load[3], (float)$load[4] );
	} 
} // END OF GZipEncode

?>
