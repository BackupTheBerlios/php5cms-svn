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
 * ClientCapabilities Class
 * Utilizes IE default behaviour
 *
 * Note: navigator_isOnline property checks browser mode, not if there
 * is a connection established
 *
 * @package util_agent
 */
 
/**
 * Constructor
 *
 * @access public
 */
ClientCapabilities = function()
{
	this.Dictionary = Dictionary;
	this.Dictionary();
	
	document.body.insertAdjacentHTML(
		'beforeEnd',
		'<ie:clientcaps id="oClientCaps" style="behavior:url(#default#clientCaps)" />'
	);
	
	this.evaluate();
};


ClientCapabilities.prototype = new Dictionary();
ClientCapabilities.prototype.constructor = ClientCapabilities;
ClientCapabilities.superclass = Base.prototype;

/**
 * @access public
 */
ClientCapabilities.prototype.evaluate = function()
{
	// capabilities taken from navigator object
	this.add( "NAVIGATOR_ISONLINE",        navigator["onLine"]          );
	this.add( "NAVIGATOR_USERLANGUAGE",    navigator["userLanguage"]    );
	this.add( "NAVIGATOR_SYSTEMLANGUAGE",  navigator["systemLanguage"]  );
	this.add( "NAVIGATOR_PLATFORM",        navigator["platform"]        );
	this.add( "NAVIGATOR_CPUCLASS",        navigator["cpuClass"]        );
	this.add( "NAVIGATOR_USERAGENT",       navigator["userAgent"]       );
	this.add( "NAVIGATOR_APPVERSION",      navigator["appVersion"]      );
	this.add( "NAVIGATOR_APPCODENAME",     navigator["appCodeName"]     );
	this.add( "NAVIGATOR_APPNAME",         navigator["appName"]         );
	this.add( "NAVIGATOR_APPMINORVERSION", navigator["appMinorVersion"] );
	
	// capabilities taken from screen object
	this.add( "SCREEN_AVAILHEIGHT",        screen["availHeight"]        );
	this.add( "SCREEN_AVAILWIDTH",         screen["availWidth"]         );
	this.add( "SCREEN_BUFFERDEPTH",        screen["bufferDepth"]        );
	this.add( "SCREEN_COLORDEPTH",         screen["colorDepth"]         );
	this.add( "SCREEN_HEIGHT",             screen["height"]             );
	this.add( "SCREEN_WIDTH",              screen["width"]              );
	this.add( "SCREEN_UPDATEINTERVAL",     screen["updateInterval"]     );
	
	// capabilities taken from clientCap behaviour	
	this.add( "CLIENTCAPS_AVAILHEIGHT",    oClientCaps.availHeight      );
	this.add( "CLIENTCAPS_AVAILWIDTH",     oClientCaps.availWidth       );
	this.add( "CLIENTCAPS_BUFFERDEPTH",    oClientCaps.bufferDepth      );
	this.add( "CLIENTCAPS_COLORDEPTH",     oClientCaps.colorDepth       );
	this.add( "CLIENTCAPS_CONNECTIONTYPE", oClientCaps.connectionType   );
	this.add( "CLIENTCAPS_COOKIEENABLED",  oClientCaps.cookieEnabled    );
	this.add( "CLIENTCAPS_CPUCLASS",       oClientCaps.cpuClass         );
	this.add( "CLIENTCAPS_HEIGHT",         oClientCaps.height           );
	this.add( "CLIENTCAPS_WIDTH",          oClientCaps.width            );
	this.add( "CLIENTCAPS_JAVAENABLED",    oClientCaps.javaEnabled      );
	this.add( "CLIENTCAPS_PLATFORM",       oClientCaps.platform         );
	this.add( "CLIENTCAPS_SYSTEMLANGUAGE", oClientCaps.systemLanguage   );
	this.add( "CLIENTCAPS_USERLANGUAGE",   oClientCaps.userLanguage     );
	
	// document.implementation
	var dI = document.implementation;
	
	if ( typeof dI != "undefined" )
	{
		this.add( "DOCUMENT.IMPL_DOM1_XML",						dI.hasFeature( "XML", "1.0" )            );
		this.add( "DOCUMENT.IMPL_DOM1_HTML",					dI.hasFeature( "HTML", "1.0" )           );
		this.add( "DOCUMENT.IMPL_DOM2_CORE",					dI.hasFeature( "Core", "2.0" )           );
		this.add( "DOCUMENT.IMPL_DOM2_XML",						dI.hasFeature( "XML", "2.0" )            );
		this.add( "DOCUMENT.IMPL_DOM2_HTML",					dI.hasFeature( "HTML", "2.0" )           );
		this.add( "DOCUMENT.IMPL_DOM2_VIEW",					dI.hasFeature( "Views", "2.0" )          );
		this.add( "DOCUMENT.IMPL_DOM2_STYLESHEET",				dI.hasFeature( "StyleSheets", "2.0" )    );
		this.add( "DOCUMENT.IMPL_DOM2_CSS",						dI.hasFeature( "CSS", "2.0" )            );
		this.add( "DOCUMENT.IMPL_DOM2_EVENT",					dI.hasFeature( "Events", "2.0" )         );
		this.add( "DOCUMENT.IMPL_DOM2_USER_INTERFACE_EVENT",	dI.hasFeature( "UIEvents", "2.0" )       );
		this.add( "DOCUMENT.IMPL_DOM2_MOUSE_EVENT",				dI.hasFeature( "MouseEvents", "2.0" )    );
		this.add( "DOCUMENT.IMPL_DOM2_MUTATION_EVENT",			dI.hasFeature( "MutationEvents", "2.0" ) );
		this.add( "DOCUMENT.IMPL_DOM2_HTML_EVENT",				dI.hasFeature( "HTMLEvents", "2.0" )     );
		this.add( "DOCUMENT.IMPL_DOM2_TRAVERSAL",				dI.hasFeature( "Traversal", "2.0" )      );
		this.add( "DOCUMENT.IMPL_DOM2_RANGE",					dI.hasFeature( "Range", "2.0" )          );
	}
		
	// various checks
	this.add( "SYSTEM_FILEENABLED", this.checkFileSystemObject() );
	
	// component checks
	this.add( "COMPONENT_ADDRESSBOOK",							this.isComponentInstalled( '{7790769C-0471-11D2-AF11-00C04FA35D02}' ) );
	this.add( "COMPONENT_WINDOWSDESKTOPUPDATENT",				this.isComponentInstalled( '{89820200-ECBD-11CF-8B85-00AA005B4340}' ) );
	this.add( "COMPONENT_DIRECTANIMATION",						this.isComponentInstalled( '{283807B5-2C60-11D0-A31D-00AA00B92C03}' ) );
	this.add( "COMPONENT_DIRECTANIMATIONJAVACLASSES",			this.isComponentInstalled( '{4F216970-C90C-11D1-B5C7-0000F8051515}' ) );
	this.add( "COMPONENT_DIRECTSHOW",							this.isComponentInstalled( '{44BBA848-CC51-11CF-AAFA-00AA00B6015C}' ) );
	this.add( "COMPONENT_DYNAMICHTMLDATABINDING",				this.isComponentInstalled( '{9381D8F2-0288-11D0-9501-00AA00B911A5}' ) );
	this.add( "COMPONENT_DYNAMICHTMLDATABINDINGFORJAVA",		this.isComponentInstalled( '{4F216970-C90C-11D1-B5C7-0000F8051515}' ) );
	this.add( "COMPONENT_INTERNETCONNECTIONWIZARD",				this.isComponentInstalled( '{5A8D6EE0-3E18-11D0-821E-444553540000}' ) );
	this.add( "COMPONENT_IE5WEBBROWSER",						this.isComponentInstalled( '{89820200-ECBD-11CF-8B85-00AA005B4383}' ) );
	this.add( "COMPONENT_IEBROWSINGENHANCEMENTS",				this.isComponentInstalled( '{630B1DA0-B465-11D1-9948-00C04F98BBC9}' ) );
	this.add( "COMPONENT_IECLASSESFORJAVA",						this.isComponentInstalled( '{08B0E5C0-4FCB-11CF-AAA5-00401C608555}' ) );
	this.add( "COMPONENT_IEHELP",								this.isComponentInstalled( '{45EA75A0-A269-11D1-B5BF-0000F8051515}' ) );
	this.add( "COMPONENT_IEHELPENGINE",							this.isComponentInstalled( '{DE5AED00-A4BF-11D1-9948-00C04F98BBC9}' ) );
	this.add( "COMPONENT_MACROMEDIAFLASH",						this.isComponentInstalled( '{D27CDB6E-AE6D-11CF-96B8-444553540000}' ) );
	this.add( "COMPONENT_MACROMEDIASHOCKWAVEDIRECTOR",			this.isComponentInstalled( '{2A202491-F00D-11CF-87CC-0020AFEECF20}' ) );
	this.add( "COMPONENT_WINDOWSMEDIAPLAYER64",					this.isComponentInstalled( '{22D6F312-B0F6-11D0-94AB-0080C74C7E95}' ) );
	this.add( "COMPONENT_WINDOWSMEDIAPLAYER7",					this.isComponentInstalled( '{6BF52A52-394A-11D3-B153-00C04F79FAA6}' ) );
	this.add( "COMPONENT_REALPLAYER",							this.isComponentInstalled( '{CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA}' ) );
	this.add( "COMPONENT_NETMEETINGNT",							this.isComponentInstalled( '{44BBA842-CC51-11CF-AAFA-00AA00B6015B}' ) );
	this.add( "COMPONENT_OFFLINEBROWSINGPACK",					this.isComponentInstalled( '{3AF36230-A269-11D1-B5BF-0000F8051515}' ) );
	this.add( "COMPONENT_OUTLOOKEXPRESS",						this.isComponentInstalled( '{44BBA840-CC51-11CF-AAFA-00AA00B6015C}' ) );
	this.add( "COMPONENT_TASKSCHEDULER",						this.isComponentInstalled( '{CC2A9BA0-3BDD-11D0-821E-444553540000}' ) );
	this.add( "COMPONENT_UNISCRIBE",							this.isComponentInstalled( '{3BF42070-B3B1-11D1-B5C5-0000F8051515}' ) );
	this.add( "COMPONENT_VECTORGRAPHICSRENDERING",				this.isComponentInstalled( '{10072CEC-8CC1-11D1-986E-00A0C955B42F}' ) );
	this.add( "COMPONENT_VISUALBASICSCRIPTINGSUPPORT",			this.isComponentInstalled( '{4F645220-306D-11D2-995D-00C04F98BBC9}' ) );
	this.add( "COMPONENT_MICROSOFTVIRTUALMACHINE",				this.isComponentInstalled( '{08B0E5C0-4FCB-11CF-AAA5-00401C608500}' ) );
	this.add( "COMPONENT_WEBFOLDERS",							this.isComponentInstalled( '{73FA19D0-2D75-11D2-995D-00C04F98BBC9}' ) );
	
	// xml capabilities	
	this.add( "XML_MICROSOFT.XMLDOMENABLED",                    this.checkParser( "Microsoft.XMLDOM" )                    );
	this.add( "XML_MICROSOFT.XMLHTTPENABLED",                   this.checkParser( "Microsoft.XMLHTTP" )                   );
	
	this.add( "XML_MSXML2.DOMDOCUMENTENABLED",                  this.checkParser( "MSXML2.DOMDocument" )                  );
	this.add( "XML_MSXML2.DOMDOCUMENT.3.0ENABLED",              this.checkParser( "MSXML2.DOMDocument.3.0" )              );
	this.add( "XML_MSXML2.FREETHREADEDDOMDOCUMENTENABLED",      this.checkParser( "MSXML2.FreeThreadedDOMDocument" )      );
	this.add( "XML_MSXML2.FREETHREADEDDOMDOCUMENT.3.0ENABLED",	this.checkParser( "MSXML2.FreeThreadedDOMDocument.3.0" )  );
	this.add( "XML_MSXML2.DSOCONTROLENABLED",                   this.checkParser( "MSXML2.DSOControl" )                   );
	this.add( "XML_MSXML2.DSOCONTROL.3.0ENABLED",               this.checkParser( "MSXML2.DSOControl.3.0" )               );
	this.add( "XML_MSXML2.XMLHTTPENABLED",                      this.checkParser( "MSXML2.XMLHTTP" )                      );
	this.add( "XML_MSXML2.XMLHTTP.3.0ENABLED",                  this.checkParser( "MSXML2.XMLHTTP.3.0" )                  );
	this.add( "XML_MSXML2.XMLSCHEMACACHEENABLED",               this.checkParser( "MSXML2.XMLSchemaCache" )               );
	this.add( "XML_MSXML2.XMLSCHEMACACHE.3.0ENABLED",           this.checkParser( "MSXML2.XMLSchemaCache.3.0" )           );
	this.add( "XML_MSXML2.XSLTEMPLATEENABLED",                  this.checkParser( "MSXML2.XSLTemplate" )                  );
	this.add( "XML_MSXML2.XSLTEMPLATE.3.0ENABLED",              this.checkParser( "MSXML2.XSLTemplate.3.0" )              );

	// plugins
	try
	{
		this.add( "PLUGIN_FLASH3ENABLED",      activeXDetect( "ShockwaveFlash.ShockwaveFlash.3" )                    );
		this.add( "PLUGIN_FLASH4ENABLED",      activeXDetect( "ShockwaveFlash.ShockwaveFlash.4" )                    );
		this.add( "PLUGIN_FLASH5ENABLED",      activeXDetect( "ShockwaveFlash.ShockwaveFlash.5" )                    );
		this.add( "PLUGIN_SHOCKWAVEENABLED",   activeXDetect( "SWCtl.SWCtl.1" )                                      );
		this.add( "PLUGIN_REALPLAYERENABLED",  activeXDetect( "RealPlayer.RealPlayer(tm) ActiveX Control (32-bit)" ) );
		this.add( "PLUGIN_REALG2ENABLED",      activeXDetect( "rmocx.RealPlayer G2 Control" )                        );
		this.add( "PLUGIN_REALVIDEOENABLED",   activeXDetect( "RealVideo.RealVideo(tm) ActiveX Control (32-bit)" )   );
		this.add( "PLUGIN_QUICKTIMEENABLED",   activeXDetect( "QuickTimeCheckObject.QuickTimeCheck.1" )              );
		this.add( "PLUGIN_MEDIAPLAYERENABLED", activeXDetect( "MediaPlayer.MediaPlayer.1" )                          );
		this.add( "PLUGIN_ACROBAT5ENABLED",    activeXDetect( "PDF.PdfCtrl.5" )                                      ); // version 5?
		this.add( "PLUGIN_ADOBESVGENABLED",    activeXDetect( "Adobe.SVGCtl" )                                       );
	}
	catch( e )
	{
		return Base.raiseError( "ActiveXDetect function not included." );
	}
};

