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
 * DBF Reader Class
 * 
 * Usage
 * 
 * $file = "your_file.dbf";
 * $dbf  = new DBF( $file );
 * $num_rec   = $dbf->dbf_rec_size;
 * $field_num = $dbf->dbf_field_size;
 * $arrRec    = $dbf->dbf_record;
 * $arrField  = $dbf->dbf_field;
 * 
 * echo( '<table border=1>' );
 * echo( '<tr>' );
 * 
 * for ( $j = 0; $j < $field_num; $j++ )
 * 		echo( '<td>' . $arrField[$j] . '&nbsp;</td>' );
 * 
 * echo( '<tr>' );
 * 
 * for ( $i = 0; $i < $num_rec; $i++ )
 * {
 * 		echo( '<tr>' );
 * 	
 * 		for ( $j = 0; $j < $field_num; $j++ )
 * 			echo( '<td>' . $arrRec[$i][$arrField[$j]] . '&nbsp;</td>' );
 * 	
 * 		echo( '<tr>' );
 * }
 * 
 * echo( '</table>' );
 *
 * @package db_dbase
*/

class DBF extends PEAR
{		
	var $dbf_record = array();
	var $dbf_field  = array();

	
	/**
	 * Constructor
	 *
	 * @param string $filename name of the DBF( dBase III ) file
	 * @return 
	 */
	function DBF( $filename ) 
	{
		if ( file_exists( $filename )  && ereg( 'DBF', strtoupper( $filename ) ) ) 
		{		
			// Read the File.
			$handle = fopen( $filename, "r" );
			
			if ( !$handle ) 
			{
				$this = new PEAR_Error( "Cannot read DBF file." ); 
				return;
			}
			
			$filesize = filesize( $filename );
			$doc      = fread( $handle, $filesize );
			
			fclose( $handle );
			
			if ( ord( $doc{0} ) != 3 && ord( $doc{$filesize} ) != 26 )
			{
				$this = new PEAR_Error( "Not a valid DBF file." ); 
				return;
			}
			
			$arrHeaderHex = array();
			
			for ( $i = 0; $i < 32; $i++ )
				$arrHeaderHex[$i] = dechex( ord( $doc{$i} ) );
				
			// Initial value
			$line     = 32; // Header Size
			$recnum   = hexdec( $arrHeaderHex[7]  . $arrHeaderHex[6] . $arrHeaderHex[5] . $arrHeaderHex[4] ); // Record Size
			$hdrsize  = hexdec( $arrHeaderHex[9]  . $arrHeaderHex[8]  ); // Header Size+Firled Descriptor
			$recsize  = hexdec( $arrHeaderHex[11] . $arrHeaderHex[10] ); // Field Size
			$numfield = floor( ( $hdrsize - $line ) / $line );			 // Number of Fields
			
			$this->dbf_field_size = $numfield;
			$this->dbf_rec_size   = $recnum;
				
			// Field properties retrieval looping
			for ( $j = 0; $j < $numfield; $j++ )
			{
				$name = '';
				
				for ( $k = ( ( $j + 1 ) * $line ); $k <= ( ( $j + 1 ) * $line ) + 10; $k++ )
				{
					if ( ord( $doc{$k} ) )
						$name .= $doc{$k};
				}
			
				$arrField[$j]['name'] = $name; // Name of the Field
				$arrField[$j]['len']  = ord( $doc{( ( ( $j + 1 ) * 32 ) + 16 )} ); // Length of the field
				$this->dbf_field[$j]  = $name;
			}
				
			// Record retrieval looping
			for ( $n = 0; $n < $recnum; $n++ )
			{
				$name = '';
				$pred = 1;
				$name = substr( $doc, ( $n * $recsize ) + $hdrsize, $recsize );
			
				for ( $m = 0; $m < $numfield; $m++ )
				{
					$arrRecords[$n][$arrField[$m]['name']] = trim( substr( $name, $pred, $arrField[$m]['len'] ) );
					$pred += $arrField[$m]['len'];
				}
			}
			
			$this->dbf_record = $arrRecords;
			unset( $doc );
		} 
	  	else 
	  	{
			$this = new PEAR_Error( "Not a DBF file or file doesn't exist." ); 
			return;
		}
	}	
} // END OF DBF

?>
