<?php

require( '../../../../prepend.php' );

using( 'auth.Shadow' );


$user = new Shadow(
	"/etc/passwd",
	"/etc/shadow",
	"/etc/group",
	"/etc/gshadow"
);

echo "Free User ID : "  . $user->get_next_uid() . "\n";
echo "Free Group ID : " . $user->get_next_gid() . "\n";
echo "oschlag Uid : "   . $user->user_to_uid( "oschlag" ) . "\n";
echo "oschlag Name : "  . $user->uid_to_user( $user->user_to_uid( "oschlag" ) ) . "\n";
echo "Perl_User GID : " . $user->group_to_gid( "Perl_User" ) . "\n";
echo "Add user ostest\n";

if ( !$user->user_add( "ostest", $user->get_next_uid(), "Perl_User", "Olivers Tester", "/bin/bash", "/home/sites/site3/users/ostst", "hubba" ) )
	echo $user->error_msg . "\n";

echo "Free User ID : "  . $user->get_next_uid() . "\n";
echo "ostest Name : "   . $user->uid_to_user( "101" ) . "\n";
echo "ostest Uid : "    . $user->user_to_uid( "ostest" ) . "\n";
echo "Add user ostest to group wheel\n";

if ( !$user->add_to_group( "wheel", "ostest" ) )
	echo $user->error_msg . "\n";

echo "Delete user ostest\n";

if ( !$user->user_del( "ostest" ) )
	echo $user->error_msg . "\n";

echo "Free User ID : "  . $user->get_next_uid() . "\n";
echo "ostest Name : "   . $user->uid_to_user( "101" ) . "\n";
echo "ostest Uid : "    . $user->user_to_uid( "ostest" ) . "\n";
echo "Add group ostest\n";

if ( !$user->group_add( "ostest" ) )
	echo $user->error_msg . "\n";

	
echo "Free Group ID : " . $user->get_next_gid() . "\n";
echo "ostest Name : "   . $user->gid_to_group( "103" ) . "\n";
echo "ostest GID : "    . $user->group_to_gid( "ostest" ) . "\n";
echo "Delete group ostest\n";

if ( !$user->group_del( "ostest" ) )
	echo $user->error_msg . "\n";

echo "Free Group ID : " . $user->get_next_gid() . "\n";
echo "ostest Name : "   . $user->gid_to_group( "103" ) . "\n";
echo "ostest GID : "    . $user->group_to_gid( "ostest" ) . "\n";

$user->stop_shadow();

?>
