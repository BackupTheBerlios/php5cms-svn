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


using( 'io.FileUtil' );


/**
 * @package peer_dns
 */
 
class BindNs extends PEAR
{
	/**
	 * Semaphore identifier for named.conf file locking
	 * @access public
	 */
	var $semlock;
	
	/**
	 * Location of named.conf
	 * @access public
	 */
	var $namedconf;
	
	/**
	 * Location of dns flies, err.. files.
	 * @access public
	 */
	var $directory;
	
	/**
	 * @access public
	 */
	var $tempdir;
	
	/**
	 * Raw dns config contents.
	 * @access public
	 */
	var $contents = "";
	
	/**
	 * Placeholder
	 * @access public
	 */
	var $domain = "";
	
	/**
	 * @access public
	 */
	var $domainfile = "";
	
	/**
	 * Bit of a misnomer, contains info from ONE domain
	 * @access public
	 */
	var $domains = array();
	
	/**
	 * If domain file doesn't exist, create?
	 * @access public
	 */
	var $create = false;
	
	/**
	 * Delete the domain
	 * @access public
	 */
	var $del = false;
	
	/**
	 * Is the domain empty?
	 * @access public
	 */
	var $empty = false;
	
	/**
	 * True if everything checks out a-ok
	 * @access public
	 */
	var $sane = false;
	
	/**
	 * Domain file exists?
	 * @access public
	 */
	var $exists = false;
	
	/**
	 * Total number of domains hosting
	 * @access public
	 */
	var $zones = 0;
	
	/**
	 * Holds host information
	 * @access public
	 */
	var $hosts = array();
	
	/**
	 * Will store current date/time
	 * @access public
	 */
	var $date = "";


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function BindNs( $domain = "" )
	{
		$this->directory = '/etc/named/';
		$this->namedconf = '/etc/named.conf';
		$this->semlock   = 4532;
		$this->tempdir   = ap_ini_get( "path_tmp_os", "path" );
		
		if ( !empty( $domain ) )
			$this->initialize( $domain );
 
		return;
	}

	
	/**
	 * The Initialize function sets up the domain, checks it
	 * for sanity, then saves it and updated $namedconf.
	 *
	 * @access public
	 */
	function initialize( $domain )
	{
		$this->domain =	$domain;
		$this->exists = false;
		
		if ( !$domain )
			$this->create = false;

		// seed the random number gen
		srand( (double)microtime() * 1000000 );

		$this->date = date( "D, M j @ h:ia T Y" ); 
		
		if ( $domain )
			$this->domainfile = $this->directory . $domain;

		if ( $domain )
		{
			if ( !is_file( $this->domainfile ) )
			{
				if ( $this->create == true )
				{
					if ( !touch( $this->domainfile ) )
						return PEAR::raiseError( "Cannot initialize: " . $domain );
				}

				$this->EMTPY = true;
			}

			if ( file_exists( $this->domainfile ) )
			{
				$this->exists = true;
              
			  	if ( FileUtil::isSane( $this->domainfile ) )
        		{
					$this->sane = true;
					
					if ( $this->empty == false )
						$this->dnsReadFile();
            	}
               	else
              	{
                  	return;
             	}
        	}
         	else
           	{
				// non-existant files are safe
            	$this->sane = true;
       		}
       
	   		return;
		}
	}

