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


/**
 * @package peer_http_agent
 */
 
class ClientSniffer extends PEAR
{
	/**
	 * $_temp_file_path
	 *		default : /tmp/
	 *      desc    : directory writable by the server to store cookie check files.
     *                trailing slash is needed. only used if you use the check cookie routine
	 *
	 * @access private
	 */
	var $_temp_file_path;

	/**	
     * $_check_cookies
     *      default : null
     *      desc    : Allow for the script to redirect the browser in order
     *                to check for cookies.   In order for this to work, this
     *                class must be instantiated before any headers are sent.
	 *
	 * @access private
	 */
	var $_check_cookies = null;

	/**
     * $_default_language
     *      default : en-us
     *      desc    : language to report as if no languages are found
	 *
	 * @access private
	 */
	var $_default_language = 'en-us';

	/**
     * $_allow_masquerading
     *      default : null
     *      desc    : Allow for browser to Masquerade as another.
     *                (ie: Opera identifies as MSIE 5.0)
	 *
	 * @access private
	 */
	var $_allow_masquerading = null;

	/**
     * $_browsers
     *      desc    : 2D Array of browsers we wish to search for
     *                in key => value pairs.
     *                key   = browser to search for [as in HTTP_USER_AGENT]
     *                value = value to return as 'browser' property
	 *
	 * @access private
	 */
    var $_browsers = array(
        'microsoft internet explorer' => 'ie',
        'msie'                        => 'ie',
        'netscape6'                   => 'ns',
        'netscape'                    => 'ns',
        'mozilla'                     => 'moz',
        'opera'                       => 'op',
        'konqueror'                   => 'konq',
        'icab'                        => 'icab',
        'lynx'                        => 'lynx',
		'links'                       => 'links',					
        'ncsa mosaic'                 => 'mosaic',
        'amaya'                       => 'amaya',
        'omniweb'                     => 'ow',
		'hotjava'					  => 'hj'
	);

	/**
     * $_javascript_versions
     *      desc    : 2D Array of javascript version supported by which browser
     *                in key => value pairs.
     *                key   = javascript version
     *                value = search parameter for browsers that support the
     *                        javascript version listed in the key (comma delimited)
     *                        note: the search parameters rely on the values
     *                              set in the $_browsers array
	 *
	 * @access private
	 */
    var $_javascript_versions = array(
        '1.5' => 'IE5.5UP,NS5UP',
        '1.4' => '',
        '1.3' => 'NS4.05UP,OP5UP,IE5UP',
        '1.2' => 'NS4UP,IE4UP',
        '1.1' => 'NS3UP,OP,KQ',
        '1.0' => 'NS2UP,IE3UP',
		'0'   => 'LN,LX,HJ'	
	);
		
	/**
	 * $_browser_features
     *      desc    : 2D Array of browser features supported by which browser
     *                in key => value pairs.
     *                key   = feature
     *                value = search parameter for browsers that support the
     *                        feature listed in the key (comma delimited)
     *                        note: the search parameters rely on the values
     *                              set in the $_browsers array
	 *
	 * @access private
	 */
	var $_browser_features = array(
		/*
	 	 * the following are true by default
	 	 * browsers listed here will be set to false
	 	 */
		'html'    => '',
		'images'  => 'LN,LX',
		'frames'  => 'LN,LX',
		'tables'  => '',
		'java'    => 'OP3,LX,LN,NS1,MO,IE1,IE2',
		'plugins' => 'IE1,IE2,LX,LN',
		
		/*
		 * the following are false by default
		 * browsers listed here will be set to true
		 */
		'css2'    => 'NS5UP,IE5UP',
		'css1'    => 'NS4UP,IE4UP',
		'iframes' => 'IE3UP,NS5UP',
		'xml'     => 'IE5UP,NS5UP',
		'dom'     => 'IE5UP,NS5UP',
		'hdml'    => '',
		'wml'     => ''
	);

