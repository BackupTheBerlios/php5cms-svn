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
 * Simple wrapper around Google Translation Service.
 *
 * @package services_google
 */
 
class GoogleTranslator extends PEAR
{
	/**
	 * Method to translate the text.
	 *
	 * The values can be:
	 * 	from
	 *		en: English
	 *		de: Gernan
	 *		es: Spanish
	 *		fr: French
	 *		it: Italian
	 *		pt: Portuguese
	 *	to
	 *		de: German
	 *		es: Spanish
	 *		fr: French
	 *		it: Italian
	 *		pt: Portuguese
	 *		en: English
	 *
	 * @param		expression string	Text to translate
	 * @param		from string			Parameter that represent
	 *									the source language of the text
	 * @param		to string			Param that represent the
	 *									language to translate
	 */
	function translate( $expression, $from, $to ) 
	{
		$f = file( "http://translate.google.com/translate_t?text=" . urlencode( $expression ) . "&langpair=$from|$to" );
		
		foreach ( $f as $v ) 
		{
			if ( strstr( $v, '<textarea' ) )
				$x = strstr( $v, '<textarea' );
		}
		
		$arr = explode( '</textarea>', $x );
		$arr = explode( 'wrap=PHYSICAL>', $arr[0] );
		
		return $arr[1];
	}
} // END OF GoogleTranslator

?>
