<?php

require( '../../../../../prepend.php' );

using( 'db.file.DBFile' );
using( 'util.Util' );


set_time_limit( 0 );

$micro_time_start = Util::getMicrotime();

$dbf = new DBFile( "C:\Programme\Apache Group\Apache\htdocs\abstractpage\kernel\inc\db\file\__example\db" );

/*
$dbf->selectDB( "db1" );

// $conteudo = $dbf->salt(1000);
// $conteudo = $dbf->convert($conteudo);
$name     = "eduardo\"f,dsgf'gdfg[\042]";
$name2    = "eduard21o";
$name3    = "ssad1 \"[]dsa 0605vb224 SET LIMIT OrDER WHERE";
// $name  = $dbf->convert( $name  );
// $name2 = $dbf->convert( $name2 );

$name3    = $dbf->convert($name3);
$value1   = "1";
$value2   = "fads\ndfghfgdh";

// echo $name3 . "<br>" . $dbf->convert( $name3, "out" );
exit;

$value1 = $dbf->convert( $value1 );
$value2 = $dbf->convert( $value2 );

// $select = $dbf->query( "SELECT * FROM table1 WHERE (email%'bol' AND id='10') OR (email%'terra') ORDER BY name" );
// $select = $dbf->query( "SELECT * FROM table2 ORDER BY id DESC LIMIT 0,8" );
// $select = $dbf->query( "SELECT * FROM table1 WHERE name!%'$name' OR id='11'" );
// $select = $dbf->query( "SELECT * FROM table1 WHERE email!%'bol' AND name!='eduardo' ORDER BY id LIMIT 0,0" );

//for ($i=0;$i<30;$i++) {
//$teste .= "('0','$name3','054','sfdasf')";
//}
//$insert = $dbf->query("INSERT INTO table2 values".$teste);

//$insert = $dbf->query("INSERT INTO table2 values('0','$name3','0','dfsdgfg')");
//$updade = $dbf->query("UPDATE table1 SET id='1',name='$name',age='3',email='ghdgh@terra.com.br' WHERE id='42'");
//$updade = $dbf->query("UPDATE table1 SET id='3',name='eduardo',email='teste'");
//$delete = $dbf->query("DELETE FROM table2");
*/

$createTable = $dbf->query("CREATE TABLE table2 'id int auto_increment','name text null','idade int(3) null','conteudo text'");
$listDB = $dbf->query("LIST DATABASES");
$listTables = $dbf->query("LIST TABLES");
print_r($listTables);
print_r($listDB);

// $createDB = $dbf->query("CREATE DATABASE tesjte");
// $dropDB = $dbf->query("DROP DATABASE tesjte");
// $dropTable = $dbf->query("DROP TABLE table2");

/*
$select = $dbf->query("SELECT * FROM table1 WHERE id>='1'");
//print_r($listTables);
//print_r($listDB);
echo $dbf->numRows($select)."<br>";
//echo $dbf->result($select,0,"id");
echo "<hr>";

while ($row = $dbf->fetchArray($select)) {
	//$delete = $dbf->query("DELETE FROM table2 WHERE id='$row[id]'");
	while(list($key,$value)=each($row)) {
	//$value = $dbf->convert($value,"out");
		echo $key . " -> " . $value . "<br>";
	}
	echo "<hr>";
}

echo "done<br>";
//print_r($dbf->t_info[db1][table2]);


$micro_time_end = Util::getMicrotime();
$time = $micro_time_end - $micro_time_start;
echo "<br>Execution Time: $time";
*/

?>
