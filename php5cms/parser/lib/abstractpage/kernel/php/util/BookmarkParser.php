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
 * This class parses IE, Netscape and Opera bookmark files and returns arrays with the bookmark / folder information.
 *
 * Usage:
 *
 * function myURL( $data, $depth, $no ) 
 * {
 * 		echo str_repeat( "&nbsp;&nbsp;&nbsp;", $depth );
 * 		echo "<img src='fileicon.png'>\n";
 * 		echo "&nbsp;<a href='" . $data["url"] . "'>". $data["descr"] ."</a>\n";
 * 		echo "<br>\n";
 * }
 * 
 * function myFolder( $data, $depth, $no ) 
 * {
 * 		echo str_repeat( "&nbsp;&nbsp;&nbsp;", $depth );        
 * 		echo "<img src='openfoldericon.png'>&nbsp;" . $data["name"] . "\n";
 * 		echo "<br>\n";
 * }
 * 
 * $class = new BookmarkParser;
 * $class->parseNetscape( "./bookmarks/bookmarks.html", 0, 'myURL', 'myFolder' );
 * // $class->parseOpera( "./bookmarks/opera6.adr", 0, 'myURL', 'myFolder' );
 * // $class->parseIE( "./bookmarks/", 0, 'myURL', 'myFolder' );
 * 
 * echo "<p># of urls: " . $class->urlsParsed    . "<br>";
 * echo "# of folder: "  . $class->foldersParsed . "<br>";
 *
 * Those two function must be passed on to the three functions above:
 * 
 *     urlFunction($bookmark, $depth, $no)
 *        $bookmark        --> an associative array 
 *        $depth               the folders depth
 *        $no                  the current bookmark count
 *     
 *     folderFunction($folder, $depth, $no)
 *        $folder          --> an associative array 
 *        $depth               the folders depth
 *        $no                  the current folder count
 *
 * @package util
 */

class BookmarkParser extends PEAR
{
	/**
	 * The number of folders parsed after a function call
	 * 
	 * @access   public
	 * @var      Integer
	 */
	var $foldersParsed = 0;

	/**
	 * The number of bookmarks parsed after a function call
	 * 
	 * @access   public
	 * @var      Integer
	 */
	var $urlsParsed = 0;
	
	
	/**
	 * Checks format.
	 *
	 * @access	 public
	 * @return   string   	type of bookmark file
	 */
	function checkFormat( $url )
	{
		$browser = '';
		
		// is it a file?
		if ( is_file( $url ) ) 
		{
			// open	file
			$fp	= @fopen( $url, "r-" );
		
			if ( @$fp ) 
			{
    			$line =	str_replace( "\n", "", fgets( $fp, 4096 ) );
				
				// is it an	opara bookmark file?
				if ( preg_match( '/Opera Hotlist version 2.0/', $line ) )	
					$browser = 'opera';
					
				// is it a ns bookmark file?
				if ( preg_match( '/<!DOCTYPE NETSCAPE-Bookmark-file-1>/i', $line ) ) 
					$browser = 'netscape';
					
				fclose( $fp );
				
				if ( empty( $browser ) )
					return PEAR::raiseError( "Invalid bookmark format." );
			}
			else
			{
				return PEAR::raiseError( "File error." );
			}
		}
		else if ( is_dir( $url ) )
		{
			// open	directory
			$d = @dir( $url );
			
			// TODO: pick first file which is not '..' or '.' or search recursively til file is found,
			// then perform some sniffing
			
			$d->close();
		}
		else
		{
			return PEAR::raiseError( "Invalid bookmark format." );
		}
			
				
		// return either 'opera', 'netscape' or 'ie'
	}
	
	/**
	 * Parse a bookmark file.
	 *
	 * @access 	 public
	 */
	function parse( $url, $folderID, $urlFunction, $folderFunction )
	{
		$browser = $this->checkFormat( $url );
		
		if ( PEAR::isError( $browser ) )
			return $browser;
		
		switch ( $browser )
		{
			case 'opera':
				return $this->parseOpera( $url, $folderID, $urlFunction, $folderFunction );
				
			case 'netscape':
				return $this->parseNetscape( $url, $folderID, $urlFunction, $folderFunction );
				
			case 'ie':
				return $this->parseIE( $url, $folderID, $urlFunction, $folderFunction, true );
		}
	}

