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


using( 'util.validation.lib.Validation' );
using( 'io.FileUtil' );


/**
 * This class is used for sending emails. These emails can be
 * Plain Text, HTML, or Both. Other uses include file
 * Attachments and email Templates (from a file).
 *
 * @package peer_mail
 */
 
class Email extends PEAR
{
	/**
	 * array of To addresses
	 * @access public
	 */
	var $mailTo = "";
	
	/**
	 * copied recipients 
	 * @access public
	 */
    var $mailCC = "";
	
	/**
	 * hidden recipients 
	 * @access public
	 */
    var $mailBCC = "";

	/**
	 * from address 
	 * @access public
	 */
    var $mailFrom = "";
	
	/**
	 * email subject 
	 * @access public
	 */
    var $mailSubject = "";
	
	/**
	 * plain text message 
	 * @access public
	 */
    var $mailText = "";
	
	/**
	 * html message 
	 * @access public
	 */
    var $mailHTML = "";
	
	/**
	 * array of attachments
	 * @access public
	 */
    var $mailAttachments = "";
	
	
	/**
	 * Sets the email To address.
	 *
	 * @param  string  $inAddress  (separate multiple values with comma)
	 * @access public
	 */
	function setTo( $inAddress )
	{ 
        // split addresses at commas 
        $addressArray = explode( ",", $inAddress ); 

        // loop through each address and exit on error 
        for ( $i = 0; $i < count( $addressArray ); $i++ )
		{ 
            if ( Validation::is_email( $addressArray[$i] ) == false )
				return false; 
        }
		
        // all values are OK so implode array into string 
        $this->mailTo = implode( $addressArray, "," ); 
        return true; 
    }

	/**
	 * Sets the email cc address.
	 *
	 * @param  string  $inAddress  (separate multiple values with comma)
	 * @access public
	 */
	function setCC( $inAddress )
	{ 
        // split addresses at commas 
        $addressArray = explode( ",", $inAddress ); 
        
		// loop through each address and exit on error 
        for ( $i = 0; $i < count( $addressArray ); $i++ )
		{ 
            if ( Validation::is_email( $addressArray[$i] ) == false )
				return false; 
        }
		
        // all values are OK so implode array into string 
        $this->mailCC = implode( $addressArray, "," ); 
        return true; 
    }
	
	/**
	 * Sets the email bcc address.
	 *
	 * @param  string  $inAddress  (separate multiple values with comma)
	 * @access public
	 */
	function setBCC( $inAddress )
	{ 
        // split addresses at commas 
        $addressArray = explode( ",", $inAddress ); 
        
		// loop through each address and exit on error 
        for ( $i = 0; $i < count( $addressArray ); $i++ )
		{ 
            if ( Validation::is_email( $addressArray[$i] ) == false )
				return false; 
        }
		
        // all values are OK so implode array into string 
        $this->mailBCC = implode( $addressArray, "," ); 
        return true; 
    }

	/**
	 * Sets the email FROM address.
	 *
	 * @param  string  $inAddress  (takes single email address)
	 * @access public
	 */
	function setFrom( $inAddress )
	{ 
		if ( Validation::is_email( $inAddress ) )
		{ 
            $this->mailFrom = $inAddress; 
            return true; 
        }
		
        return false; 
    } 

	/**
	 * Sets the email subject.
	 *
	 * @param  string  $inSubject
	 * @access public
	 */
	function setSubject( $inSubject )
	{ 
        if ( strlen( trim( $inSubject ) ) > 0 )
		{ 
            $this->mailSubject = ereg_replace( "\n", "", $inSubject ); 
            return true; 
        }
		
        return false; 
    } 

	/**
	 * Sets the email text.
	 *
	 * @param  string  $inText
	 * @access public
	 */
	function setText( $inText )
	{ 
        if ( strlen( trim( $inText ) ) > 0 )
		{ 
            $this->mailText = $inText; 
            return true; 
        }
		 
        return false; 
    }

	/**
	 * Sets the email HMTL.
	 *
	 * @param  string  $inHTML
	 * @access public
	 */
	function setHTML( $inHTML )
	{ 
        if ( strlen( trim( $inHTML ) ) > 0 )
		{ 
            $this->mailHTML = $inHTML; 
            return true; 
        }
		 
        return false; 
    }
	
	/**
	 * Stores the Attachment string.
	 *
	 * @param  string  $inAttachments as string with directory included (separate multiple values with comma) 
	 * @access public
	 */
	function setAttachments( $inAttachments )
	{ 
        if ( strlen( trim( $inAttachments ) ) > 0 )
		{ 
            $this->mailAttachments = $inAttachments; 
            return true; 
        }
		         
        return false; 
    }
	
