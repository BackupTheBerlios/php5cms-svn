<?php

require( '../../../../../../prepend.php' );

using( 'peer.http.agent.AgentSpoofing' );


$as = new AgentSpoofing;

print_r( $as->getList( false, true, "en" ) );
echo( "<hr>\n" );

print_r( $as->getList( true ) );
echo( "<hr>\n" );

$as->setBrowser();
$as->setBrowserVersion();
$as->setOS();
$as->setOSVersion();
$as->setLanguage();
echo( $as->getAgentString() );
echo( "<hr>\n" );

$as->setBrowser( "IE" );
$as->setBrowserVersion( "7.02" );
$as->setOS( "Windows" );
$as->setOSVersion( "NT 5.1" );
$as->setLanguage( "fr" );
echo( $as->getAgentString( true, true ) );
echo( "<hr>\n" );

$as->setBrowser( "Opera" );
$as->setBrowserVersion( "6.04" );
$as->setOS( "Windows" );
$as->setOSVersion( "XP" );
$as->setLanguage( "de" );
echo( $as->getAgentString( true, true ) );
echo( "<hr>\n" );

?>
