<?php

require( '../../../../prepend.php' );

using( 'search.MultiSearch' );


$search = new Multisearch;
$en = $search->getEngine( $engine );

$action        = $en[0];
$method        = $en[1];
$criteriafield = $en[2];
	
?>

<html>
<head>

<title>Multisearch Example</title>

</head>

<body>

<form name="theForm" action="<?php echo( $action ) ?>" method="<?php echo( $method ) ?>">
<input type="hidden" name="<?php echo( $criteriafield ) ?>" value="<?php echo( $scrit ); ?>">

<?php

$hidden = (array)$search->getHiddenFieldValues( $engine );

while ( list( $key, $val ) = each( $hidden ) )
	echo "<input type='hidden' name='$key' value='$val'>\n";

?>

</form>

<script language="JavaScript">

document.theForm.submit();

</script>

</body>
</html>