	/**
	 * Reads in the domain files.
	 *
	 * @access public
	 */
	function dnsReadFile()
	{
		global $php_errormsg;

		$Mytemp		= array();
		$Myjunk		= array();
		$Junk		= array();
		$count		= 0;
		$track		= 3;
		$lobo		= 0;
		$domain		= "";
		$domainame	= "";
		$host		= "";
		$origin		= "";
		$nameserver	= "";
		$contact	= "";
		$bufa		= "";
		$bufb		= "";
		$bufc		= "";
		$contents	= "";
		$serial		= "";
		$refresh	= 0;
		$retry		= 0;
		$expire		= 0;
		$ttl		= 0;
		$mxtrack	= 0;
		$nstrack	= 0;
		$ctrack		= 0;
		$atrack		= 0;
		$loop		= 0;
		$ip			= "";
		$ns			= array();
		$mx			= array();
		$cname		= array();
		$crap		= "";

		$filename   = $this->domainfile;
		$filesize   = filesize( $this->domainfile );

		if ( !empty( $filename ) )
		{
			$this->empty = false;
			$fd = fopen( $filename, "r" );

			if ( empty( $fd ) )
				return PEAR::raiseError( "FATAL File access error: " . $php_errormsg );

			$contents = fread( $fd, filesize( $filename ) );
          	fclose( $fd );
			$this->contents = $contents;

			if ( !$contents )
			{
				$this->empty = true;
				return true;
			}

			$Mytemp = split( "\n", $contents );
			list( $origin, $bufa ) = split( " ", $Mytemp[0], 2 );
			list( $bufb, $crap, $crap, $nameserver, $contact ) = split( '[[:space:]]+', $Mytemp[1], 6 );
			$domain = substr( $bufa, 0, 3 );
			$domainame = "$bufb" . ".$domain";
			$host = $bufb;
			trim( $domainame );

			if ( strcmp( $domainame, $this->domain ) != 0 )
				return PEAR::raiseError( "FATAL domain file does not match domain: " . $php_errormsg );
		
			list( $crap, $serial, $refresh, $retry, $expire, $ttl ) = split( '[[:space:]]+', $Mytemp[2], 7 );

			$Myjunk["$domainame"]["nameserver"]	= $nameserver;
			$Myjunk["$domainame"]["contact"]	= $contact;
			$Myjunk["$domainame"]["serial"]		= $serial;
			$Myjunk["$domainame"]["refresh"]	= $refresh;
			$Myjunk["$domainame"]["retry"]		= $retry;
			$Myjunk["$domainame"]["expire"]		= $expire;
			$Myjunk["$domainame"]["ttl"]		= $ttl;

			for ( $count = 3; $count < count( $Mytemp ); $count++ )
			{
				if ( $loop == 0 )
				{	
					list( $bufc, $bufa, $bufb, $crap ) = split( '[[:space:]]+', $Mytemp[$count], 4 );

					if ( $bufa == "IN" )
					{
						// okay, of what kind is our result...
						if ( $bufb == "NS" )
						{
							$Myjunk["$domainame"]["NS"][$nstrack] = $crap;
							$nstrack++;			
							$bufa = "";
						}

						if ( $bufb == "A" )
						{
							$Myjunk["$domainame"]["A"][$atrack] = $crap;
							$bufa = "";					
							$atrack++;
						}
				
						$bufa = "";
				
						if ( $bufb == "MX" )
						{								
							$Myjunk["$domainame"]["MX"][$mxtrack] = "$crap";
							$mxtrack++;
							$bufa = "";
						}
					}

					if ( $bufc == "\$ORIGIN" )
					{ 					
						$loop = 1;
						$count++;
			 		}
				}

				if ( $loop == 1 )
				{
					list( $bufa, $bufb, $bufc, $crap ) = split( '[[:space:]]+', $Mytemp[$count], 4 );

					if ( $bufc == "CNAME" )
					{
						// found CNAME record
						$Myjunk["$domainame"]["CNAME"][$ctrack] = "$bufa";
						$ctrack++;
						$bufc = "";
					}

					if ( $bufc == "A" )
					{
						// found record
						$Myjunk["$domainame"]["A"][$atrack] = "$bufa" . ":$crap";
						$bufc = "";
						$atrack++;
					}
				}
			}
		
			$this->domains = $Myjunk;
		}
		else
		{
			$this->empty = true;
		}
	}
 
	/**
	 * Writes the domain file.
	 *
	 * @access public
	 */
	function dnsWriteFile()
	{
		$domainfile	= $this->domainfile;
		$domain 	= $this->domain;
		$dhead 		= "";
		$nameserver	= "";
		$contact 	= "";
		$serial 	= "";
		$echeck 	= "";
		$refresh 	= "";
		$retry 		= "";
		$expire 	= "";
		$ttl 		= "";
		$MX 		= "";
		$dtail 		= "";
		$NS 		= "";
		$count 		= 0;

		list ( $dhead, $dtail ) = split( "\.", $domain, 2 );

		// We don't want blank files.
		if ( $this->empty )
			return false;
     
	 	if ( empty( $domain ) )
			return false;

		if ( $this->del == true )
		{
			unlink( $domainfile );
			return true;
		}
	
		$fd = fopen( $domainfile, "w" );

		if ( empty( $fd ) )
     	{		
			$myerror = $php_errormsg;	// In case the unlink generates
                                       	// a new one - we don't care if
                                       	// the unlink fails - we're
                                       	// already screwed anyway
			unlink( $domainfile );		
			return PEAR::raiseError( "FATAL File access error: " . $myerror );
		}
			
		$nameserver	= $this->domains[$domain]["nameserver"];
		$contact 	= $this->domains[$domain]["contact"];
		
		fwrite( $fd, "\$ORIGIN $dtail.\n$dhead\tIN\tSOA\t$nameserver $contact (\n" );
			
		$serial 	= $this->domains[$domain]["serial"];
		$refresh 	= $this->domains[$domain]["refresh"];
		$retry 		= $this->domains[$domain]["retry"];
		$expire 	= $this->domains[$domain]["expire"];
		$ttl 		= $this->domains[$domain]["ttl"];

		fwrite( $fd, "\t\t$serial $refresh $retry $expire $ttl )\n" );

		$echeck = "";
		$echeck = $this->domains[$domain]["NS"];
		
		if ( !empty( $echeck ) )
		{
			for ( $count = 0; $count < count( $this->domains[$domain]["NS"] ); $count++ )
			{
				$NS = $this->domains[$domain]["NS"][$count];
				
				if ( !empty( $NS ) )
					fwrite( $fd, "\t\tIN\tNS\t$NS\n" );
			}
		}

		$echeck = "";
		$echeck = $this->domains[$domain]["MX"];
		
		if ( !empty( $echeck ) )
		{
			for ( $count = 0; $count < count( $this->domains[$domain]["MX"] ); $count++ )
			{
           		$MX = $this->domains[$domain]["MX"][$count];
                 
			 	if ( !empty( $MX ) )
					fwrite( $fd, "\t\tIN\tMX\t$MX\n" );
       		}
       	}

		$echeck = "";
		$echeck = $this->domains[$domain]["A"];
		
		if ( !empty( $echeck ) )
		{
         	for ( $count = 0; $count < count( $this->domains[$domain]["A"]); $count++ )
			{
				if ( !eregi( ":", $this->domains[$domain]["A"][$count] ) )
				{
                   	$A = $this->domains[$domain]["A"][$count];
                     
				 	if ( !empty( $A ) )
						fwrite( $fd, "\t\tIN\tA\t$A\n" );
				}
           	}
       	}

		fwrite( $fd, "\$ORIGIN $domain.\n" );

		$echeck = "";
		$echeck = $this->domains[$domain]["A"];
		
		if ( !empty( $echeck ) )
		{
			for ( $count = 0; $count < count( $this->domains[$domain]["A"]); $count++ )
			{
				if ( eregi( ":", $this->domains[$domain]["A"][$count] ) )
				{
					list( $dhead, $A ) = split( ":", $this->domains[$domain]["A"][$count], 2 );
						
					if ( !empty( $A ) )
						fwrite( $fd, "$dhead\t\tIN\tA\t$A\n" );
				}
			}
		}

		$echeck = "";
       	$echeck = $this->domains[$domain]["CNAME"];
          
	 	if ( !empty( $echeck ) )
		{
			for ( $count = 0; $count < count( $this->domains[$domain]["CNAME"] ); $count++ )
			{
				if ( !eregi( ":", $this->domains[$domain]["CNAME"][$count] ) )
				{
  					$CNAME = $this->domains[$domain]["CNAME"][$count];
            			
					if ( !empty( $CNAME ) )
						fwrite( $fd, "$CNAME\t\tIN\tCNAME\t$domain.\n" );
                 }
			}
		}

		fclose( $fd );
		return true;
	}

