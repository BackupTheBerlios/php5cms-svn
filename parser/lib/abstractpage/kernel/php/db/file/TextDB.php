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
 * TextDB
 * three basic types: int, str, arr
 *
 * int file format: 
 *   number [4 bytes]
 *
 * str file format:
 *   length [4 bytes]
 *   string [length]
 *
 * arr file format:
 *   length [4 bytes]
 *     type [raw - 3 bytes]
 *     element 1 [type]
 *     ...
 *     element n [type]
 *
 * data file format:
 *   key [str]
 *   num fields [int]
 *     field name 1 [str]
 *     field type 1 [raw - 3 bytes]
 *     ...
 *     field name n [str]
 *     field type n [raw - 3 bytes]
 *   data...
 *
 * @package db_file
 */

class TextDB extends PEAR
{
	/**
	 * are we active?
	 * @access public
	 */
    var $active;
	
	/**
	 * main file pointer
	 * @access public
	 */
    var $fp;
	
	/**
	 * index file pointer
	 * @access public
	 */
    var $idx;
	
	/**
	 * array of fields
	 * @access public
	 */
    var $a;
	
	/**
	 * number of fields
	 * @access public
	 */
    var $fields;
	
	/**
	 * number of elements
	 * @access public
	 */
    var $size;
	
	/**
	 * key field
	 * @access public
	 */
    var $key;
	
	/**
	 * current position
	 * @access public
	 */
    var $pos;
	
	/**
	 * auto number key field?
	 * @access public
	 */
    var $auto;
	
	/**
	 * @access public
	 */
    var $fpname;
	
	/**
	 * @access public
	 */
    var $idxname;
	
	/**
	 * @access public
	 */
    var $orderby;
	
	/**
	 * @access public
	 */
    var $ordertype;

	
    /**
	 * Constructor
	 *
	 * @access public
	 */ 
    function TextDB()
    {
        $this->active = false;
    }
	

    /** 
	 * Opens the file given, and sets everything up.
	 *
	 * @access public
	 */
    function open( $file )
    {
        $this->active  = false;
        $this->size    = 0;
        $this->pos     = 0;
        $this->auto    = false;
        $this->fpname  = $file . ".dat";
        $this->idxname = $file . ".idx";
		
        $this->fp = @fopen( $this->fpname, "r+" );
        
		if ( !$this->fp )
            return false;
			
        $this->idx = @fopen( $this->idxname, "r+" );
        
		if ( !$this->idx )
        {
            fclose( $this->fp );
            return false;
        }

        // get the key
        flock( $this->fp, 1 );
        $tempkey = $this->tdbread( "str" );
        flock( $this->fp, 3 );

        // is it auto?
        if ( substr( $tempkey, 0, 1 ) == "@" )
        {
            $this->auto = true;
            $this->key  = substr( $tempkey, 1 );
        }
		else
		{
			$this->key = $tempkey;
		}

        // get the number of fields
        flock( $this->fp, 1 );
        $this->fields = $this->tdbread( "int" );

        // read in the fields to our array
        for ( $i = 0; $i < $this->fields; $i++ )
            $this->a[] = array( $this->tdbread( "str" ), fread( $this->fp, 3 ) );
        
		flock( $this->fp, 3 );

        // find out how many records we have
        $this->size   = filesize( $this->idxname ) / 4;
        $this->active = true;
		
        return true;
    }

