<!doctype html public "-//W3C//DTD HTML 4.0 //EN">

<html>
<head>

<title>SQLInjection Examples</title>

</head>

<body>
<?php

/*
theses examples try to get some different informations of the tables
to test them, remove the comments from a kind of test
*/

require( '../../../../../prepend.php' );

using( 'db.sql.SQLInjection' );


function request( $case )
{
    $sRQ = '';
    switch ( $case )
    {
        // try to verify a identity
        case 1:
            $sRQ = "SELECT * FROM users WHERE login = '" . $_POST['test' . "$case"] . "' AND pwd = PASSWORD('" . $_POST['mdp'] . "')";
            break;

        // try to verify a identity
        case 2:
            $sRQ = "SELECT * FROM users WHERE login = '" . $_POST['test' . "$case"] . "' AND pwd = PASSWORD('" . $_POST['mdp'] . "')";
            break;
			
        case 3:
            $sRQ = "SELECT email FROM users WHERE login = '" . $_POST['test' . "$case"] . "'";
            break;
        
        case 4:
            $sRQ = "SELECT email FROM users WHERE login = '" . $_POST['test' . "$case"] . "'";
            break;
        
        case 5:
            $sRQ = "SELECT email FROM users WHERE login_id = " . $_POST['test' . "$case"];
            break;
        
        case 6:
            $sRQ = "SELECT email FROM users WHERE login_id = " . $_POST['test' . "$case"];
            break;
        
        case 7:
            $sRQ = "INSERT INTO users ('login','pwd','is_admin') VALUES ('" . $_POST['login'] . "','" . $_POST['test' . "$case"] . "','N')";
            break;
        
        case 8:
            $sRQ = "INSERT INTO users ('login','pwd','is_admin') VALUES ('" . $_POST['login'] . "','" . $_POST['test' . "$case"] . "','N')";
            break;
    }
	
    echo "case [" . $case . "] : SQL data with SQL inject [" . $sRQ . "]<BR>\r\n";
    return $sRQ;
}


$_POST['login'] = 'hacker';
$_POST['test1'] = " admin'#;"; // try to pass through the admin verification
$_POST['test2'] = "'%%';DROP TABLE ('users');#"; // try to execute more SQL data
$_POST['test3'] = "'%%';DROP TABLE ('users');"; // try to execute more SQL data
$_POST['test4'] = "%%' AND login IS NOT NULL";
$_POST['test5'] = "%% AND 1 = 1"; // always true expression, will return the 1st tuple - in more case the admin tuple -
$_POST['test6'] = "%% AND 2 between 1 AND 3"; // always true expression, will return the 1st tuple - in more case the admin tuple -
$_POST['test7'] = "pass','Y')#";// try to modify it's right
$_POST['test8'] = "pass','Y');DELETE FROM users WHERE user.is_admin = 'Y' AND login <> 'hacker'#";// try to modify it's right



$sql = new SQLInjection();

$sRQ = request( 1 );
echo 'result case [1] found attempt? [' . $sql->test( $sRQ ) . "]<BR>\r\n";

$sRQ = request(2);
echo 'result case [2] found attempt? [' . $sql->test( $sRQ ) . "]<BR>\r\n";

$sRQ = request(3);
echo 'result case [3] found attempt? [' . $sql->test( $sRQ ) . "]<BR>\r\n";

$sRQ = request(4);
echo 'result case [4] found attempt? [' . $sql->test( $sRQ ) . "]<BR>\r\n";

$sRQ = request(5);
echo 'result case [5] found attempt? [' . $sql->test( $sRQ ) . "]<BR>\r\n";

$sRQ = request(6);
echo 'result case [6] found attempt? [' . $sql->test( $sRQ ) . "]<BR>\r\n";

$sRQ = request(7);
echo 'result case [7] found attempt? [' . $sql->test( $sRQ ) . "]<BR>\r\n";

$sRQ = request(8);
echo 'result case [8] found attempt? [' . $sql->test( $sRQ ) . "]<BR>\r\n";

?>

</body>
</html>
