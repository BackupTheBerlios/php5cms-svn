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


using( 'peer.irc.lib.IRCActionHandler' );
using( 'peer.irc.lib.IRCData' );
using( 'peer.irc.lib.IRCListenFor' );
using( 'peer.irc.lib.IRCTimeHandler' );
using( 'util.Util' );


/*
 * IRC conforms to RFC 2812 (Internet Relay Chat: Client Protocol)
 */
 
define( 'IRC_CRLF',                 "\r\n" );

define( 'IRC_STDOUT',                    0 );
define( 'IRC_SYSLOG',                    1 );

define( 'IRC_UNUSED',                  '*' );

define( 'IRC_STATE_DISCONNECTED',        0 );
define( 'IRC_STATE_CONNECTING',          1 );
define( 'IRC_STATE_CONNECTED',           2 );

define( 'IRC_DEBUG_NONE',                0 );
define( 'IRC_DEBUG_NOTICE',              1 );
define( 'IRC_DEBUG_CONNECTION',          2 );
define( 'IRC_DEBUG_SOCKET',              4 );
define( 'IRC_DEBUG_IRCMESSAGES',         8 );
define( 'IRC_DEBUG_MESSAGETYPES',       16 );
define( 'IRC_DEBUG_ACTIONHANDLER',      32 );
define( 'IRC_DEBUG_TIMEHANDLER',        64 );
define( 'IRC_DEBUG_ALL',               127 );

define( 'IRC_TYPE_UNKNOWN',              1 );
define( 'IRC_TYPE_CHANNEL',              2 );
define( 'IRC_TYPE_QUERY',                4 );
define( 'IRC_TYPE_CTCP',                 8 );
define( 'IRC_TYPE_NOTICE',              16 );
define( 'IRC_TYPE_WHO',                 32 );
define( 'IRC_TYPE_JOIN',                64 );
define( 'IRC_TYPE_INVITE',             128 );
define( 'IRC_TYPE_ACTION',             256 );
define( 'IRC_TYPE_TOPICCHANGE',        512 );
define( 'IRC_TYPE_NICKCHANGE',        1024 );
define( 'IRC_TYPE_KICK',              2048 );
define( 'IRC_TYPE_QUIT',              4096 );
define( 'IRC_TYPE_LOGIN',             8192 );
define( 'IRC_TYPE_INFO',             16384 );
define( 'IRC_TYPE_LIST',             32768 );
define( 'IRC_TYPE_NAME',             65536 );
define( 'IRC_TYPE_MOTD',            131072 );
define( 'IRC_TYPE_MODECHANGE',      262144 );
define( 'IRC_TYPE_PART',            524288 );
define( 'IRC_TYPE_ERROR',          1048576 );
define( 'IRC_TYPE_BANLIST',        2097152 );
define( 'IRC_TYPE_TOPIC',          4194304 );