    /** 
	 * Create a new DB file (erasing the old one if it exists).
	 *
	 * @access public
	 */
    function create( $file, $newa, $key = false )
    {
        $this->active  = false;
        $this->size    = 0;
        $this->pos     = 0;
        $this->auto    = false;
        $this->fpname  = $file . ".dat";
        $this->idxname = $file . ".idx";
        $this->fp      = fopen( $this->fpname, "w+" );
        
		if ( !$this->fp )
            return false;
			
        $this->idx = fopen( $this->idxname, "w+" );
		
        if ( !$this->idx )
        {
            fclose( $this->fp );
            return false;
        }
		
        if ( $key == true )
            $newkey = $newa[0][0];
        else
            $newkey = "null";
			
        if ( substr( $newkey, 0, 1 ) == "@" )
        {
            $this->auto = true;
            $this->key  = substr( $newkey, 1 );
            $newa[0][0] = substr( $newa[0][0], 1 );
        }
		else
		{
			$this->key = $newkey;
		}
        
		flock( $this->fp, 2 );
        $this->tdbwrite( $newkey, "str" );
        $this->tdbwrite( count( $newa ), "int" );
        
		for ( $i = 0; $i < count( $newa ); $i++ )
        {
            $this->tdbwrite( $newa[$i][0], "str" );
            fwrite( $this->fp, $newa[$i][1], 3 );
        }
		
        flock( $this->fp, 3 );
		
        $this->a      = $newa;
        $this->fields = count( $newa );
        $this->active = true;
		
        return true;
    }

    /**
	 * Close the current DB.
	 *
	 * @access public
	 */
    function close()
    {
        if ( $this->active )
        {
            fclose( $this->fp  );
            fclose( $this->idx );
			
            $this->fields    = 0;
            $this->a         = 0;
            $this->key       = "";
            $this->size      = 0;
            $this->pos       = 0;
            $this->auto      = false;
            $this->active    = false;
            $this->fpname    = "";
            $this->idxname   = "";
            $this->orderby   = "";
            $this->ordertype = "";
        }
    }

    /** 
	 * Executes an sql query.
	 *
	 * @access public
	 */
    function exec($q)
    {
        if ( $this->active )
        	return $this->parse( $q );
		
        return false;
    }

	/**
	 * @access public
	 */
    function parse( $q )
    {
        $command = $this->nextword( &$q );
		
        switch ( strtolower( $command ) )
        {
            case "create" :
                break;
				
            case "drop" :
                break;
				
            case "alter" :
                break;
				
            case "rename" :
                break;
				
            case "select" :
                $fields = $this->nextwords( &$q );
				
                while ( strlen( $q ) > 0 )
                {
                    $next = $this->nextword( &$q );
					
                    switch ( strtolower( $next ) )
                    {
                        case "where" :
                            $where = $this->nextwhere( &$q, $w );
                            break;
							
                        case "order" :
                            $this->nextword( &$q );
                            $orderby = $this->nextorder( &$q );
                            break;
							
                        case "limit" :
                            $limit = $this->nextwords( &$q );
                            break;
                    }
                }

                for ( $i = 0; $i < $this->size; $i++ ) 
                {
					if ( $odir == "desc" )
						$tmp = $this->get( $this->size - 1 - $i );
					else
	                    $tmp = $this->get( $i );

                    if ( isset( $where ) && $where )
                        $add = $this->expr( $tmp[$w[0][fld]], $w[0][oper], $w[0][crit] );
                    else
                        $add = true;
                    
                    if ( $fields[0] == "*" )
                    {
                        if ( $add )
							$ret[] = $tmp;
                    }
                    else
                    {
                        if ( $add )
                        {
                            for ( $j = 0; $j < count( $fields ); $j++ )
                                $tmparr[$fields[$j]] = $tmp[$fields[$j]];
                            
							$ret[] = $tmparr;
                            unset( $tmparr );
                        }
                    }
                }
				
                if ( is_array( $orderby ) )
   					$ret = $this->tdbsort( $ret, $orderby );
 
                if ( is_array( $limit ) )
                {
                    for ( $e = 0; $e < ( ( count( $ret ) < $limit[0] )? count( $ret ) : $limit[0] ); $e++ )
                        $newret[] = $ret[$e];
						
                    $ret = $newret;
                    unset( $newret );
                }

                return $ret;
				
            case "insert" :
                break;
				
            case "delete" :
                break;
				
            case "replace" :
                break;
				
            case "update" :
                break;
        }
		
        return true;
    }