	/**
	 * $_browser_quirks
     *      desc    : 2D Array of browser quirks present in which browser
     *                in key => value pairs.
     *                key   = quirk
     *                value = search parameter for browsers that feature the
     *                        quirk listed in the key (comma delimited)
     *                        note: the search parameters rely on the values
     *                              set in the $_browsers array
	 *
	 * @access private
	 */
	var $_browser_quirks = array(
		'must_cache_forms'			=> 'NS',
		'avoid_popup_windows'		=> 'IE3,LX,LN',
		'cache_ssl_downloads'		=> 'IE',
		'break_disposition_header'	=> 'IE5.5',
		'empty_file_input_value'	=> 'KQ',
		'scrollbar_in_way'			=> 'IE6'
	);

	/**
	 * @access private
	 */
	var $_browser_info = array(
    	'ua'         => '',
    	'browser'    => 'Unknown',
    	'version'    => 0,
    	'maj_ver'    => 0,
    	'min_ver'    => 0,
    	'letter_ver' => '',
    	'javascript' => '0.0',
    	'platform'   => 'Unknown',
    	'os'         => 'Unknown',
    	'ip'         => 'Unknown',
        'cookies'    => 'Unknown', // remains for backwards compatability
    	'ss_cookies' => 'Unknown',
        'st_cookies' => 'Unknown',
    	'language'   => '',
		'long_name'  => '',
		'gecko'      => '',
        'gecko_ver'  => ''
	);
	
	/**
	 * @access private
	 */
	var $_feature_set = array(
		'html'		 =>	true,
		'images'	 =>	true,
		'frames' 	 =>	true,
		'tables'	 =>	true,
		'java'		 =>	true,
		'plugins'	 => true,
		'iframes'	 => false,
		'css2'		 =>	false,
		'css1'		 =>	false,
		'xml'		 =>	false,
		'dom'		 =>	false,
		'wml'		 =>	false,
		'hdml'		 =>	false
	);

	/**
	 * @access private
	 */	
	var $_quirks = array(
		'must_cache_forms'			=>	false,
		'avoid_popup_windows'		=>	false,
		'cache_ssl_downloads'		=>	false,
		'break_disposition_header'	=>	false,
		'empty_file_input_value'	=>	false,
		'scrollbar_in_way'			=>	false
	);

	/**
	 * @access private
	 */
	var $_get_languages_ran_once = false;
	
	/**
	 * @access private
	 */
	var $_browser_search_regex = '([a-z]+)([0-9]*)([0-9.]*)(up|dn)?';

	/**
	 * @access private
	 */
	var $_language_search_regex = '([a-z-]{2,})';
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function ClientSniffer( $UA = '', $settings = true )
    {
		$this->_temp_file_path = ap_ini_get( "path_tmp_os", "path" );
		
        if ( is_array( $settings ) )
		{
            $run = true;
            extract( $settings );
			
            $this->_check_cookies      = $check_cookies;
            $this->_default_language   = $default_language;
            $this->_allow_masquerading = $allow_masquerading;
        }
		else
		{
            // for backwards compatibility with 2.0.x series of this script
            $run = $settings;
        }
        
        if ( empty( $UA ) )
			$UA = $_SERVER["HTTP_USER_AGENT"];
			
        if ( empty( $UA ) )
			$pv = explode( ".", PHP_VERSION );
		
		if ( empty( $UA ) )
			return false;
        
        $this->_setBrowser( 'ua', $UA );
        
		if ( $run )
			$this->init();
    }
	

	/**
	 * @access public
	 */
    function init()
    {   
		$this->_getIP();
		$this->_testCookies(); // method only runs if allowed
		$this->_getBrowserInfo();
		$this->_getLanguages();
		$this->_getOSInfo();
		$this->_getJavaScript();
		$this->_getFeatures();
		$this->_getQuirks();
		$this->_getGecko();
	}

