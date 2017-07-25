<?php
class ShippingHandle
{
	private $_data;
	private $result;
	private $_airmail = 'airmail';
	
	public function __construct()
	{
		$this->_data = new stdClass;
	}
	
	private function setShippingCarrier($_carrier) 
	{
		if (strpos($_carrier,'p') == 0) {
			$shipper = ltrim($_carrier,'p');
		}
		
		$sql = "SELECT * FROM ".__TABLE_CARRIER__." WHERE carrier LIKE '%" .$_carrier. "%'";
		if ($row = db()->fetch($sql)) {
			$this->_data->carrier = $row;
			if (stripos($row['carrier'],$this->_airmail) !==false) {
				$this->_data->carrier['method'] = 'airmail';
			}
		}
	}
	
	private function setDestCountry($_country) 
	{
		$sql = "SELECT * FROM " . __TABLE_COUNTRY__ . " WHERE 1";
		
		if (strlen($_country) <= 2) {
			//get country by ISO Code
			$sql .= " AND iso = '$_country'";
		} else {
			$sql .= " AND country = '$_country'";
		}
		if ($row = db()->fetch($sql)) {
			$this->_data->country = $row;
		}
	}
	
	private function setWeight($_weight)
	{
		$this->_data->weight = $_weight;
		if ($this->_data->carrier['method'] !== $this->_airmail) {
			$this->_data->weight = $_weight * 1000; //转换成Kg
		}
	}
	
	
	private function getExpressQuote()
	{
		$sql = "SELECT * FROM " . __TABLE_EXPRESS_DATA__ . " WHERE
		    carrier = " . $this->_data->carrier['id']
            . " AND country = " . $this->_data->country['id'];
        if ($row = db()->fetch($sql)) {
            $this->_data->quote = $row;
        }
	}
	
	private function getAirmailQuote()
	{
		$sql = "SELECT * FROM " . __TABLE_AIRMAIL_DATA__ . " WHERE
		    carrier = " . $this->_data->carrier['id']
            . " AND country = " . $this->_data->country['id'];
        if ($row = db()->fetch($sql)) {
            $this->_data->quote = $row;
        }
	}
	
	public function getFreightData($_config)
	{
		$_price = new stdClass;
		
		self::setShippingCarrier($_config['carrier']);
		self::setDestCountry($_config['country']);
		self::setWeight($_config['weight']);
		
		if (!$this->_data->carrier) throw new Exception("Invaild Shipping Carrier");
		if (!$this->_data->country) throw new Exception("Invaild Destination Country");
		if (!$this->_data->weight) throw new Exception("Invaild The Parcel's Weight");
		
		if ($_config['type'] == 'airmail') {
			self::getAirmailQuote();
			//print_r($this->_data);exit;
			/* 计算价格 */
			$_price->gt = round(($this->_data->quote['price'] * $this->_data->weight) + $this->_data->quote['tracking_no'] , 2);
			//print_r($this->_data);exit;
			
		} else {
			self::getExpressQuote();
			$_weight['init'] = 500; //首重和续重的单位限制
			$_weight['added'] = (float)$this->_data->weight - $_weight['init']; //续重
	
			/* 计算价格 */
			$_price->gt = 0;
			$_price->first = $this->_data->quote['first'];	//首重价
			$_wei['added'] = "";
			if ($this->_data->weight > 500) {
				//计算几个续重
				$_wei['added'] = ceil($_weight['added'] / $_weight['init']);
				if ($_wei['added'] < 1) {
					$_wei['added'] = 1;
				}
			}
			$_price->added =  $_wei['added'] * $this->_data->quote['added']; //续重价
			$_price->gt = round($_price->first + $_price->added , 2);
			$this->_data->totalWeight = array('first' => $_weight['init'] , 'added' => $_wei['added'] , 'total' => $this->_data->weight);
	
		}
		
        //税
        if (!empty($_config['tax'])) {
            $this->_tax = new stdClass;
            $this->_tax->percent = $_config['tax']; //费率
            $_price->_tax = $_price->gt * $_config['tax'];
            $_price->gt += $_price->_tax;
        }

        //处理汇率
        $_price->orgi = $_price->gt;
        if (!empty($_config['currency'])) {
            $_price->gt = round($_price->gt / $_config['currency'] , 2);
        }

        $_result = array(
            'carrier_id'	=> $this->_data->carrier['id'],
            'carrier'	=> $this->_data->carrier['carrier'],
            'country_id'	=> $this->_data->country['id'],
            'country_iso'	=> $this->_data->country['iso'],
            'country'	=> $this->_data->country['country'],
			'tax'		=> $_price->_tax,
            'weight'	=> $this->_data->totalWeight,
            'price_orgi'    => $_price->orgi,
            'price'		=> $_price->gt,
        );

		return serialize($_result);
	}
}

require 'config.php';

/*$cc = new ShippingHandle();
$result = $cc->getFreightData(array(
	'carrier'	=> 'dhl',
	'country'	=> 'japan',
	'weight'	=> 0.245,
	'currency'	=> 6.8,
	'method'	=> 'airmail'
));
print_r(unserialize($result));exit;*/

$server = new SoapServer('ShippingHandle.wsdl',array('soap_version'	=> SOAP_1_2));
$server->setClass('ShippingHandle');
$server->handle();

?>