	/**
	 * @access public
	 */
    function tdbcmp( $a, $b )
    {
        return $this->compare( $a, $b, $this->orderby, $this->ordertype );
    }

	/**
	 * @access public
	 */
    function tdbsort( $arr, $orderby )
    {
        for ( $i = count( $orderby ) - 1; $i >= 0; $i-- )
        {
            $j = 0;
            while ( $this->a[$j][0] != $orderby[$i][val] )
				$j++;
			
            $this->ordertype = $this->a[$j][1];
            $this->orderby   = $orderby[$i];
			
            usort( $arr, array( $this, "tdbcmp" ) );
        }
		
        return $arr;
    }

	/**
	 * @access public
	 */
    function get( $elem )
    {
        if ( $this->active )
        {
            if ( $elem < 0 )
				return false;
			
            $this->lockf( 1 );
            fseek( $this->idx, $elem * 4 );
            $pos = $this->bin2dec( fread( $this->idx, 4 ) );
            fseek( $this->fp, $pos );
			
            for ( $i = 0; $i < $this->fields; $i++ )
				$arr[$this->a[$i][0]] = $this->tdbread( $this->a[$i][1] );
			
            $this->pos = $elem + 1;
            $this->unlockf();
            
			return $arr;
        }
		
        return false;
    }

    /**
	 * Finds all matching things.
	 *
	 * @access public
	 */
    function findall( $invar = "", $tofind = "" )
    {
        for ( $i = 0; $i < $this->size; $i++ )
        {
            $tmp = $this->get( $i );

            if ( ( $invar == "" && $tofind == "" ) || ( $tmp[$invar] == $tofind ) )
                $arr[] = $tmp;
        }
		
        if ( count( $arr ) > 0 )
            return $arr;
			
        return false;
    }

	/**
	 * @access public
	 */
    function findcount( $invar, $tofind )
    {
        $cnt = 0;
        
		for ( $i = 0; $i < $this->size; $i++ )
        {
            $tmp = $this->get( $i );
			
            if ( $tmp[$invar] == $tofind )
                $cnt++;
        }
		
        return $cnt;
    }

	/**
	 * @access public
	 */
    function find( $invar, $tofind )
    {
        for ( $i = 0; $i < $this->size; $i++ )
        {
            $tmp = $this->get($i);
            
			if ( $tmp[$invar] == $tofind )
                return $i;
        }
		
        return -1;
    }

	/**
	 * @access public
	 */
    function erase( $elem )
    {
        if ( $this->active )
        {
            flock( $this->idx, 2 );
            $len = filesize( $this->idxname );
            fseek( $this->idx, 0 );
            $buf = fread( $this->idx, $len );
            fclose( $this->idx );
            $this->idx = fopen( $this->idxname, "w+" );
            $newidx = substr( $buf, 0, $elem * 4 ) . substr( $buf, $elem * 4 + 4 );
            fwrite( $this->idx, $newidx, $len - 4 );
            $this->size--;
            flock( $this->idx, 3 );

            return true;
        }

        return false;
    }
    
