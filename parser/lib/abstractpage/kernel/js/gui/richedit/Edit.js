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
Edit = function()
{
	this.Base = Base;
	this.Base();
};


Edit.prototype = new Base();
Edit.prototype.constructor = Edit;
Edit.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Edit.activeCSS = "border: 1 solid buttonface; color: windowtext; cursor: text;";

/**
 * @access public
 * @static
 */
Edit.inactiveCSS = "border: 1 solid window; cursor: hand; color: red;";

/**
 * @access public
 * @static
 */
Edit.validTextColor = "windowtext";

/**
 * @access public
 * @static
 */
Edit.invalidTextColor = "buttonshadow";

/**
 * @access public
 * @static
 */
Edit.sourceCodeModeLabel = "Source Code Mode";

/**
 * @access public
 * @static
 */
Edit.wysiwygModeLabel = "WYSIWYG Mode";

/**
 * @access public
 * @static
 */
Edit.doInput = function( el )
{
	var emptyText = el.getAttribute( "emptytext" );
	
	if ( el.editMode == null )
		el.editMode = false;
	
	if ( !el.editMode )
	{
		el.editMode = true;
		
		// first time
		if ( ( el.value == emptyText ) || ( emptyText == null ) )
		{
			emptyText = el.value;
			el.value  = "";
			
			el.setAttribute( "emptytext", emptyText );
		}
		
		el.style.cssText = Edit.activeCSS;
	}
	else // user was in edit mode, so save values and restore
	{
		el.editMode = false;
		el.style.cssText = Edit.inactiveCSS;

		// valid value		
		if ( el.value.replace(/\s/g,"") != "" )
		{
			el.style.color = Edit.validTextColor;
		}
		else
		{
			if ( emptyText == null )
			{
				emptyText = defaultEmptyText;
				el.setAttribute( "emptytext", emptyText );
			}
			
			el.value = emptyText;
			el.style.color = Edit.invalidTextColor;
		}
	}
};

/**
 * @access public
 * @static
 */
Edit.submitText = function()
{
	var text;
	var eb = document.all.editbar._editor;
	
	if ( eb.RichEdit.format == "HTML" )
	{
		if ( Browser.ie6up )
			text = "" + myEditor.frames.textEdit.document.documentElement.outerHTML + "";
		else
			text = "<html>" + myEditor.frames.textEdit.document.body.outerHTML + "</html>";
	}
	else
	{
		text = "" + myEditor.frames.textEdit.document.body.innerText + "";
	}

	alert( text );
};

/**
 * Call the formatting command in the editor.
 *
 * @access public
 * @static
 */
Edit.doFormat = function( what )
{
	var eb = document.all.editbar;
	
	if ( eb && eb._editor && eb._editor.RichEdit.execCommand )
		eb._editor.RichEdit.execCommand( what, arguments[1], arguments[2] );
};

/**
 * Call the swapmodes command in the editor.
 *
 * @access public
 * @static
 */
Edit.swapMode = function( el )
{
	var eb = document.all.editbar._editor;
	eb.RichEdit.swapModes();
	
	if ( eb.RichEdit.format == "HTML" )
	{
		el.innerHTML = Edit.sourceCodeModeLabel;
		Edit.disableEditBar( false );
	}
	else
	{
		el.innerHTML = Edit.wysiwygModeLabel;
		Edit.disableEditBar( true );
	}
};

/**
 * @access public
 * @static
 */
Edit.disableEditBar = function( b )
{
	// formatSelect.children[0].disabled = b
	fontSelect.children[0].disabled = b;
	sizeSelect.children[0].disabled = b;
	
	for ( var i = 0; i < editbar.rows[0].cells.length; i++ )
	{
		if ( editbar.rows[0].cells[i].className == "coolButton" )
		{
			if ( b )
				CoolButton.disable( editbar.rows[0].cells[i] );
			else
				CoolButton.enable( editbar.rows[0].cells[i] );
		}
	}
};

/**
 * @access public
 * @static
 */
Edit.doSelectClick = function( str, el )
{
	if ( el.selectedIndex != -1 )
		Edit.doFormat( str, el.options[el.selectedIndex].value );
};

/**
 * @access public
 * @static
 */
Edit.paletteToogle = function()
{
	palette.style.display = ( palette.style.display == "block" )? "none" : "block";
};

/**
 * @access public
 * @static
 */
Edit.replaceSpecial = function( str )
{
	return str.replace(/\&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
};