define( 'IRC_RPL_WELCOME',           '001' );
define( 'IRC_RPL_YOURHOST',          '002' );
define( 'IRC_RPL_CREATED',           '003' );
define( 'IRC_RPL_MYINFO',            '004' );
define( 'IRC_RPL_BOUNCE',            '005' );
define( 'IRC_RPL_TRACELINK',         '200' );
define( 'IRC_RPL_TRACECONNECTING',   '201' );
define( 'IRC_RPL_TRACEHANDSHAKE',    '202' );
define( 'IRC_RPL_TRACEUNKNOWN',      '203' );
define( 'IRC_RPL_TRACEOPERATOR',     '204' );
define( 'IRC_RPL_TRACEUSER',         '205' );
define( 'IRC_RPL_TRACESERVER',       '206' );
define( 'IRC_RPL_TRACESERVICE',      '207' );
define( 'IRC_RPL_TRACENEWTYPE',      '208' );
define( 'IRC_RPL_TRACECLASS',        '209' );
define( 'IRC_RPL_TRACERECONNECT',    '210' );
define( 'IRC_RPL_STATSLINKINFO',     '211' );
define( 'IRC_RPL_STATSCOMMANDS',     '212' );
define( 'IRC_RPL_ENDOFSTATS',        '219' );
define( 'IRC_RPL_UMODEIS',           '221' );
define( 'IRC_RPL_SERVLIST',          '234' );
define( 'IRC_RPL_SERVLISTEND',       '235' );
define( 'IRC_RPL_STATSUPTIME',       '242' );
define( 'IRC_RPL_STATSOLINE',        '243' );
define( 'IRC_RPL_LUSERCLIENT',       '251' );
define( 'IRC_RPL_LUSEROP',           '252' );
define( 'IRC_RPL_LUSERUNKNOWN',      '253' );
define( 'IRC_RPL_LUSERCHANNELS',     '254' );
define( 'IRC_RPL_LUSERME',           '255' );
define( 'IRC_RPL_ADMINME',           '256' );
define( 'IRC_RPL_ADMINLOC1',         '257' );
define( 'IRC_RPL_ADMINLOC2',         '258' );
define( 'IRC_RPL_ADMINEMAIL',        '259' );
define( 'IRC_RPL_TRACELOG',          '261' );
define( 'IRC_RPL_TRACEEND',          '262' );
define( 'IRC_RPL_TRYAGAIN',          '263' );
define( 'IRC_RPL_AWAY',              '301' );
define( 'IRC_RPL_USERHOST',          '302' );
define( 'IRC_RPL_ISON',              '303' );
define( 'IRC_RPL_UNAWAY',            '305' );
define( 'IRC_RPL_NOWAWAY',           '306' );
define( 'IRC_RPL_WHOISUSER',         '311' );
define( 'IRC_RPL_WHOISSERVER',       '312' );
define( 'IRC_RPL_WHOISOPERATOR',     '313' );
define( 'IRC_RPL_WHOWASUSER',        '314' );
define( 'IRC_RPL_ENDOFWHO',          '315' );
define( 'IRC_RPL_WHOISIDLE',         '317' );
define( 'IRC_RPL_ENDOFWHOIS',        '318' );
define( 'IRC_RPL_WHOISCHANNELS',     '319' );
define( 'IRC_RPL_LISTSTART',         '321' );
define( 'IRC_RPL_LIST',              '322' );
define( 'IRC_RPL_LISTEND',           '323' );
define( 'IRC_RPL_CHANNELMODEIS',     '324' );
define( 'IRC_RPL_UNIQOPIS',          '325' );
define( 'IRC_RPL_NOTOPIC',           '331' );
define( 'IRC_RPL_TOPIC',             '332' );
define( 'IRC_RPL_INVITING',          '341' );
define( 'IRC_RPL_SUMMONING',         '342' );
define( 'IRC_RPL_INVITELIST',        '346' );
define( 'IRC_RPL_ENDOFINVITELIST',   '347' );
define( 'IRC_RPL_EXCEPTLIST',        '348' );
define( 'IRC_RPL_ENDOFEXCEPTLIST',   '349' );
define( 'IRC_RPL_VERSION',           '351' );
define( 'IRC_RPL_WHOREPLY',          '352' );
define( 'IRC_RPL_NAMREPLY',          '353' );
define( 'IRC_RPL_LINKS',             '364' );
define( 'IRC_RPL_ENDOFLINKS',        '365' );
define( 'IRC_RPL_ENDOFNAMES',        '366' );
define( 'IRC_RPL_BANLIST',           '367' );
define( 'IRC_RPL_ENDOFBANLIST',      '368' );
define( 'IRC_RPL_ENDOFWHOWAS',       '369' );
define( 'IRC_RPL_INFO',              '371' );
define( 'IRC_RPL_MOTD',              '372' );
define( 'IRC_RPL_ENDOFINFO',         '374' );
define( 'IRC_RPL_MOTDSTART',         '375' );
define( 'IRC_RPL_ENDOFMOTD',         '376' );
define( 'IRC_RPL_YOUREOPER',         '381' );
define( 'IRC_RPL_REHASHING',         '382' );
define( 'IRC_RPL_YOURESERVICE',      '383' );
define( 'IRC_RPL_TIME',              '391' );
define( 'IRC_RPL_USERSSTART',        '392' );
define( 'IRC_RPL_USERS',             '393' );
define( 'IRC_RPL_ENDOFUSERS',        '394' );
define( 'IRC_RPL_NOUSERS',           '395' );
define( 'IRC_ERR_NOSUCHNICK',        '401' );
define( 'IRC_ERR_NOSUCHSERVER',      '402' );
define( 'IRC_ERR_NOSUCHCHANNEL',     '403' );
define( 'IRC_ERR_CANNOTSENDTOCHAN',  '404' );
define( 'IRC_ERR_TOOMANYCHANNELS',   '405' );
define( 'IRC_ERR_WASNOSUCHNICK',     '406' );
define( 'IRC_ERR_TOOMANYTARGETS',    '407' );
define( 'IRC_ERR_NOSUCHSERVICE',     '408' );
define( 'IRC_ERR_NOORIGIN',          '409' );
define( 'IRC_ERR_NORECIPIENT',       '411' );
define( 'IRC_ERR_NOTEXTTOSEND',      '412' );
define( 'IRC_ERR_NOTOPLEVEL',        '413' );
define( 'IRC_ERR_WILDTOPLEVEL',      '414' );
define( 'IRC_ERR_BADMASK',           '415' );
define( 'IRC_ERR_UNKNOWNCOMMAND',    '421' );
define( 'IRC_ERR_NOMOTD',            '422' );
define( 'IRC_ERR_NOADMININFO',       '423' );
define( 'IRC_ERR_FILEERROR',         '424' );
define( 'IRC_ERR_NONICKNAMEGIVEN',   '431' );
define( 'IRC_ERR_ERRONEUSNICKNAME',  '432' );
define( 'IRC_ERR_NICKNAMEINUSE',     '433' );
define( 'IRC_ERR_NICKCOLLISION',     '436' );
define( 'IRC_ERR_UNAVAILRESOURCE',	 '437' );
define( 'IRC_ERR_USERNOTINCHANNEL',	 '441' );
define( 'IRC_ERR_NOTONCHANNEL',      '442' );
define( 'IRC_ERR_USERONCHANNEL',     '443' );
define( 'IRC_ERR_NOLOGIN',           '444' );
define( 'IRC_ERR_SUMMONDISABLED',    '445' );
define( 'IRC_ERR_USERSDISABLED',     '446' );
define( 'IRC_ERR_NOTREGISTERED',     '451' );
define( 'IRC_ERR_NEEDMOREPARAMS',    '461' );
define( 'IRC_ERR_ALREADYREGISTRED',	 '462' );
define( 'IRC_ERR_NOPERMFORHOST',     '463' );
define( 'IRC_ERR_PASSWDMISMATCH',    '464' );
define( 'IRC_ERR_YOUREBANNEDCREEP',	 '465' );
define( 'IRC_ERR_YOUWILLBEBANNED',	 '466' );
define( 'IRC_ERR_KEYSET',            '467' );
define( 'IRC_ERR_CHANNELISFULL',     '471' );
define( 'IRC_ERR_UNKNOWNMODE',       '472' );
define( 'IRC_ERR_INVITEONLYCHAN',    '473' );
define( 'IRC_ERR_BANNEDFROMCHAN',    '474' );
define( 'IRC_ERR_BADCHANNELKEY',     '475' );
define( 'IRC_ERR_BADCHANMASK',       '476' );
define( 'IRC_ERR_NOCHANMODES',       '477' );
define( 'IRC_ERR_BANLISTFULL',       '478' );
define( 'IRC_ERR_NOPRIVILEGES',      '481' );
define( 'IRC_ERR_CHANOPRIVSNEEDED',  '482' );
define( 'IRC_ERR_CANTKILLSERVER',    '483' );
define( 'IRC_ERR_RESTRICTED',        '484' );
define( 'IRC_ERR_UNIQOPPRIVSNEEDED', '485' );
define( 'IRC_ERR_NOOPERHOST',        '491' );
define( 'IRC_ERR_UMODEUNKNOWNFLAG',  '501' );
define( 'IRC_ERR_USERSDONTMATCH',    '502' );

define( 'IRC_PSIC_VERSION',        '0.4.0' );
define( 'IRC_PSIC_VERSIONSTRING',  'Abstractpage IRC ' . IRC_PSIC_VERSION );


/**
 * @package peer_irc_lib
 */
 
class IRC extends PEAR
{
	/**
	 * @access private
	 */
	var $_socket;
	