	/**
	 * @access public
	 */
    function put( $arr, $edit = false )
    {
        if ( $this->active )
        {
            fclose( $this->fp );
            fclose( $this->idx );
			
            $this->fp  = fopen( $this->fpname,  "r+" );
            $this->idx = fopen( $this->idxname, "r+" );
            $arr = array_values($arr);

            // check to make sure we don't have a dupe
            if ( !$edit )
            {
                if ( $this->key != "null" && $this->auto == false )
                {
                    $foo = $this->find( $this->key, $arr[0] );
                    
					if ( $foo > -1 )
                    {
						// echo( "duplicate key value (" . $arr[0] . ")." );
                        return false;
                    }
                }
            }
			
            // go to the end of the file
            fseek( $this->fp, filesize( $this->fpname ) );
            
			// get the position
            $idxnum = $this->dec2bin( ftell( $this->fp ) );

            // auto-increment if need be.
            if ( $this->auto == true && $edit == false )
            {
                if ( $this->size > 0 )
                {
                    $this->lockf( 1 );
                    fseek( $this->idx, filesize( $this->idxname ) -4 );
                    $t = $this->bin2dec( fread( $this->idx, 4 ) );
                    fseek( $this->fp, $t );
                    $t = $this->tdbread( "int" );
                    $arr[0] = $t + 1;
                    $this->unlockf();
                }
				else
				{
					$arr[0] = 0;
				}
            }

            // write the index
            if ( $edit )
            {
                $z = $this->find( $this->a[0][0], $arr[0] );
				
                if ( $z == -1 )
                    return false;
					
                fseek( $this->idx, $z * 4 );
            }
            else
            {
                fseek( $this->idx, filesize( $this->idxname ) );
            }

            flock( $this->idx, 2 );
            fwrite( $this->idx, $idxnum );
            flock( $this->idx, 3 );
            fseek( $this->fp, filesize( $this->fpname ) );

            // and write the data...
            flock( $this->fp, 2 );
			
            for ( $i = 0; $i < count( $arr ); $i++ )
                $this->tdbwrite( $arr[$i], $this->a[$i][1] );
            
			flock( $this->fp, 3 );
            
            if ( !$edit )
                $this->size++;

            return true;
        }
		
        return false;
    }

	/**
	 * @access public
	 */
    function add( $data )
    {
        return $this->put( $data );
    }

	/**
	 * @access public
	 */
    function edit( $data )
    {
        return $this->put( $data, true );
    }

	/**
	 * @access public
	 */
    function addfield( $newfield )
    {
        return $this->rewrite( "add", 0, $newfield );
    }

	/**
	 * @access public
	 */
    function delfield( $fid )
    {
        return $this->rewrite( "del", $fid );
    }

	/**
	 * @access public
	 */
    function editfield( $fid, $newname )
    {
        return $this->rewrite( "edit", $fid, $newname );
    }

	/**
	 * @access public
	 */
    function compact()
    {
        return $this->rewrite();
    }

	/**
	 * @access public
	 */
    function rewrite( $action = "", $fid = "", $fn = "" )
    {
        if ( $this->active )
        {
            fclose( $this->fp );
            $this->fp = fopen( $this->fpname, "r+" );
            flock( $this->fp, 1 );
			
            $newdat = $this->tdbread( "str", true );
            $n      = $this->tdbread( "int" );
			
            if ( $action == "add" )
                $newdat .= $this->dec2bin( $n + 1 );
            else if ( $action == "del" )
                $newdat .= $this->dec2bin( $n - 1 );
            else
                $newdat .= $this->dec2bin( $n );
            
			for ( $j = 0; $j < $n; $j++ )
            {
                $fname = $this->tdbread( "str", true );
                $ftype = fread( $this->fp, 3 );
                
				if ( $action == "del" )
                {
                    if ( $fid != $j )
                        $newdat .= $fname . $ftype;
                }
                else if ( $action == "edit" )
                {
                    if ( $fid == $j )
                        $newdat .= $this->tdbstr( $fn[0] ) . $fn[1];
                    else
                        $newdat .= $fname . $ftype;
                }
                else
				{
					$newdat .= $fname . $ftype;
				}
            }
			
            if ( $action == "add" )
                $newdat .= $this->tdbstr( $fn[0] ) . $fn[1];
 
            fclose( $this->idx );
            $this->idx = fopen( $this->idxname, "r+" );
            flock( $this->idx, 1 );
			
            for ( $i = 0; $i < $this->size; $i++ )
            {
                fseek( $this->fp, $this->bin2dec( fread( $this->idx, 4 ) ) );
                $newidx .= $this->dec2bin( strlen( $newdat ) );

                for ( $j = 0; $j < $n; $j++ )
                {
                    if ( $action == "edit" )
                    {
                        $data = $this->tdbread( $this->a[$j][1] );
						
                        if ( $fid == $j && $this->a[$j][1] != $fn[1] )
                            $newdat .= $this->tdbconv( $data, $fn[1] );
                        else
                            $newdat .= $this->tdbconv( $data, $this->a[$j][1] );
                    }
                    else if ( $action == "del" )
                    {
                        $data = $this->tdbread( $this->a[$j][1], true );

                        if ( $fid != $j )
                            $newdat .= $data;
                    }
                    else
					{
						$newdat .= $this->tdbread( $this->a[$j][1], true );
					}
                }
				
                if ( $action == "add" )
                {
                    if ( $fn[1] == "str" )
                        $y = "";
                    else if ( $fn[1] == "int" )
                        $y = 0;
                    else if ( $fn[1] == "arr" )
                        $y = array( array(0, "int") );
						
                    $newdat .= $this->tdbconv( $y, $fn[1] );
                }
            }
			
            fclose( $this->idx );
            fclose( $this->fp );
            $this->idx = fopen( $this->idxname, "w+" );
            flock( $this->idx, 2 );
            fwrite( $this->idx, $newidx, strlen( $newidx ) );
            $this->fp = fopen( $this->fpname, "w+" );
            flock( $this->fp, 2 );
            fwrite( $this->fp, $newdat, strlen( $newdat ) );
            flock( $this->idx, 3 );
            flock( $this->fp, 3 );

            if ( $action == "add" )
			{
				$this->a[] = $fn;
			}
            else if ( $action == "del" )
            {
                $olda = $this->a;
                unset( $this->a );
				
                for ( $j = 0; $j < count( $this->a ); $j++ )
                {
                    if ( $fid == $j )
                        $this->a[] = $olda[$j];
                }
				
                unset( $olda );
            }
            else if ( $action == "edit" )
			{
				$this->a[$fid] = $fn;
			}
			
            $this->fields = count( $this->a );
            return true;
        }
		
        return false;
    }

