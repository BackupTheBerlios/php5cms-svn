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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


/**
 * @package html_js
 */
 
class JavaScriptCompress extends PEAR 
{
	/**
	 * @access public
	 */
	var $doRemoveComments = true;
	
	/**
	 * @access public
	 */
	var $doCompressWhiteSpace = true;

	/**
	 * @access private
	 */	
	var $_literalStrings;
	
	
	/**
	 * @access public
	 */
	function crunch( $input ) 
	{
		$output = $input;
		$output = str_replace( "\r\n", "\n", $output );
		$output = str_replace( "\r",   "\n", $output );
		$output = $this->_replaceLiteralStrings( $output );
		
		if ( $this->doRemoveComments ) 
			$output = $this->removeComments( $output );
		
		if ( $this->doCompressWhiteSpace ) 
			$output = $this->compressWhiteSpace ($output );
		
		$output = $this->_combineLiteralStrings( $output );
		$output = $this->_restoreLiteralStrings( $output );
		
		return $output;
	}

	/**
	 * @access public
	 */
	function removeComments( $s ) 
	{
		$lines = explode( "\n", $s );
		$t = '';
		$linesSize = sizeof( $lines );
		
		for ( $i = 0; $i < $linesSize; $i++ ) 
			$t .= preg_replace( '/([^\x2f]*)\x2f\x2f.*$/', '$1', $lines[$i] ) . "\n";

		$t         = str_replace( "\n", '__newline__', $t );
		$lines     = explode( '*/', $t );
		$t         = '';
		$linesSize = sizeof( $lines );
		
		for ( $i = 0; $i < $linesSize; $i++ ) 
			$t .= preg_replace( '/(.*)\x2f\x2a(.*)$/', '$1 ', $lines[$i] );
			
		$t = str_replace( '__newline__', "\n", $t );
		return $t;
	}

	/**
	 * @access public
	 */
	function compressWhiteSpace( $s ) 
	{
		$t = explode( "\n", $s );
		$newS = '';
		
		foreach ( $t as $line ) 
		{
			if ( !$this->_isWhite( $line ) ) 
			{
				$lastLine = preg_replace( "/^\s+(.*)\s+$/", '$1', $line );
				$lastLine = trim( $lastLine );
				
				if ( substr( $lastLine, -1 ) !== ';' ) 
					$lastLine .= "\n";
				
				$newS .= $lastLine;
			}
		}

		$s = $newS;
		return $s;
	}
	
	
	// private methods

	/**
	 * @access private
	 */
	function _replaceLiteralStrings( $s ) 
	{
		$this->_literalStrings = array();
		
		$t         = '';
		$lines     = explode( "\n", $s );
		$linesSize = sizeof( $lines );
		
		for ( $i = 0; $i < $linesSize; $i++ ) 
		{
			$j = 0;
			$inQuote = false;
			
			if ( ( strpos( $lines[$i], '.replace(' ) !== false ) || ( strpos( $lines[$i], '.match(' ) !== false ) || ( strpos( $lines[$i], '.replace(' ) !== false ) ) 
			{
				$t .= '__literal_' . sizeof( $this->_literalStrings ) . '__' . "\n";
				$this->_literalStrings[] = $lines[$i];
				
				continue;
			}

			while ( $j < strlen( $lines[$i] ) ) 
			{
				$c = $lines[$i][$j];
				
				if ( !$inQuote ) 
				{
					if ( ( $c == '"' ) || ( $c == "'" ) ) 
					{
						do 
						{
							$posOfReplace = strpos( $lines[$i], '.replace(' );
							
							if ( $posOfReplace !== false ) 
							{
								$posOfComma = strpos( $lines[$i], ',', $posOfReplace );
								
								if ( $posOfComma !== false ) 
								{
									if ( $posOfComma > $j )
										break;
								}
							}

							$inQuote   = true;
							$escaped   = false;
							$quoteChar = $c;
							$literal   = $c;
						} while ( false );
						
						if ( !$inQuote ) 
							$t .= $c;
					} 
					else 
					{
						$t .= $c;
					}
				} 
				else 
				{
					if ( ( $c == $quoteChar ) && !$escaped ) 
					{
						$inQuote  = false;
						$literal .= $quoteChar;
						$t .= '__literal_' . sizeof( $this->_literalStrings ) . '__';
						$this->_literalStrings[] = $literal;
					} 
					else if ( ( $c == "\\" ) && !$escaped ) 
					{
						$escaped = true;
					} 
					else 
					{
						$escaped = false;
					}
					
					$literal .= $c;
				}
				
				$j++;
			}

			$t .= "\n";
		}
		
		return $t;
	}

	/**
	 * @access private
	 */
	function _combineLiteralStrings( $s ) 
	{
		$s = preg_replace( '/"\+"/', '', $s );
		$s = preg_replace( "/'\+'/", '', $s );
		
		return $s;
	}

	/**
	 * @access private
	 */
	function _restoreLiteralStrings( $s ) 
	{
		$litLen = sizeof( $this->_literalStrings );
		
		for ( $i = 0; $i < $litLen; $i++ ) 
			$s = preg_replace( '/__literal_' . $i . '__/', $this->_literalStrings[$i], $s );

		return $s;
	}
	
	/**
	 * @access private
	 */
	function _isWhite( $string ) 
	{
		return (bool)preg_match( '/^\s*$/', $string );
	}
} // END OF JavaScriptCompress

?>