	/**
 	 * Parses an Opera bookmark file.
 	 *
 	 * Parses the file, default name for bookmark file is "Opera6.adr"
 	 * Tested with Opera 6.
 	 *
 	 * @access	 public
	 *
	 * @param    String		$url   url to the bookmark file
	 * @param    int		$folderID  id of the root folder
	 * @param    String		$urlFunction    the function name to be called when an url is parsed
	 * @param    String		$folderFunction the function name to be called when a bookmark is parsed
	 *
	 * @return   int/Error
	 */
	function parseOpera( $url, $folderID, $urlFunction, $folderFunction ) 
	{
		$this->foldersParsed = 0;
		$this->urlsParsed    = 0;
	
		$depth   = 0;
		$parents = array();
	
		array_push( $parents, $folderID );
	
		// is it a file?
		if ( is_file( $url ) ) 
		{
			// open	file
			$fp	= @fopen( $url, "r-" );
		
			if ( @$fp ) 
			{
    			// is it an	opara bookmark file?
    			$line =	str_replace( "\n", "", fgets( $fp, 4096 ) );
    		
				if ( preg_match( '/Opera Hotlist version 2.0/', $line ) )	
				{
        			// insert Opera	root in	DB
        			// read	lines
        			while ( !@feof( $fp ) ) 
					{
            			$line =	str_replace( "\n", "", fgets( $fp, 4096 ) );
        			
						// folder found
        				if ( preg_match( '/^[\s]*#folder/i', $line ) ) 
						{
                			// extract the name
                			$line =	str_replace( "\n", "", fgets( $fp, 4096 ) );
                        	$tmp  = explode( "=", $line, 2 );
                        	$name = $tmp[1];
                		
							// extract create creation date
                			$line    = str_replace( "\n", "", fgets( $fp, 4096 ) );
                        	$tmp     = explode( "=", $line, 2 );
                        	$created = $tmp[1];
							
                			// extract the visit date
                			$line    = str_replace( "\n", "", fgets( $fp, 4096 ) );
                        	$tmp     = explode( "=", $line, 2 );
                        	$visited = $tmp[1];
                        
							// insert into db                    
                        	$this->foldersParsed++;
                        	
							$this->_callFunction( $folderFunction, false, 
								array(
									"name"    => $name,
									"descr"   => "",
									"created" => $created,
									"parent"  => $parents[$depth],
									"added"   => $created,
									"visited" => $visited
								), $depth, $this->foldersParsed 
							);

                        	// current id of folder is stored in a stack
        					array_push( $parents, $folderID + $this->foldersParsed );
                        	$depth++;
        				}
        				// bookmark	found
        				else if ( preg_match( '/^#url/i', $line ) ) 
						{
        					// extract url 
                			$line  = str_replace( "\n", "", fgets( $fp, 4096 ) );
                        	$tmp   = explode( "=", $line, 2 );
        					$descr = $tmp[1];
                		
							// extract the name
                			$line  = str_replace( "\n", "", fgets( $fp, 4096 ) );
                        	$tmp   = explode( "=", $line, 2 );
        					$url   = $tmp[1];
                		
							// extract create creation date
                			$line    = str_replace( "\n", "", fgets( $fp, 4096 ) );
                        	$tmp     = explode( "=", $line, 2 );
                        	$created = $tmp[1];
                		
							// extract the visit date
							$line    = str_replace( "\n", "", fgets( $fp, 4096 ) );
							$tmp     = explode( "=", $line, 2 );
							$visited = $tmp[1];
                        
							// insert into db
                        	$this->urlsParsed++;
                        
							$this->_callFunction( $urlFunction, false, 
								array( 
									"url"     => $url,
									"descr"   => $descr,
									"parent"  => $parents[$depth],
									"added"   => $created,
									"visited" => $visited
								), $depth, $this->urlsParsed 
							);
        				}
        				// folder closed
        				else if ( preg_match( '/^[\s]*-/', $line ) )
						{
        					array_pop( $parents );
        					$depth--;
        				}
        			}
        		
					fclose( $fp );
        			return true;
        		} 
				else 
				{
					return PEAR::raiseError( "Wrong header." );
    			}
	    	} 
			else 
			{
				return PEAR::raiseError( "File error." );
			}   
		} 
		else 
		{
			return PEAR::raiseError( "Wrong header." );
    	}
	}

