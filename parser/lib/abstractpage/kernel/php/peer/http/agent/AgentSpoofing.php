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
 * @todo add more browsers, Konqueror etc.
 * @package peer_http_agent
 */
 
class AgentSpoofing extends PEAR
{
	/**
	 * @access public
	 */
	var $lang;
	
	/**
	 * @access public
	 */
	var $browser;
	
	/**
	 * @access public
	 */
	var $browser_version;

	/**
	 * @access public
	 */	
	var $os;
	
	/**
	 * @access public
	 */
	var $os_version;
	
	
	/**
	 * @access public
	 */
	function getList( $show_ap = false, $show_lang = false, $language = "" )
	{
		$suffix = $this->_getSuffix( $show_ap, $show_lang, $language );

		$agent_names = array(
			// original abstractpage
			ap_ini_get( "agent_name", "settings" ) . ( ( $show_lang == true )? " [" . $lang . "]" : "" ), 
			
			// common browsers (just for testing purposes)
			"Mozilla/4.78 (Windows XP; U)"                       . $suffix, // Netscape 4.78/Win
			"Mozilla/4.0 (compatible; MSIE 5.0; Windows XP)"     . $suffix, // IE5/Win XP
			"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)" . $suffix, // IE6/Win XP
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.1)"   . $suffix, // Mozilla/Win XP
			"Opera/6.05 (Windows XP; U)"                         . $suffix  // Opera 6/Win XP
		);
		
		return $agent_names;
	}
	
	/**
	 * @access public
	 */
	function setBrowser( $browser = "ie" )
	{
		switch ( strtolower( $browser ) )
		{
			case 'netscape':
			
			case 'ns':
			
			case 'moz':
			
			case 'mozilla':
				$br = "mozilla";
				break;
				
			case 'ie':
			
			case 'internetexplorer':
			
			case 'msie':
				$br = "msie";
				break;
		
			case 'op':
			
			case 'opera':
				$br = "opera";
				break;
				
			default:
				$br = "unknown";
				break;		
		}
		
		$this->browser = $br;
	}
	
	/**
	 * @access public
	 */
	function getBrowser()
	{
		return $this->browser;
	}

	/**
	 * @access public
	 */	
	function setBrowserVersion( $version = "6.0" )
	{
		$this->browser_version = $version;
	}

	/**
	 * @access public
	 */	
	function getBrowserVersion()
	{
		return $this->browser_version;
	}

	/**
	 * @access public
	 */	
	function setOS( $os = "Windows" )
	{
		switch ( strtolower( $os ) )
		{
			case 'win':
		
			case 'windows':
				$br = "windows";
				break;
			
			default:
				$br = "unknown";
				break;		
		}
		
		$this->os = $br;
	}
	
	/**
	 * @access public
	 */
	function getOS()
	{
		return $this->os;
	}
	
	/**
	 * @access public
	 */
	function setOSVersion( $version = "XP" )
	{
		$this->os_version = $version;
	}
	
	/**
	 * @access public
	 */
	function getOSVersion()
	{
		return $this->os_version;
	}
	
	/**
	 * @access public
	 */
	function setLanguage( $language = "" )
	{
		$this->lang = $language;
	}
	
	/**
	 * @access public
	 */
	function getLanguage()
	{
		return $this->lang;
	}
	
	/**
	 * @access public
	 */
	function getAgentString( $show_ap = false, $show_lang = false )
	{
		if ( ( $this->browser == "unknown" ) || ( $this->os == "unknown" ) )
			return false;
			
		switch ( $this->browser )
		{
			case "mozilla":
				$major = explode( ".", $this->browser_version );
				
				// example: "Mozilla/5.0 (Windows; U; Windows NT 5.1)
				if ( $major[0] >= 5 )
					$agent = "Mozilla/" . $this->browser_version . " (" . ucfirst( $this->os ) . "; " . ucfirst( $this->os ) . " " . $this->os_version . ")";
				// example: "Mozilla/4.78 (Windows XP; U)" 
				else
					$agent = "Mozilla/" . $this->browser_version . " (" . ucfirst( $this->os ) . " "  . $this->os_version . "; U)";
					
				break;
				
			case "msie":
				// example: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)
				//          Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)
				$agent = "Mozilla/4.0 (compatible; MSIE " . $this->browser_version . "; " . ucfirst( $this->os ) . " " . $this->os_version . ")";
				break;
				
			case "opera":
				// example: Opera/6.05 (Windows XP; U)
				$agent = "Opera/" .  $this->browser_version . " (" . ucfirst( $this->os ) . " "  . $this->os_version . "; U)";
				break;
				
			default:
				return false;
		}
		
		$suffix = $this->_getSuffix( $show_ap, $show_lang, $this->getLanguage() );
		return $agent . $suffix;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _getSuffix( $show_ap = false, $show_lang = false, $language = "en" )
	{
		$lang    = $language;
		$suffix  = ( $show_ap   == true )? " Abstractpage"    : "";
		$suffix .= ( $show_lang == true )? " [" . $lang . "]" : "";
		
		return $suffix;
	}
} // END OF AgentSpoofing

?>
