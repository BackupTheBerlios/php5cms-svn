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
 * MediaPlayer Object
 * DOM-based wrapper for MediaPlayer Version 7 as
 * shipped with Windows 98 Gold, 98 SE, ME, NT4, 2000.
 *
 * The Player object supports the following events:
 *
 * - buffering
 * - currentItemChange
 * - currentPlaylistChange
 * - error
 * - markerHit
 * - mediaChange
 * - mediaCollectionChange
 * - modeChange
 * - openStateChange
 * - playlistChange
 * - playStateChange
 * - positionChange
 * - scriptCommand
 * - statusChange
 *
 * @package format_wmf_lib
 */

/**
 * Constructor
 *
 * @access public
 */
MediaPlayer = function( w, h, autostart, visible, div, id )
{
	this.Base = Base;
	this.Base();
	
	this.mpid = id || "mplayer" + ( MediaPlayer.idcount++ );
	
	this.elm = document.createElement( "OBJECT" );
	this.elm.id = this.elm.name = this.mpid;
	this.elm.classid = "clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6"; // Version 7
	this.style = this.elm.style;
	
	// shortcuts to hierarchical object model
	this.player        = this.elm;
	this.settings      = this.player.settings;
	this.controls      = this.player.controls;
	this.network       = this.player.network;
	this.closedcaption = this.player.closedcaption;
	
	// collections
	this.cdromcollection    = new MPCDRomCollection( this.player.cdromcollection );
	this.playlistcollection = new MPPlaylistCollection( this.player.playlistcollection );
	this.mediacollection    = new MPMediaCollection ( this.player.mediacollection );
	this.error              = new MPError( this.player.error );
	
	// error callbacks defined by this library
	this.onBeginPlay     = 
	this.onPause         =
	this.onStop          =
	this.onNext          =
	this.onPrev          =
	this.onFastForward   =
	this.onFastReverse   =
	this.onBalanceChange =
	this.onVolumeChange  = function()
	{
		return false;
	}
	
	this.hasFile = false;
	
	this.setWH( w || 0, h || 0 );
	this.setAutostartMode( autostart || 0 )

	if ( !visible )
		this.style.display = "none";
	
	if ( div != null && document.getElementById( div ) )
		document.getElementById( div ).appendChild( this.elm );
	else
		document.getElementsByTagName( "BODY" ).item( 0 ).appendChild( this.elm );
};


MediaPlayer.prototype = new Base();
MediaPlayer.prototype.constructor = MediaPlayer;
MediaPlayer.superclass = Base.prototype;

/**
 * @access public
 */
MediaPlayer.prototype.setWH = function( w, h )
{
	if ( w != null )
		this.style.width  = w;
	
	if ( h != null )
		this.style.height = h;
};

/**
 * Specifies the name of the clip to play.
 *
 * @access public
 */
MediaPlayer.prototype.setURL = function( file )
{
	if ( file != null )
	{
		this.player.URL = file;
		this.hasFile = true;
	}
};

/**
 * Retrieves the name of the clip to play.
 *
 * @access public
 */
MediaPlayer.prototype.getURL = function( shortform )
{
	if ( this.hasFile == true )
	{
		var fn = ( this.player.URL.indexOf( "\\" ) != -1 )?
			this.player.URL.substring( this.player.URL.lastIndexOf( "\\" ) + 1 ) : // local
			this.player.URL.substring( this.player.URL.lastIndexOf( "/"  ) + 1 );  // web
			
		return ( shortform == true )? fn : this.player.URL;
	}
	else
	{
		return false;
	}
};

/**
 * @access public
 */
MediaPlayer.prototype.enableContextMenu = function()
{
	this.player.enableContextMenu = true;
};

/**
 * @access public
 */
MediaPlayer.prototype.disableContextMenu = function()
{
	this.player.enableContextMenu = false;
};

/**
 * Specifies a value indicating whether the Windows Media Player control is enabled.
 *
 * @access public
 */
MediaPlayer.prototype.setEnabled = function( mode )
{
	if ( mode != null && Util.is_bool( mode ) )
		this.player.enabled = mode;
};

