<?php

require( '../../../../prepend.php' );

using( 'image.ImageMagick' );


$filename = '';

// make sure this directory is writeble by the httpd user
$targetdir = '/export/home/docuverse/htdocs/imagemagick/images';


if ( isset( $_FILES['image'] ) && $_FILES['image']['size'] > 0 ) 
{
	$imObj = new ImageMagick( $_FILES['image'] );
	$imObj->setTargetDir( $targetdir );

	if ( isset( $_POST['resize'] ) && $_POST['resize'] == 1 )
		$imObj->resize( $_POST['resize_x'], $_POST['resize_y'], $_POST['resize_method'] );

	if ( isset( $_POST['crop'] ) && $_POST['crop'] == 1 )
		$imObj->crop( $_POST['crop_x'], $_POST['crop_y'], $_POST['crop_method'] );

	if ( isset( $_POST['square'] ) && $_POST['square'] == 1 )
		$imObj->square( $_POST['square_method'] );

	if ( isset( $_POST['monochrome'] ) && $_POST['monochrome'] == 1 )
		$imObj->monochrome();

	if ( isset( $_POST['negative'] ) && $_POST['negative'] == 1 )
		$imObj->negative();

	if ( isset( $_POST['flip'] ) && $_POST['flip'] == 1 )
		$imObj->flip( $_POST['flip_method'] );

	if ( isset( $_POST['dither'] ) && $_POST['dither'] == 1 )
		$imObj->dither();

	if ( isset( $_POST['frame'] ) && $_POST['frame'] == 1 )
		$imObj->frame( $_POST['frame_width'], $_POST['frame_color'] );

	if ( isset( $_POST['rotate'] ) && $_POST['rotate'] == 1 )
		$imObj->rotate( $_POST['rotate_angle'], $_POST['rotate_bgcolor'] );

	if ( isset( $_POST['blur'] ) && $_POST['blur'] == 1 )
		$imObj->blur( $_POST['blur_radius'], $_POST['blur_sigma'] );

	$imObj->convert( $_POST['convert'] );
	$filename = $imObj->save();
	$imObj->cleanUp();
}

?>
<html>
<head>

<title>ImageMagick Example</title>

</head>

<body>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="0" cellpadding="0"><tr><td bgcolor="#FFFFFF">
<table cellspacing="1" cellpadding="2">
<tr>
	<td><b>Select image:</b></td>
	<td colspan="4"><input type="file" name="image"></td>
</tr>
<tr>
	<td><b>Verbose mode:</b></td>
	<td colspan="4"><input type="checkbox" name="verbose" value="1"<?=!isset($_POST['monochrome'])?' CHECKED':($_POST['monochrome']==1?' CHECKED':'')?>></td>
</tr>
<tr>
	<td colspan="5"><BR>Select options:</td>
</tr>
<tr>
	<td><b>Resize:</b></td>
	<td><input type="checkbox" name="resize" value="1"<?=isset($_POST['resize'])&&$_POST['resize']==1?' CHECKED':''?>></td>
	<td>X-size: <input type="text" size="4" name="resize_x" value="<?=isset($_POST['resize_x'])?$_POST['resize_x']:'640'?>"></td>
	<td>Y-size: <input type="text" size="4" name="resize_y" value="<?=isset($_POST['resize_y'])?$_POST['resize_y']:'480'?>"></td>
	<td>Method: <select name="resize_method"><option<?=isset($_POST['resize_method'])&&$_POST['resize_method']=='keep_aspect'?' selected':''?>>keep_aspect</option><option<?=isset($_POST['resize_method'])&&$_POST['resize_method']=='fit'?' selected':''?>>fit</option></td>
</tr>
<tr>
	<td><b>Crop:</b></td>
	<td><input type="checkbox" name="crop" value="1"<?=isset($_POST['crop'])&&$_POST['crop']==1?' CHECKED':''?>></td>
	<td>X-size: <input type="text" size="4" name="crop_x" value="<?=isset($_POST['crop_x'])?$_POST['crop_x']:'200'?>"></td>
	<td>Y-size: <input type="text" size="4" name="crop_y" value="<?=isset($_POST['crop_y'])?$_POST['crop_y']:'300'?>"></td>
	<td>Method: <select name="crop_method"><option<?=isset($_POST['crop_method'])&&$_POST['crop_method']=='center'?' selected':''?>>center</option><option<?=isset($_POST['crop_method'])&&$_POST['crop_method']=='left'?' selected':''?>>left</option><option<?=isset($_POST['crop_method'])&&$_POST['crop_method']=='right'?' selected':''?>>right</option></select></td>