	/**
	 * Loads the domains in named.conf for your viewing pleasure.
	 *
	 * @access public
	 */
	function named()
	{		
		$Mytemp 	= array();
       	$hosts 		= array();
		$contents 	= "";
     	$buf 		= "";
		$zhost 		= "";
      	$count 		= 0;
		$sem 		= 0;
		$filename 	= $this->namedconf;

		$sem = sem_get( $this->semlock );
		
		if ( $sem == false )
		{
			sleep( 1 );
			$sem = sem_get( $this->semlock );
			
			if ( $sem == false )
				return PEAR::raiseError( "Semaphore failed to init." );
		}
		
		if ( !sem_acquire( $sem ) )
			return PEAR::raiseError( "Semaphore failed to aquire." );
        
		$fd = fopen( $filename, "r" );

        if ( !$fd )
			return PEAR::raiseError( "File inaccessible. Cannot activate " . $domainame );

        $contents = fread( $fd, filesize( $filename ) );
      	fclose( $fd );

		if ( !sem_release( $sem ) )
			return PEAR::raiseError( "Semaphore failed to release." );

     	$Mytemp = split( "\n", $contents );

       	for ( $count = 0; $count < count( $Mytemp ); $count++ )
		{
			if ( eregi( "^zone", $Mytemp[$count] ) )
			{
				list( $crap, $buf ) = split( '[[:space:]]+', $Mytemp[$count], 3 );
				$crap  = trim( $buf );
				$buf   = $crap;
              	$zhost = eregi_replace( "\"", "", $buf );
				
				if ( $zhost != "." && !eregi( "IN-ADDR.ARPA", $zhost ) )
				{
					// add host
					array_push( $hosts, $zhost );
				}
			}
		}

		$this->hosts = $hosts;
		$this->zones = $count;

		return true;
	}

	/**
	 * Set the contact email addy for the domain.
	 *
	 * @access public
	 */
	function setContact( $email )
	{
		if ( !$email )
			return PEAR::raiseError( "Contact email address not specified." );
		
		$domain = $this->domain;
		list( $user, $domain ) = split( '@', $email, 2 );

		if ( !$user || !$domain )
			return PEAR::raiseError( "Contact email address invalid." );

		$contact = $user . "." . $domain;

		if ( !( $this->empty ) && ( $this->domain ) )
			$this->domains[$this->domain]["contact"] = $contact;

		return true;
	}

