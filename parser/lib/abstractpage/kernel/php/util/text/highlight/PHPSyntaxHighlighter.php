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
 * Highlights PHP syntax
 *
 * Example:
 * <code>
 * $p = &new PHPSyntaxHighlighter( new File( __FILE__ ) );
 * echo $p->getHighlight();
 * </code>
 *
 * @see php://highlight_string
 * @package util_text_highlight
 */

class PHPSyntaxHighlighter extends PEAR
{  
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed input default NULL a string or a file object
     */
    function PHPSyntaxHighlighter( $input = null ) 
	{
      	if ( is_a( $input, 'File' ) )
        	$this->setFile( $input );
      	else
        	$this->setSource( $input );
      
      	// Set some defaults
      	$this->setStyle( 'string',   'color: darkblue' );
      	$this->setStyle( 'comment',  'color: gray' );
      	$this->setStyle( 'keyword',  'color: darkred; font-weight: bold' );
      	$this->setStyle( 'default',  'color: black' );
      	$this->setStyle( 'html',     'color: lightgray' );
      	$this->setStyle( 'variable', 'color: darkblue; font-weight: bold' );
    }
    
	
    /**
     * Sets style for keywords.
     * Keywords are: string, comment, keyword, default, html, variable
     *
     * @access  public
     * @param   string what one of the keywords listed above
     * @param   string style anything which will work within style="??????"
     */
    function setStyle( $what, $style ) 
	{
      	$this->styles[$what] = $style;
      	ini_set( 'highlight.' . $what, $style );
    }
    
    /**
     * Sets style for strings.
     *
     * @access  public
     * @param   string style
     * @see     #setStyle
     */
    function setStringStyle( $style ) 
	{
      	$this->setStyle( 'string', $style );
    }

    /**
     * Sets style for comments.
     *
     * @access  public
     * @param   string style
     * @see     #setStyle
     */
    function setCommentStyle( $style ) 
	{
      	$this->setStyle( 'comment', $style );
    }
  
    /**
     * Sets style for keywords.
     *
     * @access  public
     * @param   string style
     * @see     #setStyle
     */
    function setKeywordStyle( $style ) 
	{
      	$this->setStyle( 'keyword', $style );
    }
    
    /**
     * Sets default style.
     *
     * @access  public
     * @param   string Style
     * @see     #setStyle
     */
    function setDefaultStyle( $style ) 
	{
      	$this->setStyle( 'default', $style );
    }

    /**
     * Sets style for HTML.
     *
     * @access  public
     * @param   string style
     * @see     #setStyle
     */
    function setHtmlStyle( $style ) 
	{
      	$this->setStyle( 'html', $style );
    }

    /**
     * Sets sourcecode string to higlight. Will require the leading
     * <?php and an ?> at the end.
     *
     * @access  publuc
     * @param   string source 
     */
    function setSource( $source ) 
	{
      	$this->source = $source;
    }

    /**
     * Sets file to highlight.
     *
     * @access  public
     * @param   file.File file
     */    
    function setFile( &$file ) 
	{
      	$file->open( FILE_MODE_READ );
      	$this->source = $file->read( $file->size() );
      	$file->close();
    }
    
    /**
     * Retrieve highlighted code. Will be XML-conform since &nbsp;
     * is replaced by &#160;. The deprecated <font>-Tag is replaced
     * by 
     *
     * @access  public
     * @return  string highlighted source
     */
    function getHighlight()
	{
      	ob_start();
      	highlight_string( $this->source );
      	$s = ob_get_contents();
      	ob_end_clean();
      
	  	return preg_replace(
			array(
          		',&nbsp;,', 
          		',<font color="([^"]+)">,', 
          		',</font>,',
          		',\$[a-z0-9_]+,i',
          		',\b(uses|implements|is|try|catch|throw|finally)\b,'
        	),
        	array(
          		'&#160;', 
          		'<span style="$1">', 
          		'</span>',
          		'<span style="'.$this->styles['variable'].'">$0</span>',
          		'<span style="'.$this->styles['keyword'].'">$1</span>'
        	),
        	$s
		);
    }
} // END OF PHPSyntaxHighlighter

?>
