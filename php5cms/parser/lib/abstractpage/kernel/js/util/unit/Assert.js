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
 * A set of assert methods.
 *
 * @package util_unit
 */

/**
 * Constructor
 *
 * @access public
 */
Assert = function()
{
};


/**
 * Asserts that a condition is true.
 *
 * @param String msg An optional error message.
 * @param String cond The condition to evaluate.
 * @exception AssertionFailedError Thrown if the evaluation was not true.
 * @access public
 */
Assert.prototype.assert = function( msg, cond )
{
	if ( arguments.length == 1 )
	{
		cond = msg;
		msg  = null;
	}
	
	if ( !eval( cond ) )
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Condition failed \"" + cond + "\"";
		this.fail( m, new CallStack() );
	}
};

/**
 * Asserts that two values are equal.
 *
 * @param String msg An optional error message.
 * @param Object expected The expected value.
 * @param Object actual The actual value.
 * @exception AssertionFailedError Thrown if the expected value is not the actual one.
 * @access public
 */
Assert.prototype.assertEquals = function( msg, expected, actual )
{
	if ( arguments.length == 2 )
	{
		actual   = expected;
		expected = msg;
		msg      = null;
	}
	
	if ( expected != actual )
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Expected:<" + expected + ">, but was:<" + actual + ">";
		this.fail( m, new CallStack() );
	}
};

/**
 * Asserts that a condition is false.
 *
 * @param String msg An optional error message.
 * @param String cond The condition to evaluate.
 * @exception AssertionFailedError Thrown if the evaluation was not false.
 * @access public
 */
Assert.prototype.assertFalse = function( msg, cond )
{
	if ( arguments.length == 1 )
	{
		cond = msg;
		msg = null;
	}
	
	if ( eval( cond ) )
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Condition should have failed \"" + cond + "\"";
		this.fail( m, new CallStack() );
	}
};

/**
 * Asserts that an object is not null.
 *
 * @param String msg An optional error message.
 * @param Object object The valid object.
 * @exception AssertionFailedError Thrown if the object is not null.
 * @access public
 */
Assert.prototype.assertNotNull = function( msg, object )
{
	if ( arguments.length == 1 )
	{
		object = msg;
		msg = null;
	}
	
	if ( object === null )
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Object was null.";
		this.fail( m, new CallStack());
	}
};

/**
 * Asserts that an object is not undefined.
 *
 * @param String msg An optional error message.
 * @param Object object The defined object.
 * @exception AssertionFailedError Thrown if the object is undefined.
 * @access public
 */
Assert.prototype.assertNotUndefined = function( msg, object )
{
	if ( arguments.length == 1 )
	{
		object = msg;
		msg    = null;
	}
	
	if ( object === undefined )
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Object <" + object + "> was undefined.";
		this.fail( m, new CallStack() );
	}
};

/**
 * Asserts that an object is null.
 *
 * @param String msg An optional error message.
 * @param Object object The null object.
 * @exception AssertionFailedError Thrown if the object is not null.
 * @access public
 */
Assert.prototype.assertNull = function( msg, object )
{
	if ( arguments.length == 1 )
	{
		object = msg;
		msg    = null;
	}
	
	if ( object !== null )
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Object <" + object + "> was not null.";
		this.fail( m, new CallStack() );
	}
};

/**
 * Asserts that two values are the same.
 *
 * @param String msg An optional error message.
 * @param Object expected The expected value.
 * @param Object actual The actual value.
 * @exception AssertionFailedError Thrown if the expected value is not the actual one.
 * @access public
 */
Assert.prototype.assertSame = function( msg, expected, actual )
{
	if ( arguments.length == 2 )
	{
		actual   = expected;
		expected = msg;
		msg      = null;
	}
	
	if ( expected === actual )
	{
		return;
	}
	else
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Same expected:<" + expected + ">, but was:<" + actual + ">";
		this.fail( m, new CallStack() );
	}
};

/**
 * Asserts that a condition is true.
 *
 * @param String msg An optional error message.
 * @param String cond The condition to evaluate.
 * @exception AssertionFailedError Thrown if the evaluation was not true.
 * @access public
 */
Assert.prototype.assertTrue = function( msg, cond )
{
	if ( arguments.length == 1 )
	{
		cond = msg;
		msg  = null;
	}
	
	if ( !eval( cond ) )
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Condition failed \"" + cond + "\"";
		this.fail( m, new CallStack() );
	}
};

/**
 * Asserts that an object is undefined.
 *
 * @param String msg An optional error message.
 * @param Object object The undefined object.
 * @exception AssertionFailedError Thrown if the object is not undefined.
 * @access public
 */
Assert.prototype.assertUndefined = function( msg, object )
{
	if ( arguments.length == 1 )
	{
		object = msg;
		msg    = null;
	}
	
	if ( object !== undefined )
	{
		var m = ( msg? ( msg + " " ) : "" ) + "Object <" + object + "> was not undefined.";
		this.fail( m, new CallStack());
	}
};

/**
 * Fails a test with a give message.
 *
 * @param String msg The error message.
 * @param CallStack stack The call stack of the error.
 * @exception AssertionFailedError Is always thrown.
 * @access public
 */
Assert.prototype.fail = function( msg, stack )
{
	var afe = new AssertionFailedError( msg, stack );
	throw afe;
};
