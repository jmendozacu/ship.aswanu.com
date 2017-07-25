<?php
require dirname(dirname(__FILE__)).'/config.php';

//get country id
$sql = "SELECT * FROM `country` WHERE `iso` IN('AT','AX','BE','CH','DE','DK','GB','ES','FI','FR','GR','IE','IN','IT','LI','LU','MC','NL','NO','PL','PT','SE','SM','TR','VA','AR','BG','CR','CZ','DO','EC','EE','GT','HN','HU','LT','LV','NI','OM','QA','RO','SI','SK','SV')";
if ($row = $db->fetchAll($sql)) {
	foreach ($row as $rs) {
		echo $rs['id'] . ",";
	}
}