<?php 

require( '../../../../../prepend.php' );

using( 'peer.js.JSRemoteScripting' );


$rs = new JSRemoteScripting;
$rs->dispatch( "test,envVar" );

function test( $str1, $str2 )
{
	global $rs;
	
	// 2 vars coming in, return array
	return $rs->arrayToString( array( strtolower( $str2 ), strtoupper( $str1 ), "javascript" ), "~" );
}

function envVar( $varname )
{
	$varname = strtoupper( $varname );
	eval( "global \$$varname;" );
	return $$varname;
}

?>
