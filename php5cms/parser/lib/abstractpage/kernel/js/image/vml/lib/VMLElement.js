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
 * VMLElement Class
 * Abstract object, do not call directly!
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLElement = function()
{
	this.Base = Base;
	this.Base();

	// create pseudo element to prevent errors
	this.elm = new Object;
	this.elm.appendChild = new Function;
	
	this.elements = new Array();
	this.count = 0;
};


VMLElement.prototype = new Base();
VMLElement.prototype.constructor = VMLElement;
VMLElement.superclass = Base.prototype;

/**
 * @access public
 */
VMLElement.prototype.getElementCount = function()
{
	return this.count;
};

/**
 * @access public
 */
VMLElement.prototype.setElementID = function( id )
{
	if ( id != null )
		this.elm.id = id;
};

/**
 * @access public
 */
VMLElement.prototype.getElementID = function( id )
{
	return this.elm.id;
};

/**
 * @access public
 */
VMLElement.prototype.add = function( element )
{
	if ( !this._isValidVMLElement( element ) )
		return false;

	this.elements[this.elements.length] = element;
	this.count++;
	
	return true;
};


// private methods

/**
 * @access private
 */
VMLElement.prototype._isValidVMLElement = function()
{
	// overload
	return false;
};

/**
 * @access private
 */
VMLElement.prototype._build = function()
{
	if ( this.elements.length == 0 )
		return false;
	
	for ( var i in this.elements )
	{
		this.elements[i]._build();
		this.elm.appendChild( this.elements[i].elm );
	}
};


/**
 * @access public
 * @static
 */
VMLElement.idcount = 0;

/**
 * @access private
 * @static
 */
VMLElement._isFraction = function( val )
{
	// percentage value?
	var valString = new String( val );
	var percentageVal = valString.substring( 0, valString.indexOf( "%" ) );
	
	if ( ( val >= 0 && val <= 100 ) || ( val >= 0.0 && val <= 1.0 ) || ( percentageVal >= 0 && percentageVal <= 100 ) )
		return true;
	else
		return false;
};

/**
 * @access private
 * @static
 */
VMLElement._isTriState = function( val )
{
	if ( ( val == true ) || ( val == "true" ) || ( val == false ) || ( val == "false" ) /*|| ( val == "mixed" )*/ )
		return true;
	else
		return false;
};

/**
 * @access private
 * @static
 */
VMLElement._isFixedAngle = function( val )
{
	if ( val == "any" || val == 30 || val == 45 || val == 60 || val == 90 )
		return true;
	else
		return false;
};

/**
 * @access private
 * @static
 */
VMLElement._isVAlign = function( val )
{
	if ( val == "top" || val == "center" || val == "bottom" )
		return true;
	else
		return false;
};

/**
 * @access private
 * @static
 */
VMLElement._isSigma = function( val )
{
	if ( val == "none" || val == "linear" || val == "sigma" || val == "any" )
		return true;
	else
		return false;
};

/**
 * @access private
 * @static
 */
VMLElement._isVector2D = function( val )
{
	val = ( val.indexOf( "," ) != -1 )? val.split( "," ) : val.split( " " );
	
	if ( val.length == 2 )
		return true;
	else
		return false;
};

/**
 * @access private
 * @static
 */
VMLElement._isVector3D = function( val )
{
	val = ( val.indexOf( "," ) != -1 )? val.split( "," ) : val.split( " " );
	
	if ( val.length == 3 )
		return true;
	else
		return false;
};

/**
 * @access private
 * @static
 */
VMLElement._isAngle = function( val )
{
	/*
	if ( ( val >= -360 && val <= 360 ))
		return true;
	else
		return false;
		*/
	
	return true;
};

/**
 * @access private
 * @static
 */
VMLElement._isNumber = function( val )
{
	// TODO
	return true;
};

/**
 * @access private
 * @static
 */
VMLElement._isColor = function( val )
{
	// TODO
	return true;
};

/**
 * @access private
 * @static
 */
VMLElement._isPoints = function( val )
{
	// TODO
	return true;
};

/**
 * @access private
 * @static
 */
VMLElement._isGradientColorArray = function( val )
{
	// TODO
	return true;
};
