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
 * @package util_agent
 */
 
/**
 * Constructor
 *
 * @access public
 */
Client = function()
{
	this.Base = Base;
	this.Base();

	// convert all characters to lowercase to simplify testing
    this.agt = navigator.userAgent.toLowerCase();

    // *** BROWSER VERSION ***
    // Note: On IE5, these return 4, so use is_ie5up to detect IE5.
	this.is_major = parseInt( navigator.appVersion );
	this.is_minor = parseFloat( navigator.appVersion );

    // Note: Opera and WebTV spoof Navigator.  We do strict client detection.
    // If you want to allow spoofing, take out the tests for opera and webtv.
	this.is_nav      = ( ( this.agt.indexOf( 'mozilla' )    != -1 ) && ( this.agt.indexOf( 'spoofer' ) == -1 ) && ( this.agt.indexOf( 'compatible' ) == -1 ) && ( this.agt.indexOf( 'opera' )   == -1 ) && ( this.agt.indexOf( 'webtv' )      == -1 ) && ( this.agt.indexOf( 'hotjava' ) == -1 ) );
    this.is_nav2     = (   this.is_nav && (   this.is_major == 2 ) );
    this.is_nav3     = (   this.is_nav && (   this.is_major == 3 ) );
    this.is_nav4     = (   this.is_nav && (   this.is_major == 4 ) );
    this.is_nav4up   = (   this.is_nav && (   this.is_major >= 4 ) );
    this.is_navonly  = (   this.is_nav && ( ( this.agt.indexOf( ";nav" ) != -1) || ( this.agt.indexOf( "; nav" ) != -1 ) ) );
    this.is_nav6     = (   this.is_nav && (   this.is_major == 5 ) );
    this.is_nav6up   = (   this.is_nav && (   this.is_major >= 5 ) );
    this.is_gecko    = (   this.agt.indexOf( 'gecko' ) != -1 );

    this.is_ie       = ( ( this.agt.indexOf( "msie" ) != -1 )   && ( this.agt.indexOf( "opera"    ) == -1 ) );
    this.is_ie3      = (   this.is_ie && ( this.is_major < 4 ) );
    this.is_ie4      = (   this.is_ie && ( this.is_major == 4 ) && ( this.agt.indexOf( "msie 4"   ) != -1 ) );
    this.is_ie4up    = (   this.is_ie && ( this.is_major >= 4 ) );
    this.is_ie5      = (   this.is_ie && ( this.is_major == 4 ) && ( this.agt.indexOf( "msie 5.0" ) != -1 ) );
    this.is_ie5_5    = (   this.is_ie && ( this.is_major == 4 ) && ( this.agt.indexOf( "msie 5.5" ) != -1 ) );
    this.is_ie5up    = (   this.is_ie && !this.is_ie3 && !this.is_ie4 );
    this.is_ie5_5up  = (   this.is_ie && !this.is_ie3 && !this.is_ie4 && !this.is_ie5 );
    this.is_ie6      = (   this.is_ie && ( this.is_major == 4 ) && ( this.agt.indexOf( "msie 6."  ) != -1 ) );
    this.is_ie6up    = (   this.is_ie && !this.is_ie3 && !this.is_ie4 && !this.is_ie5 && !this.is_ie5_5 );

    // KNOWN BUG: On AOL4, returns false if IE3 is embedded browser
    // or if this is the first browser window opened.  Thus the
    // variables is_aol, is_aol3, and is_aol4 aren't 100% reliable.
	this.is_aol      = ( this.agt.indexOf( "aol"     ) != -1 );
	this.is_aol3     = ( this.is_aol && this.is_ie3  );
	this.is_aol4     = ( this.is_aol && this.is_ie4  );
	this.is_aol5     = ( this.agt.indexOf( "aol 5"   ) != -1 );
	this.is_aol6     = ( this.agt.indexOf( "aol 6"   ) != -1 );

	this.is_opera    = ( this.agt.indexOf( "opera"   ) != -1 );
	this.is_opera2   = ( this.agt.indexOf( "opera 2" ) != -1 || this.agt.indexOf( "opera/2" ) != -1 );
	this.is_opera3   = ( this.agt.indexOf( "opera 3" ) != -1 || this.agt.indexOf( "opera/3" ) != -1 );
	this.is_opera4   = ( this.agt.indexOf( "opera 4" ) != -1 || this.agt.indexOf( "opera/4" ) != -1 );
	this.is_opera5   = ( this.agt.indexOf( "opera 5" ) != -1 || this.agt.indexOf( "opera/5" ) != -1 );
	this.is_opera5up = ( this.is_opera && !this.is_opera2 && !this.is_opera3 && !this.is_opera4 );

	this.is_webtv = ( this.agt.indexOf( "webtv" ) != -1 ); 

    this.is_TVNavigator = ( ( this.agt.indexOf( "navio" ) != -1 ) || ( this.agt.indexOf( "navio_aoltv" ) != -1 ) ); 
    this.is_AOLTV = this.is_TVNavigator;

	this.is_hotjava    = ( this.agt.indexOf( "hotjava" ) != -1 );
	this.is_hotjava3   = ( this.is_hotjava && ( this.is_major == 3 ) );
	this.is_hotjava3up = ( this.is_hotjava && ( this.is_major >= 3 ) );

    if ( this.is_nav2 || this.is_ie3 )
		this.is_js = 1.0;
    else if ( this.is_nav3 )
		this.is_js = 1.1;
    else if ( this.is_opera5up )
		this.is_js = 1.3;
    else if ( this.is_opera )
		this.is_js = 1.1;
    else if ( ( this.is_nav4 && ( this.is_minor <= 4.05 ) ) || this.is_ie4 )
		this.is_js = 1.2;
    else if ( ( this.is_nav4 && ( this.is_minor > 4.05 ) )  || this.is_ie5 )
		this.is_js = 1.3;
    else if ( this.is_hotjava3up )
		this.is_js = 1.4;
    else if ( this.is_nav6 || this.is_gecko )
		this.is_js = 1.5;
    // NOTE: In the future, update this code when newer versions of JS
    // are released. For now, we try to provide some upward compatibility
    // so that future versions of Nav and IE will show they are at
    // *least* JS 1.x capable. Always check for JS version compatibility
    // with > or >=.
    else if ( this.is_nav6up )
		this.is_js = 1.5;
    // NOTE: ie5up on mac is 1.4
    else if ( this.is_ie5up )
		this.is_js = 1.3
    // HACK: no idea for other browsers; always check for JS version with > or >=
    else
		this.is_js = 0.0;

    // *** PLATFORM ***
	this.is_win   = ( ( this.agt.indexOf( "win"            ) != -1 ) || ( this.agt.indexOf( "16bit" ) != -1 ) );
    // NOTE: On Opera 3.0, the userAgent string includes "Windows 95/NT4" on all
    // Win32, so you can't distinguish between Win95 and WinNT.
	this.is_win95 = ( ( this.agt.indexOf( "win95" ) != -1  ) || ( this.agt.indexOf( "windows 95" ) != -1 ) );

	this.is_win16 = ( ( this.agt.indexOf( "win16"          ) != -1 ) || ( this.agt.indexOf( "16bit" ) != -1 ) || ( this.agt.indexOf( "windows 3.1" ) != -1 ) || ( this.agt.indexOf( "windows 16-bit" ) != -1 ) );
	this.is_win31 = ( ( this.agt.indexOf( "windows 3.1"    ) != -1 ) || ( this.agt.indexOf( "win16" ) != -1 ) || ( this.agt.indexOf( "windows 16-bit" ) != -1 ) );
	this.is_winme = ( ( this.agt.indexOf( "win 9x 4.90"    ) != -1 ) );
	this.is_win2k = ( ( this.agt.indexOf( "windows nt 5.0" ) != -1 ) );

    // NOTE: Reliable detection of Win98 may not be possible. It appears that:
    // - On Nav 4.x and before you'll get plain "Windows" in userAgent.
    // - On Mercury client, the 32-bit version will return "Win98", but
    // the 16-bit version running on Win98 will still return "Win95".
	this.is_win98 = ( ( this.agt.indexOf( "win98" ) != -1 ) || ( this.agt.indexOf( "windows 98" ) != -1 ) );
	this.is_winnt = ( ( this.agt.indexOf( "winnt" ) != -1 ) || ( this.agt.indexOf( "windows nt" ) != -1 ) );
	this.is_win32 = (   this.is_win95 || this.is_winnt || this.is_win98 || ( ( this.is_major >= 4 ) && ( navigator.platform == "Win32" ) ) || ( this.agt.indexOf( "win32" ) != -1 ) || ( this.agt.indexOf( "32bit" ) != -1 ) );
	this.is_os2   = ( ( this.agt.indexOf( "os/2"  ) != -1 ) || ( navigator.appVersion.indexOf( "OS/2" ) != -1 ) || ( this.agt.indexOf( "ibm-webexplorer" ) != -1 ) );
	this.is_mac   = (   this.agt.indexOf( "mac"   ) != -1 );

    // hack ie5 js version for mac
    if ( this.is_mac && this.is_ie5up)
		this.is_js = 1.4;
		
	this.is_mac68k = ( this.is_mac && ( ( this.agt.indexOf( "68k" ) != -1 ) || ( this.agt.indexOf( "68000"   ) != -1 ) ) );
	this.is_macppc = ( this.is_mac && ( ( this.agt.indexOf( "ppc" ) != -1 ) || ( this.agt.indexOf( "powerpc" ) != -1 ) ) );

	this.is_sun      = (   this.agt.indexOf( "sunos"         ) != -1 );
	this.is_sun4     = (   this.agt.indexOf( "sunos 4"       ) != -1 );
	this.is_sun5     = (   this.agt.indexOf( "sunos 5"       ) != -1 );
	this.is_suni86   = (   this.is_sun &&  ( this.agt.indexOf( "i86" ) != -1 ) );
	this.is_irix     = (   this.agt.indexOf( "irix"          ) != -1 ); // SGI
	this.is_irix5    = (   this.agt.indexOf( "irix 5"        ) != -1 );
	this.is_irix6    = ( ( this.agt.indexOf( "irix 6"        ) != -1 ) || ( this.agt.indexOf( "irix6" ) != -1 ) );
	this.is_hpux     = (   this.agt.indexOf( "hp-ux"         ) != -1 );
	this.is_hpux9    = (   this.is_hpux && ( this.agt.indexOf( "09." ) != -1 ) );
	this.is_hpux10   = (   this.is_hpux && ( this.agt.indexOf( "10." ) != -1 ) );
	this.is_aix      = (   this.agt.indexOf( "aix"           ) != -1 ); // IBM
	this.is_aix1     = (   this.agt.indexOf( "aix 1"         ) != -1 );    
	this.is_aix2     = (   this.agt.indexOf( "aix 2"         ) != -1 );    
	this.is_aix3     = (   this.agt.indexOf( "aix 3"         ) != -1 );    
	this.is_aix4     = (   this.agt.indexOf( "aix 4"         ) != -1 );    
	this.is_linux    = (   this.agt.indexOf( "inux"          ) != -1 );
	this.is_sco      = (   this.agt.indexOf( "sco"           ) != -1 ) || ( this.agt.indexOf( "unix_sv" ) != -1 );
	this.is_unixware = (   this.agt.indexOf( "unix_system_v" ) != -1 ); 
	this.is_mpras    = (   this.agt.indexOf( "ncr"           ) != -1 ); 
	this.is_reliant  = (   this.agt.indexOf( "reliantunix"   ) != -1 );
	this.is_dec      = ( ( this.agt.indexOf( "dec"           ) != -1 ) || ( this.agt.indexOf( "osf1" ) != -1 ) || ( this.agt.indexOf( "dec_alpha" ) != -1 ) || ( this.agt.indexOf( "alphaserver" ) != -1 ) || ( this.agt.indexOf( "ultrix" ) != -1 ) || ( this.agt.indexOf( "alphastation" ) != -1 ) ); 
	this.is_sinix    = (   this.agt.indexOf( "sinix"         ) != -1 );
	this.is_freebsd  = (   this.agt.indexOf( "freebsd"       ) != -1 );
	this.is_bsd      = (   this.agt.indexOf( "bsd"           ) != -1 );
	this.is_unix     = ( ( this.agt.indexOf( "x11"           ) != -1 ) || this.is_sun || this.is_irix || this.is_hpux || this.is_sco ||this.is_unixware || this.is_mpras || this.is_reliant || this.is_dec || this.is_sinix || this.is_aix || this.is_linux || this.is_bsd || this.is_freebsd );
	this.is_vms      = ( ( this.agt.indexOf( "vax"           ) != -1 ) || ( this.agt.indexOf( "openvms" ) != -1 ) );
};


Client.prototype = new Base();
Client.prototype.constructor = Client;
Client.superclass = Base.prototype;