	/**
	 * Activates the domain in named.conf
	 *	How you ask?  We read in named.conf, parse the sh*t out of it,
	 *	store the options, and rebuild everything else. What a drag.
	 *
	 * @access public
	 */
	function activate( $command = "add" )
	{
		$Mytemp		= array();	
		$named 		= array();
		$hosts 		= array();
		$domain 	= $this->domain;
		$contents 	= "";
		$buf 		= "";
		$bufa 		= "";
		$bufb 		= "";
		$bufc 		= "";
		$crap 		= "";
		$tbuf 		= "";
		$val 		= "";
		$directory 	= "";
		$count 		= 0;
		$optrack 	= 0;
		$ztrack 	= 0;
		$sem 		= 0;
		$innamed 	= false;
		$tempfile	= tempnam( $this->tempdir, "nmd" );
		$domainame	= $this->domainfile;
		$filename	= $this->namedconf;
		$sem        = sem_get( $this->semlock );
	
		if ( $sem == false )
		{
			sleep( 2 );
			$sem = sem_get( $this->semlock );
			
			if ( $sem == false )
				return PEAR::raiseError( "Semaphore failed to init." );
   		}
		
		if ( !sem_acquire( $sem ) )
			return PEAR::raiseError( "Semaphore failed to aquire." );
		
		$fd = fopen( $filename, "r" );

		if ( !$fd )
			return PEAR::raiseError( "File inaccessible. Cannot activate " . $domainame );

		$contents = fread( $fd, filesize( $filename ) );
		fclose( $fd );

		// Fat chance: eregi( "options \{[[:space:]]+directory \"(.*)\"", $contents, $directory );

		$Mytemp = split( "\n", $contents );

		for ( $count = 0; $count < count( $Mytemp ); $count++ )
		{
			// Deal quick with all options. Just pass them through like bad chili.
			if ( eregi( "^options", $Mytemp[$count] ) )
			{
				$count++;

				while ( !eregi( "^\};", $Mytemp[$count] ) && !eregi( "^zone", $Mytemp[$count] ) )
				{
					$tcrap = trim( $Mytemp[$count] );
					$named["options"][$optrack] = $tcrap;
				
					$count++;
					$optrack++;
				
					if ( $optrack > $count )
						return PEAR::raiseError( "Syntax error in NAMEDCONF." );
				}
			
				$optrack = 0;
			}

			// We want to deal better with zones since we have to tell what's what, what to kill, and what to just maime.
			// We also want to filter out the domain name we specified since we will be re-creating that entry.

			if ( eregi( "^zone", $Mytemp[$count] ) )
			{
				list( $crap, $buf ) = split( '[[:space:]]+', $Mytemp[$count], 3 );
				
				$crap  = trim( $buf );
				$buf   = $crap;
				$zhost = eregi_replace( "\"", "", $buf );

				if ( eregi( "$domain", "$zhost" ) )
				{
					while ( !eregi( "^\};", $Mytemp[$count] ) && !eregi( "^zone", $Mytemp[$count] ) )
						$count++;
				}
				else
				{   
					array_push( $hosts, $zhost );
            	 	$count++;
				
					while ( !eregi( "^\};", $Mytemp[$count] ) && !eregi( "^zone", $Mytemp[$count] ) )
					{
						$tcrap = trim( $Mytemp[$count] );
						$named[$zhost]["zone"][$ztrack] = $tcrap;
          
						$count++;
						$ztrack++;
					
						if ( $ztrack>$count )
							return PEAR::raiseError( "Syntax error in NAMEDCONF." );
					}
			
					$ztrack = 0;
				}
			}
		}

		$this->hosts = $hosts;
		$this->zones = $count;

		// Now we "activate" the new domain......
		if ( $command == "add" )
		{
			$ztrack = 0;
			
			$named[$this->domain]["zone"][$ztrack] = "type master;";
			$ztrack++;
			
			$named[$this->domain]["zone"][$ztrack] = "file \"" . $this->domain ."\";";
			$ztrack++;
			
			$ztrack = 0;
			array_push( $hosts, $this->domain );
			$count++;
			$this->zones = $count;
		}

		// Now we write out a new named.conf (with a side order of temp).
		$fd = fopen( $tempfile, "w" );
      	
		if ( empty( $fd ) )
     	{
			$myerror = $php_errormsg;	// In case the unlink generates
                                 		// a new one - we don't care if
                        				// the unlink fails - we're
                                   		// already screwed anyway
										
			unlink( $tempfile );			
			return PEAR::raiseError( "FATAL File access error: " . $myerror );
		}

		fwrite( $fd, "#\n# named.conf - generated $this->date\n#\n\n" );
		
		if ( $named["options"] )
		{
			fwrite( $fd,"options {\n" );

			for ( $otrack = 0; $otrack < count($named["options"] ); $otrack++ )
			{
				$buf = $named["options"][$otrack];
				fwrite( $fd, "\t$buf\n");
			}
			
			fwrite( $fd,"};\n\n" );
		}

		if ( $hosts )
		{
			foreach( $hosts as $val )
			{
				fwrite( $fd,"zone \"$val\" {\n" );
				
				for ( $ztrack = 0; $ztrack < count( $named[$val]["zone"] ); $ztrack++ )
				{
					$buf = $named[$val]["zone"][$ztrack];
					fwrite( $fd, "\t$buf\n" );
				}
				
				fwrite( $fd, "};\n\n" );
			}
		}

		fclose( $fd );

		if ( $command == "del" )
			$this->del = true;
		
		// We write the domain.db file!
		$this->dnsWriteFile();
		
		// unlink($filename);
		copy( $tempfile, $filename );
		unlink( $tempfile );

		if ( !sem_release( $sem ) )
			return PEAR::raiseError( "Semaphore failed to release." );
		
		return true;
	}

