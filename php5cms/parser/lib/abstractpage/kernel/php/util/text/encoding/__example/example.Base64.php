<html>
<body>

<pre>

<?php

require( '../../../../../../prepend.php' );

using( 'util.text.encoding.Base64' );


$decoder = new Base64();

if ( $decoder->EncodeFile( 'example.zip', 'foo.encoded' ) )
{
	echo 'Encoded file - Woot' . "\n";

	if ( $decoder->DecodeFile( 'foo.encoded', 'foo.decoded' ) )
		echo 'Decoded file - Woot' . "\n";
}

?>

</pre>

</body>
</html>