	/**
	 * Parses a Netscape bookmark file.
	 *
	 * Parses the file, default name is "bookmarks.html".
	 * Tested with Netscape 4.x and 6.x.
	 *
	 * @access   public
	 *
	 * @param    String      $url   url to the bookmark file
	 * @param    int         $folderID  id of the root folder
	 * @param    String    $urlFunction    the function name to be called when an url is parsed
	 * @param    String    $folderFunction the function name to be called when a bookmark is parsed
	 *
	 * @return   int/Error
	 */
	function parseNetscape( $url, $folderID, $urlFunction, $folderFunction ) 
	{
		$this->foldersParsed = 0;
		$this->urlsParsed    = 0;
	
		$depth   = 0;
		$parents = array();
	
		array_push( $parents, $folderID );
	
		// is it a file?
		if ( is_file( $url ) ) 
		{
			// open	file
			$fp	= @fopen( $url, "r-" );
    	}
    
		if ( @$fp ) 
		{
			// is it a ns bookmark file?
        	$line =	str_replace( "\n", "", fgets( $fp, 4096 ) );
		
			if ( !preg_match( '/<!DOCTYPE NETSCAPE-Bookmark-file-1>/i', $line ) ) 
				return PEAR::raiseError( "Wrong header." );
		
			// insert NS root in DB
			// read	lines
		
			while ( !@feof( $fp ) ) 
			{
            	$line =	str_replace( "\n", "", fgets( $fp, 4096 ) );
			
				// extract add_date
				preg_match( "(/ADD_DATE=\"([^\"]*/i))", $line, $match );
				@$added = $match[1];
			
				// folder found
				if ( preg_match( '/<H3[^>]*>(.*)<\/H3>/i', $line, $match ) )
				{
                	$name = $match[1];
                	$this->foldersParsed++;
                	
					$this->_callFunction( $folderFunction, false, 
						array(
							"name"   => $name,
							"parent" => $parents[$depth],
							"added"  => $added
						), $depth, $folderID + $this->foldersParsed 
					);
				
					array_push( $parents, $folderID + $this->foldersParsed );
					$depth++;
				}
				// bookmark	found
				else if	( preg_match( '/<A HREF="([^"]*)[^>]*>(.*)<\/A>/i', $line,	$match ) )
				{
					// extract url and descr
					$url   = $match[1];
					$descr = $match[2];
				
					// extract dates
					preg_match( "/ADD_DATE=\"([^\"]*)/i", $line, $match );
					@$add_date = $match[1];
				
					preg_match( "/LAST_VISIT=\"([^\"]*)/i", $line, $match );
					@$visited = $match[1];
					
					preg_match( "/LAST_MODIFIED=\"([^\"]*)/i", $line, $match );
					@$modified = $match[1];
                
					// insert into db
					$this->urlsParsed++;
					
					$this->_callFunction( $urlFunction, false, 
						array( 
							"url"      => $url,
							"descr"    => $descr,
							"parent"   => $parents[$depth],
							"added"    => $add_date,
							"modified" => $modified,
							"visited"  => $visited
						), $depth, $this->urlsParsed 
					);
        		}
				// folder closed
				else if	( preg_match( '/<\/DL>/i', $line ) )	
				{
					array_pop( $parents );
					$depth--;
				}
			}
		
			fclose( $fp );
		} 
		else 
		{
			return PEAR::raiseError( "File error." );
    	}
	}