/**
 * Retrieves a value indicating whether the Windows Media Player control is enabled.
 *
 * @access public
 */
MediaPlayer.prototype.getEnabled = function()
{
	return this.player.enabled;
};

/**
 * Specifies a value indicating whether video content is played back in fullscreen mode.
 *
 * @access public
 */
MediaPlayer.prototype.setFullscreenMode = function( mode )
{
	if ( mode != null && Util.is_bool( mode ) )
		this.player.fullscreen = mode;
};

/**
 * Retrieves a value indicating whether video content is played back in fullscreen mode.
 *
 * @access public
 */
MediaPlayer.prototype.getFullscreenMode = function()
{
	return this.player.fullscreen;
};

/**
 * Retrieves a value indicating the status of Windows Media Player.
 *
 * @access public
 */
MediaPlayer.prototype.getStatus = function()
{
	return this.player.status;
};

/**
 * Retrieves a boolean value indicating whether the user is connected to a network.
 *
 * @access public
 */
MediaPlayer.prototype.isOnline = function()
{
	return this.player.isOnline;
};

/**
 * Sends a URL to the user´s default browser to be rendered.
 *
 * @access public
 */
MediaPlayer.prototype.launchURL = function( url )
{
	if ( url != null )
		this.player.launchURL( url );
};

/**
 * Retrieves a value indicating the state of the Windows Media Player operation.
 *
 * @access public
 */
MediaPlayer.prototype.getState = function( asString )
{
	var state = this.player.playState;
	
	if ( asString == null )
	{
		return state;
	}
	else
	{
		var str = "";
		
		switch( state )
		{
			case 0:
				str = "undefined";
				break;
				
			case 1:
				str = "stopped";
				break;
				
			case 2:
				str = "paused";
				break;
				
			case 3:
				str = "playing";
				break;
				
			case 4:
				str = "scanforward";
				break;
				
			case 5:
				str = "scanreverse";
				break;
				
			case 6:
				str = "buffering";
				break;
				
			case 7:
				str = "waiting";
				break;
				
			case 8:
				str = "mediaended";
				break;
				
			case 9:
				str = "transitioning";
				break;
				
			case 10:
				str = "ready";
				break;
		}
		
		return str;
	}
};

/**
 * @access public
 */
MediaPlayer.prototype.getOpenState = function()
{
	return this.player.openState;
};

/**
 * @access public
 */
MediaPlayer.prototype.stretchToFit = function( stretch )
{
	if ( stretch != null && Util.is_bool( stretch ) )
		this.player.stretchToFit = stretch;
}

/**
 * @access public
 */
MediaPlayer.prototype.setUIMode = function( mode )
{
	if ( mode != null && (
		 mode == "none" ||
		 mode == "mini" ||
		 mode == "full" ) )
	{
		this.player.uiMode = mode;
	}
};

/**
 * @access public
 */
MediaPlayer.prototype.getVersion = function()
{
	return this.player.versionInfo;
};

/**
 * @access public
 */
MediaPlayer.prototype.close = function()
{
	this.player.close();
};

/**
 * @access public
 */
MediaPlayer.prototype.getCurrentMedia = function()
{
	return new MPMedia( this.player.currentMedia );
};

/**
 * @access public
 */
MediaPlayer.prototype.getCurrentPlaylist = function()
{
	return new MPPlaylist( this.player.currentPlaylist );
};

/**
 * Specifies the current media item.
 *
 * @access public
 */
MediaPlayer.prototype.setCurrentItem = function( media )
{
	if ( media != null )
		this.controls.currentItem = media;
};

/**
 * Retrieves the current media item.
 *
 * @access public
 */
MediaPlayer.prototype.getCurrentItem = function()
{
	return new MPMedia( this.controls.currentItem );
};

/**
 * Specifies the current marker number.
 *
 * @access public
 */
MediaPlayer.prototype.setCurrentMarker = function( marker )
{
	if ( marker != null && Util.is_int( marker ) )
		this.controls.currentMarker = marker;
};

/**
 * Retrieves the current marker number.
 *
 * @access public
 */
