<pre>

<?php 

require( '../../../../../prepend.php' );

using( 'org.samba.SambaConfig' );


$smbconf = new SambaConfig;
print_r( $smbconf->parse( "smb.conf" ) );
print( "\n\n\n" );
print( $smbconf->recreate() );

?>

</pre>
