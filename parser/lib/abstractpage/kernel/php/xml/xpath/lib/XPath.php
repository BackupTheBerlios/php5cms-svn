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
 * @package xml_xpath_lib
 */
class XPath extends Base
{
	/**
	 * As debugging of the xml parse is spread across several functions, we need to make this a member.
	 * @access public
	 */
	public $bDebugXmlParse = false;
	
	/**
	 * @access protected
	 */
	protected $_lastError;

	
  	/**
 	 * Constructor
	 *
	 * @access public
  	 */
  	public function __construct()
	{
		$this->properties['verboseLevel'] = 1;  // 0=silent, 1 and above produce verbose output (an echo to screen). 
  	}
  
  
  	/**
   	 * Resets the object so it's able to take a new xml sting/file
   	 *
   	 * Constructing objects is slow.  If you can, reuse ones that you have used already
   	 * by using this reset() function.
	 *
	 * @access public
   	 */
  	public function reset()
	{
    	$this->_lastError = '';
  	}
  
  	/**
   	 * Turn verbose (error) output ON or OFF
  	 *
   	 * Pass a bool. true to turn on, false to turn off.
   	 * Pass a int >0 to reach higher levels of verbosity (for future use).
  	 *
   	 * @param  $levelOfVerbosity  (mixed) default is 0 = off
	 * @access public
   	 */
  	public function setVerbose( $levelOfVerbosity = 1 )
	{
    	$level = -1;
    
		if ( $levelOfVerbosity === true )
      		$level = 1;
    	else if ( $levelOfVerbosity === false )
      		$level = 0;
    	else if ( is_numeric( $levelOfVerbosity ) )
      		$level = $levelOfVerbosity;
    
    	if ( $level >= 0 )
			$this->properties['verboseLevel'] = $levelOfVerbosity;
  	}
   
  	/**
   	 * Returns the last occured error message.
   	 *
   	 * @access public
   	 * @return string (may be empty if there was no error at all)
   	 */
  	public function getLastError()
	{
    	return $this->_lastError;
  	}
	
	
  	// protected methods
	
  	/**
   	 * This method checks the right ammount and match of brackets
   	 *
   	 * @param     $term (string) String in which is checked.
   	 * @return          (bool)   true: OK / false: KO  
   	 * @access protected
   	 */
  	protected function _bracketsCheck( $term )
	{
    	$leng = strlen( $term );
    	$brackets = 0;
    	$bracketMisscount = $bracketMissmatsh = false;
    	$stack = array();
    
		for ( $i = 0; $i < $leng; $i++ )
		{
      		switch ( $term[$i] )
			{
        		case '(': 
        
				case '[': 
          			$stack[$brackets] = $term[$i]; 
          			$brackets++; 
          			break;
        
				case ')': 
          			$brackets--;
          
		  			if ( $brackets < 0 )
					{
            			$bracketMisscount = true;
            			break 2;
          			}
          
		  			if ( $stack[$brackets] != '(' )
					{
            			$bracketMissmatsh = true;
            			break 2;
          			}
          
		  			break;
        
				case ']': 
          			$brackets--;
          
		  			if ( $brackets < 0 )
					{
            			$bracketMisscount = true;
            			break 2;
          			}
          
		  			if ( $stack[$brackets] != '[' )
					{
            			$bracketMissmatsh = true;
            			break 2;
          			}
          
		  			break;
      		}
    	}
    
		// Check whether we had a valid number of brackets.
    	if ( $brackets != 0 )
			$bracketMisscount = true;
    
		if ( $bracketMisscount || $bracketMissmatsh )
      		return false;
    
    	return true;
	}
  
