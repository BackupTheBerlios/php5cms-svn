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
|Authors: Moritz Heidkamp <moritz.heidkamp@invision-team.de>           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * CSSParser
 * 
 * Class to parse CSS code respectively files containing CSS code.
 *
 * @package html_css
 */
 
class CSSParser extends PEAR
{
	/**
	 * @access private
	 */
	var $_css;
	
	/**
	 * @access private
	 */
    var $_file = '';
 

    /**
     * Parses given CSS code and makes it accessible via the CSSParser::get() method.
     * 
     * @param string $css - string containing (preferably) valid CSS code
     */
    function parse( $css )
    {
        // remove single-line comments
        $css = preg_replace( '!//.*?(\n|\r)!s', '', $css );
		
        // remove all line-breaks because the /m modifier of preg doesn't seem to work properly
        $css = str_replace( array( "\n", "\r" ), '', $css );
		
        // remove multi-line comments
        $css = preg_replace( '!/\*(.*?)\*/!m', '', $css );
		
        $parsed    = array();
        $selectors = '';
        
        // iterate through css code
        for ( $i = 0; $i < strlen( $css ); $i++ ) 
		{
            if ( $css{$i} != '{' ) 
			{
                $selectors .= $css{$i};
            }
            else 
			{
                $selectors = explode( ',', $selectors );
                
                for ( $s = 0; $s < count( $selectors ); $s++ ) 
				{
                    $selectors[$s] = trim( $selectors[$s] );
                    
                    if ( !isset( $parsed[$selectors[$s]] ) )
                        $parsed[$selectors[$s]] = array();
                }
                
                $values = '';
                $j = $i + 1;
                
                while ( ( $css{$j} != '}' ) && ( $j < strlen( $css ) ) ) 
				{
                    $values .= $css{$j};
                    $j++;
                }
                
                if ( $values{ strlen( $values ) - 1} == ';' )
                    $values = substr( $values, 0, -1 );
                
                $values = $this->_explode( ';', $values );
                
                foreach ( $values as $val ) 
				{
                    $val    = $this->_explode( ':', $val, true );
                    $val[0] = explode( ',', $val[0] );
					
					if ( isset( $val[1] ) )
						$val[1] = $this->_removeQuotes( trim( $val[1] ) );
                    
                    foreach ( $val[0] as $key ) 
					{
                        $key = trim( $key );
                        
                        if ( $key != '' ) 
						{
                            foreach ( $selectors as $selector )
                                $parsed[$selector][$key] = $val[1];
                        }
                    }
                }
                
                $selectors = '';
                $i = $j;
            }
        }
        
        $this->_reset();
        
        foreach ( $parsed as $key => $value ) 
		{
            switch ( $key{0} )
			{
                case '@': 
					$this->_css['Globals'][substr( $key, 1 )] = $value;
                    break; 
					
                case '.': 
					$this->_css['Classes'][substr( $key, 1 )] = $value;
                    break;
					
                case '#': 
					$this->_css['IDs'][substr( $key, 1 )] = $value;
                    break;
					
            	default:  
					$this->_css['Tags'][$key] = $value;
            }
        }
    }
    
    /**
     * Reads a file and passes its contents to CSSParser::parse().
     * 
     * @param string $file - filename of CSS file
     * @return boolean     - true on success, false otherwise
     */
    function parseFile( $file )
    {
        $this->_file = $file;
        $fp = fopen( $this->_file, 'r' );
        
        if ( $fp !== false ) 
		{
            $css = fread( $fp, filesize( $this->_file ) );
            fclose( $fp );
            $this->parse( $css );
			
            return true;
        }
        else 
		{
            return false;
        }
    }
    
    /**
     * Get a section, selector or value from parsed CSS code.
     * 
     * @return mixed - desired value
     */
    function get()
    {
        if ( func_num_args() == 0 )
            return false;
        
        $keys   = func_get_args();
        $keyStr = '';
        
        foreach ( $keys as $key )
          $keyStr .= '[\'' . $key . '\']';
        
        return eval( 'return $this->_css' . $keyStr . ';' );
    }
    
	
	// private methods
	
    /**
     * Resets internal stack.
     */
    function _reset()
    {
        $this->_css = array(
            'Globals' => array(),
            'Classes' => array(),
            'IDs'     => array(),
            'Tags'    => array()
        );
    }
    
    /**
     * Similar to the native php function explode() but considering quotes and escape chars.
     * 
     * @param string  $glue
     * @param string  $str
     * @param boolean $functions - whether to parse function calls or not (default is false)
     * @param array   $quotes
     * @param string  $terminator - if not-null, the result will be returned immediatly on occurrence
     * @return 
     */
    function _explode( $glue, $str, $functions = false, $quotes = array( "'", '"' ), $escape = '\\', $terminator = null, $iterator = null )
    {
        $result     = array();
        $item       = '';
        $inQuotes   = false;
        $startQuote = '';
        
        for ( $i = 0; $i < strlen( $str ); $i++ ) 
		{
            if ( ( !is_null( $terminator ) ) && ( !$inQuotes ) && ( $str{$i} == $terminator ) ) 
			{
                if ( $item != '' )
                    $result[] = $item;
                
                $iterator += $i + 1;
                return $result;
            }
        
            if ( ( in_array( $str{$i}, $quotes ) ) && ( ( $i > 0 )? $str{$i - 1} != $escape : true ) && ( $startQuote == ''? true : $str{$i} == $startQuote ) ) 
			{
                if ( $startQuote == '' )
                    $startQuote = $str{$i};
                else
                    $startQuote = '';
                
                $inQuotes = !$inQuotes;
            }
            
            if ( ( ( $inQuotes ) || ( $str{$i} != $glue ) ) && ( $str{$i} != $escape ) ) 
			{    
                // check for function call
                if ( ( $functions ) && ( !$inQuotes ) && ( $str{$i} == '(' ) ) 
				{
                    $arguments = implode( ',', $this->_explode( ',', substr( $str, $i + 1 ), true, $quotes, $escape, ')', &$i ) );
                    $item = eval( 'return @' . $item . '(' . $arguments . ');' );
                }
                else 
				{
                    $item .= $str{$i};
                }
            }
            else if ( ( !$inQuotes ) && ( $str{$i} == $glue ) ) 
			{
                $result[] = $item;
                $function = '';
                $item     = '';
            }
        }
        
        if ( $item != '' )
            $result[] = $item;
        
        return $result;
    }
    
    /**
     * Removes quotes from the beginning and the end of a string.
     * 
     * @param $str
     * @param array $quotes
     * @return 
     */
    function _removeQuotes( $str, $quotes = array( "'", '"' ) )
    {
        $result = $str;
        
        foreach ( $quotes as $quote ) 
		{
            if ( ( $str{0} == $quote ) && ( $str{strlen( $str ) - 1} == $quote ) ) 
			{
                $result = substr( $str, 1, -1 );
                break;
            }
        }
        
        return $result;
    }
} // END OF CSSParser

?>
