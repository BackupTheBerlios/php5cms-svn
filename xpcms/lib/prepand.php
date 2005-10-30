<?php
/*
 * Created on 30.10.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$inc = get_include_path();
$sep = (stripos(PHP_OS, 'win') === false ? ':' : ';');
$dir = dirname(__FILE__);

$prado  = sprintf('%s/prado/framework', $dir);
$creole = sprintf('%s/creole/classes', $dir);

$inc = sprintf('%s%s%s%s%s', $inc, $sep, $prado, $sep, $creole);

set_include_path($inc);
?>
