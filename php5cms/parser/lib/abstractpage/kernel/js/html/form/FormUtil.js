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
 * @package html_form
 */
 
/**
 * Constructor
 *
 * @access public
 */
FormUtil = function() 
{
	this.Base = Base;
	this.Base();
};


FormUtil.prototype = new Base();
FormUtil.prototype.constructor = FormUtil;
FormUtil.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
FormUtil.toggleCheckbox = function( formName, fieldName ) 
{
	if ( document.forms[formName].elements[fieldName].checked ) 
		document.forms[formName].elements[fieldName].checked = false;
	else 
		document.forms[formName].elements[fieldName].checked = true;
};

/**
 * @access public
 * @static
 */
FormUtil.toggleContainer = function( containerName ) 
{
	if ( document.all[containerName].style.display == "none" ) 
		document.all[containerName].style.display = "";
	else 
		document.all[containerName].style.display = "none";
};

/**
 * @access public
 * @static
 */
FormUtil.checkMail = function( url, fieldObj, checkType ) 
{
	var fieldName = fieldObj.name;
	var fieldID   = fieldObj.id;
	var email     = fieldObj.value;
	var iFrameObj = document.getElementById( 'mailCheck' + fieldName );
	
	url += "?email=" + email + "&checkType=" + checkType;
	var time = new Date();
	url += "&random=" + time.getMilliseconds();
	iFrameObj.src = url;
};

/**
 * @access public
 * @static
 */
FormUtil.jumpToFirstError = function( fieldName, formName, doSelect ) 
{
	if ( document.forms[formName].elements[fieldName] ) 
	{
		if ( doSelect && (document.forms[formName].elements[fieldName].value != '' ) ) 
		{
			if ( document.forms[formName].elements[fieldName].select ) 
				document.forms[formName].elements[fieldName].select();
		}
		
		if ( document.forms[formName].elements[fieldName].focus ) 
			document.forms[formName].elements[fieldName].focus();
	}
};

/**
 * @access public
 * @static
 */
FormUtil.enterSubmit = function( e, myForm ) 
{
	if ( window.event && window.event.keyCode == 13 ) 
		myForm.submit();
	else if ( e && e.which == 13 ) 
		myForm.submit();
	else 
		return true;
};

/**
 * @access public
 * @static
 */
FormUtil.noEnter = function( e ) 
{
	if ( window.event ) 
		return !( window.event && window.event.keyCode == 13 );
	else if ( e ) 
		return !( e.which == 13 );

	return true;
};

/**
 * @access public
 * @static
 */
FormUtil.enterToTab = function( e ) 
{
	if ( window.event && window.event.keyCode == 13 ) 
		window.event.keyCode = 9;
	else if ( e && e.which == 13 ) 
		e.keyCode = 9;

	return true;
};

/**
 * @access public
 * @static
 */
FormUtil.fieldSetFocusAndSelect = function( field, force ) 
{
	if ( typeof( field ) == 'string' ) 
		field = document.getElementById( field );

	if ( !field ) 
		return false;
		
	try 
	{
		if ( force || !field.hasFocus ) 
		{
			field.focus();
			field.select();
		}
	} 
	catch ( e ) 
	{
		return false;
	}

	return true;
};

/**
 * @access public
 * @static
 */
FormUtil.doHiddenSubmit = function( exitScreen, exitAction, nextScreen, nextAction, dataHash, submitToAction ) 
{
	var formOutArray =  new Array();
	var ii = 0;
	
	formOutArray[ii++] = '<form name="smSubmitForm" action="' + submitToAction + '" method="post">';
	formOutArray[ii++] = '<input type="hidden" name="todo[nextScreen]" value="' + nextScreen + '">';
	formOutArray[ii++] = '<input type="hidden" name="todo[exitScreen]" value="' + exitScreen + '">';
	
	switch ( typeof( nextAction ) ) 
	{
		case 'string':
			formOutArray[ii++] = '<input type="hidden" name="todo[nextAction]" value="' + nextAction + '">';
			break;
		
		case 'object':
			for ( var key in nextAction ) 
				formOutArray[ii++] = '<input type="hidden" name="todo[nextAction][' + key + ']" value="' + nextAction[key] + '">';
				
			break;

		default:
	}

	switch ( typeof( exitAction ) ) 
	{
		case 'string':
			formOutArray[ii++] = '<input type="hidden" name="todo[exitAction]" value="' + exitAction + '">';
			break;
		
		case 'object':
			for ( var key in exitAction ) 
				formOutArray[ii++] = '<input type="hidden" name="todo[exitAction][' + key + ']" value="' + exitAction[key] + '">';
				
			break;

		default:
	}

	dataHash = FormUtil._recursiveObj2Hash( dataHash );
	
	for ( var matrixStr in dataHash ) 
		formOutArray[ii++] = '<input type="hidden" name="' + "todo[dataHash]" + matrixStr + '" value="' + dataHash[matrixStr] +  '">';

	formOutArray[ii++] = '</form>';
	var body = document.getElementsByTagName( 'body' ).item( 0 );
	body.innerHTML = formOutArray.join( '' );
	var form = document.smSubmitForm;
	form.submit();
};


// private methods

/**
 * @access private
 * @static
 */
FormUtil._recursiveObj2Hash = function( aObject, matrixStr, flatObjHash ) 
{
	if ( !flatObjHash ) 
	{
		flatObjHash = new Object();
		matrixStr   = '';
	}

	if ( typeof( aObject ) != 'object' ) 
	{
		flatObjHash[matrixStr] = aObject;
	} 
	else 
	{
		for ( var key in aObject ) 
		{
			var newMatrixStr = matrixStr + '[' + key + ']';
			FormUtil._recursiveObj2Hash( aObject[key], newMatrixStr, flatObjHash );
		}
	}

	return flatObjHash;
};
