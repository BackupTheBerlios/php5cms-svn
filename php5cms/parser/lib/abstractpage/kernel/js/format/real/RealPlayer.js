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
 * @package format_real
 */
 
/**
 * RealPlayer Class (ActiveX)
 *
 * Callbacks fired by ActiveX Control (uppercase!):
 *
 * - OnAuthorChange( author )
 * - OnBuffering( flag, percentComplete )
 * - OnClipColsed()
 * - OnClipOpened( shortName, URL )
 * - OnContacting( host )
 * - OnCopyrightChange( copyright )
 * - OnGotoURL( URL, target )
 * - OnKeyDown( flags, key )
 * - OnKeyPress( flags, key )
 * - OnKeyUp( flags, key )
 * - OnLButtonDown( flags, x, y )
 * - OnLButtonUp( flags, x, y )
 * - OnMouseMove( flags, x, y )
 * - OnMuteChange( mute )
 * - OnPlayStateChange( oldState, newState )
 * - OnPosLength( pos, len )
 * - OnPositionChange( pos, len )
 * - OnPostSeek( oldTime, newTime )
 * - OnPreFetchComplete()
 * - OnPreSeek( oldTime, newTime )
 * - OnRButtonDown( flags, x, y )
 * - OnRButtonUp( flags, x, y )
 * - OnShowStatus( text )
 * - OnStateChange( oldState, newState )
 * - OnTitleChange( title )
 * - OnVolumeChange( newVolume )
 *
 * @see http://service.real.com/help/library/guides/extend/embed.htm
 */

/**
 * Constructor
 *
 * @access public
 */
RealPlayer = function( w, h, autostart, div, id )
{
	this.Base = Base;
	this.Base();
	
	this.mpid = id || "rplayer" + ( RealPlayer.idcount++ );
	
	this.elm = document.createElement( "OBJECT" );
	this.elm.id = this.elm.name = this.mpid;
	this.elm.classid = "clsid:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA";
	
	this.events = this.elm;
	this.style  = this.elm.style;
	
	// callbacks (fired by this object)
	this.events.onBeginPlay       =
	this.events.onPause           =
	this.events.onStop            =
	this.events.onNext            =
	this.events.onPrev            =
	this.events.onAuthorChange    =
	this.events.onCopyrightChange =
	this.events.onGotoURL         =
	this.events.onSetPosition     =
	this.events.onResize          =
	this.events.onTitleChange     =
	this.events.onMute            =
	this.events.onMuteChange      =
	this.events.onVolumeChange    = function()
	{
		return false;
	}

	this.setAutostart( autostart );
	this.setWH( w || 1, h || 1 );
	
	if ( div != null && document.getElementById( div ) )
		document.getElementById( div ).appendChild( this.elm );
	else
		document.getElementsByTagName( "BODY" ).item( 0 ).appendChild( this.elm );
};


RealPlayer.prototype = new Base();
RealPlayer.prototype.constructor = RealPlayer;
RealPlayer.superclass = Base.prototype;

/**
 * @access public
 */
RealPlayer.prototype.isLive = function()
{
	return this.elm.GetLiveState();
};

/**
 * @access public
 */
RealPlayer.prototype.isPlus = function()
{
	return this.elm.GetIsPlus();
};

/**
 * @access public
 */
RealPlayer.prototype.getVersion = function()
{
	return this.elm.GetVersionInfo();
};

/**
 * Note: available only in Embedded RealPlayer 6.0.8.1024 and later.
 *
 * @access public
 */
RealPlayer.prototype.getDRMIInfo = function( id )
{
	if ( id == null || id.length != 4 )
		return false;
	
	var i, pair;
	var info = this.elm.GetDRMIInfo.split( "&" );
	var ret  = new Dictionary();
	
	for ( i in info )
	{
		pair = info[i].split( "=" );
		ret.add( pair[0], pair[1] );	
	}
	
	return ret;
};

