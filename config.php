<?php
require dirname(__FILE__) . '/include/config.php';

define("__TABLE_CARRIER__" , 'carrier');
define("__TABLE_COUNTRY__" , "country");
define("__TABLE_EXPRESS_DATA__" , "data_express");
define("__TABLE_AIRMAIL_DATA__" , "data_airmail");

$_globalData['currency'] = 'CNY ';
$_globalData['carrier'] = getCarrierDataByKey();
$_globalData['country'] = getCountryDataByKey();
//print_r($_globalData);exit;

//get carrier data id=>carrier
function getCarrierDataByKey($field = '' , $val = "")
{
	global $db;
	$data = array();
	$sql = "SELECT * FROM " . __TABLE_CARRIER__;
	if (!empty($field)) {
		$sql .= " WHERE $field = ";
		$sql .= intval($val) ? "$val" : "'$val'";
		/*
		$rs = $db->fetch($sql);
		$data = array(
			$rs['id']	=> $rs['carrier']
		);*/
	}
	$sql .= " ORDER BY id DESC";
	if ($row = $db->fetchAll($sql)) {
		foreach ($row as $rs) {
			$data[$rs['id']] = $rs['carrier'];
		}
	}
		
	return $data;
}

//get country data
function getCountryDataByKey($field = "" , $val = "")
{
	global $db;
	$data = array();
	$sql = "SELECT * FROM " . __TABLE_COUNTRY__;
	if (!empty($field)) {
		$sql .= " WHERE $field ";
		$sql .= intval($val) ? " =$val" : " LIKE '%$val%'";
		$data = $db->fetch($sql);
	} else {
		if ($row = $db->fetchAll($sql)) {
			foreach ($row as $rs) {
				$data[$rs['id']] = $rs['country'];
			}
			return $data;
		}
	}
	return $data;
}

function getCarrierType()
{
	return array(
		'express'	=> 'Express',
		'airmail'	=> 'Airmail'
	);
}