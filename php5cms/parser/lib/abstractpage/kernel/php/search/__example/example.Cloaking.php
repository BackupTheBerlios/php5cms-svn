<?php

require( '../../../../prepend.php' );

using( 'search.Cloaking' );


$cl = new Cloaking();
$cl->_runCheck();
echo "<pre>\n";
echo $cl->infoString( "\n" );
echo "</pre>\n";
echo "<hr>\n";

// Infoseek - agent
$cl = new Cloaking();
$cl->_agent = "Infoseek Sidewinder";
$cl->_runCheck();
echo "<pre>\n";
echo $cl->infoString( "\n" );
echo "</pre>\n";
echo "<hr>\n";

// Excite - wildcarded ip
$cl = new Cloaking();
$cl->_ip = "199.172.149.2";
$cl->_runCheck();
echo "<pre>\n";
echo $cl->infoString( "\n" );
echo "</pre>\n";
echo "<hr>\n";

// Excite - wildcarded host
$cl = new Cloaking();
$cl->_host = "something.sjc.lycos.com";
$cl->_runCheck();
echo "<pre>\n";
echo $cl->infoString( "\n" );
echo "</pre>\n";
echo "<hr>\n";

// Inktomi - fragment
$cl = new Cloaking();
$cl->_agent = "Greatwhitecrawl/1.0";
$cl->_runCheck();
echo "<pre>\n";
echo $cl->infoString( "\n" );
echo "</pre>\n";
echo "<hr>\n";

// Altavista - ip (assumed), then host 
$cl = new Cloaking();
$cl->_ip   = "209.73.164.4";
$cl->_host = "whatever.av.pa-x.dec.com";
$cl->_runCheck();
echo "<pre>\n";
echo $cl->infoString( "\n" );
echo "</pre>\n";
echo "<hr>\n";
 
?>
 