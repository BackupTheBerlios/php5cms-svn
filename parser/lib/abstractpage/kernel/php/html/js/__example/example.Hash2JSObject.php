<?php

require( '../../../../../prepend.php' );

using( 'html.js.Hash2JSObject' );


$obj = new Hash2JSObject( "myobject" );

$arr = array( "prop" => "test", "y" => 200, "z" => 300 );
$obj->add( $arr );

?>

<html>
<head>

<title>Hash2JSObject Example</title>

</head>

<body>

<pre>

<?php

echo $obj->getJSObject();

?>

</pre>

</body>
</html>
