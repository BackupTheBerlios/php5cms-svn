<html>
<head>

<title>Midi2Text</title>

</head>

<body>

<form enctype="multipart/form-data" action="mid2txt.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><!-- 1 MB -->
MIDI file (*.mid) to upload: <input type="file" name="mid_upload">
<input type="submit" value=" send ">
</form>

<?php

require( '../../../../../prepend.php' );

using( 'format.mid.Midi' );


if ( isset( $_FILES['mid_upload'] ) )
{
	$file = $_FILES['mid_upload']['tmp_name'];
	$midi = new Midi;
	$midi->importMid( $file );
	echo '<br>' . nl2br( $midi->getTxt() );
}

?>

</body>
</html>
