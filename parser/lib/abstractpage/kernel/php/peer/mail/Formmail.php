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
 * @package peer_mail
 */
 
class Formmail extends PEAR
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Formmail()
	{
		$this->masquerade = ap_ini_get( "agent_name", "settings" );
	}
	
	
	/**
	 * Function to check the referer for security reasons.
	 *
	 * @access public
	 */
	function checkReferer( $referers )
	{
		if ( count( $referers ) )
		{
			$found   = false;
      		$temp    = explode( "/", $_SERVER["HTTP_REFERER"] );
      		$referer = $temp[2];
      
	 	 	for ( $x = 0; $x < count( $referers ); $x++ )
			{
       		  	if ( ereg( $referers[$x], $referer ) )
       		     	$found = true;
      		}
      
	 	 	if ( !$found )
				return PEAR::raiseError( "You are comming from an unauthorized domain." );
      
	  		return $found;
  		}
		else
		{
			// Not a good idea, if empty, it will allow it.
     		return true;
		}
	}

	/**
	 * Parse the form and create the content string which we will send.
	 *
	 * @access public
	 */
	function parseForm( $array )
	{
		// build reserved keyword array
   		$reserved_keys[] = "MAX_FILE_SIZE";
   		$reserved_keys[] = "required";
   		$reserved_keys[] = "redirect";
   		$reserved_keys[] = "email";
   		$reserved_keys[] = "require";
   		$reserved_keys[] = "path_to_file";
   		$reserved_keys[] = "recipient";
   		$reserved_keys[] = "subject";
   		$reserved_keys[] = "bgcolor";
   		$reserved_keys[] = "text_color";
   		$reserved_keys[] = "link_color";
   		$reserved_keys[] = "vlink_color";
   		$reserved_keys[] = "alink_color";
   		$reserved_keys[] = "title";
   		$reserved_keys[] = "missing_fields_redirect";
   		$reserved_keys[] = "env_report";
   
   		if ( count( $array ) )
		{
   	   		while ( list( $key, $val ) = each( $array ) )
			{
         		// exclude reserved keywords
         		$reserved_violation = 0;
         
		 		for ( $ri = 0; $ri < count( $reserved_keys ); $ri++ )
				{
            		if ( $key == $reserved_keys[$ri] )
               			$reserved_violation = 1;
        	 	}
         
			 	// prepare content
         		if ( $reserved_violation != 1 )
				{
            		if ( is_array( $val ) )
					{
               			for ( $z = 0; $z < count( $val ); $z++ )
                  			$content .= "$key: $val[$z]\n";
            		}
					else
					{
						$content .= "$key: $val\n";
					}
         		}
      		}
   		}

		return $content;
	}

	/**
	 * Mail the content we figure out in the following steps.
	 *
	 * @access public
	 */
	function mail( $content, $subject, $email, $recipient )
	{
		mail( $recipient, $subject, $content, "From: $email\r\nReply-To: email\r\nX-Mailer: " . $this->masquerade );
	}

	/**
	 * Take in the body building arguments and build the body tag for page display.
	 *
	 * @access public
	 */
	function buildBody( $title, $bgcolor, $text_color, $link_color, $vlink_color, $alink_color, $style_sheet )
	{
		if ( $style_sheet )
   			echo( "<LINK rel=STYLESHEET href=\"$style_sheet\" Type=\"text/css\">" );
   
   		if ( $title )
    		echo( "<title>" . $title . "</title>" );
   
   		if ( !$bgcolor )
      		$bgcolor = "#FFFFFF";
   
   		if ( !$text_color )
      		$text_color = "#000000";
   
   		if ( !$link_color )
      		$link_color = "#0000FF";
   
   		if ( !$vlink_color )
      		$vlink_color = "#FF0000";
   
   		if ( !$alink_color )
      		$alink_color = "#000088";
   
   		if ( $background )
      		$background = "background=\"$background\"";

		echo( "<body bgcolor=\"$bgcolor\" text=\"$text_color\" link=\"$link_color\" vlink=\"$vlink_color\" alink=\"$alink_color\" $background>" );
	}

	/**
	 * Error function.
	 *
	 * @access public
	 */
	function printError( $reason, $type = 0 )
	{
		$this->buildBody( $title, $bgcolor, $text_color, $link_color, $vlink_color, $alink_color, $style_sheet );
		echo( $reason );
		
		exit;
	}
} // END OF Formmail

?>