    /**
     * @param  $p property to return . optional (null returns entire array)
     * @return array/string entire array or value of property
	 * @access public
     */
    function getProperty( $p = null )
    {
		if ( $p == null )
        	return $this->_browser_info;
        else
        	return $this->_browser_info[strtolower( $p )];
    }

    /**
     * @param   $s string search phrase format = l:lang;b:browser
     * @return  bool true on success
	 * @access  public
     */
    function is( $s )
    {
		// perform language search
		if ( preg_match( '/l:' . $this->_language_search_regex . '/i', $s, $match ) )
        {
			if ( $match )
				return $this->_performLanguageSearch( $match );
        }
        // perform browser search
        else if ( preg_match( '/b:' . $this->_browser_search_regex . '/i', $s, $match ) )
        {
			if ( $match )
				return $this->_performBrowserSearch( $match );
        }
		
        return false;
    }
	
	/**
	 * @param   $s string search phrase for browser
	 * @return  bool true on success
	 * @access  public
	 */
	function isBrowser( $s )
	{
		preg_match( '/' . $this->_browser_search_regex . '/i', $s, $match );
		
		if ( $match )
			return $this->_performBrowserSearch( $match );
	}
	
	/**
	 * @param   $s string search phrase for language
	 * @return  bool true on success
	 * @access  public
	 */
	function isLanguage( $s )
	{
		preg_match( '/' . $this->_language_search_regex . '/i', $s, $match );
		
		if ( $match )
			return $this->_performLanguageSearch( $match );
	}
	
	/**
	 * @param   $s string feature we're checking on
	 * @return  bool true on success
	 * @access  public
	 */
	function hasFeature( $s )
	{
		return $this->_feature_set[$s];
	}
	
	/**
	 * @param   $s string quirk we're looking for
	 * @return  bool true on success
	 * @access  public
	 */
	function hasQuirk( $s )
	{
		return $this->_quirks[$s];
	}


	// private methods
	
