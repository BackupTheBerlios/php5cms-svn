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
 * @package util_text_encoding
 */
 
class Base64 extends PEAR
{
	/**
	 * @access public
	 */
	var $chunk_length;
	
	/**
	 * @access public
	 */
	var $decode_chunk_length;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Base64()
	{
		// How many bytes to encode at a time 
		// (must be a multiple of 3, and less than (76*0.75)
		$this->encode_chunk_length = 45;
		// $this->encode_chunk_length = 60*57;

		// How many bytes to decode at a time
		$this->decode_chunk_length = 32 * 1024;
	}

	/**
	 * @access public
	 */
	function encodeFile( $file, $output_file )
	{
		if ( $fh = fopen( $file, 'r' ) )
		{
			if ( $ofh = fopen( $output_file, 'w' ) )
			{
				while ( $buffer = fread( $fh, $this->encode_chunk_length ) )
				{
					$encoded = base64_encode( $buffer );
					
					if ( ! ereg( "\n", $encoded ) )
						$encoded .= "\n";
 
					fwrite( $ofh, $encoded );
				}
				
				fclose( $ofh );
				fclose( $fh  );
				
				return true;
			}
			
			fclose( $fh );
			return false;
		}
		
		return false;
	}

	/**
	 * @access public
	 */
	function decodeFile( $file, $output_file  )
	{
		if ( $fh = fopen( $file, 'r' ) )
		{
			if ( $ofh = fopen( $output_file, 'w' ) )
			{
				$buffer  = '';
				$len_4xN = 0;
				
				while( $tbuffer = fread( $fh, $this->decode_chunk_length ) )
				{
					// remove any non-base64 chars
					// TODO: tr{A-Za-z0-9+/}{}cd ?? 
					// $tbuffer = ereg_replace( '[^A-Za-z0-9\+\/]', '', $tbuffer );
					$tbuffer = ereg_replace( '[^A-Za-z0-9\+\/]', '', $tbuffer );

					// concat current working buffer onto string
					$buffer .= $tbuffer;

					if ( strlen( $buffer ) >= $this->decode_chunk_length )
					{
						$len_4xN = strlen( $buffer ) & ~3;
						fwrite( $ofh, base64_decode( substr( $buffer, 0, $len_4xN ) ) );
						$buffer = substr( $buffer, $len_4xN );
					}
				}
				
				if ( strlen( $buffer ) )
				{
					// Okay leftover data
					// $buffer .= '===';
					$buffer .= '===';
					$tmp_var = strlen( $buffer ) & ~3;
					fwrite( $ofh, base64_decode( substr( $buffer, 0, $tmp_var ) ) );
					$buffer = '';
				}
				
				fclose( $fh  );
				fclose( $ofh );
				
				return true;
			}
			
			fclose( $fh );
			return false;
		}
		
		return false;
	}
} // END OF Base64

?>
