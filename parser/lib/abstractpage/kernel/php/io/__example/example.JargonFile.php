<html>
<head>

<title>JargonFile Example</title>

</head>

<body>

<pre>

<?php

set_time_limit( 300 ); // uh-huuh!


require( '../../../../prepend.php' );

using( 'io.JargonFile' );


$j = new JargonFile( $strict );
$j ->out( "out" );

?>

</pre>

</body>
</html>

