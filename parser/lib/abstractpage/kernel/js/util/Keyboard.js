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
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Keyboard Class
 *
 * Mapping of the keyboard to JavaScript works completely correctly only with 
 * Microsoft Internet Explorer version 5.5 or better. Earlier versions of 
 * Internet Explorer give partial functionality.
 *
 * @package util
 */
 
/**
 * Constructor
 *
 * @access public
 */
Keyboard = function()
{
	this.Base = Base;
	this.Base();
};


Keyboard.prototype = new Base();
Keyboard.prototype.constructor = Keyboard;
Keyboard.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Keyboard.callbackFn = new Function;

/**
 * @access public
 * @static
 */
Keyboard.map = [
	{ keyCode:0,   isMapped:false },
	/*	
	{ keyCode:49,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F1"         }, // Alt/1
	{ keyCode:50,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F2"         }, // Alt/2
	{ keyCode:51,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F3"         }, // Alt/3
	{ keyCode:52,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F4"         }, // Alt/4
	{ keyCode:53,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F5"         }, // Alt/5
	{ keyCode:54,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F6"         }, // Alt/6
	{ keyCode:55,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F7"         }, // Alt/7
	{ keyCode:56,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F8"         }, // Alt/8
	{ keyCode:57,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F9"         }, // Alt/9
	{ keyCode:48,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F10"        }, // Alt/0
	{ keyCode:187, isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F11"        }, // Alt/+
	{ keyCode:219, isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:F12"        }, // Alt/\
	{ keyCode:49,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F13"        }, // Shift/Alt/1
	{ keyCode:50,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F14"        }, // Shift/Alt/2
	{ keyCode:51,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F15"        }, // Shift/Alt/3
	{ keyCode:52,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F16"        }, // Shift/Alt/4
	{ keyCode:53,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F17"        }, // Shift/Alt/5
	{ keyCode:54,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F18"        }, // Shift/Alt/6
	{ keyCode:55,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F19"        }, // Shift/Alt/7
	{ keyCode:56,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F20"        }, // Shift/Alt/8
	{ keyCode:57,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F21"        }, // Shift/Alt/9
	{ keyCode:48,  isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F22"        }, // Shift/Alt/0
	{ keyCode:187, isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F23"        }, // Shift/Alt/+
	{ keyCode:219, isMapped:true,  shift:true,  ctrl:false, alt:true,  vKey:"command:F24"        }, // Shift/Alt/\
	{ keyCode:49,  isMapped:true,  shift:false, ctrl:true,  alt:false, vKey:"command:PA1"        }, // Ctrl/1 (3270)
	{ keyCode:50,  isMapped:true,  shift:false, ctrl:true,  alt:false, vKey:"command:PA2"        }, // Ctrl/2 (3270)
	{ keyCode:51,  isMapped:true,  shift:false, ctrl:true,  alt:false, vKey:"command:PA3"        }, // Ctrl/3 (3270)
	{ keyCode:67,  isMapped:true,  shift:false, ctrl:true,  alt:false, vKey:"command:Break"      }, // Ctrl/C
	{ keyCode:68,  isMapped:true,  shift:false, ctrl:true,  alt:false, vKey:"command:Disconnect" }, // Ctrl/D
	{ keyCode:82,  isMapped:true,  shift:false, ctrl:true,  alt:false, vKey:"command:Refresh"    }, // Ctrl/R
	{ keyCode:33,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:RollDown"   }, // Alt/PageUp (5250)
	{ keyCode:34,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"command:RollUp"     }, // Alt/PageDown (5250)
	{ keyCode:72,  isMapped:true,  shift:false, ctrl:true,  alt:true,  vKey:"command:Help"       }, // Ctrl/Alt/Home (5250)
	{ keyCode:35,  isMapped:true,  shift:false, ctrl:true,  alt:false, vKey:"GoToBottom"         }, // Ctrl/End
	{ keyCode:46,  isMapped:true,  shift:true,  ctrl:false, alt:false, vKey:"EraseField"         }, // Shift/Del
	{ keyCode:46,  isMapped:true,  shift:false, ctrl:true,  alt:false, vKey:"EraseEOP"           }, // Ctrl/Del
	{ keyCode:46,  isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"ClearScreen"        }, // Alt/Del
	{ keyCode:9,   isMapped:true,  shift:true,  ctrl:false, alt:false, vKey:"BackTab"            }, // Shift/Tab in first field
	{ keyCode:36,  isMapped:true,  shift:false, ctrl:false, alt:false, vKey:"CursorHome"         }, // Home
	{ keyCode:38,  isMapped:true,  shift:false, ctrl:false, alt:false, vKey:"CursorUp"           }, // Cursor Up
	{ keyCode:40,  isMapped:true,  shift:false, ctrl:false, alt:false, vKey:"CursorDown"         }, // Cursor Down
	{ keyCode:84,  isMapped:false, shift:false, ctrl:true,  alt:false, vKey:"macro:@E"           }, // Ctrl/T: transmit key (example only)
	*/
	
	// Abstractpage keyboard events
	{ keyCode:70,  isMapped:true,  shift:false, ctrl:true,  alt:true,  vKey:"alt:ctrl:f"		 }, // ALT+CTRL+F: Find
	{ keyCode:80,  isMapped:true,  shift:false, ctrl:true,  alt:true,  vKey:"alt:ctrl:p"		 }, // ALT+CTRL+P: Print
	{ keyCode:81,  isMapped:true,  shift:false, ctrl:true,  alt:true,  vKey:"alt:ctrl:q"		 }, // ALT+CTRL+Q: Quit
	{ keyCode:82,  isMapped:true,  shift:false, ctrl:true,  alt:true,  vKey:"alt:ctrl:r"		 }, // ALT+CTRL+R: Replace
	
	{ keyCode:112, isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"alt:F1"      	 	 }, // ALT+F1: Help
	{ keyCode:117, isMapped:true,  shift:false, ctrl:false, alt:true,  vKey:"alt:F6"      	 	 }  // ALT+F6: Resource Tab
];

