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
|Authors: Joerg Schaible <joehni@mail.berlios.de>                      |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Thrown, when a test assertion fails.
 *
 * @package util_unit
 */

/**
 * Constructor
 *
 * An AssertionFailedMessage needs a message and a call stack for construction.
 *
 * @param  String     msg    Failure message.
 * @param  CallStack  stack  The call stack of the assertion.
 * @access public
 */
AssertionFailedError = function( msg, stack )
{
	// Error.call( this, msg );
	this.message = msg;
	
	/**
	 * The call stack for the message.
	 */
	this.mCallStack = stack;
};


AssertionFailedError.prototype = new Error();

/**
 * The name of the TypeError class as String.
 *
 * @type   String
 * @access public
 */
AssertionFailedError.prototype.name = "AssertionFailedError";
