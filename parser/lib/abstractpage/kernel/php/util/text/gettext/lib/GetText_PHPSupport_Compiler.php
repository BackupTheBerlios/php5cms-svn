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
|Authors: Laurent Bedubourg <laurent.bedubourg@free.fr>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * This class write a php file from a gettext hashtable.
 *
 * The produced file return the translation hashtable on include.
 * 
 * @package util_text_gettext_lib
 */
 
class GetText_PHPSupport_Compiler extends PEAR
{
    /**
     * Write hash in an includable php file.
	 *
	 * @access public
     */
    function compile( &$hash, $sourcePath )
    {
        $destPath = preg_replace( '/\.po$/', '.php', $sourcePath );
        $fp = @fopen( $destPath, "w" );
		
        if ( !$fp ) 
            return PEAR::raiseError( sprintf( 'Unable to open "%s" in write mode.', $destPath ) );
		
        fwrite( $fp, '<?php' . "\n" );
        fwrite( $fp, 'return array(' . "\n" );
		
        foreach ( $hash as $key => $value ) 
		{
            $key   = str_replace( "'", "\\'", $key   );
            $value = str_replace( "'", "\\'", $value );
			
            fwrite( $fp, '    \'' . $key . '\' => \'' . $value . "',\n" );
        }
		
        fwrite( $fp, ');' . "\n" );
        fwrite( $fp, '?>' );
        fclose( $fp );
    }
} // END OF GetText_PHPSupport_Compiler

?>