    /** 
	 * Converts from old style data files to new style.
	 *
	 * @access public
	 */
    function convert( $fn, $newformat )
    {
        if ( !$this->active )
        {
        	$this->fpname  = $fn . ".dat";
        	$this->idxname = $fn . ".idx";
		
            $this->fp = @fopen( $this->fpname, "r+" );

            if ( $this->fp == false )
                return false;
				
            $this->idx = @fopen( $this->idxname, "r+" );

            if ( $this->idx == false )
            {
                fclose( $this->fp );
                return false;
            }
			
            $this->size = filesize( $this->idxname ) / 4;
            flock( $this->fp, 1 );
			
            $newdat  = $this->tdbstr( chop( fgets( $this->fp, 4096 ) ) );
            $n       = chop( fgets( $this->fp, 4096 ) );
            $newdat .= $this->dec2bin( $n );
			
            for ( $i = 0; $i < $n; $i++ )
            {
                $fieldname = chop( fgets( $this->fp, 4096 ) );
                $newdat   .= $this->tdbstr( $fieldname );
                $newdat   .= $newformat[$i];
                $this->a[] = array( $fieldname, $newformat[$i] );
            }
			
            $newidx = "";
            flock( $this->idx, 1 );
			
            for ( $i = 0; $i < $this->size; $i++ )
            {
                $newidx .= $this->dec2bin( strlen( $newdat ) );
                fseek( $this->fp, $this->bin2dec( fread( $this->idx, 4 ) ) );
                
				for ( $j= 0 ; $j < $n; $j++ )
                    $newdat .= $this->tdbconv( chop( fgets( $this->fp, 4096 ) ), $this->a[$j][1] );
            }
			
            $this->unlockf();
            $this->fflush( "w+" );
            $this->lockf();
            fwrite( $this->fp,  $newdat, strlen( $newdat ) );
            fwrite( $this->idx, $newidx, strlen( $newidx ) );
            $this->unlockf();
			
            return true;
        }
		
        return false;
    }