</tr>
<tr>
	<td><b>Square:</b></td>
	<td><input type="checkbox" name="square" value="1"<?=isset($_POST['square'])&&$_POST['square']==1?' CHECKED':''?>></td>
	<td colspan="3">Method: <select name="square_method"><option<?=isset($_POST['square_method'])&&$_POST['square_method']=='center'?' selected':''?>>center</option><option<?=isset($_POST['square_method'])&&$_POST['square_method']=='left'?' selected':''?>>left</option><option<?=isset($_POST['square_method'])&&$_POST['square_method']=='right'?' selected':''?>>right</option></select></td>
</tr>
<tr>
	<td><b>Monochrome:</b></td>
	<td colspan="4"><input type="checkbox" name="monochrome" value="1"<?=isset($_POST['monochrome'])&&$_POST['monochrome']==1?' CHECKED':''?>></td>
</tr>
<tr>
	<td><b>Flip:</b></td>
	<td><input type="checkbox" name="flip" value="1"<?=isset($_POST['flip'])&&$_POST['flip']==1?' CHECKED':''?>></td>
	<td colspan="3">Method: <select name="flip_method"><option<?=isset($_POST['flip_method'])&&$_POST['flip_method']=='horizontal'?' selected':''?>>horizontal</option><option<?=isset($_POST['flip_method'])&&$_POST['flip_method']=='vertical'?' selected':''?>>vertical</option></td>
</tr>
<tr>
	<td><b>Dither:</b></td>
	<td colspan="4"><input type="checkbox" name="dither" value="1"<?=isset($_POST['dither'])&&$_POST['dither']==1?' CHECKED':''?>></td>
</tr>
<tr>
	<td><b>Frame:</b></td>
	<td><input type="checkbox" name="frame" value="1"<?=isset($_POST['frame'])&&$_POST['frame']==1?' CHECKED':''?>></td>
	<td>Framewidth: <input type="text" size="3" name="frame_width" value="<?=isset($_POST['frame_width'])?$_POST['frame_width']:'5'?>"></td>
	<td colspan="2">Framecolor: #<input type="text" size="7" maxlength="6" name="frame_color" value="<?=isset($_POST['frame_color'])?$_POST['frame_color']:'FF0000'?>"></td>
</tr>
<tr>
	<td><b>Rotate:</b></td>
	<td><input type="checkbox" name="rotate" value="1"<?=isset($_POST['rotate'])&&$_POST['rotate']==1?' CHECKED':''?>></td>
	<td>Angle: <input type="text" size="3" name="rotate_angle" value="<?=isset($_POST['rotate_angle'])?$_POST['rotate_angle']:'30'?>"></td>
	<td colspan="2">Background color: #<input type="text" size="7" maxlength="6" name="rotate_bgcolor" value="<?=isset($_POST['rotate_bgcolor'])?$_POST['rotate_bgcolor']:'FFA200'?>"></td>
</tr>
<tr>
	<td><b>Blur:</b></td>
	<td><input type="checkbox" name="blur" value="1"<?=isset($_POST['blur'])&&$_POST['blur']==1?' CHECKED':''?>></td>
	<td>Radius: <input type="text" size="3" name="blur_radius" value="<?=isset($_POST['blur_radius'])?$_POST['blur_radius']:'5'?>"></td>
	<td colspan="2">Sigma: <input type="text" size="3" name="blur_sigma" value="<?=isset($_POST['blur_sigma'])?$_POST['blur_sigma']:'2'?>"></td>
</tr>
<tr>
	<td><b>Convert to:</b></td>
	<td colspan="4"><select name="convert"><option<?=isset($_POST['convert'])&&$_POST['convert']=='jpg'?' selected':''?>>jpg</option><option<?=isset($_POST['convert'])&&$_POST['convert']=='gif'?' selected':''?>>gif</option><option<?=isset($_POST['convert'])&&$_POST['convert']=='png'?' selected':''?>>png</option></td>
</tr>
<tr>
	<td colspan="5">&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="4"><input type="submit" value="convert"></td>
</tr>
</table>
</td></tr>
</table>
</form>

<?php

if ( isset( $filename ) && $filename != '' ) 
{
?>
<br><br><img src="images/<?=$filename?>" border="0">
<?php
}
?>

</body>
</html>
