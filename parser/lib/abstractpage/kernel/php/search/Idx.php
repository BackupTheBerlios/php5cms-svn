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


/* How verbose should the indexer and merger be?  This doesn't
 * affect the search at all.  1 shows errors, 3 shows progress
 * and errors
 */
define( "IDX_VERBOSITY", 3 );

/*
 *What is the length of shortest word we'll try to index?
 */
define( "IDX_MIN_WORD_LENGTH", 3 ); 

/*
 * How long is the longest word we'll try to index?
 */
define( "IDX_MAX_WORD_LENGTH", 25 ); 
	   
/*
 * What is the normal word factor?  Set this to 1 for normal.
 * See the docs for info on word factor (:wf3: syntax).
 */
define( "IDX_WORD_FACTOR", 1 ); 
		
/* 
 * What directory can we use for the data files? Perhaps
 * this directory could be writable by the program indexing...
 * Perhaps as well, this partition will have enough space
 * so that we can use it?  This directory needs to exist
 * before you try to use it.
 */
define( "IDX_DATA_DIR", AP_ROOT_PATH . ap_ini_get( "path_cache", "path" ) ); 

/*
 * What directory can we use for temporary files? Perhaps
 * this directory could be writable by the program indexing...
 * Perhaps as well, this partition will have enough space
 * so that we can use it?  This directory needs to exist
 * before you try to use it.
 */
define( "IDX_TMP_DIR", ap_ini_get( "path_tmp_os", "path" ) );
		
	
/**
 * @package search
 */
 
class Idx extends PEAR
{
	/**
	 * Stopwords. Make sure your words are lower case!
	 * If they are not, they won't be recognized properly.
	 *
	 * @access public
	 */
	var $stopwords = array(
		"www" 		=> 1,
		"net" 		=> 1,
		"com" 		=> 1,
		"here" 		=> 1,
		"there" 	=> 1,
		"with" 		=> 1,
		"the" 		=> 1,
		"for" 		=> 1,
		"and" 		=> 1,
		"where" 	=> 1,
		"address"	=> 1
	);
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Idx( $index )
	{
		$this->idx = trim( $index );
	} 


	/**
	 * You can make words from certain areas 'weight' more if you use
	 * this syntax in your indexing system:
	 * index( "Title", "words words words :wf2: more more more :wf5: less less less" );
	 * So, the first set (before :wf2: have normal factor, the 2nd has factor of 2,
	 * the 3rd have factor of 5 (they'll score highest and be returned first, 
	 * most likely);
	 *
	 *
	 * @access public
	 */
	function is_wf( $word )
	{
		if ( eregi( ":wf([0-9]+):", $word, $regs ) )
			return (int)$regs[1];
		else
			return false;
	}

	/**
	 * Functionality for forced phrase matching. 
 	 *
	 * If a user searches for 'hans'
	 * he would not get an entry for 'hans anderson',
	 * structrued like this: 'hans+anderson'. So, if
	 * desired, you can call this function, factor, to
	 * find out how many different combinations there are
	 * to factor, and a list of them.
	 * It only works in order.
	 *
	 * For example, given these search terms:
	 *
	 * "Hans Hopkins Anderson"
	 *
	 * factor() will return three items:
	 *
	 * "Hans+Hopkins"
	 * "Hopkins+Anderson"
	 * "Hans+Hopkins+Anderson"
	 *
	 * And those will only match premade phrases
	 * in the index, so if a user searches for
	 * "Hans Anderson", they won't get bogus matches.
	 * This was only added for a unique circumstance and isn't
	 * really necessary for general use.
	 *
	 * @access public
	 */
	function factor( $f, $d )
	{
		// $f=data, $d=delimiter
		$a = explode( $d, $f );
		$c = $cnt = count( $a );

		if ( $cnt <= 1 )
			return array(
				"possibles"	=> $cnt,
				"list"		=> "$f"
			);

		$t = $c;

		for ( $i = 0; $i < $cnt; $i++ )
		{
			if ( substr( $a[$i],   0, 1 ) != '+' &&
				 substr( $a[$i],   0, 1 ) != '-' &&
				 substr( $a[$i+1], 0, 1 ) != '+' &&
				 substr( $a[$i+1], 0, 1 ) != '-' )
			{
				if ( $i + 1 < $cnt )
					$l[] = $a[$i] . "+" . $a[$i+1];
				
				$ll[] = $a[$i];
			}
			else
			{
				// don't build the entire query into word+word+word, etc
				$nobuildall = 1;
			}

			if ( $c <= 1 )
				break;
			
			$t *= --$c;
		}

		if ( !isset( $nobuildall ) && is_array( $ll ) )
			$l[] = implode( "+", $ll );
			
		if ( is_array( $l ) )
			$l = implode( " ", $l );

		return array(
			"possibles"	=> $t,
			"list"		=> $l 
		);
	}

