<?php

ob_start();

require( '../../../../../prepend.php' );

using( 'peer.http.GZipEncode' );

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>GZipped Example</title>

</head>

<body>

Some content here!

</body>
</html>

<?php

new GZipEncode();

?>