	/**
	 * Returns the current domain name.
	 *
	 * @access public
	 */
	function getDomain()
	{
		$domain = "";
		$domain = $this->domain;

		if ( empty( $domain ) )
			return false; 

		return $domain;
	}

	/**
	 * eturns the Name Server.
	 *
	 * @access public
	 */
	function getNameserver()
	{
		$nameserver	= "";
		$domain 	= "";
		$len 		= 0;
		$domain 	= $this->domain;

		if ( $this->empty )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$nameserver = $this->domains[$domain]["nameserver"];	
		trim( $nameserver );
		$len = strlen( $nameserver );
      	$len--;
     	$nameserver = substr( $nameserver, 0, $len );

		return $nameserver;
	}

	/**
	 * Returns the Contact as email addy.
	 *
	 * @access public
	 */
	function getContact()
	{
		$email		= "";
		$domain		= "";
		$cdomain	= "";
		$buf		= "";
		$len		= 0;
		$domain		= $this->domain;

		if ( $this->empty )
			return false;
			
     	if ( empty( $domain ) )
			return false;

		list( $buf, $cdomain ) = split( "\.", $this->domains[$domain]["contact"], 2 );
		trim( $cdomain );
		$len = strlen( $cdomain );
		$len--;
		$cdomain = substr( $cdomain, 0, $len );
		$email = $buf . "@" . $cdomain;

        return $email;
	}

	/**
	 * Returns the Serial.
	 *
	 * @access public
	 */
	function getSerial()
	{
		$domain	= "";
		$serial	= "";
		$domain	= $this->domain;

		if ( $this->empty )
			return false;
   
   		if ( empty( $domain ) )
			return false;

       	$serial = $this->domains[$domain]["serial"];
        return $serial;
	}

	/**
	 * Returns the Refresh.
	 *
	 * @access public
	 */
	function getRefresh()
	{
		$domain  = "";
		$refresh = "";

		if ( $this->empty )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$refresh = $this->domains[$domain]["refresh"];
        return $refresh;
	}

	/**
	 * Returns the Retry.
	 *
	 * @access public
	 */
	function getRetry()
	{
		$domain = "";
		$retry	= "";
		$domain = $this->$domain;

		if ( $this->empty )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$retry = $this->domains[$domain]["retry"];
        return $retry;
	}

	/**
	 * Returns the Expire field.
	 *
	 * @access public
	 */
	function getExpire()
	{
		$domain = "";
		$expire = "";
		$domain = $this->domain;

		if ( $this->empty )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$ttl = $this->domains[$domain]["expire"];
        return $ttl;
	}

	/**
	 * Returns the TTL.
	 *
	 * @access public
	 */
	function getTtl()
	{
		$domain = "";
		$ttl 	= "";
		$domain = $this->domain;

		if ( $this->empty )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$ttl = $this->domains[$domain]["ttl"];
        return $ttl;
	}
	
	/**
	 * Returns the NS array.
	 *
	 * @access public
	 */
	function getNS()
	{
		$domain = "";
		$NS 	= array();
		$domain = $this->$domain;

		if ( $this->empty )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$NS = $this->domains[$domain]["NS"];
		trim( $NS );
		$len=strlen( $NS );
		$len--;
		$NS = substr( $NS, 0, $len );

        return $NS;
	}

	/**
	 * Returns the A array.
	 *
	 * @access public
	 */
	function getA()
	{
		$domain = "";
		$A 		= array();
		$domain = $this->domain;

		if ( $this->empty )
			return false;
		
		if ( empty( $domain ) )
			return false;

		$A = $this->domains[$domain]["A"];
        return $A;
	}

	/**
	 * Returns the MX array.
	 *
	 * @access public
	 */
	function getMX()
	{
		$domain = "";
		$MX 	= array();
		$domain = $this->domain;

		if ( $this->empty )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$MX = $this->domains[$domain]["MX"];
		return $MX;
	}

	/**
	 * Returns the CNAME array.
	 *
	 * @access public
	 */
	function getCNAME()
	{
		$domain = "";
		$CNAME 	= array();
		$domain	= $this->domain;

		if ( $this->empty )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$CNAME = $this->domains[$domain]["CNAME"];
		return $CNAME;
	}

	/**
	 * Returns the main IP addy.
	 *
	 * @access public
	 */
	function getIP()
	{
		$IP 	= "";
		$count 	= 0;
		$domain = "";
		$domain = $this->domain;

		if ( $this->empty )
			return false;
		
		if ( empty( $domain ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["A"] ); $count++ )
		{
			if ( !ereg( ":", $this->domains[$domain]["A"][$count] ) )
				$IP = $this->domains[$domain]["A"][$count];
		}
			
        return $IP;
	}