	/**
	 * Looks for a string within another string -- BUT the search-string must be located *outside* of any brackets.
	 *
	 * This method looks for a string within another string. Brackets in the
	 * string the method is looking through will be respected, which means that
	 * only if the string the method is looking for is located outside of
	 * brackets, the search will be successful.
	 *
	 * @param     $term       (string) String in which the search shall take place.
	 * @param     $expression (string) String that should be searched.
	 * @return                (int)    This method returns -1 if no string was found, 
	 *                                 otherwise the offset at which the string was found.
	 * @access protected
	 */
	protected function _searchString( $term, $expression )
	{
    	$bracketCounter = 0; // Record where we are in the brackets. 
    	$leng = strlen( $term );
    	$exprLeng = strlen( $expression );
    
		for ( $i = 0; $i < $leng; $i++ )
		{
      		$char = $term[$i];
      
	  		if ( $char == '(' || $char == '[' )
			{
        		$bracketCounter++;
        		continue;
      		}
      		else if ( $char == ')' || $char == ']' )
			{
        		$bracketCounter--;
      		}
			
      		if ( $bracketCounter == 0 )
			{
        		// Check whether we can find the expression at this index.
        		if ( substr( $term, $i, $exprLeng ) == $expression )
					return $i;
      		}
    	}
    
		// Nothing was found.
    	return ( -1 );
	}
  
  	/**
   	 * Split a string by a searator-string -- BUT the separator-string must be located *outside* of any brackets.
   	 * 
   	 * Returns an array of strings, each of which is a substring of string formed 
   	 * by splitting it on boundaries formed by the string separator. 
   	 *
   	 * @param     $separator  (string) String that should be searched.
   	 * @param     $term       (string) String in which the search shall take place.
   	 * @return                (array)  see above
	 * @access protected
   	 */
  	protected function _bracketExplode( $separator, $term )
	{
    	// Note that it doesn't make sense for $separator to itself contain (,),[ or ],
    	// but as this is a protected function we should be ok.
    	$resultArr = array();
    	$bracketCounter = 0; // Record where we are in the brackets. 
    
		// BEGIN try block
		do
		{
      		// Check if any separator is in the term
      		$sepLeng = strlen( $separator );
			
			// no separator found so end now
      		if ( strpos( $term, $separator ) === false )
			{
        		$resultArr[] = $term;
        		break; // try-block
      		}
      
      		// Make a substitute separator out of 'unused chars'.
      		$substituteSep = str_repeat( chr( 2 ), $sepLeng );
      
      		// Now determin the first bracket '(' or '['.
      		$tmp1 = strpos( $term, '(' );
      		$tmp2 = strpos( $term, '[' );
			
      		if ( $tmp1 === false )
				$startAt = (int)$tmp2;
      		else if ( $tmp2 === false )
        		$startAt = (int)$tmp1;
      		else
        		$startAt = min( $tmp1, $tmp2 );
      
      		// Get prefix string part before the first bracket.
      		$preStr = substr( $term, 0, $startAt );
			
      		// Substitute separator in prefix string.
      		$preStr = str_replace( $separator, $substituteSep, $preStr );
      
      		// Now get the rest-string (postfix string)
      		$postStr = substr( $term, $startAt );
      
	  		// Go all the way through the rest-string.
      		$strLeng = strlen( $postStr );
			
      		for ( $i = 0; $i < $strLeng; $i++ )
			{
        		$char = $postStr[$i];
        
				// Spot (,),[,] and modify our bracket counter.  Note there is an
        		// assumption here that you don't have a string(with[mis)matched]brackets.
        		// This should be ok as the dodgy string will be detected elsewhere.
        		if ( $char == '(' || $char == '[' )
				{
          			$bracketCounter++;
          			continue;
        		} 
        		else if ( $char == ')' || $char == ']' )
				{
          			$bracketCounter--;
        		}
        
				// If no brackets surround us check for separator.
        		if ( $bracketCounter == 0 )
				{
          			// Check whether we can find the expression starting at this index.
          			if ( ( substr( $postStr, $i, $sepLeng ) == $separator ) )
					{
            			// Substitute the found separator. 
            			for ( $j = 0; $j < $sepLeng; $j++ )
              				$postStr[$i + $j] = $substituteSep[$j];
          			}
        		}
      		}
      		
			// Now explod using the substitute separator as key.
      		$resultArr = explode( $substituteSep, $preStr . $postStr );
 		} while ( false );
	
		// Return the results that we found. May be a array with 1 entry.
    	return $resultArr;
  	}
  
