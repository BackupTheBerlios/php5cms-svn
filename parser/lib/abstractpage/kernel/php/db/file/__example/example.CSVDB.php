<?php

require( '../../../../../prepend.php' );

using( 'db.file.CSVDB' );


$vars = strtolower( $_SERVER['REQUEST_METHOD'] ) == 'get'? $_GET : $_POST;

$phase = $vars['phase'] ;
$db = new CSVDB();
$db->dir = "C:/"; // <= set this to an appropriate writeable directoty

// Encrypted File Operation
// uncomment the next line to run in encrypted mode
// $db->cryptKey = 'EncryptionKey';        // Any string value  
// Encryption keys must be EXACTLY the same for reading and writing !

if (!isset($phase) || ($phase == '0')) {
    phase0($db);
} else if ($phase == '1') {
    phase1($db);
} else if ($phase == '2') {
    phase2_3($db,$var);
} else if ($phase == '3') {
    phase2_3($db, $var, true);
} else if ($phase == '4') {
    phase4($db, $vars);
} else if ($phase == '5') {
    phase5($db, $_SERVER);
}

function phase0($db)
{
    echo "<H2>Example 0 - loading array db  from source</H2>";

    $db->newrows = getdata();
    $db->append();
    echo "<pre>";
    print_r($db->db) ;
    echo "</pre>";
    echo "This is an print_r dump of the array db contained within the DB object<br />\n";
    echo "<a href='$PHP_SELF?phase=1' > Next Example</a>\n";
} 
function phase1($db)
{
    echo "<H2> Example 1 - loading array db  from source and writing to file</H2>";
    $db->newrows = getdata();
    $db->append();
    if ($db->writeDB(false, true)) { // Write database to file forcing overwrite/ file creation
        echo "<pre>";
        $cf = "$db->dir/$db->dataFile";
        readfile($cf) ;
        echo "</pre>";
        echo "This is a readfile dump of the file created from the DB object<br />\n";
        echo "<a href='$PHP_SELF?phase=0' > Previous Example</a>\n&nbsp;&nbsp;&nbsp;";
        echo "<a href='$PHP_SELF?phase=2' > Next Example</a>\n";
    } else {
        echo $db->error;
    } 
} 
function phase2_3($db, $var, $i = false)
{
    $db->assoc = $i;
//    $db->cryptKey = ($var['cryp'] == 'on' ? 'Encryption' : false) ; // key is 'Encryption'
    echo "<H2> Example " . ($i?"3":"2") . " - loading array db  from  file - " . ($i?"Associative":"Numeric") . " Index</H2>";
    if ($db->readDB()) { // read DB file
        $row = 0;
        echo "<table>";
        foreach($db->db as $d) {
            echo "<tr>\n";
            echo "<td>\n";
            echo sprintf("%8s", "Row  " . $row++ . "  ");
            echo "</td>\n";
            while (list($key, $val) = each($d)) {
                echo "<td>";
                echo sprintf("%8s", $key) . "=>" . sprintf("%12s", $val);
                echo "</td>\n";
            } // while
            echo "</tr>\n";
        } 
//        echo "<tr><td> Encryption</td>";//
//        echo "<td> <input type='checkbox' name='cryp'></td>";
//        echo "</tr>";

        echo "</table>\n";
        echo "<pre>";
        echo "\n";
        $cf = "$db->dir/$db->dataFile";
        readfile($cf) ;
        echo "</pre>";
        if ($i) {
            echo "this is the db array listed by row and column Name.<br /> \n";
            echo "and a readfile dump of the raw file <br />\n";
            echo "Note that in associative mode, the array has one less row than before,<br />";
            echo "The first row in now used for the index <br />\n";
            echo "<a href='$PHP_SELF?phase=2' > Previous Example</a>\n&nbsp;&nbsp;&nbsp;";
            echo "<a href='$PHP_SELF?phase=4' > Next Example</a>\n";
        } else {
            echo "this is the db array listed by row and column Number \n";
            echo "and  a readfile dump of the raw file <br />\n";
            echo "<a href='$PHP_SELF?phase=1' > Previous Example</a>\n&nbsp;&nbsp;&nbsp;";
            echo "<a href='$PHP_SELF?phase=3' > Next Example</a>\n";
        } 
    } else {
        echo $db->error;
    } 
} 
function phase4($db, $vars)
{
    $db->assoc = true;
    echo "<H2> Example 4 - Find, Update, Delete</H2>";
    if ($db->readDB()) { // read DB file
        // if a submit button was pressed, retrieve data and perform find/delete/update as appropriate
        // retrieve input values
        $findKey = $vars['fk'];
        $findVal = $vars['fv'];
        $newVal = $vars['nv'];
        $a = array($findKey => $findVal) ; // note this array may contain numerous key/value pairs                    
        // find treats these as an AND clause,
        // if you want and OR you will have to write it!
        $b = $db->find($a); 
        // first the find button
        if ($vars['fnd']) { // basic find routine
            echo "Looking for key <span class='b'>$findKey</span> with a value of <span class='b'>$findVal</span><br /> ";
            if ($b) {
                while (list($key, $val) = each ($b)) {
                    echo "Found in Row <span class='b'>$val</span><br />";
                } // while
            } else {
                echo "No Matches Found";
            } 
        } 
        if ($vars['upd']) { // find all matching values and process them all
            if ($b) {
                while (list($key, $val) = each($b)) {
                    if ($db->update($val, array($findKey => $newVal))) {
                        echo "Replaced <span class='b'>$findVal</span> with <span class='b'>$newVal</span> in row <span class='b'>" . $b['0'] . "</span><br />";
                    } else {
                        echo $db->error;
                    } 
                } 
            } 
        } 
        if ($vars['del']) { // find all matching values and process them all - in reverse order !
            if ($b) {
                rsort($b);
                reset($b);
                while (list($key, $val) = each($b)) {
                    if ($db->delete($val)) {
                        echo "Deleted <span class='b'>" . $b['0'] . "</span> where key <span class='b'>$findKey</span> had value <span class='b'>$findVal</span><br />";
                    } else {
                        echo $db->error;
                    } 
                } // while
            } 
        } 
        if ($vars['cbx'] == 'on') {
            if ($db->writeDB()) {
                echo "File Written";
            } else {
                echo $db->error;
            } 
        } 
        // $input = ;
        // $action = $vars['act'] ;
        $row = 0;
        echo "<form>";
        echo "<input type = 'hidden' name ='phase' value = '4' >";
        echo "<table>";
        foreach($db->db as $d) {
            echo "<tr>\n";
            echo "<td>\n";
            echo sprintf("%8s", "Row  " . $row++ . "  ");
            echo "</td>\n";
            while (list($key, $val) = each($d)) {
                echo "<td>";
                echo sprintf("%8s", $key) . "=>" . sprintf("%12s", $val);
                echo "</td>\n";
            } // while
            echo "</tr>\n";
        } 
        echo "<tr><td colspan=6><hr /></td></tr>";
        echo "<tr>";
        echo "<td colspan =2 class ='l'>";
        echo "<input type = 'submit' name='fnd' value = 'Find' >";
        echo "</td>\n";
        echo "<td colspan =1 class='r'>";
        echo "Key Name:";
        echo "</td>\n";
        echo "<td colspan =3 class='l'><input type = 'text' name = 'fk' style='width:120px'>";
        echo "</td>\n";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan =2 class ='l'>";
        echo "<input type = 'submit' name ='del' value = 'Delete' >";
        echo "</td>\n";
        echo "<td colspan =1 class='r'>";
        echo "Value to Match:";
        echo "<td colspan =3 class='l'><input type = 'text' name = 'fv' style='width:120px'>";
        echo "</td>\n";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan =2 class ='l'>";
        echo "<input type = 'submit' name = 'upd' value = 'Update' >";
        echo "</td>\n";
        echo "<td colspan =1 class='r'>";
        echo "New Value:";
        echo "<td colspan =3 class='l'><input type = 'text' name = 'nv' style='width:120px'>";
        echo "</td>\n";
        echo "</tr>";
        echo "<tr><td> Write to file</td>";
        echo "<td> <input type='checkbox' name='cbx'></td>";
        echo "</tr>";
        echo "<tr><td colspan=6><hr /></td></tr>";

        echo "</table><form>\n";
        echo "<a href='$PHP_SELF?phase=1' > Reload Array</a>\n";
        echo "<a href='$PHP_SELF?phase=3' > Previous Example</a>\n";
        echo "<a href='$PHP_SELF?phase=5' > Next Example</a>\n";
    } else {
        echo $db->error;
    } 
} 
function phase5($log, $server)
{
    echo "<H2> Example 5 - Log writing</H2>";

    $log->dataFile = 'Log.csv';
    $log->newrows[] = array(date('Y:m:d-H:i:s'), $server['HTTP_USER_AGENT'], $server['HTTP_REFERER']);
    $log->appendDB();
    if ($log->readDB()) {
        echo "<table>";
        echo "<tr><td> Date & Time<hr /></td><td> Browser <hr /></td><td> Referrer <hr /></td></tr>";
        foreach($log->db as $d) {
            echo "<tr>\n";
            while (list($key, $val) = each($d)) {
                echo "<td>";
                echo $val;
                echo "</td>\n";
            } // while
            echo "</tr>\n";
        } 
        echo "</table>\n";
    } else {
        echo $log->error;
    } 
    echo "<a href='$PHP_SELF?phase=5' > Re-Run this Example</a>\n";
    echo "<a href='$PHP_SELF?phase=4' > Previous Example</a>\n";
    echo "<a href='$PHP_SELF?phase=0' > Next Example</a>\n";
} 
function getdata()
{
    $data1[] = array('car_maker', 'fruit', 'river', 'town', 'county');
    $data1[] = array('Rover', 'orange', 'lea', 'hertford', 'herts');
    $data1[] = array('Vauxhall', 'apple', 'mimram', 'ware', 'essex');
    $data1[] = array('Volkswagen', 'bananna', 'ash', 'London', 'hants');
    $data1[] = array('BMW', 'grape', 'rib', 'Welwyn', 'devon');
    $data1[] = array('ford', 'lemon', 'thames', 'stevenage', 'cornwall');
    return $data1;
} 

?>