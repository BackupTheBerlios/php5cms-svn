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
 * XML DBMS class for PHP.
 *
 * @package db_xml
 */
 
class XMLDatabase extends PEAR
{
	/**
	 * @access public
	 */
	function arrayDump( $XMLFile, &$dbaseCaption, &$RecordCaption, &$RecordCount, &$FieldCount, &$FieldCaptions )
	{
		$fp  = file( $XMLFile );
		$rec = implode( $fp, "" );

		$buffer = 0;
		
		$openTags  = 0;
		$closeTags = 0;
		$Tagsets   = 0;
		$tmpFields = 0;
		$cntFields = 0;
		$ptrFields = 0;
		$cntRec    = 1;

		$inReccord = false;

		for ( $i = 0; $i < strlen( $rec ); $i++ )
		{
			$extChar = substr( $rec, $i, 1 );

			switch ( $extChar )
			{
				case "<" :
					$openTags += 1;
					$startTag  = $i;

					// Actually using the stack could return the number of fields much more easily but since done earlier in a sillier method just leave it...
					// ptrSTACK == 3 means that the file pointer is currently pointing to the < of the close tag of a field as in its open tag the push adds it to 3
					// once ptrSTACK reaches 1 then a record is finished therefore reset field pointer 

					// <field>data here</field>
					
					if ( $buffer == 3 )
					{
						$FieldArray[$ptrFields] = $Tagcaption;
						$DataArray[$cntRec - 1][$ptrFields] = trim( substr( $rec, $endTag + 1, $startTag - $endTag - 1 ) );
					
						// print $endTag . "<br>";
						// print $startTag;
						// print $DataArray[$cntRec - 1][$ptrFields];
					
						$ptrFields += 1;
					}

					if ( $buffer == 1 )
						$ptrFields = 0;

					break;

				case ">" :
					$closeTags += 1;
					$Tagsets   += 1;
					$endTag     = $i;

					$start = $startTag + 1;
					$end   = $endTag - $startTag - 1;

					switch ( $Tagsets )
					{
						case 0:
							break;
			
						case 1 :
							$dbaseCaption = substr( $rec, $start, $end );
							$this->_stack( "PUSH", $dbaseCaption, $buffer );
						
							break;

						case 2 : 
							$RecordCaption = substr( $rec, $start, $end );
							$this->_stack( "PUSH", $RecordCaption, $buffer );
						
							break;
						
						default : 
							$Tagcaption = substr( $rec, $start, $end );

							$bool1      = ( trim( $Tagcaption ) == trim( $RecordCaption ) );
							$bool2      = ( trim( $Tagcaption ) == trim( $dbaseCaption ) );
							$bool3      = ( trim( $Tagcaption ) == trim( "/$RecordCaption" ) );
							$bool4      = ( trim( $Tagcaption ) == trim( "/$dbaseCaption" ) );
							$isCloseTag = ( substr( $Tagcaption, 0, 1 ) == "/" );

							if ( !( $isCloseTag ) )
								$this->_stack( "PUSH", $Tagcaption, $buffer );
						
							if ( $isCloseTag )
								$this->_stack( "POP", "", $buffer );
							
							// 1st if: if the current tag is not record or dbase open/close tags then can add to the temp number of fields
							// 2nd if: if pointer reaches a new record and the actual number of fields is still unset (ie = 0) then have it set tp the temp number of fields.
							// 3rd if: if current tag is a record tag then add to the record counter.

							if ( ( !( ( $bool1 ) || ( $bool2 ) || ( $bool3 ) || ( $bool4 ))) && ( !( $isCloseTag ) ) )
								$tmpFields += 1;
						
							if ( ( $bool1 ) && ( $cntFields == 0 ) )
								$cntFields = $tmpFields;
						
							if ( $bool1 )
								$cntRec += 1;

							break;
					}

					break;
			}
		}

		$RecordCount   = $cntRec;
		$FieldCount    = $cntFields;
		$FieldCaptions = $FieldArray;

		return $DataArray;
	}

	/**
	 * @access public
	 */
	function getData( $XMLFile, $RecNum, $FieldName )
	{
		// for this records start from 1 not 0

		$fieldexists = false;
		$XMLArray    = $this->arrayDump( $XMLFile, $DB, $REC, $cntREC, $cntFLD, $FLD );
		$RecNum     -= 1;
		
		for  ( $i = 0; $i < $cntFLD; $i++ )
		{
			if ( trim( $FieldName ) == trim( $FLD[$i] ) )
			{
				$ptr = $i;
				$fieldexists = true;
			}
		}

		// field must be there
		// record number must not exceed
		// record number must not be lesser than 1.

		if ( !( $fieldexists ) )
			return "";
	
		if ( $RecNum > $cntREC )
			return "";
	
		if ( $RecNum <= -1 )
			return "";

		return $XMLArray[$RecNum][$ptr];
	}

	/**
	 * Write the database back to the XML File with the updated array passed from 
	 * the addRecord Function.
	 *
	 * @access public
	 */
	function writeFile( $XMLFile, $RecordArray, $dbaseName, $RecordName, $FieldNameAry, $RecNum, $FldNum )
	{
		$XMLString = "<$dbaseName>" . chr(13) . chr(10) . chr(13) . chr(10);
		
		// loop to write the records and the fields.
		for ( $rec = 0; $rec < $RecNum; $rec++ )
		{
			$XMLString .= "  <$RecordName>" . chr(13) . chr(10);

			for ( $fld = 0; $fld < $FldNum; $fld++ )
				$XMLString .= "    <$FieldNameAry[$fld]>" . $RecordArray[$rec][$fld] . "</$FieldNameAry[$fld]>" . chr(13) . chr(10);

			$XMLString .= "  </$RecordName>" . chr(13) . chr(10) . chr(13) . chr(10);
		}

		$XMLString .= "</$dbaseName>";

		$fp = fopen( $XMLFile, "w" );
		fwrite( $fp, $XMLString );
		fclose( $fp );
	}

