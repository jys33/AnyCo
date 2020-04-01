<?php
 
/**
 * ac_logo_upload.php: Upload a new company logo
 * @package Logo
 */
 
session_start();
require('ac_db.inc.php');
require('ac_equip.inc.php');
 
$sess = new \Equipment\Session;
$sess->getSession();
if (!isset($sess->username) || empty($sess->username)
        || !$sess->isPrivilegedUser()) {
    header('Location: index.php');
    exit;
}
 
$page = new \Equipment\Page;
$page->printHeader("AnyCo Corp. Upload Logo");
$page->printMenu($sess->username, $sess->isPrivilegedUser());
printcontent($sess);
$page->printFooter();
 
// Functions

/**
 * Print the main body of the page
 *
 * @param Session $sess
 */
function printcontent($sess) {
    echo "<div id='content'>";
    if (!isset($_FILES['lob_upload'])) {
        printform();
    } else {
        $blobdata = file_get_contents($_FILES['lob_upload']['tmp_name']);
        if (!$blobdata) {  
            // N.b. this test could be enhanced to confirm the image is a JPEG
            printform();
        } else {
            $db = new \Oracle\Db("Equipment", $sess->username);
            $sql = 'INSERT INTO pictures (pic)
                    VALUES(EMPTY_BLOB()) RETURNING pic INTO :blobbind';
            $db->insertBlob($sql, 'Insert Logo BLOB', 'blobbind', $blobdata);
            echo '<p>New logo was uploaded</p>';
        }
    }
    echo "</div>";  // content
}

/**
 * Print the HTML form to upload the image
 *
 * Adding CSRF protection is an exercise for the reader
 */
function printform() {
    echo <<<EOF
Upload new company logo:
<form action="ac_logo_upload.php" method="POST" enctype="multipart/form-data">
<div>
   Image file name: <input type="file" name="lob_upload">
   <input type="submit" value="Upload"
</div>
<form
EOF;
}
