<html>
<head>

<title>IMStatus Example</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body>

<form action="" method="get" name="IMStatus">
Medium :
<select name="medium" size="1">
	<option value="aim">AIM</option>
    <option value="icq">ICQ</option>
    <option value="jabber">Jabber</option> 
    <option value="msn">MSN</option>
    <option value="yahoo">Yahoo</option>
</select>

<br />

Account 

<input name="account" type="text" value="<?php if (isset ($_GET["account"])) { print ($_GET["account"]); }?>">
<input name="submit" type="submit" value="Query">
</form>
<?php 

require( '../../../../../prepend.php' );

using( 'peer.im.IMStatus' );

if ( isset( $_GET["account"] ) && isset( $_GET["medium"] ) )
{
	$imstatus = new IMStatus( $_GET["account"], $_GET["medium"] );
	$status   = $imstatus->test();
	
	if ( !PEAR::isError( $status ) )
	{
		switch ( $status )
		{ 
			case IM_ONLINE: 
				print( ucfirst( $_GET["medium"] ) . " " . $_GET["account"] . " is online!" );
				break; 
				
			case IM_OFFLINE: 
				print( ucfirst( $_GET["medium"] ) . " " . $_GET["account"] . " is offline!" );
				break;
				
			case IM_UNKNOWN: 
				print( ucfirst( $_GET["medium"] ) . " " . $_GET["account"] . " is in an unknown status!" );
				break; 
		} 		
	}
	else
	{
		print( "An error occurred during IMStatus query: <br />" );
		print( "Error: " . $status->getMessage() );
	}
}

?>

</body>
</html>