MediaPlayer.prototype.getCurrentMarker = function()
{
	return this.controls.currentMarker;
};

/**
 * Specifies the current position in the media item in seconds from the beginning.
 *
 * @access public
 */
MediaPlayer.prototype.setCurrentPosition = function( pos )
{
	if ( pos != null && Util.is_float( pos ) )
		this.controls.currentPosition = pos;
};

/**
 * Retrieves the current position in the media item in seconds from the beginning.
 *
 * @access public
 */
MediaPlayer.prototype.getCurrentPosition = function()
{
	return this.controls.currentPosition;
};

/**
 * Plays the specified media item.
 *
 * @access public
 */
MediaPlayer.prototype.playItem = function( mediaItem )
{
	if ( mediaItem != null)
	{
		this.controls.playItem( mediaItem );
		this.onBeginPlay();
	}
};

/**
 * Retrieves the current position as a string formatted as hh:mm:ss.
 *
 * @access public
 */
MediaPlayer.prototype.getCurrentPositionString = function()
{
	return this.controls.currentPositionString;
};

/**
 * Determines whether a specified type of information is available or
 * a given action can be performed.
 *
 * @access public
 */
MediaPlayer.prototype.controls_isAvailable = function( name )
{
	if ( name != null && (
		 name == "Play"            ||
		 name == "Pause"           ||
		 name == "Stop"            ||
		 name == "FastForward"     ||
		 name == "FastReverse"     ||
		 name == "Next"            ||
		 name == "Previous"        ||
		 name == "CurrentMarker"   ||
		 name == "CurrentPosition" ||
		 name == "CurrentItem"    ) )
	{ 
		return this.controls.isAvailable( name );
	}
};

/**
 * @access public
 */
MediaPlayer.prototype.play = function()
{
	this.controls.play();
	this.onBeginPlay();
};

/**
 * @access public
 */
MediaPlayer.prototype.pause = function()
{
	this.controls.pause();
	this.onPause();
};

/**
 * @access public
 */
MediaPlayer.prototype.stop = function()
{
	this.controls.stop();
	this.onStop();
};

/**
 * @access public
 */
MediaPlayer.prototype.next = function()
{
	this.controls.next();
	this.onNext();
};

/**
 * @access public
 */
MediaPlayer.prototype.prev = function()
{
	this.controls.previous();
	this.onPrev();
};

/**
 * @access public
 */
MediaPlayer.prototype.fastForward = function()
{
	this.controls.fastForward();
	this.onFastForward();
};

/**
 * @access public
 */
MediaPlayer.prototype.fastReverse = function()
{
	this.controls.fastReverse();
	this.onFastReverse();
};

/**
 * Determines whether the loop mode or shuffle mode is active.
 *
 * @access public
 */
MediaPlayer.prototype.getMode = function( mode )
{
	if ( mode != "loop" || mode != "shuffle" )
		return this.settings.getMode( mode );
	else
		return -1;
};

/**
 * @access public
 */
MediaPlayer.prototype.setMode = function( mode, state )
{
	if ( ( mode != null && ( mode == "loop" || mode == "shuffle" ) ) && ( state != null && Util.is_bool( state ) ) )
		this.settings.setMode( mode, state )
};

/**
 * Specifies a value indicating whether error dialogs are shown automatically.
 *
 * @access public
 */
MediaPlayer.prototype.setErrorDialogsMode = function( b )
{
	if ( b != null && Util.is_bool( b ) )
		this.settings.enableErrorDialogs = b;
};

/**
 * Retrieves a value indicating whether error dialogs are shown automatically.
 *
 * @access public
 */
MediaPlayer.prototype.getErrorDialogsMode = function()
{
	return this.settings.enableErrorDialogs;
};

/**
 * Specifies the name of the frame used to display a URL received in a scriptCommand.
 *
 * @access public
 */
MediaPlayer.prototype.setDefaultFrame = function( str )
{
	if ( str != null )
		this.settings.defaultFrame = str;
};

/**
 * Retrieves the name of the frame used to display a URL received in a scriptCommand.
 *
 * @access public
 */
