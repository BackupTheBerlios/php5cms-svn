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
 * A Decorator to set up and tear down additional fixture state.
 * Subclass TestSetup and insert it into your tests when you want
 * to set up additional state once before the tests are run.
 *
 * @package util_unit
 */

/**
 * Constructor
 *
 * @param  Test   test  The test to decorate.
 * @access public
 */ 
TestSetup = function( test )
{
	TestDecorator.call( this, test );
};

TestSetup.prototype = new TestDecorator();

/**
 * Runs a test case with additional set up and tear down.
 *
 * @param  TestResult  result  The result set.
 * @access public
 */
TestSetup.prototype.run = function( result )
{
	function OnTheFly() 
	{
	}
	
	OnTheFly.prototype.protect = function()
	{	
		this.mTestSetup.setUp();
		this.mTestSetup.basicRun( this.result );
		this.mTestSetup.tearDown();
	}
	
	OnTheFly.prototype.result = result;
	OnTheFly.prototype.mTestSetup = this;
	OnTheFly.fulfills( Protectable );
	
	result.runProtected( this.mTest, new OnTheFly() );
};

/**
 * Sets up the fixture. Override to set up additional fixture state.
 *
 * @access public
 */
TestSetup.prototype.setUp = function() 
{
};

/**
 * Tears down the fixture. Override to tear down the additional fixture state.
 *
 * @access public
 */
TestSetup.prototype.tearDown = function() 
{
};
