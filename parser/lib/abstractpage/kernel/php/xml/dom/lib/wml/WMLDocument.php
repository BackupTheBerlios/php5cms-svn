<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.dom.lib.Document' );
using( 'xml.dom.lib.wml.WMLWmlElement' );


/**
 * The Document-Class. This is the base class for all WML documents.
 *
 * The WMLDocument classes simply implements a different constructor!
 * If the DOM/XML package provides more functionality concerning document
 * types, this will be implementet here.
 *
 * @package xml_dom_lib_wml
 */
 
class WMLDocument extends Document
{
	/**
	 * Constructor
	 *
	 * This function has to create a skeleton for the document. If no filename
	 * is given, the create() function is called to create an empty document.
	 * If the filename is provided, the document is created from the file.
	 *
	 * @param	string		$file	optional
	 * @access 	public
	 */
	function WMLDocument( $file = "" )
	{
		$this->Document();
		
		$this->doctype = "WML";
		
		if ( $file == "" )
		{
			$this->create( "wml" );
			$this->setDocumentElement( new WMLWmlElement );
		}
		else
		{
			$this->createFromFile( $file );
		}
	}
	
	/**
	 * printWML creates the Document and prints it to the browser.
	 *
	 * @access public
	 */
	function printWML()
	{
		$root = $this->getDocumentElement();
		echo( $root->toString() );
	}
} // END OF WMLDocument

?>
