<html>
<head>

<title>MapQuest Example</title>

</head>

<body>

1. this is where I live:<br>

<?php

require( '../../../../prepend.php' );

using( 'util.MapQuest' );


$a = new MapQuest();
$a->a_css = "ex";
$a->addQvar( "address", "2634 W. Rice St." );
$a->printA();

?>

<br><br>
1.b another way to phrase it:<br>

<?php

$a = new MapQuest();
$a->a_css = "ex";
$a->addQvar( "address", "2634 W. Rice St." );
$a->a_text = $a->mq_qstring['address='];
$a->printA();

?>

<br><br>
1.c this is the aerial view of where I live:<br>
(that's the big Metra train yard to the left)<br>

<?php

$a = new MapQuest();
$a->a_css = "ex";
$a->addQvar( "dtype", "a" ); //a = aerial, s = streetmap
$a->addQvar( "address", "2634 W. Rice St." );
$a->printA();

?>

<br>
<br>
<br>

2. this is where I used to live:<br>
(sometimes I miss the ocean)<br>

<?php

$a = new MapQuest();
$a->a_css = "ex";
$a->addQvar( "address", "136 High Dr." );
$a->addQvar( "city", "Laguna Beach" );
$a->addQvar( "state", "CA" );
$a->printA();

?>

<br><br>
2.b this is where I used to live (zoom = 0):<br>

<?php

$a = new MapQuest();
$a->a_css = "ex";
$a->zoom( 0 );
$a->addQvar( "address", "136 High Dr." );
$a->addQvar( "city", "Laguna Beach" );
$a->addQvar( "state", "CA" );
$a->printA();

?>

<br><br>
3. this is how you store a value:<br>

<?php

$a = new MapQuest();
$a->a_css = "ex";
$a->addQvar( "address","136 High Dr." );
$a->addQvar( "city","Laguna Beach" );
$a->addQvar( "state", "CA" );
$a->makeA();

$stored = $a->mq;
print $stored;

?>

<br><br>
4. or... if you just want the url (it's long):<br>

<?php

$a = new MapQuest();
$a->a_css = "ex";
$a->addQvar( "address","136 High Dr." );
$a->addQvar( "city", "Laguna Beach" );
$a->addQvar( "state", "CA" );
$a->printHREF();

?>

</body>
</html>
