<?php

require( '../../../../prepend.php' );

using( 'io.FolderStructure' );


$oDir = new FolderStructure();

// read my Directory, only htm and html files, recursive, files,
// no directories, add /my/files/ to any entry, don't add files/dirs
// containing _vti_ or vssver.scc
$oDir->read( "../../", "(gif|jpg)\$", true, true, false, "", "(_vti_)|(vssver.scc)" );

// output
$oDir->output();

// do something
reset( $oDir->aFiles );
while( list( $sKey, $aFile ) = each( $oDir->aFiles ) )
{
    $sFullname = $oDir->fullName( $aFile );
    echo "$sFullname<br>\n";
}

?>