	/**
	 * @access public
	 */
	function get_word_offsets( $index )
	{
		$fp = fopen( IDX_DATA_DIR . "/$index.word_offsets.data", "r" );
		$offsets = unserialize( fread( $fp, filesize( IDX_DATA_DIR . "/$index.word_offsets.data" ) ) );
		fclose( $fp );
		
		return $offsets;
	}

	/**
	 * This is a screwy little function.  Use it to find items in
	 * more than one associative array that are in common.  If you
	 * pass it an empty array, there can be nothing in common.  So, 
	 * if you are looping through a list of more than two arrays,
	 * DO NOT declare an array for the first ($m) item the FIRST
	 * time through.  If you do, there will never be anything in
	 * common.  If you pass it an uninitiated variable the
	 * first time through, it will return the other.  I don't
	 * recommend trying to use this function for anything but this,
	 * as it's got lots of potential problems if not called correctly.
	 *
	 * @access public
	 */
	function in_common( $m, $n )
	{
		if ( !is_array( $m ) )
		{
			// returning n because m is not an array
			return $n;
		}
		else if ( !is_array( $n ) )
		{
			// returning empty because n is not an array
			// can't return $m, else it'll think $m are valid results (on last time through)
			return array();
		}

		$som = sizeof( $m );
		
		if ( $som == 0 )
			return array(); /* if it's size is 0, there can't be 
					 		   anything in common with the other
					 		   array. You might check the return
					 		   of this for size.  If it's 0, nothing
					 		   will ever be in common, and you can 
					 		   pretty well return to doing other
					 		   things without continuing to look */
	
		$son = sizeof( $n );
	
		if ( $son == 0 )
			return array(); /* if it's size is 0, there can't be 
					 		   anything in common with the other
					 		   array. You might check the return
					 		   of this for size.  If it's 0, nothing
					 		   will ever be in common, and you can 
					 		   pretty well return to doing other
					 		   things without continuing to look */

        if ( ( $som <= $son ) )
		{
			while ( list( $k, $v ) = each( $m ) )
			{
				if ( isset( $n["$k"] ) )
					$o["$k"] = $v + $n["$k"];
			}
        }
		else
		{
			while ( list( $k, $v ) = each( $n ) )
			{
				if ( isset( $m["$k"] ) )
					$o["$k"] = $v + $m["$k"];
			}
		}

		if ( !is_array( $o ) )
			return array();
		else
			return $o;
	}

	/**
	 * Is the word in question a stopword?
	 * If so, don't index it! Stop words are
	 * defined in the file ./include.stopwords.php,
	 * which is included in the indexer file.
	 *
	 * @access public
	 */
	function is_stopword( $word )
	{
		if ( $this->stopwords["$word"] )
			return true;

		return false;
	}

	/**
	 * @access public
	 */
	function plural_stem( $word )
	{
		$w = eregi_replace( "'s$","", trim( $word ) );

		if ( eregi( "[^s]s$", $w ) )
		{
			if ( eregi( "sses$", $w ) )
				$w = eregi_replace( "sses$", "ss", $w );
			else if ( eregi( "ies$", $w ) )
  				$w = eregi_replace( "ies$", "i", $w );
			else if ( eregi( "s$", $w ) )
				$w = eregi_replace( "s$", "", $w );
			
			$did_stem = true;
		}

		if ( $did_stem )
			$w = eregi_replace( "(.)i$", "\\1y", $w );

        return $w;
	}

	/**
	 * This function takes the argument of 
	 * a word, just one word. If this word 
	 * is indexable by phpsearch, TRUE is 
	 * returned.  If it's not, FALSE is returned.
	 * A word may not be indexable because 
	 * it's too short, too long, doesn't have 
	 * enough letters, it's all numbers (if you 
	 * have set that to be false), etc.
	 *
	 * @access public
	 */
	function is_indexable( $word )
	{
		$length = strlen( $nw = trim( $this->plural_stem( strtolower( ereg_replace("[^@A-Za-z0-9+]","",$word) ) ) ) );

		if ( $length < IDX_MIN_WORD_LENGTH || $length > IDX_MAX_WORD_LENGTH || $this->is_stopword( $nw ) )
			return false;

		return $nw;
	}

