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

 
using( 'util.text.encoding.data.lib.Encode_quotedprintable' );
using( 'util.text.encoding.data.lib.Encode_base64' );


/**
 * Internet address.
 *
 * @link    http://www.cs.tut.fi/~jkorpela/rfc/822addr.html
 * @see     rfc://2822
 * @see     rfc://2822#3.4.1
 * @package peer_mail
 */

class MailAddress extends PEAR
{
	/**
	 * @access public
	 */
    var $personal = '';
	
	/**
	 * @access public
	 */
    var $localpart = '';
	
	/**
	 * @access public
	 */
    var $domain = '';
      
	  
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed mail
     * @param   string personal default ''
     */
    function MailAddress( $mail, $personal = '' ) 
	{
      	list( $this->localpart, $this->domain ) = ( is_array( $mail )? $mail : explode( '@', $mail ) );
      	$this->personal = $personal;
    }

    
    /**
     * Create an MailAddress object from a string.
     *
     * Recognizes:
     * <pre>
     *   Markus Nix <mnix@docuverse.de>
     *   mnix@docuverse.de (Markus Nix)
     *   "Markus Nix" <mnix@docuverse.de>
     *   mnix@docuverse.de
     *   <mnix@docuverse.de>
     *   =?iso-8859-1?Q?Markus_Nix?= <mnix@docuverse.de>
     * </pre>
     *
     * @static
     * @access  public
     * @param   string str
     * @return  &MailAddress address object
     * @throws  Error
     */
    function &fromString( $str ) 
	{
      	static $matches = array(
        	'/^=\?([^\?])+\?([QB])\?([^\?]+)\?= <([^ @]+@[0-9a-z.-]+)>$/i' => 3,
        	'/^<?([^ @]+@[0-9a-z.-]+)>?$/i'                                => 0,
        	'/^([^<]+) <([^ @]+@[0-9a-z.-]+)>$/i'                          => 2,
        	'/^"([^"]+)" <([^ @]+@[0-9a-z.-]+)>$/i'                        => 1,
        	'/^([^ @]+@[0-9a-z.-]+) \(([^\)]+)\)$/i'                       => 1,
      	);
      
      	$str = trim( chop( $str ) );
      
	  	foreach ( $matches as $match => $def ) 
		{
        	if ( !preg_match( $match, $str, $_ ) ) 
				continue;
        
        	switch ( $def ) 
			{
          		case 0: 
					$mail = $_[1]; 
					$personal = ''; 
					break;
          		
				case 1: 
					$mail = $_[1]; 
					$personal = $_[2]; 
					break;
          		
				case 2: 
					$mail = $_[2]; 
					$personal = $_[1]; 
					break;
          		
				case 3: 
					$mail = $_[4]; 
					
					switch ( strtoupper( $_[2] ) ) 
					{
						case 'Q': 
							$personal = Encode_quotedprintable::decode( $_[3] ); 
								break;
            	
						case 'B': 
							$personal = Encode_base64::decode( $_[3] ); 
							break;
          			} 
          
		  			break;
        	}
        
        	break;
      	}
      
      	if ( !isset( $mail ) )
			return PEAR::raiseError( 'String "' . $str . '" could not be parsed.' );
      
      	return new MailAddress( $mail, $personal );
    }
    
    /**
     * Create string representation.
     *
     * Return values:
     * <pre>
     * - personal specified: =?iso-8859-1?Q?Markus_Nix?= <mnix@docuverse.de>
     * - Empty personal:     <test@docuverse.com>  
     * </pre>
     *
     * @access  public
     * @param   string charset default 'iso-8859-1'
     * @return  string
     */
    function toString( $charset = 'iso-8859-1' ) 
	{
      	return ( empty( $this->personal )? '' : Encode_quotedprintable::encode( $this->personal, $charset ) . ' ' ) . '<' . $this->localpart . '@' . $this->domain . '>';
	}
} // END OF MailAddress

?>
