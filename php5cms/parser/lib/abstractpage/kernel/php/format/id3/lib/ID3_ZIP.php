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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


using( 'format.id3.lib.ID3' );


/**
 * @package format_id3_lib
 */
 
class ID3_ZIP extends ID3
{
	function getZipHeaderFilepointer( $filename, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat'] = 'zip';
	
		if ( !function_exists( 'zip_open' ) )
		{
			$MP3fileInfo['error'][] = 'Zip functions not available (requires at least PHP 4.0.7RC1 and ZZipLib (http://zziplib.sourceforge.net/).';
			return false;
		} 
		else if ( $zip = zip_open( $filename ) ) 
		{
			$zipentrycounter = 0;
		
			while ( $zip_entry = zip_read( $zip ) ) 
			{
				$MP3fileInfo['zip']['entries']["$zipentrycounter"]['name']              = zip_entry_name( $zip_entry );
				$MP3fileInfo['zip']['entries']["$zipentrycounter"]['filesize']          = zip_entry_filesize( $zip_entry );
				$MP3fileInfo['zip']['entries']["$zipentrycounter"]['compressedsize']    = zip_entry_compressedsize( $zip_entry );
				$MP3fileInfo['zip']['entries']["$zipentrycounter"]['compressionmethod'] = zip_entry_compressionmethod( $zip_entry );

				$zipentrycounter++;
			}
		
			zip_close( $zip );
			return true;
		} 
		else 
		{
			$MP3fileInfo['error'][] = 'Could not open file.';
			return false;
		}
	}
} // END OF ID3_ZIP

?>
