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


using( 'util.Dictionary' );
using( 'util.DataConstraint' );


define( "ALPHA_REG",        "/^\D+$/" );
define( "ALPHANUMERIC_REG", "/^(.+|[^.]+)+$/" );
define( "NUMERIC_REG",      "/^\d+$/" );
define( "PASSWORD_REG",     "/^....+$/" );
define( "EMAIL_REG",        "/^.+@.+\..{2,4}$/" );
define( "NOTEMPTY_REG",     "/^.+$/" );
define( "PHONE_REG",        "/^([0-9]( |-)?)?(\(?[0-9]{3}\)?|[0-9]{3})( |-)?([0-9]{3}( |-)?[0-9]{4}|[a-zA-Z0-9]{7})$/" );
define( "CREDITCARD_REG",   "/^((4\d{3})|(5[1-5]\d{2})|(6011))-?\d{4}-?\d{4}-?\d{4}|3[4,7]\d{13}$/" );


/**
 * Validates strings of text according to rules defined by regular
 * expressions.
 *
 * @package util_validation
 */

class DataValidator extends PEAR
{
	/**
	 * @access public
	 */
	var $constraints;
	
	/**
	 * @access public
	 */
	var $isValidated = false;

	
	/**
	 * Creates a DataValidator object to validate strings.
	 *
	 * @access public
	 */
	function DataValidator() 
	{
		$this->constraints = new Dictionary();
	}

	
	/**
	 * Adds a string and its type to the list of strings to be validated.
	 *
	 * @param  $id string. Unique identifier for the string to be validated.
	 * @param  $regex string. A regular expression representing all acceptable forms of the text.
	 * @param  $text string. The text to be validated.
	 * @param  $errorMessage string. The error generated if the text fails validation.
	 * @return void
	 * @access public
	 */
	function setConstraint( $id, $regex, $text, $errorMessage ) 
	{
		$constraint = new DataConstraint( $regex, $text, $errorMessage );
		$this->constraints->set( $id, $constraint );
	}

	/**
	 * Gets the DataConstraint associated with a unique name or returns false
	 * if it not found.
	 *
	 * @param  $id string. Unique identifier for the string to be validated.
	 * @return DataConstraint
	 * @access public
	 */
	function getContraint( $id ) 
	{
		return $this->constraints->get( $id );
	}

	/**
	 * Validates the list of strings and returns true if they all pass, and
	 * false otherwise. If false is returned, the client can call 
	 * $this->getConstraints() listing all the items that are not correct
	 *
	 * @return boolean
	 * @access public
	 */
	function validate() 
	{
		$this->isValidated = true;
		$isCorrect = true;
		
		foreach ( $this->constraints->toArray() as $id => $constraint ) 
		{
			if ( $isMatched = $constraint->isMatched() )
				$this->constraints->remove( $id );

			$isCorrect = $isCorrect & $isMatched;
		}

		return $isCorrect;
	}

	/**
	 * Returns a Dictionary of all the constraints that did not match.
	 *
	 * @return Vector
	 * @access public
	 */
	function getConstraints() 
	{
		if ( !$this->isValidated ) 
			$this->validate();
		
		return $this->constraints;
	}


	// static matching methods

	/**
	 * Tests if the text passes the regular expression check.
	 *
	 * @param $text string. The text to be validated.
	 * @param $regex string. A regular expression representing all acceptable forms of the text.
	 * @static
	 * @return boolean
	 * @access public
	 */
	function isMatch( $text, $regex ) 
	{
		return preg_match( $regex, $text );
	}

	/**
	 * Tests if the text passes the alphabetic regular expression check.
	 *
	 * @param $text string. The text to be validated.
	 * @static
	 * @return boolean
	 * @access public
	 */
	function isAlpha( $text ) 
	{
		return preg_match( ALPHA_REG, $text );
	}

	/**
	 * Tests if the text passes the alphanumeric regular expression check.
	 *
	 * @param $text string. The text to be validated.
	 * @static
	 * @return boolean
	 * @access public
	 */
	function isAlphanumeric( $text ) 
	{
		return preg_match( ALPHANUMERIC_REG, $text );
	}

	/**
	 * Tests if the text passes the numeric regular expression check.
	 *
	 * @param $text string. The text to be validated.
	 * @static
	 * @return boolean
	 * @access public
	 */
	function isNumeric( $text ) 
	{
		return preg_match( NUMERIC_REG, $text );
	}

	/**
	 * Tests if the text passes the password regular expression check.
	 *
	 * @param $text string. The text to be validated.
	 * @static
	 * @return boolean
	 * @access public
	 */
	function isPassword( $text ) 
	{
		return preg_match( PASSWORD_REG, $text );
	}

	/**
	 * Tests if the text passes the E-Mail regular expression check.
	 *
	 * @param $text string. The text to be validated.
	 * @static
	 * @return boolean
	 * @access public
	 */
	function isEmail( $text ) 
	{
		return preg_match( EMAIL_REG, $text );
	}

	/**
	 * Tests if the text is empty.
	 *
	 * @param $text string. The text to be validated.
	 * @static
	 * @return boolean
	 * @access public
	 */
	function isEmpty( $text ) 
	{
		return $text == "";
	}
} // END OF DataValidator

?>
