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
 * This class reads CA/Clipper NTX index files, similar to the xBase seek function. 
 * Writing is not (yet) possible. 
 *
 * Usage:
 *
 * $ntx = new NTXFile( "customer.ntx" );
 * $recno = $ntx->Seek( "Johnson" );
 *
 * if ( $recno === false )
 * {
 *		echo "Not Found";
 * }
 * else
 * {
 *		while ( $recno !== false )
 *		{
 *			// Use $recno in dbase_get_record()
 *			$recno = $this->next();
 *		}
 * }
 *	
 * $ntx->Close();
 *
 * @package io
 */

class NTXFile extends PEAR
{
	/**
	 * @access public
	 */
	var $Long;
	
	/**
	 * @access public
	 */
	var $Word;
	
	/**
	 * @access public
	 */
	var $TDate;
	
	/**
	 * @access public
	 */
	var $TIndexKey;
	
	/**
	 * @access public
	 */
	var $Byte;
	
	/**
	 * @access public
	 */
	var $NtxHeaderParse;
	
	/**
	 * @access public
	 */
	var $ItemParse;
	
	/**
	 * @access public
	 */
	var $MyIndexKey;
	
	/**
	 * @access public
	 */
	var $headersize;	// b-tree page size
	
	/**
	 * @access public
	 */
	var $_NtxName;		// name of ntx file
	
	/**
	 * @access public
	 */
	var $ntxfp;			// file pointer to ntx file
	
	/**
	 * @access public
	 */
	var $nr;			// index number in b-tree page
	
	/**
	 * @access public
	 */
	var $firstofs;		// offset of first page
	
	/**
	 * @access public
	 */
	var $ofsndx;		// offset in ntx file to current b-tree page
	
	/**
	 * @access public
	 */
	var $softseek;		// Seek() returns false if keys do not match anymore
						// note: softseek not implemented yet
						
	/**
	 * @access public
	 */
	var $lookupkey;		// key to look up
	
	/**
	 * @access public
	 */
	var $buffer;		// holds current page
	
	/**
	 * @access public
	 */
	var $nrentries;		// number of entries on page

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function NTXFile( $ntxfilename )
	{
		$this->headersize      = 1024;  // all pages are 1K
		$this->keysize         = 256;   // size of index keys
		$this->softseek        = false;
		$this->lookupkey       = "";

		$this->TChar           = "C1";
		$this->Long            = "V1";
		$this->Word            = "v1";
		$this->TDate           = "C8";
		$this->TIndexKey       = "a256";
		$this->Byte            = "C1";

		$this->NtxHeaderParse  = "{$this->Word}sign/{$this->Word}version/{$this->Long}ofsndx/{$this->Long}ofskey/{$this->Word}dist/{$this->Word}keysize/{$this->Word}nrdec/{$this->Word}maxentr/{$this->Word}minentr/{$this->TIndexKey}key/{$this->Byte}unique";

		$this->_NtxName        = $ntxfilename;
		$this->ntxfp           = fopen( $this->_NtxName, "rb" );
		$ntxHeader             = $this->IndexHeader();
		$this->ofsndx          = $ntxHeader["ofsndx"];
		$this->firstofs        = $this->ofsndx;
		$this->nr              = 1;


		/*
		Xxplanation of the fields:
        
		sign        : word; {signature byte}
		version     : word; {clipper indexing version number}
		ofsndx      : long; {offset in file for first index page}
		ofskey      : long; {offset to an unused key page}
		dist        : word; {key size + 8 bytes (distance between key pages)}
		keysize     : word; {key size}
		nrdec       : word; {number of decimals in key}
		maxentr     : word; {maximum entries per page}
		minentr     : word; {minimum entries per page or half page}
		key         : TIndexKey;
		unique      : byte;
		*/
	}


	/**
	 * @access public
	 */	
	function IndexHeader()
	{
		$this->gotopage( 0 );
		$header_arr = unpack( $this->NtxHeaderParse, $this->buffer );
		$keysize = $header_arr["keysize"];
		$this->MyIndexKey = "a{$keysize}";
		$this->ItemParse  = "{$this->Long}prior/{$this->Long}recno/{$this->MyIndexKey}key";
		
		return $header_arr;
	}

	/**
	 * @access public
	 */
	function compare( $a, $b )
	{
		if ( $a > $b )
			$compare = 1;
		else if ( $a < $b )
			$compare = -1;
		else
			$compare = 0;

		return $compare;
	}

	/**
	 * @access public
	 */
	function gotopage( $ofsndx )
	{
		echo "Page: $ofsndx<br>\n";
		fseek( $this->ntxfp, $ofsndx );
		$this->buffer = fread( $this->ntxfp, $this->headersize );
		$temp = unpack( "v1nrentries", $this->buffer );
		$this->nrentries = $temp["nrentries"];
	}