/**
 * @access public
 */
RealPlayer.prototype.play = function()
{
	if ( this.canPlay() )
	{
		this.elm.DoPlay();
		this.events.onBeginPlay();
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
RealPlayer.prototype.pause = function()
{
	if ( this.canPause() )
	{
		this.elm.DoPause();
		this.events.onPause();
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
RealPlayer.prototype.stop = function()
{
	if ( this.canStop() )
	{
		this.elm.DoStop();
		this.events.onStop();
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
RealPlayer.prototype.canPlay = function()
{
	return this.elm.CanPlay();
};

/**
 * @access public
 */
RealPlayer.prototype.canPause = function()
{
	return this.elm.CanPause();
};

/**
 * @access public
 */
RealPlayer.prototype.canStop = function()
{
	return this.elm.CanStop();
};

/**
 * @access public
 */
RealPlayer.prototype.setSize = function( size )
{
	if ( size == "double" )
		this.setDoubleSize();
	else if ( size == "original" )
		this.setOriginalSize();
	else if ( size == "fullscreen" )
		this.setFullscreen();
		
	this.events.onResize( size );
};

/**
 * @access public
 */
RealPlayer.prototype.getSize = function()
{
	if ( this.getDoubleSize() )
		return "double";
	else if ( this.getOriginalSize() )
		return "original";
	else if ( this.getFullscreen() )
		return "fullscreen";
};

/**
 * @access public
 */
RealPlayer.prototype.setDoubleSize = function()
{
	this.elm.SetDoubleSize();
	this.events.onResize( "double" );
};

/**
 * @access public
 */
RealPlayer.prototype.getDoubleSize = function()
{
	return this.elm.GetDoubleSize();
};

/**
 * @access public
 */
RealPlayer.prototype.setFullscreen = function()
{
	this.elm.SetFullScreen();
	this.events.onResize( "fullscreen" );
};

/**
 * @access public
 */
RealPlayer.prototype.getFullscreen = function()
{
	return this.elm.GetFullScreen();
};

/**
 * @access public
 */
RealPlayer.prototype.setOriginalSize = function()
{
	this.elm.SetOriginalSize();
	this.events.onResize( "original" );
};

/**
 * @access public
 */
RealPlayer.prototype.getOriginalSize = function()
{
	return this.elm.GetOriginalSize();
};

/**
 * @access public
 */
RealPlayer.prototype.setWH = function( w, h )
{
	if ( w != null && Util.is_int( w ) && w >= 1 )
		this.elm.width = w;
		
	if ( h != null && Util.is_int( w ) && w >= 1 )
		this.elm.height = h;
		
	this.events.onResize( "userdefined" );
};

/**
 * @access public
 */
RealPlayer.prototype.setVolume = function( vol )
{
	if ( vol != null && Util.is_percent( vol ) )
	{
		this.elm.SetVolume( vol );
		this.events.onVolumeChange( vol );
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getVolume = function()
{
	return this.elm.GetVolume();
};

/**
 * @access public
 */
RealPlayer.prototype.setMuteMode = function( mute )
{
	if ( mute != null && Util.is_bool( mute ) )
	{
		this.elm.SetMute( mute );
		this.events.onMuteChange( mute );
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getMuteMode = function()
{
	return this.elm.GetMute();
};

/**
 * @access public
 */
RealPlayer.prototype.mute = function()
{
	this.setMuteMode( true );
	this.events.onMute();
};

/**
 * @access public
 */
RealPlayer.prototype.isStereo = function()
{
	return this.elm.GetStereoState();
};

/**
 * @access public
 */
RealPlayer.prototype.setEnableContextMenu = function( enable )
{
	if ( enable != null && Util.is_bool( enable ) )
		this.elm.SetEnableContextMenu( enable );
};

/**
 * @access public
 */
RealPlayer.prototype.getEnableContextMenu = function()
{
	return this.elm.GetEnableContextMenu();
};

/**
 * @access public
 */
RealPlayer.prototype.setEnableDoubleSize = function( doublesize )
{
	if ( doublesize != null && Util.is_bool( doublesize ) )
		this.elm.SetEnableDoubleSize( doublesize );
};

/**
 * @access public
 */
RealPlayer.prototype.getEnableDoubleSize = function()
{
	return this.elm.GetEnableDoubleSize();
};

/**
 * @access public
 */
RealPlayer.prototype.setEnableFullscreen = function( fullscreen )
{
	if ( fullscreen != null && Util.is_bool( fullscreen ) )
		this.elm.SetEnableFullScreen( fullscreen );
};

/**
 * @access public
 */
RealPlayer.prototype.getEnableFullscreen = function()
{
	return this.elm.GetEnableFullScreen();
};

/**
 * @access public
 */
RealPlayer.prototype.setEnableOriginalSize = function( originalsize )
{
	if ( originalsize != null && Util.is_bool( originalsize ) )
		this.elm.SetEnableOriginalSize( originalsize );
};

/**
 * @access public
 */
RealPlayer.prototype.getEnableOriginalSize = function()
{
	return this.elm.GetEnableOriginalSize();
};

/**
 * @access public
 */
RealPlayer.prototype.setImageStatus = function( imagestatus )
{
	if ( imagestatus != null && Util.is_bool( imagestatus ) )
		this.elm.SetImageStatus( imagestatus );
};

/**
 * @access public
 */
RealPlayer.prototype.getImageStatus = function()
{
	return this.elm.GetImageStatus();
};

/**
 * @access public
 */
RealPlayer.prototype.getConnectionBandwidth = function()
{
	return this.elm.GetConnectionBandwidth();
};

/**
 * @access public
 */
RealPlayer.prototype.getCountry = function()
{
	return this.elm.GetUserCountryID();
};

/**
 * @access public
 */
RealPlayer.prototype.getPreferedLanguage = function( format )
{
	if ( format == "id" )
		return this.elm.GetPreferedLanguageID();
	else
		return this.elm.GetPreferedLanguageString();
};

/**
 * @access public
 */
RealPlayer.prototype.getSourceTransport = function( num )
{
	if ( num != null && Util.is_int( num ) && num >= 1 )
		return this.elm.GetSourceTransport();
	else
		return false;
};

/**
 * @access public
 */
RealPlayer.prototype.getSourcesCount = function()
{
	return this.elm.GetNumSources();
};

/**
 * @access public
 */
RealPlayer.prototype.getPacketsTotal = function()
{
	return this.elm.GetPacketsTotal();
};

/**
 * @access public
 */
RealPlayer.prototype.getPacketsMissing = function()
{
	return this.elm.GetPacketsMissing();
};

/**
 * @access public
 */
RealPlayer.prototype.getPacketsReceived = function()
{
	return this.elm.GetPacketsReceived();
};

/**
 * @access public
 */
RealPlayer.prototype.getPacketsOutOfOrder = function()
{
	return this.elm.GetPacketsOutOfOrder();
};

/**
 * @access public
 */
RealPlayer.prototype.getPacketsEarly = function()
{
	return this.elm.GetPacketsEarly();
};

/**
 * @access public
 */
RealPlayer.prototype.getPacketsLate = function()
{
	return this.elm.GetPacketsLate();
};

/**
 * @access public
 */
RealPlayer.prototype.getBandwidthAverage = function()
{
	return this.elm.GetBandwidthAverage();
};

/**
 * @access public
 */
RealPlayer.prototype.getBandwidthCurrent = function()
{
	return this.elm.GetBandwidthCurrent();
};

/**
 * @access public
 */
RealPlayer.prototype.getBufferingTimeElapsed = function( convert )
{
	return convert? RealPlayer._convertTime( this.elm.GetBufferingTimeElapsed() ) : this.elm.GetBufferingTimeElapsed();
};

/**
 * @access public
 */
RealPlayer.prototype.getBufferingTimeRemaining = function( convert )
{
	return convert? RealPlayer._convertTime( this.elm.GetBufferingTimeRemaining() ) : this.elm.GetBufferingTimeRemaining();
};

/**
 * @access public
 */
RealPlayer.prototype.getState = function( asString )
{
	var val = this.elm.GetPlayState();
	
	if ( asString == null )
	{
		return val;
	}
	else
	{
		var str = "";
		
		switch( val )
		{
			case 0:
				str = "stopped";
				break;
			
			case 1:
				str = "contacting";
				break;
			
			case 2:
				str = "buffering";
				break;
			
			case 3:
				str = "playing";
				break;
			
			case 4:
				str = "paused";
				break;
			
			case 5:
				str = "seeking";
				break;
			
			default:
				str = "unknown";
		}
	
		return str;
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getLastStatus = function()
{
	return this.elm.GetLastStatus();
};

/**
 * @access public
 */
RealPlayer.prototype.setAutoGotoURL = function( enableStart )
{
	this.elm.SetAutoGoToURL( enableStart || false );
};

/**
 * @access public
 */
RealPlayer.prototype.getAutoGotoURL = function()
{
	return this.elm.GetAutoGoToURL();
};

/**
 * @access public
 */
RealPlayer.prototype.gotoURL = function( url, target )
{
	if ( url != null )
	{
		this.elm.DoGotoURL( url, target || "_blank" );
		this.events.onGotoURL( url );
	}
};

/**
 * @access public
 */
RealPlayer.prototype.setAutostart = function( start )
{
	if ( start != null && Util.is_bool( start ) )
		this.elm.SetAutoStart( start );
};

/**
 * @access public
 */
RealPlayer.prototype.getAutostart = function()
{
	return this.elm.GetAutoStart();
};

/**
 * @access public
 */
RealPlayer.prototype.setBgColor = function( col )
{
	if ( col != null && RealPlayer._isColor( col ) )
		this.elm.SetBackgroundColor( col );
};

/**
 * @access public
 */
RealPlayer.prototype.getBgColor = function()
{
	return this.elm.GetBackgroundColor();
};

/**
 * @access public
 */
RealPlayer.prototype.setCenter = function( center )
{
	if ( center != null && Util.is_bool( center ) )
		this.elm.SetCenter( center );
};

/**
 * @access public
 */
RealPlayer.prototype.getCenter = function()
{
	return this.elm.GetCenter();
};

/**
 * @access public
 */
RealPlayer.prototype.setConsole = function( name )
{
	if ( name != null )
		this.elm.SetConsole( name );
};

/**
 * @access public
 */
RealPlayer.prototype.getConsole = function()
{
	return this.elm.GetConsole();
};

/**
 * @access public
 */
RealPlayer.prototype.setControls = function( controls )
{
	if ( controls != null )
		this.elm.SetControls( controls );
};

/**
 * @access public
 */
RealPlayer.prototype.getControls = function()
{
	return this.elm.GetControls();
};

/**
 * @access public
 */
RealPlayer.prototype.setMaintainAspectRatio = function( aspectratio )
{
	if ( aspectratio != null && Util.is_bool( aspectratio ) )
		this.elm.SetMaintainAspect( aspectratio );
};

/**
 * @access public
 */
RealPlayer.prototype.getMaintainAspectRatio = function()
{
	return this.elm.GetMaintainAspect();
};

/**
 * @access public
 */
RealPlayer.prototype.setLoopMode = function( loop )
{
	if ( loop != null && Util.is_bool( loop ) )
		this.elm.SetLoop( loop );
};

/**
 * @access public
 */
RealPlayer.prototype.getLoopMode = function()
{
	return this.elm.GetLoop();
};

/**
 * @access public
 */
RealPlayer.prototype.setNoLogoMode = function( logo )
{
	if ( logo != null && Util.is_bool( logo ) )
		this.elm.SetNoLogo( logo );
};

/**
 * @access public
 */
RealPlayer.prototype.getNoLogoMode = function()
{
	return this.elm.GetNoLogo();
};

/**
 * @access public
 */
RealPlayer.prototype.setLoopCount = function( num )
{
	if ( num != null && Util.is_int( num ) )
		this.elm.SetNumLoop( num );
};

/**
 * @access public
 */
RealPlayer.prototype.getLoopCount = function()
{
	return this.elm.GetNumLoop();
};

/**
 * @access public
 */
RealPlayer.prototype.setPreFetch = function( prefetch )
{
	if ( prefetch != null && Util.is_bool( prefetch ) )
		this.elm.SetPreFetch( prefetch );
};

/**
 * @access public
 */
RealPlayer.prototype.getPreFetch = function()
{
	return this.elm.GetPreFetch();
};

/**
 * @access public
 */
RealPlayer.prototype.setShuffleMode = function( shuffle )
{
	if ( shuffle != null && Util.is_bool( shuffle ) )
		this.elm.SetShuffle( shuffle );
};

/**
 * @access public
 */
RealPlayer.prototype.getShuffleMode = function()
{
	return this.elm.GetShuffle();
};

/**
 * @access public
 */
RealPlayer.prototype.setURL = function( file )
{
	if ( file != null && (
		 file.atBegin( "file://" ) || 
		 file.atBegin( "rtsp://" ) || 
		 file.atBegin( "http://" ) || 
		 file.atBegin( "pnm://"  ) ) )
	{
		this.elm.setSource( file );
		this.hasFile = true;
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getURL = function( shortform )
{
	if ( this.hasFile == true )
	{
		var fn = ( this.elm.getSource().indexOf( "\\" ) != -1 )?
			this.elm.getSource().substring( this.elm.getSource().lastIndexOf( "\\" ) + 1 ) : // local
			this.elm.getSource().substring( this.elm.getSource().lastIndexOf( "/"  ) + 1 );  // web
			
		return ( shortform == true )? fn : this.elm.getSource();
	}
	else
	{
		return false;
	}
};

/**
 * @access public
 */
RealPlayer.prototype.setCanSeek = function( can )
{
	if ( can != null && Util.is_bool( can ) )
		this.elm.SetCanSeek( can );
};

/**
 * @access public
 */
RealPlayer.prototype.getCanSeek = function()
{
	return this.elm.GetCanSeek();
};

/**
 * @access public
 */
RealPlayer.prototype.getLength = function( convert )
{
	return convert? RealPlayer._convertTime( this.elm.GetLength() ) : this.elm.GetLength();
};

/**
 * @access public
 */
RealPlayer.prototype.setPosition = function( ms )
{
	if ( ms != null && Util.is_int( ms ) )
	{
		this.elm.SetPosition( ms );
		this.events.onSetPosition();
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getPosition = function( convert )
{
	return convert? RealPlayer._convertTime( this.elm.GetPosition() ) : this.elm.GetPosition();
};

/**
 * @access public
 */
RealPlayer.prototype.setTitle = function( str )
{
	if ( str != null )
	{
		this.elm.SetTitle( str );
		this.onTitleChange( str );
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getTitle = function()
{
	return this.elm.GetTitle();
};

/**
 * Note: I´m not sure if this is supported by the ActiveX Control.
 *
 * @access public
 */
RealPlayer.prototype.setAuthor = function( str )
{
	if ( str != null )
	{
		this.elm.SetAuthor( str );
		this.onAuthorChange( str );
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getAuthor = function()
{
	return this.elm.GetAuthor();
};

/**
 * @access public
 */
RealPlayer.prototype.setCopyright = function( str )
{
	if ( str != null )
	{
		this.elm.SetCopyright( str );
		this.onCopyrightChange( str );
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getCopyright = function()
{
	return this.elm.GetCopyright();
};

/**
 * @access public
 */
RealPlayer.prototype.getClipWidth = function()
{
	return this.elm.GetClipWidth();
};

/**
 * @access public
 */
RealPlayer.prototype.getClipHeight = function()
{
	return this.elm.GetClipHeight();
};

/**
 * @access public
 */
RealPlayer.prototype.next = function()
{
	if ( this.hasNext() )
	{
		this.elm.DoNextEntry();
		this.events.onNext();
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
RealPlayer.prototype.prev = function()
{
	if ( this.hasPrev() )
	{
		this.elm.DoPrevEntry();
		this.events.onPrev();
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
RealPlayer.prototype.hasNext = function()
{
	return this.elm.HasNextEntry();
};

/**
 * @access public
 */
RealPlayer.prototype.hasPrev = function()
{
	return this.elm.HasPrevEntry();
};

/**
 * @access public
 */
RealPlayer.prototype.getEntryAbstract = function( num )
{
	if ( num != null || Util.is_int( num ) || ( num >= 0 && num <= this.getEntryCount() ) )
		return this.elm.GetEntryAbstract( num );
	else
		return this.elm.GetEntryAbstract( this.getEntryCount() );
};

/**
 * @access public
 */
RealPlayer.prototype.getEntryAuthor = function( num )
{
	if ( num != null || Util.is_int( num ) || ( num >= 0 && num <= this.getEntryCount() ) )
		return this.elm.GetEntryAuthor( num );
	else
		return this.elm.GetEntryAuthor( this.getEntryCount() );
};

/**
 * @access public
 */
RealPlayer.prototype.getEntryCopyright = function( num )
{
	if ( num != null || Util.is_int( num ) || ( num >= 0 && num <= this.getEntryCount() ) )
		return this.elm.GetEntryCopyright( num );
	else
		return this.elm.GetEntryCopyright( this.getEntryCount() );
};

/**
 * @access public
 */
RealPlayer.prototype.getEntryTitle = function( num )
{
	if ( num != null || Util.is_int( num ) || ( num >= 0 && num <= this.getEntryCount() ) )
		return this.elm.GetEntryTitle( num );
	else
		return this.elm.GetEntryTitle( this.getEntryCount() );
};

/**
 * @access public
 */
RealPlayer.prototype.getCurrentEntry = function()
{
	return this.elm.GetCurrentEntry();
};

/**
 * @access public
 */
RealPlayer.prototype.getEntryCount = function()
{
	// returns 1 on single entry
	return this.elm.GetNumEntries();
};

/**
 * @access public
 */
RealPlayer.prototype.setShowAbout = function( show )
{
	if ( show != null && Util.is_bool( show ) )
		this.elm.SetShowAbout( show );
};

/**
 * @access public
 */
RealPlayer.prototype.getShowAbout = function()
{
	return this.elm.GetShowAbout();
};

/**
 * @access public
 */
RealPlayer.prototype.setShowStatistics = function( show )
{
	if ( show != null && Util.is_bool( show ) )
		this.elm.SetShowStatistics( show );
};

/**
 * @access public
 */
RealPlayer.prototype.getShowStatistics = function()
{
	return this.elm.GetShowStatistics();
};

/**
 * @access public
 */
RealPlayer.prototype.setShowPreferences = function( show )
{
	if ( show != null && Util.is_bool( show ) )
		this.elm.SetShowPreferences( show );
};

/**
 * @access public
 */
RealPlayer.prototype.getShowPreferences = function()
{
	return this.elm.GetShowPreferences();
};

/**
 * @access public
 */
RealPlayer.prototype.setWantErrors = function( want )
{
	if ( want != null && Util.is_bool( want ) )
		this.elm.SetWantErrors( want );
};

/**
 * @access public
 */
RealPlayer.prototype.getWantErrors = function()
{
	return this.elm.GetWantErrors();
};

/**
 * @access public
 */
RealPlayer.prototype.getLastErrorSeverity = function( asString )
{
	var val = this.elm.GetLastErrorSeverity();
	
	if ( asString == null )
	{
		return val;
	}
	else
	{
		var str = "";
		
		switch( val )
		{
			case 0:
				str = "panic";
				break;
				
			case 1:
				str = "severe";
				break;
				
			case 2:
				str = "critical";
				break;
				
			case 3:
				str = "general";
				break;
				
			case 4:
				str = "warning";
				break;
				
			case 5:
				str = "notice";
				break;
				
			case 6:
				str = "informational";
				break;
				
			case 7:
				str = "debug";
				break;
		}
		
		return str;
	}
};

/**
 * @access public
 */
RealPlayer.prototype.getLastErrorUserCode = function()
{
	return this.elm.GetLastErrorUserCode();
};

/**
 * @access public
 */
RealPlayer.prototype.getLastErrorUserString = function()
{
	return this.elm.GetLastErrorUserString();
};

/**
 * @access public
 */
RealPlayer.prototype.getLastErrorRMACode = function()
{
	return this.elm.GetLastErrorRMACode();
};

/**
 * @access public
 */
RealPlayer.prototype.getLastErrorMoreInfoURL = function()
{
	return this.elm.GetLastErrorMoreInfoURL();
};

/**
 * @access public
 */
RealPlayer.prototype.setWantKeyboardEvents = function( evt )
{
	if ( evt != null && Util.is_bool( evt ) )
		this.elm.SetWantKeyboardEvents( evt );
};

/**
 * @access public
 */
RealPlayer.prototype.getWantKeyboardEvents = function()
{
	return this.elm.GetWantKeyboardEvents();
};

/**
 * @access public
 */
RealPlayer.prototype.setWantMouseEvents = function( evt )
{
	if ( evt != null && Util.is_bool( evt ) )
		this.elm.SetWantMouseEvents( evt );
};

/**
 * @access public
 */
RealPlayer.prototype.getWantMouseEvents = function()
{
	return this.elm.GetWantMouseEvents();
};

/**
 * @access public
 */
RealPlayer.prototype.setWantConsoleEvents = function( evt )
{
	if ( evt != null && Util.is_bool( evt ) )
		this.elm.SetConsoleEvents( evt );
};

/**
 * @access public
 */
RealPlayer.prototype.getWantConsoleEvents = function()
{
	return this.elm.GetConsoleEvents();
};


/**
 * @access public
 * @static
 */
RealPlayer.idcount = 0;

/**
 * @access public
 * @static
 */
RealPlayer.colors = new Array(
	"white",
	"silver",
	"gray",
	"black",
	"yellow",
	"fuchsia",
	"red",
	"maroon",
	"lime",
	"olive",
	"green",
	"purple",
	"aqua",
	"teal",
	"blue",
	"navy"
);

/**
 * @access private
 * @static
 */
RealPlayer._convertTime = function( ms )
{
	ms = ms / 1000;
	
    var mins = parseInt( ms / 60, 10 );
    var secs = parseInt( ms % 60, 10 );
	
	if ( isNaN( mins ) )
		mins = 0;
		
    ms = mins + ":" + ( ( secs < 10 )? "0" + secs : secs );
    return ms;
};

/**
 * @access private
 * @static
 */
RealPlayer._isColor = function( col )
{
	if ( col != null )
	{
		if ( ( col.length == 7 ) && ( col.charAt( 0 ) == "#" ) )
			return true;
			
		for ( var i in RealPlayer.colors )
		{
			if ( col == RealPlayer.colors[i] )
				return true;
		}
		
		return false;
	}
	else
	{
		return false;
	}
};
