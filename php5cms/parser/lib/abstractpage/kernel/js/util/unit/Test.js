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
 * A test can be run and collect its results.
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * @access public
 */
Test = function()
{
};


/**
 * Counts the number of test cases that will be run by this test.
 *
 * @return Number The number of test cases.
 * @access public
 */
Test.prototype.countTestCases = function() 
{
};

/**
 * Search a test by name.
 *
 * The function compares the given name with the name of the test and 
 * returns its own instance if the name is equal.
 *
 * @param  String testName The name of the searched test.
 * @return Test   The test instance itself of null.
 * @access public
 */
Test.prototype.findTest = function( testName ) 
{
};

/**
 * Retrieves the name of the test.
 *
 * @return String The name of test.
 * @access public
 */
Test.prototype.getName = function() 
{
};

/**
 * Runs the test.
 *
 * @param  TestResult result The result to fill.
 * @return TestResult The result of test cases.
 * @access public
 */
Test.prototype.run = function( result ) 
{
};

/**
 * Sets the name of the test.
 *
 * @param  String testName The new name of the test.
 * @access public
 */
Test.prototype.setName = function( testName ) 
{
};