	/**
	 * This function adds the title to the 
	 * titles file.  Later, when searching, 
	 * we'll match searches to this 'title' 
	 * and output the title.
	 *
	 * @access public
	 */
	function title_add( $fp, $title )
	{
 		global $title_pointer;

		$told1 = ftell( $title_pointer );
		
		if( !fputs( $title_pointer, "$title\n" ) ) 
			return false;
		
		$told2 = ftell( $title_pointer ) - $told1;
		return array( $told1, $told2 );
	}

	/**
	 * If you don't purge the index, you
	 * will add to it.  This allows you
	 * to append to the index, though not
	 * really 'update' it. If you get new
	 * stuff, you can index it this way. If
	 * you need to update, you'll have to 
	 * purge_index(), then index().
	 * There is no functionality to 'update'
	 * or 'delete' specific entries in the
	 * index.
	 *
	 * @access public
	 */
	function purge_index()
	{
		$index = $this->idx;

		if ( file_exists( IDX_DATA_DIR . "/$index.titles.data" ) )
			unlink( IDX_DATA_DIR . "/$index.titles.data" );

		$a = "abcdefghijklmnopqrstuvwxyz0123456789";

		for ( $j = 0; $j < strlen( $a ); $j++ )
		{
			$f = substr( $a, $j, 1 );
			
			if ( file_exists( IDX_TMP_DIR . "/$f" ) )
				unlink( IDX_TMP_DIR . "/$f" );
		}

		return true;
	}

	/**
	 * For each round of 'index', we write all 
	 * the words, their title offsets and the 
	 * number of times they appear in the entry, 
	 * to an array.  Now, we open the appropriate 
	 * files and write it out, to save memory.
	 *
	 * @access public
	 */
	function write_array( $a )
	{
		global $indexer_pointers;

		if ( !is_array( $a ) )
			return false;

		while ( list( $k1, $v1 ) = each( $a ) )
		{ 
			while ( list( $k2, $v2 ) = each( $v1 ) )
			{
				$line = "$k2";
	 
				while ( list( $k3, $v3 ) = each( $v2 ) )
					$line .= " $k3:$v3";

				fputs( $indexer_pointers["$k1"], "$line\n" );
			}
		}

		unset( $a  );
		unset( $v1 );
		unset( $v2 );
		
		return true;
	}

	/**
	 * This function is the guts of the indexing.
	 * It takes a bunch of words and indexes them
	 * (if they are not stop words, too long or
	 * too short or are made up of any other
	 * exceptions to our rules). After we call it,
	 * we then call write_array, which records that
	 * page's index.
	 *
	 * @access public
	 */
	function index( $title, $words )
	{
		global $indexer_pointers;

		if( !$title_array = $this->title_add( $title_pointer, $title ) )
			return false;

		list( $title_offset_start, $title_offset_end ) = $title_array;

		$wf = IDX_WORD_FACTOR; // normal word factor
		$a  = ''; // I've had problems with clearing the array
		$w  = split( "[^:@A-Za-z0-9+]+", $words );

		for ( $i = 0; $i < count( $w ); $i++ )
		{
			if ( $this->is_wf( $w[$i] ) )
				$wf = $this->is_wf( $w[$i] );

			if ( $wd = $this->is_indexable( $w[$i] ) )
			{ 
				$f = substr( $wd, 0, 1 ); 

				if ( !eregi( "[a-z0-9]", $f ) )
					continue;

				$a["$f"]["$wd"]["$title_offset_start:$title_offset_end"] += $wf;
			}   
		}

		unset( $w );
		unset( $wd );
		unset( $words );

		if ( $this->write_array( $a ) )
		{
			unset( $a );
			return true;
		}
		else
		{
			unset( $a );
			return false;  
		}
 
 		return true;
	}

	/**
	 * @access public
	 */
	function index_open( $index )
	{
		if ( !is_file( IDX_DATA_DIR . "/$index.index.data" ) )
			return false;

		if ( !is_readable( IDX_DATA_DIR . "/$index.index.data" ) )
			return false;

		return fopen( IDX_DATA_DIR . "/$index.index.data", "r" );
	}