	/**
	 * @access public
	 */
    function fflush( $mode = "r+" )
    {
        fclose( $this->fp );
        fclose( $this->idx );
        
		$this->fp  = fopen( $this->fpname, $mode );
        $this->idx = fopen( $this->idxname, $mode );
    }

	/**
	 * @access public
	 */
    function lockf( $oper = 2 )
    {
        flock( $this->fp, $oper );
        flock( $this->idx, $oper );
    }

	/**
	 * @access public
	 */
    function unlockf()
    {
        flock( $this->fp, 3 );
        flock( $this->fp, 3 );
    }

	/**
	 * @access public
	 */
    function tdbstr( $data )
    {
        return $this->dec2bin( strlen( $data ) ) . $data;
    }

	/**
	 * @access public
	 */
    function tdbconv( $data, $type )
    {
        switch ( $type )
        {
            case "int":
                if ( !is_long( $data ) )
                    $data = 0;
					
                $str = $this->dec2bin( $data );
                break;
				
            case "str" :
                if ( is_array( $data ) )
				{
                    if ( is_array( $data[0] ) )
                        $data = $data[0][0];
                    else
                        $data = $data[0];
                }
				
				$str = $this->dec2bin( strlen( $data ) ) . $data;
                break;
				
            case "arr" :
                if ( is_long( $data ) )
                    $data = array( array( $data, "int" ) );
                else if ( is_string( $data ) )
                    $data = array( array( $data, "str" ) );
					
                $str = $this->dec2bin( count( $data ) );
                
				for ( $i = 0; $i < count( $data ); $i++ )
                    $str .= $data[$i][1] . $this->tdbconv( $data[$i][0], $data[$i][1] );

                break;
        }

        return $str;
    }

	/**
	 * @access public
	 */
    function tdbread( $type, $raw = false )
    {
        $len = $this->bin2dec( fread( $this->fp, 4 ) );
        
		if ( $type == "int" )
        {
            if ( $raw == true )
                return $this->dec2bin( $len );
            else
                return $len;
        }
        else if ( $type == "arr" )
        {
            for ( $i = 0; $i < $len;$i++ )
            {
                $atype = fread( $this->fp, 3 );
                $arr[] = array( $this->tdbread( $atype, $raw ), $atype );
            }
			
            if ( $raw == true )
            {
                $ret = $this->dec2bin( $len );
				
                for ( $i = 0; $i < count( $arr ); $i++ )
                    $ret .= $arr[$i][1] . $arr[$i][0];
 
                return $ret;
            }
            else
			{
				return $arr;
			}
        }
        else if ( $type == "str" )
        {
            $str = fread( $this->fp, $len );
            
			if ( $raw == true )
                return $this->dec2bin( $len ) . $str;
            else
                return $str;
        }
    }

	/**
	 * @access public
	 */
    function tdbwrite( $data, $type )
    {
        switch ( $type )
        {
            case "int" :
                fwrite( $this->fp, $this->dec2bin( $data ), 4);
                break;
				
            case "str" :
                fwrite( $this->fp, $this->dec2bin( strlen( $data ) ), 4 );
                fwrite( $this->fp, $data, strlen( $data ) );
                break;
				
            case "arr" :
                fwrite( $this->fp, $this->dec2bin( count( $data ) ), 4 );
                
				for ( $i = 0; $i < count( $data ); $i++ )
                {
                    $atype = $data[$i][1];
                    fwrite( $this->fp, $atype, 3 );
                    $this->tdbwrite( $data[$i][0], $atype );
                }
				
                break;
        }
		
        return true;
    }

	/**
	 * @access public
	 */
    function dec2bin( $data )
    {
        $hex = dechex( $data );
		
        while ( strlen( $hex ) < 8 )
            $hex = "0" . $hex;
			
        $ret = chr( hexdec( substr( $hex, 0, 2 ) ) ) .
			   chr( hexdec( substr( $hex, 2, 2 ) ) ) .
			   chr( hexdec( substr( $hex, 4, 2 ) ) ) .
			   chr( hexdec( substr( $hex, 6, 2 ) ) );
        
		return $ret;
    }

