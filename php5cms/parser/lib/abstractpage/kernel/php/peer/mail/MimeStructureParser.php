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


using( 'peer.mail.imap.ImapMessageStructure' );
using( 'peer.mail.imap.ImapMessageStructureProperty' );


/**
 * This class was ported from squirlemail.
 *
 * @package peer_mail
 */
 
class MimeStructureParser extends PEAR
{
	/**
	 * @access public
	 */
	function match_parenthesis( $pos, $structure )
	{
		$char = substr( $structure, $pos, 1 );
	
		// ignore all extra characters
		// If inside of a string, skip string -- Boundary IDs and other
		// things can have ) in them.
		while ( $pos < strlen( $structure ) )
		{
			$pos++;
			$char = substr( $structure, $pos, 1 );

			if ( $char == ")" )
			{
				return $pos;
			}
			else if ( $char == '"' )
			{
				$pos ++;
				
				while ( substr( $structure, $pos, 1 ) != '"' && $pos < strlen( $structure ) )
					$pos ++;
			}
			else if ( $char == "(" )
			{
				$pos = $this->match_parenthesis ($pos, $structure);
			}
		}
	}

	/**
	 * @access public
	 */
	function parse_structure( $structure, $ent_id )
	{
		$msg = new ImapMessageStructure();
		
		if ( substr( $structure, 0, 1 ) == "(" )
		{
			$ent_id = $this->new_element_level( $ent_id );
			$start  = $end = -1;
			
			do
			{	
				$start   = $end+1;
				$end     = $this->match_parenthesis( $start, $structure );	
				$element = substr( $structure, $start + 1, ( $end - $start ) - 1 );
				$ent_id  = $this->increment_id( $ent_id );
				$newmsg  = $this->parse_structure( $element, $ent_id );
				
				if ( $ent_id == 1 )
					$msg = $newmsg;
				else
					$msg->addEntity( $newmsg );
			} while ( substr( $structure, $end + 1, 1 ) == "(" );
		}
		else
		{
			// parse the elements		
			$msg = $this->get_element( &$structure, $msg, $ent_id );
		}

		return $msg;
	}

	/**
	 * Increments the element ID.  An element id can look like any of
	 * the following:  1, 1.2, 4.3.2.4.1, etc.  This function increments
	 * the last number of the element id, changing 1.2 to 1.3.
	 *
	 * @access public
	 */
	function increment_id( $id )
	{
		if ( strpos( $id, "." ) )
		{
			$first = substr( $id, 0, strrpos( $id, "." ) );
			$last  = substr( $id, strrpos( $id, "." ) + 1 );
			$last++;
			$new   = $first . "." .$last;
		}
		else
		{    
			$new = $id + 1;
		}
	
		return $new;
	}
	
	/**
	 * See comment for increment_id().
	 * This adds another level on to the entity_id changing 1.3 to 1.3.0
	 * NOTE:  1.3.0 is not a valid element ID.  It MUST be incremented 
	 *		  before it can be used.  I left it this way so as not to have
	 *		  to make a special case if it is the first entity_id.  It
	 *		  always increments it, and that works fine.
	 *
	 * @access public
	 */
	function new_element_level( $id )
	{
		if ( !$id )
			$id = 0;
		else
			$id = $id . ".0";
		
		return $id;	
	}

