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


using( 'db.nanodb.backup.NanoDBBackup' );


/** 
 * Creates a (gzip'ed) compressed archive of files
 * that can be extracted with the help of a PERL interpreter.
 *
 * @package db_nanosql_backup
 */

class NanoDBBackup_pear extends NanoDBBackup
{
	/**
	 * Constructor
	 */
	function NanoDBBackup_pear( $options = array() )
	{
		$this->NanoDBBackup( $options );
	}
	
	
	/**
	 * @abstract Archives (and gzips) all added files
     * @param asfilename  the filename of the archive
     * @param temp_dir  a directory where to store a temporary file.  This file
     * is automatically deleted once it has been used.  Defaults to the current
     * directory.
     * @return boolean true on success, false on failure.
     */
   function create( $asfilename, $tmpdir )
   {
      	$tmpname = tempnam( $tmpdir, "pear" );
      	$tmpfp   = gzopen( $tmpname, "wb" );
      
	  	if ( !$tmpfp )
         	return false;

      	// put in the header
      	gzputs( $tmpfp, "# This is an archive containing many files.\n" );
      	gzputs( $tmpfp, "# To extract them, type 'perl $asfilename' at your command prompt.\n" );

      	foreach ( $this->filelist as $file )
      	{
         	gzputs( $tmpfp, "e(<<\"Z\", \"" . basename( $file ) . "\");\n" );
         	$fp = @fopen( $file, "rb" );
         
		 	if ( $fp ) 
         	{
            	// copy the file to the temporary file, in ASCII hex
            	$size   = filesize( $file );
            	$buffer = fread( $fp, $size );
            
				for ( $i = 0; $i < $size; ++$i ) 
               		gzputs( $tmpfp, sprintf( "%02X", ord( $buffer{$i} ) ) );
            
				gzputs( $tmpfp, "\nZ\n" );
            	fclose( $fp );
         	}
      	}

      	// put in the extractor subroutine
      	gzputs(
         	$tmpfp, 
         	"sub e($$) {\n  my(\$d,\$f)=@_; chomp \$d; \$l=length(\$d); " .
        	"open(F, \">\$f\"); binmode F;\n  for(\$i=0; \$i<\$l; \$i+=2) { " .
        	"print F chr(hex(substr(\$d, \$i, 2))); }\n  close(F);\n}\n"
      	);

      	gzclose( $tmpfp );
   	}
} // END OF NanoDBBackup_pear

?>
