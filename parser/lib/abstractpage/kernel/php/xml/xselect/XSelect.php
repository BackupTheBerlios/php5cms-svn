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


using( 'xml.xselect.XSelect_Tree' );
using( 'util.Debug' );


/**
 * @package xml_xselect
 */
 
class XSelect extends PEAR
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function XSelect()
	{
		$this->debug = new Debug();
		$this->debug->Off();
		
		// string query
		$this->query = '';
		$this->query_type = '';
		
		// arrays for each segment of the query
		$this->select = array();
		$this->update = array();
		$this->from   = "";
		$this->where  = array();
		
		// got to fix this!
		$this->where_tagname = '';
		
		// xmltree object
		$this->xmltree = new XSelect_Tree();
	}
	
	
	/**
	 * @access public
	 */
	function loadXML( $file )
	{
		// parse xml file
		if ( $xml_err = $this->xmltree->parse( $file ) )
			return PEAR::raiseError( "XML File Error: " . $xml_err );
	}
	
	/**
	 * @access public
	 */
	function executeQuery( $query )
	{
		if ( ereg( "^update", $query ) )
		{
			// translate update queries here
		}
		else
		{
			$res = $this->translateQuery( $query );
			
			if ( PEAR::isError( $res ) )
				return $res;
				
			$paths = $this->getValidpaths();
			
			if ( !$paths || PEAR::isError( $paths ) )
				return PEAR::raiseError( "No valid paths returned." );
			
			$result = $this->getResultset( $paths );
			
			if ( !$result || PEAR::isError( $result ) )
				return PEAR::raiseError( "No result set returned." );
			else
				return $result;
		}
	}

	/**
	 * @access public
	 */	
	function prestr( $string,$delim )
	{
		return substr( $string, 0, strlen( $string ) - strlen( strstr( $string, "$delim" ) ) );
	}

	/**
	 * @access public
	 */	
	function translateQuery( $query )
	{
		/*
		"select" array structure:
		$array["tagname"][] = arg
		
		"from" array structure:
		$array[] = value
		
		"where" array/var structure:
		$where_tagname
		$array["$argument|operator"] = arg,arg,arg
		
		translator's current query limitations:
		. only selecting cdata and attributes
		. only one "select" element
		. only one "where" element
		. only one "from" element
		*/
		
		$select_string = trim( str_replace( "select", "", $this->prestr( $query, "from" ) ) );
		
		// check for 'where' and use it to get 'from'
		if ( ereg( "where", $query ) )
		{
			$this->from   = trim( str_replace( "from",  "", strstr( $this->prestr( $query, "where" ), "from" ) ) );
			$where_string = trim( str_replace( "where", "", strstr( $query, "where" ) ) );
		}
		else
		{ 
			$from_string = trim( str_replace( "from", "", strstr( $query, "from" ) ) );
			$this->from  = $from_string;
			$this->debug->Message( "Found the 'from' element: " . $from_string );
		}
		
		// prep select criteria
		// where tagname is everything preceding the first lp
		$select_tagname = trim( $this->prestr( $select_string, "(" ) );
		
		// strip off the first lp
		$select_string = substr( trim( $select_string), strpos( $select_string, "(") + 1, strlen( strstr( $select_string, "(" ) )- 1 );
		
		// strip off the last rp
		if ( !ereg("\)$", $select_string ) )
			return PEAR::raiseError( "You seem to have forgotten an ending parentheses in your expression!" );
		else
			$select_string = substr( trim( $select_string ), 0, strlen( $select_string ) -1 );

		// turn args into array
		$select_args = split( ",", str_replace( " ", "", $select_string ) );
		$this->select["$select_tagname"] = $select_args;
		
		if ( $where_string )
		{
			// where tagname is everything preceding the first lp
			$this->where_tagname = trim( $this->prestr( $where_string, "(") );
			
			// strip off the first lp
			$where_string = substr( trim( $where_string ), strpos( $where_string, "(") + 1, strlen( strstr( $where_string, "(") ) - 1 );
			
			// strip off the last rp
			if ( !ereg("\)$", trim( $where_string ) ) )
				return PEAR::raiseError( "You seem to have forgotten an ending parentheses in your expression!" );
			else
				$where_string = substr( trim( $where_string ), 0, strlen( $where_string ) - 1 );
			
			// get where args
			$args = $this->getArgs( trim( $where_string ) );
			
			// loop thru args, dissecting into arrays
			foreach( $args as $arg )
			{
				$where_args = $this->dissectArg( $arg );
				
				if ( PEAR::isError( $where_args ) )
					return $where_args;
					
				$arg_array_string = implode( ",", $where_args[1] );
				$this->where["$where_args[0]"] = $arg_array_string;
			}
		}
	}
	
	/**
	 * This function works for update, select and where segments.
	 * It takes a string of expressions, separated by commas, and returns an array
	 * with each expression as an element.
	 * example string: @name = ("x","y"), cdata like "tombed", @url!=("z", "a")  returns:
	 * array("@name = ("x","y")", cdata like "tombed", @url!=("z", "a"));
	 *
	 * @access public
	 */
	function getArgs( $string )
	{
		while( $string != "" )
		{
			// test for commas
			if ( ereg( ",", $string ) )
			{
				// test to see if there is a left parentheses preceding the first comma
				if ( ereg( "\(", $this->prestr( $string, "," ) ) )
				{
					// everything before the first right parentheses is an arg
					$args[] = $this->prestr( $string, ")" );
					
					// chop off everything preceding and incuding the first rp
					$string = substr( trim( $string ), strpos( $string, ")" ) + 1, strlen( strstr( $string, ")" ) ) - 1 );
					
					// chop off everything preceding and incuding the first comma
					if ( !$string = substr( trim( $string ), strpos( $string, "," ) + 1, strlen( strstr( $string, "," ) ) - 1 ) )
						break;
				}
				else if ( ereg( ",", $this->prestr( $string, "(" ) ) )
				{
					$args[] = $this->prestr( $string, "," );

					// chop off everything preceding and incuding the first comma
					if ( !$string = substr( trim( $string ), strpos( $string, "," ) + 1, strlen( strstr( $string, "," ) ) - 1 ) )
						break;
				}
			}
			else
			{
				$args[] = $string;
				break;
			}
		}
		
		return $args;
	}
	
	/**
	 * Returns an array. first element is the argument and the operator, separated by |
	 * the second element is an array of values/criteria.
	 *
	 * @access public
	 */
	function dissectArg( $string )
	{
		// contains operator?
		if ( ereg( "(like|=|!=|!like)", $string ) )
		{
			// contains parentheses?
			if ( ereg ("\(", $string ) )
			{
				// everything preceding the lp
				$arg_op = $this->getOperator( $this->prestr( $string, "(" ) );
				
				if ( PEAR::isError( $arg_op ) )
					return $arg_op;
				
				// everything after and including the lp
				$string = strstr( $string, "(" );

				// chop off everything preceding and incuding the first lp
				$valstring = substr( trim( $string ), strpos( $string, "(" ) + 1, strlen( strstr( $string, "(" ) ) - 1 );
				$vals = explode( ",", str_replace("\"","", stripslashes($valstring)));
				
				foreach( $vals as $val )
					$arg_vals[] = trim( $val );
			}
			else
			{
				$arg_op = $this->getOperator( $this->prestr( stripslashes( $string ), "\"" ) );
				
				if ( PEAR::isError( $arg_op ) )
					return $arg_op;
					
				$val = str_replace( "\"","", stripslashes(strstr($string,"\"" ) ) );
				$arg_vals[] = trim( $val );
			}
		}
		else
		{
			return PEAR::raiseError( "No operator or bad operator." );
		}
		
		return array( "$arg_op", $arg_vals );
	}
	
	/**
	 * Takes a string: "cdata !="
	 * and returns string: "cdata|!="
	 *
	 * @access public
	 */
	function getOperator( $string )
	{
		// !=
		if ( ereg( "!=", $string ) )
		{
			$arg    = trim( $this->prestr( $string, "!" ) );
			$op     = trim( strstr( $string, "!" ) );
			$arg_op = $arg . "|" . $op;
		}
		// =
		else if ( ereg ( "=", $string ) )
		{
			$arg    = trim( $this->prestr( $string, "=" ) );
			$op     = trim( strstr( $string, "=" ) );
			$arg_op = $arg . "|" . $op;
		}
		// !like
		else if ( ereg( "!like", $string ) )
		{
			$arg    = trim( $this->prestr( $string, "!like" ) );
			$op     = trim( strstr( $string, "!like" ) );
			$arg_op = $arg."|".$op;
		}
		// like		
		else if ( ereg( "like", $string ) )
		{
			$arg    = trim( $this->prestr( $string, "like" ) );
			$op     = trim( strstr( $string, "like" ) );
			$arg_op = $arg . "|" . $op;
		}
		else
		{
			return PEAR::raiseError( "No operator or bad operator." );
		}

		return $arg_op;
	}
	
	/**
	 * Here begins the code for comparing our query against the data and forming a result-set 
	 * with the matches.
	 *
	 * @access public
	 */
	function getValidpaths()
	{	
		if (!isset($this->xml_err) || !$this->xml_err)
		{
			$xmltree = $this->xmltree;

			if ( !$parent_path = $xmltree->pathBykey( $this->from ) )
				return PEAR::raiseError( "Couldn't get the parent path for " . $this->from );
			
			$this->debug->Message( "Got parent path for " . $this->from . ": " . $parent_path );
			$dirList = $xmltree->getDirlist( $parent_path );
			$this->debug->Message( "Got Dirlist." );
			
			foreach( $dirList as $dirEntry => $quantity )
			{
				for( $i = 0; $i < $quantity; $i++ )
				{
					if ( $this->select["$dirEntry"] )
					{
						$potPath = $parent_path . "/" . $dirEntry . "(" . ( $i + 1 ) . ")";
						$potentialPaths[$potPath] = 1;
					}
				}
			}
			
			if ( !is_array( $potentialPaths ) )
				return PEAR::raiseError( "Found no matches for that query." );
			
			$count = count( $potentialPaths );
			$this->debug->Message( "XSelect", "Found potential paths: " . $count );
			
			// okay, take a deep breath. we are going to loop thru the where conditions, weeding out bad paths
			if ( count( $this->where ) > 0 )
			{
				$this->debug->Message( "Processing the 'where' segment." );
				
				foreach( $this->where as $arg_op => $values )
				{	
					// first, split up the arg and op
					$arg = $this->prestr( $arg_op, "|" );
					$op  = str_replace( "|", "", strstr( $arg_op, "|" ) );
					
					// make array of values
					$val_array = explode( ",", $values );
					
					// loop thru values, using operator to accept or decline
					foreach( $val_array as $val )
					{
						// loop through potential paths, comparing
						foreach( $potentialPaths as $potPath => $quantity )
						{
							if ( $arg == "cdata" )
							{
								// loop thru operators
								if ( $op == "=" )
								{
									if ( $val == $xmltree->getCdata( $potPath ) )
									{
										$paths[$potPath] = 1;
										unset( $potentialPaths[$potPath] );
									}
								}
								else if ( $op == "!=" )
								{
									if ( $val != $xmltree->getCdata( $potPath ) )
									{
										$paths[$potPath] = 1;
										unset( $potentialPaths[$potPath] );
									}
								}
								else if( $op == "like" )
								{
									if ( ereg("$val", $xmltree->getCdata( $potPath ) ) )
									{
										$paths[$potPath] = 1;
										unset( $potentialPaths[$potPath] );
									}
								}
								else if( $op == "!like" )
								{
									if ( !ereg( "$val", $xmltree->getCdata( $potPath ) ) )
									{
										$paths[$potPath] = 1;
										unset( $potentialPaths[$potPath] );
									}
								}
								else
								{
									return PEAR::raiseError( "Your operator isn't accepted here." );
								}
							}
							else if( ereg( "^@", $arg ) )
							{
								// is the attribute actually exists
								if ( $xmlval = $xmltree->getAttribute( $potPath, str_replace( "@", "", $arg ) ) )
								{	
									// loop thru operators
									if ( $op == "=" )
									{
										if ( $val == $xmlval )
										{
											$paths[$potPath] = 1;
											unset( $potentialPaths[$potPath] );
										}
									}
									else if ( $op == "!=" )
									{
										if ( $val != $xmlval )
										{
											$paths[$potPath] = 1;
											unset( $potentialPaths[$potPath] );
										}
									}
									else if( $op == "like" )
									{
										if ( ereg( "$val", $xmlval ) )
										{
											$paths[$potPath] = 1;
											unset( $potentialPaths[$potPath] );
										}
									}
									else if ( $op == "!like" )
									{
										if ( !ereg( "$val", $xmlval ) )
										{
											$paths[$potPath] = 1;
											unset( $potentialPaths[$potPath] );
										}
									}
									else
									{
										return PEAR::raiseError( "Unknown operator." );
									}
								}
								else
								{
									return PEAR::raiseError( "Attribute is not valid for path " . $potPath );
								}
							}
							else
							{
								return PEAR::raiseError( $arg . ": " . "Invalid argument name. Must use 'cdata' or a valid attribute name." );
							}
						}
					}
				}
				
				if ( is_array( $paths ) )
				{
					$count = count( $paths );
					$paths = array_keys( $paths );
				}
				else
				{
					return PEAR::raiseError( "You had no matches for that query." );
				}
			}
			else
			{
				$paths = array_keys( $potentialPaths );
				$count = count( $paths );
			}
		}
		else
		{
			return PEAR::raiseError( "Your file could not be parsed." );
		}
		
		return $paths;
	}
	
	/**
	 * Uses paths to create array of requested data.
	 *
	 * @access public
	 */
	function getResultset( $paths )
	{
		foreach ( $paths as $path )
		{
			foreach( $this->select as $tagname => $args )
			{
				foreach( $args as $arg )
				{
					if ( $arg == "*" )
					{
						$result[$path] = $this->xmltree->nodes[$path]["attributes"];
						$result[$path]["cdata"] = $this->xmltree->getCdata( $path );
					}
					else if ( $arg == "cdata" )
					{
						// get cdata for path and add to array
						$result[$path]["cdata"] = $this->xmltree->getCdata( $path );
					}
					else if ( ereg("^@", $arg ) )
					{
						// get attribute data and add to array
						$arg = str_replace( "@", "", $arg );
						$result[$path]["$arg"] = $this->xmltree->getAttribute( $path, str_replace( "@", "", $arg ) );
					}
					else
					{
						return PEAR::raiseError( "Invalid request: " . $arg );
					}
				}
			}
		}
		
		return $result;
	}
} // END OF XSelect

?>