	/**
	 * @access public
	 */
	function addRecord( $XMLFile, $DataArray, $Mode, $PosToAdd )
	{
		// osToAdd: 0     -> default value and this will have the record added to the eof
		//		  : [NUM] -> indicates the record number of this new record after add thus determining its final location
	
		$XMLArray = $this->arrayDump( $XMLFile, $DB, $REC, $cntREC, $cntFLD, $FLD );
		
		if ( $PosToAdd == 0 ) 
		{
			// End of file therefore just require 1 more element to the array.
			for ( $i = 0; $i < $cntFLD; $i++ )
				$XMLArray[$cntREC][$i] = $DataArray[$i];

			$this->writeFile( $XMLFile, $XMLArray, $DB, $REC, $FLD, $cntREC + 1, $cntFLD );
			return true;
		}

		switch ( $Mode )
		{
			case "OVERWRITE" :
  				// if user propose to OVERWRITE @ pos 3 means that Record 3 would
				// be overwritten with the new record. Record 3 is Record[2][?]
				// as the array starts from 0 therefore 1 must be deducted.
				
				// Cannot exceed the last record.		
				if ( $PosToAdd > $cntREC )
					return false;
				
				$PosToAdd -= 1;

				for ( $i = 0; $i < $cntFLD; $i++ )
					$XMLArray[$PosToAdd][$i] = $DataArray[$i];

				$this->writeFile( $XMLFile, $XMLArray, $DB, $REC, $FLD, $cntREC, $cntFLD );		
				break;
			
			case "INSERT" :
  				// if user propose to INSEERT @ pos 3 means that Record 3 would
				// be pushed to record 4 and all following records would be pushed downwards 
				// with the new record at record 3. Record 3 is Record[2][?]
				//  as the array starts from 0 therefore 1 must be deducted.

				// Cannot exceed the last record.
				if ( $PosToAdd > $cntREC )
					return false;	
			
				$PosToAdd -= 1;

				for ( $i = 0; $i < $cntREC + 1; $i++ )
				{
					// Before the record where the new record is to be 
					// added copy the array as per normal
					// When reach then copy the required item and when passed
					// copy 1 item before as the pointer is at the next item
					// and since for loop conditions has accomodated for 1 
					// more would not affect.

					if ( $i <  $PosToAdd )
						$NewArray[$i] = $XMLArray[$i];
				
					if ( $i == $PosToAdd )
						$NewArray[$i] = $DataArray;
				
					if ( $i >  $PosToAdd )
						$NewArray[$i] = $XMLArray[$i - 1];
				}

				$this->writeFile( $XMLFile, $NewArray, $DB, $REC, $FLD, $cntREC + 1, $cntFLD );
				break;
		}
	}

	/**
	 * @access public
	 */
	function deleteRecord( $XMLFile, $RecNum )
	{
		$XMLArray = $this->arrayDump( $XMLFile, $DB, $REC, $cntREC, $cntFLD, $FLD );

	 	// if user propose to DELETE record 3 means that Record 4 would
		// be pushed to record 3 and all following records would be pushed upwards 
		// Record 3 is Record[2][?] as the array starts from 0 therefore 1 must be 
		// deducted.

		// Cannot exceed the last record.
		if ( $RecNum > $cntREC )
			return false;
				
		$RecNum -= 1;

		for ( $i = 0; $i < $cntREC - 1; $i++ )
		{
			// To Delete the record simply not include it.
			// There fore when record pointer is there take the next record and so on.
			if ( $i <  $RecNum )
				$NewArray[$i] = $XMLArray[$i];
		
			if ( $i >= $RecNum )
				$NewArray[$i] = $XMLArray[$i + 1];
		}
	
		$this->writeFile( $XMLFile, $NewArray, $DB, $REC, $FLD, $cntREC - 1, $cntFLD );
	}

	/**
	 * @access public
	 */
	function dbSearch( $XMLFile, $FieldName, $SearchTerm )
	{
		$XMLArray = $this->arrayDump( $XMLFile, $DB, $REC, $cntREC, $cntFLD, $FLD );

		// return X means field not found
		// return N means unable to locate search term in records
		// return integer means that search term has been found and number is record number

		$foundfield = false;

		for ( $flds = 0; $flds < $cntFLD; $flds++ )
		{
			if ( trim( $FieldName ) == trim( $FLD[$flds] ) )
			{
				$TFieldIndex = $flds;
				$foundfield  = true;
			
				break;
			}
		}

		if ( $foundfield )
		{
			$foundrecord = false;
			$SearchTerm  = trim( strtoupper( $SearchTerm ) );

			for ( $i = 0; $i < $cntREC; $i++ )
			{
				if ( trim( strtoupper( $XMLArray[$i][$TFieldIndex])) == $SearchTerm )
				{
					$TRecordIndex = $i;
					$foundrecord  = true;
					
					break;
				}
			}

			if ( $foundrecord )
				return $TRecordIndex + 1; // add 1 as only in PHP array elements start at 0 cannot have record 0
			else
				return "N";
		}
		else
		{
			return "X";
		}
	}
	
	
	// private methods

	/**
	 * @access private
	 */
	function _stack( $action, $element, &$buffer )
	{
		global $ptrSTACK;
		global $STACK;
		
		switch ( $action )
		{
			case "PUSH" :
				$ptrSTACK += 1;
				$STACK[$ptrSTACK] = $element;
			
				break;

			case "POP" :
				$ptrSTACK -= 1;
				break;
		}

		$buffer = $ptrSTACK;
	}
} // END OF XMLDatabase

?>
