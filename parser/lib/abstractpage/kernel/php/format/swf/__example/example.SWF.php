<?php

error_reporting( E_ALL );


require( '../../../../../prepend.php' );

using( 'format.swf.SWF' );


header( "Content-Type: application/x-shockwave-flash" );

$swf = new SWF;
$swf->SetSWFVersion( 5 );
$swf->SetFrameSize( 16000, 12000 );
$swf->SetFrameRate( 24.0 ); 
$swf->SetBackgroundColor( 255, 255, 255 );

// lights! camera! action!
$swf->BeginMovie();

// frame # 00000
// define some gradients
$GradientRGB        = array();
$GradientRGB[]      = array( "Ratio" =>   0, "R" => 255, "G" =>   0, "B" =>   0 );
$GradientRGB[]      = array( "Ratio" =>  64, "R" => 128, "G" => 128, "B" =>   0 );
$GradientRGB[]      = array( "Ratio" => 172, "R" =>   0, "G" => 255, "B" => 128 );
$GradientRGB[]      = array( "Ratio" => 255, "R" =>   0, "G" =>   0, "B" => 255 );

$GradientGray       = array();
$GradientGray[]     = array( "Ratio" =>   0, "R" => 255, "G" => 255, "B" => 255 );
$GradientGray[]     = array( "Ratio" => 255, "R" =>   0, "G" =>   0, "B" =>   0 );

$GradientRedAlpha   = array();
$GradientRedAlpha[] = array( "Ratio" =>   0, "R" => 255, "G" => 0, "B" => 0, "A" => 255 );
$GradientRedAlpha[] = array( "Ratio" => 255, "R" =>   0, "G" => 0, "B" => 0, "A" =>   0 );

// define shapes
$CharacterInfo  = $swf->DefineRectangleGradient( 800, 800, 4800, 4800, 300, false, true, 0, 0, 0, 0, "l", $GradientRGB, true, null );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefinePolygonGradient( 5, 8000, 6000, 5500, 300, true, true, 0, 0, 100, 50, "r", $GradientRGB, true, null );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineTriangleGradient( 2000, 2000, 10000, 8000, 12000, 5000, 300, true, true, 0, 255, 0, 140, "l", $GradientGray, true, null );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineRectangleGradient( 2000, 8000, 15000, 9000, 300, true, false, 255, 255, 0, 128, "r", $GradientRedAlpha, true, null );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineCircleGradient( 9, 12000, 4000, 3500, 300, false, false, 0, 0, 0, 0, "l", $GradientRedAlpha, true, null );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineArcClosedGradient( 9, 0, 3 * pi() / 2, 4000, 9000, 2000, 300, false, true, 0, 100, 10, 0, "r", $GradientGray, true, null );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );

$swf->EndFrame();
$swf->EndMovie();

print $swf->GetMovie();
exit;

?>