	/**
	 * Sets the Name Server.
	 *
	 * @access public
	 */
	function setNameserver( $nameserver )
	{
		$buf 	= "";
		$domain = $this->domain;

		if ( !$nameserver )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$buf = $nameserver . ".";
		$nameserver = $buf;	
    	$this->domains[$domain]["nameserver"] = $nameserver;

        return true;
	}

	/**
	 * Sets the Contact as email addy.
	 *
	 * @access public
	 */
	function setContact( $email )
	{
		$buf 		= "";
		$domain 	= "";
		$cdomain	= "";
		$domain 	= $this->domain;

		if ( !$email )
			return false;
			
		if ( empty( $domain ) )
			return false;

		list( $buf, $cdomain ) = split( "@", $email, 2 );
		$email = $buf . "." . $cdomain . ".";
		$this->domains[$domain]["contact"] = $email;

        return true;
	}

	/**
	 * Sets the Serial.
	 *
	 * @access public
	 */
	function setSerial( $serial )
	{
		$domain = $this->domain;

		if ( !$serial )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$this->domains[$domain]["serial"] = $serial;
		return true;
	}

	/**
	 * Sets the Refresh.
	 *
	 * @access public
	 */
	function setRefresh( $refresh = "10800" )
	{
		$domain = $this->domain;

		if ( !$refresh )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$this->domains[$domain]["refresh"] = $refresh;
        return true;
	}

	/**
	 * Sets the Retry.
	 *
	 * @access public
	 */
	function setRetry( $retry = "3600" )
	{
		$domain = $this->domain;

		if ( !$retry )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$this->domains[$domain]["retry"] = $retry;
		return true;
	}

	/**
	 * Sets the Expire.
	 *
	 * @access public
	 */
	function setExpire( $expire = "604800" )
	{
		$domain = $this->domain;
                
		if ( !$expire )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$this->domains[$domain]["expire"] = $expire;
		return true;
	}

	/**
	 * Sets the TTL.
	 *
	 * @access public
	 */
	function setTtl( $ttl = "86400" )
	{
		$domain = $this->domain;

		if ( !$ttl )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$this->domains[$domain]["ttl"] = $ttl;
		return true;
	}
	
	/**
	 * Adds to the NS array.
	 *
	 * @access public
	 */
	function addNS( $NS )
	{
		$count	= 0;
		$buf 	= "";
		$domain = $this->domain;

		if ( !$NS )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$buf = $NS . ".";
		$NS	 = $buf;
	
		if ( empty( $this->domains[$domain]["NS"] ) )
			$count = 0;
		else
			$count = count( $this->domains[$domain]["NS"] );
		
		$this->domains[$domain]["NS"][$count] = $NS;
		return true;
	}

