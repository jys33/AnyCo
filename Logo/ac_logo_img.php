<?php
 
/**
 * ac_logo_img.php: Create a JPEG image of the company logo
 *
 * do not have any text or white space before the "<?php" tag because it will
 * be incorporated into the image stream and corrupt the picture.
 *
 * @package Logo
 */
 
session_start();
require('ac_db.inc.php');
require('ac_equip.inc.php');
 
$sess = new \Equipment\Session;
$sess->getSession();
if (isset($sess->username) && !empty($sess->username)) {
    $username = $sess->username;
} else { // index.php during normal execution, or other external caller
    $username = "unknown-logo";
}
 
$db = new \Oracle\Db("Equipment", $username);
$sql = 'SELECT pic FROM pictures WHERE id = (SELECT MAX(id) FROM pictures)';
$img = $db->fetchOneLob($sql, "Get Logo", "pic");
 
header("Content-type: image/jpg");
echo $img;
