<?php
require 'config.php';
require 'auth.php';
require 'config.express.php';

$action = @$_GET['act'];
$_succeed = @$_GET['succeed'];

$id = $_GET['id'];
$act = $_GET['act'];

//forward
//$_referer = urldecode($_GET['referer']);
if ($_GET['referer']) {
	//$_COOKIE['referer'] = urldecode($_GET['referer']);
	//setcookie("referer" , urldecode($_GET['referer']));
	$_SESSION['referer'] = urldecode($_GET['referer']);
}
//echo $_referer;
//print_r($_COOKIE);

$data = array();
if (!empty($id)) {
	//$sql = "SELECT * FROM " . __TABLE_EXPRESS_DATA__ . " WHERE id=$id";
	$sql = "SELECT a.*,b.iso FROM " . __TABLE_AIRMAIL_DATA__
		. " a LEFT JOIN " . __TABLE_COUNTRY__ . " b ON (a.country = b.id)"
		. " WHERE a.id = " . $id;
	$row = $db->fetch($sql);
}


if ($act == 'save') {
	$_succeed = FALSE;
	$data = $_POST;
	
	//根据ISO找country id
	if (!$data['country']) {
		$_country = getCountryDataByKey('iso' , $data['iso']);
		$data['country'] = $_country['id'];
	}
	
	$data['updated_at'] = date("Y-m-d");
	//update
	if (!empty($id)) {
		if ($db->where("id=" . $id)
			->update(__TABLE_EXPRESS_DATA__ , $data)){
			$_succeed = TRUE;	
		}
	}
	else {
		if ($db->insert(__TABLE_EXPRESS_DATA__ , $data)) {
			$_succeed = TRUE;
		}
	}
	
	if (!empty($id)) {
		$_url = "?id=" . $id . "&succeed=" . $_succeed;
	} else {
		$_url = $_COOKIE['referer'];
	}
	//echo $_url;
	jsLocation("" , $_url);
}

?>
<?php include 'header.php';?>
<form id="edit_carrier" method="post" action="?act=save&id=<?php echo $id;?>">
<?php if ($_succeed):?>
<div class="success-msg">It was successfully to Saved</div>
<?php endif;?>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="list_row" style="background:#e8e8e8">
    <tr>
        <td height="30" colspan="2" bgcolor="#FFFFFF">Add/Edit Freight Data</td>
    </tr>
    <tr>
        <td width="17%" height="30" bgcolor="#FFFFFF">Select Carrier</td>
        <td width="83%" height="30" bgcolor="#FFFFFF"><?php echo formSelect('carrier' , $_globalData['carrier'] , $row['carrier'])?></td>
    </tr>
    <tr>
        <td width="17%" height="30" bgcolor="#FFFFFF">Select Country</td>
        <td width="83%" height="30" bgcolor="#FFFFFF"><?php echo formSelect('country' , $_globalData['country'] , $row['country'])?></td>
    </tr>
    <tr>
        <td width="17%" height="30" bgcolor="#FFFFFF">Or Input Country Code</td>
        <td width="83%" height="30" bgcolor="#FFFFFF"><?php echo formText('iso' , $row['iso'])?></td>
    </tr>
    <tr>
        <td width="17%" height="30" bgcolor="#FFFFFF">First Price</td>
        <td width="83%" height="30" bgcolor="#FFFFFF"><?php echo formText('first' , $row['first'])?><span class="required">* CNY</span></td>
    </tr>
    <tr>
        <td width="17%" height="30" bgcolor="#FFFFFF">Added Price</td>
        <td width="83%" height="30" bgcolor="#FFFFFF"><?php echo formText('added' , $row['added'])?><span class="required">* CNY</span></td>
    </tr>
    <?php if (!empty($id)) :?>
    <tr>
        <td width="17%" height="30" bgcolor="#FFFFFF">Update Date</td>
        <td width="83%" height="30" bgcolor="#FFFFFF"><?php echo formText('updated_at' , $row['updated_at'])?></td>
    </tr>
    <?php endif;?>
    <tr bgcolor="#F0FFF0">
        <td height="30" colspan="2">
        <button id="submit" type="submit" class="button btn-cart"> <span><span>Save</span></span></button>
        <button id="forward" type="button" onclick="window.location.href='<?php echo $_SESSION['referer']?>'" class="button btn-cart">
          <span><span>Back</span></span>
        </button>
        </td>
    </tr>
</table>
</form>