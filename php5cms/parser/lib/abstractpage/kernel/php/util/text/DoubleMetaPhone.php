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
 * This class implements a "sounds like" algorithm developed
 * by Lawrence Philips which he published in the June, 2000 issue
 * of C/C++ Users Journal. Double Metaphone is an improved
 * version of Philips' original Metaphone algorithm.
 *
 * @package util_text
 */
 
class DoubleMetaPhone extends PEAR
{
	/**
	 * @access public
	 */
 	var $original = "";
	
	/**
	 * @access public
	 */
	var $primary = "";
	
	/**
	 * @access public
	 */
	var $secondary = "";
	
	/**
	 * @access public
	 */
	var $length =  0;
	
	/**
	 * @access public
	 */
	var $last =  0;
	
	/**
	 * @access public
	 */
	var $current =  0;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function DoubleMetaPhone( $string )
	{
		$this->primary   = "";
		$this->secondary = "";
		$this->current   = 0;
		$this->length    = strlen( $string );
		$this->last      = $this->length - 1;
		
		$this->original  = $string . "     ";
		$this->original  = strtoupper( $this->original );

		// skip this at beginning of word
		if ( $this->_stringAt( $this->original, 0, 2, array( 'GN', 'KN', 'PN', 'WR', 'PS' ) ) )
		$this->current++;

		// Initial 'X' is pronounced 'Z' e.g. 'Xavier'
		if ( substr( $this->original, 0, 1 ) == 'X' )
		{
      		$this->primary   .= "S"; // 'Z' maps to 'S'
      		$this->secondary .= "S";
      		$this->current++;
    	}

		// main loop
		while ( strlen( $this->primary ) < 4 || strlen( $this->secondary < 4 ) )
		{
      		if ( $this->current >= $this->length )
        		break;

      		switch ( substr( $this->original, $this->current, 1 ) )
			{
				case 'A':
				
				case 'E':
				
				case 'I':
				
				case 'O':
				
				case 'U':
				
				case 'Y':
          			if ( $this->current == 0 )
					{
            			// all init vowels now map to 'A'
            			$this->primary   .= 'A';
            			$this->secondary .= 'A';
          			}
          
		  			$this->current += 1;
          			break;

        		case 'B':
          			// '-mb', e.g. "dumb", already skipped over ...
          			$this->primary   .= 'P';
          			$this->secondary .= 'P';

          			if ( substr( $this->original, $this->current + 1, 1 ) == 'B' )
            			$this->current += 2;
          			else
            			$this->current += 1;
          
		  			break;

        		case 'Ç':
          			$this->primary   .= 'S';
          			$this->secondary .= 'S';
          			$this->current += 1;
          
		  			break;

				case 'C':
          			// various gremanic
          			if ( ( $this->current > 1 ) &&
						  !$this->_isVowel( $this->original, $this->current - 2 ) && 
						   $this->_stringAt( $this->original, $this->current - 1, 3, array( "ACH" ) ) &&
						   ( ( substr( $this->original, $this->current + 2, 1 ) != 'I' ) &&
						   ( ( substr( $this->original, $this->current + 2, 1 ) != 'E' ) ||
						   $this->_stringAt( $this->original, $this->current - 2, 6, array( "BACHER", "MACHER" ) ) ) ) )
					{
						$this->primary   .= 'K';
						$this->secondary .= 'K';
						$this->current += 2;
						
						break;
					}

					// special case 'caesar'
					if ( ( $this->current == 0 ) && $this->_stringAt( $this->original, $this->current, 6, array( "CAESAR" ) ) )
					{
						$this->primary   .= 'S';
						$this->secondary .= 'S';
						$this->current += 2;
						
						break;
					}

					// italian 'chianti'
					if ( $this->_stringAt( $this->original, $this->current, 4, array( "CHIA" ) ) )
					{
						$this->primary   .= 'K';
						$this->secondary .= 'K';
						$this->current += 2;
						
						break;
					}

					if ( $this->_stringAt( $this->original, $this->current, 2, array( "CH" ) ) )
					{
						// find 'michael'
						if ( ( $this->current > 0 ) && $this->_stringAt( $this->original, $this->current, 4, array( "CHAE" ) ) )
						{
							$this->primary   .= 'K';
							$this->secondary .= 'X';
							$this->current += 2;
							
							break;
						}

						// greek roots e.g. 'chemistry', 'chorus'
            			if ( ( $this->current == 0 ) &&
							 ( $this->_stringAt( $this->original, $this->current + 1, 5, array( "HARAC", "HARIS" ) ) ||
							   $this->_stringAt( $this->original, $this->current + 1, 3, array( "HOR", "HYM", "HIA", "HEM" ) ) ) &&
				    	   	  !$this->_stringAt( $this->original, 0, 5, array( "CHORE" ) ) )
						{
							$this->primary   .= 'K';
							$this->secondary .= 'K';
							$this->current += 2;
						
							break;
						}

						// germanic, greek, or otherwise 'ch' for 'kh' sound
						if ( ( $this->_stringAt( $this->original, 0, 4, array( "VAN ", "VON " ) ) ||
							   $this->_stringAt( $this->original, 0, 3, array( "SCH" ) ) ) ||
							   // 'architect' but not 'arch', orchestra', 'orchid'
							   $this->_stringAt( $this->original, $this->current - 2, 6, array( "ORCHES", "ARCHIT", "ORCHID" ) ) ||
							   $this->_stringAt( $this->original, $this->current + 2, 1, array( "T", "S" ) ) ||
						   ( ( $this->_stringAt( $this->original, $this->current - 1, 1, array( "A", "O", "U", "E" ) ) ||
						     ( $this->current == 0 ) ) &&
							   // e.g. 'wachtler', 'weschsler', but not 'tichner'
  							   $this->_stringAt( $this->original, $this->current + 2, 1, array( "L", "R", "N", "M", "B", "H", "F", "V", "W", " " ) ) ) )
						{
							$this->primary   .= 'K';
							$this->secondary .= 'K';
						}
						else
						{
							if ( $this->current > 0 )
							{
								if ( $this->_stringAt( $this->original, 0, 2, array( "MC" ) ) )
								{
                  					// e.g. 'McHugh'
                  					$this->primary   .= 'K';
                  					$this->secondary .= 'K';
                				}
								else
								{
                  					$this->primary   .= 'X';
                  					$this->secondary .= 'K';
                				}
              				}
							else
							{
                				$this->primary   .= 'X';
                				$this->secondary .= 'X';
              				}
            			}
            
						$this->current += 2;
            			break;
          			}

	          		// e.g. 'czerny'
    	      		if ( $this->_stringAt( $this->original, $this->current, 2, array( "CZ" ) ) && !$this->_stringAt( $this->original, $this->current - 2, 4, array( "WICZ" ) ) )
					{
            			$this->primary   .= 'S';
           		 		$this->secondary .= 'X';
            			$this->current += 2;
            
						break;
          			}

	          		// e.g. 'focaccia'
    	      		if ( $this->_stringAt( $this->original, $this->current + 1, 3, array( "CIA" ) ) )
					{
            			$this->primary   .= 'X';
            			$this->secondary .= 'X';
           		 		$this->current += 3;
            
						break;
          			}

	          		// double 'C', but not McClellan'
    	      		if ( $this->_stringAt( $this->original, $this->current, 2, array( "CC" ) ) && !( ( $this->current == 1 ) && ( substr( $this->original, 0, 1 ) == 'M' ) ) )
					{
            			// 'bellocchio' but not 'bacchus'
         		   		if ( $this->_stringAt( $this->original, $this->current + 2, 1, array( "I", "E", "H" ) ) && !$this->_stringAt( $this->original, $this->current + 2, 2, array( "HU" ) ) )
						{
              				// 'accident', 'accede', 'succeed'
              				if ( ( ( $this->current == 1 ) && ( substr( $this->original, $this->current - 1, 1 ) == 'A' ) ) || $this->_stringAt( $this->original, $this->current - 1, 5, array( "UCCEE", "UCCES" ) ) )
							{
								$this->primary   .= "KS";
								$this->secondary .= "KS";
							}
							else
							{
            	    			$this->primary   .= "X";
                				$this->secondary .= "X";
              				}
              
			  				$this->current += 3;
              				break;
           		 		}
						else
						{
            	  			// Pierce's rule
              				$this->primary   .= "K";
            	  			$this->secondary .= "K";
             		 		$this->current += 2;
              			
							break;
            			}
          			}

	          		if ( $this->_stringAt( $this->original, $this->current, 2, array( "CK", "CG", "CQ" ) ) )
					{
        	    		$this->primary   .= "K";
            			$this->secondary .= "K";
            			$this->current += 2;
            
						break;
          			}

         	 		if ( $this->_stringAt( $this->original, $this->current, 2, array( "CI", "CE", "CY" ) ) )
					{
        	    		// italian vs. english
            			if ( $this->_stringAt( $this->original, $this->current, 3, array( "CIO", "CIE", "CIA" ) ) )
						{
        	      			$this->primary   .= "S";
            	  			$this->secondary .= "X";
           		 		}
						else
						{
            	  			$this->primary   .= "S";
              				$this->secondary .= "S";
            			}
            
						$this->current += 2;
            			break;
         	 		}

	          		$this->primary   .= "K";
    	      		$this->secondary .= "K";

	          		// name sent in 'mac caffrey', 'mac gregor'
    	      		if ( $this->_stringAt( $this->original, $this->current + 1, 2, array( " C", " Q", " G" ) ) )
					{
            			$this->current += 3;
          			}
					else
					{
        	    		if ( $this->_stringAt( $this->original, $this->current + 1, 1, array( "C", "K", "Q" ) ) && !$this->_stringAt( $this->original, $this->current + 1, 2, array( "CE", "CI" ) ) )
							$this->current += 2;
           		 		else
              				$this->current += 1;
          			}
          
		  			break;

				case 'D':
					if ( $this->_stringAt( $this->original, $this->current, 2, array( "DG" ) ) )
					{
            			if ( $this->_stringAt( $this->original, $this->current + 2, 1, array( "I", "E", "Y" ) ) )
						{
              				// e.g. 'edge'
              				$this->primary   .= "J";
              				$this->secondary .= "J";
              				$this->current += 3;
              				
							break;
            			}
						else
						{
              				// e.g. 'edgar'
              				$this->primary   .= "TK";
              				$this->secondary .= "TK";
              				$this->current += 2;
              
			  				break;
            			}
          			}

          			if ( $this->_stringAt( $this->original, $this->current, 2, array( "DT", "DD" ) ) )
					{
            			$this->primary   .= "T";
            			$this->secondary .= "T";
            			$this->current += 2;
            
						break;
          			}

          			$this->primary   .= "T";
          			$this->secondary .= "T";
          			$this->current += 1;
          
		  			break;

        		case 'F':
					if ( substr( $this->original, $this->current + 1, 1 ) == 'F' )
						$this->current += 2;
					else
						$this->current += 1;
					
					$this->primary   .= "F";
					$this->secondary .= "F";
					
					break;

				case 'G':
          			if ( substr( $this->original, $this->current + 1, 1 ) == 'H' )
					{
            			if ( ( $this->current > 0 ) && !$this->_isVowel( $this->original, $this->current - 1 ) )
						{
              				$this->primary   .= "K";
              				$this->secondary .= "K";
              				$this->current += 2;
              
			  				break;
            			}

            			if ( $this->current < 3 )
						{
              				// 'ghislane', 'ghiradelli'
              				if ( $this->current == 0 )
							{
                				if ( substr( $this->original, $this->current + 2, 1 ) == 'I' )
								{
                  					$this->primary   .= "J";
                  					$this->secondary .= "J";
                				}
								else
								{
                  					$this->primary   .= "K";
                  					$this->secondary .= "K";
                				}
                
								$this->current += 2;
                				break;
              				}
            			}

            			// Parker's rule (with some further refinements) - e.g. 'hugh'
            			if ( ( ( $this->current > 1 ) &&
								 $this->_stringAt( $this->original, $this->current - 2, 1, array( "B", "H", "D" ) ) ) ||
						         // e.g. 'bough'
 							 ( ( $this->current > 2 ) &&  $this->_stringAt( $this->original, $this->current - 3, 1, array( "B", "H", "D" ) ) ) ||
                				 // e.g. 'broughton'
							 ( ( $this->current > 3 ) &&
							 	 $this->_stringAt( $this->original, $this->current - 4, 1, array( "B", "H" ) ) ) )
						{
              				$this->current += 2;
              				break;
            			}
						else
						{
              				// e.g. 'laugh', 'McLaughlin', 'cough', 'gough', 'rough', 'tough'
              				if ( ( $this->current > 2 ) &&
							     ( substr( $this->original, $this->current - 1, 1 ) == 'U' ) &&
								   $this->_stringAt( $this->original, $this->current - 3, 1, array( "C", "G", "L", "R", "T" ) ) )
							{
                				$this->primary   .= "F";
                				$this->secondary .= "F";
              				}
							else if ( ( $this->current > 0 ) && substr( $this->original, $this->current - 1, 1 ) != 'I' )
							{
                				$this->primary   .= "K";
                				$this->secondary .= "K";
              				}
              
			  				$this->current += 2;
              				break;
            			}
          			}

					if ( substr( $this->original, $this->current + 1, 1 ) == 'N' )
					{
            			if ( ( $this->current == 1 ) && $this->_isVowel( $this->original, 0 ) && !$this->_slavoGermanic( $this->original ) )
						{
              				$this->primary   .= "KN";
              				$this->secondary .= "N";
            			}
						else
						{
              				// not e.g. 'cagney'
              				if ( !$this->_stringAt( $this->original, $this->current + 2, 2, array( "EY" ) ) &&
							     (substr( $this->original, $this->current + 1 ) != "Y" ) &&
								 !$this->_slavoGermanic( $this->original ) )
							{
                 				$this->primary   .= "N";
                 				$this->secondary .= "KN";
              				}
							else
							{
                 				$this->primary   .= "KN";
                 				$this->secondary .= "KN";
              				}
            			}
            			
						$this->current += 2;
            			break;
          			}

          			// 'tagliaro'
          			if ( $this->_stringAt( $this->original, $this->current + 1, 2, array( "LI" ) ) && !$this->_slavoGermanic( $this->original ) )
					{
            			$this->primary   .= "KL";
            			$this->secondary .= "L";
            			$this->current += 2;
            			
						break;
          			}

					// -ges-, -gep-, -gel- at beginning
          			if ( ( $this->current == 0 ) &&
					   ( ( substr( $this->original, $this->current + 1, 1 ) == 'Y' ) ||
					       $this->_stringAt( $this->original, $this->current + 1, 2, array( "ES", "EP", "EB", "EL", "EY", "IB", "IL", "IN", "IE", "EI", "ER" ) ) ) )
					{
            			$this->primary   .= "K";
            			$this->secondary .= "J";
            			$this->current += 2;
            
						break;
          			}

          			// -ger-, -gy-
          			if ( ( $this->_stringAt( $this->original, $this->current + 1, 2, array( "ER" ) ) ||
						 ( substr( $this->original, $this->current + 1, 1 ) == 'Y' ) ) &&
						  !$this->_stringAt( $this->original, 0, 6, array( "DANGER", "RANGER", "MANGER" ) ) &&
						  !$this->_stringAt( $this->original, $this->current -1, 1, array( "E", "I" ) ) &&
						  !$this->_stringAt( $this->original, $this->current -1, 3, array( "RGY", "OGY" ) ) )
					{
            			$this->primary   .= "K";
            			$this->secondary .= "J";
            			$this->current += 2;
            
						break;
          			}

          			// italian e.g. 'biaggi'
          			if ( $this->_stringAt( $this->original, $this->current + 1, 1, array( "E", "I", "Y" ) ) ||
						 $this->_stringAt( $this->original, $this->current - 1, 4, array( "AGGI", "OGGI" ) ) )
					{
            			// obvious germanic
            			if ( ( $this->_stringAt( $this->original, 0, 4, array( "VAN ", "VON " ) ) ||
							   $this->_stringAt( $this->original, 0, 3, array( "SCH" ) ) ) ||
							   $this->_stringAt( $this->original, $this->current + 1, 2, array( "ET" ) ) )
						{
              				$this->primary   .= "K";
              				$this->secondary .= "K";
            			}
						else
						{
							// always soft if french ending
              				if ( $this->_stringAt( $this->original, $this->current + 1, 4, array( "IER " ) ) )
							{
                				$this->primary   .= "J";
                				$this->secondary .= "J";
             	 			}
							else
							{
                				$this->primary   .= "J";
                				$this->secondary .= "K";
              				}
            			}
            
						$this->current += 2;
            			break;
          			}

          			if ( substr( $this->original, $this->current + 1, 1 ) == 'G' )
            			$this->current += 2;
          			else
            			$this->current += 1;

          			$this->primary   .= 'K';
          			$this->secondary .= 'K';
          			
					break;

        		case 'H':
					// only keep if first & before vowel or btw. 2 vowels
          			if ( ( ( $this->current == 0 ) || 
							 $this->_isVowel( $this->original, $this->current - 1 ) ) &&
							 $this->_isVowel( $this->original, $this->current + 1 ) )
					{
            			$this->primary   .= 'H';
            			$this->secondary .= 'H';
            			$this->current += 2;
          			}
					else
					{
            			$this->current += 1;
					}
          
		  			break;

        		case 'J':
          			// obvious spanish, 'jose', 'san jacinto'
          			if ( $this->_stringAt( $this->original, $this->current, 4, array( "JOSE" ) ) ||
						 $this->_stringAt( $this->original, 0, 4, array( "SAN " ) ) )
					{
            			if ( ( ( $this->current == 0 ) &&
							   ( substr( $this->original, $this->current + 4, 1 ) == ' ' ) ) ||
							     $this->_stringAt( $this->original, 0, 4, array( "SAN " ) ) )
						{
              				$this->primary   .= 'H';
              				$this->secondary .= 'H';
            			}
						else
						{
              				$this->primary   .= "J";
              				$this->secondary .= 'H';
            			}
            
						$this->current += 1;
            			break;
          			}

          			if ( ( $this->current == 0 ) &&
						  !$this->_stringAt( $this->original, $this->current, 4, array( "JOSE" ) ) )
					{
            			$this->primary   .= 'J';  // Yankelovich/Jankelowicz
            			$this->secondary .= 'A';
					}
					else
					{
            			// spanish pron. of .e.g. 'bajador'
            			if (     $this->_isVowel( $this->original, $this->current - 1 ) && 
							    !$this->_slavoGermanic( $this->original ) &&
						     ( ( substr( $this->original, $this->current + 1, 1 ) == 'A' ) || 
						       ( substr( $this->original, $this->current + 1, 1 ) == 'O' ) ) )
						{
              				$this->primary   .= "J";
              				$this->secondary .= "H";
            			}
						else
						{
              				if ( $this->current == $this->last )
							{
                				$this->primary   .= "J";
                				$this->secondary .= "";
              				}
							else
							{
                				if ( !$this->_stringAt( $this->original, $this->current + 1, 1, array( "L", "T", "K", "S", "N", "M", "B", "Z" ) ) && 
									 !$this->_stringAt( $this->original, $this->current - 1, 1, array( "S", "K", "L" ) ) )
								{
                  					$this->primary   .= "J";
                  					$this->secondary .= "J";
                				}
              				}
            			}
          			}

					if ( substr( $this->original, $this->current + 1, 1 ) == 'J' ) // it could happen
						$this->current += 2;
					else 
						$this->current += 1;
					
					break;

				case 'K':
					if ( substr( $this->original, $this->current + 1, 1 ) == 'K' )
						$this->current += 2;
					else
						$this->current += 1;
					
					$this->primary   .= "K";
					$this->secondary .= "K";
					
					break;

				case 'L':
					if ( substr( $this->original, $this->current + 1, 1 ) == 'L' )
					{
            			// spanish e.g. 'cabrillo', 'gallegos'
            			if ( ( ( $this->current == ( $this->length - 3 ) ) &&
								 $this->_stringAt( $this->original, $this->current - 1, 4, array( "ILLO", "ILLA", "ALLE" ) ) ) || 
							 ( ( $this->_stringAt( $this->original, $this->last - 1, 2, array( "AS", "OS" ) ) ||
							 	 $this->_stringAt( $this->original, $this->last, 1, array( "A", "O" ) ) ) &&
								 $this->_stringAt( $this->original, $this->current - 1, 4, array( "ALLE" ) ) ) )
						{
              				$this->primary   .= "L";
              				$this->secondary .= "";
              				$this->current += 2;
              
			  				break;
            			}
            			
						$this->current += 2;
          			}
					else
					{ 
            			$this->current += 1;
					}
          
		  			$this->primary   .= "L";
          			$this->secondary .= "L";
          			
					break;

        		case 'M':
					if (     ( $this->_stringAt( $this->original, $this->current - 1, 3, array( "UMB" ) ) &&
					 	 ( ( ( $this->current + 1 ) == $this->last ) ||
						 	   $this->_stringAt( $this->original, $this->current + 2, 2, array( "ER" ) ) ) ) ||
							   // 'dumb', 'thumb'
							 ( substr( $this->original, $this->current + 1, 1 ) == 'M' ) )
					{
						$this->current += 2;
					}
					else
					{
              			$this->current += 1;
          			}
          
		  			$this->primary   .= "M";
          			$this->secondary .= "M";
          
		  			break;

        		case 'N':
          			if ( substr( $this->original, $this->current + 1, 1 ) == 'N' ) 
						$this->current += 2;
					else
						$this->current += 1;
					
					$this->primary   .= "N";
					$this->secondary .= "N";
					
					break;

				case 'Ñ':
					$this->current += 1;
					$this->primary   .= "N";
					$this->secondary .= "N";
					
					break;

				case 'P':
					if ( substr( $this->original, $this->current + 1, 1 ) == 'H' )
					{
						$this->current += 2;
						$this->primary   .= "F";
						$this->secondary .= "F";
						
						break;
					}

					// also account for "campbell" and "raspberry"
					if ( $this->_stringAt( $this->original, $this->current + 1, 1, array( "P", "B" ) ) )
						$this->current += 2;
					else
						$this->current += 1;
					
					$this->primary   .= "P";
					$this->secondary .= "P";
					
					break;

				case 'Q':
					if ( substr( $this->original, $this->current + 1, 1 ) == 'Q' ) 
						$this->current += 2;
					else 
						$this->current += 1;
					
					$this->primary   .= "K";
					$this->secondary .= "K";
					
					break;

				case 'R':
					// french e.g. 'rogier', but exclude 'hochmeier'
					if ( ( $this->current == $this->last ) &&
						  !$this->_slavoGermanic( $this->original ) &&
						   $this->_stringAt( $this->original, $this->current - 2, 2, array( "IE" ) ) &&
						  !$this->_stringAt( $this->original, $this->current - 4, 2, array( "ME", "MA" ) ) )
					{
            			$this->primary   .= "";
            			$this->secondary .= "R";
          			}
					else
					{
            			$this->primary   .= "R";
            			$this->secondary .= "R";
          			}
          
		  			if ( substr( $this->original, $this->current + 1, 1 ) == 'R' ) 
						$this->current += 2;
					else
						$this->current += 1;
					
					break;

				case 'S':
					// special cases 'island', 'isle', 'carlisle', 'carlysle'
					if ( $this->_stringAt( $this->original, $this->current - 1, 3, array( "ISL", "YSL" ) ) )
					{
            			$this->current += 1;
            			break;
          			}

          			// special case 'sugar-'
          			if ( ( $this->current == 0 ) &&
						   $this->_stringAt( $this->original, $this->current, 5, array( "SUGAR" ) ) )
					{
						$this->primary   .= "X";
						$this->secondary .= "S";
						$this->current += 1;
						
						break;
					}

					if ( $this->_stringAt( $this->original, $this->current, 2, array( "SH" ) ) )
					{
						// germanic
						if ( $this->_stringAt( $this->original, $this->current + 1, 4, array( "HEIM", "HOEK", "HOLM", "HOLZ" ) ) )
						{
							$this->primary   .= "S";
							$this->secondary .= "S";
						}
						else
						{
							$this->primary   .= "X";
							$this->secondary .= "X";
						}
						
						$this->current += 2;
						break;
					}

					// italian & armenian 
					if ( $this->_stringAt( $this->original, $this->current, 3, array( "SIO", "SIA" ) ) ||
						 $this->_stringAt( $this->original, $this->current, 4, array( "SIAN" ) ) )
					{
            			if ( !$this->_slavoGermanic( $this->original ) )
						{
              				$this->primary   .= "S";
              				$this->secondary .= "X";
            			}
						else
						{
              				$this->primary   .= "S";
              				$this->secondary .= "S";
            			}
            
						$this->current += 3;
            			break;
          			}

          			// german & anglicisations, e.g. 'smith' match 'schmidt', 'snider' match 'schneider'
          			// also, -sz- in slavic language altho in hungarian it is pronounced 's'
          			if ( ( ( $this->current == 0 ) &&
							 $this->_stringAt( $this->original, $this->current + 1, 1, array( "M", "N", "L", "W" ) ) ) ||
							 $this->_stringAt( $this->original, $this->current + 1, 1, array( "Z" ) ) )
					{
            			$this->primary   .= "S";
            			$this->secondary .= "X";
            
						if ( $this->_stringAt( $this->original, $this->current + 1, 1, array( "Z" ) ) )
							$this->current += 2;
						else
							$this->current += 1;
						
						break;
					}

					if ( $this->_stringAt( $this->original, $this->current, 2, array( "SC" ) ) )
					{
						// Schlesinger's rule 
            			if ( substr( $this->original, $this->current + 2, 1 ) == 'H' )
						{
              				// dutch origin, e.g. 'school', 'schooner'
              				if ( $this->_stringAt( $this->original, $this->current + 3, 2, array( "OO", "ER", "EN", "UY", "ED", "EM" ) ) )
							{
                				// 'schermerhorn', 'schenker' 
                				if ( $this->_stringAt( $this->original, $this->current + 3, 2, array( "ER", "EN" ) ) )
								{
                  					$this->primary   .= "X";
                  					$this->secondary .= "SK";
                				}
								else
								{
                  					$this->primary   .= "SK";
                  					$this->secondary .= "SK";
                				}
                
								$this->current += 3;
                				break;
							}
							else
							{
                				if ( ( $this->current == 0 ) &&
									  !$this->_isVowel( $this->original, 3 ) &&
									 ( substr( $this->original, $this->current + 3, 1 ) != 'W' ) )
								{
									$this->primary   .= "X";
									$this->secondary .= "S";
								} 
								else
								{
                  					$this->primary   .= "X";
                 	 				$this->secondary .= "X";
                				}
                
								$this->current += 3;
                				break;
              				}
						}

						if ( $this->_stringAt( $this->original, $this->current + 2, 1, array( "I", "E", "Y" ) ) )
						{
                			$this->primary   .= "S";
                			$this->secondary .= "S";
                			$this->current += 3;
                
							break;
              			}

 						$this->primary   .= "SK";
						$this->secondary .= "SK";
						$this->current += 3;
						
						break;
          			}

					// french e.g. 'resnais', 'artois'
          			if ( ( $this->current == $this->last ) &&
						   $this->_stringAt( $this->original, $this->current - 2, 2, array( "AI", "OI" ) ) )
					{
            			$this->primary   .= "";
            			$this->secondary .= "S";
          			}
					else
					{
            			$this->primary   .= "S";
            			$this->secondary .= "S";
          			}

          			if ( $this->_stringAt( $this->original, $this->current + 1, 1, array( "S", "Z" ) ) )
						$this->current += 2;
					else 
						$this->current += 1;
					
					break;

				case 'T':
					if ( $this->_stringAt( $this->original, $this->current, 4, array( "TION" ) ) )
					{
            			$this->primary   .= "X";
            			$this->secondary .= "X";
            			$this->current += 3;
            
						break;
          			}

          			if ( $this->_stringAt( $this->original, $this->current, 3, array( "TIA", "TCH" ) ) )
					{
            			$this->primary   .= "X";
            			$this->secondary .= "X";
            			$this->current += 3;
            
						break;
          			}

					if ( $this->_stringAt( $this->original, $this->current, 2, array( "TH" ) ) ||
						 $this->_stringAt( $this->original, $this->current, 3, array( "TTH" ) ) )
					{
            			// special case 'thomas', 'thames' or germanic
            			if ( $this->_stringAt( $this->original, $this->current + 2, 2, array( "OM", "AM" ) ) ||
							 $this->_stringAt( $this->original, 0, 4, array( "VAN ","VON " ) ) ||
							 $this->_stringAt( $this->original, 0, 3, array( "SCH" ) ) )
						{
							$this->primary   .= "T";
							$this->secondary .= "T";
						}
						else
						{
              				$this->primary   .= "0";
              				$this->secondary .= "T";
            			}
            
						$this->current += 2;
            			break;
          			}

          			if ( $this->_stringAt( $this->original, $this->current + 1, 1, array( "T", "D" ) ) )
						$this->current += 2;
					else
						$this->current += 1;
					
					$this->primary   .= "T";
					$this->secondary .= "T";
					
					break;

				case 'V':
					if ( substr( $this->original, $this->current + 1, 1 ) == 'V' )
						$this->current += 2;
					else
						$this->current += 1;
					
					$this->primary   .= "F";
					$this->secondary .= "F";
					
					break;

				case 'W':
					// can also be in middle of word
					if ( $this->_stringAt( $this->original, $this->current, 2, array( "WR" ) ) )
					{
            			$this->primary   .= "R";
            			$this->secondary .= "R";
           	 			$this->current += 2;
            
						break;
          			}

          			if ( ( $this->current == 0 ) &&
						 ( $this->_isVowel( $this->original,  $this->current + 1 ) ||
						   $this->_stringAt( $this->original, $this->current, 2, array( "WH" ) ) ) )
					{
            			// Wasserman should match Vasserman 
            			if ( $this->_isVowel( $this->original, $this->current + 1 ) )
						{
              				$this->primary   .= "A";
              				$this->secondary .= "F";
            			}
						else
						{
              				// need Uomo to match Womo 
              				$this->primary   .= "A";
              				$this->secondary .= "A";
            			}
          			}

					// Arnow should match Arnoff
					if ( ( ( $this->current == $this->last ) &&
							 $this->_isVowel(  $this->original, $this->current - 1 ) ) ||
							 $this->_stringAt( $this->original, $this->current - 1, 5, array( "EWSKI", "EWSKY", "OWSKI", "OWSKY" ) ) ||
							 $this->_stringAt( $this->original, 0, 3, array( "SCH" ) ) )
					{
            			$this->primary   .= "";
            			$this->secondary .= "F";
            			$this->current += 1;
            
						break;
          			}

          			// polish e.g. 'filipowicz'
          			if ( $this->_stringAt( $this->original, $this->current, 4, array( "WICZ", "WITZ" ) ) )
					{
            			$this->primary   .= "TS";
            			$this->secondary .= "FX";
            			$this->current += 4;
            
						break;
          			}

					$this->current += 1;
					break;

				case 'X':
					// french e.g. breaux 
					if ( !( ( $this->current == $this->last ) &&
							( $this->_stringAt( $this->original, $this->current - 3, 3, array( "IAU", "EAU" ) ) ||
							  $this->_stringAt( $this->original, $this->current - 2, 2, array( "AU",  "OU" ) ) ) ) )
					{
            			$this->primary   .= "KS";
            			$this->secondary .= "KS";
          			}

          			if ( $this->_stringAt( $this->original, $this->current + 1, 1, array( "C", "X" ) ) )
						$this->current += 2;
          			else
            			$this->current += 1;
          			
					break;

				case 'Z':
					// chinese pinyin e.g. 'zhao' 
         	 		if ( substr( $this->original, $this->current + 1, 1 ) == "H" )
					{
            			$this->primary   .= "J";
            			$this->secondary .= "J";
            			$this->current += 2;
            
						break;
          			} 
					else if ( $this->_stringAt( $this->original, $this->current + 1, 2, array( "ZO", "ZI", "ZA" ) ) ||
						   ( $this->_slavoGermanic( $this->original ) && ( ( $this->current > 0 ) &&
						     substr( $this->original, $this->current - 1, 1 ) != 'T' ) ) )
					{
            			$this->primary   .= "S";
            			$this->secondary .= "TS";
          			}
					else
					{
            			$this->primary   .= "S";
            			$this->secondary .= "S";
          			}

          			if ( substr( $this->original, $this->current + 1, 1 ) == 'Z' )
						$this->current += 2;
					else
						$this->current += 1;
					
					break;

				default:
					$this->current += 1;
			}

			// printf( "<br>ORIGINAL:    '%s'\n", $this->original  );
			// printf( "<br>current:     '%s'\n", $this->current   );
			// printf( "<br>  PRIMARY:   '%s'\n", $this->primary   );
			// printf( "<br>  SECONDARY: '%s'\n", $this->secondary );
		}

    	$this->primary   = substr( $this->primary,   0, 4 );
    	$this->secondary = substr( $this->secondary, 0, 4 );
    
    	$result["primary"]   = $this->primary;
    	$result["secondary"] = $this->secondary;
    
    	return $result;
	}
  
  
	// private methods
  
  	/**
	 * @access private
	 */
	function _stringAt( $string, $start, $length, $list )
	{
    	if ( ( $start < 0 ) || ( $start >= strlen( $string ) ) )
			return false;

		for ( $i = 0; $i < count( $list ); $i++ )
		{
			if ( $list[$i] == substr( $string, $start, $length ) )
        		return true;
    	}
    
		return false;
  	}

  	/**
	 * @access private
	 */
  	function _isVowel( $string, $pos )
	{
    	return ereg( "[AEIOUY]", substr( $string, $pos, 1 ) );
  	}

  	/**
	 * @access private
	 */
  	function _slavoGermanic( $string )
	{
    	return ereg( "W|K|CZ|WITZ", $string );     
  	}
} // END OF DoubleMetaPhone

?>
