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
 * @package xml_dom_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
DOMException = function( code, message )
{
	this.code = code;
	this.message = message;
};


DOMException.prototype = new Error();
DOMException.prototype.constructor = DOMException;
DOMException.superclass = Error.prototype;

/**
 * @constant
 */
DOMException.INDEX_SIZE_ERR = 1;

/**
 * @constant
 */
DOMException.DOMSTRING_SIZE_ERR = 2;

/**
 * @constant
 */
DOMException.HIERARCHY_REQUEST_ERR = 3;

/**
 * @constant
 */
DOMException.WRONG_DOCUMENT_ERR = 4;

/**
 * @constant
 */
DOMException.INVALID_CHARACTER_ERR = 5;

/**
 * @constant
 */
DOMException.NO_DATA_ALLOWED_ERR = 6;

/**
 * @constant
 */
DOMException.NO_MODIFICATION_ALLOWED_ERR = 7;

/**
 * @constant
 */
DOMException.NOT_FOUND_ERR = 8;

/**
 * @constant
 */
DOMException.NOT_SUPPORTED_ERR = 9;

/**
 * @constant
 */
DOMException.INUSE_ATTRIBUTE_ERR = 10;
