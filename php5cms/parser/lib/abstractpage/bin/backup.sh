#!/bin/sh

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


# cvsroot backup script

# define the dump path and date string
dump_dir="/home/abstractpage_org/work/dumps/"
file_date_string=`date +%m_%d_%Y_%I_%M%p_%Z`

# build the tar
echo "--> creating the tar"
tar -cf bk.tar cvsroot
echo "--> gzipping the tar"
gzip bk.tar
echo "--> done"

# rename it based on the user input
echo "--> renaming the tar to $file_name.tar.gz"
mv bk.tar.gz $file_name.tar.gz

# dump it in my dump dir
cp $file_date_string.tar.gz $dump_dir
echo "--> copied $file_date_string.tar.gz to $dump_dir"

# kill the one here, no need for it
rm -f $file_date_string.tar.gz
echo "------------------------------------------------------"
echo "backup is complete"
