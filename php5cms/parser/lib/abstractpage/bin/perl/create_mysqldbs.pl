#!/usr/bin/perl

# Database installer process :
#
# - Yes you can run this multiple times, some people actually use 
#   different dbs for different roles, i.e. mysql for the sessions,
#   and oracle for user authentication. This script is _very_
#   dumb and should be used with caution due to that fact. It may
#   or may not blow up and delete all previous data.
#
# - Okay with that said, let's setup a database :
#   ./create_mysqldbs.pl   \
#      --user=foo     \
#      --password=bar \
#      --mysql
#
# The account that your creating as will need to have create 
# database privileges under mysql, if the database doesn't exist.
#
# This doesn't create users or anything like that anymore,
# but there are example sql statements in the table folder.
#
# - But i cannot create databases, or i don't want the db called prometheus:
#
# That's fine, what you'll need to do is provide the db_name param
# a different value than prometheus.
#
# ./create_mysqldbs.pl   \
#    --user=foo     \
#    --password=bar \
#    --mysql        \
#    --db_name=yourdbname

use Getopt::Long;
use strict;


$| = 1;
my $database_user             = '';
my $database_user_password    = '';

my $mysql_flag                = 0;
my $mysql_new_flag            = 0;
my $mysql_command             = '';
my $mysql_noshow              = 0;
my $mysqlshow_command         = '';

my $session_flag              = 0;
my $user_flag                 = 0;
my $domain_flag               = 0;
my $admin_privileges_flag     = 0;
my $mail_settings_flag        = 0;
my $customer_flag             = 0;
my $user_settings_flag        = 0;

my $db_name                   = 'prometheus';
my $SQL_BASE                  = './';


if ( $ARGV[ 0 ] eq '' )
{
   die <<"USAGE";

create_mysqldbs.pl [options]

	--user=username            - username to be used
	--password=password        - password to be used

	--mysql                    - mysql database
	--mysql_command=path       - path to mysql command
	--mysql_noshow             - disable the mysqlshow command, will
                                use alternate method for checking
                                database/table creation
	--mysqlshow_command=path   - path to mysqlshow command
	--mysql_new                - turns on support for mysql versions
                                greater than 3.22.27 otherwise it 
                                will fail

	--sql_base=path            - base directory to the sql

	--db_name=databasename     - The database name you would like the tables
                                placed into. If it does not exist it will be
                                created for you if possible.

USAGE
}

my $foo;
my $result = GetOptions(
	'user=s',                     \$database_user,
	'password=s',                 \$database_user_password,
	'mysql',                      \$mysql_flag,
	'mysql_new',                  \$mysql_new_flag,
	'mysql_command=s',            \$mysql_command,
	'mysql_noshow',               \$mysql_noshow,
	'mysqlshow_command=s',        \$mysqlshow_command,
	'sql_base=s',                 \$SQL_BASE,
	'db_name=s',                  \$db_name,
);

# print "BASE : " . $SQL_BASE . "\n";

if ( $mysql_command eq '' )
{
	$mysql_command       = find_command('mysql');
}

if ( $mysqlshow_command eq '' )
{
	$mysqlshow_command   = find_command('mysqlshow');
}

print "Option confirmation : \n";

display_option( 'Database Username    ',     $database_user );
display_option( 'Database Password    ',     $database_user_password );

if ( $mysql_flag )
{
	display_option( 'Mysql                ',     $mysql_flag );
	display_option( 'Mysql     Command    ',     $mysql_command );
	display_option( 'Mysqlshow Command    ',     $mysqlshow_command );
}

print "\n";

if ( $mysql_flag )
{
	if ( $mysql_command eq '' )
	{
		die "The mysql command is required : \n" .
			"--mysql-command=path_to_mysql is a override\n";
	}
   
	if ( $mysqlshow_command eq '' )
	{
		die "The mysqlshow command is required :\n" .
			"--mysqlshow-command=path_to_mysqlshow is a override\n";
	}

	# Okay create the mysql database
	create_all_mysql_dbs( $db_name );
}

exit();


sub print_log
{
	my $mesg = shift;
	
	open( LOG_CREATE, ">> create_log.log" );
	print LOG_CREATE . "*" x 40 . "\n";
	print LOG_CREATE . $mesg . "\n";
	close( LOG_CREATE );
	open( LOG_CREATE, ">> create_log.stderr" );
	print LOG_CREATE . "*" x 40 . "\n";
	print LOG_CREATE . $mesg . "\n";
	close( LOG_CREATE );
}

sub display_option
{
	my $option_name      = shift;
	my $option_value     = shift;

	print $option_name . ' - ' . $option_value . "\n";
}

