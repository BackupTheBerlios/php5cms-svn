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
 * @package gui_richedit
 */
 
/**
 * Constructor
 *
 * @access public
 */
RichEdit = function()
{
	this.Base = Base;
	this.Base();
};


RichEdit.prototype = new Base();
RichEdit.prototype.constructor = RichEdit;
RichEdit.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
RichEdit.format = "HTML";

/**
 * @access public
 * @static
 */
RichEdit.codeStyle = "font-family: courier new; font-size: 12px;";

/**
 * @access public
 * @static
 */
RichEdit.elementCodeStyle = "color: maroon;";

/**
 * Set the focus to the editor.
 *
 * @access public
 * @static
 */
RichEdit.setFocus = function()
{
	textEdit.focus();
};

/**
 * Execute a command against the editor. At minimum one argument is required.
 * Some commands require a second optional argument: eg. ("formatblock","<H1>") to make an H1
 *
 * @access public
 * @static
 */
RichEdit.execCommand = function( command )
{
	if ( RichEdit.format == "HTML" )
	{
		var edit = textEdit.document.selection.createRange();
		
		if ( arguments[1] == null )
			edit.execCommand( command );
		else
			edit.execCommand( command, false, arguments[1] );
		
		edit.select();
	}
};

/**
 * Swap between WYSIWYG mode and raw HTML mode.
 *
 * @access public
 * @static
 */
RichEdit.swapModes = function()
{
	var tmp;
	
	if ( RichEdit.format == "HTML" )
	{
		if ( Browser.ie6up )
			tmp = "" + textEdit.document.documentElement.outerHTML + "";
		else
			tmp = "" + textEdit.document.body.outerHTML + "";
		
		textEdit.document.open();
		textEdit.document.write( RichEdit.formatHTMLCode( tmp ) );
		textEdit.document.close();
		
		RichEdit.format = "Text";
	}
	else
	{
		tmp = "" + textEdit.document.body.innerText + "";
		
		textEdit.document.open();
		textEdit.document.write( tmp );
		textEdit.document.close();
		
		RichEdit.format = "HTML";
	}

	textEdit.focus();
	var s = textEdit.document.body.createTextRange();
	s.collapse( false );
	s.select();
};

/**
 * @access public
 * @static
 */
RichEdit.formatHTMLCode = function( str )
{
	return "<div style='" + RichEdit.codeStyle + "'>" + RichEdit.formatCode( str.replace(/&/g, "&amp;") ) + "</div>";
};

/**
 * @access public
 * @static
 */
RichEdit.formatCode = function( s )
{
	var str        = "";
	var IN_TEXT    = 1;
	var IN_ELEMENT = 2;
	var state      = IN_TEXT;
	
	while ( s.length > 0 )
	{
		var endTagBreak;
		var emptyTagBreak;
		
		if ( state == IN_ELEMENT )
		{
			var endIndex = s.indexOf( ">" );
			var endTag   = ( s.substring( 0, 1 ) == "/" );
			
			if ( endIndex != -1 )
			{
				str += s.substring( 0, endIndex ) + "&gt;</span>";
				
				if ( endTag && endTagBreak || emptyTagBreak )
					str += "<br>";
				
				s = s.substring( endIndex + 1, s.length );
				state = IN_TEXT;
			}
			else
			{
				str += s + "</span>";
				s = "";
			}
		}
		else
		{
			var startIndex = s.indexOf( "<" );
			var startTags  = new Array( "BR", "HR", "META", "HTML", "BODY", "HEAD" );
			var endTags    = new Array( "P", "DIV", "H1", "H2", "H3", "H4", "H5", "H6", "BLOCKQUOTE", "OL", "LI", "PRE", "UL", "TITLE", "BODY", "META", "HEAD" );
			
			if ( startIndex != -1 )
			{
				var tagName;
				var nameStartIndex;
				
				var gtIndex    = s.indexOf( ">" );
				var spaceIndex = s.indexOf( " " );
				var slashIndex = s.indexOf( "/" );
				
				endTagBreak   = false;
				emptyTagBreak = false;

				if ( ( slashIndex != -1 ) && ( slashIndex == startIndex + 1 ) )
					nameStartIndex = slashIndex + 1;
				else
					nameStartIndex = startIndex + 1;
				
				if ( ( spaceIndex != -1 ) && ( spaceIndex > startIndex ) && ( spaceIndex < gtIndex ) )
					tagName = s.substring( nameStartIndex, spaceIndex );
				else if ( gtIndex != -1 )
					tagName = s.substring( nameStartIndex, gtIndex );
				else
					tagName = s.substring( nameStartIndex, s.length );

				for ( var i = 0; i < endTags.length; i++ )
				{
					if ( endTags[i] == tagName )
					{
						endTagBreak = true;
						break;
					}
				}

				for ( var i = 0; i < startTags.length; i++ )
				{
					if ( startTags[i] == tagName )
					{
						emptyTagBreak = true;
						break;
					}
				}
							
				str  += s.substring( 0, startIndex ) + "<span style='" + RichEdit.elementCodeStyle + "'>&lt;";
				s     = s.substring( startIndex + 1, s.length );
				state = IN_ELEMENT;
			}
			else
			{
				str += s;
				s = "";
			}
		}
	}
	
	return str;
};