	/**
	 * @access private
	 */
	var $_address;
	
	/**
	 * @access private
	 */
	var $_port;
	
	/**
	 * @access private
	 */
	var $_nick;
	
	/**
	 * @access private
	 */
	var $_username;
	
	/**
	 * @access private
	 */
	var $_realname;
	
	/**
	 * @access private
	 */
	var $_benchmark_starttime;
	
	/**
	 * @access private
	 */
	var $_benchmark_endtime;
	
	/**
	 * @access private
	 */
	var $_logfilefp;
	
	/**
	 * @access private
	 */
	var $_lastmicrotimestamp;
	
	/**
	 * @access private
	 */
	var $_state = false;
	
	/**
	 * @access private
	 */
	var $_actionhandler = array();
	
	/**
	 * @access private
	 */
	var $_timehandler = array();
	
	/**
	 * @access private
	 */
	var $_debug = IRC_DEBUG_NOTICE;
	
	/**
	 * @access private
	 */
	var $_messagebuffer = array();
	
	/**
	 * @access private
	 */
	var $_usesockets = false;
	
	/**
	 * @access private
	 */
	var $_receivedelay = 100;
	
	/**
	 * @access private
	 */
	var $_senddelay = 250;
	
	/**
	 * @access private
	 */
	var $_logdestination = IRC_STDOUT;
	
	/**
	 * @access private
	 */
	var $_logfile = "irc.log";
	
	/**
	 * @access private
	 */
	var $_disconnecttime = 1000;
	
	/**
	 * @access private
	 */
	var $_loggedin = false;
	
	/**
	 * @access private
	 */
	var $_benchmark = false;
	
	/**
	 * @access private
	 */
	var $_lastactionhandlerid = 0;
	
	/**
	 * @access private
	 */
	var $_lasttimehandlerid = 0;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function IRC()
	{
		ob_implicit_flush();
		set_time_limit( 0 );
		ignore_user_abort( true );
		$this->_lastmicrotimestamp = $this->_microint();
	}
	
	
	/**
	 * Enables/disables the usage of real sockets.
	 *
	 * @return void
	 * @param  boolean bool
	 * @access public
	 */
	function usesockets( $boolean )
	{
		if ( $boolean == true )
		{
			if ( Util::extensionExists( 'sockets' ) )
			{
				$this->_usesockets = true;
			}
			else
			{
				$this->log( IRC_DEBUG_NOTICE, 'WARNING: your PHP build does not support real sockets, will use fsocks.' );
				$this->_usesockets = false;
			}
		}
		else
		{
			$this->_usesockets = false;
		}
	}

	/**
	 * Sets the level of debug messages.
	 *
	 * @return void
	 * @param  level int
	 * @access public
	 */
	function debug( $level )
	{
		$this->_debug = $level;
	}

	/**
	 * Enables/disables benchmark test.
	 *
	 * @return void
	 * @param  boolean bool
	 * @access public
	 */
	function benchmark( $boolean )
	{
		if ( is_bool( $boolean ) )
			$this->_benchmark = $boolean;
		else 
			$this->_benchmark = false;
	}

	/**
	 * Starts the benchmark.
	 *
	 * @return void
	 * @access public
	 */
	function benchmarkstart()
	{
		$this->_benchmark_starttime = $this->_microint();
	}

	/**
	 * Ends the benchmark.
	 *
	 * @return void
	 * @access public
	 */
	function benchmarkend()
	{
		$this->_benchmark_endtime = $this->_microint();
		
		if ( $this->_benchmark )
			$this->show_benchmark();
	}

	/**
	 * Enables/disables benchmark test.
	 *
	 * @return void
	 * @param  boolean bool
	 * @access public
	 */
	function show_benchmark()
	{
		$this->log( IRC_DEBUG_NOTICE, 'benchmark time: ' . ( (float)$this->_benchmark_endtime - (float)$this->_benchmark_starttime ) );
	}

	/**
	 * Adds an entry to the log.
	 *
	 * @return void
	 * @param  level int
	 * @param  entry string
	 * @access public
	 */
	function log( $level, $entry )
	{
		if ( !( $level & $this->_debug ) )
			return;
		
		if ( substr( $entry, -1 ) != "\n" )
			$entry .= "\n";

		$formatedentry = date( 'M d H:i:s ' ) . $entry;
			
		switch ( $this->_logdestination )
		{
			case IRC_STDOUT:
				echo( $formatedentry );
				flush();
				
				break;

			case IRC_SYSLOG:
				define_syslog_variables();
			
				if ( !is_int( $this->_logfilefp ) )
					$this->_logfilefp = openlog( 'IRC', LOG_NDELAY, LOG_DAEMON );
				
				syslog( LOG_INFO, $entry );
			
				break;
		}
	}
	
	/**
	 * Sets the destination of all log messages.
	 *
	 * @return void
	 * @param  type constant
	 * @access public
	 */
	function setlogdestination( $type )
	{
		switch($type)
		{
			case IRC_STDOUT:
				$this->_logdestination = IRC_STDOUT;
				break;
			
			case IRC_SYSLOG:
				$this->_logdestination = IRC_SYSLOG;
				break;
			
			default:
				$this->log( IRC_DEBUG_NOTICE, 'WARNING: unknown logdestination type (' . $type . '), will use IRC_STDOUT instead' );
				$this->_logdestination = IRC_STDOUT;
		}
	}
	
	/**
	 * Sets the file for the log if the destination is set to file.
	 *
	 * @return void
	 * @param  file string
	 * @access public
	 */
	function setlogfile( $file )
	{
		$this->_logfile = $file;
	}

	/**
	 * Sets the delaytime before closing the socket when disconnect.
	 *
	 * @return void
	 * @param  milliseconds int
	 * @access public
	 */
	function disconnecttime( $milliseconds )
	{
		if ( is_integer( $milliseconds ) && $milliseconds >= 100 )
			$this->_disconnecttime = $milliseconds;
		else
			$this->_disconnecttime = 100;
	}

	/**
	 * Sets the delay for receiving data from the IRC server.
	 *
	 * @return void
	 * @param  milliseconds int
	 * @access public
	 */
	function receivedelay( $milliseconds )
	{
		if ( is_integer( $milliseconds ) && $milliseconds >= 100 )
			$this->_receivedelay = $milliseconds;
		else
			$this->_receivedelay = 100;
	}

