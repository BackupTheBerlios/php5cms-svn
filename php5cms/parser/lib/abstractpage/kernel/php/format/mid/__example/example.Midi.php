<?php

require( '../../../../../prepend.php' );

using( 'format.mid.Midi' );


$p = $_POST;

if ( isset( $_FILES['mid_upload'] ) )
{
	$tmpFile = $_FILES['mid_upload']['tmp_name'];
	
	if ( !is_dir( 'tmp' ) ) 
		mkdir( 'tmp' );
		
	srand ( (double)microtime() * 1000000 );
	$file = 'tmp/~' . rand() . '.mid';
  	copy( $tmpFile,$file ) or die( 'problems uploading file' );
	@chmod( $file, 0666 );
}
else if ( isset( $p['file'] ) ) 
{	
	$file = $p['file'];
}

?>

<html>

<title>Manipulate MIDI file</title>

</head>

<body>

<form enctype="multipart/form-data" action="example.Midi.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><!-- 1 MB -->
MIDI file (*.mid) to upload: <input type="file" name="mid_upload">
<input type="submit" value=" send ">
</form>
<hr>

<?php

if ( isset( $file ) )
{
	$engine = isset( $p['engine'] )? $p['engine'] : 'wm';
	$midi = new Midi;
	$midi->importMid( $file );
	$tc = $midi->getTrackCount();

?>

<form action="example.Midi.php" method="POST">
<input type="hidden" name="file" value="<?=isset($file)?$file:''?>">
<input type="radio" name="engine" value="bk"<?=$engine=='bk'?' checked':''?>>Beatnik
<input type="radio" name="engine" value="qt"<?=$engine=='qt'?' checked':''?>>QuickTime
<input type="radio" name="engine" value="wm"<?=$engine=='wm'?' checked':''?>>Windows Media
<input type="radio" name="engine" value=""<?=$engine==''?' checked':''?>>other (default Player)
<br><br>
<input type="checkbox" name="up"<?=isset($p['up'])?' checked':''?>>transpose up (1 octave)
<input type="checkbox" name="down"<?=isset($p['down'])?' checked':''?>>transpose down (1 octave)
<br><br>
<input type="checkbox" name="double"<?=isset($p['double'])?' checked':''?>>double tempo
<input type="checkbox" name="half"<?=isset($p['half'])?' checked':''?>>half tempo
<br><br>
<input type="checkbox" name="delete"<?=isset($p['delete'])?' checked':''?>>delete track 
<select name="delTrackNum"><?php for ($i=0;$i<$tc;$i++) echo "<option value=\"$i\"".(isset($p['delTrackNum'])&&$i==$p['delTrackNum']?' selected':'').">$i</option>\n";?></select>
<input type="checkbox" name="solo"<?=isset($p['solo'])?' checked':''?>>solo track 
<select name="soloTrackNum"><?php for ($i=0;$i<$tc;$i++) echo "<option value=\"$i\"".(isset($p['soloTrackNum'])&&$i==$p['soloTrackNum']?' selected':'').">$i</option>\n";?></select>
<br><br>
<input type="checkbox" name="insert"<?=isset($p['insert'])?' checked':''?>>insert MIDI messages (3 handclaps at start)
<br><br>
<input type="checkbox" name="show"<?=isset($p['show'])?' checked':''?>>show MIDI result as Text
<br><br>
<input type="submit" value=" PLAY! ">
</form>

<?php

	$new = 'tmp/~' . rand() . '.mid';
	
	if ( isset( $p['up'] ) )
		$midi->transpose( 12 );

	if ( isset( $p['down'] ) )
		$midi->transpose( -12 );

	if ( isset( $p['double'] ) )
		$midi->setTempo( $midi->getTempo() / 2 );

	if ( isset( $p['half'] ) )
		$midi->setTempo( $midi->getTempo() * 2 );

	if ( isset( $p['solo'] ) )
		$midi->soloTrack( $p['soloTrackNum'] );

	if ( isset( $p['delete'] ) )
		$midi->deleteTrack( $p['delTrackNum'] );

	if ( isset( $p['insert'] ) )
	{
		$midi->insertMsg( 0, "0 On ch=10 n=39 v=127" );
		$midi->insertMsg( 0, "120 On ch=10 n=39 v=127" );
		$midi->insertMsg( 0, "240 On ch=10 n=39 v=127" );
	}

	$midi->saveMidFile( $new );
	$midi->playMidFile( $new, 1, 1, 0, $engine );
	
	if ( isset( $p['show'] ) )
 		echo '<hr>' . nl2br( $midi->getTxt() );
}

?>

</body>
</html>
