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
 * A Decorator that runs a test repeatedly.
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * @param  Test test The test to repeat.
 * @param  Number repeat The number of repeats.
 * @access public
 */
RepeatedTest = function( test, repeat )
{
	TestDecorator.call( this, test );
	this.mTimesRepeat = repeat;
};


RepeatedTest.prototype = new TestDecorator();

/**
 * @access public
 */
RepeatedTest.prototype.countTestCases = function()
{
	var tests = TestDecorator.prototype.countTestCases.call( this );
	return tests * this.mTimesRepeat;
};

/**
 * Runs a test case with additional set up and tear down.
 *
 * @param  TestResult  result  The result set.
 * @access public
 */
RepeatedTest.prototype.run = function( result )
{
	for ( var i = 0; i < this.mTimesRepeat; i++ )
	{
		if ( result.shouldStop() )
			break;
			
		TestDecorator.prototype.run.call( this, result );
	}
};

/**
 * @access public
 */
RepeatedTest.prototype.toString = function()
{
	return TestDecorator.prototype.toString.call( this ) + " (repeated)";
};