sub find_command
{
	my $target_command = shift;
	my @path_elems = ();
	my $path_elem  = '';

	@path_elems = split( /\:/, $ENV{ "PATH" } );

	foreach $path_elem ( @path_elems )
	{
		my $test_item = '';
		$test_item = $path_elem . '/' . $target_command;
		
		if ( -x $test_item )
		{
			return $test_item;
		}
	}
}

sub get_mysql_dbs
{
	my @mysql_dbs = ();

	# mysqlshow is half-borked on most
	# of my systems, so this is an alternate method 
	# to retrieve databases
	#
	# The real problem occurs in "get_mysql_dbs_tables",
	# see comments there for a better explanation.
	if ( $mysql_noshow )
	{
		open( MYSQL_SHOW, $mysql_command . ' ' .
			'--user=' . $database_user . ' ' .
			'--password=' . $database_user_password . ' ' .
			'--batch --execute=\'show databases\' |'
		) or die "$!\n";
		
		# Eat first line, yummy.
		<MYSQL_SHOW>;
		while( <MYSQL_SHOW> )
		{
			my $input_line = $_;
         
			chomp($input_line);
			push( @mysql_dbs, $input_line );
		}
	}

	else
	{
		open( MYSQL_SHOW, $mysqlshow_command . ' ' . 
			'--user=' . $database_user . ' ' .
			'--password=' . $database_user_password .
			' |'
		) or die "$!\n";
		
		while( <MYSQL_SHOW> )
		{
			my $input_line = $_;

		 	if ( $input_line =~ /\+/ )
			{
				next;
			}
         
		 	if ( $input_line =~ /Databases/ )
			{
				next;
			}
         
		 	if ( $input_line =~ /\|\s*(.*)\s*\|/ )
			{
            
				my $target = $1;
            
				while( $target =~ / $/ )
				{
					$target =~ s/ $//g;
				}
				
				push( @mysql_dbs, $target );
			}
		}
	}

	close( MYSQL_SHOW );
	return @mysql_dbs;
}

sub get_mysql_dbs_tables
{
	my $dbs = shift;
	my @mysql_tables = ();

	# When mysqlshow --user=blah --password=blah database_name
	# is executed on my system, sometimes, it will simply
	# display the database name, rather than the tables it contains.
	# Obviously, this isn't ideal, so I've added a couple of extra
	# routines to get around it.

	$mysql_noshow = detect_bad_mysqlshow();

	# mysqlshow is half-borked on most
	# of my systems, so this is an alternate method 
	# to retrieve table listings
	if ( $mysql_noshow )
	{
		open( MYSQL_SHOW, $mysql_command . ' ' .
			'--user=' . $database_user . ' ' .
			'--password=' . $database_user_password . ' ' .
			'--batch --execute "show tables"' . ' ' .
			$dbs . ' |'
		) or die "$!\n";
		
		<MYSQL_SHOW>;
		while( <MYSQL_SHOW> )
		{
			my $input_line = $_;
			
			chomp( $input_line );
			push( @mysql_tables, $input_line );  
		}
		
		close( MYSQL_SHOW );
	}
	else
	{
		my $command = $mysqlshow_command . ' ' . 
			'--user=' . $database_user . ' ' .
            '--password=' . $database_user_password . ' ' .
            $dbs ;
		
		if ( $mysql_new_flag == 1 )
		{
			# % Doesn't work on all versions of mysql
			# The percent sign is a table wild card . ' % ';
			$command .= ' % ';
		}
		
		# print_log( $command );
		open( MYSQL_SHOW, $command . ' |') or die "$!\n";
		while( <MYSQL_SHOW> )
		{
			#print_log( 'OUTPUT: ' . $_ );
			my $input_line = $_;
			
			if ( $input_line =~ /\+/ )
			{
				next;
			}
         
		 	if ( $input_line =~ /Tables/ )
			{
				next;
			}
         
		 	if ( $input_line =~ /\|\s*(.*)\s*\|/ )
			{
				my $target = $1;
				
				while( $target =~ / $/ )
				{
					$target =~ s/ $//g;
				}
				
				push( @mysql_tables, $target );
			}
		}
	}

   	close( MYSQL_SHOW );
	return @mysql_tables;
}

sub get_mysql_database_existence
{
	my $target_dbs = shift;
	my @mysql_dbs = get_mysql_dbs();
	my $item = '';
	
	foreach $item ( @mysql_dbs )
	{
		# print $item . "***\n";
		
		if ( $item eq $target_dbs )
		{
			# dbs is already there...
			return 1;
		}
	}

   return 0;
}