/**
 * @access public
 */
ClientCapabilities.prototype.checkParser = function( progid )
{
	var xml = true;
	
	try
	{
		var parser = new ActiveXObject( progid );
	}
	catch( e )
	{
		xml = false;
	}

	return xml;
};

/**
 * @access public
 */
ClientCapabilities.prototype.isComponentInstalled = function( uid )
{
	if ( uid == null )
		return false;
		
	return oClientCaps.isComponentInstalled( uid, "componentid" );
};

/**
 * @access public
 */
ClientCapabilities.prototype.checkFileSystemObject = function()
{
	if ( window.opener && ( window.opener.name == AP_FILEWINNAME ) )
		return true;
	else
		return false;
};

/**
 * @access public
 */
ClientCapabilities.prototype.forceDownload = function( component )
{
	var uid = null;
	
	switch( component )
	{
		case 'AddressBook':
			uid = '{7790769C-0471-11D2-AF11-00C04FA35D02}';
			break;
			
		case 'AOLARTImageFormatSupport':
			uid = '{47F67D00-9E55-11D1-BAEF-00C04FC2D130}';
			break;
			
		case 'ArabicTextDisplaySupport':
			uid = '{76C19B38-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'ChineseSimplifiedTextDisplaySupport':
			uid = '{76C19B34-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'ChineseTraditionalTextDisplaySupport':
			uid = '{76C19B33-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'DynamicHTMLDataBinding':
			uid = '{9381D8F2-0288-11D0-9501-00AA00B911A5}';
			break;
			
		case 'DirectAnimation':
			uid = '{283807B5-2C60-11D0-A31D-00AA00B92C03}';
			break;
			
		case 'HebrewTextDisplaySupport':
			uid = '{76C19B36-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'InternetConnectionWizard':
			uid = '{5A8D6EE0-3E18-11D0-821E-444553540000}';
			break;
			
		case 'InternetExplorerBrowsingEnhancements':
			uid = '{630B1DA0-B465-11D1-9948-00C04F98BBC9}';
			break;
			
		case 'InternetExplorerHelp':
			uid = '{45EA75A0-A269-11D1-B5BF-0000F8051515}';
			break;
			
		case 'JapaneseTextDisplaySupport':
			uid = '{76C19B30-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'KoreanTextDisplaySupport':
			uid = '{76C19B31-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'LanguageAutoSelection':
			uid = '{76C19B50-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'MacromediaFlash':
			uid = '{D27CDB6E-AE6D-11CF-96B8-444553540000}';
			break;
			
		case 'MacromediaShockwaveDirector':
			uid = '{2A202491-F00D-11CF-87CC-0020AFEECF20}';
			break;

		case 'WindowsMediaPlayer64':
			uid = '{22D6F312-B0F6-11D0-94AB-0080C74C7E95}';
			break;
		
		case 'WindowsMediaPlayer7':
			uid = '{6BF52A52-394A-11D3-B153-00C04F79FAA6}';
			break;
				
		case 'OfflineBrowsingPack':
			uid = '{3AF36230-A269-11D1-B5BF-0000F8051515}';
			break;
			
		case 'PanEuropeanTextDisplaySupport':
			uid = '{76C19B32-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'ThaiTextDisplaySupport':
			uid = '{76C19B35-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'Uniscribe':
			uid = '{3BF42070-B3B1-11D1-B5C5-0000F8051515}';
			break;
			
		case 'VectorGraphicsRendering':
			uid = '{10072CEC-8CC1-11D1-986E-00A0C955B42F}';
			break;
			
		case 'VietnameseTextDisplaySupport':
			uid = '{76C19B37-F0C8-11CF-87CC-0020AFEECF20}';
			break;
			
		case 'MicrosoftVirtualMachine':
			uid = '{08B0E5C0-4FCB-11CF-AAA5-00401C608500}';
			break;
			
		case 'VisualBasicScriptingSupport':
			uid = '{4F645220-306D-11D2-995D-00C04F98BBC9}';
			break;
			
		case 'WebFolders':
			uid = '{73FA19D0-2D75-11D2-995D-00C04F98BBC9}';
			break;
	}
	
	// invalid selection
	if ( uid == null )
		return false;
	
	// already installed
	if ( this.isComponentInstalled( uid ) )
		return true;
		
	oClientCaps.addComponentRequest( uid, "componentid" );
	
	try
	{
		return oClientCaps.doComponentRequest();
	}
	catch ( e )
	{
		return Base.raiseError( "Download of Component " + component + " failed." );
	}	
};

