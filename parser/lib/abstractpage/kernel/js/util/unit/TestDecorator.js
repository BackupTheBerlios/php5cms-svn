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
 * A Decorator for Tests. Use TestDecorator as the base class
 * for defining new test decorators. Test decorator subclasses
 * can be introduced to add behaviour before or after a test
 * is run.
 *
 * @package util_unit
 */
 
/**
  * Constructor
  *
  * The constructore saves the test.
  *
  * @param  Test  test  The test to decorate
  * @access public
  */
TestDecorator = function( test )
{
	Assert.call( this );
	this.mTest = test;
};


TestDecorator.prototype = new Assert();

/**
 * The basic run behaviour. The function calls the run method of the decorated
 * test.
 *
 * @param  TestResult  result  The test result.
 * @access public
 */
TestDecorator.prototype.basicRun = function( result ) 
{ 
	this.mTest.run( result ); 
};

/**
 * Returns the number of the test cases.
 *
 * @access public
 */
TestDecorator.prototype.countTestCases = function() 
{ 
	return this.mTest.countTestCases();
};

/** 
 * Returns the test if it matches the name.
 *
 * @param  String  name  The searched test name.
 * @access public
 */
TestDecorator.prototype.findTest = function( name ) 
{ 
	return this.mTest.findTest( name ); 
};

/** 
 * Returns name of the test.
 *
 * @access public
 */
TestDecorator.prototype.getName = function() 
{ 
	return this.mTest.getName(); 
};

/** 
 * Returns name the decorated test.
 *
 * @access public
 */
TestDecorator.prototype.getTest = function() 
{ 
	return this.mTest; 
};

/**
 * Run the test.
 *
 * @param  TestResult result The test result.
 * @access public
 */
TestDecorator.prototype.run = function( result ) 
{ 
	this.basicRun( result ); 
};

/** 
 * Sets name of the test.
 *
 * @param  String name The new name of the test.
 * @access public
 */
TestDecorator.prototype.setName = function( name ) 
{ 
	this.mTest.setName( name ); 
};

/** 
 * Returns the test as string.
 *
 * @access public
 */
TestDecorator.prototype.toString = function() 
{ 
	return this.mTest.toString(); 
};


TestDecorator.fulfills( Test );