	/**
	 * @access public
	 */
	function index_close( $pointer )
	{
		return fclose( $pointer );
	}

	/**
	 * @access public
	 */
	function indexer_open()
	{
		if ( is_dir( IDX_TMP_DIR ) )
		{
			if ( is_writeable( IDX_TMP_DIR ) )
			{
	       		$a = "abcdefghijklmnopqrstuvwxyz0123456789";

				for ( $j = 0; $j < strlen( $a ); $j++ )
				{
	          		$f = substr( $a, $j, 1 );
	          		$ary[$f] = fopen( IDX_TMP_DIR . DIRECTORY_SEPARATOR . $f, "a" );
				}

				return $ary;
			}
			else
			{
	      		return false;
	     	}
		}
		else
		{
			return false;
		}
	}

	/**
	 * @access public
	 */
	function indexer_close( $indexer_pointers )
	{
		while ( list( $k, $v ) = each( $indexer_pointers ) )
			fclose( $v );
			
		return true;
	}

	/**
	 * @access public
	 */
	function merger_open( $index )
	{
		$a = "abcdefghijklmnopqrstuvwxyz0123456789";
 
		for ( $j = 0; $j < strlen( $a ); $j++ )
		{
			$f = substr( $a, $j, 1 );
			$ary["$f"] = fopen( IDX_TMP_DIR . DIRECTORY_SEPARATOR . $f, "r" );
		}

		$ary["title_offsets"] = fopen( IDX_DATA_DIR . "/$index.title_offsets.data", "w" );
		return $ary; 
	}

	/**
	 * @access public
	 */
	function merger_close( $merger_pointers )
	{
		while ( list( $k, $v ) = each( $merger_pointers ) )
			fclose( $v );
	}

	/**
	 * @access public
	 */
	function title_open( $index, $how = "r" )
	{
		if ( is_dir( IDX_DATA_DIR ) )
		{
			$fp = fopen( IDX_DATA_DIR . "/$index.titles.data", $how );
	        fputs( $fp, " " );
			
			return $fp;
		}
		else
		{
	    	return false;
		}
	}

	/**
	 * @access public
	 */
	function title_close( $title_pointer )
	{
		return fclose( $title_pointer );
	}

	/**
	 * @access public
	 */
	function title_offsets_open( $index )
	{
		if ( is_dir( IDX_DATA_DIR ) )
			return fopen( IDX_DATA_DIR . "/$index.title_offsets.data", "r" ); 
		else
			return false;
	}

	/**
	 * @access public
	 */
	function title_offsets_close( $pointer )
	{
		 return fclose( $pointer );
	}

	/**
	 * Here is where we look for the word in our index.
	 * If found, returns the 'record' (word offsets:for:titles)
	 *
	 * @access public
	 */
	function find_word( $fp, $term, $offsets )
	{
		if ( strlen( $term ) < IDX_MIN_WORD_LENGTH || strlen( $term ) > IDX_MAX_WORD_LENGTH || $this->is_stopword( $term ) )
			return false;

		$term = $this->plural_stem( $term );
		$f    = strtolower( substr( $term, 0, 1 ) );

		$read_len = $offsets[$f.'e'] - $offsets[$f.'s'];

		if ( fseek( $fp, $offsets[$f . 's'] ) < 0 )
			return false;

		if ( !$read = fread( $fp, $read_len ) )
			return false;
			
		$spos = strpos( $read,' ' . $term . ' ' );

		if ( $spos == 0 )
			return false;

		if ( fseek( $fp, $offsets[$f . 's'] + $spos ) <0 )
			return false;

		return fgets( $fp, 40 );
	}

	/**
	 * @access public
	 */	
	function get_title_offsets( $start, $end, $word_offsets_fp )
	{
		fseek( $word_offsets_fp, $start );
		return trim( fread( $word_offsets_fp, $end ) );
	}

	/**
	 * @access public
	 */
	function get_title( $start, $end, $title_offsets_fp )
	{
		fseek( $title_offsets_fp, $start );
		return trim( fread( $title_offsets_fp, $end ) );
	} 

