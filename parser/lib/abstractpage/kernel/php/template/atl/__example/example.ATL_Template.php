<?php

require( '../../../../../prepend.php' );

using( 'template.atl.ATL_Template' );


error_reporting( E_ALL );

// create a new template object 
$template = new ATL_Template( 
	"example.htm", 
	"./tpl/",
	AP_ROOT_PATH . ap_ini_get( "path_cache", "path" )
);


// the Person class 
class Person 
{ 
    var $name; 
    var $phone;
	
	
    function Person( $name, $phone ) 
    { 
        $this->name  = $name; 
        $this->phone = $phone; 
    } 
};

// let's create an array of objects for test purpose 
$result = array(); 
$result[] = new Person( "foo", "01-344-121-021" ); 
$result[] = new Person( "bar", "05-999-165-541" ); 
$result[] = new Person( "baz", "01-389-321-024" ); 
$result[] = new Person( "buz", "05-321-378-654" );



// put some data into the template context
$template->set( "title",  "the title value" );
$template->set( "result", $result );
$template->set( "result2", $result );
$template->set('page/last_modified', date('d.m.Y', filemtime('./tpl/example.htm')));

// execute template
$res = $template->execute();

// result may be an error
if ( PEAR::isError( $res ) )
    die( "Error: " . $res->toString() );
else
    echo $res;

?>