	/**
	 * @access public
	 */
    function bin2dec( $data )
    {
		$x = "";
        
		while ( strlen( $data ) > 0 )
        {    
			$x   .= $this->pad( dechex( ord( $data ) ) );
            $data = substr( $data, 1 );
        }
		
        return hexdec($x);
    }
	
	
	// string methods

	/**
	 * @access public
	 */	
	function nextword( $str )
	{
    	$pos = $this->tdbstrpos( $str, " ," );
    
		if ( $pos == false )
    	{
        	$ret = $str;
        	$str = "";
		
        	return $ret;
    	}
	
    	$ret = substr( $str, 0, $pos );
    	$str = ltrim( substr( $str, $pos + 1 ) );
	
    	return $ret;
	}

	/**
	 * @access public
	 */
	function nextwhere( $str, &$where )
	{
    	$done = false;
    
		while ( !$done )
    	{
        	$pos      = $this->tdbstrpos( $str, "=<>!" );
        	$arr[fld] = trim( substr( $str, 0, $pos ) );
        	$pos2     = $pos + 1;
        
			while ( $this->matchchar( $str[$pos2], "=<>!" ) )
            	$pos2++;
        
			$arr[oper] = substr( $str, $pos, $pos2 - $pos );
        	$str = ltrim( substr( $str, $pos2 ) );
		
        	if ( $str[0] == '"' || $str[0] == "'" )
        	{
            	$quot = $str[0];
            	$str  = substr( $str, 1 );
            	$pos  = strpos( $str, $quot );
        	}
        	else
			{
				$pos = strpos( $str, " " );
			}
		
        	if ( $this->validpos( $pos ) )
        	{
            	$arr[crit] = trim( substr( $str, 0, $pos ) );
            	$str = trim( substr( $str, $pos + 1 ) );
        	}
        	else
        	{
            	$arr[crit] = trim( substr( $str, 0 ) );
            	$str = "";
        	}
		
        	$done = true;
        
			if ( strlen( $str ) > 0 )
        	{
            	$pos = strpos( $str, " " );

	            if ( $this->validpos( $pos ) )
    	        {
        	        $andor = trim( substr( $str, 0, $pos ) );

	                if ( ( strtolower( $andor ) == "and" ) || ( strtolower( $andor ) == "or" ) )
    	            {
        	            $str = trim( substr( $str, $pos + 1 ) );
            	        $arr[andor] = $andor;
                	    $done = false;
        	        }
            	}
        	}
		
        	$where[] = $arr;
        	unset( $arr );
    	}
	
    	return true;
	}

	/**
	 * @access public
	 */
	function nextorder( $str )
	{
 		$ddir  = "asc";
    	$going = true;
    	$i     = 0;
    
		while ( $going )
    	{
        	if ( $str[$i] == "," )
        	{
            	if (!is_string( $dir ) )
                	$dir = $ddir;
            
				$o[] = array( "val" => trim( $word ), "dir" => $dir );
            	unset( $word );
            	unset( $dir );
        	}
        	else if ( $str[$i] == " " )
        	{
            	$x = $i-1;
            
				while ( $str[$x] == " " )
                	$x--;
				
            	if ( $str[$x] != "," )
            	{
                	do
                	{
                    	$i++;
                	} while ( $str[$i] == " " );

	                if ( strtolower( substr( $str, $i, 3 ) ) == "asc" )
    	            {
        	            $dir = substr( $str, $i, 3 );
            	        $i += 3;
           	     	}
            	    else if ( strtolower( substr( $str, $i, 4 ) ) == "desc" )
                	{
         	           $dir = substr( $str, $i, 4 );
            	        $i += 4;
   	             	}
    	            else if ( $str[$i] != "," )
        	        {
            	        $going = false;
                    
						if ( !is_string( $dir ) )
        	                $dir = $ddir;
						
           	         	$o[] = array( "val" => trim( $word ), "dir" => $dir );
                	}
				
                	$i--;
            	}
        	}
        	else if ( $i >= strlen( $str ) )
        	{
            	$going = false;
            
				if ( !is_string( $dir ) )
                	$dir = $ddir;
				
            	$o[] = array( "val" => trim( $word ), "dir" => $dir );
        	}
        	else
			{
				$word .= $str[$i];
			}
        
			$i++;
    	}
	
    	$str = ltrim( substr( $str, $i ) );
	
    	if ( is_array( $o ) )
        	return $o;
    	else
        	return false;
	}