	/**
	 * @access public
	 */
	function search( $search_terms, $start_results = 1, $num_results = 10 )
	{
		$offsets = $this->get_word_offsets( $this->idx );
		$terms = split( " +", $search_terms );
			
		if ( !$fp = $this->index_open( $this->idx ) )
		{
		  print "Couldn't open index\n";
		  exit;
		}

		$how_many_musts = 0;
        
		for ( $i = 0 ; $i < count( $terms ) ; $i++ )
		{
			if ( !$terms[$i] )
				continue;
			
			$term = strtolower( $terms[$i] );
			$fc   = substr( $term, 0, 1 );

			if ( $fc == '-' )
			{
				$term = substr( $term, 1, strlen( $term ) - 1 );

				if ( !$this->is_indexable( $term ) )
					continue;
         		
				if ( $fw = $this->find_word( $fp, $term, $offsets ) )
				{
					list ( $word, $my_word_offsets ) = explode( " ", trim( $fw ) );
				  	$words_found["not"] .= "$my_word_offsets ";
				}
			}
			else if ( $fc == "+" )
			{
				$term = substr( $term, 1, strlen( $term ) - 1 );

				if ( !$this->is_indexable( $term ) )
					continue;
				
				$this_len = strlen( ereg_replace( "[^@A-za-z0-9+]", "", $term ) );

				if ( $this_len < IDX_MIN_WORD_LENGTH )
					continue;
					
				$how_many_musts++;

				if ( $fw = $this->find_word( $fp, $term,$offsets ) )
				{
					list ( $word, $my_word_offsets ) = explode( " ", trim( $fw ) );
				    $words_found["must"] .= "$my_word_offsets ";
				    $something_found = 1;
				}
			}
			else
			{
				if ( !$this->is_indexable( $term ) )
					continue;
         	
				if ( $fw = $this->find_word( $fp, $term, $offsets ) )
				{
					list ( $word, $my_word_offsets ) = explode( " ", trim( $fw ) );
				   	$words_found["can"] .= "$my_word_offsets ";
				 	$something_found = 1;
				}
			}
		}

		$this->index_close( $fp );

		if ( !isset( $something_found ) )
			return false;

		// now we have the offsets for the words,
		// so we look in the word_offsets.data
		// file for all of the titles that match

		$title_offsets_fp = $this->title_offsets_open( $this->idx );
		$titles_fp = $this->title_open( $this->idx, "r" );

		if ( isset( $words_found["must"] ) )
			$must = explode( " ", trim( $words_found["must"] ) );
			
		if ( isset( $words_found["can"]  ) )
			$can = explode( " ", trim( $words_found["can"] ) );
			
		if ( isset( $words_found["not"]  ) )
			$not = explode( " ", trim( $words_found["not"] ) );

		// for all the words that 'can' be in the results (those with no + or -)
		$cans = array();
	
		for ( $j = 0; $j < count( $can ); $j++ )
		{
	  		list ( $start, $end ) = explode( ":", $can[$j] );
			$cans = array_merge( $cans, unserialize( trim( $this->get_title_offsets( $start, $end, $title_offsets_fp ) ) ) );
		}

		// for all the words that 'must' be in the results (those with a +)
		for ( $j = 0; $j < $how_many_musts; $j++ )
		{
	  		list( $start, $end ) = explode( ":", $must[$j] );
			$newmusts = unserialize( trim( $this->get_title_offsets( $start, $end, $title_offsets_fp ) ) );
			$musts = $this->in_common( $musts, $newmusts );
 		}

		// for all the words that can 'not' be in the results (those with a -)
		$nots = array();
		
		for ( $j = 0; $j < count( $not ); $j++ )
		{
	  		list( $start, $end ) = explode( ":", $not[$j] );
			$nots = array_merge( $nots, unserialize( trim( $this->get_title_offsets( $start, $end, $title_offsets_fp ) ) ) );
		}

		// now, take all the cans (w/o - or +), find those
		// that that are musts (+), if any, and remove the nots (-)

		if ( is_array( $musts ) && ( sizeof( $cans ) > 0 ) )
		{
			while ( list( $k, $v ) = each( $musts ) )
			{
	 			if ( isset( $cans["$k"] ) )
					$total["$k"] = $cans["$k"] + $v;
				else
					$total["$k"] = $v;
			}
		}
		else if( ( is_array( $musts ) ) && ( sizeof( $cans ) == 0 ) )
		{
			$total = $musts;
		}
		else
		{
			// no musts, so we can use all of the cans
			$total = $cans;
		}

		if ( is_array( $nots ) )
		{
	  		$so_t = sizeof( $total );
	  		$so_n = sizeof( $nots  );

			// for speed, try to each() the smallest array
			if ( $so_n <= $so_t )
			{
				while ( list( $k, $v ) = each( $nots ) )
					unset( $total["$k"] );
			}
			else
			{
				while ( list( $k, $v ) = each( $total ) )
				{
			 		if ( isset( $nots["$k"] ) )
				 		unset( $total["$k"] );
				}
			
				reset($total);
			}
		}

		if ( !is_array( $total ) )
			return false;
	  
	  	$size_total = sizeof( $total );
	
		if ( $size_total > 0 )
			arsort( $total );

		$end_result = $num_results + $start_results;

		$i = $j = 0;
		while ( list( $k, $v ) = each( $total ) )
		{
			if ( $j >= $end_result )
				break;
		
  		    if ( $i <= $num_results && $j >= $start_results )
			{
				list( $title_offset_start, $title_offset_end ) = explode( ":", $k );
			    $title = $this->get_title( $title_offset_start, $title_offset_end, $titles_fp );
			    $array["$title"] = $v;
			
				$i++;
				$j++;;
		   	}
			else
			{
				$j++; 
		    }
		}

		$this->title_offsets_close( $title_offsets_fp );
		$this->title_close( $titles_fp );

		return array(
			"result_count"	=> $size_total,
			"results"		=> $array
		);
	}

