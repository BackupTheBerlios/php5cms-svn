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
 * Description:
 * This is a spell checking function that uses /usr/bin/ispell to check
 * a form input against a dictionary. 
 *
 * The returned $outString looks exactly like the original $inString
 * except that any words which did not match against the ispell dictionary
 * are html tagged in red and bold letters.  You can then write your script
 * to display $outString to the user so s/he can visually inspect the
 * returned message and then either re-edit it or submit it to your form 
 * as good and correct.
 *
 * Requirements:
 *
 * 1: Your system must have ispell installed and in the path.  If you have 
 *	  it installed but not in the path, edit this script to call the correct
 *	  path for ispell.
 * 2: Your server must be set up to allow the Apache/PHP user, which is
 *	  usually "nobody", to have write access to the directory in which this 
 *	  script runs.  This script creates and deletes two files per call, so 
 *	  it needs a place for those files.  If you prefer, you may use "/tmp" 
 *	  or another "safe" location.
 *
 * @package util_text_spell
 */

class ISpellUtil
{
	/**
	 * @access public
	 * @static
	 */
	function check( $inString )
	{
		// regexp to ignore html tags.
		$inString = eregi_replace( "<[^>]*>", " ", $inString );
		mt_srand( (double)microtime() * 1000000 );
		
		$rfn = mt_rand();
		$scf = fopen( "./in-$rfn", "w+" );
		
		fwrite( $scf, $inString );
		fclose( $scf );

		$cmdline = "ispell -l < in-$rfn > out-$rfn";
		system( $cmdline );
		unlink( "in-$rfn" );

		if ( filesize( "out-$rfn" ) > 1 )
		{
			$winc = 0;
			$scf  = fopen( "out-$rfn", "r" );
		
			while ( !feof( $scf ) )
			{
				$badwords[ $winc ]  = trim( fgets( $scf, 255 ) );
				$goodwords[ $winc ] = "<font color=\"red\"><b>" . $badwords[ $winc ] . "</b></font>";
				$winc++;
			}
			
			fclose( $scf );
			$winc--;
			
			for ( $ridx = 0; $ridx < $winc; $ridx++ )
				$inString = str_replace( $badwords[ $ridx ], $goodwords[ $ridx ], $inString );
		}
		
		unlink( "out-$rfn" );
		return $inString;
	}
} // END OF ISpellUtil

?>
