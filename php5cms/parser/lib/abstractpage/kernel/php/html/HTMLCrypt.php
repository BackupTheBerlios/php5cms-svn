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
 * HTMLCrypt provides methods to encrypt text, which 
 * can be later be decrypted using JavaScript on the client side
 *
 * This is very useful to prevent spam robots collecting email
 * addresses from your site, included is a method to add mailto 
 * links to the text being generated
 * 
 * a basic example to encrypt an email address
 * $c = new HTMLCrypt('yourname@emailaddress.com', 8);
 * $c->addMailTo();
 * $c->output();
 *
 * @package html
 */
 
class HTMLCrypt extends PEAR
{
    /**
     * The unencrypted text
     *
     * @access public
     * @var    string
     * @see    setText()
     */
     var $text = '';
     
    /**
     * The full javascript to be sent to the browser
     *
     * @access public
     * @var    string
     * @see    getScript()
     */
     var $script = '';
     
    /**
     * The text encrypted - without any js
     *
     * @access public
     * @var    string
     * @see    cyrptText
     */
     var $cryptString = '';
     
    /**
     * The number to offset the text by
     *
     * @access public
     * @var    int
     */
     var $offset;
     

    /**
     * Constructor
     *
     * @access public
     * @param string $text  The text to encrypt
     * @param int $offset  The offset used to encrypt/decrypt
     */
    function HTMLCrypt( $text = '', $offset = 3 )
	{
        $this->offset = $offset;
        $this->text   = $text;
        $this->script = '';
    }

	
    /**
     * Set name of the current realm
     *
     * @access public
     * @param  string $text  The text to be encrypted
     */
    function setText( $text )
	{
        $this->text = $text;
    }

    /**
     * Turns the text into a mailto link (make sure 
     * the text only contains an email)
     *
     * @access public
     */
    function addMailTo()
	{
    	$email = $this->text;
    	$this->text = '<a href="mailto:' . $email . '">' . $email . '</a>';
    }

    /**
     * Encrypts the text
     *
     * @access private
     */
    function cryptText()
	{
	    $length = strlen( $this->text );
				
		for ( $i = 0; $i < $length; $i++ )
		{
			$current_chr = substr( $this->text, $i, 1 );
			$inter       = ord( $current_chr ) + $this->offset;
			$enc_string .= chr( $inter );
		}
		
        $this->cryptString = $enc_string;
    }

    /**
     * Set name of the current realm
     *
     * @access public
     * @return string $script The javascript generated
     */
    function getScript()
	{
    	if ( ( $this->cryptString == '' ) && ( $this->text != '' ) )
			$this->cryptText();
    	
		// get a random string to use as a function name
		srand ( (float) microtime() * 10000000 );

		$letters = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' );
		$rnd     = $letters[array_rand( $letters )] . md5( time() );
		
		// the actual js (in one line to confuse)
		$script = "<script language=\"JavaScript\" type=\"text/JavaScript\">var a,s,n;function $rnd(s){r='';for(i=0;i<s.length;i++){n=s.charCodeAt(i);if(n>=8364){n=128;}r+=String.fromCharCode(n-".$this->offset.");}return r;}a='".$this->cryptString."';document.write ($rnd(a));</script>";
		$this->script = $script;
		
		return $script;
    }

    /**
     * Outputs the full JS to the browser
     *
     * @access public
     */
    function output()
	{
        if ( $this->script == '' )
        	$this->getScript();
        
        echo $this->script;
    }
} // END OF HTMLCrypt
  
?>
