<?php
require( '../../../../prepend.php' );

using( 'search.SearchCriteriaParser' );

$scp = new SearchCriteriaParser;
$searchcriteria = "chef and (2nd or Second) and not (wellington or south island)";
$tokens = $scp->gettokens( $searchcriteria );

for ( $i = 0; $i < sizeof( $tokens ); $i++ )
  	echo "<br>" . $tokens[$i];

$complieswithrules = $scp->checkwithrules( $tokens );

if ( $complieswithrules == true )
		echo "<br>SUCCESS: complies with the rules";
else
		echo "<br>ERROR: does not comply with the rules";

?>
