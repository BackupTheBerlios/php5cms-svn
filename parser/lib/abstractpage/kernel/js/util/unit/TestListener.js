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
 * A listener for test progress.
 *
 * @package util_unit
 */

/**
 * Constructor
 *
 * @access public
 */
TestListener = function()
{
};


/**
 * An occured error was added.
 *
 * @param  Test   test The failed test.
 * @param  Error  except The thrown exception.
 * @access public
 */
TestListener.prototype.addError = function( test, except ) 
{
};

/**
 * An occured failure was added.
 *
 * @param  Test   test The failed test.
 * @param  Error  except The thrown exception.
 * @access public
 */
TestListener.prototype.addFailure = function( test, except ) 
{
};

/**
 * A test ended.
 *
 * @param  Test   test  The ended test.
 * @access public
 */
TestListener.prototype.endTest = function( test ) 
{
};

/**
 * A test started.
 *
 * @param  Test   test  The started test.
 * @access public
 */
TestListener.prototype.startTest = function( test ) 
{
};
