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
 * Template engine with support for blocks, loops and ifset (also works in loops). 
 * Works fast. Most suitable for small and medium projects.
 *
 * @package template
 */
 
class SimpleTemplate extends PEAR
{
	/**
	 * @access public
	 */
	var $root;
	
	/**
	 * @access public
	 */
	var $empty;

	/**
	 * @access public
	 */
	var $vars = array();
	
	/**
	 * @access public
	 */
	var $loop_vars = array();
	
	/**
	 * @access public
	 */
	var $loop_count = array();
	
	/**
	 * @access public
	 */
	var $blocks = array();


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SimpleTemplate( $root = './', $empty = 'empty' ) 
	{
		$this->root = $root;

		switch ( $empty )
		{
			case 'none':	
				$this->empty = '\0'; 
				break;
			
			case 'comment':	
				$this->empty = '<!-- \0 -->'; 
				break;
			
			case 'space':	
				$this->empty = '&nbsp;'; 
				break;
			
			default:		
				$this->empty = '';
		}
	}


	/**
	 * Assign file content to variable.
	 *
	 * @access public
	 */
	function setFile( $var_name, $file_name ) 
	{
		$file_name = $this->root . $file_name;
		
		if ( file_exists( $file_name ) ) 
		{
			$file_handle = fopen( $file_name, 'r' );
			$this->vars[$var_name] = fread( $file_handle, filesize( $file_name ) );
			$this->vars[$var_name] = $this->_extractBlocks( $this->vars[$var_name] );
			fclose( $file_handle );
			
			return true;
		} 
		else 
		{
			return PEAR::raiseError( "Could not open template file '$file_name'." );
		}
	}

	/**
	 * Assign value to variable.
	 *
	 * @access public
	 */
	function setVar( $var_name, $var_value ) 
	{
		if ( is_array( $var_value ) ) 
		{
			while ( list( $sub_name, $sub_value ) = each( $var_value ) )
				$this->setVar( $var_name . '.' . $sub_name, $sub_value );
		} 
		else 
		{
			$this->vars[$var_name] = $var_value;
		}
	}
	
	/**
	 * Set loop variables.
	 *
	 * @access public
	 */
	function setLoop( $loop_name, $loop_value ) 
	{
		$this->loop_vars[$loop_name]  = $loop_value;
		$this->loop_count[$loop_name] = count( $loop_value );
	}

	/**
	 * Assign block to variable.
	 *
	 * @access public
	 */
	function setBlock( $var_name, $block_name, $append = false ) 
	{
		if ( isset( $this->blocks[$block_name] ) ) 
		{
			$block = $this->blocks[$block_name];
			$block = $this->_parseVar( $block );
			$block = $this->_parseLoop( $block );
			
			if ( PEAR::isError( $block ) )
				return $block;
				
			$block = $this->_parseIfSet( $block, $this->vars );

			if ( $append && isset( $this->vars[$var_name] ) )
				$this->vars[$var_name] .= $block;
			else
				$this->vars[$var_name] = $block;
				
			return true;
		} 
		else 
		{
			return PEAR::raiseError( "Could not set block: '$block_name' does not exist." );
		}
	}

	/**
	 * Parse variables, loops, blocks.
	 *
	 * @access public
	 */
	function parse( $var_name, $output = 'echo', $file_name = 'output.htm' ) 
	{
		$object = $this->vars[$var_name];
		$object = $this->_parseVar( $object );
		$object = $this->_parseLoop( $object );
		
		if ( PEAR::isError( $object ) )
			return $object;
			
		$object = $this->_parseIfSet( $object, $this->vars );
		$object = preg_replace( '#\{[a-zA-Z0-9_,\-\+\.]+\}#si', $this->empty, $object );
		
		switch ( $output )
		{
			case 'return': 
				return ( $object ); 
				break;
			
			case 'file': 
				$this->_writeFile( $file_name, $object ); 
				break;
			
			default: 
				echo( $object );
		}
	}
	
	
	// private methods

	/**
	 * Parse variables.
	 *
	 * @access private
	 */
	function _parseVar( $object ) 
	{
		$object_pieces = explode( '{', $object );
		$parsed_object = array_shift( $object_pieces );
		
		foreach ( $object_pieces as $object_piece ) 
		{
			list( $var_name, $piece_end ) = explode( '}', $object_piece, 2 );
			
			if ( isset( $this->vars[$var_name] ) )
				$parsed_object .= $this->vars[$var_name] . $piece_end;
			else
				$parsed_object .= '{' . $var_name . '}' . $piece_end;
		}
		
		return ( $parsed_object );
	}

