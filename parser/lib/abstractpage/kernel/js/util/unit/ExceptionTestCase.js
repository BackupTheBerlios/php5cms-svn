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
 * A TestCase that expects an exception of class mClass to be thrown.
 * The other way to check that an expected exception is thrown is:
 * <pre>
 * try
 * {
 *   	this.shouldThrow();
 * }
 * catch ( ex ) 
 * {
 *   	if (ex instanceof SpecialException)
 *   		return;
 *   	else
 *      	throw ex;
 * }
 * this.fail( "Expected SpecialException" );
 * </pre>
 *
 * To use ExceptionTestCase, create a TestCase like:
 * <pre>
 * new ExceptionTestCase( "testShouldThrow", SpecialException );
 * </pre>
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * The constructor is initialized with the name of the test and the expected
 * class to be thrown.
 *
 * @param  String   name  The name of the test case.
 * @param  Function class The class to be thrown.
 * @access public
 */
ExceptionTestCase = function( name, clazz )
{
	TestCase.call( this, name )
	
	/**
	 * Save the class.
	 * @type Function
	 */
	this.mClass = clazz;
};


ExceptionTestCase.prototype = new TestCase();

/**
 * Execute the test method expecting that an exception of
 * class mClass or one of its subclasses will be thrown.
 *
 * @access public
 */
ExceptionTestCase.prototype.runTest = function()
{
	try
	{
		TestCase.prototype.runTest.call( this );
	}
	catch( ex )
	{
		if ( ex instanceof this.mClass )
			return;
		else
			throw ex;
	}
	
	this.fail( "Expected exception " + this.mClass.toString() );
};
