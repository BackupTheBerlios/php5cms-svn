<html>
<head>

<title>PortList Example</title>

</head>

<body>

<?php

require( '../../../../prepend.php' );

using( 'peer.PortList' );


$pl = new PortList();
echo $pl->debugDump();

?> 

</body>
</html>
