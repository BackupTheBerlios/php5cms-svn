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


/**
 * @package html_widget
 */
 
class URHere extends PEAR
{
	/**
	 * @access public
	 */
	var $sitename = "My Site";
	
	/**
	 * @access public
	 */
	var $seperator = "&gt;"; // "&gt;" is ">", "&lt;" is "<", ":", ,"::", "|", "*""
	
	/**
	 * @access public
	 */
	var $text = "";
	
	/**
	 * @access public
	 */
	var $link = "";


	/**
	 * @access public
	 */
	function setSiteName( $sitename = "" )
	{
		$this->sitename = $sitename;
	}

	/**
	 * @access public
	 */	
	function getSiteName()
	{
		return $this->sitename;
	}
	
	/**
	 * @access public
	 */
	function setSeperator( $seperator = "&gt;" )
	{
		$this->seperator = $seperator;
	}
	
	/**
	 * @access public
	 */
	function getSeperator()
	{
		return $this->seperator;
	}

	/**
	 * @access public
	 */	
    function toText( $sent_path = "" )
    {
        if ( strlen( $sent_path ) > 0 )
            $path = explode( "/", $sent_path );
        else
            $path = explode( "/", $_SERVER[PHP_SELF] );

        $c = 1;
        while ( list( $key, $val ) = each( $path ) ) 
		{
          	if ( $c > 1 ) 
			{
                $this->text .= " " . $this->seperator . " ";
                $val = str_replace( "_", " ", $val ); // strip underscore
                $val = str_replace( "-", " ", $val ); // strip hyphen
                $this->text .= ucwords( ereg_replace( "\..*$", "", $val ) ); // strip extensions
            } 
			else 
			{
                $this->text = $this->sitename;
            }
			 
            $c++;
        } 
		
        return $this->text;
    } 

	/**
	 * @access public
	 */
    function toLink( $sent_path = "" )
    {
        if ( strlen( $sent_path ) > 0 )
            $path = explode( "/", $sent_path );
        else
            $path = explode( "/", $_SERVER[PHP_SELF] );

        $c = 1;
        while ( list( $key, $val ) = each( $path ) ) 
		{
            if ( $c > 1 ) 
			{
                $this->link .= " " . $this->seperator . " ";
                
				if ( $c < count( $path ) )
                    $link .= "$val/";
                else
                    $link .= "$val";
                
				$val = str_replace( "_", " ", $val ); // strip underscore
                $val = str_replace( "-", " ", $val ); // strip hyphen

                $this->link .= '<a href="/' . $link . '">' . ucwords( ereg_replace( "\..*$", "", $val ) ) . '</a>'; //Strip extensions
            } 
			else 
			{
                $this->link = '<a href="/">' . $this->sitename . '</a>';
            }
			 
            $c++;
        } 
		
        return $this->link;
    } 
} // END OF URHere

?>
