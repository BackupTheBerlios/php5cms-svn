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
 * @package peer_ftp
 */
 
class FTPUtil
{
	/**
	 * @access public
	 * @static
	 */
	function parseRawlist( $list, $type = "UNIX" ) 
	{
		if ( $type == "UNIX" )
		{
			$regexp = "([-ldrwxs]{10})[ ]+([0-9]+)[ ]+([A-Z|0-9|-]+)[ ]+([A-Z|0-9|-]+)[ ]+([0-9]+)[ ]+([A-Z]{3}[ ]+[0-9]{1,2}[ ]+[0-9|:]{4,5})[ ]+(.*)";
			$i = 0;
			
			foreach ( $list as $line ) 
			{
				$is_dir = $is_link = false;
				$target = "";

				if ( eregi( $regexp, $line, $regs ) )
				{
					// hide hidden files
					if ( !eregi( "^[.]", $regs[7] ) )
					{
						// don't hide hidden files
						if ( !eregi( "^[.]{2}", $regs[7] ) ) 
						{
							$i++;
						
							if ( eregi( "^[d]", $regs[1] ) )
							{
								$is_dir = true;
							}
							else if ( eregi( "^[l]", $regs[1] ) ) 
							{ 
								$is_link = true;
								list( $regs[7], $target ) = split( " -> ", $regs[7] );
							}

							// Get extension from file name.
							$regs_ex = explode( ".", $regs[7] );
						
							if ( ( !$is_dir ) && ( count( $regs_ex ) > 1 ) )
						   		$extension = $regs_ex[count( $regs_ex ) - 1];
							else 
								$extension = "";

							$files[$i] = array (
								"is_dir"	=> $is_dir,
								"extension"	=> $extension,
								"name"		=> $regs[7],
								"perms"		=> $regs[1],
								"num"		=> $regs[2],
								"user"		=> $regs[3],
								"group"		=> $regs[4],
								"size"		=> $regs[5],
								"date"		=> $regs[6],
								"is_link"	=> $is_link,
								"target"	=> $target 
							);
						}
					}
				}
			}
		}
		else
		{
			$regexp = "([0-9\-]{8})[ ]+([0-9:]{5}[APM]{2})[ ]+([0-9|<DIR>]+)[ ]+(.*)";
			
			foreach ( $list as $line ) 
			{
				$is_dir = false;
				
				if ( eregi( $regexp, $line, $regs ) ) 
				{
					if ( !eregi( "^[.]", $regs[4] ) )
					{
						if ( $regs[3] == "<DIR>" )
						{
							$is_dir  = true;
							$regs[3] = '';
						}
						
						$i++;
	
						// Get extension from filename.
						$regs_ex = explode( ".", $regs[4] );
						
						if ( ( !$is_dir ) && ( count( $regs_ex ) > 1 ) )
						   $extension = $regs_ex[count( $regs_ex ) - 1];
						else 
							$extension = "";

						$files[$i] = array (
							"is_dir"	=> $is_dir,
							"extension"	=> $extension,
							"name"		=> $regs[4],
							"date"		=> $regs[1],
							"time"		=> $regs[2],
							"size"		=> $regs[3],
							"is_link"	=> 0,
							"target"	=> "",
							"num"		=> "" 
						);
					}
				}
			}
		}
		
		if ( is_array( $files ) && count( $files ) > 0 )
		{
			asort( $files );
			reset( $files );
		}
		
		return $files;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function delRecursive( $currentDir, $connection, $file )
	{
		if ( $lista = @ftp_nlist( $connection, "$file" ) )
		{
			for ( $x = 0; $x < count( $lista ); $x++ )
			{
				if ( !@ftp_delete( $connection, "$lista[$x]" ) )
				  	FTPUtil::delRecursive( $currentDir, $connection, $lista[$x] );
			}
			
			@ftp_rmdir( $connection, "$file" );
		}
	}
} // END OF FTPUtil

?>