/**
 * @access public
 */
ClientCapabilities.prototype.clearDownloadQueue = function()
{
	oClientCaps.clearComponentRequest();
};


/**
 * @access public
 * @static
 */
ClientCapabilities.writeActiveXDetect = function()
{
	if ( Browser.ie && Browser.os == "win32" )
	{
		document.writeln( '<script language="VBscript">' );
		document.writeln( 'Dim detect_through_vb' );
		document.writeln( 'detect_through_vb = 0' );
		document.writeln( 'If ScriptEngineMajorVersion >= 2 then' );
		document.writeln( '	detect_through_vb = 1' );
		document.writeln( 'End If' );
		document.writeln( 'Function activeXDetect(activeXname)' );
		document.writeln( '	on error resume next' );
		document.writeln( '	If ScriptEngineMajorVersion >= 2 then' );
		document.writeln( '		activeXDetect = False' );
		document.writeln( '		activeXDetect = IsObject(CreateObject(activeXname))' );
		document.writeln( '		If (err) then' );
		document.writeln( '			activeXDetect = False' );
		document.writeln( '		End If' );
		document.writeln( '	Else' );
		document.writeln( '		activeXDetect = False' );
		document.writeln( '	End If' );
		document.writeln( 'End Function' );
		document.writeln( '</'+'script>' );
	}
};
