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
 * Simple class for using Google's WebAPI and PHP5's SOAP extension
 *
 * Requirements:
 *
 * - PHP 5 and the SOAP extension
 * - Google licence key - get an account at http://www.google.com/apis/
 */

class GoogleClient extends SOAPClient
{
	/**
	 * @access public
	 */	
	public $results;

	/**
	 * @access private
	 */		
	private $exception = '';
	
	/**
	 * @access private
	 */	
	private $licenceKey = '';


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct( $licenceKey )
	{
    	$this->licenceKey = $licenceKey;
    	parent::__construct( 'GoogleSearch.wsdl' );
  	}

	
	/**
	 * @access public
	 */	
	function search( $query, $start = 0, $maxResults = 10, $filter = 'false', $restrict = '', $safeSearch = 'false',
					 $languageRestrict = '', $inputEncoding = 'latin1', $outputEncoding = 'latin1' )
	{
    	try
		{
      		$this->results = $this->doGoogleSearch( $this->licenceKey, $query, $start, $maxResults, $filter, $restrict, $safeSearch, $languageRestrict, $inputEncoding, $outputEncoding );
			return true;
		} 
		catch ( SoapFatal $exception )
		{
			$this->exception = $exception;
			return false;
		}
	}

  	/**
	 * @access public
	 */	
	function error()
	{
    	return $this->exception;
  	}
} // END OF GoogleClient

?>