	/**
	 * @access public
	 */
	function IndexRecords()
	{
		$found = false;
		$done  = false;

		do
		{
			$temp     = unpack( "v{$this->nr}dummy/v1nroffset", $this->buffer );
			$nroffset = $temp["nroffset"];
			$temp     = unpack( "C{$nroffset}dummy/{$this->ItemParse}", $this->buffer );
			$prior    = $temp["prior"];
			$recno    = $temp["recno"];
			$key      = $temp["key"];
			
			switch ( $this->compare( $key, $this->lookupkey ) )
			{
				case +1:
					if ( $prior == 0 )
					{
						$found = false;
					}
					else
					{
						$this->nr = 1;
						$this->gotopage( $prior );
						$found = $this->IndexRecords();
					}
					
					$done = true;
					break;
					
				case 0:
					$found = $recno;
					$done  = true;
					
					break;
					
				case -1:
					// continue looking
					$this->nr++;
					
					if ( $this->nr > $this->nrentries + 1 )
					{
						// last entry holds pointer to next page
						if ( $prior == 0 )
						{
							$found = false;
						}
						else
						{
							$this->nr = 1;
							$this->gotopage( $prior );
							$found = $this->IndexRecords();
						}
						
						$done = true;
					}
					
					break;
			}
		} while ( !$done );
			
		return $found;
	}

	/**
	 * @access public
	 */
	function Next()
	{
		$this->nr++;
		echo "$this->nr / $this->nrentries - ";
		
		$temp     = unpack("v{$this->nr}dummy/v1nroffset", $this->buffer);
		$nroffset = $temp["nroffset"];
		$temp     = unpack("C{$nroffset}dummy/{$this->ItemParse}", $this->buffer);
		$prior    = $temp["prior"];
		$recno    = $temp["recno"];
		$key      = $temp["key"];
		
		if ( $this->nr > $this->nrentries )
		{
			echo " (new page: $nroffset / $prior / $recno / $key) ";
			
			if ( $prior == 0 )
				return false;
                        
			$this->gotopage( $prior );
			$this->nr = 0;
			
			return $this->Next();
		}
		else
		{
			echo " ($nroffset / $prior / $recno / $key) ";
			echo " <= $recno ";
			
			return $recno;
		}
	}

	/**
	 * @access public
	 */
	function Prev()
	{
		if ( $this->nr == 1 )
		{
			$temp     = unpack( "v{$this->nr}dummy/v1nroffset", $this->buffer );
			$nroffset = $temp["nroffset"];
			$temp     = unpack( "C{$nroffset}dummy/{$this->ItemParse}", $this->buffer );
			$prior    = $temp["prior"];
			$recno    = $temp["recno"];
			$key      = $temp["key"];
			
			if ( $prior == 0 ) 
				return false;
                        
			$this->gotopage( $prior );
			$this->nr = $this->nrentries;
			
			return $this->Prev();
		}
		else
		{
			$this->nr--;
			
			$temp     = unpack( "v{$this->nr}dummy/v1nroffset", $this->buffer );
			$nroffset = $temp["nroffset"];
			$temp     = unpack( "C{$nroffset}dummy/{$this->ItemParse}", $this->buffer );
			$recno    = $temp["recno"];
			
			return $recno;
		}
	}

	/**
	 * @access public
	 */
	function Skip( $nr )
	{
		while ( $nr != 0 )
		{
			if ( $nr > 0 )
			{
				$res = $this->Next();
				$nr--;
			}
			else
			{
				$res = $this->Prev();
				$nr++;
			}
		}
		
		return $res;
	}

	/**
	 * @access public
	 */
	function First()
	{
		$this->gotopage( $this->firstofs );
		$this->nr = 1;

		$temp     = unpack( "v{$this->nr}dummy/v1nroffset", $this->buffer );
		$nroffset = $temp["nroffset"];
		$temp     = unpack( "C{$nroffset}dummy/{$this->ItemParse}", $this->buffer );
		$tempno   = $temp["recno"];
		$recno    = false;
		
		while ( $tempno !== false )
		{
			$recno = $tempno;
			echo "Pred: $recno <br>\n";;
			$tempno = $this->Prev();
		}
		
		return $recno;
	}

	/**
	 * @access public
	 */
	function Seek( $key )
	{
		$this->gotopage( $this->firstofs );
		$this->nr = 1;
		$this->lookupkey = $key;
		$res = $this->IndexRecords();
		
		return $res;
	}

	/**
	 * @access public
	 */
	function Close()
	{
		fclose( $this->ntxfp );
	}

	/**
	 * @access public
	 */
	function SetSoftSeek( $on )
	{
		$this->softseek = $on;
	}
} // END OF NTXFile

?>