sub get_mysql_table_existence
{
	my $target_dbs   = shift;
	my $target_table = shift;
	my @mysql_tables = get_mysql_dbs_tables( $target_dbs );
	my $item = '';
   
	foreach $item ( @mysql_tables )
	{
		#print $item . "***\n";
		
		if ( $item eq $target_table )
		{
			# dbs is already there...
			return 1;
		}
	}
	
	return 0;
}

sub run_mysql_script
{
	my $script     = shift;
	my $target_dbs = shift;
	my @command    = ();
	
	@command = ( $mysql_command,
		'--user=' . $database_user,
		'--password=' . $database_user_password
	);
	
	if ( $target_dbs ne "" )
	{
		push( @command, $target_dbs );
	}
	
	push( @command, ">> create_log.log 2>> create_log.stderr" );
	push( @command, "< $script" );

	system( join( ' ', @command ) );
}

sub create_mysql_dbs
{
	my $target_dbs  = shift;
	my $dbs_tables  = shift;
	my $table_cheks = shift;
	my $db_there = 0;

	$db_there = get_mysql_database_existence( $target_dbs );

	if ( $db_there == 1 )
	{
		warn "$target_dbs is already there\n" .
			"we can assume that this is okay, moving on.\n";
	}
	else
	{
		open( TMP, "> ./tmp_create_dbs.sql" ) or die "Could not create tmp_create_dbs.sql";
		print TMP "CREATE DATABASE " . $target_dbs . "\n";
		close( TMP );

		run_mysql_script( './tmp_create_dbs.sql' );
	}

	$db_there = get_mysql_database_existence( $target_dbs );

	if ( $db_there == 1 )
	{
		print "$target_dbs - Database is still there!\n";
	}
	else
	{
		die "Hmm the database $target_dbs is still not there after i tried to\n" .
			"create it, i'm guessing that there is something afoot!\n";
	}

	my $table = '';
	foreach $table ( @$dbs_tables )
	{
		my $pre_table     = $table;
		my $post_table    = $table;

		$pre_table     =~ s/^(.+)\/(.*)\.sql$/$1\/pre_$2\.sql/;
		$post_table    =~ s/^(.+)\/(.*)\.sql$/$1\/post_$2\.sql/;

		if ( -f $pre_table )
		{
			run_mysql_script( $pre_table, $target_dbs );
		}

		run_mysql_script( $table, $target_dbs );

		if ( -f $post_table )
		{
			run_mysql_script( $post_table, $target_dbs );
		}
	}

	foreach $table ( @$table_cheks )
	{
		my $message = '';
		my $status  = '';
		
		if ( get_mysql_table_existence( $target_dbs, $table ) )
		{
			# Table is there
			$message = "Table : $table checked out good";
			$status  = "OK";
		}
		else
		{
			$message = "Table : $table checked DID NOT out good";
			$status  = "ERROR";
		}
		
		printf( '%-50s [ %s ]', $message, $status );
		print "\n";
	}

	print "$target_dbs - Okay looks like we have a database to use now, have fun!\n\n";
}

# Adding get_mysql_version to retrieve mysql's version number, so that
# we can build in a bit of backward compatibility.
sub detect_bad_mysqlshow
{
	my $badversion = $mysql_noshow;
	
	if ( ! $mysql_noshow )
	{
		print "\tDetected faulty 'mysqlshow', using alternate routine.\t[ WARNING ]\n";
		open( BAD_MYSQLVIEW, $mysqlshow_command . ' ' .
			'--user=' . $database_user . ' ' .
			'--password=' . $database_user_password .
			' |'
		) or die "$!\n";
		
		while( <BAD_MYSQLVIEW> )
		{
			my $input_line = $_;
			
			if ( $input_line =~ /\+/ )
			{
				next;
			}
         
		 	if ( $input_line =~ /Databases/ )
			{ 
            	$badversion = 1; 
			}
		}
	}
	
	return $badversion;
}

sub create_all_mysql_dbs
{
	my $target_dbs  = shift;
	my @stuff = glob( $SQL_BASE . 'tables/sql/*.sql' );
	my @dbs_tables  = ();
	my @table_cheks = ();

	foreach my $table ( @stuff )
	{
		if ( $table =~ /pre_(.*)\.sql$/ )
		{
			# print "Skip pre script : $table\n";
			next;
		}
		
		if ( $table =~ /post_(.*)\.sql$/ )
		{
			# print "Skip post script : $table\n";
			next;
		}
		
		if ( $table =~ /^.+\/(.*)\.sql$/ )
		{
			# print "Table name : $1\n";
			push( @table_cheks, $1 );
		}
		
		# print "Table : $table\n";
		push( @dbs_tables, $table );
	}

	create_mysql_dbs(
		$target_dbs,
		\@dbs_tables,
		\@table_cheks
	);
}