MediaPlayer.prototype.getDefaultFrame = function()
{
	return this.settings.defaultFrame;
};

/**
 * Specifies the base URL used for relative path resolution.
 *
 * @access public
 */
MediaPlayer.prototype.setBaseURL = function( url )
{
	if ( url != null )
		this.settings.baseURL = url;
};

/**
 * Retrieves the base URL used for relative path resolution.
 *
 * @access public
 */
MediaPlayer.prototype.getBaseURL = function()
{
	return this.settings.baseURL;
};

/**
 * Specifies the current stereo balance.
 *
 * @access public
 */
MediaPlayer.prototype.setBalance = function( balance )
{
	if ( balance != null && Util.is_range( balance ) )
	{
		this.settings.balance = balance;
		this.onBalanceChange();
	}
};

/**
 * Retrieves the current stereo balance.
 *
 * @access public
 */
MediaPlayer.prototype.getBalance = function()
{
	return this.settings.balance;
};

/**
 * Specifies a value indicating whether the current media item begins playing automatically.
 *
 * @access public
 */
MediaPlayer.prototype.setAutostartMode = function( b )
{
	if ( b != null && Util.is_bool( b ) )
		this.settings.autoStart = b;
};

/**
 * Retrieves a value indicating whether the current media item begins playing automatically.
 *
 * @access public
 */
MediaPlayer.prototype.getAutostartMode = function()
{
	return this.settings.autoStart;
};

/**
 * Specifies the current volume.
 *
 * @access public
 */
MediaPlayer.prototype.setVolume = function( vol )
{
	if ( vol != null && Util.is_percent( vol ) )
	{
		this.settings.volume = vol;
		this.onVolumeChange();
	}
};

/**
 * Retrieves the current volume.
 *
 * @access public
 */
MediaPlayer.prototype.getVolume = function()
{
	return this.settings.volume;
};

/**
 * Specifies the number of times a media item will play.
 *
 * @access public
 */
MediaPlayer.prototype.setPlayCount = function( count )
{
	if ( count != null && Util.is_int( count ) && count >= 1 )
		this.settings.playCount = count;
};

/**
 * Retrieves the number of times a media item will play.
 *
 * @access public
 */
MediaPlayer.prototype.getPlayCount = function()
{
	return this.settings.playCount;
};

/**
 * Specifies a value indicating whether audio is muted.
 * 
 * @access public
 */
MediaPlayer.prototype.setMuteMode = function( mute )
{
	if ( mute != null && Util.is_bool( mute ) )
		this.settings.mute = mute;
};

/**
 * Retrieves a value indicating whether audio is muted.
 *
 * @access public
 */
MediaPlayer.prototype.getMuteMode = function()
{
	return this.settings.mute;
};

/**
 * @access public
 */
MediaPlayer.prototype.mute = function()
{
	this.setMuteMode( true );
};

/**
 * Determines whether a specified type of information is available or a given action can be performed.
 *
 * @access public
 */
MediaPlayer.prototype.settings_isAvailable = function( str )
{
	if ( str != null /*&& str == "rate" */ )
		this.settings.isAvailable( str );
};

/**
 * Specifies a value indicating whether URL events should launch a Web browser.
 *
 * @access public
 */
MediaPlayer.prototype.setInvokeURLsMode = function( invoke )
{
	if ( invoke != null && Util.is_bool( invoke ) )
		this.settings.invokeURLs = invoke;
};

/**
 * Retrieves a value indicating whether URL events should launch a Web browser.
 *
 * @access public
 */
MediaPlayer.prototype.getInvokeURLsMode = function()
{
	return this.settings.invokeURLs;
};

/**
 * Specifies the current playback rate.
 *
 * @access public
 */
MediaPlayer.prototype.setRate = function( rate )
{
	if ( rate != null && Util.is_float( rate ) )
		this.settings.rate = rate;
};

/**
 * Retrieves the current playback rate.
 *
 * @access public
 */
MediaPlayer.prototype.getRate = function()
{
	return this.settings.rate;
};

/**
 * Retrieves the source protocol used to receive data.
 *
 * @access public
 */
