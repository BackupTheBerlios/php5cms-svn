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
 * A test case defines the fixture to run multiple tests. 
 * To define a test case
 * -# implement a subclass of TestCase
 * -# define instance variables that store the state of the fixture
 * -# initialize the fixture state by overriding <code>setUp</code>
 * -# clean-up after a test by overriding <code>tearDown</code>.
 * Each test runs in its own fixture so there can be no side effects among 
 * test runs.
 *
 * For each test implement a method which interacts
 * with the fixture. Verify the expected results with assertions specified
 * by calling <code>assert</code> with a boolean or one of the other assert 
 * functions.
 *
 * Once the methods are defined you can run them. The framework supports
 * both a static and more generic way to run a test.
 * In the static way you override the runTest method and define the method to
 * be invoked.
 * The generic way uses the JavaScript functionality to enumerate a function's
 * methods to implement <code>runTest</code>. In this case the name of the case
 * has to correspond to the test method to be run.
 *
 * The tests to be run can be collected into a TestSuite. JsUnit provides
 * several <i>test runners</i> which can run a test suite and collect the
 * results.
 * A test runner expects a function <code><i>FileName</i>Suite</code> as the 
 * entry point to get a test to run.
 *
 * @package util_unit
 * @see TestResult
 * @see TestSuite
 */
 
/**
 * Constructor
 *
 * Constructs a test case with the given name.
 *
 * @param  String  name  The name of the test case.
 * @access public
 */
TestCase = function( name )
{
	Assert.call( this );
	this.mName = name;
};


TestCase.prototype = new Assert();

/**
 * Counts the number of test cases that will be run by this test.
 *
 * @return Number Returns 1.
 * @access public
 */
TestCase.prototype.countTestCases = function() 
{ 
	return 1; 
};

/**
 * Creates a default TestResult object.
 *
 * @return TestResult Returns the new object.
 * @access public
 */
TestCase.prototype.createResult = function() 
{ 
	return new TestResult(); 
};

/**
 * Find a test by name.
 *
 * @param  String testName The name of the searched test.
 * @return Test Returns this if the test's name matches or null.
 * @access public
 */
TestCase.prototype.findTest = function( testName ) 
{ 
	return testName == this.mName? this : null; 
};

/**
 * Retrieves the name of the test.
 *
 * @return String The name of test cases.
 * @access public
 */
TestCase.prototype.getName = function() 
{ 
	return this.mName; 
};

/**
 * Runs a test and collects its result in a TestResult instance.
 * The function can be called with or without argument. If no argument is
 * given, the function will create a default result set and return it.
 * Otherwise the return value can be omitted.
 *
 * @param  TestResult result The test result to fill.
 * @return TestResult Returns the test result.
 * @access public
 */
TestCase.prototype.run = function( result )
{
	if ( !result )
		result = this.createResult();
	
	result.run( this );
	return result;
};

/**
 * @access public
 */
TestCase.prototype.runBare = function()
{
	this.setUp();
	
	try
	{
		this.runTest();
		this.tearDown();
	}
	catch( ex )
	{
		this.tearDown();
		throw ex;
	}
};

/**
 * Override to run the test and assert its state.
 *
 * @access public
 */
TestCase.prototype.runTest = function()
{
	var method = this[this.mName];
	
	if ( method )
		method.call( this );
	else
		this.fail( "Method '" + this.mName + "' not found!" );
};

/**
 * Sets the name of the test case.
 *
 * @param  String name The new name of test cases.
 * @access public
 */
TestCase.prototype.setName = function( name ) 
{ 
	this.mName = name; 
};

/**
 * Retrieve the test case as string.
 *
 * @return String Returns the name of the test case.
 * @access public
 */
TestCase.prototype.toString = function() 
{ 
	/*
	var className = new String( this.constructor ); 
	var regex = /function (\w+)/;
	regex.exec( className );
	className = new String( RegExp.$1 );
	*/
	return this.mName; // + "(" + className + ")"; 
};

/**
 * Set up the environment of the fixture.
 *
 * @access public
 */
TestCase.prototype.setUp = function() 
{
};

/**
 * Clear up the environment of the fixture.
 *
 * @access public
 */
TestCase.prototype.tearDown = function() 
{
};


TestCase.fulfills( Test );