    /**
     * @param  $data string what we're searching for
     * @return bool true on success
     * @access private
     */
    function _performBrowserSearch( $data )
    {  
		$search = array();
		$search['phrase']    = isset( $data[0] )? $data[0] : '';
		$search['name']      = isset( $data[1] )? strtolower( $data[1] ) : '';
		$search['maj_ver']   = isset( $data[2] )? $data[2] : '';
		$search['min_ver']   = isset( $data[3] )? $data[3] : '';
		$search['direction'] = isset( $data[4] )? strtolower( $data[4] ) : '';
		
        $looking_for = $search['maj_ver'] . $search['min_ver'];
		
        if ( $search['name'] == 'aol' || $search['name'] == 'webtv' )
        {
			return stristr( $this->_browser_info['ua'], $search['name'] );
        }
        else if ( $this->_browser_info['browser'] == $search['name'] )
        {
			$majv = $search['maj_ver']? $this->_browser_info['maj_ver'] : '';
            $minv = $search['min_ver']? $this->_browser_info['min_ver'] : '';
            $what_we_are = $majv . $minv;
			
            if ( ( $search['direction'] == 'up' ) && ( $what_we_are >= $looking_for ) )
            	return true;
			else if ( ( $search['direction'] == 'dn' ) && ( $what_we_are <= $looking_for ) )
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
		// if we've not grabbed the languages, then do so.
        $this->_getLanguages();
        return stristr( $this->_browser_info['language'], $data[1] );
    }

	/**
	 * @access private
	 */
    function _getLanguages()
    {
		// capture available languages and insert into container
        if ( !$this->_get_languages_ran_once )
        {
			if ( $languages = $_SERVER["HTTP_ACCEPT_LANGUAGE"] )
            	$languages = preg_replace( '/(;q=[0-9]+.[0-9]+)/i','', $languages );
            else
            	$languages = $this->_default_language;
            
            $this->_setBrowser( 'language', $languages );
            $this->_get_languages_ran_once = true;
        }
    }

	/**
	 * @access private
	 */
    function _getOSInfo()
    {
		// regexes to use
        $regex_windows  = '/([^dar]win[dows]*)[\s]?([0-9a-z]*)[\w\s]?([a-z0-9.]*)/i';
        $regex_mac      = '/(68[k0]{1,3})|(ppc mac os x)|([p\S]{1,5}pc)|(darwin)/i';
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

        // look for Windows Box
        if ( preg_match_all( $regex_windows, $this->_browser_info['ua'], $match ) )
        {
			// Windows has some of the most ridiculous HTTP_USER_AGENT strings
			
			// $match[1][count($match[0])-1];
            $v  = $match[2][count( $match[0] ) - 1];
            $v2 = $match[3][count( $match[0] ) - 1];
			
            // Establish NT 5.1 as Windows XP
			if ( stristr( $v, 'NT' ) && ( $v2 == 5.1 ) )
				$v = 'xp';
			// Establish NT 5.0 and Windows 2000 as win2k
            else if ( $v == '2000' )
				$v = '2k';
            else if ( stristr( $v, 'NT' ) && ( $v2 == 5.0 ) )
				$v = '2k';
			// Establish 9x 4.90 as Windows 98
			else if ( stristr( $v, '9x' ) && ( $v2 == 4.9 ) )
				$v = '98';
            // See if we're running windows 3.1
            else if ( $v.$v2 == '16bit' )
				$v = '31';
            // otherwise display as is (31,95,98,NT,ME,XP)
            else
				$v .= $v2;
            
			// update browser info container array
            if ( empty( $v ) )
				$v = 'win';
            
			$this->_setBrowser( 'os', strtolower( $v ) );
            $this->_setBrowser( 'platform', 'win' );
        }
        // look for OS2
        else if ( preg_match( $regex_os2, $this->_browser_info['ua'] ) )
        {
			$this->_setBrowser( 'os', 'os2' );
            $this->_setBrowser( 'platform', 'os2' );
        }
        // look for mac
        // sets: platform = mac ; os = 68k or ppc
        else if ( preg_match( $regex_mac, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', 'mac' );
			
            $os = !empty( $match[1] )? '68k' : '';
            $os = !empty( $match[2] )? 'osx' : $os;
            $os = !empty( $match[3] )? 'ppc' : $os;
            $os = !empty( $match[4] )? 'osx' : $os;
   
            $this->_setBrowser( 'os', $os );
        }
        // look for *nix boxes
        // sunos sets: platform = *nix ; os = sun|sun4|sun5|suni86
        else if ( preg_match( $regex_sunos, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            
			if ( !stristr( 'sun', $match[1] ) )
				$match[1] = 'sun' . $match[1];
				
            $this->_setBrowser( 'os', $match[1] . $match[2] );
        }
        // irix sets: platform = *nix ; os = irix|irix5|irix6|...
        else if ( preg_match( $regex_irix, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', $match[1] . $match[2] );
        }
        // hp-ux sets: platform = *nix ; os = hpux9|hpux10|...
        else if ( preg_match( $regex_hpux, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $match[1] = str_replace( '-', '', $match[1] );
            $match[2] = (int)$match[2];
            $this->_setBrowser( 'os', $match[1] . $match[2] );
        }
        // aix sets: platform = *nix ; os = aix|aix1|aix2|aix3|...
        else if ( preg_match( $regex_aix, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'aix' . $match[1] );
        }
        // dec sets: platform = *nix ; os = dec
        else if ( preg_match( $regex_dec, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'dec' );
        }
        // vms sets: platform = *nix ; os = vms
        else if ( preg_match( $regex_vms, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'vms' );
        }
        // sco sets: platform = *nix ; os = sco
        else if ( preg_match( $regex_sco, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'sco' );
        }
        // unixware sets: platform = *nix ; os = unixware
        else if ( stristr( 'unix_system_v', $this->_browser_info['ua'] ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'unixware' );
        }
        // mpras sets: platform = *nix ; os = mpras
        else if ( stristr( 'ncr', $this->_browser_info['ua'] ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'mpras' );
        }
        // reliant sets: platform = *nix ; os = reliant
        else if(stristr( 'reliantunix', $this->_browser_info['ua'] ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'reliant' );
        }
        // sinix sets: platform = *nix ; os = sinix
        else if ( stristr( 'sinix', $this->_browser_info['ua'] ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'sinix' );
        }
        // bsd sets: platform = *nix ; os = bsd|freebsd
        else if ( preg_match( $regex_bsd, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', $match[1] . $match[2] );
        }
        // last one to look for
        // linux sets: platform = *nix ; os = linux
        else if ( preg_match( $regex_linux, $this->_browser_info['ua'], $match ) )
        {
			$this->_setBrowser( 'platform', '*nix' );
            $this->_setBrowser( 'os', 'linux' );
        }
    }

	/**
	 * @access private
	 */
    function _getBrowserInfo()
    {
		$this->_buildRegex();
        
		if ( preg_match_all( $this->_browser_regex, $this->_browser_info['ua'], $results ) )
        {
			// get the position of the last browser found
            $count = count( $results[0] ) - 1;
			
            // if we're allowing masquerading, revert to the next to last browser found
            // if possible, otherwise stay put
            if ( $this->_allow_masquerading && $count > 0 )
				$count--;
            
			// insert findings into the container
            $this->_setBrowser( 'browser',   $this->_getShortName( $results[1][$count] ) );
			$this->_setBrowser( 'long_name', $results[1][$count] );
            $this->_setBrowser( 'maj_ver',   $results[2][$count] );
			
            // parse the minor version string and look for alpha chars
            preg_match( '/([.\0-9]+)?([\.a-z0-9]+)?/i', $results[3][$count], $match );
			
            if ( isset( $match[1] ) )
                $this->_setBrowser( 'min_ver', $match[1] );
            else
                $this->_setBrowser( 'min_ver', '.0' );
            
            if ( isset( $match[2] ) )
				$this->_setBrowser( 'letter_ver', $match[2] );
            
			// insert findings into container
            $this->_setBrowser( 'version', $this->_browser_info['maj_ver'] . $this->getProperty( 'min_ver' ) );
        }
    }

	/**
	 * @access private
	 */
    function _getIP()
    {
		if ( getenv( 'HTTP_CLIENT_IP' ) )
        	$ip = getenv( 'HTTP_CLIENT_IP' );
        else
        	$ip = $_SERVER["REMOTE_ADDR"];
        
        $this->_setBrowser( 'ip', $ip );
    }

	/**
	 * @access private
	 */
    function _buildRegex()
    {
		$browsers = '';
        
		while ( list($k,) = each( $this->_browsers ) )
        {
			if ( !empty( $browsers ) )
				$browsers .= "|";
            
			$browsers .= $k;
        }
		
        $version_string = "[\/\sa-z]*([0-9]+)([\.0-9a-z]+)?";
        $this->_browser_regex = "/($browsers)$version_string/i";
    }

	/**
	 * @access private
	 */
    function _getShortName( $long_name )
    {
		return $this->_browsers[strtolower( $long_name )];
    }

	/**
	 * @access private
	 */
    function _testCookies()
    {
		global $phpSniff_session, $phpSniff_stored;
        
		if ( $this->_check_cookies )
        {
			$fp = @fopen( $this->_temp_file_path . $this->getProperty( 'ip' ), 'r' );
            
			if ( !$fp )
            {
				$fp = @fopen( $this->_temp_file_path . $this->getProperty( 'ip' ), 'a' );
                fclose( $fp );
                setcookie( 'phpSniff_session', 'ss' );
                setcookie( 'phpSniff_stored',  'st', time() + 3600 * 24 * 365 );
                $QS = $_SERVER["QUERY_STRING"];
                $script_path = getenv( 'PATH_INFO' )? getenv( 'PATH_INFO' ) : getenv( 'SCRIPT_NAME' );
				
                if ( is_integer( $pos = strpos( strrev( $script_path ), "php.xedni/" ) ) && !$pos )
                    $script_path = strrev( substr( strrev( $script_path ), 9 ) );
                
                $location = 'http://' . $_SERVER["SERVER_NAME"] . $script_path . ( ( $QS == '' )? '' : '?' . $QS );
                header( "Location: $location" );
                exit;
            }
            else
            {
				unlink( $this->_temp_file_path . $this->getProperty( 'ip' ) );
                fclose( $fp );
				
                // remains for backwards compatability
				$this->_setBrowser( 'cookies',    ( $phpSniff_session == 'ss' )? 'true' : 'false' );

                // new cookie settings
                $this->_setBrowser( 'ss_cookies', ( $phpSniff_session == 'ss' )? 'true' : 'false' );
                $this->_setBrowser( 'st_cookies', ( $phpSniff_stored  == 'st' )? 'true' : 'false' );

                setcookie( 'phpSniff_stored', '' );
            }
        }
    }

	/**
	 * @access private
	 */
    function _getJavaScript()
    {
		$set = false;
		
		// see if we have any matches
        while ( list( $version, $browser ) = each( $this->_javascript_versions ) )
        {
			$browser = explode( ',', $browser );
           
		    while ( list(,$search) = each( $browser ) )
            {
				if ( $this->is( 'b:' . $search ) )
                {
					$this->_setBrowser( 'javascript', $version );
                    $set = true;
					
                    break;
                }
            }
        
			if ( $set )
				break;
        }
    }

	/**
	 * @access private
	 */
	function _getFeatures()
	{
		while ( list( $feature, $browser ) = each( $this->_browser_features ) )
		{
			$browser = explode( ',', $browser );
			
			while ( list(,$search) = each( $browser ) )
			{
				if ( $this->isBrowser( $search ) )
				{
					$this->_setFeature( $feature );
					break;
				}
			}
		}
	}
	
	/**
	 * @access private
	 */
	function _getQuirks()
	{
		while ( list( $quirk, $browser ) = each( $this->_browser_quirks ) )
		{
			$browser = explode( ',', $browser );
			
			while ( list(,$search) = each( $browser ) )
			{
				if ( $this->isBrowser( $search ) )
				{
					$this->_setQuirk( $quirk );
					break;
				}
			}
		}		
	}
	
	/**
	 * @access private
	 */
    function _getGecko()
	{
		if ( preg_match( '/gecko\/([0-9]+)/i', $this->getProperty( 'ua' ), $match ) )
		{
			$this->_setBrowser( 'gecko', $match[1] );
            
			if ( preg_match( '/rv:([0-9a-z.+]+)/i', $this->getProperty( 'ua' ), $mozv ) )
            	$this->_setBrowser( 'gecko_ver', $mozv[1] );
            else if ( preg_match( '/(m[0-9]+)/i', $this->getProperty( 'ua' ), $mozv ) )
            	$this->_setBrowser( 'gecko_ver', $mozv[1] );
		}
	}
	
	/**
	 * @access private
	 */
	function _setBrowser( $k, $v )
    {
		$this->_browser_info[strtolower( $k )] = strtolower( $v );
    }
	
	/**
	 * @access private
	 */
	function _setFeature( $k )
    {
		$this->_feature_set[strtolower( $k )] = !$this->_feature_set[strtolower( $k )];
    }
	
	/**
	 * @access private
	 */
	function _setQuirk( $k )
    {
		$this->_quirks[strtolower( $k )] = true;
    }
} // END OF ClientSniffer

?>
