<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


class MyTemplate extends Template
{
    function MyTemplate( $what )
	{
		Template::Template( $what );
    }
	
    
    function dotag_readfile( $tag, $str )
	{
		$obj = & $this->topobject();
		
		if ( $obj == null )
	    	$val = $this->lookup( $tag->args['filename'] );
		else
	    	$val = $this->getItem( $obj, $tag->args['filename'] );

		$output = '';
	
		if ( file_exists( $val ) ) 
		{
	    	$handle = fopen( $val, "r" );
	    	$output = fread( $handle, filesize( $val ) );
	    	
			fclose( $handle );
		}
		
		return $output;
	}
}


$tpl= "
<tpl:readfile filename='myfile'>...</tpl:readfile> 
";

$template = new MyTemplate( $tpl );
$template->set( 'myfile','data/extending.data' );
$page = $template->parse();

print( $page );

?>
