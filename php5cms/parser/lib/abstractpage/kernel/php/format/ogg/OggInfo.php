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
 * @package format_ogg
 */
 
class OggInfo extends PEAR
{
    /**
     * Takes a single string which contains the path to the file which to 
     * extract the info. 
     *    Example: "/path/to/file.ogg" 
     * 
     * It returns an associative array containing whatever information was 
     * gathered. If the array contains only size,name,path then this 
     * file contained no user comment fields.
	 *
	 * @access public
	 * @static
     */ 
    function get( $filename ) 
	{ 
        $fp   = fopen( $filename, "r" ); 
        $info = array(); 
        $info['size'] = filesize( $filename ); 
        $info['name'] = basename( $filename ); 
        $info['path'] = dirname( $filename ); 
     
        $done = false; 
        while ( $done == false ) 
		{ 
            $working = fread( $fp, 1 ); 
            
			if ( $working == "l" ) 
			{ 
                $working .= fread( $fp, 8 ); 
                
				if ( $working == "libVorbis" ) 
                    $done = true;
            } 
        } 

        while ( ord( $working ) > 31 )
            $working = fread( $fp, 1 ); 
        
        $tag = ""; 
        while ( $tag != "done" ) 
		{ 
            $working = ""; 
            while ( ( $working != "=" ) && ( $tag != "done" ) ) 
			{ 
                if ( ord( $working ) > 31 ) 
                    $tag .= $working; 
                
                $working = fread( $fp, 1 ); 
                
				if ( $tag == "v" ) 
				{ 
                    $tag .= "o" . fread( $fp, 9 );
                
					if ( ( substr_count( $tag, "vorbis" ) == 1 ) && ( substr_count( $tag, "BCV" ) == 1 ) ) 
                        $tag = "done"; 
                    else 
                        fseek( $fp, ( ftell( $fp ) - 10 ) );
              	} 
            } 

            if ( $tag != "done" ) 
			{ 
                $working = fread( $fp, 1 ); 
                while ( ord( $working ) > 31 ) 
				{ 
                    $title   .= $working; 
                    $working  = fread( $fp, 1 ); 
                }
				
                $info[$tag] = $title; 
                $tag   = ""; 
                $title = ""; 
            } 
        } 
		
        fclose( $fp ); 
        return $info; 
    }
} // END OF OggInfo

?>