	/**
	 * Retrieves a substring before a delimiter.
	 *
	 * This method retrieves everything from a string before a given delimiter,
	 * not including the delimiter.
	 *
	 * @param     $string     (string) String, from which the substring should be extracted.
	 * @param     $delimiter  (string) String containing the delimiter to use.
	 * @return                (string) Substring from the original string before the delimiter.
	 * @access protected
	 */
	protected function _prestr( &$string, $delimiter, $offset = 0 )
	{
    	// Return the substring.
    	$offset = ( $offset < 0 )? 0 : $offset;
   	 	$pos = strpos( $string, $delimiter, $offset );
		
    	if ( $pos === false )
			return $string;
		else
			return substr( $string, 0, $pos );
  	}
  
	/**
	 * Retrieves a substring after a delimiter.
	 *
	 * This method retrieves everything from a string after a given delimiter,
	 * not including the delimiter.
	 *
	 * @param     $string     (string) String, from which the substring should be extracted.
	 * @param     $delimiter  (string) String containing the delimiter to use.
	 * @return                (string) Substring from the original string after the delimiter.
	 * @access protected
	 */
	protected function _afterstr( $string, $delimiter, $offset = 0 )
	{
    	$offset = ( $offset < 0 )? 0 : $offset;
    
		// Return the substring.
    	return substr( $string, strpos( $string, $delimiter, $offset ) + strlen( $delimiter ) );
  	}
  
	/**
	 * Creates a textual error message and sets it. 
	 * 
	 * example: 'XPath error in THIS_FILE_NAME:LINE. Message: YOUR_MESSAGE';
	 * 
	 * I don't think the message should include any markup because not everyone wants to debug 
	 * into the browser window.
	 * 
	 * You should call _displayError() rather than _setLastError() if you would like the message,
	 * dependant on their verbose settings, echoed to the screen.
	 * 
	 * @param  $message (string) a textual error message default is ''
	 * @param  $line    (int)    the line number where the error occured, use __LINE__
	 * @access protected
	 */
	protected function _setLastError( $message = '', $line = '-', $file = '-' )
	{
    	$this->_lastError = 'XPath error in ' . basename( $file ) . ':' . $line . '. Message: ' . $message;
  	}
  
  	/**
   	 * Displays an error message.
   	 *
   	 * This method displays an error messages depending on the users verbose settings 
   	 * and sets the last error message.  
   	 *
   	 * If also possibly stops the execution of the script.
   	 * ### Terminate should not be allowed --fab. Should it?
  	 *
   	 * @param  $message    (string)  Error message to be displayed.
   	 * @param  $lineNumber (int)     line number given by __LINE__
   	 * @param  $terminate  (bool)    (default TURE) End the execution of this script.
	 * @access protected
   	 */
	protected function _displayError( $message, $lineNumber = '-', $file = '-', $terminate = true )
	{
    	// Display the error message.
    	$err = '<b>XPath error in ' . basename( $file ) . ':' . $lineNumber . '</b> ' . $message . "<br \>\n";
    	$this->_setLastError( $message, $lineNumber, $file );
    
		if ( ( $this->properties['verboseLevel'] > 0 ) || $terminate )
			echo $err;
    
		// End the execution of this script.
    	if ( $terminate )
			exit;
  	}

  	/**
   	 * Displays a diagnostic message
  	 *
   	 * This method displays an error messages
   	 *
   	 * @param  $message    (string)  Error message to be displayed.
   	 * @param  $lineNumber (int)     line number given by __LINE__
	 * @access protected
   	 */
  	protected function _displayMessage( $message, $lineNumber = '-', $file = '-' )
	{
    	// Display the error message.
    	$err = '<b>XPath message from ' . basename( $file ) . ':' . $lineNumber . '</b> ' . $message . "<br \>\n";
    
		if ( $this->properties['verboseLevel'] > 0 )
			echo $err;
  	}
  
