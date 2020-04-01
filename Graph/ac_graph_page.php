<?php
 
/**
 * ac_graph_page.php: Display a page containing the equipment graph
 * @package Graph
 */
 
session_start();
require('ac_equip.inc.php');
 
$sess = new \Equipment\Session;
$sess->getSession();
if (!isset($sess->username) || empty($sess->username)
         || !$sess->isPrivilegedUser()) {
    header('Location: index.php');
    exit;
}
 
$page = new \Equipment\Page;
$page->printHeader("AnyCo Corp. Equipment Graph");
$page->printMenu($sess->username, $sess->isPrivilegedUser());
 
echo <<<EOF
<div id='content'>
<img src='ac_graph_img.php' alt='Graph of office equipment'>
</div>
EOF;
 
$page->printFooter();
