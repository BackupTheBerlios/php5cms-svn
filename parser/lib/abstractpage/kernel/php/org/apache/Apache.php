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


using( 'util.Util' );


/**
 * @package org_apache
 */
 
class Apache extends PEAR
{
	var $_pathes = array(
		"conf"   => "/etc/httpd/httpd.conf",
		"vhosts" => "/etc/httpd/vhosts",
		"logs"   => "/var/log/httpd",
		"pid"    => "/var/run/httpd.pid"
	);
	
	
	function getVHostFilename( $domain ) 
	{
		return $this->_pathes["vhosts"] . "/$domain.vhost";
	}
	
	function getIndex()
	{
		return $this->_pathes["vhosts"] . "/index.vhost";
	}
	
	function restartApache()
	{
		Util::sudoRun( array( "cmd" => "apachectl", "args" => "restart" ) );		
	
		if ( !file_exists( $this->_pathes["pid"] ) ) 
			return -1;
			
		return 0;
	}

	/**
	 * Values:
	 * ip - of the virtualhost
	 * domain
	 * alias - a string with aliases separated by white spaces
	 * directory - there must be htdocs and /cgi-bin/
	 */
	function addVHost( $values ) 
	{
		extract( $values );
				
		$content  = "<VirtualHost $ip>\n";
		$content .= "ServerName $domain\n";
		
		if ( isset( $alias ) && "" != $alias )
			$content .= "ServerAlias $alias\n";

		$content .= "DocumentRoot $directory\n";
		$content .= "ErrorLog {$this->_pathes["logs"]}/$domain-error_log\n";
		$content .= "CustomLog {$this->_pathes["logs"]}/$domain-access_log common\n";
		$content .= "</VirtualHost>\n";
		
		$vhost_filename = Apache::getVHostFilename( $domain );
		$index_filename = Apache::getIndex();
		
		// copy 
		$filename = Util::getTempFile();
		$fd = fopen( $filename, "w" );
		fwrite( $fd, $content );
		fclose( $fd );
		
		$result = Util::sudoRun( array( "cmd" => "cp", "args" => " $filename $vhost_filename " ) );
		
		if ( $result["result"] != "ok" ) 
			return PEAR::raiseError( "'cp $filename $vhost_filename' failed. (" . Util::getString( $result ) . ")" );

		// remove the temp file
		Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
		
		// get the temp filename for htdocs
		$filename = Util::getTempFile();

		// copy index.vhost to the temp file
		$result = Util::sudoRun( array( "cmd" => "cp", "args" => " $index_filename $filename " ) );
		
		if ( $result["result"] != "ok" ) 
			return PEAR::raiseError( "'cp $index_filename $filename' failed. (" . Util::getString( $result ) . ")" );

		// adjust its permissions
		$result = Util::sudoRun( array( "cmd" => "chmod", "args" => " a+w $filename" ) );
		
		if ( $result["result"] != "ok" ) 
		{
			Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
			return PEAR::raiseError( "'chmod +w $filename' failed. (" . Util::getString( $result ) . ")" );
		}

		$content = "Include $vhost_filename";

		// read the whole file
		$fd = fopen ( $filename, "rb" );
		$contents = fread( $fd, filesize( $filename ) );
		fclose( $fd );
		
		// is the include line already in the file? if yes bail out
		if( preg_match( "/\/" . Apache::getPregName( $domain ) . "/", $contents ) ) 
		{
			Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
			return PEAR::raiseError( "'$content' already in '$index_filename'." );
		}

		// append the include line
		$fd = fopen ( $filename, "a" );
		fwrite( $fd, $content . "\n" );
		fclose( $fd );

		// copy index.vhost back
		$result = Util::sudoRun( array( "cmd" => "cp", "args" => " $filename $index_filename" ) );
		
		if ( $result["result"] != "ok" ) 
		{
			Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
			return PEAR::raiseError( "'cp $filename $index_filename' failed. (" . Util::getString( $result ) . ")" );
		}
	
		if ( Apache::restartApache() == -1 ) 
		{
			Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
			return PEAR::raiseError( "Apache couldn't be restarted." );
		}
	
		// remove the temp file
		Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
			
		return true;
	}
	
	/**
	 * Value: domain
	 */
	function delVHost( $values ) 
	{
		extract( $values );
		
		$filename = Apache::getVHostFilename( $domain );
		$index_filename	= Apache::getIndex();
		
		$result = Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
		
		if ( $result["result"] != "ok" ) 
			return PEAR::raiseError( "Couldn't remove the vhost file '$filename'" );

		// get the temp filename for htdocs
		$filename = Util::getTempFile();

		// copy index.vhost to the temp file
		$result = Util::sudoRun( array( "cmd" => "cp", "args" => " $index_filename $filename " ) );
		
		if ( $result["result"] != "ok" ) 
			return PEAR::raiseError( "'cp $index_filename $filename' failed. (" . Util::getString( $result ) . ")" );
		
		// adjust its permissions
		$result = Util::sudoRun( array( "cmd" => "chmod", "args" => " a+w $filename" ) );
		
		if ( $result["result"] != "ok" ) 
		{
			Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
			return PEAR::raiseError( "'chmod +w $filename' failed. (" . Util::getString( $result ) . ")" );
		}

		// read the whole file
		$fd = fopen ( $filename, "rb" );
		$contents = fread( $fd, filesize( $filename ) );
		fclose( $fd );
		
		// is the include line already in the file? if yes bail out
		$contents = preg_replace( "/Include(.*)\/" . Apache::getPregName( $domain ) . "(.*)\n/", "", $contents );

		// append the include line
		$fd = fopen( $filename, "w" );
		fwrite( $fd, $contents );
		fclose( $fd );

		// copy index.vhost back
		$result = Util::sudoRun( array( "cmd" => "cp", "args" => " $filename $index_filename" ) );

		if ( $result["result"] != "ok" ) 
		{
			Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
			return PEAR::raiseError( "'cp $filename $index_filename' failed. (" . Util::getString( $result ) . ")" );
		}
	
		if ( Apache::restartApache() == -1 ) 
		{
			Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );
			return PEAR::raiseError( "Apache couldn't be restarted." );
		}
	
		// remove the temp file
		Util::sudoRun( array( "cmd" => "rm", "args" => " -f $filename" ) );	
		return true;
	}
	
	/**
	 * @static
	 */
	function getPregName( $value ) 
	{
		return str_replace( "_", "\_", str_replace( "-", "\-", str_replace( ".", "\.", $value ) ) );
	}
} // END OF Apache

?>