	/**
	 * Reads in a template file and replaces hash values.
	 *
	 * @param  string  $inFileLocation  as string with relative directory 
	 * @param  array   $inHash          Hash with populated values
	 * @param  string  $inFormat        either "text" or "html"
	 * @access public
	 */
	function loadTemplate( $inFileLocation, $inHash, $inFormat )
	{ 
        /* 
        template files have lines such as: 
            Dear ~!UserName~, 
            Your address is ~!UserAddress~ 
        */
		
        // specify template delimeters 
        $templateDelim     = "~"; 
        $templateNameStart = "!"; 
        
		// set out string 
        $templateLineOut = ""; 
        
		// open template file 
        if ( $templateFile = fopen( $inFileLocation, "r" ) )
		{ 
            // loop through file, line by line 
            while ( !feof( $templateFile ) )
			{ 
                // get 1000 chars or (line break internal to fgets) 
                $templateLine = fgets( $templateFile, 1000 );
				 
                // split line into array of hashNames and regular sentences 
                $templateLineArray = explode( $templateDelim, $templateLine ); 
                
				// loop through array  
                for ( $i = 0; $i < count( $templateLineArray ); $i++ )
				{ 
                    // look for $templateNameStart at position 0 
                    if ( strcspn( $templateLineArray[$i], $templateNameStart ) == 0 )
					{ 
                        // get hashName after $templateNameStart 
                        $hashName = substr( $templateLineArray[$i], 1 );
						
                        // replace hashName with acual value in $inHash 
                        // (string) casts all values as "strings" 
                        $templateLineArray[$i] = ereg_replace( $hashName, (string)$inHash[$hashName], $hashName ); 
                    } 
                }
				 
                // output array as string and add to out string 
                $templateLineOut .= implode( $templateLineArray, "" );         
            }
			
            // close file         
            fclose( $templateFile );
			 
            // set Mail body to proper format 
            if ( strtoupper( $inFormat ) == "TEXT" )
				return ( $this->setText( $templateLineOut ) ); 
            else if ( strtoupper( $inFormat ) == "HTML" )
				return ( $this->setHTML( $templateLineOut ) ); 
        }
		
        return false; 
    } 

	/**
	 * Returns a random boundary.
	 *
	 * @param  int     $offset  offset as integer - used for multiple calls
	 * @access public
	 */
	function getRandomBoundary( $offset = 0 )
	{ 
        // seed random number generator 
        srand( time() + $offset );
		 
        // return md5 32 bits plus 4 dashes to make 38 chars 
        return ( "----" . ( md5( rand() ) ) ); 
    }
	
	/**
	 * Returns a formated header for text.
	 *
	 * @access public
	 */
	function formatTextHeader()
	{ 
        $outTextHeader  = ""; 
        $outTextHeader .= "Content-Type: text/plain; charset=us-ascii\n"; 
        $outTextHeader .= "Content-Transfer-Encoding: 7bit\n\n"; 
        $outTextHeader .= $this->mailText . "\n"; 
        
		return $outTextHeader; 
    } 

	/**
	 * Returns a formatted header for HTML.
	 *
	 * @access public
	 */
	function formatHTMLHeader()
	{ 
        $outHTMLHeader  = ""; 
        $outHTMLHeader .= "Content-Type: text/html; charset=us-ascii\n"; 
        $outHTMLHeader .= "Content-Transfer-Encoding: 7bit\n\n"; 
        $outHTMLHeader .= $this->mailHTML . "\n"; 
        
		return $outHTMLHeader; 
    } 
 
	/**
	 * Returns a formated header for an attachment.
	 *
	 * @param  string  $inFileLocation as string with relative directory
	 * @access public
	 */
	function formatAttachmentHeader( $inFileLocation )
	{ 
        $outAttachmentHeader = ""; 
        
		// get content type based on file extension
		$fileExt     = FileUtil::getFileExtension( $inFileLocation );
        $contentType = FileUtil::getMimeType( $fileExt );
		
        // if content type is TEXT the standard 7bit encoding 
        if ( ereg( "text", $contentType ) )
		{ 
            // format header 
            $outAttachmentHeader .= "Content-Type: " . $contentType . ";\n"; 
            $outAttachmentHeader .= ' name="' . basename( $inFileLocation ) . '"' . "\n"; 
            $outAttachmentHeader .= "Content-Transfer-Encoding: 7bit\n"; 
            $outAttachmentHeader .= "Content-Disposition: attachment;\n"; // other: inline 
            $outAttachmentHeader .= ' filename="' . basename( $inFileLocation ) . '"' . "\n\n"; 
            $textFile = fopen( $inFileLocation, "r" ); 
            
			// loop through file, line by line 
            while ( !feof( $textFile ) )
			{ 
                // get 1000 chars or (line break internal to fgets) 
                $outAttachmentHeader .= fgets( $textFile, 1000 ); 
            }
			
            // close file         
            fclose( $textFile ); 
            $outAttachmentHeader .= "\n"; 
        }
        // NON-TEXT use 64-bit encoding 
        else
		{ 
            // format header 
            $outAttachmentHeader .= "Content-Type: " . $contentType . ";\n"; 
            $outAttachmentHeader .= ' name="' . basename( $inFileLocation ) . '"' . "\n"; 
            $outAttachmentHeader .= "Content-Transfer-Encoding: base64\n"; 
            $outAttachmentHeader .= "Content-Disposition: attachment;\n"; // other: inline 
            $outAttachmentHeader .= ' filename="' . basename( $inFileLocation ) . '"' . "\n\n"; 
            
			// call uuencode - output is returned to the return array 
            exec( "uuencode -m $inFileLocation nothing_out", $returnArray ); 
            
			// add each line returned 
            for ( $i = 1; $i < ( count( $returnArray ) ); $i++ )
				$outAttachmentHeader .= $returnArray[$i] . "\n";
        }
		 
        return $outAttachmentHeader; 
    }
	
