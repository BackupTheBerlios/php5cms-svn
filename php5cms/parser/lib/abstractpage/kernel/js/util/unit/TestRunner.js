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
 * General base class for an application running test suites.
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * @access public
 */
TestRunner = function()
{
	this.mSuites = new TestSuite();
	this.mElapsedTime = 0;
};


/**
 * Add a test suite to the application.
 *
 * @param  TestSuite suite The suite to add.
 * @access public
 */
TestRunner.prototype.addSuite = function( suite ) 
{ 
	this.mSuites.addTest( suite ); 
};

/**
 * Counts the number of test cases that will be run by this test 
 * application.
 *
 * @return Number The number of test cases.
 * @access public
 */
TestRunner.prototype.countTestCases = function() 
{ 
	return this.mSuites.countTestCases(); 
};

/**
 * The milliseconds needed to execute all registered tests of the runner.
 * This number is 0 as long as the test was never started.
 *
 * @return Number The milliseconds.
 * @access public
 */
TestRunner.prototype.countMilliSeconds = function() 
{ 
	return this.mElapsedTime; 
};

/**
 * Creates an instance of a TestResult.
 *
 * @return TestResult Returns the new TestResult instance.
 * @access public
 */
TestRunner.prototype.createTestResult = function() 
{ 
	return new TestResult(); 
};

/**
 * Runs all test of all suites and collects their results in a TestResult 
 * instance.
 *
 * @param  String name The name of the test.
 * @param  TestResult result The test result to fill.
 * @access public
 */
TestRunner.prototype.run = function( name, result )
{
	var test = this.mSuites.findTest( name );
	
	if ( test == null )
	{
		var ex = new AssertionFailedError( "Test \"" + name + "\" not found.", new CallStack() );
		result.addFailure( new Test( name ), ex );
	}
	else
	{
		this.mElapsedTime = new Date();
		test.run( result );
		this.mElapsedTime = new Date() - this.mElapsedTime;
	}
};

/**
 * Runs all test of all suites and collects their results in a TestResult 
 * instance.
 *
 * @param  TestResult result The test result to fill.
 * @access public
 */
TestRunner.prototype.runAll = function( result ) 
{ 
	this.mElapsedTime = new Date();
	this.mSuites.run( result ); 
	this.mElapsedTime = new Date() - this.mElapsedTime;
};

/**
 * @access public
 */
TestRunner.prototype.addError = function( test, except ) 
{
};

/**
 * @access public
 */
TestRunner.prototype.addFailure = function( test, except ) 
{
};

/**
 * @access public
 */
TestRunner.prototype.endTest = function( test ) 
{
};

/**
 * @access public
 */
TestRunner.prototype.startTest = function( test ) 
{
};


TestRunner.fulfills( TestListener );
