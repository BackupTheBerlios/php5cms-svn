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
 * Usage:
 *
 *	 $bbcode = new BBCode();
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'b',
 *	 		'HtmlBegin'			=> '<span style="font-weight: bold;">',
 *	 		'HtmlEnd'			=> '</span>'
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'i',
 *	 		'HtmlBegin'			=> '<span style="font-style: italic;">',
 *	 		'HtmlEnd'			=> '</span>'
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'u',
 *	 		'HtmlBegin'			=> '<span style="text-decoration: underline;">',
 *	 		'HtmlEnd'			=> '</span>'
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'link',
 *	 		'HasParam'			=> true,
 *	 		'HtmlBegin'			=> '<a href="%%P%%">',
 *	 		'HtmlEnd'			=> '</a>'
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'color',
 *	 		'HasParam'			=> true,
 *	 		'ParamRegex'		=> '[A-Za-z0-9#]+',
 *	 		'HtmlBegin'			=> '<span style="color: %%P%%;">',
 *	 		'HtmlEnd'			=> '</span>',
 *	 		'ParamRegexReplace'	=> array( '/^[A-Fa-f0-9]{6}$/' => '#$0' )
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'email',
 *	 		'HasParam'			=> true,
 *	 		'HtmlBegin'			=> '<a href="mailto:%%P%%">',
 *	 		'HtmlEnd'			=> '</a>'
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'size',
 *	 		'HasParam'			=> true,
 *	 		'HtmlBegin'			=> '<span style="font-size: %%P%%pt;">',
 *	 		'HtmlEnd'			=> '</span>',
 *	 		'ParamRegex'		=> '[0-9]+'
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'bg',
 *	 		'HasParam'			=> true,
 *	 		'HtmlBegin'			=> '<span style="background: %%P%%;">',
 *	 		'HtmlEnd'			=> '</span>',
 *	 		'ParamRegex'		=> '[A-Za-z0-9#]+'
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 's',
 *	 		'HtmlBegin'			=> '<span style="text-decoration: line-through;">',
 *	 		'HtmlEnd'			=> '</span>'
 *	 	)
 *	 );
 *	 $bbcode->add_tag(
 *	 	array(
 *	 		'Name'				=> 'align',
 *	 		'HtmlBegin'			=> '<div style="text-align: %%P%%">',
 *	 		'HtmlEnd'			=> '</div>',
 *	 		'HasParam'			=> true,
 *	 		'ParamRegex'		=> '(center|right|left)'
 *	 	)
 *	 );
 *	 
 *	 $bbcode->add_alias( 'url', 'link' );
 *	 
 *	 print $bbcode->parse('[b]Bold text[/b]
 *	 [i]Italic text[/i]
 *	 [u]Underlinex text[/u]
 *	 [link=http://phpclasses.org/]A link[/link]
 *	 [url=http://phpclasses.org/]Another link[/url]
 *	 [color=red]Red text[/color]
 *	 [email=eurleif@ecritters.biz]Email me![/email]
 *	 [size=20]20-point text[/size]
 *	 [bg=red]Text with a red background[/bg]
 *	 [s]Text with a line through it[/s]
 *	 [align=center]Centered text[/align]');
 *
 * Result:
 *
 *	 <span style="font-weight: bold;">Bold text</span>
 *	 <span style="font-style: italic;">Italic text</span>
 *	 <span style="text-decoration: underline;">Underlinex text</span>
 *	 <a href="http://phpclasses.org/">A link</a>
 *	 <a href="http://phpclasses.org/">Another link</a>
 *	 <span style="color: red;">Red text</span>
 *	 <a href="mailto:eurleif@ecritters.biz">Email me!</a>
 *	 <span style="font-size: 20pt;">20-point text</span>
 *	 <span style="background: red;">Text with a red background</span>
 *	 <span style="text-decoration: line-through;">Text with a line through it</span>
 *	 <div style="text-align: center">Centered text</div>
 *
 * @package html
 */

class BBCode extends PEAR
{
	/**
	 * @access public
	 */
	var $tags;
	
	/**
	 * @access public
	 */
	var $settings;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function BBCode()
	{
		$this->tags     = array();
		$this->settings = array( 'enced' => true );
	}
	
	
	/**
	 * @access public
	 */
	function get_data( $name, $cfa = '' )
	{
		if ( !array_key_exists( $name, $this->tags ) ) 
			return '';
		
		$data = $this->tags[$name];
		
		if ( $cfa ) 
			$sbc = $cfa; 
		else 
			$sbc = $name;
		
		if ( !is_array( $data ) )
		{
			$data = preg_replace( '/^ALIAS(.+)$/', '$1', $data );
			return $this->get_data( $data, $sbc );
		}
		else
		{
			$data['Name'] = $sbc;
			return $data;
		}
	}

	/**
	 * @access public
	 */	
	function change_setting( $name, $value )
	{
		$this->settings[$name] = $value;
	}
	
	/**
	 * @access public
	 */
	function add_alias( $name, $aliasof )
	{
		if ( !array_key_exists( $aliasof, $this->tags ) || array_key_exists( $name, $this->tags ) ) 
			return false;
		
		$this->tags[$name] = 'ALIAS' . $aliasof;
		return true;
	}
	
	/**
	 * @access public
	 */
	function onparam( $param, $regexarray )
	{
		$param = BBCode::replace_pcre_array( $param, $regexarray );
		
		if ( !$this->settings['enced'] )
			$param = htmlentities( $param );
		
		return $param;
	}
	
	/**
	 * @access public
	 */
	function export_definition()
	{
		return serialize( $this->tags );
	}
	
	/**
	 * @access public
	 */
	function import_definiton( $definition, $mode = 'append' )
	{
		switch ( $mode )
		{
			case 'append':
				$array = unserialize( $definition );
				$this->tags = $array + $this->tags;
				break;
			
			case 'prepend':
				$array = unserialize( $definition );
				$this->tags = $this->tags + $array;		
				break;
			
			case 'overwrite':
				$this->tags = unserialize( $definition );
				break;
			
			default:
				return false;
		}
		
		return true;
	}
	
	/**
	 * @access public
	 */
	function add_tag( $params )
	{
		if ( !is_array( $params ) ) 
			return PEAR::raiseError( 'Paramater array not an array.' );
		
		if ( !array_key_exists( 'Name', $params ) || empty( $params['Name'] ) ) 
			return PEAR::raiseError( 'Name parameter is required.' );
		
		if ( preg_match( '/[^A-Za-z]/', $params['Name'] ) ) 
			return PEAR::raiseError( 'Name can only contain letters.' );
		
		if ( !array_key_exists( 'HasParam', $params ) ) 
			$params['HasParam'] = false;
		
		if ( !array_key_exists( 'HtmlBegin', $params ) ) 
			return PEAR::raiseError( 'HtmlBegin paremater not specified!' );
		
		if ( !array_key_exists( 'HtmlEnd', $params ) )
		{
			 if ( preg_match( '/^(<[A-Za-z]>)+$/', $params['HtmlBegin'] ) )
			 	$params['HtmlEnd'] = BBCode::begtoend( $params['HtmlBegin'] );
			 else
			 	return PEAR::raiseError( 'You didn\'t specify the HtmlEnd parameter, and your HtmlBegin parameter is too complex to change to an HtmlEnd parameter. Please specify HtmlEnd.' );
		}
		
		if ( !array_key_exists( 'ParamRegexReplace', $params ) ) 
			$params['ParamRegexReplace'] = array();
		
		if ( !array_key_exists( 'ParamRegex', $params ) ) 
			$params['ParamRegex'] = '[^\\]]+';
		
		if ( !array_key_exists( 'HasEnd', $params ) ) 
			$params['HasEnd'] = true;
		
		if ( array_key_exists( $params['Name'], $this->tags ) ) 
			return PEAR::raiseError( 'The name you specified is already in use.' );
		
		$this->tags[$params['Name']] = $params;
		return '';
	}
	
	/**
	 * @access public
	 */
	function parse( $text )
	{
		foreach ( $this->tags as $tagname => $tagdata )
		{
			if ( !is_array( $tagdata ) ) 
				$tagdata = $this->get_data( $tagname );
			
			$startfind = "/\\[{$tagdata['Name']}";
			
			if ( $tagdata['HasParam'] )
				$startfind.= '=(' . $tagdata['ParamRegex'] . ')';
			
			$startfind.= '\\]/';
			
			if ( $tagdata['HasEnd'] )
			{
				$endfind   = "[/{$tagdata['Name']}]";
				$starttags = preg_match_all( $startfind, $text, $ignore );
				$endtags   = substr_count( $text, $endfind );

				if ( $endtags < $starttags )
					$text .= str_repeat( $endfind, $starttags - $endtags );
				
				$text = str_replace( $endfind, $tagdata['HtmlEnd'], $text );
			}
			
			$replace = str_replace( array( '%%P%%', '%%p%%' ), '\' . $this->onparam( \'$1\', $tagdata[\'ParamRegexReplace\'] ) . \'', '\'' . $tagdata['HtmlBegin'] . '\'' );
			$text = preg_replace( $startfind . 'e', $replace, $text );
		}
		
		return $text;
	}
	
	
	// static methods

	/**
	 * @access public
	 * @static
	 */	
	function begtoend( $htmltag )
	{
		return preg_replace( '/<([A-Za-z]+)>/', '</$1>', $htmltag );
	}

	/**
	 * @access public
	 * @static
	 */
	function replace_pcre_array( $text, $array )
	{
		$pattern = array_keys( $array );
		$replace = array_values( $array );
		$text    = preg_replace( $pattern, $replace, $text );
	
		return $text;
	}
} // END OF BBCode

?>