	/**
	 * Sends the email.
	 *
	 * @access public
	 */
	function send()
	{ 
        // set  mail header to blank 
        $mailHeader = ""; 
        
		// add CC 
        if ( $this->mailCC != "" )
			$mailHeader .= "CC: " . $this->mailCC . "\n"; 
        
		// add BCC 
        if ( $this->mailBCC != "" )
			$mailHeader .= "BCC: " . $this->mailBCC . "\n"; 
        
		// add From 
        if ( $this->mailFrom != "" )
			$mailHeader .= "FROM: " . $this->mailFrom . "\n"; 

			
		// message type
        
		// text only 
        if ( ( $this->mailText != "" ) && ( $this->mailHTML == "" ) && ( $this->mailAttachments == "" ) )
		{ 
            return mail( $this->mailTo, $this->mailSubject, $this->mailText, $mailHeader ); 
        }
		// html and text 
        else if ( ( $this->mailText != "" ) && ( $this->mailHTML != "" ) && ( $this->mailAttachments == "" ) )
		{ 
            // get random boundary for content types 
            $bodyBoundary = $this->getRandomBoundary(); 
            
			// format headers 
            $textHeader = $this->formatTextHeader(); 
            $htmlHeader = $this->formatHTMLHeader(); 
            
			// set MIME-Version 
            $mailHeader .= "MIME-Version: 1.0\n"; 
            
			// set up main content header with boundary 
            $mailHeader .= "Content-Type: multipart/alternative;\n"; 
            $mailHeader .= ' boundary="' . $bodyBoundary . '"'; 
            $mailHeader .= "\n\n\n"; 
            
			// add body and boundaries 
            $mailHeader .= "--" . $bodyBoundary . "\n"; 
            $mailHeader .= $textHeader; 
            $mailHeader .= "--" . $bodyBoundary . "\n"; 
            
			// add html and ending boundary 
            $mailHeader .= $htmlHeader; 
            $mailHeader .= "\n--" . $bodyBoundary . "--"; 
            
			// send message 
            return mail( $this->mailTo, $this->mailSubject, "", $mailHeader ); 
        } 
        // HTML AND TEXT AND ATTACHMENTS 
        else if ( ( $this->mailText != "" ) && ( $this->mailHTML != "" ) && ( $this->mailAttachments != "" ) )
		{      
            // get random boundary for attachments 
            $attachmentBoundary = $this->getRandomBoundary(); 
            
			// set main header for all parts and boundary 
            $mailHeader .= "Content-Type: multipart/mixed;\n"; 
            $mailHeader .= ' boundary="' . $attachmentBoundary . '"' . "\n\n"; 
            $mailHeader .= "This is a multi-part message in MIME format.\n"; 
            $mailHeader .= "--" . $attachmentBoundary . "\n"; 
             
            // TEXT AND HTML 
            // get random boundary for content types 
            $bodyBoundary = $this->getRandomBoundary( 1 );
			 
            // format headers 
            $textHeader = $this->formatTextHeader(); 
            $htmlHeader = $this->formatHTMLHeader();
			
            // set MIME-Version 
            $mailHeader .= "MIME-Version: 1.0\n"; 
            
			// set up main content header with boundary 
            $mailHeader .= "Content-Type: multipart/alternative;\n"; 
            $mailHeader .= ' boundary="' . $bodyBoundary . '"'; 
            $mailHeader .= "\n\n\n";
			
            // add body and boundaries 
            $mailHeader .= "--" . $bodyBoundary . "\n"; 
            $mailHeader .= $textHeader; 
            $mailHeader .= "--" . $bodyBoundary . "\n";
			 
            // add html and ending boundary 
            $mailHeader .= $htmlHeader; 
            $mailHeader .= "\n--" . $bodyBoundary . "--"; 
             
            // get array of attachment filenames 
            $attachmentArray = explode( ",", $this->mailAttachments ); 
            
			// loop through each attachment 
            for ( $i = 0; $i < count( $attachmentArray ); $i++ )
			{ 
                // attachment separator 
                $mailHeader .= "\n--" . $attachmentBoundary . "\n"; 
                
				// get attachment info 
                $mailHeader .= $this->formatAttachmentHeader( $attachmentArray[$i] ); 
            }
			
            $mailHeader .= "--" . $attachmentBoundary . "--"; 
            return mail( $this->mailTo, $this->mailSubject, "", $mailHeader ); 
        }
		
        return false; 
    }
} // END OF Email

?> 
