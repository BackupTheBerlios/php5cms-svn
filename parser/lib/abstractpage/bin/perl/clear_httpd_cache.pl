#!/usr/bin/perl

# +----------------------------------------------------------------------+
# |Abstractpage Web Application Framework                                |
# +----------------------------------------------------------------------+
# |This program is free software; you can redistribute it and/or modify  |
# |it under the terms of the GNU General Public License as published by  |
# |the Free Software Foundation; either version 2 of the License, or     |
# |(at your option) any later version.                                   |
# |                                                                      |
# |This program is distributed in the hope that it will be useful,       |
# |but WITHOUT ANY WARRANTY; without even the implied warranty of        |
# |MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
# |GNU General Public License for more details.                          |
# |                                                                      |
# |You should have received a copy of the GNU General Public License     |
# |along with this program; if not, write to the Free Software           |
# |Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
# +----------------------------------------------------------------------+
# |Authors: Markus Nix <mnix@docuverse.de>                               |
# +----------------------------------------------------------------------+


# This maintaince script removes the httpd cache files.

# Config
my $httpd_cache_dir = '/var/cache/httpd';

# Grab the sorted list of cache files
foreach my $cache_file ( sort glob( $httpd_cache_dir . '/*' ) )
{
	my $file_age = int( -M $cache_file );

	if ( -f $cache_file && $file_age != 0 )
	{
		print "File age : $file_age\n";
		print "Removing cache entry: $cache_file\n";
		unlink( $cache_file );

		if ( -f $cache_file )
		{
			warn "WARN:: could not remove $cache_file\n";
		}
	}
}

print "Done!\n";
exit();