	/**
	 * Sets the delay for sending data to the IRC server.
	 *
	 * @return void
	 * @param  milliseconds int
	 * @access public
	 */
	function senddelay( $milliseconds )
	{
		if ( is_integer( $milliseconds ) )
			$this->_senddelay = $milliseconds;
		else
			$this->_senddelay = 250;
	}
	
	/**
	 * Creates the sockets and connects to the IRC server.
	 *
	 * @return void
	 * @param  address string
	 * @param  port int
	 * @access public
	 */
	function connect( $address, $port )
	{
		$this->log( IRC_DEBUG_CONNECTION, 'IRC_DEBUG_CONNECTION: connecting' );
		
		$this->_address = $address;
		$this->_port    = $port;
		
		if ( $this->_usesockets == true )
		{
			$this->log( IRC_DEBUG_SOCKET, 'IRC_DEBUG_SOCKET: using real sockets' );
			$this->_socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
			$result = @socket_connect( $this->_socket, $this->_address, $this->_port );
		}
		else
		{
			$this->log( IRC_DEBUG_SOCKET, 'IRC_DEBUG_SOCKET: using fsockets' );
			$this->_socket = @fsockopen( $this->_address, $this->_port );
			$this->log( IRC_DEBUG_SOCKET, 'IRC_DEBUG_SOCKET: nonblocking socket mode' );
			socket_set_blocking( $this->_socket, false );
		}

		$this->_updatestate();
	}
	
	/**
	 * Creates the sockets and connects to the IRC server.
	 *
	 * @return void
	 * @param  nick string
	 * @param  realname string
	 * @param  username string
	 * @param  password string
	 * @param  realname string
	 * @access public
	 */
	function login( $nick, $realname, $username = null, $password = null )
	{
		$this->log( IRC_DEBUG_CONNECTION, 'IRC_DEBUG_CONNECTION: logging in' );

		$this->_nick     = str_replace( ' ', '', $nick );
		$this->_realname = $realname;
		
		if ( $username != null )
			$this->_username = str_replace( ' ', '', $username );
		else
			$this->_username = str_replace( ' ', '', exec( 'whoami' ) );
			
		if ( $password != null )
			$this->_rawsend( 'PASS ' . $password );

		$mode = '0';

		$this->_rawsend( 'NICK ' . $this->_nick );
		$this->_rawsend( 'USER ' . $this->_username . ' ' . $mode . ' ' . IRC_UNUSED . ' :' . $this->_realname );
	}
	
	/**
	 * Joins an IRC channel.
	 *
	 * @return void
	 * @param  channel string
	 * @param  key string
	 * @access public
	 */
	function join( $channel, $key = null )
	{
		if ( $key != null )
			$this->_bufferedsend( 'JOIN ' . $channel . ' ' . $key );
		else
			$this->_bufferedsend( 'JOIN ' . $channel );
	}

	/**
	 * Parts one or more IRC channels.
	 *
	 * @return void
	 * @param channelarray mixed
	 * @param reason string
	 * @access public
	 */
	function part( $channelarray, $reason = null)
	{
		if ( !is_array( $channelarray ) )
			$channelarray = array( $channelarray );

		$channellist = implode( $channelarray, ',' );
		
		if ( $reason != null )
			$this->_bufferedsend( 'PART ' . $channellist . ' :' . $reason );
		else
			$this->_bufferedsend( 'PART ' . $channellist );
	}

	/**
	 * Kicks a user from a IRC channel.
	 *
	 * @return void
	 * @param  channel string
	 * @param  nickname string
	 * @param  reason string
	 * @access public
	 */
	function kick( $channel, $nickname, $reason = null )
	{
		if ( $reason != null )
			$this->_rawsend( 'KICK ' . $channel . ' ' . $nickname . ' :' . $reason );
		else
			$this->_rawsend( 'KICK ' . $channel . ' ' . $nickname );
	}

	/**
	 * Gets a list of one ore more channels.
	 *
	 * @return void
	 * @param  channelarray mixed
	 * @access public
	 */
	function getlist( $channelarray = null )
	{
		if ( $channelarray != null )
		{
			if ( !is_array( $channelarray ) )
				$channelarray = array( $channelarray );
			
			$channellist = implode( $channelarray, ',' );
			$this->_bufferedsend( 'LIST ' . $channellist );
		}
		else
		{
			$this->_bufferedsend( 'LIST' );
		}
	}

	/**
	 * Gets all nicknames of one or more channels.
	 *
	 * @return void
	 * @param  channelarray mixed
	 * @access public
	 */
	function names( $channelarray = null )
	{
		if ( $channelarray != null )
		{
			if ( !is_array( $channelarray ) )
				$channelarray = array( $channelarray );

			$channellist = implode( $channelarray, ',' );
			$this->_bufferedsend( 'NAMES ' . $channellist );
		}
		else
		{
			$this->_bufferedsend( 'NAMES' );
		}
	}

	/**
	 * Sets a new topic of a channel.
	 *
	 * @return void
	 * @param  channel string
	 * @param  newtopic string
	 * @access public
	 */
	function settopic( $channel, $newtopic )
	{
		$this->_bufferedsend( 'TOPIC ' . $channel . ' :' . $newtopic );
	}

	/**
	 * Gets the topic of a channel.
	 *
	 * @return void
	 * @param  channel string
	 * @access public
	 */
	function gettopic( $channel )
	{
		$this->_bufferedsend( 'TOPIC ' . $channel );
	}

	/**
	 * Sets or gets the mode of an user or channel.
	 *
	 * @return void
	 * @param  target string
	 * @param  newmode string
	 * @access public
	 */
	function mode( $target, $newmode = null )
	{
		if ( $newmode != null )
			$this->_bufferedsend( 'MODE ' . $target . ' ' . $newmode );
		else 
			$this->_bufferedsend( 'MODE ' . $target );
	}

	/**
	 * Ops an user in the given channel.
	 *
	 * @return void
	 * @param  channel string
	 * @param  nickname string
	 * @access public
	 */
	function op( $channel, $nickname )
	{
		$this->mode( $channel, '+o ' . $nickname );
	}