	/**
	 * @access public
	 */
	function nextwords( $str )
	{
    	$going = true;
    	$i     = 0;
    	$brak  = 0;
 
    	while ( $going )
    	{
        	if ( $str[$i] == "," )
        	{
            	$words[] = trim( $word );
            	unset( $word );
        	}
        	else if ( $str[$i] == " " )
        	{
            	$x = $i-1;
            
				while ( $str[$x] == " " )
                	$x--;
				
            	if ( $str[$x] != "," )
            	{
                	do
                	{
                    	$i++;
                	} while ( $str[$i] == " " );

                	if ( $str[$i] != "," )
                	{
                    	$going   = false;
                    	$words[] = trim( $word );
                	}
				
                	$i--;
            	}
        	}
        	else if ( $i >= strlen( $str ) )
        	{
            	$going   = false;
            	$words[] = trim( $word );
        	}
        	else if ( $str[$i] == "(" )
        	{
            	$brak++;
        	}
        	else if ( $str[$i] == ")" )
        	{
            	$brak--;
        	}
        	else
			{
				$word .= $str[$i];
			}
        
			$i++;
    	}
	
    	$str = ltrim( substr( $str, $i ) );
    
		if ( is_array( $words ) )
        	return $words;
    	else
        	return false;
	}

	/**
	 * @access public
	 */
	function tdbstrpos( $haystack, $needle )
	{
    	for ( $i = 0; $i < strlen( $needle ); $i++ )
    	{
        	$tmp = strpos( $haystack, substr( $needle, $i, 1 ) );
		
			if ( $this->validpos( $tmp ) )
				$res[] = $tmp;
    	}
	
    	if ( is_array( $res ) )
        	return min( $res );
    	else
        	return false;
	}

	/**
	 * @access public
	 */
	function validpos( $pos )
	{
		if ( $pos === false )
 			return false;
	
    	return true;
	}

	/**
	 * @access public
	 */
	function matchchar( $char, $chars )
	{
    	for ( $i = 0; $i < strlen( $chars ); $i++ )
    	{
        	if ( $char == $chars[$i] )
            	return true;
    	}
	
    	return false;
	}

	/**
	 * @access public
	 */
	function expr( $left, $oper, $right )
	{
    	switch ( $oper )
    	{
        	case "=" :
            	return ( $left == $right );
			
        	case "!=" :
            	return ( $left != $right );
			
        	case ">" :
            	return ( $left  > $right );
			
        	case ">=" :
            	return ( $left >= $right );
			
        	case "<" :
            	return ( $left  < $right );
			
        	case "<=" :
            	return ( $left <= $right );
			
        	default :
            	return false;
    	}
	}

	/**
	 * @access public
	 */
	function compare( $a, $b, $orderby, $type )
	{
    	switch ( $type )
    	{
        	case "int" :
            	if ( $a[$orderby[val]] == $b[$orderby[val]] )
					return false;
            
				$r = ( $a < $b )? -1 : 1;
            	return ( $orderby[dir] == "asc" )? $r : -$r;
			
        	case "str" :
            	$r = strcmp( $a[$orderby[val]], $b[$orderby[val]] );
            	return ( $orderby[dir] == "asc" )? $r : -$r;
    	}
	}

	/**
	 * @access public
	 */
	function pad( $str, $len = 2 )
	{
		while ( strlen( $str ) < $len )
			$str = "0" . $str;
		
		return $str;
	}
} // END OF TextDB

?>
