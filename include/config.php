<?php
header('Content-type:text/html;charset=utf-8');
ini_set('display_errors',1);

date_default_timezone_set('HongKong');

define('ROOT',dirname(dirname(dirname(__FILE__))));
define('LIB_PATH',dirname(dirname(__FILE__)) . '/lib');

//set library path
set_include_path(implode(PATH_SEPARATOR, array(
	LIB_PATH,
    get_include_path(),
)));

require 'Mivec/Db.php';
require 'Mivec/Acl.php';
require 'common.php';

//db
$config = array(
    'dbhost'	=> 'localhost',
    'dbport'	=> 3306,
    'dbname'	=> 'ship_aswanu_com',
    'dbuser'	=> 'root',
    'dbpass'	=> 'us7lbSz7'
);
$db = new Mivec_Db($config);

/*
$o = new stdClass;
$o->db = new PDO('mysql:host=localhost;dbname=ship_spidermall_com','root','');
*/

function db() {
    global $db;
    return $db;
}

/**
 * db 的用法
$res = db()->field(array('sid','aa','bbc'))
    ->order(array('sid'=>'desc','aa'=>'asc'))
    ->where(array('sid'=>"101",'aa'=>array('123455','>','or')))
    ->limit(1,2)
    ->select('t_table');
 *
 */