	/**
	 * Deops an user in the given channel.
	 *
	 * @return void
	 * @param  channel string
	 * @param  nickname string
	 * @access public
	 */
	function deop( $channel, $nickname )
	{
		$this->mode( $channel, '-o ' . $nickname );
	}

	/**
	 * Bans a hostmask for the given channel or shows the current banlist.
	 *
	 * @return void
	 * @param  channel string
	 * @param  nickname string
	 * @access public
	 */
	function ban( $channel, $hostmask = null )
	{
		if ( $hostmask != null )
			$this->mode( $channel, '+b ' . $hostmask );
		else
			$this->mode( $channel, 'b' );
	}

	/**
	 * Unbans a hostmask for the given channel.
	 *
	 * @return void
	 * @param  channel string
	 * @param  nickname string
	 * @access public
	 */
	function unban( $channel, $hostmask )
	{
		$this->mode( $channel, '-b ' . $hostmask );
	}

	/**
	 * Invites a user to a channel.
	 *
	 * @return void
	 * @param  nickname string
	 * @param  channel string
	 * @access public
	 */
	function invite( $nickname, $channel )
	{
		$this->_bufferedsend( 'INVITE ' . $nickname . ' ' . $channel );
	}

	/**
	 * Changes the own nickname.
	 *
	 * @return void
	 * @param  newnick string
	 * @access public
	 */
	function changenick( $newnick )
	{
		$this->_bufferedsend( 'NICK ' . $newnick );
	}
	
