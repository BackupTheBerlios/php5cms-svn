<?php

require( '../../../../prepend.php' );

using( 'html.HTMLUtil' );


$str = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>Sample Document</title>

</head>

<body>

<a href="example.HTMLUtil.php">Example Link</a>

</body>
</html>
EOF;

$res = HTMLUtil::toWML( $str );

echo $res;

?>