MediaPlayer.prototype.getSourceProtocol = function()
{
	return this.network.sourceProtocol;
};

/**
 * Specifies the proxy setting for a given protocol.
 *
 * @access public
 */
MediaPlayer.prototype.setProxySettings = function( protocol, setting )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) &&
		   setting  != null && ( setting  >=      0 || setting  <=     3 ) )
	{
		this.network.setProxySettings( protocol, setting );
	}
};

/**
 * Retrieves the proxy setting for a given protocol.
 *
 * @access public
 */
MediaPlayer.prototype.getProxySettings = function( protocol )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) )
		return this.network.getProxySettings( protocol );
};

/**
 * Specifies the proxy port to use.
 *
 * @access public
 */
MediaPlayer.prototype.setProxyPort = function( protocol, port )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) &&
		   port     != null && Util.is_int( port ) )
	{
		this.network.setProxyPort( protocol, port );
	}
};

/**
 * Retrieves the proxy port to use.
 *
 * @access public
 */
MediaPlayer.prototype.getProxyPort = function( protocol )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) )
		return this.network.getProxyPort( protocol );
};

/**
 * Specifies the name of a proxy server to use.
 *
 * @access public
 */
MediaPlayer.prototype.setProxyName = function( protocol, name )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) && name != null )
	{
		this.network.setProxyName( protocol, name );
	}
};

/**
 * Retrieves the name of a proxy server to use.
 *
 * @access public
 */
MediaPlayer.prototype.getProxyName = function( protocol )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) )
		return this.network.getProxyName( protocol );
};

/**
 * Specifies the proxy exception list.
 *
 * @access public
 */
MediaPlayer.prototype.setProxyExceptionList = function( protocol, list )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) && list != null )
		this.network.setProxyExceptionList( protocol, list );
};

/**
 * Retrieves the proxy exception list.
 *
 * @access public
 */
MediaPlayer.prototype.getProxyExceptionList = function( protocol )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) )
		return this.network.getProxyExceptionList( protocol );
};

/**
 * Specifies a value indicating whether the proxy server should by bypassed if the origin server is on a local network.
 *
 * @access public
 */
MediaPlayer.prototype.setProxyBypassForLocal = function( protocol, bypass )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) &&
		   bypass   != null && Util.is_bool( bypass ) )
	{
		this.network.setProxyBypassForLocal( protocol, bypass );
	}
};

/**
 * Retrieves a value indicating whether the proxy server should by bypassed if the origin server is on a local network.
 *
 * @access public
 */
MediaPlayer.prototype.getProxyBypassForLocal = function( protocol )
{
	if ( ( protocol != null && ( protocol == "HTTP" || protocol == "MMS" ) ) )
		return this.network.getProxyBypassForLocal( protocol );
};

/**
 * Specifies the maximum allowed bandwidth.
 *
 * @access public
 */
MediaPlayer.prototype.setMaxBandwidth = function( bandwidth )
{
	if ( bandwidth != null && Util.is_int( bandwidth ) )
		this.network.maxBandwidth = bandwidth;
};

/**
 * Retrieves the maximum allowed bandwidth.
 *
 * @access public
 */
MediaPlayer.prototype.getMaxBandwidth = function()
{
	return this.network.maxBandwidth;
};

/**
 * Specifies the amount of buffering time in milliseconds before playing begins.
 *
 * @access public
 */
MediaPlayer.prototype.setBufferingTime = function( time )
{
	if ( time != null )
		this.network.bufferingTime = time;
};

/**
 * Retrieves the amount of buffering time in milliseconds before playing begins.
 *
 * @access public
 */
MediaPlayer.prototype.getBufferingTime = function()
{
	return this.network.bufferingTime;
};

/**
 * Retrieves the number of recovered packets.
 *
 * @access public
 */
MediaPlayer.prototype.getRecoveredPackets = function()
{
	return this.network.recoveredPackets;
};

/**
 * Retrieves the percentage of packets received in the last 30 seconds.
 *
 * @access public
 */
MediaPlayer.prototype.getReceptionQuality = function()
{
	return this.network.receptionQuality;
};

