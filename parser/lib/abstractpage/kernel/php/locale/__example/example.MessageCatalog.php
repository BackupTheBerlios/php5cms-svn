<?php

require( '../../../../prepend.php' );

using( 'locale.MessageCatalog' );
using('locale.LanguageDetection');

$ld = new LanguageDetection();
printf("<pre>%s</pre>", var_export($ld, true));

print $ld->getPrimaryPrefix();

?>

<html>
<head>

<title>MSGCat Example</title>

</head>
<body>

<?php

$msg = new MessageCatalog( "en", "./" );
echo $msg->mc( "WM" );

?>

</body>
</html>
