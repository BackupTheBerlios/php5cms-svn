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
|Authors: Carlo Zottmann <carlo@g-blog.net>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'peer.im.jabber.Jabber' );


/**
 * @package peer_im_jabber
 */
 
class JabberXML extends Jabber
{
	/**
	 * @access public
	 */
	var $nodes;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function JabberXML()
	{
		$this->Jabber();
		$nodes = array();
	}


	/**
	 * @access public
	 */
	function addPacketDetails( $string, $value = null )
	{
		if ( preg_match( "/\(([0-9]*)\)$/i", $string ) )
			$string .= "/[\"#\"]";

		$temp = @explode( "/", $string );

		for ( $a = 0; $a < count( $temp ); $a++ )
		{
			$temp[$a] = preg_replace( "/^[@]{1}([a-z0-9_]*)$/i",       "[\"@\"][\"\\1\"]", $temp[$a] );
			$temp[$a] = preg_replace( "/^([a-z0-9_]*)\(([0-9]*)\)$/i", "[\"\\1\"][\\2]",   $temp[$a] );
			$temp[$a] = preg_replace( "/^([a-z0-9_]*)$/i",             "[\"\\1\"]",        $temp[$a] );
		}

		$node = implode( "", $temp );

		// Yeahyeahyeah, I know it's ugly... get over it. ;)
		echo "\$this->nodes$node = \"" . htmlspecialchars($value) . "\";<br/>";
		eval( "\$this->nodes$node = \"" . htmlspecialchars($value) . "\";" );
	}

	/**
	 * @access public
	 */
	function buildPacket( $array = null )
	{
		if ( !$array )
			$array = $this->nodes;

		if ( is_array( $array ) )
		{
			array_multisort( $array, SORT_ASC, SORT_STRING );

			foreach ( $array as $key => $value )
			{
				if ( is_array( $value ) && $key == "@" )
				{
					foreach ( $value as $subkey => $subvalue )
					{
						$subvalue  = htmlspecialchars( $subvalue );
						$text     .= " $subkey='$subvalue'";
					}

					$text .= ">\n";
				}
				else if ( $key == "#" )
				{
					$text .= htmlspecialchars( $value );
				}
				else if ( is_array( $value ) )
				{
					for ( $a = 0; $a < count( $value ); $a++ )
					{
						$text .= "<$key";

						if ( !$this->_preg_grep_keys( "/^@/", $value[$a] ) )
							$text .= ">";

						$text .= $this->buildPacket( $value[$a] );
						$text .= "</$key>\n";
					}
				}
				else
				{
					$value  = htmlspecialchars( $value );
					$text  .= "<$key>$value</$key>\n";
				}
			}

			return $text;
		}
	}


	// private methods

	/**
	 * @access private
	 */	
	function _preg_grep_keys( $pattern, $array )
	{
		while ( list( $key, $val ) = each( $array ) )
		{
			if ( preg_match( $pattern, $key ) )
				$newarray[$key] = $val;
		}
		
		return ( is_array( $newarray ) )? $newarray : false;
	}
} // END OF JabberXML

?>