	/**
	 * Parse loops.
	 *
	 * @access private
	 */
	function _parseLoop( $object ) 
	{
		while ( list( $loop_name, $loop_vars ) = each( $this->loop_vars ) ) 
		{
			$loop_pos_start  = strpos( $object, '<tpl loop="'    . $loop_name . '">' );
			$loop_pos_middle = strpos( $object, '</tpl loop="'   . $loop_name . '">', $loop_pos_start  );
			$loop_pos_end    = strpos( $object, '</tpl noloop="' . $loop_name . '">', $loop_pos_middle );
			
			if ( $loop_pos_middle ) 
			{
				list( $begin, $end ) = explode( '<tpl loop="'  . $loop_name . '">', $object, 2 );
				list( $loop,  $end ) = explode( '</tpl loop="' . $loop_name . '">', $end,    2 );
				
				if ( $loop_pos_end ) 
					list( $noloop, $end ) = explode( '</tpl noloop="' . $loop_name . '">', $end, 2 );

				if ( ( $this->loop_count[$loop_name] == 0 ) && ( $loop_pos_end > 0 ) ) 
				{
					$object = $begin . $noloop . $end;
				} 
				else 
				{
					$loop   = $this->_parseLoopVar( $loop, $loop_name, $loop_vars );
					$object = $begin . $loop . $end;
				}
			} 
			else 
			{
				return PEAR::raiseError( "Could not parse loop: bad syntax in '$loop_name' tag." );
			}
		}

		return ( $object );
	}

	/**
	 * Parse variables in loops.
	 *
	 * @access private
	 */
	function _parseLoopVar( $object, $loop_name, $loop_vars ) 
	{
		// read loop text block and prepare it for looping
		$object_pieces = explode( '{' . $loop_name . '.', $object );
		$loop_pieces[0]['var']  = '{begining}';
		$loop_pieces[0]['text'] = array_shift( $object_pieces );
		
		$i = 1;
		
		foreach ( $object_pieces as $object_piece ) 
		{
			list( $var_name, $end )    = explode( '}', $object_piece, 2 );
			$loop_pieces[$i]['var']    = $var_name;
			$loop_pieces[$i++]['text'] = $end;
		}
		
		// looping
		$parsed_object = '';

		foreach ( $loop_vars as $loop_var ) 
		{
			$parsed_object_piece = '';
			
			foreach ( $loop_pieces as $loop_piece ) 
			{
				$var_name = $loop_piece['var'];
				$text = $loop_piece['text'];
				
				if ( isset( $loop_var[$var_name] ) )
					$parsed_object_piece .= $loop_var[$var_name] . $text;
				else if ( '{begining}' == $var_name )
					$parsed_object_piece .= $text;
				else
					$parsed_object_piece .= '{' . $loop_name . '.' . $var_name . '}' . $text;
			}
			
			$parsed_object_piece = $this->_parseIfSet( $parsed_object_piece, $loop_var, $loop_name . '.' );
			$parsed_object .= $parsed_object_piece;
		}
		
		return ( $parsed_object );
	}

	/**
	 * Parse ifset tags.
	 *
	 * @access private
	 */
	function _parseIfSet( $object, $vars, $loop_name = '' ) 
	{
		$object_pieces = explode( '<tpl ifset="' . $loop_name, $object );
		$parsed_object = array_shift( $object_pieces );
		
		foreach ( $object_pieces as $object_piece ) 
		{
			list( $var_name, $end   ) = explode( '">', $object_piece, 2 );
			list( $ifset_text, $end ) = explode( '</tpl ifset="' . $loop_name . $var_name . '">', $end, 2 );

			if ( !isset( $vars[$var_name] ) )
				$parsed_object .= $end;
			else
				$parsed_object .= $ifset_text . $end;
		}
		
		return ( $parsed_object );
	}
	
	/**
	 * Extract blocks into blocks array.
	 *
	 * @access private
	 */
	function _extractBlocks( $object ) 
	{
		$object_pieces = explode( '<tpl block="', $object );
		$parsed_object = array_shift( $object_pieces );
		
		foreach ( $object_pieces as $object_piece ) 
		{
			list( $block_name, $end ) = explode( '">', $object_piece, 2 );
			$block_pieces = explode( '</tpl block="' . $block_name . '">', $end, 2 );
			
			if ( 2 == count( $block_pieces ) ) 
			{
				list ( $block_text, $end ) = $block_pieces;
				$this->blocks[$block_name] = $block_text;
			} 
			else 
			{
				return PEAR::raiseError( "Could not set block: bad syntax in '$block_name' tags." );
			}
			
			$parsed_object .= $end;
		}
		
		return ( $parsed_object );
	}

	/**
	 * fwrite data into file.
	 *
	 * @access private
	 */
	function _writeFile( $file_name, $data ) 
	{
		$file_name   = $this->root . $file_name;
		$file_handle = fopen( $file_name, 'w' );
		
		fwrite( $file_handle, $data );
		fclose( $file_handle );
	}
} // END OF SimpleTemplate

?>