  	/**
   	 * Called to begin the debug run of a function.
   	 *
   	 * This method starts a <DIV><PRE> tag so that the entry to this function
   	 * is clear to the debugging user.  Call _closeDebugFunction() at the
   	 * end of the function to create a clean box round the function call.
   	 *
   	 * @param     $functionName (string) the name of the function we are beginning to debug
   	 * @return                  (array)  the output from the microtime() function.
   	 * @access protected
   	 */
  	protected function _beginDebugFunction( $functionName )
	{
    	$fileName = basename( __FILE__ );
    
		static $color = array(
			'green',
			'blue',
			'red',
			'lime',
			'fuchsia', 
			'aqua'
		);
    
		static $colIndex = -1;
    	$colIndex++;
    
		$pre = '<pre STYLE="border:solid thin ' . $color[$colIndex % 6] . '; padding:5">';
    	$out = '<div align="left"> ' . $pre . "<STRONG>{$fileName} : {$functionName}</STRONG><HR>";
    	echo $out;
    
		return microtime();
  	}
  
	/**
	 * Called to end the debug run of a function.
	 *
	 * This method ends a <DIV><PRE> block and reports the time since $aStartTime
	 * is clear to the debugging user.
	 *
	 * @param     $aStartTime   (array) the time that the function call was started.
	 * @param     $return_value (mixed) the return value from the function call that 
	 *                                  we are debugging
	 * @access protected
	 */
	protected function _closeDebugFunction( $aStartTime, $returnValue = "" )
	{
    	echo "<hr>";
    
		if ( isSet( $returnValue ) )
		{
      		if ( is_array( $returnValue ) )
        		echo "Return Value: " . print_r( $returnValue ) . "\n";
      		else if ( is_numeric( $returnValue ) ) 
        		echo "Return Value: '$return_value'\n";
      		else if ( is_bool( $returnValue ) ) 
        		echo "Return Value: " . ( $returnValue? "TRUE" : "FALSE" ) . "\n";
      		else 
        		echo "Return Value: \"".htmlspecialchars($returnValue)."\"\n";
    	}
    
		$this->_profileFunction( $aStartTime, "Function took" );
    	echo " \n</pre></div>";
  	}
  
	/**
	 * Call to return time since start of function for Profiling
	 *
	 * @param  $aStartTime  (array)  the time that the function call was started.
	 * @param  $alertString (string) the string to describe what has just finished happening
	 * @access protected
	 */
	protected function _profileFunction( $aStartTime, $alertString )
	{
    	// Print the time it took to call this function.
    	$now   = explode( ' ', microtime() );
    	$last  = explode( ' ', $aStartTime );
    	$delta = ( round( ( ( $now[1] - $last[1] ) + ( $now[0] - $last[0] ) ) * 1000 ) );
    
		echo "\n{$alertString} <strong>{$delta} ms</strong>";
	}
  
	/**
   	 * This is a debug helper function. It dumps the node-tree as HTML
   	 *
   	 * *QUICK AND DIRTY*. Needs some polishing.
   	 *
   	 * @param  $node   (array)   A node 
   	 * @param  $indent (string) (optional, default=''). For internal recursive calls.
	 * @access protected
   	 */
  	protected function _treeDump( $node, $indent = '' )
	{
    	$out = '';
    
    	// Get rid of recursion.
    	$parentName = empty( $node['parentNode'] )? "SUPER ROOT" :  $node['parentNode']['name'];
    	unset($node['parentNode']);
    	$node['parentNode'] = $parentName ;
    
    	$out .= "NODE[{$node['name']}]\n";
    
    	foreach ( $node as $key => $val )
		{
      		if ( $key === 'childNodes' )
				continue;
      
	  		if ( is_Array( $val ) )
        		$out .= $indent . "  [{$key}]\n" . arrayToStr( $val, $indent . '    ' );
      		else
        		$out .= $indent . "  [{$key}] => '{$val}' \n";
		}
    
		if ( !empty( $node['childNodes'] ) )
		{
      		$out .= $indent . "  ['childNodes'] (Size = " . sizeOf($node['childNodes'] ) . ")\n";
			
      		foreach ( $node['childNodes'] as $key => $childNode )
        		$out .= $indent . "     [$key] => " . $this->_treeDump( $childNode, $indent . '       ' ) . "\n";
    	}
    
    	if ( empty( $indent ) )
      		return "<pre>" . htmlspecialchars( $out ) . "</pre>";
    
    	return $out;
  	}
} // END OF XPath

?>
