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
 * @package html_css
 */
 
/**
 * Constructor
 *
 * @access public
 */
StyleSheetRules = function( ssObj )
{
	this.Base = Base;
	this.Base();
	
	this.styleSheetObject = ssObj || document.styleSheets[0];
};


StyleSheetRules.prototype = new Base();
StyleSheetRules.prototype.constructor = StyleSheetRules;
StyleSheetRules.superclass = Base.prototype;

/**
 * @access public
 */
StyleSheetRules.prototype.addRule = function( rule )
{
	this.styleSheetObject.addRule( rule );
};

/**
 * @access public
 */
StyleSheetRules.prototype.removeRule = function( rule )
{
	this.styleSheetObject.removeRule( rule );
};
