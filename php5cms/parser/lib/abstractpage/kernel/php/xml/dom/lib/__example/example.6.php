<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );
using( 'xml.dom.lib.html.*' );


$doc = new HTMLDocument;
    
// creating the Root Element
$html = $doc->setDocumentElement( new HTMLHtmlElement );
$head = $html->appendChild( new HTMLHeadElement );
$title = $head->appendChild( new HTMLTitleElement("Document Title") );
    
// creating the Body
$body = $html->appendChild( new HTMLBodyElement );
$paragraph = $body->appendChild( new HTMLParagraphElement( "Content of the paragraph" ) );
    
$form = $body->appendChild( new HTMLFormElement  );
$form->setName( "form_name" );
$form->setMethod( "POST" );
$form->setAction( "formhandler.php" );

$input = $form->appendChild( new HTMLInputElement  );
$input->setName( "input_element" );
$input->setType( "text" );
$input->setValue( "Value of the Text" );

$button= $form->appendChild( new HTMLInputElement  );
$button->setName( "Submit" );
$button->setType( "submit" );
$button->setValue( "Submit" );

$doc->printHTML();

?>
