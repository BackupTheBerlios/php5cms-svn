<?php

require( '../../../../../prepend.php' );

using( 'db.xml.XMLDatabase' );
extract($_REQUEST);

$db = new XMLDatabase;

if ( !isset( $SearchTerm ) )
	$SearchTerm = "SEARCH-TERM-NOT-SET";

if ( !isset( $NoCharDisp ) )
	$NoCharDisp = 15;

if ( !isset( $SearchMode ) )
	$SearchMode = 0; // 0: word by word (delimited by space) | 1: whole phrase

if ( !isset( $WrdXplicit ) )
	$WrdXplicit = 1; // 0: "basic" would return for "basica" | 1: basic is iif basic


// This function serves to tell if a character is a alphabet, number or neither.
function notAlphaNumeric( $charDataIn )
{
	if ( ( ( ord( $charDataIn ) >= 97 ) && ( ord( $charDataIn ) <= 122 ) ) || ( ( ord( $charDataIn ) >= 48 ) && ( ord( $charDataIn ) <= 57 ) ) )
		return false;
	else
		return true;
}

// Check if a certain instance of a string exists in another bigger string.
function TermExists( $TString, $Term, &$Pos )
{
	$RetVal = false;

	if ( trim( strval( strpos( $TString, $Term ) ) ) != "" )
	{
		$RetVal = true;
		$Pos    = strpos( $TString, $Term );
	}

	return $RetVal;
}

function removeHTMLTags( $strDataIn )
{
	$Temp = "";

	for ( $i = 0; $i < strlen( $strDataIn ); $i++ )
	{
		if ( substr( $strDataIn, $i, 1 ) == "<" )
			$Add = false;

		if ( $Add )
			$Temp .= substr( $strDataIn, $i, 1 );

		if ( substr( $strDataIn, $i, 1 ) == ">" )
		$Add = true;
	}

	return $Temp;
}


$SearchDB    = "db.xml";
$SearchData  = $db->arrayDump( $SearchDB, $DB, $REC, $cntREC, $cntFLD, $FLD );
$SearchTerm  = strtolower( trim( $SearchTerm ) );
$SearchMatch = false;
$NumMatches  = 0;

printf(
	"<pre>%s</pre>", 
	'SearchData: ' . var_export($SearchData, true) .
	'DB:         ' . var_export($DB, true) .
	'REC:        ' . var_export($REC, true) .
	'cntREC:     ' . var_export($cntREC, true) .
	'cntFld:     ' . var_export($cntFLD, true) .
	'FLD:        ' . var_export($FLD, true)
);


if ( $SearchMode == 0 )
	$TermArray = explode( " ", $SearchTerm );

if ( $SearchMode == 1 )
	$TermArray[0] = $SearchTerm;

print "<b>Search Results for: ";

for ( $i = 0; $i < sizeof( $TermArray ); $i++ )
	print "|&nbsp;\"" . $TermArray[$i] . "\"&nbsp;";

print "|</b><br><br>";

for ( $i = 0; $i < $cntREC; $i++ )
{
	$TFile   = $SearchData[$i][1];
	$fp      = file( $TFile );
	$strFile = implode( $fp, "" );
	$strFile = removeHTMLTags( $strFile );
	$bakFile = $strFile;
	$strFile = strtolower( $strFile );
	$Prev    = -1;

	for ( $j = 0; $j < sizeof( $TermArray ); $j++ )
	{
		if ( TermExists( $strFile, $TermArray[$j], $Pos ) )
		{
			/* this condition checks that sandwiching the search term is 2 spaces and no
			 * other characters makeing an explicit search
 			 * --------------------------------------------------------------------------
			 * However a little switch is made and that is the $WrdXplicit var if this
			 * variable is given a preset value of 0 then the search can then be optioned
			 * not to be explicit meaning "basic" could return "basica".
			 *
			 * SLIGHT CHANGE: The 2 characters sandwiching the search term must not be
			 * alpha numeric characters. ie they can be punctuations.
			 *
			 * YET ANOTHER CHANGE: Here is another note the search initially changes
			 * all the case to lower to facilitate the operations but had not made it
			 * such that the state is just what it is in the document. creating a
			 * backup var for $strFile - $bakFile the operation has been perfectly
			 * handled.
			 */

			if ( ( ( notAlphaNumeric( substr( $strFile, $Pos - 1, 1 ) ) ) && ( notAlphaNumeric( substr( $strFile, $Pos + strlen( $TermArray[$j] ), 1 ) ) ) ) || ( $WrdXplicit == 0 ) )
			{
				$SearchMatch = true;

				// if the previous successful result return came from the same page
				// there is no need to print the header another time
				if ( $Prev != $i )
				{
					print "<b>[<a href=" . $SearchData[$i][0] . "><font color=#000000>" . $SearchData[$i][2] . "</font></a>]</b><br>";
					$NumMatches += 1;
				}

				$LEADING_CHARS_START  = $Pos - $NoCharDisp;
				$LEADING_CHARS_LEN    = $NoCharDisp;
				$TRAILING_CHARS_START = $Pos + strlen( $TermArray[$j] );
				$TRAILING_CHARS_LEN   = $NoCharDisp;

				print "<font color=#008000><b>" . $TermArray[$j] . ": </b></font>";
	
				// if the user set the number of leading characters to show to a number
				// such that the backward tracing of characters go before 0 it would have
				// an unsightly ouput in php... therefore create the limit here
				if ( $Pos < $NoCharDisp )
				{
					$LEADING_CHARS_START = 0;
					$LEADING_CHARS_LEN   = $Pos;
				}
				else
				{
					print ".....";
				}

				print substr( $bakFile, $LEADING_CHARS_START, $LEADING_CHARS_LEN ) . "<font color=#800000><b>" . substr( $bakFile, $Pos, strlen( $TermArray[$j] ) ) . "</b></font>" . substr( $bakFile, $TRAILING_CHARS_START, $TRAILING_CHARS_LEN );

				if ( !( $Pos + strlen( $TermArray[$j] ) + $NoCharDisp > strlen( $strFile ) ) )
					print ".....";

				print "<br>";
				$Prev = $i;
			}
		}
	}

	// if a record was found previously then print the line breaker
	if ( $Prev != -1 )
		print "<br>";
}

if ( !( $SearchMatch ) )
	print "<b>No Matches found.</b><br>";
	
print "<b>Return: $NumMatches Matches</b>";

?>