/**
 * Retrieves the number of packets received.
 *
 * @access public
 */
MediaPlayer.prototype.getReceivedPackets = function()
{
	return this.network.receivedPackets;
};

/**
 * Retrieves the number of packets lost.
 *
 * @access public
 */
MediaPlayer.prototype.getLostPackets = function()
{
	return this.network.lostPackets;
};

/**
 * Retrieves the total number of frames skipped during playback.
 *
 * @access public
 */
MediaPlayer.prototype.getFramesSkipped = function()
{
	return this.network.framesSkipped;
};

/**
 * Retrieves the current video frame rate in frames per second.
 *
 * @access public
 */
MediaPlayer.prototype.getFrameRate = function()
{
	return this.network.frameRate;
};

/**
 * Retrieves the video frame rate specified by the content author.
 *
 * @access public
 */
MediaPlayer.prototype.getEncodedFrameRate = function()
{
	return this.network.encodedFrameRate;
};

/**
 * Retrieves the percentage of download completed.
 *
 * @access public
 */
MediaPlayer.prototype.getDownloadProgress = function()
{
	return this.network.downloadProgress;
};

/**
 * Retrieves the percentage of buffering completed.
 *
 * @access public
 */
MediaPlayer.prototype.getBufferingProgress = function()
{
	return this.network.bufferingProgress;
};

/**
 * Retrieves the number of times buffering occurred during clip playback.
 *
 * @access public
 */
MediaPlayer.prototype.getBufferingCount = function()
{
	return this.network.bufferingCount;
};

/**
 * Retrieves the current bit rate being received.
 *
 * @access public
 */
MediaPlayer.prototype.getBitrate = function()
{
	return this.network.bitRate;
};

/**
 * Retrieves the current bandwidth of the clip.
 *
 * @access public
 */
MediaPlayer.prototype.getBandwidth = function()
{
	return this.network.bandWidth;
};

/**
 * Specifies the name of the frame or control displaying the captioning.
 *
 * @access public
 */
MediaPlayer.prototype.setCaptioningID = function( id )
{
	if ( id != null )
		this.closedcaption.captioningID = id;
};

/**
 * Retrieves the name of the frame or control displaying the captioning.
 *
 * @access public
 */
MediaPlayer.prototype.getCaptioningID = function()
{
	return this.closedcaption.captioningID;
};

/**
 * Specifies the name of the file containing the information needed for closed captioning.
 *
 * @access public
 */
MediaPlayer.prototype.setSAMIFileName = function( filename )
{
	if ( filename != null )
		this.closedcaption.SAMIFileName = filename;
};

/**
 * Retrieves the name of the file containing the information needed for closed captioning.
 *
 * @access public
 */
MediaPlayer.prototype.getSAMIFileName = function()
{
	return this.closedcaption.SAMIFileName;
};

/**
 * Specifies the language displayed for closed captioning.
 *
 * @access public
 */
MediaPlayer.prototype.setSAMILang = function( lang )
{
	if ( lang != null )
		this.closedcaption.SAMILang = lang;
};

/**
 * Retrieves the language displayed for closed captioning.
 *
 * @access public
 */
MediaPlayer.prototype.getSAMILang = function()
{
	return this.closedcaption.SAMILang;
};

/**
 * Specifies the closed captioning style.
 *
 * @access public
 */
MediaPlayer.prototype.setSAMIStyle = function( style )
{
	if ( style != null )
		this.closedcaption.SAMIStyle = style;
};

/**
 * Retrieves the closed captioning style.
 *
 * @access public
 */
MediaPlayer.prototype.getSAMIStyle = function()
{
	return this.closedcaption.SAMIStyle;
};


/**
 * @access public
 * @static
 */
MediaPlayer.idcount = 0;

/**
 * @access private
 * @static
 */
MediaPlayer._convertTime = function( tmp )
{
    var mins = parseInt( tmp / 60, 10 );
    var secs = parseInt( tmp % 60, 10 );
	
	if ( isNaN( mins ) )
		mins = 0;
		
    tmp = mins + ":" + ( ( secs < 10 )? "0" + secs : secs );
    return tmp;
};