/**
 * @access public
 * @static
 */
Keyboard.findKeyMap = function( keycode, shift, ctrl, alt )
{
	for ( var i = 0; i < Keyboard.map.length; i++ )
	{
		if ( ( Keyboard.map[i].keyCode  == keycode ) &&
			 ( Keyboard.map[i].shift    == shift   ) &&
			 ( Keyboard.map[i].ctrl     == ctrl    ) &&
			 ( Keyboard.map[i].alt      == alt     ) )
		{
			if ( Keyboard.map[i].isMapped )
				return Keyboard.map[i].vKey;
		}
	}

	return false;
};

/**
 * @access public
 * @static
 */
Keyboard.keyPressed = function()
{
	var myKeyCode      = event.keyCode;
	var mySrcElement   = event.srcElement;
	var isShiftPressed = event.shiftKey;
	var isCtrlPressed  = event.ctrlKey;
	var isAltPressed   = event.altKey;

	// Enter(13), Shift(16), Ctrl(17), Alt(18), CapsLock(20) keys?
	if ( myKeyCode >= 13 && myKeyCode <= 20 )
		return true;

	/*
	alert( "Key code=" + myKeyCode +
		"; Shift="          + isShiftPressed +
		"; Ctrl="           + isCtrlPressed  +
		"; Alt="            + isAltPressed   +
		"\nThis key is "    + ( Keyboard.findKeyMap( myKeyCode, isShiftPressed, isCtrlPressed, isAltPressed ) || "not mapped" )
	);
	*/
	
	Keyboard.callbackFn( Keyboard.findKeyMap( myKeyCode, isShiftPressed, isCtrlPressed, isAltPressed ) );

	// cancel event and bubbling
	window.event.returnValue  = false;
	window.event.cancelBubble = true;
	
	return false;
};

/**
 * @access public
 * @static
 */
Keyboard.setCallback = function( fn )
{
	if ( fn && typeof( fn ) == "function" )
		Keyboard.callbackFn = fn;
};

