<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>Example Serializer Options</title>

</head>

<body>

<?php

require( '../../../../../../prepend.php' );

using( 'xml.rdf.lib.RdfAPI' );
using( 'xml.rdf.lib.util.RdfMemoryModel' );


// Filename of an RDf document
$base = "example1.rdf";

// Create a new RdfMemoryModel
$model = new RdfMemoryModel();

// Load and parse document
$model->load( $base );

// Output model as HTML table
$model->writeAsHtmlTable();
echo "<P>";

// Create Serializer and serialize model to RDF with default configuration
$ser = new RDFSerializer();
$rdf =& $ser->serialize( $model );
echo "<p><textarea cols='110' rows='20'>" . $rdf . "</textarea>";

// Serialize model to RDF using attributes
$ser->configUseAttributes( true );
$rdf =& $ser->serialize( $model );
echo "<p><textarea cols='110' rows='20'>" . $rdf . "</textarea>";
$ser->configUseAttributes( false );
 
// Serialize model to RDF using entities
$ser->configUseEntities( true );
$rdf =& $ser->serialize( $model );
echo "<p><textarea cols='110' rows='30'>" . $rdf . "</textarea>";
$ser->configUseEntities( false );

?>

</body>
</html>
