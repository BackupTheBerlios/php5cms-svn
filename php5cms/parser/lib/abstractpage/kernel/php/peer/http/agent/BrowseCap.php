<?php

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


using( 'peer.http.url.URLUtil' );
using( 'peer.http.cookie.CookieUtil' );


$GLOBALS["BROWSECAP_TEST_HTML"] = <<<EOF
<html>
<head>
			
<noscript><meta http-equiv="refresh" content="1; url=__redirectNoJs__"></noscript>

<meta http-equiv="refresh" content="__secsEquivRefresh__; url=__redirectNoJs__">

<script language="JavaScript">
<!--

function apCheck() 
{
	var apH = (window.screen)? screen.height : ''; // height
	var apW = (window.screen)? screen.width  : ''; // width
			
	if ( document.body ) 
	{
		var apHa = ( document.body.clientHeight )? document.body.clientHeight : ''; // height available
		var apWa = ( document.body.clientWidth  )? document.body.clientWidth  : ''; // width  available
	} 
	else 
	{
		var apHa = ''; // height available
		var apWa = ''; // width  available
	}
			
	var apCd = ( window.screen )         ? screen.colorDepth     : ''; // colorDepth
	var apCc = ( navigator.cpuClass )    ? navigator.cpuClass    : ''; // cpuClass
	var apCn = ( navigator.appCodeName ) ? navigator.appCodeName : ''; // browserCodeName
			
	// check dom
	if ( document.documentElement ) 
		var apDom = 'dom';
	else if ( document.all ) 
		var apDom = 'ie4';
	else if ( document.layers ) 
		var apDom = 'ns4';
	else if ( document.images ) 
		var apDom = 'basic';
	else 
		var apDom = '';  
				
	/* I think it's more secure to pick these on the server side
	var apVer = navigator.appVersion;
	var apAgt = navigator.userAgent;
	
	// still missing opera5+, ie55up, gecko, ns7
	var apDom    = document.getElementById? 1 : 0;
	var apOpera5 = ( apAgt.search(/(Opera)(\s)?(5).?(\d){0,2}/) != -1 )? 1 : 0;
	var apIe5    = ( apVer.indexOf( \"MSIE 5\"   ) > -1 && apDom && !apOpera5 )? 1 : 0; 
	var apIe55   = ( apVer.indexOf( \"MSIE 5.5\" ) > -1 || apVer.indexOf( \"MSIE 5.6\" ) > -1 );
	var apIe6    = ( apVer.indexOf( \"MSIE 6\"   ) > -1 && apDom && !apOpera5 )? 1 : 0;
	var apIe4    = ( document.all && !apDom && !apOpera5 )? 1 : 0;
	var apIe     = ( apIe4 || apIe5 || apIe6 );
	var apKonq2  = ( apAgt.indexOf( \"Konqueror\" ) > -1 );
	var apMacdtd = ( document.doctype && apIe )? document.doctype.name.indexOf( \".dtd\" ) != -1 : 0;
	var apIe6dtd = ( document.compatMode && apIe6 )? document.compatMode == \"CSS1Compat\" : 0;
	var apIedtd  = ( apMacdtd || apIe6dtd );
	var apMac    = apAgt.indexOf( \"Mac\" ) > -1; 
	var apNs6    = apGecko = ( apAgt.search(/gecko/i) != -1 )? 1 : 0; 
	var apNs4    = ( document.layers && !apDom )? 1 : 0;
	var apStrict = ( apIedtd || apGecko || apKonq2 );
	var apBw     = ( apIe6   || apIe5   || apIe4 || apGecko || apKonq2 );
	*/
			
	if ( window.clientInformation ) 
	{
		var apUl = ( window.clientInformation.userLanguage   )? window.clientInformation.userLanguage.toLowerCase()   : ''; // user language
		var apSl = ( window.clientInformation.systemLanguage )? window.clientInformation.systemLanguage.toLowerCase() : ''; // system language
		var apCp = ( window.clientInformation.cookieEnabled  )? 1 : 0;
		var apCt = ( window.clientInformation.connectionType )? window.clientInformation.connectionType : '';
	} 
	else 
	{
		var apUl = ''; // user language
		var apSl = ''; // system language
		var apCp = '';
		var apCt = '';
	}
			
	var d = new Date();
	var apTz    = d.getTimezoneOffset() / 60;				// time zone diff
	var apIsFmd = ( top.location != self.location )? 1 : 0; // isFramed. stupid javascript does not allow conversion of bool to int.
			
	var apFl   = ''; // flash
	var apFlv  = ''; // version
			
	if ( ( navigator.appName == "Microsoft Internet Explorer" ) && ( navigator.appVersion.indexOf( "Mac" ) != -1 ) ) 
	{
		// Should we keep that behavior?
		// IF THE USER IS RUNNING IE ON A MAC, EXTERNAL SCRIPTING WON'T WORK ON THIS PLATFORM
		// maybe we should ask the user if he has flash.
		// no change, keep var meaning as 'unknown'.
	} 
	else 
	{
		if ( navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"] ) 
		{
			// this does not work in explorer. ie does not have this object.
			apFl  = 0;
			apFlv = 0;
					
			if ( navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"] ) 
			{
				if ( navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin ) 
					apFl = 1;
			}
		} 
		else if ( navigator.appName == "Microsoft Internet Explorer" ) 
		{
			apFl  = ieCheckPlugin( "ShockwaveFlash.ShockwaveFlash" );
			apFlv = 0;
		} 
		else 
		{
			// no change, keep var meaning as 'unknown'.
		}
	}
			
	var apJe = '';
			
	if ( navigator.appName == "Netscape" ) 
	{
		// ie does not support this way of method checking...
		if ( navigator.javaEnabled ) 
		{
			apJe = ( navigator.javaEnabled() )? 1 : 0; // java (applet) enabled
		}
	} 
	else 
	{
		// typeof is supported in ns3+ and ie3+.
		// ns2 does not like this syntax (typeof):
		// that's why we use eval (since js1 => ns2+ ie3+)
		if ( eval( 'typeof navigator.javaEnabled != "undefined"' ) ) 
		{
			apJe = ( navigator.javaEnabled() )? 1 : 0; // java (applet) enabled
		}
	}
			
	var redirectUrl = '__redirectUrl__' +
		'&apCd='    + apCd    + // color depth
		'&apW='     + apW     + // width
		'&apH='     + apH     + // heigt
		'&apWa='    + apWa    + // width  available
		'&apHa='    + apHa    + // height available
		'&apTz='    + apTz    + // timezone diff gmt 
		'&apSl='    + apSl    + // system language
		'&apUl='    + apUl    + // user language
		'&apIsFmd=' + apIsFmd + // is framed
		'&apDom='   + apDom   + // dom
		'&apFl='    + apFl    + // flash version
		'&apJe='    + apJe    + // java applets enabled
		'&apCc='    + apCc    + // cpu class
		'&apCn='    + apCn    + // browser code name
		'&apCp='    + apCp    + // cookies enabled
		'&apCt='    + apCt    + // connection type
		'&apSpeed=' + w_speed;  // connection speed

	__redirectLine__
}

function redirect( url ) 
{
	self.location.href = url;
}
		
if ( navigator.appName == "Microsoft Internet Explorer" ) 
{
	document.writeln( '<script language="VBScript">' );
	document.writeln( 'function ieCheckPlugin(pluginName)' );
	document.writeln( '  err.clear' );
	document.writeln( '  on error resume next' );
	document.writeln( '  dim plugin' );
	document.writeln( '  set plugin = createobject(pluginName)' );
	document.writeln( '  if err.number <> 0 then' );
	document.writeln( '    ieCheckPlugin = 0' );
	document.writeln( '  else' );
	document.writeln( '    ieCheckPlugin = 1' );
	document.writeln( '  end if' );
	document.writeln( 'end function' );
	document.writeln( '</script>' );
}
		
//-->
</script>
			
</head>
			
<body onLoad="apCheck();">
			
One moment please...<br>
Un instant svp...<br>
Einen Moment bitte...<br>

<br><br>

If you do not get redirected automatically in a few seconds, please <a href="__redirectNoJs__">click here</a>.

<script language="JavaScript">

var w_start = new Date();

</script>

<!--  
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
...\)+.Y..6\z.'N.kZ%z.Cmw.R;!7jJj.V3_.wmu.5.zqjC.[
_|..z,DV0hI(mzby..*i#w.cf.;.l^xKY.S%.`(\.4.&.(m.R
i4..R(.K(1,S.%z:~('_aCuh.FktEa*crgk.oF.|.A~`z..0
O'3v[(SgPcLI..3I..z..y3zC3Sx*]1KX..j|zJ@.IbI}j{.fH
ZN:..J(.$x.%.4@pL.z.z.TU6v'C1v:-k4)..(].44AzLx...0
[8v);iHv;/3V4vKAq.o#HU;#....k'C_&zbi..}...NXx.J.
z.~nfbQ\^3z`v&.ud`3=\(Ah%.S.e~eDRP.S0R~I7.z.~.{2Q
hRz.t4ZgeUy_p(-_dl.r.5,/.6n..6B`..z...f.yn.J.+.c44
(/....HViY1z{w...^.d.1Wzyrdpy..(.hA:a.$HBV+zd&.TN
o_Jt.3v.z..!.d.oSq.HGiI'a.zWT(gz.p.!:v?y0D_=:G.5.j
L|yv..LF9g}.
-->

<script language="JavaScript">

var w_end     = new Date();
var w_durinms = w_end - w_start;
var w_speed   = 100 * 1000 / w_durinms;
var w_durins  = Math.round( w_durinms / 1000 );
var w_bps     = w_speed * 8;
var w_speed   = Math.round( w_speed * 10 ) / 10;
var w_bps     = Math.round( w_bps   * 10 ) / 10;

var speed;

if ( w_bps > 591 )
	speed = "bb";
	
if ( w_bps <= 591 && w_bps >= 342 )
	speed = "mb";

if ( w_bps < 342 )
  	speed = "56k";

</script>

</body>
</html>
EOF;


/**
 * @package peer_http_agent
 */
 
class BrowseCap extends PEAR 
{
	/**
	 * @access public
	 */
	var $runTestTemplate;
	
	/**
	 * @access public
	 */
	var $runTestTimeout;
	
	/**
	 * @access public
	 */
	var $data = array();


	/**
	 * @access public
	 */	
	function isWebCrawler( $userAgent, $ip = null, $hostName = null ) 
	{
		if ( $ip ) 
		{
			if ( substr( $ip, 0, 11 ) == '216.239.46.' ) 
				return true;
				
			if ( substr( $ip, 0, 11 ) == '216.239.51.' ) 
				return true;
			
			if ( $ip == '195.141.85.146' ) 
				return true;
		}

		$regExp = array( 
			'/T-H-U-N-D-E-R-S-T-O-N-E/i' 
		);
		
		while ( list(,$r) = each( $regExp ) ) 
		{
			if ( preg_match( $r, $userAgent ) ) 
				return true;
		}

		return false;
	}
	
	/**
	 * @access public
	 */
	function isEmailCrawler( $userAgent, $ip = null, $hostName = null ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function isCrawler( $userAgent, $ip = null, $hostName = null ) 
	{
		return ( $this->isWebCrawler( $userAgent, $ip ) || $this->isEmailCrawler( $userAgent, $ip ) );
	}

	/**
	 * @access public
	 */
	function compute() 
	{
		$data = &$this->data;
		$data['userAgent'] = $GLOBALS['HTTP_USER_AGENT'];
		
		list( 
			$data['browser'], 
			$data['browserFullVersion'], 
			$data['browserMajorVersion'], 
			$data['browserMinorVersion'], 
			$data['browserMinorVerlet'], 
			$data['isGecko'], 
			$data['browserBuild']
		) = $this->_getBrowserInfo( $data['userAgent'] );
		
		$data['browserCodeName']  = $_GET['apCn'];
		$data['browserLanguages'] = $GLOBALS['HTTP_SERVER_VARS']['HTTP_ACCEPT_LANGUAGE'];
		
		list( $data['os'], $data['osVersion'] ) = $this->_getOsInfo( $data['userAgent'] );
		
		if ( !empty( $_GET['apCc'] ) ) 
			$data['cpuClass'] = $_GET['apCc'];
			
		$data['ip'] = $GLOBALS['REMOTE_ADDR'];
		
		if ( isset( $GLOBALS['REMOTE_HOST'] ) ) 
		{
			$data['ipResolved'] = $GLOBALS['REMOTE_HOST'];
			$dotPos = strrpos( $data['ipResolved'], '.' );
			
			if ( $dotPos !== false ) 
				$data['country'] = strtolower( substr( $data['ipResolved'], $dotPos + 1 ) );
		}

		if ( !isset( $data['country'] ) ) 
		{
			if ( isset( $data['browserLanguages'] ) && !empty( $data['browserLanguages'] ) ) 
			{
				if ( strlen( $data['browserLanguages'][0] ) == 5 ) 
					$data['country'] = strtolower( substr( $data['browserLanguages'][0], -2 ) );
			}
		}

		$data['referrer'] = $GLOBALS['HTTP_REFERER'];
		
		if ( isset( $GLOBALS['HTTP_VIA'] ) ) 
			$data['via'] = $GLOBALS['HTTP_VIA'];
		
		$data['cookiesSession']    = ( isset( $_COOKIE['phpBcSession']   ) )? true : false;
		$data['cookiesPermanent']  = ( isset( $_COOKIE['phpBcPermanent'] ) )? true : false;
		
		if ( $data['browser'] == 'ie' ) 
		{
			$data['png'] = (bool)( $data['browserMajorVersion'] >= 5 );
		} 
		else if ( $data['browser'] == 'ns' ) 
		{
			if ( ( $data['browserMajorVersion'] >= 5 ) || ( ( $data['browserMajorVersion'] >= 4 ) && ( $data['browserMinorVersion'] >= 1 ) ) || ( ( $data['browserMajorVersion'] >= 4 ) && ( $data['browserMinorVerlet'] >= 4 ) )  ) 
				$data['png'] = true;
			else 
				$data['png'] = false;
		} 
		else if ( $data['browser'] == 'op' ) 
		{
			if ( ( $data['browserMajorVersion'] >= 4 ) || ( ( $data['browserMajorVersion'] >= 3 ) && ( $data['browserMinorVersion'] >= 6 ) ) || ( ( $data['browserMajorVersion'] >= 3 ) && ( $data['browserMinorVerlet'] >= 1 ) ) ) 
				$data['png'] = true;
			else 
				$data['png'] = false;
		} 
		else 
		{
			$data['png'] = null;
		}
			
		if ( empty( $_GET['apRun'] ) || ( isset( $_GET['js'] ) && ( $_GET['js'] == 0 ) ) ) 
		{
			$data['javaScript']        = null;
			$data['javaScriptEnabled'] = false;
			
			if ( ( ( $data['browser'] == 'ie' ) && ( $data['browserMajorVersion'] >= 5 ) ) || ( ( $data['isGecko'] ) && ( $data['browserBuild'] >= '20020530' ) ) ) 
			{
				$data['dom'] = 'dom';
			} 
			else 
			{
				if ( ( $data['browser'] == 'ie' ) && ( $data['browserMajorVersion'] == 4 ) ) 
					$data['dom'] = 'ie4';
				else if ( ( $data['browser'] == 'ns' ) && ( $data['browserMajorVersion'] == 4 ) ) 
					$data['dom'] = 'ns4';
			}
		} 
		else 
		{
			$data['isFramed']          = (bool)$_GET['apIsFmd'];
			$data['height']            =       $_GET['apH'];
			$data['width']             =       $_GET['apW'];
			$data['heightAvailable']   =       $_GET['apHa'];
			$data['widthAvailable']    =       $_GET['apWa'];
			$data['colorDepth']        =  (int)$_GET['apCd'];
			$data['langUser']          =       $_GET['apUl']; // user language
			$data['langSystem']        =       $_GET['apSl']; // system language
			$data['timeZoneDiffGmt']   =       $_GET['apTz']; // time zone diff
			$data['connectionType']    =       $_GET['apCt'];
			$data['dom']               =       $_GET['apDom'];
			
			$data['javaScript']        = true;
			$data['javaScriptEnabled'] = true;
	
			$_javascriptVersions = array(
				'1.5' => array( 'IE6UP', 'NS6UP', 'NS5UP' ),
				// 1.4?
				'1.3' => array( 'IE5UP', 'NS4.05UP', 'OP5UP' ),
				'1.2' => array( 'NS4UP', 'IE4UP' ),
				'1.1' => array( 'NS3UP', 'OP'    ),
				'1.0' => array( 'NS2UP', 'IE3UP' ) 
			);
			
			foreach ( $_javascriptVersions as $version => $browserList ) 
			{
				foreach ( $browserList as $nr => $browserType )
				{
					if ( $this->is( 'b:' . $browserType ) ) 
					{
						$data['javaScriptVersion'] = $version;
						break 2;
					}
				}
			}
			
			if ( isset( $_GET['apJe'] ) ) 
			{
				if ( $_GET['apJe'] ) 
				{
					$data['javaApplets']        = true;
					$data['javaAppletsEnabled'] = true;
				} 
				else 
				{
					$data['javaApplets']        = null;
					$data['javaAppletsEnabled'] = false;
				}
			}

			if ( isset( $_GET['apFl'] ) ) 
			{
				if ( $_GET['apFl'] > 0 ) 
				{
					$data['pluginFlash']        = true;
					$data['pluginFlashVersion'] = (int)$_GET['apFl'];
				} 
				else 
				{
					$data['pluginFlash'] = false;
				}
			}
			
			if ( isset( $_GET['apSpeed'] ) )
			{
				$data['connectionSpeed'] = (int)$_GET['apSpeed'];
			}
		}
	}

	/**
	 * @access public
	 */
	function runTest() 
	{
		CookieUtil::set( 'phpBcSession',   'test', '', '/', '', 0 );
		CookieUtil::set( 'phpBcPermanent', 'test', time() + 900000, '/' );
		
		$redirectUrl = URLUtil::getUrlJunk( '7' );
		
		if ( strpos( $redirectUrl, '?' ) !== false ) 
			$redirectUrl .= '&';
		else 
			$redirectUrl .= '?';

		$redirectUrl  .= 'apRun=1';
		$redirectNoJs  = $redirectUrl . '&js=0';
		
		if ( $this->runTestTemplate )
			$page = join( '', file( $this->runTestTemplate ) );
		else 
			$page = $GLOBALS["BROWSECAP_TEST_HTML"];

		$secsEquivRefresh = 6;
		
		if ( $this->runTestTimeout ) 
		{
			$secsEquivRefresh += $this->runTestTimeout;
			$redirectLine = "window.setTimeout(\"redirect('\" + redirectUrl + \"')\", {$this->runTestTimeout}000);";
		} 
		else 
		{
			$redirectLine = "self.location.href = redirectUrl;";
		}
		
		/* do some replacements */
		$page = str_replace( "__redirectUrl__",      $redirectUrl,      $page );
		$page = str_replace( "__redirectNoJs__",     $redirectNoJs,     $page );
		$page = str_replace( "__redirectLine__",     $redirectLine,     $page );
		$page = str_replace( "__secsEquivRefresh__", $secsEquivRefresh, $page );
		
		echo $page;
		exit;
	}

	/**
	 * @access public
	 */
	function is( $s ) 
	{
		$status = false;
		
		do 
		{
			if ( preg_match( '/l:([a-z-]{2,})/i', $s, $match ) ) 
				$status = $this->_performLanguageSearch( $match );
			else if ( preg_match( '/b:([a-z]+)([0-9]*)([\.0-9]*)(up)?/i', $s, $match ) ) 
				$status = $this->_performBrowserSearch( $match );
		} while ( false );
		
		return $status;
	}

	
	// private methods

	/**
	 * @access private
	 */	
	function _getOsInfo( &$userAgent ) 
	{
		$regex_windows  = '/(win[dows]*)[\s]?([0-9a-z]*)[\w\s]?([a-z0-9.]*)/i';
		$regex_mac      = '/(68)[k0]{1,3}|[p\S]{1,5}(pc)/i';
		$regex_os2      = '/os\/2|ibm-webexplorer/i';
		$regex_sunos    = '/(sun|i86)[os\s]*([0-9]*)/i';
		$regex_irix     = '/(irix)[\s]*([0-9]*)/i';
		$regex_hpux     = '/(hp-ux)[\s]*([0-9]*)/i';
		$regex_aix      = '/aix([0-9]*)/i';
		$regex_dec      = '/dec|osfl|alphaserver|ultrix|alphastation/i';
		$regex_vms      = '/vax|openvms/i';
		$regex_sco      = '/sco|unix_sv/i';
		$regex_linux    = '/x11|inux/i';
		$regex_bsd      = '/(free)?(bsd)/i';
		
		if ( preg_match_all( $regex_windows, $userAgent, $match ) ) 
		{
			$v  = $match[2][count( $match[0] ) - 1];
			$v2 = $match[3][count( $match[0] ) - 1];
			
			if ( stristr( $v, 'NT' ) && $v2 == 5 ) 
				$v = '2000';
			else if ( stristr( $v, 'NT' ) && $v2 > 5 ) 
				$v = 'xp';
			else if ( $v . $v2 == '16bit' ) 
				$v = '31';
			else 
				$v .= $v2;

			if ( empty( $v ) ) 
				$v = 'win';
			
			return array( 'win', strtolower( $v ) );
		} 
		else if ( preg_match( $regex_os2, $userAgent ) ) 
		{
			return array( 'os2', 'os2' );
		} 
		else if ( preg_match( $regex_mac, $userAgent, $match ) ) 
		{
			$os = !empty( $match[1] )? '68k' : '';
			$os = !empty( $match[2] )? 'ppc' : $os;
			
			return array( 'mac', $os );
		} 
		else if ( preg_match( $regex_sunos, $userAgent, $match ) ) 
		{
			if ( !stristr( 'sun', $match[1] ) ) 
				$match[1] = 'sun' . $match[1];
			
			return array( '*nix', $match[1] . $match[2] );
		} 
		else if ( preg_match( $regex_irix, $userAgent,$match ) ) 
		{
			return array( '*nix', $match[1] . $match[2] );
		} 
		else if ( preg_match( $regex_hpux, $userAgent, $match ) ) 
		{
			$match[1] = str_replace( '-', '', $match[1] );
			$match[2] = (int)$match[2];
			
			return array( '*nix', $match[1] . $match[2] );
		} 
		else if ( preg_match( $regex_aix, $userAgent, $match ) ) 
		{
			return array( '*nix', 'aix' . $match[1] );
		} 
		else if ( preg_match( $regex_dec, $userAgent, $match ) ) 
		{
			return array( '*nix', 'dec' );
		} 
		else if ( preg_match( $regex_vms, $userAgent, $match ) ) 
		{
			return array( '*nix', 'vms' );
		} 
		else if ( preg_match( $regex_sco, $userAgent, $match ) ) 
		{
			return array( '*nix', 'sco' );
		} 
		else if ( stristr( 'unix_system_v', $userAgent ) ) 
		{
			return array( '*nix', 'unixware' );
		} 
		else if ( stristr( 'ncr', $userAgent ) ) 
		{
			return array( '*nix', 'mpras' );
		} 
		else if ( stristr( 'reliantunix', $userAgent ) ) 
		{
			return array( '*nix', 'reliant' );
		} 
		else if ( stristr( 'sinix', $userAgent ) ) 
		{
			return array( '*nix', 'sinix' );
		} 
		else if ( preg_match( $regex_bsd, $userAgent, $match ) ) 
		{
			return array( '*nix', $match[1] . $match[2] );
		} 
		else if ( preg_match( $regex_linux, $userAgent, $match ) ) 
		{
			return array( '*nix', 'linux' );
		} 
		else 
		{
			return array( 'unknown', 'unknown' );
		}
	}

	/**
	 * @access private
	 */	
	function _getBrowserInfo( $userAgent ) 
	{
		$browsersArray = array(
			'microsoft internet explorer' => 'ie',
			'msie'                        => 'ie',
			'netscape6'                   => 'ns',
			'mozilla'                     => 'ns',
			'opera'                       => 'op',
			'konqueror'                   => 'kq',
			'icab'                        => 'ic',
			'lynx'                        => 'lx',
			'ncsa mosaic'                 => 'mo',
			'amaya'                       => 'ay',
			'omniweb'                     => 'ow'
		);
		
		$allowMasquerading = false;
		$firstLoop         = true;
		$browsersString    = '';
		
		foreach ( $browsersArray as $fullName => $abreviation ) 
		{
			$browsersString .= $firstLoop? $fullName : "|" . $fullName;
			
			if ( $firstLoop )  
				$firstLoop = false;
		}

		$versionString = "[\/\sa-z]*([0-9]+)([\.0-9a-z]+)";
		$regExp        = "/($browsersString)$versionString/i";
		$gecko         = null;
		$browserBuild  = null;
		
		if ( preg_match_all( $regExp, $userAgent, $results ) ) 
		{
			$count = count( $results[0] ) - 1;
			
			if ( $allowMasquerading && ( $count > 0 ) ) 
				$count--;
				
			$which   = strtolower( $results[1][$count] );
			$browser = $browsersArray[$which];
			$major   = $results[2][$count];
			
			preg_match( '/([.\0-9]+)([\.a-z0-9]+)?/i', $results[3][$count], $match );
			
			$minor   = substr( $match[1], 1 );
			$verlet  = $match[2];
			$full    = $major . '.' . $minor . $verlet;
			$gecko   = (bool)preg_match( "/gecko/i", $userAgent );
			
			if ( $gecko ) 
			{
				if ( preg_match( '/gecko\/([0-9]{8})/i', $userAgent, $geckoMatch ) ) 
					$browserBuild = $geckoMatch[1];
			}

			return array( $browser, $full, $major, $minor, $verlet, $gecko, $browserBuild );
		}

		return array( null, null, null, null, null, $gecko, $browserBuild );
	}
	
	/**
	 * @access private
	 */	
	function _performBrowserSearch( $data ) 
	{
		$search['phrase']  = $data[0];
		$search['name']    = strtolower( $data[1] );
		$search['maj_ver'] = $data[2];
		
		if ( !empty( $data[3] ) ) 
			$search['min_ver'] = substr( $data[3], 1 );

		$search['up'] = !empty( $data[4] );
		$looking_for  = (double)( $search['maj_ver'] . '.' . $search['min_ver'] );
		
		if ( ( $search['name'] == 'aol' ) || ( $search['name'] == 'webtv' ) ) 
		{
			return stristr( $this->data['userAgent'], $search['name'] );
		} 
		else if ( $this->data['browser'] == $search['name'] ) 
		{
			$majv = $search['maj_ver']? $this->data['browserMajorVersion'] : '';
			$minv = $search['min_ver']? $this->data['browserMinorVersion'] : '';
			$what_we_are = (double)( $majv . '.' . $minv );
			
			if ( $search['up'] && ( $what_we_are >= $looking_for ) ) 
				return true;
			else if ( $what_we_are == $looking_for )
				return true;
		}

		return false;
	}

	/**
	 * @access private
	 */	
	function _performLanguageSearch( $data ) 
	{
		$this->_getLanguages();
		return stristr( $this->_browser_info['language'], $data[1] );
	}

	/**
	 * @access private
	 */	
	function _getLanguages() 
	{
		if ( !$this->_get_languages_ran_once )
		{
			if ( $languages = getenv( 'HTTP_ACCEPT_LANGUAGE' ) ) 
				$languages = preg_replace( '/(;q=[0-9]+.[0-9]+)/i', '', $languages );
			else 
				$languages = $this->_default_language;

			$this->_insert( 'language', $languages );
			$this->_get_languages_ran_once = true;
		}
	}

	/**
	 * @access private
	 */	
	function _get_ip() 
	{
		$ip = ( $tmp = getenv( HTTP_CLIENT_IP ) )? $tmp : getenv( REMOTE_ADDR );
		$this->_insert( 'ip', $ip );
	}

	/**
	 * @access private
	 */	
	function _insert( $k, $v )
	{
		$this->_browser_info[strtolower( $k )] = strtolower( $v );
	}

	/**
	 * @access private
	 */	
	function _test_cookies() 
	{
		global $ctest, $phpSniff_testCookie;
		
		if ( $this->_check_cookies ) 
		{
			if ( $ctest != 1 ) 
			{
				setcookie( 'phpSniff_testCookie', 'test', 0, '/' );
				$QS = getenv( QUERY_STRING );
				$script_path = ( $tmp = getenv( PATH_INFO ) )? $tmp : getenv( SCRIPT_NAME );
				$location    = $script_path . ( $QS == ""? "?ctest=1" : "?" . $QS . "&ctest=1" );
				
				header( "Location: $location" );
				exit;
			}
			else if ( $phpSniff_testCookie == "test" ) 
			{
				$this->_insert( 'cookies', true );
			}
			else 
			{
				$this->_insert( 'cookies', false );
			}
		}
		else 
		{
			$this->_insert( 'cookies', false );
		}  
	}
} // END OF BrowseCap

?>
