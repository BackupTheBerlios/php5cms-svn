<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>HTML2WML Example</title>

</head>

<body>

<?php

require( '../../../../prepend.php' );

using( 'wml.WMLUtil' );


$str = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>Sample Document</title>

</head>

<body>

<a href="example.HTMLUtil.wml.php">Example Link</a>

</body>
</html>
EOF;

$res = WMLUtil::toWML( $str );

?>

<pre>

<?php echo $res; ?>

</pre>

</body>
</html>
