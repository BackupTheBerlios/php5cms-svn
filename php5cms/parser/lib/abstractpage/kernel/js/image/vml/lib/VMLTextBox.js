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
 * VMLTextBox Class
 * Defines a text box for a shape.
 *
 * @package image_vml_lib
 */

/**
 * Constructor
 *
 * @access public
 */
VMLTextBox = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":textbox";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
	
	this.style = this.elm.style;
};


VMLTextBox.prototype = new VMLElement();
VMLTextBox.prototype.constructor = VMLTextBox;
VMLTextBox.superclass = VMLElement.prototype;

/**
 * Defines the direction of the text.
 *
 * @access public
 */
VMLTextBox.prototype.setDirection = function( val )
{
	if ( val != null && ( val == "ltr" || val == "rtl" || val == "context" ) )
		this.style.direction = val;
};

/**
 * Specifies inner margin values for textbox text.
 *
 * @access public
 */
VMLTextBox.prototype.setInset = function( val )
{
	if ( val != null )
		this.elm.inset = val;
};

/**
 * Defines how an application will allow custom inset values.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLTextBox.prototype.setInsetMode = function( val )
{
	if ( val != null && ( val == "auto" || val == "custom" ) )
		this.elm.insetmode = val; // o:insetmode
};
*/

/**
 * Determines the flow of the text in the textbox.
 *
 * @access public
 */
VMLTextBox.prototype.setLayoutFlow = function( val )
{
	if ( val != null && ( val == "horizontal" || val == "vertical" || val == "vertical-ideographic" || val == "horizontal-ideographic" ) )
		this.style.layoutflow = val;
};

/**
 * Defines alternate directions for text in textboxes.
 *
 * @access public
 */
/*
VMLTextBox.prototype.setMSODirectionAlt = function( val )
{
	if ( val != null )
		this.style.msodirectionalt = val; // mso-direction-alt
};
*/

/**
 * Determines whether a shape will stretch to fit text.
 *
 * @access public
 */
/*
VMLTextBox.prototype.setMSOFitShapeToText = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.msofitshapetotext = val; // mso-fit-shape-to-text
};
*/

/**
 * Determines whether text will stretch to fit a shape.
 *
 * @access public
 */
/*
VMLTextBox.prototype.setMSOFitTextToShape = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.msofittexttoshape = val; // mso-fit-text-to-shape
};
*/

/**
 * Defines the alternate layout flow for text in a textbox.
 *
 * @access public
 */
/*
VMLTextBox.prototype.setMSOLayoutFlowAlt = function( val )
{
	if ( val != null )
		this.style.msolayoutflowalt = val; // mso-layout-flow-alt
};
*/

/**
 * Specifies the next textbox in a series.
 *
 * @access public
 */
/*
VMLTextBox.prototype.setMSONextTextbox = function( val )
{
	if ( val != null )
		this.style.msonexttextbox = val; // mso-next-textbox
};
*/

/**
 * Determines whether text rotates with a rotated shape.
 *
 * @access public
 */
/*
VMLTextBox.prototype.setMSORotate = function( val )
{
	if ( val != null )
		this.style.msorotate = val; // mso-rotate
};
*/

/**
 * Defines the scaling factor for fitting text to shapes.
 */
/*
VMLTextBox.prototype.setMSOTextScale = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.style.msotextscale = val; // mso-text-scale
};
*/

/**
 * Determines whether text is selectable with a single click.
 *
 * @access public
 */
VMLTextBox.prototype.setSingleClick = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.style.singleclick = val;
};

/**
 * Defines the vertical anchoring of text in a textbox.
 *
 * @access public
 */
/*
VMLTextBox.prototype.setVTextAnchor = function( val )
{
	if ( val != null && (
		 val == "top"                 ||
		 val == "middle"              ||
		 val == "bottom"              ||
		 val == "top-center"          ||
		 val == "middle-center"       ||
		 val == "bottom-center"       ||
		 val == "top-baseline"        ||
		 val == "bottom-baseline"     ||
		 val == "top-center-baseline" ||
		 val == "bottom-center-baseline" ) ) this.elm.vtextanchor = val; // v-text-anchor
};
*/
