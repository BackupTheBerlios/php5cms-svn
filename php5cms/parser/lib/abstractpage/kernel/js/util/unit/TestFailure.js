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
 * A TestFailure collects a failed test together with the caught exception.
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * @param  Test  test   The failed test.
 * @param  Error except The thrown error of the exception
 * @see    TestResult
 * @access public
 */
TestFailure = function( test, except )
{
	this.mException = except;
	this.mTest = test;
};

/**
 * Retrieve the failed test.
 *
 * @return Test Returns the failed test.
 * @access public
 */
TestFailure.prototype.failedTest = function() 
{ 
	return this.mTest; 
};

/**
 * Retrieve the thrown exception.
 *
 * @return Test Returns the thrown exception.
 * @access public
 */
TestFailure.prototype.thrownException = function() 
{ 
	return this.mException; 
};

/**
 * Retrieve failure as string.
 *
 * @return String Returns the error message.
 * @access public
 */
TestFailure.prototype.toString = function() 
{ 
	return "Test " + this.mTest + " failed: " + this.mException; 
};
