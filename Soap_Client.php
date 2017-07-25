<?php
require 'config.php';
header('Content-type:text/html;charset=utf-8');

$obj = new stdClass;
$obj->params = $_REQUEST;
if (!$obj->params['carrier']) {
    $obj->params['carrier'] = 'airmail';
    $obj->params['country'] = 'jp';
    $obj->params['weight'] = 0.75;
    //$obj->params['tax'] = 0.05;
    $obj->params['currency'] = 6.8211;
	$obj->params['type'] = 'airmail';
}
//print_r($obj->soap->client);


try {
    //default soapclient
    $obj->soap->url = 'http://ship.aswanu.com/Soap_Server.php?WSDL';
    $obj->soap->client = new SoapClient($obj->soap->url);

    $result = $obj->soap->client->getFreightData($obj->params);
    $result = unserialize($result);
    //print_r(json_encode($result));
    print_r($result);

} catch(Exception $e) {
    echo $e->getMessage();
}
?>
<?php require 'header.php';?>