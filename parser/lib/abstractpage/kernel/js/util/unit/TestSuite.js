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
 * A TestSuite is a composition of Tests. It runs a collection of test cases.
 * In despite of the JUnit implementation, this class has also functionality of
 * TestSetup of the extended JUnit framework. This is because of &quot;recursion
 * limits&quot; of the JavaScript implementation of BroadVision's One-to-one
 * Server (an OEM version of Netscape Enterprise Edition).
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * The constructor collects all test methods of the given object and adds them
 * to the array of tests.
 *
 * @param  Object  obj  if obj is an instance of a TestCase, the suite is filled with the 
 *                      fixtures automatically. Otherwise obj's string value is treated as name.
 * @access public
 */
TestSuite = function( obj )
{
	this.mTests = new Array();
	var name, str;
	
	switch ( typeof obj )
	{
		case "function":
			if ( !str )
				str = new String( obj );
			
			name = str.substring( str.indexOf( " " ) + 1, str.indexOf( "(" ) );
			
			if ( name == "(" )
				name = "[anonymous]";
			
			break;
		
		case "string": 
			name = obj; 
			break;
		
		case "object": 
			if ( obj !== null )
				this.addTest( this.warning( "Cannot instantiate test class for " + "object '" + obj + "'" ) );
			
			// fall through
		
		case "undefined": 	
			// fall through
		
		default: 
			name = null; 
			break;
	}

	this.mName = name;

	// collect all testXXX methods
	if ( typeof( obj ) == "function" )
	{
		for ( var member in obj.prototype )
		{
			if ( member.indexOf( "test" ) == 0 )
				this.addTest( new ( obj )( member ) );
		}
	}
};


/**
 * Add a test to the suite.
 *
 * @param  Test test The test to add.
 * @access public
 */
TestSuite.prototype.addTest = function( test ) 
{ 
	this.mTests.push( test ); 
};

/**
 * Add a test suite to the current suite.
 * All fixtures of the test case will be collected in a suite which
 * will be added.
 *
 * @param  TestCase testCase The TestCase object to add.
 * @access public
 */
TestSuite.prototype.addTestSuite = function( testCase ) 
{ 
	this.addTest( new TestSuite( testCase ) ); 
};

/**
 * Counts the number of test cases that will be run by this test suite.
 *
 * @return Number The number of test cases.
 * @access public
 */
TestSuite.prototype.countTestCases = function()
{
	var tests = 0;
	
	for ( var i = 0; i < this.testCount(); ++i )
		tests += this.mTests[i].countTestCases();

	return tests;
};

/**
 * Search a test by name.
 *
 * The function compares the given name with the name of the test and 
 * returns its own instance if the name is equal.
 *
 * @param  String name The name of the searched test.
 * @return String The instance itself of null.
 * @access public
 */
TestSuite.prototype.findTest = function( name )
{
	if ( name == this.mName )
		return this;

	for ( var i = 0; i < this.testCount(); ++i )
	{
		var test = this.mTests[i].findTest( name );
		
		if ( test != null )
			return test;
	}
	
	return null;
};

/**
 * Retrieves the name of the test suite.
 *
 * @return String The name of test suite.
 * @access public
 */
TestSuite.prototype.getName = function() 
{ 
	return this.mName; 
};

/**
 * Runs the tests and collects their result in a TestResult instance.
 *
 * @param  TestResult result The test result to fill.
 * @access public
 */
TestSuite.prototype.run = function( result )
{
	--result.mRunTests;
	result.startTest( this );

	for ( var i = 0; i < this.testCount(); ++i )
	{
		if ( result.shouldStop())
			break;
		
		var test = this.mTests[i];
		this.runTest( test, result );
	}

	if ( i == 0 )
	{
		var ex = new AssertionFailedError( "Test suite with no tests.", new CallStack() );
		result.addFailure( this, ex );
	}

	result.endTest( this );
};

/**
 * Runs a single test test and collect its result in a TestResult instance.
 *
 * @param  Test test The test to run.
 * @param  TestResult result The test result to fill.
 * @access public
 */
TestSuite.prototype.runTest = function( test, result )
{
	test.run( result );
};

/**
 * Sets the name of the suite.
 *
 * @param  String name The name to set.
 * @access public
 */
TestSuite.prototype.setName = function( name ) 
{ 
	this.mName = name; 
};

/**
 * Runs the test at the given index.
 *
 * @param  Number index The index.
 * @access public
 */
TestSuite.prototype.testAt = function( index )
{
	return this.mTests[index];
};

/**
 * Returns the number of tests in this suite.
 *
 * @access public
 */
TestSuite.prototype.testCount = function() 
{ 
	return this.mTests.length; 
};

/**
 * Retrieve the test suite as string.
 *
 * @return String Returns the name of the test case.
 * @access public
 */
TestSuite.prototype.toString = function() 
{ 
	return "Suite '" + this.mName + "'";
};

/**
 * Returns a test which will fail and log a warning message.
 *
 * @param  String message The warning message.
 * @access public
 */
TestSuite.prototype.warning = function( message )
{
	function Warning() 
	{ 
		TestCase.call( this, "warning" ); 
	}
	
	Warning.prototype = new TestCase();
	
	Warning.prototype.runTest = function() 
	{ 
		this.fail( this.mMessage ); 
	}
	
	Warning.prototype.mMessage = message;
	return new Warning();
};


TestSuite.fulfills( Test );
