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
 * @package util
 */
 
class DataConstraint extends PEAR
{
	/**
	 * @access public
	 */
	var $regex;
	
	/**
	 * @access public
	 */
	var $text;
	
	/**
	 * @access public
	 */
	var $errorMessage;

	
	/**
	 * Constructs a DataConstraint with the text, regular expression and 
	 * error message.
	 *
	 * @param  string  $regex  The perl-style regular expression
	 * @param  string  $text   The text to test the match on.
	 * @param  string  $errorMessage  The error message associated with the DataConstraint to return back to the client.
	 * @access public
	 */
	function DataConstraint( $regex, $text, $errorMessage ) 
	{
		$this->setRegex( $regex );
		$this->setText( $text );
		$this->setErrorMessage( $errorMessage );
	}

	
	/**
	 * Sets the regular expression.
	 *
	 * @param string  $regex  The perl-style regular expression
	 * @return void
	 */
	function setRegex( $regex ) 
	{
		$this->regex = $regex;
	}

	/**
	 * Gets the regular expression.
	 * 
	 * @return string
	 */
	function getRegex() 
	{
		return $this->regex;
	}

	/**
	 * Sets the text to match.
	 *
	 * @param   string  $text  The text to test the match on.
	 * @return void
	 */
	function setText( $text ) 
	{
		$this->text = $text;
	}

	/**
	 * Gets the text.
	 *
	 * @return void
	 */
	function getText() 
	{
		return $this->text;
	}

	/**
	 * Sets the error message.
	 *
	 * @param   string  $errorMessage  The error message associated with the DataConstraint to return back to the client.
	 * @return void
	 */
	function setErrorMessage( $errorMessage ) 
	{
		$this->errorMessage = $errorMessage;
	}

	/**
	 * Returns the error message.
	 *
	 * @return string
	 */
	function getErrorMessage() 
	{
		return $this->errorMessage;
	}

	/**
	 * Returns true if the regular expression matches the text or false otherwise.
	 *
	 * @return bool
	 */
	function isMatched() 
	{
		return preg_match( $this->regex, $this->text );
	}

	/**
	 * Returns the clientside javascript code to ensure this constraint.
	 * 
	 * @return string
	 */
	function getJavaScript( $formName, $elementName ) 
	{
		return 
			'isControlOkay = validateControl( "' . $formName. '", "' . $elementName . '", ' . $this->regex . ' );' . 
				"\n" .
			"if ( isControlOkay == false ) { " . "\n" .
			'   errorMessage += "' . $this->errorMessage . '\n";' . 
				"\n" .
			"}" . "\n" .
			"\n" .
			'isFormOkay = isFormOkay && isControlOkay;' . "\n" .
			"\n";
	}
} // END OF DataConstraint

?>
