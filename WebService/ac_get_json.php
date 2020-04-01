<?php
 
/**
 * ac_get_json.php: Service returning equipment counts in JSON
 * @package WebService
 */
 
require('ac_db.inc.php');
 
if (!isset($_POST['username'])) {
    header('Location: index.php');
    exit;
}
 
$db = new \Oracle\Db("Equipment", $_POST['username']);
 
$sql = "select equip_name, count(equip_name) as cn
        from equipment
        group by equip_name";
$res = $db->execFetchAll($sql, "Get Equipment Counts");
 
$mydata = array();
foreach ($res as $row) {
    $mydata[$row['EQUIP_NAME']] = (int) $row['CN'];
}

// {"cardboard box":1,"pen":4,"computer":2,"telephone":3,"paper":3,"car":1}
echo json_encode($mydata);
