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
 * @package format
 */
 
/**
 * Constructor
 *
 * @access public
 */
MediaFactory = function( ext, file, w, h, div, id )
{
	this.Base = Base;
	this.Base();
	
	var w = w || 240;
	var h = h || 180;
	
	this.player  = null;
	
	this.play    = 
	this.stop    =
	this.pause   =
	this.prev    =
	this.next    =
	this.first   =
	this.last    = 
	this.setFile = function()
	{
		return false;
	}
	
	switch ( ext )
	{
		case 'real':
			this.player = new RealPlayer( w, h, false, div, id );
			this.player.setURL( file );
			this.player.setEnableContextMenu( false );
			this.player.setNoLogoMode( true );
			
			this.play    = MediaFactory.playReal;
			this.stop    = MediaFactory.stopReal;
			this.pause   = MediaFactory.pauseReal;
			this.prev    = MediaFactory.prevReal;
			this.next    = MediaFactory.nextReal;
			this.first   = MediaFactory.firstReal;
			this.last    = MediaFactory.lastReal;
			this.setFile = MediaFactory.setFileReal;
			
			break;
			
		case 'wmp':
			this.player = new MediaPlayer( w, h, false, false, div, id );
			this.player.setURL( file );
			this.player.disableContextMenu();
			
			this.play    = MediaFactory.playWindowsMedia;
			this.stop    = MediaFactory.stopWindowsMedia;
			this.pause   = MediaFactory.pauseWindowsMedia;
			this.prev    = MediaFactory.prevWindowsMedia;
			this.next    = MediaFactory.nextWindowsMedia;
			this.first   = MediaFactory.firstWindowsMedia;
			this.last    = MediaFactory.lastWindowsMedia;
			this.setFile = MediaFactory.setFileWindowsMedia;
			
			break;
			
		case 'flash':
			this.player  = new Flash( w, h, file, false, false, null, null, div, id );
			
			this.play    = MediaFactory.playFlash;
			this.stop    = MediaFactory.stopFlash;
			this.pause   = MediaFactory.pauseFlash;
			this.prev    = MediaFactory.prevFlash;
			this.next    = MediaFactory.nextFlash;
			this.first   = MediaFactory.firstFlash;
			this.last    = MediaFactory.lastFlash;
			this.setFile = MediaFactory.setFileFlash;
			
			break;
		
		/*
		// Right now, there is no public interface to the
		// Quicktime plugin available on Windows OS
		case 'quicktime':
			// TODO: setup player
			
			this.play    = MediaFactory.playQuicktime;
			this.stop    = MediaFactory.stopQuicktime;
			this.pause   = MediaFactory.pauseQuicktime;
			this.prev    = MediaFactory.prevQuicktime;
			this.next    = MediaFactory.nextQuicktime;
			this.first   = MediaFactory.firstQuicktime;
			this.last    = MediaFactory.lastQuicktime;
			this.setFile = MediaFactory.setFileQuicktime;
			
			break;
		*/
	}
};


MediaFactory.prototype = new Base();
MediaFactory.prototype.constructor = MediaFactory;
MediaFactory.superclass = Base.prototype;


/**
 * @abstract
 */
MediaFactory.playReal = function()
{
};

/**
 * @abstract
 */
MediaFactory.playWindowsMedia = function()
{
};

/**
 * @abstract
 */
MediaFactory.playFlash = function()
{
};

/**
 * @abstract
 */
MediaFactory.playQuicktime = function()
{
};


/**
 * @abstract
 */
MediaFactory.stopReal = function()
{
};

/**
 * @abstract
 */
MediaFactory.stopWindowsMedia = function()
{
};

/**
 * @abstract
 */
MediaFactory.stopFlash = function()
{
};

/**
 * @abstract
 */
MediaFactory.stopQuicktime = function()
{
};


/**
 * @abstract
 */
MediaFactory.pauseReal = function()
{
};

/**
 * @abstract
 */
MediaFactory.pauseWindowsMedia = function()
{
};

/**
 * @abstract
 */
MediaFactory.pauseFlash = function()
{
};

/**
 * @abstract
 */
MediaFactory.pauseQuicktime = function()
{
};


/**
 * @abstract
 */
MediaFactory.prevReal = function()
{
};

/**
 * @abstract
 */
MediaFactory.prevWindowsMedia = function()
{
};

/**
 * @abstract
 */
MediaFactory.prevFlash = function()
{
};

/**
 * @abstract
 */
MediaFactory.prevQuicktime = function()
{
};


/**
 * @abstract
 */
MediaFactory.nextReal = function()
{
};

/**
 * @abstract
 */
MediaFactory.nextWindowsMedia = function()
{
};

/**
 * @abstract
 */
MediaFactory.nextFlash = function()
{
};

/**
 * @abstract
 */
MediaFactory.nextQuicktime = function()
{
};


/**
 * @abstract
 */
MediaFactory.firstReal = function()
{
};

/**
 * @abstract
 */
MediaFactory.firstWindowsMedia = function()
{
};

/**
 * @abstract
 */
MediaFactory.firstFlash = function()
{
};

/**
 * @abstract
 */
MediaFactory.firstQuicktime = function()
{
};


/**
 * @abstract
 */
MediaFactory.lastReal = function()
{
};

/**
 * @abstract
 */
MediaFactory.lastWindowsMedia = function()
{
};

/**
 * @abstract
 */
MediaFactory.lastFlash = function()
{
};

/**
 * @abstract
 */
MediaFactory.lastQuicktime = function()
{
};


/**
 * @abstract
 */
MediaFactory.setFileReal = function()
{
};

/**
 * @abstract
 */
MediaFactory.setFileWindowsMedia = function()
{
};

/**
 * @abstract
 */
MediaFactory.setFileFlash = function()
{
};

/**
 * @abstract
 */
MediaFactory.setFileQuicktime = function()
{
};