	/**
	 * @access public
	 */
	function ticker( $j = 0 )
	{
		$backspace = chr(8);

		switch ( $j )
		{
			case 0 :
				$o = "/";
				$j++;
				
				break;
		
			case 1 :
				$o = "-";
				$j++;
				
				break;
		
			case 2 :
				$o = "\\";
				$j++;
				
				break;
		
			case 3 :
				$o = "|";
				$j = 0;
				
				break;
		
			default :
				$j = 0;
		}
		
		print "$backspace$o";
		flush();
	
		return $j;
	}

	/**
	 * @access public
	 */
	function merge()
	{
		$index = $this->idx;
		$merger_pointers = $this->merger_open( $index );
 
 		$fp = fopen( IDX_DATA_DIR . "/$index.index.data", "w" );
 		fputs( $fp, "\n" ); // problem with finding first word in index if we don't do this

		while ( list( $k, $v ) = each( $merger_pointers ) )
		{
			if ( $k == 'title_offsets' )
				continue;

	 		$fs = filesize( IDX_TMP_DIR . DIRECTORY_SEPARATOR . $k );
		
			if ( $fs == 0 )
				continue;

			if ( IDX_VERBOSITY > 2 )
				print Chr(8) . "merging words starting with $k...\n";

			$offsets[$k . 's'] = ftell( $fp ) - 1; // position in index $k starts
			$array = file( IDX_TMP_DIR . "/$k" );
			sort( $array );
			$array_size = sizeof( $array );
		
			for ( $i = 0; $i <= $array_size; $i++ )
			{
				if ( IDX_VERBOSITY > 2 )
					$j = $this->ticker( $j );

				list ( $w, $o ) = explode( " ", trim( $array[$i] ) );

				if ( $w == '' && $i < $array_size )
					continue;

				if ( $word == $w )
				{
					$these_offsets[] = "$o";
				}
				// first word for this letter
				else if ( $word == '' )
				{
					$word = $w;
					$these_offsets[] = "$o";
				}
				else
				{
					$cos = count( $these_offsets );
				
					for ( $z = 0; $z < $cos; $z++ )
					{
						list( $t1, $t2, $t3 ) = explode( ":", $these_offsets[$z] );
						$hash["$t1:$t2"] += $t3;
					}

					$offset_told1 = ftell( $merger_pointers["title_offsets"] );
					fputs( $merger_pointers["title_offsets"],"" . serialize( $hash ) ."\n" );
					$offset_told2 = ftell( $merger_pointers["title_offsets"] ) - $offset_told1;

					fputs( $fp, " $word $offset_told1:$offset_told2\n" );
					unset( $hash );

					unset( $these_offsets ); // loose old data
					$these_offsets[] = "$o";
					$word = $w;
				}

				unset( $array[$i] );
			}

			$offsets[$k.'e'] = ( ftell( $fp ) - 1 ); // position in index $k ends
		}

		fclose( $fp );
		$this->merger_close( $merger_pointers );

		$fp = fopen( IDX_DATA_DIR . "/$index.word_offsets.data", "w" );
		fputs( $fp, serialize( $offsets ) );
		fclose( $fp );
	}
} // END OF Idx

?>
