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
|         Roy Kaldung                                                  |
+----------------------------------------------------------------------+
*/


using( 'format.pdf.lib.PDFTagAttribute' );


/**
 * Class for emulating DomNode from the domxml extension.
 *
 * @package format_pdf_lib
 */

class PDFTagNode extends PEAR
{
    /**
     * @var array
	 * @access private
     */
    var $ar_attr;

    /**
     * @var string
	 * @access private
     */
    var $c;

    /**
     * @var string
	 * @access private
     */
    var $tagname;

	
    /**
     * Constructor
	 *
     * @param  array
	 * @access public
     */
    function PDFTagNode( $tagname, &$attr ) 
	{
        $this->tagname = $tagname;
        $this->ar_attr = array();
        
		if ( is_array( $attr ) )
		{
            foreach ( $attr as $key => $value )
                $this->ar_attr[] = new PDFTagAttribute( strtolower( $key ), $value );
        } 
		else 
		{
            print_r( $attr );
        }
    }

	
    /**
     * Retrieve the node's attributes.
	 *
     * @return array of object
	 * @access public
     */
    function attributes()
	{ 
        return $this->ar_attr; 
    }

    /**
     * Returns the content.
	 *
     * @return string
	 * @access public
     */
    function get_content()
	{
        return $this->c;
    }
    
    /**
     * Set the attribute's value.
	 *
     * @param  string 
     * @return boolean
	 * @access public
     */
    function set_content( &$c ) 
	{
        $this->c = $c;
        return true;
    }
} // END OF PDFTag

?>