	/**
	 * Parses an IE bookmarks folder.
	 *
	 * @access   public
	 *
	 * @param    String      $url   url to the bookmark file
	 * @param    int         $folderID  id of the root folder
	 * @param    String    $urlFunction    the function name to be called when an url is parsed
	 * @param    String    $folderFunction the function name to be called when a bookmark is parsed
	 * @param    boolean     $firstCall  only true, upon the first call
	 *
	 * @return   int/Error
	 */
	function parseIE( $url, $folderID, $urlFunction, $folderFunction, $firstCall = true )
	{
		if ( $firstCall ) 
		{
    		$this->foldersParsed = 0;
	    	$this->urlsParsed    = 0;
		}
	
		static $depth = 0;
	
		// open	directory
		$d = @dir( $url );
	
		while ( $entry = $d->read() )
		{
			// is not .	or ..
			if ( $entry != "." && $entry != ".." ) 
			{
				// is it a dir?
				if ( is_dir( "$url/$entry" ) ) 
				{
			    	$depth++;
                	
					$this->_callFunction( $folderFunction, false,
						array( 
							"name"   => $entry,
							"descr"  => "",
							"parent" => $folderID
						), $depth, $this->foldersParsed + $depth
					);
					
					// visit it
					$this->parseIE( "$url/$entry", $folderID + 1, $urlFunction, $folderFunction, false );
                	$this->foldersParsed++;
					$depth--;
			
					// is there	a ie internet shortcut?
				} 
				else if ( preg_match( "/.url$/i", $entry ) ) 
				{
            		$modified =	"";
            		$lineno   =	0;
            	
					// open	it
            		$fp	= @fopen( "$url/$entry", "r-" );
            	
					if ( @$fp ) 
					{
            			$name =	substr( basename( $entry ), 0, strlen( basename( $entry ) ) - 4 );
            		
						while ( !@feof( $fp ) ) 
						{
            				$lineno++;
                        	$line =	str_replace( "\n", "", @fgets( $fp, 4096 ) );
            			
							// extract url
            				if ( preg_match( "/^url=/i", $line ) ) 
            					$href = trim( substr( $line, 4 ) );
							else if ( preg_match( "/^modified=/i", $line ) ) 
            					$modified =	trim( substr( $line, 9 ) );
            			}
                    
						// insert into db
                        $this->urlsParsed++;
                        
						$this->_callFunction( $urlFunction, false, 
							array( 
								"url"    => $href,
								"descr"  => $name,
								"parent" => $folderID + $this->foldersParsed
							), $depth, $this->urlsParsed
						);
            		} 
					else 
					{
            			return PEAR::raiseError( "File error." );
            		}
            	
					fclose ( $fp );
				}
			}
		}
	
		$d->close();
	}


	// private methods
	
	/**
 	 * @access   private
 	 *
	 * @param    String  $functionName
	 * @param    boolean $abortAmbiguous  
	 * @param    mixed   the params for the function
	 *
	 * @return   Integer    -1 when an error occurs or the retrun value of the function
	 */
	function _callFunction( $functionName, $abortAmbiguous = false ) 
	{
      	// # of params
        $count = func_num_args();
        
		// array for passed on params
        $params = array();
        
		for ( $i = 2; $i < $count; $i++ )
            $params[$i - 1] = func_get_arg( $i );
        
        // flags if funcrion exists in a class or outside
        if ( is_array( $functionName ) )
            return call_user_func_array( $functionName, $params );
        
        $isInside  = method_exists( @$this, $functionName );
        $isOutside = function_exists( $functionName );
		
        // do we need to abort if function name is ambigous?
        if ( $abortAmbiguous ) 
		{
            if ( $isInside && $isOutside )
                return -1;
        }
		
        // call the inner method first
        if ( $isInside ) 
            return call_user_func_array( array( $this, $functionName ), $params );
		// or the "outer" one
		else if ( $isOutside ) 
            return call_user_func_array( $functionName, $params );
		// function does not exist at all
		else if ( $functionName ) 
            return -1;
	}
} // END OF BookmarkParser

?>
