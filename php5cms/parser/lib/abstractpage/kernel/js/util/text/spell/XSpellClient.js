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
 * @package util_text_spell
 */
 
/**
 * Constructor
 *
 * @access public
 */
XSpellClient = function()
{
	this.Base = Base;
	this.Base();
};


XSpellClient.prototype = new Base();
XSpellClient.prototype.constructor = XSpellClient;
XSpellClient.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
XSpellClient.jsdata = new Array();

/**
 * @access public
 * @static
 */
XSpellClient.callSpellerUpdate = new Function; // Callback stuff

/**
 * @access public
 * @static
 */
XSpellClient.outputDiv = "output";

/**
 * @access public
 * @static
 */
XSpellClient.init = function()
{
	// Netscape 6 and IE5
	if ( document.implementation && document.implementation.createDocument )
	{
		XSpellClient.xmlDoc = document.implementation.createDocument( "", "test", null );
		XSpellClient.xmlDoc.addEventListener( "load", XSpellClient.documentLoaded, false );
	}
	else if ( document.all && document.getElementById )
	{
		XSpellClient.xmlDoc = new ActiveXObject( 'Microsoft.XMLDOM' ); // TODO: derive from XMLParser Object
		XSpellClient.xmlDoc.async = true;
		XSpellClient.xmlDoc.onreadystatechange = XSpellClient.loadNextDoc;
	}
};

/**
 * Parser for NS.
 *
 * @access public
 * @static
 */
XSpellClient.documentLoaded = function( e )
{
	dvo = "";
	XSpellClient.jsdata = new Array();

	for ( sc = 0; sc < XSpellClient.xmlDoc.getElementsByTagName( 'struct' ).length; sc++ )
	{
		s = XSpellClient.xmlDoc.getElementsByTagName( 'struct' ).item( sc );

		errLocation = -1;
		errSuggest  = -1;
		errWord     = -1;
		errLen      = -1; // length of misspelled word
		
		for ( mc = 0; mc < s.getElementsByTagName( 'member' ).length; mc++ )
		{
			m = s.getElementsByTagName( 'member' ).item( mc );
			n = m.getElementsByTagName( 'name' ).item( 0 ).firstChild.nodeValue; // get name    		 
        
			switch ( n )
			{
				case "suggestions":
					DOMerrSuggest = m.getElementsByTagName( 'string' );
					errSuggest = new Array();
					
					for ( es = 0; es < DOMerrSuggest.length; es++ )
						errSuggest[es] = DOMerrSuggest.item( es ).firstChild.nodeValue;
					
					break;

				case "location":
					errLocation = m.getElementsByTagName( 'i4' ).item( 0 ).firstChild.nodeValue; 
					break;

				case "length": // This is an optional extension added by GED July 20, 2001
					errLen = m.getElementsByTagName( 'i4' ).item( 0 ).firstChild.nodeValue			   
					break;
			
				case "word":
					errWord = m.getElementsByTagName( 'string' ).item( 0 ).firstChild.nodeValue;
					break;
				
				default:
					return Base.raiseError( "[" + n + "] is not a recognised response member." );
			}	    
		}
		
		// We must build js arrays based on location of spelling error
		// as it is the only piece of data that is for sure unique.

		if ( errLocation != -1 && errSuggest != -1 && errWord != -1 )
		{
			// if we set all of those then we are ready to build a JS array entry...
			errLocation = errLocation + "";
			XSpellClient.jsdata[errLocation] = new XSpellMistakeWord( errLocation, errWord, errSuggest, errLen );  
		}	 
	}

	XSpellClient.callSpellerUpdate();
	return;
};

/**
 * Parser for IE.
 *
 * @access public
 * @static
 */
XSpellClient.loadNextDoc = function( e )
{
	if ( document.all && XSpellClient.xmlDoc.readyState != 4 )
		return; 

	d = XSpellClient.xmlDoc.documentElement;

	while ( d.nodeName != "struct" ) 
	{
		if ( !d.firstChild )
		{ 		
			XSpellClient.callSpellerUpdate();
			return; 
		}  
		
		d = d.firstChild;
	}
	
	d = d.parentNode.parentNode;
	
	for ( sc = 0; sc < d.childNodes.length; sc++ )
	{
		s = d.childNodes[sc].firstChild;

		errLocation = -1;
		errSuggest  = -1;
		errWord     = -1;
		errLen      = -1; // length of misspelled word

		for ( mc = 0; mc < s.childNodes.length; mc++ )
		{ 
			m = s.childNodes[mc].firstChild;
			n = m.firstChild.nodeValue; // get name    		 

			switch ( n )
			{
				case "suggestions":
					DOMerrSuggest = m.nextSibling.firstChild.firstChild;
					errSuggest = new Array();
					es = -1;
					
					if ( DOMerrSuggest.firstChild )
					{
						DOMerrSuggest = DOMerrSuggest.firstChild;
						
						while ( DOMerrSuggest.nextSibling )
						{
							es++;
							errSuggest[es] = DOMerrSuggest.firstChild.firstChild.nodeValue;
							DOMerrSuggest = DOMerrSuggest.nextSibling;
						}
					}
					
					break;

				case "location":
					errLocation = m.nextSibling.firstChild.firstChild.nodeValue;
					break;
      
				case "length": // This is an optional extension added by GED July 20, 2001
					errLen = m.nextSibling.firstChild.firstChild.nodeValue;
					break;
				
				case "word":
					errWord = m.nextSibling.firstChild.firstChild.nodeValue;
					break;			

      			default:
					return Base.raiseError( "[" + n + "] is not a recognised response member" );
			}					
		} 

		if ( errLocation != -1 && errSuggest != -1 && errWord != -1 )
		{
			// if we set all of those then we are ready to build a JS array entry...
			errLocation = errLocation + "";
			XSpellClient.jsdata[errLocation] = new XSpellMistakeWord( errLocation, errWord, errSuggest, errLen );  
		}	 
	}
	
	XSpellClient.callSpellerUpdate();
	return;
};

/**
 * @access public
 * @static
 */
XSpellClient.check = function()
{
	dvo = "";

	for ( a in XSpellClient.jsdata )
	{
		dvo = dvo +
			"<hr>Word: <b>"  + XSpellClient.jsdata[a].word + "</b>" +
			"<br>Location: " + XSpellClient.jsdata[a].location +
			"<br>Length: "   + XSpellClient.jsdata[a].len +
			"<br>Suggestions: ";
							
		for ( i in XSpellClient.jsdata[a].suggestions )
			dvo = dvo + "<br>" + XSpellClient.jsdata[a].suggestions[i];
	}

	document.getElementById( XSpellClient.outputDiv ).innerHTML = dvo;
	return;
};

/**
 * @access public
 * @static
 */
XSpellClient.loadDocument = function( url )
{
	XSpellClient.xmlDoc.load( url );
};
