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
 * @package html
 */
 
class HTMLSAX extends PEAR
{
	/**
	 * @abstract
	 */
	function startCallback( $tag, $dontknow = "", $dontknow2 = 0 )
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @abstract
	 */
	function endCallback( $tag = "" )
	{
		return PEAR::raiseError( "Abstract method." );
	}

	/**
	 * @abstract
	 */	
	function startCommentCallback()
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @abstract
	 */
	function commentCallback( $comment = "" )
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @abstract
	 */
	function endCommentCallback()
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @abstract
	 */
	function textStartCallback()
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @abstract
	 */
	function textCallback( $text = "" )
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @abstract
	 */
	function textEndCallback()
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @abstract
	 */
	function declCallback( $tag, $dontknow = "", $dontknow2 = 0 )
	{
		return PEAR::raiseError( "Abstract method." );
	}
	

	/**
	 * @access public
	 * @param  string  $html
	 */
	function parse( $html )
	{
		$html2 = $html;
		$tmp   = $html2;
		$c     = 0;
	
		while ( ( $tmp[$c] || $c < strlen( $tmp ) ) )
		{
			if ( $tmp[$c] == '<' || $istag )
			{
				$istag = 0;
			
				if ( $tmp[$c++] == '!' )
				{
					// comment
					if ( $tmp[$c++] == '-' || $tmp[$c + 1] == '-' )
					{
						$c += 2;
						// trim( $tmp ) ??
					
						while ( $tmp[$c] == ' ' )
							$c++;
					
						$c--;
					
						$this->startCommentCallback();
					
						// find the end of the comment
						$t = $c;
					
						while ( ( $tmp[$t] || $t < strlen( $tmp ) ) && !( $tmp[$t] == '-' && $tmp[$t++] == '-' && $tmp[$t+2] == '>' ) )
							$t++;
					
						if ( ( $tmp[$t] || $t < strlen( $tmp ) ) )
						{
							while ( $tmp[$t] == ' ' )
								$t--;
						
							$comment = substr( $tmp, $c, $t - $c );
						
							while ( $tmp[$t] == ' ' )
								$t++;
						
							$t+=2;
							$c = $t;
						}
					
						$this->commentCallback( $comment );
						$this->endCommentCallback();
					
						$c++;
					}
					else
					{
						$c--;
					}
				}
				else if ( $tmp[$c] == '/' || $tmp[$c-1] == '/' )
				{
					if ( $tmp[$c] == '/' )
						$c++;
				
					$t=$c;
				
					while ( ( $tmp[$t] || $t < strlen( $tmp ) ) && $tmp[$t] != '>' )
						$t++;
				
					$tag = substr( $tmp, $c, $t - $c );
					$this->endCallback( $tag );
					$t++;
					$c = $t;
					continue;
				}
				else
				{
					// starttag here
					// maybe inseperate function because of above
					if ( $tmp[$c-1] != '<' )
						$c--;
				
					// comment function from above in seperate function call.
					if ( $tmp[$c] == '!' && $tmp[$c++] == '-' )
						comment();
			
					$t = $c;
					$q = $c; // $q belongs to $tag[$q]
					$tagstart = $c;
					$tag = substr( $tmp, $c );

					while ( $tmp[$t] != '>' && $tmp[$t] != ' ' )
						$t++;
				
					if ( $tmp[$t] == '>' )
					{
						$tag = substr( $tmp, $c, $t - $c );
					
						if ( $tag[0] == '!' )
						{
							$tag = substr( $tmp, $c + 1, $t - ( $c + 1 ) );
							$tagstart = $c+1;
							$this->declCallback( $tag, "", 0 );
						}
						else
						{
							$this->startCallback( $tag, "", 0 );
						}
					
						// TEST was c = t
						$c = $t + 1;
						
						continue;
					}
					else if ( $tmp[$c] == ' ' )
					{
						while ( $tmp[$c] == ' ' )
							$c++;
					}
					else
					{
						if ( $tmp[$q] == '!' )
						{
							$q++;
							$tag = substr( $tmp, $q, $q - $c );
							$this->declCallback( $tag, "", 0 );
						}
						else
						{
							$this->declCallback( $tag, "", 0 );
						}
						
						break;
					}
				
					unset( $args );
					$numargs = 0;
				
					while ( ( $tmp[$c] || $c < strlen( $tmp ) ) )
					{
						$istrue   = 0;
						$tagended = 0;
					
						while ( $tmp[$c] == ' ' )
							$c++;
					
						if ( !$tmp[$c-1] == ' ' )
							$c--;
					
						// $arg = start of argument
						$arg = $c;
							
						// $q is used to find end of argument
						if ( $tmp[$arg] == '"' || $tmp[$arg] == '\'' )
						{
							$c++;
							$arg = $c;
						
							while ( ( $tmp[$c] || $c < strlen( $tmp ) ) && !( $tmp[$c] == '"' && $tmp[$c-1] != '\\' && $tmp[$c] != '\'' ) )
								$c++;

							if ( $tmp[$c] != '>' )
								continue;
						
							if ( $tmp[$c+1] == '>' )
								$c++;
						
							break;
						}
					
						$val = "";
					
						while ( $tmp[$c] != '=' && $tmp[$c] != ' ' && $tmp[$c] != '>' )
							$c++;
					
						if ( $tmp[$c] != ' ' && $tmp[$c] != '>' )
							$istrue = 1;
					
						if ( $tmp[$c] == '>' )
							$tagended = 1;
					
						$q = $c;
						$c++;
					
						if ( $istrue )
						{
							if ( $tmp[$c] != '\'' && $tmp[$c] != '"' )
							{
								while ( $tmp[$c] != ' ' && $tmp[$c] != '>' )
									$c++;
							
								if ( $tmp[$c] == '>' )
								{
									$val = substr( $tmp, $q, $c - $q );
								}
								else
								{
									$c++;
									$val = substr( $tmp, $c, $c - $q );
							
									continue;
								}
							}
							else
							{
								$c++;
								while ( $tmp[$c] && ( $tmp[$c] != '\'' || ($tmp[$c] == '\'' && $tmp[$c-1] == '\\' ) ) && ( $tmp[$c] != '"' || ( $tmp[$c] == '"' && $tmp[$c-1] == '\\' ) ) )
									$c++;
							
								if ( $tmp[$c] == '>' )
								{
									$val = substr( $tmp, $q, $c - $q );
									$c++;
									// add args
									
									break;
								}
								else if ( $tmp[$c+1] == '>' )
								{
									$val = substr( $tmp, $q, $c - $q );
									$c++;
									// add args
									
									break;
								}
								else
								{
									$val = substr( $tmp, $q, $c - $q );
									$c += 2;
									// add args
								}
							}
						}
						else
						{
							// add args
							if ( !$tagended )
								continue;
						
							$tagended = 0;
							$c--;
							break;
						}
					}
				
					// is q allowed here?
					$q = 0;
				
					if ( $tag[$q] == '!' )
					{
						$q++;
						// FIXME
						$tag = substr( $tag, $q );
					
						// FIX THIS TOO
						$this->declCallback( $tag, "", 0 );
					}
					else
					{
						// and this
						$this->startCallback( $tag, "", 0 );
					}
					
					// clear arg list;
					$c++;
					continue;
				}
			}
			else
			{
				// check for newline char						
				if ( $tmp[$c] == '\n' )
				{
					$c++;
					continue;
				}
			
				$text = $tmp;
				$q = $c;
			
				if ( $text[$q] == '!' )
				{
					$q--;
				
					if ( $text[$q-1] == '<' )
					{
						$q--;
						continue;
					}
				}
			
				while ( $tmp[$c] == ' ' && $tmp[$c] != '<' && ( $tmp[$c] || $c < strlen( $tmp ) ) )
					$c++;
			
				if ( $tmp[$c] == '<' && $tmp[$c+1] )
					continue;
				else if ( !($tmp[$c] || $c < strlen( $tmp ) ) )
					break;
			
				// text start
				$this->textStartCallback();
			
				for (;;)
				{
					while ( ( $tmp[$c] || $c < strlen( $tmp ) ) && $tmp[$c] != '<' )
						$c++;
				
					if ( $tmp[$c] == '<' )
					{
						if ( $tmp[$c+1] == ' ' )
						{
							$c++;
							continue;
						}
						else $istag = 1;
					}
					
					break;
				}
				
				$text = substr( $tmp, $q, $c - $q );
				
				// text callback
				$this->textCallback( $text );
			
				// text end
				$this->textEndCallback();
				$c++;
				
				continue;
			}
		}
		
		return;
	}
	
	/**
	 * Debug print function cause tags aren't that easy to output.
	 *
	 * @access public
	 */
	function dp( $txt, $var )
	{
		$trans   = get_html_translation_table( HTML_ENTITIES );
		$encoded = strtr( $var, $trans );
		
		echo( sprintf( "%s is now: %s", $txt, $encoded ) );
	}
} // END OF HTMLSAX

?>
