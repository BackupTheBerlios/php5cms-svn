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
 * VMLTextPath Class
 * Defines a text path for a shape.
 *
 * @package image_vml_lib
 */

/**
 * Constructor
 *
 * @access public
 */
VMLTextPath = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":textpath";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );

	this.style  = this.elm.style;
};


VMLTextPath.prototype = new VMLElement();
VMLTextPath.prototype.constructor = VMLTextPath;
VMLTextPath.superclass = VMLElement.prototype;

/**
 * Defines whether the text fits the path of a shape.
 *
 * @access public
 */
VMLTextPath.prototype.setFitPath = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.fitpath = val;
};

/**
 * Defines whether the text fits the shape boundaries.
 *
 * @access public
 */
VMLTextPath.prototype.setFitShape = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.fitshape = val;
};

/**
 * Specifies the compound font value of text.
 *
 * @access public
 */
VMLTextPath.prototype.setFont = function( val )
{
	if ( val != null )
		this.style.font = val;
};

/**
 * Specifies the font-family of text.
 *
 * @access public
 */
VMLTextPath.prototype.setFontFamily = function( val )
{
	if ( val != null )
		this.style.fontfamily = val;
};

/**
 * Specifies the font-size of text.
 *
 * @access public
 */
VMLTextPath.prototype.setFontSize = function( val )
{
	if ( val != null )
		this.style.fontsize = val;
};

/**
 * Specifies the font-style of text.
 *
 * @access public
 */
VMLTextPath.prototype.setFontStyle = function( val )
{
	if ( val != null && ( val == "normal" || val == "oblique" || val == "italic" ) )
		this.style.fontstyle = val;
};

/**
 * Specifies the font-variant of text.
 *
 * @access public
 */
VMLTextPath.prototype.setFontVariant = function( val )
{
	if ( val != null && ( val == "normal" || val == "small-caps" ) )
		this.style.fontvariant = val;
};

/**
 * Specifies the font-weight of text.
 *
 * @access public
 */
VMLTextPath.prototype.setFontWeight = function( val )
{
	if ( val != null )
		this.style.fontweight = val;
};

/**
 * Defines whether a shadow is applied to the text.
 *
 * @access public
 */
/*
VMLTextPath.prototype.setMSOTextShadow = function( val )
{
	if ( val != null )
		this.style.msotextshadow = val; // mso-text-shadow
};
*/

/**
 * Defines whether the text is displayed.
 *
 * @access public
 */
VMLTextPath.prototype.setOn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.on = val;
};

/**
 * Defines the text string.
 *
 * @access public
 */
VMLTextPath.prototype.setText = function( val )
{
	if ( val != null )
		this.elm.string = val;
};

/**
 * Defines the text-decoration of the text.
 *
 * @access public
 */
VMLTextPath.prototype.setTextDecoration = function( val )
{
	if ( val != null && ( val == "none" || val == "underline" || val == "overline" || val == "line-through" || val == "blink" ) )
		this.style.textdecoration = val;
};

/**
 * Defines whether extra space is removed above and below the text.
 *
 * @access public
 */
VMLTextPath.prototype.setTrim = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.trim = val;
};


/**
 * Determines whether the letters of the text are rotated.
 *
 * @access public
 */
/*
VMLTextPath.prototype.setVRotateLetters = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.vrotateletters = val; // v-rotate-letters
};
*/

/**
 * Determines whether all letters will have the same height.
 *
 * @access public
 */
/*
VMLTextPath.prototype.setVSameLetterHeights = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.vsameletterheights = val; // v-same-letter-heights
};
*/

/**
 * Defines the alignment of the text.
 *
 * @access public
 */
/*
VMLTextPath.prototype.setVTextAlign = function( val )
{
	if ( val != null && ( val == "left" || val == "right" || val == "center" || val == "justify" || val == "letter-justify" || val == "stretch-justify" ) )
		this.style.vtextalign = val; //v-text-align
};
*/

/**
 * Determines whether kerning is turned on.
 *
 * @access public
 */
/*
VMLTextPath.prototype.setVTextKern = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.vtextkern = val; // v-text-kern
};
*/

/**
 * Determines whether the layout order of rows is reversed.
 *
 * @access public
 */
/*
VMLTextPath.prototype.setVTextReverse = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.vtextreverse = val; // v-text-reverse
};
*/

/**
 * Defines the mode for letterspacing.
 *
 * @access public
 */
/*
VMLTextPath.prototype.setVTextSpacingMode = function( val )
{
	if ( val != null && ( val == "tightening" || val == "tracking" ) )
		this.style.vtextspacingmode = val; // v-text-spacing-mode
};
*/

/**
 * Defines the amount of spacing for text.
 *
 * @access public
 */
/*
VMLTextPath.prototype.setVTextSpacing = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.style.vtextspacing = val; // v-text-spacing
};
*/

/**
 * Determines whether a straight textpath will be used instead of the shape path.
 *
 * @access public
 */
VMLTextPath.prototype.setXScale = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.xscale = val;
};
