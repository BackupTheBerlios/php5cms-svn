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
 * Class for an application running test suites with a test based status report.
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * @access public
 */
TextTestRunner = function()
{
	TestRunner.call( this );

	this.mRunTests  = 0;
	this.mNest      = "";
	this.mStartArgs = new Array();
};


TextTestRunner.prototype = new TestRunner();

/**
 * An occured error was added.
 *
 * @param  Test  test The failed test.
 * @param  Error except The thrown exception.
 * @access public
 */
TextTestRunner.prototype.addError = function( test, except )
{
	var str = "";
	
	if ( except.message || except.description )
	{
		if ( except.name )
			str = except.name + ": ";
		
		str += except.message || except.description;
	}
	else
	{
		str = except;
	}
	
	this.writeLn( "ERROR in " + test + ": " + str );
};

/**
 * An occured failure was added.
 *
 * @param  Test test The failed test.
 * @param  Error except The thrown exception.
 * @access public
 */
TextTestRunner.prototype.addFailure = function( test, except )
{
	this.writeLn( "FAILURE in " + test + ": " + except );
	this.writeLn( except.mCallStack );
};

/**
 * A test ended.
 *
 * @param  Test test The ended test.
 * @access public
 */
TextTestRunner.prototype.endTest = function( test )
{
	if ( test.testCount != null )
	{
		this.mNest = this.mNest.substr( 1 );
		this.writeLn( "<" + this.mNest.replace( /-/g, "=" ) + " Completed test suite \"" + test.getName() + "\"" );
	}
};

/**
 * Write a header starting the application.
 *
 * @access public
 */
TextTestRunner.prototype.printHeader = function()
{
	this.writeLn( "TestRunner(" + this.mStartArgs[0] + ") (" + this.countTestCases() + " test cases available)" );
};

/**
 * Write a footer at application end with a summary of the tests.
 *
 * @param  TestResult result The result of the test run.
 * @access public
 */
TextTestRunner.prototype.printFooter = function( result )
{
	if ( result.wasSuccessful() == 0 )
	{
		var error   = ( result.errorCount()   != 1 )? " errors"   : " error";
		var failure = ( result.failureCount() != 1 )? " failures" : " failure";
		
		this.writeLn( result.errorCount() + error + ", " + result.failureCount() + failure + "." );
	}
	else
	{
		this.writeLn( result.runCount() + " tests successful in " + ( this.mElapsedTime / 1000 ) + " seconds." );
	}
};

/**
 * Start the test functionality of the application.
 *
 * @param  args list of test names in an array or a single test name
 * @return Number 0 if no test fails, otherwise -1
 * @access public
 */
TextTestRunner.prototype.start = function( args )
{
	if ( typeof( args ) == "undefined" )
		args = new Array();
	else if ( typeof( args ) == "string" )
		args = new Array( args );
	
	if ( args.length == 0 )
		args[0] = "all";
	
	this.mStartArgs = args;
	var result = this.createTestResult();
	result.addListener( this );
	this.printHeader();
	
	if ( args[0] == "all" )
	{
		this.runAll( result );
	}
	else
	{
		for ( var i = 0; i < args.length; ++ i )
			this.run( args[i], result );
	}
	
	this.printFooter( result );
	return result.wasSuccessful()? 0 : -1;
};

/**
 * A test started.
 *
 * @param  Test test The started test.
 * @access public
 */
TextTestRunner.prototype.startTest = function( test )
{
	if ( test.testCount == null )
	{
		++this.mRunTests;
		this.writeLn( this.mNest + " Running test " + this.mRunTests + ": \"" + test + "\"" );
	}
	else
	{
		this.writeLn( this.mNest.replace(/-/g, "=") + "> Starting test suite \"" + test.getName() + "\"" );
		this.mNest += "-";
	}
};

/**
 * Write a line of text.
 *
 * The method of this object does effectivly nothing. It must be 
 * overloaded with a proper version, that knows how to print a line,
 * if the script engine cannot be detected (yet).
 *
 * @param  String str The text to print on the line.
 * @access public
 */
TextTestRunner.prototype.writeLn = function ( str )
{
	JsUtil.prototype.print( str );
};
