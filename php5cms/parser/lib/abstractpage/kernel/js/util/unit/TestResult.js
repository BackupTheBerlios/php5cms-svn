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
 * A TestResult collects the results of executing a test case.
 * The test framework distinguishes between <i>failures</i> and <i>errors</i>.
 * A failure is anticipated and checked for with assertions. Errors are
 * unanticipated problems like a JavaScript run-time error.
 *
 * @package util_unit
 * @see Test
 */
 
/**
 * Constructor
 *
 * @access public
 */
TestResult = function()
{
	this.mErrors    = new Array();
	this.mFailures  = new Array();
	this.mListeners = new Array();
	this.mRunTests  = 0;
	this.mStop      = 0;
};


/**
 * Add an occured error and call the registered listeners.
 *
 * @param  Test  test The failed test.
 * @param  Error except The thrown exception.
 * @access public
 */
TestResult.prototype.addError = function( test, except )
{
	this.mErrors.push( new TestFailure( test, except ) );
	
	for ( var i = 0; i < this.mListeners.length; ++i )
		this.mListeners[i].addError( test, except );
};

/**
 * Add an occured failure and call the registered listeners.
 *
 * @param  Test test The failed test.
 * @param  Error except The thrown exception.
 * @access public
 */
TestResult.prototype.addFailure = function( test, except )
{
	this.mFailures.push( new TestFailure( test, except ) );
	
	for ( var i = 0; i < this.mListeners.length; ++i )
		this.mListeners[i].addFailure( test, except );
};

/**
 * Add a listener.
 *
 * @param  TestListener listener The listener.
 * @access public
 */
TestResult.prototype.addListener = function( listener ) 
{ 
	this.mListeners.push( listener ); 
};

/**
 * Returns a copy of the listeners.
 *
 * @return Array A copy of the listeners.
 * @access public
 */
TestResult.prototype.cloneListeners = function() 
{ 
	var listeners = new Array();
	
	for ( var i = 0; i < this.mListeners.length; ++i )
		listeners[i] = this.mListeners[i];
	
	return listeners;
};

/**
 * A test ended, inform the listeners.
 *
 * @param  Test test The ended test.
 * @access public
 */
TestResult.prototype.endTest = function( test )
{
	for ( var i = 0; i < this.mListeners.length; ++i )
		this.mListeners[i].endTest( test );
};

/**
 * Retrieve the number of occured errors.
 *
 * @access public
 */
TestResult.prototype.errorCount = function() 
{ 
	return this.mErrors.length; 
};

/**
 * Retrieve the number of occured failures.
 *
 * @access public
 */
TestResult.prototype.failureCount = function() 
{ 
	return this.mFailures.length; 
};

/**
 * Remove a listener.
 *
 * @param  TestListener listener The listener.
 * @access public
 */
TestResult.prototype.removeListener = function( listener ) 
{ 
	for ( var i = 0; i < this.mListeners.length; ++i )
	{
		if ( this.mListeners[i] == listener )
		{
			this.mListeners.splice( i, 1 );
			break;
		}
	}
};

/**
 * Runs a test case.
 *
 * @param  Test test The test case to run.
 * @access public
 */
TestResult.prototype.run = function( test )
{
	this.startTest( test );

	function OnTheFly() 
	{
	}
	
	OnTheFly.prototype.protect = function() 
	{ 
		this.mTest.runBare(); 
	}
	
	OnTheFly.prototype.mTest = test;
	OnTheFly.fulfills( Protectable );
	
	this.runProtected( test, new OnTheFly() );
	this.endTest( test );
};

/**
 * Retrieve the number of run tests.
 *
 * @access public
 */
TestResult.prototype.runCount = function() 
{ 
	return this.mRunTests; 
};

/**
 * Runs a test case protected.
 *
 * To implement your own protected block that logs thrown exceptions, 
 * pass a Protectable to TestResult.runProtected().
 *
 * @param  Test test The test case to run.
 * @param  Protectable p The protectable block running the test.
 * @access public
 */
TestResult.prototype.runProtected = function( test, p )
{
	try
	{
		p.protect();
	}
	catch ( ex )
	{
		if ( ex instanceof AssertionFailedError )
			this.addFailure( test, ex );
		else
			this.addError( test, ex );
	}
};

/**
 * Checks whether the test run should stop.
 *
 * @access public
 */
TestResult.prototype.shouldStop = function() 
{ 
	return this.mStop; 
};

/**
 * A test starts, inform the listeners.
 *
 * @param  Test test The test to start.
 * @access public
 */
TestResult.prototype.startTest = function( test )
{
	++this.mRunTests;

	for ( var i = 0; i < this.mListeners.length; ++i )
		this.mListeners[i].startTest( test );
};

/**
 * Marks that the test run should stop.
 *
 * @access public
 */
TestResult.prototype.stop = function() 
{ 
	this.mStop = 1; 
};

/**
 * Returns whether the entire test was successful or not.
 *
 * @access public
 */
TestResult.prototype.wasSuccessful = function() 
{ 
	return this.mErrors.length + this.mFailures.length == 0; 
};


TestResult.fulfills( TestListener );