	/**
	 * Adds to the A array. Remember, $A can be either an ip or in
	 * in the format: subdomain:ip  ie.:  www:10.1.1.50
	 *
	 * @access public
	 */
	function addA( $A )
	{
		$count 	= 0;
		$domain = $this->domain;

		if ( !$A )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["A"] ) )
			$count = 0;
		else
			$count = count( $this->domains[$domain]["A"] );

		$this->domains[$domain]["A"][$count] = $A;
		return true;
	}
	
	/**
	 * Adds to the MX array.
	 *
	 * @access public
	 */
	function addMX( $priority, $where )
	{
		$count 	= 0;
		$MX 	= "";
		$domain = $this->domain;

		if ( !$where )
			return false;
		
		if ( !$priority )
			return false;
		
		if ( empty( $domain ) )
			return false;

		$MX = "$priority" . " " . "$where" . ".";

		if ( empty( $this->domains[$domain]["MX"] ) )
			$count = 0;
		else
			$count = count($this->domains[$domain]["MX"]);
 
		$this->domains[$domain]["MX"][$count] = $MX;
        return true;
	}

	/**
	 * Adds to the CNAME array. Remember, $CNAME can be either an ip or in
	 * in the format: subdomain:ip  ie.:  www:10.1.1.50
	 *
	 * @access public
	 */
	function addCNAME( $CNAME )
	{
		$count 	= 0;
		$buf 	= "";
		$domain = $this->domain;

		if ( !$CNAME )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["CNAME"] ) )
			$count = 0;
		else
			$count = count($this->domains[$domain]["CNAME"]);

		$this->domains[$domain]["CNAME"][$count] = $CNAME;
        return true;
	}

	/**
	 * Adds or creates a reverse lookup entry to an IN-ADDR.ARPA file and
	 * updates named.conf loaded variables to reflect reverse lookup.
	 *
	 * @access public
	 */
	function addReverse( $action = "add" )
	{
		$ip = $this->getIP();
	
		if ( empty( $ip ) )
			return false;

		/*
		Our mode of action here is to parse out ever A ip as well as subdomain:ip
		combo.  Make sure they are unique, then parse in (if exists) and IN-ADDR.ARPA
		file and add the data...  whew!

		if ( !ereg( ":", $this->domains[$domain]["A"][$count] ) )
			$IP = $this->domains[$domain]["A"][$count];
		*/

		// Unimplemented!
		return false;
	}

	/**
	 * Deletes a reverse lookup entry to an IN-ADDR.ARPA file and
	 * updates named.conf loaded variables to reflect reverse lookup.
	 *
	 * @access public
	 */
	function delReverse()
	{
		/*
		Pretty much the same deal as above, except we parse the data and remove it
		from a reverse lookup file.
		*/

		// Unimplemented!
        return false;
	}

	/**
	 * Automatically generates a random serial number in the format:
	 * YYYYMMDDNNN  (Where Y=Year, M=Month, D=Day, N=Number).
	 * We generate with the current date and number starting at 0.
	 *
	 * @access public
	 */
	function autoSerial()
	{
		$Serial = "YYYYMMDDNNN";
		$NUM 	= 0;
		$date 	= "";
		$domain = $this->domain;

		if ( empty( $domain ) )
			return false;
			
		$date = date( "Ymd" );
		// preg_replace( "YYYYMMDDNNN", "$date001", $serial );
		$Serial = $date . "001";
		$this->domains[$domain]["serial"] = $Serial;

        return true;
	}

	/**
	 * Automatically increments the serial in this format:
	 * YYYYMMDDNNN  (Where Y=Year, M=Month, D=Day, N=Number).
	 * We set the date, and increment the number.
	 *
	 * @access public
	 */
	function incSerial()
	{
		$Serial		= "";
		$NewSerial 	= "";
		$NUM 		= "";
		$date 		= "";
		$domain 	= $this->domain;

		if ( empty( $domain ) )
			return false;

		$date = date( "Ymd" );
		$Serial = $this->domains[$domain]["serial"];

		/*
		preg_replace( "YYYYMMDDNNN", "$date001", $serial );
		$Serial = $date . "001";
		$this->domains[$domain]["serial"] = $Serial;
		*/

		$buf = substr( $Serial, 8, 3 );
		$buf++;
		// $NUM = $buf + 1;
		$NUM = sprintf( "%03d", $buf );
		$Serial = $date . "$NUM";
		$this->domains[$domain]["serial"] = $Serial;
		
        return true;
	}

	/**
	 * Deletes the first occurance of $A from domain db.
	 * in the format: subdomain:ip  ie.:  www:10.1.1.50 or just IP.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function fdelA( $A )
	{
		$count 	= 0;
		$curip 	= "";
		$domain = $this->domain;

		if ( !$A )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["A"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["A"] ); $count++ )
		{
			$curip = $this->domains[$domain]["A"][$count];
			
			if ( preg_match( "/$A/i", $curip ) )
			{
				$this->domains[$domain]["A"][$count] = "";
				break;
			}
		}
        
		return true;
	}

	/**
	 * Deletes the last occurance of $A from domain db.
	 * in the format: subdomain:ip  ie.:  www:10.1.1.50 or just IP.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function ldelA( $A )
	{
		$count 	= 0;
		$lcount = 0;
		$curip 	= "";
		$domain = $this->domain;

		if ( !$A )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["A"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["A"] ); $count++ )
		{
			$curip = $this->domains[$domain]["A"][$count];
			
			if ( preg_match( "/$A/i", $curip ) )
				$lcount = $count;
		}

		$this->domains[$domain]["A"][$lcount] = "";
        return true;
	}

	/**
	 * Deletes all occurance of $A from domain db.
	 * in the format: subdomain:ip  ie.:  www:10.1.1.50 or just IP.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function delA( $A )
	{
		$count 	= 0;
		$curip 	= "";
		$domain = $this->domain;

		if ( !$A )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["A"] ) )
			return false;
        
		for ( $count = 0; $count < count( $this->domains[$domain]["A"] ); $count++ )
		{
			$curip = $this->domains[$domain]["A"][$count];
			
			if ( preg_match( "/$A/i", $curip ) )
				$this->domains[$domain]["A"][$count] = "";
		}
        
		return true;
	} 

	/**
	 * Deletes $NS from the NS array.
	 *
	 * @access public
	 */
	function delNS( $NS )
	{
		$count 	= 0;
  		$buf 	= "";
		$curns 	= "";
		$domain = $this->domain;

		if ( !$NS )
			return false;
			
		if ( empty( $domain ) )
			return false;

		$buf = $NS . ".";
		$NS  = $buf;
        
		if ( empty( $this->domains[$domain]["NS"] ) )
			return false;
                        
		for ( $count = 0; $count < count( $this->domains[$domain]["NS"] ); $count++ )
		{	
			$curns = $this->domains[$domain]["NS"][$count];
			
			if ( preg_match( "/$NS/i", $curns ) )
				$this->domains[$domain]["NS"][$count] = "";
		}

        return true;
	}
	
	/**
	 * Deletes the first occurance of $NS from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function fdelNS( $NS )
	{
		$count 	= 0;
		$curns 	= "";
		$domain = $this->domain;

		if ( !$NS )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["NS"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["NS"] ); $count++ )
		{
			$curns = $this->domains[$domain]["NS"][$count];

			if ( preg_match( "/$NS/i", $curns ) )
			{
				$this->domains[$domain]["NS"][$count] = "";
				break;
			}
		}
		
        return true;
	}
	
	/**
	 * Deletes the last occurance of $NS from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function ldelNS( $NS )
	{
		$count 	= 0;
		$lcount = 0;
		$curns 	= "";
		$domain = $this->domain;

		if ( !$NS )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["NS"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["NS"] ); $count++ )
		{
			$curns = $this->domains[$domain]["NS"][$count];
			
			if ( preg_match( "/$NS/i", $curns ) )
				$lcount = $count;
		}

		$this->domains[$domain]["NS"][$lcount] = "";
        return true;
	}

	/**
	 * Deletes all occurance of $MX from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function delMX( $MX )
	{
		$count 		= 0;
		$curmx 		= "";
		$priority 	= 0;
		$domain 	= $this->domain;

		if ( !$MX )
			return false;
			
		if ( empty( $domain ) )
			return false;
			
		if ( empty( $this->domains[$domain]["MX"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["MX"] ); $count++ )
		{
			list( $priority, $curmx ) = split( " ", $this->domains[$domain]["MX"][$count] );
			
			if ( preg_match( "/$MX/i", $curmx ) )
				$this->domains[$domain]["MX"][$count] = "";
		}
		
        return true;
	}
	
	/**
	 * Deletes all occurance of $priority for MX from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function delMXp( $delp )
	{
		$count 		= 0;
		$curmx 		= "";
		$priority 	= 0;
		$domain 	= $this->domain;

		if ( !$delp )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["MX"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["MX"] ); $count++ )
		{
			list( $priority, $curmx ) = split( " ", $this->domains[$domain]["MX"][$count] );
			
			if ( preg_match( "/$delp/i", $priority ) )
 				$this->domains[$domain]["MX"][$count] = "";
		}
		
        return true;
	}
	
	/**
	 * Deletes the first occurance of $MX from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function fdelMX( $MX )
	{
		$count 		= 0;
		$curmx 		= "";
		$priority 	= 0;
		$domain 	= $this->domain;

		if ( !$MX )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["MX"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["MX"] ); $count++ )
		{
			list( $priority, $curmx ) = split( " ", $this->domains[$domain]["MX"][$count] );
			
			if ( preg_match( "/$MX/i", $curmx ) )
			{
				$this->domains[$domain]["MX"][$count] = "";
				break;
			}
		}
        
		return true;
	}
	
	/**
	 * Deletes the last occurance of $NS from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function ldelMX( $MX )
	{
		$count 		= 0;
		$lcount 	= 0;
		$curmx 		= "";
		$priority 	= 0;
		$domain 	= $this->domain;

		if ( !$MX )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["MX"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["MX"] ); $count++ )
		{
			list( $priority, $curmx ) = split( " ", $this->domains[$domain]["MX"][$count] );
			
			if ( preg_match( "/$MX/i", $curmx ) )
				$lcount = $count;
		}

		$this->domains[$domain]["MX"][$lcount] = "";
        return true;
	}

	/**
	 * Deletes all occurance of $CNAME from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function delCNAME( $CNAME )
	{
		$count 	= 0;
		$curcn 	= "";
		$domain = $this->domain;

		if ( !$CNAME )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["CNAME"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["CNAME"] ); $count++ )
		{
			$curcn = $this->domains[$domain]["CNAME"][$count];
			
			if ( preg_match( "/$CNAME/i", $curcn ) )
				$this->domains[$domain]["A"][$count] = "";
		}
		
		return true;
	}

	/**
	 * Deletes the first occurance of $NS from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function fdelCNAME( $CNAME )
	{
		$count 	= 0;
		$curcn 	= "";
		$domain = $this->domain;

		if ( !$CNAME )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["CNAME"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["CNAME"] ); $count++ )
		{
			$curcn = $this->domains[$domain]["CNAME"][$count];
			
			if ( preg_match( "/$CNAME/i", $curcn ) )
			{
				$this->domains[$domain]["CNAME"][$count] = "";
				break;
			}
		}
        
		return true;
	}

	/**
	 * Deletes the last occurance of $CNAME from domain db.
	 * Note: strings must match!
	 *
	 * @access public
	 */
	function ldelCNAME( $CNAME )
	{
		$count 	= 0;
		$lcount = 0;
		$curcn 	= "";
		$domain	= $this->domain;

		if ( !$CNAME )
			return false;
			
		if ( empty( $domain ) )
			return false;

		if ( empty( $this->domains[$domain]["CNAME"] ) )
			return false;

		for ( $count = 0; $count < count( $this->domains[$domain]["CNAME"] ); $count++ )
		{
			$curcn = $this->domains[$domain]["CNAME"][$count];
			
			if ( preg_match( "/$CNAME/i", $curcn ) )
 				$lcount = $count;
		}

		$this->domains[$domain]["CNAME"][$lcount] = "";
        return true;
	}
} // END OF BindNs

?>