	/**
	 * Goes into receive mode.
	 *
	 * @return void
	 * @access public
	 */
	function listen()
	{
		if ( $this->_state() == IRC_STATE_CONNECTED )
		{
			$this->_rawreceive();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Waits for a special message type and puts the answer it in $result.
	 *
	 * @return void
	 * @param  messagetype constant
	 * @param  result array
	 * @access public
	 */
	function listen_for( $messagetype, &$result )
	{
		$listenfor = &new IRCListenFor( $this );
		$this->register_actionhandler( $messagetype, '.*', $listenfor, 'handler' );
		$this->listen();
		$result = $listenfor->result;
		unset( $listenfor );
	}
	
	/**
	 * Sends a new message.
	 *
	 * @return bool
	 * @param  type constant
	 * @param  destination string
	 * @param  message string
	 * @access public
	 */
	function message( $type, $destination, $message )
	{
		switch ( $type )
		{
			case IRC_TYPE_CHANNEL:
			
			case IRC_TYPE_QUERY:
				$this->_bufferedsend( 'PRIVMSG ' . $destination . ' :' . $message );
				break;
			
			case IRC_TYPE_ACTION:
				$this->_bufferedsend( 'PRIVMSG ' . $destination . ' :' . chr( 1 ) . 'ACTION ' . $message );
				break;
			
			case IRC_TYPE_NOTICE:
				$this->_bufferedsend( 'NOTICE ' . $destination . ' :' . $message );
				break;
			
			case IRC_TYPE_CTCP:
				$this->_bufferedsend( 'NOTICE ' . $destination . ' :' . chr( 1 ) . $message . chr( 1 ) );
				break;
			
			default:
				return false;
		}
			
		return true;
	}

	/**
	 * Registers a new actionhandler and returns the assigned id.
	 *
	 * @return integer
	 * @param  handlertype constant
	 * @param  regexhandler string
	 * @param  object object
	 * @param  methodname string
	 * @access public
	 */
	function register_actionhandler( $handlertype, $regexhandler, &$object, $methodname )
	{
		$id = $this->_lastactionhandlerid++;
		$newactionhandler = &new IRCActionHandler;
		$newactionhandler->id      = $id;
		$newactionhandler->type    = $handlertype;
		$newactionhandler->message = $regexhandler;
		$newactionhandler->object  = &$object;
		$newactionhandler->method  = $methodname;
		
		array_push( $this->_actionhandler, $newactionhandler );
		$this->log( IRC_DEBUG_ACTIONHANDLER, 'IRC_DEBUG_ACTIONHANDLER: actionhandler(' . $id . ') registered' );
		
		return $id;
	}

	/**
	 * Unregisters an existing actionhandler.
	 *
	 * @return void
	 * @param  handlertype constant
	 * @param  regexhandler string
	 * @param  object object
	 * @param  methodname string
	 * @access public
	 */
	function unregister_actionhandler( $handlertype, $regexhandler, &$object, $methodname )
	{
		$handler = &$this->_actionhandler;
		
		for ( $j = 0; $j < count( $handler ); $j++ )
		{
			$handlerobject = &$handler[$j];
						
			if ( $handlerobject->type    == $handlertype  &&
			     $handlerobject->message == $regexhandler &&
			     $handlerobject->object  == $object       &&
			     $handlerobject->method  == $methodname )
			{
				$id = $handlerobject->id;
				unset( $this->_actionhandler[$j] );
				$this->log( IRC_DEBUG_ACTIONHANDLER, 'IRC_DEBUG_ACTIONHANDLER: actionhandler(' . $id . ') unregistered' );
				$this->_reorderactionhandler();

				return true;
			}
		}
		
		$this->log( IRC_DEBUG_ACTIONHANDLER, 'IRC_DEBUG_ACTIONHANDLER: could not find actionhandler type: "' . $handlertype . '" message: "' . $regexhandler . '" method: "' . $methodname . '" _not_ unregistered' );
		return false;
	}
	
	/**
	 * Unregisters an existing actionhandler via the id.
	 *
	 * @return bool
	 * @param  id integer
	 * @access public
	 */
	function unregister_actionid( $id )
	{
		$handler = &$this->_actionhandler;
		
		for ( $j = 0; $j < count( $handler ); $j++ )
		{
			$handlerobject = &$handler[$j];
						
			if ( $handlerobject->id == $id )
			{
				unset( $this->_actionhandler[$j] );
				$this->log( IRC_DEBUG_ACTIONHANDLER, 'IRC_DEBUG_ACTIONHANDLER: actionhandler(' . $id . ') unregistered' );
				$this->_reorderactionhandler();

				return true;
			}
		}
		
		$this->log( IRC_DEBUG_ACTIONHANDLER, 'IRC_DEBUG_ACTIONHANDLER: could not find actionhandler id: ' . $id . ' _not_ unregistered' );
		return false;
	}

	/**
	 * Registers a timehandler and returns the assigned id.
	 *
	 * @return integer
	 * @param  interval integer
	 * @param  object object
	 * @param  methodname string
	 * @access public
	 */
	function register_timehandler( $interval, &$object, $methodname )
	{
		$id = $this->_lasttimehandlerid++;
		$newtimehandler = &new IRCTimeHandler;
		$newtimehandler->id       = $id;
		$newtimehandler->interval = $interval;
		$newtimehandler->object   = &$object;
		$newtimehandler->method   = $methodname;
		
		array_push( $this->_timehandler, $newtimehandler );
		$this->log( IRC_DEBUG_TIMEHANDLER, 'IRC_DEBUG_TIMEHANDLER: timehandler(' . $id . ') registered' );

		return $id;
	}

	/**
	 * Unregisters an existing timehandler via the id.
	 *
	 * @return bool
	 * @param  id integer
	 * @access public
	 */
	function unregister_timeid( $id )
	{
		$handler = &$this->_timehandler;
		
		for ( $j = 0; $j < count( $handler ); $j++ )
		{
			$handlerobject = &$handler[$j];
						
			if ( $handlerobject->id == $id )
			{
				unset( $this->_timehandler[$j] );
				$this->log( IRC_DEBUG_TIMEHANDLER, 'IRC_DEBUG_TIMEHANDLER: timehandler(' . $id . ') unregistered' );
				$this->_reordertimehandler();

				return true;
			}
		}
		
		$this->log( IRC_DEBUG_TIMEHANDLER, 'IRC_DEBUG_TIMEHANDLER: could not find timehandler id: ' . $id . ' _not_ unregistered' );
		return false;
	}
	
	/**
	 * Requests a WHO from the specified target.
	 *
	 * @return void
	 * @param  target string
	 * @access public
	 */
	function who( $target )
	{
		$this->_bufferedsend( 'WHO ' . $target );
	}

	/**
	 * Requests a WHOIS from the specified target.
	 *
	 * @return void
	 * @param  target string
	 * @access public
	 */
	function whois( $target )
	{
		$this->_bufferedsend( 'WHOIS ' . $target );
	}

	/**
	 * Requests a WHOWAS from the specified target.
	 *
	 * @return void
	 * @param  target string
	 * @access public
	 */
	function whowas( $target )
	{
		$this->_bufferedsend( 'WHOWAS ' . $target );
	}
	
	/**
	 * Sends QUIT to IRC server and disconnects.
	 *
	 * @return void
	 * @param  quitmessage string
	 * @access public
	 */
	function quit( $quitmessage = null )
	{
		if ( $quitmessage != null )
			$this->_bufferedsend( 'QUIT :' . $quitmessage );
		else
			$this->_bufferedsend( 'QUIT' );
			
		$this->disconnect( true );
	}
	
	/**
	 * Disconnects from the IRC server nicely with a QUIT or just destroys the socket.
	 *
	 * @return bool
	 * @param  quickdisconnect bool
	 * @access public
	 */
	function disconnect( $quickdisconnect = false )
	{
		if ( $this->_state() == IRC_STATE_CONNECTED )
		{
			if ( $quickdisconnect == false )
			{
				$this->_rawsend( 'QUIT' );
				usleep( $this->_disconnecttime * 1000 );
			}
			
			if ( $this->_usesockets == true )	
			{
				@socket_shutdown( $this->_socket );
				socket_close( $this->_socket );
			}
			else
			{
				fclose( $this->_socket );
			}

			$this->_updatestate();			
			$this->log( IRC_DEBUG_CONNECTION, 'IRC_DEBUG_CONNECTION: disconnected' );
				
			return true;
		}
		else
		{
			return false;
		}

		if ( $this->_logdestination == IRC_SYSLOG )
			closelog();
	}
	
	
	// private methods
	
	/**
	 * Getting current microtime, needed for benchmarks.
	 *
	 * @return float
	 * @access private
	 */
	function _microint()
	{
		$tmp       = microtime();
		$parts     = explode( " ", $tmp );
		$floattime = (float)$parts[0] + (float)$parts[1];

		return $floattime;
	}
	
	/**
	 * Returns the current connection state.
	 *
	 * @return constant
	 * @access private
	 */
	function _state()
	{
		$result = $this->_updatestate();
		
		if ( $result == true )
			return IRC_STATE_CONNECTED;
		else
			return IRC_STATE_DISCONNECTED;
	}
	
	/**
	 * Updates the current connection state.
	 *
	 * @return bool
	 * @access private
	 */
	function _updatestate()
	{
		$rtype = get_resource_type( $this->_socket );
		
		if ( is_resource( $this->_socket ) && $this->_socket !== false && ( $rtype == 'socket' || $rtype == 'Socket' || $rtype == 'stream' ) )
		{
			$this->_state = true;
			return true;
		}
		else
		{
			$this->_state = false;
			return false;
		}
	}
	
	/**
	 * Determines the messagetype of $line.
	 *
	 * @return constant
	 * @param  line string
	 * @access private
	 */
	function _gettype( $line )
	{
		if ( ereg( '^:.* [0-9]{3} .*$', $line ) == 1 )
		{
			$lineex = explode( ' ', $line );
			$code   = $lineex[1];
				
			switch ( $code )
			{
				case IRC_RPL_WELCOME:
					return IRC_TYPE_LOGIN;
				
				case IRC_RPL_YOURHOST:
					return IRC_TYPE_LOGIN;
				
				case IRC_RPL_CREATED:
					return IRC_TYPE_LOGIN;
				
				case IRC_RPL_MYINFO:
					return IRC_TYPE_LOGIN;
				
				case IRC_RPL_BOUNCE:
					return IRC_TYPE_LOGIN;
				
				case IRC_RPL_LUSERCLIENT:
					return IRC_TYPE_INFO;
				
				case IRC_RPL_LUSEROP:
					return IRC_TYPE_INFO;
				
				case IRC_RPL_LUSERUNKNOWN:
					return IRC_TYPE_INFO;
				
				case IRC_RPL_LUSERME:
					return IRC_TYPE_INFO;
				
				case IRC_RPL_LUSERCHANNELS:
					return IRC_TYPE_INFO;
				
				case IRC_RPL_MOTDSTART:
					return IRC_TYPE_MOTD;
				
				case IRC_RPL_MOTD:
					return IRC_TYPE_MOTD;
				
				case IRC_RPL_ENDOFMOTD:
					return IRC_TYPE_MOTD;
				
				case IRC_RPL_NAMREPLY:
					return IRC_TYPE_NAME;
				
				case IRC_RPL_ENDOFNAMES:
					return IRC_TYPE_NAME;
				
				case IRC_RPL_WHOREPLY:
					return IRC_TYPE_WHO;
				
				case IRC_RPL_ENDOFWHO:
					return IRC_TYPE_WHO;
				
				case IRC_RPL_LISTSTART:
					return IRC_TYPE_LIST;
				
				case IRC_RPL_LIST:
					return IRC_TYPE_LIST;
				
				case IRC_RPL_LISTEND:
					return IRC_TYPE_LIST;
				
				case IRC_RPL_BANLIST:
					return IRC_TYPE_BANLIST;
				
				case IRC_RPL_ENDOFBANLIST:
					return IRC_TYPE_BANLIST;

				case IRC_RPL_TOPIC:
					return IRC_TYPE_TOPIC;

				case IRC_ERR_NICKNAMEINUSE:
					return IRC_TYPE_ERROR;

				case IRC_ERR_NOTREGISTERED:
					return IRC_TYPE_ERROR;

				default:
					$this->log( IRC_DEBUG_IRCMESSAGES, 'IRC_DEBUG_IRCMESSAGES: replycode UNKNOWN (' . $code . '): "' . $line . '"' );
			}
		}

		if ( ereg( '^:.* PRIVMSG .* :' . chr( 1 ) . 'ACTION .*$', $line ) == 1 )
		{
			return IRC_TYPE_ACTION;
		}
		else if ( ereg( '^:.* PRIVMSG .* :' . chr( 1 ) . '.*' . chr( 1 ) . '$', $line ) == 1 )
		{
			return IRC_TYPE_CTCP;
		}
		else if ( ereg( '^:.* PRIVMSG (\&|\#|\+|\!).* :.*$', $line ) == 1 )
		{
			return IRC_TYPE_CHANNEL;
		}
		else if ( ereg( '^:.* PRIVMSG .*:.*$', $line ) == 1 )
		{
			return IRC_TYPE_QUERY;
		}
		else if ( ereg( '^:.* NOTICE .* :.*$', $line ) == 1 )
		{
			return IRC_TYPE_NOTICE;
		}
		else if ( ereg( '^:.* INVITE .* .*$', $line ) == 1 )
		{
			return IRC_TYPE_INVITE;
		}
		else if ( ereg( '^:.* JOIN .*$', $line ) == 1 )
		{
			return IRC_TYPE_JOIN;
		}
		else if ( ereg( '^:.* TOPIC .* :.*$', $line ) == 1 )
		{
			return IRC_TYPE_TOPICCHANGE;
		}
		else if ( ereg( '^:.* NICK .*$', $line ) == 1 )
		{
			return IRC_TYPE_NICKCHANGE;
		}
		else if ( ereg( '^:.* KICK .* .*$', $line ) == 1 )
		{
			return IRC_TYPE_KICK;
		}
		else if ( ereg( '^:.* PART .* :.*$', $line ) == 1 )
		{
			return IRC_TYPE_PART;
		}
		else if ( ereg( '^:.* MODE .* .*$', $line ) == 1 )
		{
			return IRC_TYPE_MODECHANGE;
		}
		else if ( ereg( '^:.* QUIT :.*$', $line ) == 1 )
		{
			return IRC_TYPE_QUIT;
		}
		else 
		{
			$this->log( IRC_DEBUG_MESSAGETYPES, 'IRC_DEBUG_MESSAGETYPES: IRC_TYPE_UNKNOWN!: "' . $line . '"' );		
 			return IRC_TYPE_UNKNOWN;
		}
	}
	
	/**
	 * Reorders the timehandler array, needed after removing one.
	 *
	 * @return void
	 * @access private
	 */
	function _reordertimehandler()
	{
		$orderedtimehandler = array();
		
		foreach ( $this->_timehandler as $value )
			array_push( $orderedtimehandler, $value );
		
		$this->_timehandler = $orderedtimehandler;
	}
	
	/**
	 * Reorders the actionhandler array, needed after removing one.
	 *
	 * @return void
	 * @access private
	 */
	function _reorderactionhandler()
	{
		$orderedactionhandler = array();
		
		foreach ( $this->_actionhandler as $value )
			array_push( $orderedactionhandler, $value );
		
		$this->_actionhandler = $orderedactionhandler;
	}
	
	/**
	 * Sends the pong for keeping alive.
	 *
	 * @return void
	 * @param  data string
	 * @access private
	 */
	function _pong( $data )
	{
		$this->log( IRC_DEBUG_CONNECTION, 'IRC_DEBUG_CONNECTION: Ping? Pong!' );	
		$this->_rawsend( 'PONG ' . $data );
	}
	
	/**
	 * Goes into main idle loop for waiting messages from the IRC server.
	 *
	 * @return void
	 * @access private
	 */
	function _rawreceive()
	{
		$lastpart  = '';
		$rawdataar = array();
		
		while ( $this->_state() == IRC_STATE_CONNECTED )
		{
			if ( $this->_usesockets == true )
			{
				$sread  = array( $this->_socket );
				$result = @socket_select( $sread, $w = null, $e = null,  0, $this->_receivedelay * 1000 );
				
				// the socket got data to read
				if ( $result == 1)
					$rawdata = @socket_read( $this->_socket, 10240 );
				// no data
				else
					$rawdata = null;
			}
			else
			{
				usleep( $this->_receivedelay * 1000 );
				$rawdata = @fread( $this->_socket, 10240 );
			}
			
			$this->_checkbuffer();
			$this->_checktimer();
			
			if ( $rawdata != null )
			{
				$rawdata   = str_replace( "\r", '', $rawdata );
				$rawdata   = $lastpart . $rawdata;
				
				$lastpart  = substr( $rawdata, strrpos( $rawdata, "\n" ) + 1 );
				$rawdata   = substr( $rawdata, 0, strrpos( $rawdata, "\n" ) );
				$rawdataar = explode( "\n", $rawdata );   
			}
			
			for ( $i= 0; $i < sizeof( $rawdataar ); $i++ )
			{
				$rawline = array_shift( $rawdataar );
				$this->log( IRC_DEBUG_IRCMESSAGES, 'IRC_DEBUG_IRCMESSAGES: received: "' . $rawline . '"' );
					
				if ( substr( $rawline, 0, 4 ) == 'PING' )
					$this->_pong( substr( $rawline, 5 ) );
				else if ( substr( $rawline, 0, 5 ) == 'ERROR' )
					$this->disconnect( true );

				if ( substr( $rawline, 0, 1 ) == ':' )
				{
					$line    = substr( $rawline, 1 );
					$lineex  = explode( ' ', $line );
					$from    = $lineex[0];
					$nick    = substr( $lineex[0], 0, strpos( $lineex[0], '!' ) );
					$message = substr( implode( array_slice( $lineex, 3 ), ' ' ), 1 );
					$type    = $this->_gettype( $rawline );
					
					switch ( $type )
					{
						case IRC_TYPE_CTCP:
							if ( substr( $message, 1, 4 ) == 'PING' )
								$this->message( IRC_TYPE_CTCP, $nick, 'PING ' . substr( $message, 5, -1 ) );
							else if ( substr( $message, 1, 7 ) == 'VERSION' )
								$this->message( IRC_TYPE_CTCP, $nick, 'VERSION ' . IRC_PSIC_VERSIONSTRING );
						
							break;
						
						case IRC_TYPE_LOGIN:
							if ( $lineex[1] == IRC_RPL_WELCOME )
							{
								$this->_loggedin = true;
								$this->log( IRC_DEBUG_CONNECTION, 'IRC_DEBUG_CONNECTION: logged in' );
							}
						
							break;
						
						case IRC_TYPE_ERROR:
							$code = $lineex[1];
						
							if ( $code == IRC_ERR_NICKNAMEINUSE )
								$this->_nicknameinuse();
						
							break;
					}
					
					$handler = &$this->_actionhandler;
					
					for ( $j = 0; $j < count( $handler ); $j++ )
					{
						$handlerobject = &$handler[$j];
						
						if ( ( $handlerobject->type & $type ) && ( ereg( $handlerobject->message, $message ) == 1 ) )
						{
							$this->log( IRC_DEBUG_ACTIONHANDLER, 'IRC_DEBUG_ACTIONHANDLER: actionhandler match found for id: ' . $j . ' type: ' . $type . ' message: "' . $message . '" regex: "' . $handlerobject->message . '"' );
							
							$ircdata = &new IRCData;
							$ircdata->nick       = $nick;
							$ircdata->from       = $from;
							$ircdata->message    = $message;
							$ircdata->type       = $type;
							$ircdata->rawmessage = $rawline;
							
							if ( $type == IRC_TYPE_CHANNEL|IRC_TYPE_ACTION )
								$ircdata->channel = $lineex[2];

							$methodobject = &$handlerobject->object;
							$method = $handlerobject->method;
					
							if ( method_exists( $methodobject, $method ))
							{
								$this->log( IRC_DEBUG_ACTIONHANDLER, 'IRC_DEBUG_ACTIONHANDLER: calling existing method "' . $method . '"' );
								$methodobject->$method( $this, $ircdata );
							}
									
							unset( $ircdata );
							break;
						}
					}
				}	
			}
		}
	}
	
	/**
	 * Checks the timer.
	 *
	 * @return void
	 * @access private
	 */
	function _checktimer()
	{
		if ( !$this->_loggedin )
			return;
			
		for ( $i = 0; $i < count( $this->_timehandler ); $i++ )
		{
			$handlerobject = &$this->_timehandler[$i];
			
			if ( $this->_microint() >= ( $handlerobject->lastmicrotimestamp + ( $handlerobject->interval / 1000 ) ) )
			{
				$methodobject = &$handlerobject->object;
				$method = $handlerobject->method;
					
				if ( method_exists( $methodobject, $method ))
				{
					$this->log( IRC_DEBUG_TIMEHANDLER, 'IRC_DEBUG_TIMEHANDLER: calling existing method "' . $method . '"' );
					$methodobject->$method( $this );
				}
				
				$handlerobject->lastmicrotimestamp = $this->_microint();
			}
		}
	}
	
	/**
	 * Adds a message to the messagebuffer.
	 *
	 * @return void
	 * @param data string
	 * @access private
	 */
	function _bufferedsend( $data )
	{
		array_push( $this->_messagebuffer, $data );
	}
	
	/**
	 * Checks the buffer if there are messages to send.
	 *
	 * @return void
	 * @access private
	 */
	function _checkbuffer()
	{
		if ( !$this->_loggedin )
			return;
			
		$messagecount = count( $this->_messagebuffer );
		
		if ( $this->_microint() >= ( $this->_lastmicrotimestamp + ( $this->_senddelay / 1000 ) ) && $messagecount > 0  )
		{
			$this->_rawsend( array_shift( $this->_messagebuffer ) );
			$this->_lastmicrotimestamp = $this->_microint();
		}
	}
	
	/**
	 * Sends a raw message to the IRC server.
	 *
	 * @return bool
	 * @param  data string
	 * @access private
	 */
	function _rawsend( $data )
	{
		if ( $this->_state() == IRC_STATE_CONNECTED )
		{
			$this->log( IRC_DEBUG_IRCMESSAGES, 'IRC_DEBUG_IRCMESSAGES: sent: "' . $data . '"' );
				
			if ( $this->_usesockets == true )
				$result = @socket_write( $this->_socket, $data . IRC_CRLF );
			else
				$result = @fwrite( $this->_socket, $data . IRC_CRLF );
			
			if ( $result == false )
				return false;
			else
				return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Changes a already used nickname to a new nickname plus 3 random digits.
	 *
	 * @return void
	 * @access private
	 */
	function _nicknameinuse()
	{
		$newnickname = substr( $this->_nick, 0, 5 ) . rand( 0, 999 );
		$this->_rawsend( 'NICK ' . $newnickname );
		$this->_nick = $newnickname;
	}
} // END OF IRC

?>
