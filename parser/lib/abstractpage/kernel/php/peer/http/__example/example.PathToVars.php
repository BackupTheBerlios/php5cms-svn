<?php

require( '../../../../../prepend.php' );

using( 'peer.http.PathToVars' );


$p = new PathToVars;
$p->set( "useBothMethods", 1 );

// var=>val pair to set for this lookup (if __LOOKUP__ is the value, the value will be that of the string you are looking up)
$varsToSet = array( "page" => "__LOOKUP__" );
$p->createLookup( "cal", $varsToSet );

// var => val pair to set for this lookup ( example setting a value other than __LOOKUP__ )
$varsToSet = array( "v" => "event_details" );
$p->createLookup( "event", $varsToSet );

// var => val pair to set for this lookup (shortcut: if you specify an empty string as a value it will resolve to __LOOKUP__)
$varsToSet = array( "v" => "" );
$p->createLookup( "3day", $varsToSet );

// var => val pair to set for this lookup
$varsToSet = array( "getting_tricky" => "" );
$p->createRegexLookup( "[[:alnum:]]+\.html", $varsToSet );

// var => val pair to set for this lookup (example specifying regex as lookup!)
$varsToSet = array( "calDay"=>"" );
$p->createRegexLookup( "^[[:digit:]]{4}-[[:digit:]]{2}-[[:digit:]]{2}$", $varsToSet );

if ( !$p->setVars() ) 
	die( "PathToVars didn't work. check the comments in the class file." );

// workaround for setting array elements from the path
$cal['day'][0] = $calDay;

?>
<html>
<body>
<!-- see the method _create_PTV_SELF() for explanation: -->
<a href="<?php echo $PTV_SELF; ?>/cal/3day/2002-04-29/">
click here for example 1 A</a><br>
(this URL will definitely be crawled)<br>
<a href="<?php echo $PTV_SELF; ?>/cal/3day/2002-04-29/?this=that">
click here for example 1 B</a><br>
(the same URL with a query, not sure if this is guaranteed to get crawled)<br>
<a href="<?php echo $PTV_SELF; ?>/event/2002-04-29/?this=that">
click here for example 1 C</a><br>
(this is just to point out the flexibility of the createLookup() methods)<br>
<a href="<?php echo $PTV_SELF; ?>?this=preserve_old_query_string_uris">
click here for example 2</a><br>
(you're old query string URIs will still work)<br>
<a href="<?php echo $PTV_SELF; ?>/cal/3day/2002-04-29/fakepage.html">
click here for example 3</a><br>
('something.html' can also be used for lookup)<br>
<a href="<?php echo $PTV_SELF; ?>/cal/3day/2002-04-29/foo_bar/bar_foo/fakepage.html?this=that">
click here for example 4</a><br>
(this shows how you can set variables with _var_value() method, to avoid many many lookups)<br>
<br>
(watch the browser location)<br><br>
<?

print "<b>request uri</b> = " . $HTTP_SERVER_VARS['REQUEST_URI'] . "<br><br>" . "<b>extracted path</b> = " . $p->varPath;

?>

<br><br>

<?

print "<b>this</b> = " . $this . "<br>";

?>

</body>
</html>