	/**
	 * @access public
	 */
	function get_element( &$structure, $msg, $ent_id )
	{
		$elem_num = 1;
		$msg->id = $ent_id;
			
		while ( strlen( $structure ) > 0 )
		{
			$structure = trim($structure);
			$char = substr($structure, 0, 1);
	
			if ( strtolower( substr( $structure, 0, 3 ) ) == "nil" )
			{
				$text = "";
				$structure = substr( $structure, 3 );
			}
			else if ( $char == "\"" ) 
			{
				// loop through until we find the matching quote, and return that as a string
				$pos = 1;
				$char = substr($structure, $pos, 1);
				
				while ( $char != "\"" && $pos < strlen( $structure ) )
				{
					$text .= $char;
					$pos++;
					$char = substr( $structure, $pos, 1 );
				}	
				
				$structure = substr( $structure, strlen( $text ) + 2 );
			}
			else if ( $char == "(" )
			{
				// comment me
				$end = $this->match_parenthesis( 0, $structure );
				$sub = substr( $structure, 1, $end - 1 );
				$this->get_props( $properties, $sub );
				$structure = substr( $structure, strlen( $sub ) + 2 );
			}
			else
			{
				// loop through until we find a space or an end parenthesis
				$pos = 0;
				$char = substr($structure, $pos, 1);
				
				while ( $char != " " && $char != ")" && $pos < strlen( $structure ) )
				{
					$text .= $char;
					$pos++;
					$char = substr( $structure, $pos, 1 );
				}
				
				$structure = substr( $structure, strlen( $text ) );
			}
	
			// This is where all the text parts get put into the header
			switch ( $elem_num )
			{
				case 1 : 
					$msg->type = strtolower( $text );		
					break;
					
				case 2 : 
					$msg->subtype = strtolower( $text );		
					break;
					
				case 5 :
					$msg->description = $text;	
					break;
				
				case 6 :
					$msg->encoding = strtolower( $text );		
					break;
				
				case 7 :
					$msg->num_bytes = $text;				
					break;
				
				default :
					if ( $msg->type == "text" && $elem_num == 8 )
					{
						// This is a plain text message, so lets get the number of lines
						// that it contains.
						$msg->num_lines = $text;
					}
					else if ( $msg->type == "message" && $msg->subtype == "rfc822" && $elem_num == 8 )
					{
						// This is an encapsulated message, so lets start all over again and 
						// parse this message adding it on to the existing one.
						$structure = trim( $structure );

						if ( substr( $structure, 0, 1 ) == "(" )
						{
							$e = $this->match_parenthesis( 0, $structure );
							$structure = substr( $structure, 0, $e );
							$structure = substr( $structure, 1 );

							// Orig line (broken) - jeo --
							// $m = $this->parse_structure($structure, $msg->entity_id);
							$m = $this->parse_structure( $structure, $msg->id );
								
							// the following conditional is there to correct a bug that wasn't
							// incrementing the entity IDs correctly because of the special case
							// that message/rfc822 is. This fixes it fine.
							if ( substr( $structure, 1, 1 ) != "(" ) 
								$m->id = $this->increment_id( $this->new_element_level( $ent_id ) );
									
							// Now we'll go through and reformat the results.
							if ( $m->parts )
							{
								for ( $i = 0; $i < count( $m->parts ); $i++ )
									$msg->addEntity( $m->parts[$i] );
							}
							else
							{
								$msg->addEntity( $m );
							}
	
							$structure = ""; 
						}
					}
					
					break;
			}
			
			$elem_num++;
			$text = "";
		}
		
		// loop through the additional properties and put those in the various headers
		if ( $msg->type != "message" )
		{
			for ( $i = 0; $i < count( $properties ); $i++ )
				$msg->parameters[( count( $msg->parameters ) )] = $properties[$i];
		}
	
		return $msg;
	}

	/**
	 * This gets properties in a nested parenthesisized list.  For example,
	 * this would get passed something like:  ("attachment" ("filename" "luke.tar.gz"))
	 * This returns an array called $props with all paired up properties.
	 * It ignores the "attachment" for now, maybe that should change later 
	 * down the road.
	 *
	 * @access public
	 */
	function get_props( &$props, $structure )
	{
		while ( strlen( $structure ) > 0 )
		{
			$structure = trim( $structure );
			$char = substr( $structure, 0, 1 );
	
			if ( $char == "\"" )
			{
				$pos  = 1;
				$char = substr( $structure, $pos, 1 );
				
				while ( $char != "\"" && $pos < strlen( $structure ) )
				{
					$tmp .= $char;
					$pos++;
					$char = substr( $structure, $pos, 1 );
				}	
				
				$structure = trim( substr( $structure, strlen( $tmp ) + 2 ) );
				$char = substr( $structure, 0, 1 );
	
				if ( $char == "\"" )
				{
					$pos  = 1;
					$char = substr( $structure, $pos, 1 );
					
					while ( $char != "\"" && $pos < strlen( $structure ) )
					{
						$value .= $char;
						$pos++;
						$char = substr( $structure, $pos, 1 );
					}	
					
					$structure = trim( substr( $structure, strlen( $tmp ) + 2 ) );						
					$k = count( $props );
					$props[$k] = new ImapMessageStructureProperty( strtolower( $tmp ), $value );
				}
				else if ( $char == "(" )
				{
					$end = $this->match_parenthesis( 0, $structure );
					$sub = substr( $structure, 1, $end - 1 );
					$this->get_props( $props, $sub );
					$structure = substr( $structure, strlen( $sub ) + 2 );
				}
				
				return $props;
			}
			else if ( $char == "(" )
			{
				$end = $this->match_parenthesis( 0, $structure );
				$sub = substr( $structure, 1, $end - 1 );
				$this->get_props( $props, $sub );
				$structure = substr( $structure, strlen( $sub ) + 2 );
				
				return $props;
			}
			else
			{
				return $props;
			}
		}
	}
} // END OF MimeStructureParser

?>
