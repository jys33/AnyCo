<?php
 
/**
 * ac_report.php: Full report of all employees and their equipment
 * @package Report
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
$page->printHeader("AnyCo Corp. Equipment Report");
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
    $db = new \Oracle\Db("Equipment", $sess->username);
 
    $sql = "select first_name || ' ' || last_name as emp_name, equip_name
        from employees left outer join equipment
        on employees.employee_id = equipment.employee_id
        order by emp_name, equip_name";
 
    // Change the prefetch value to compare performance.
    // Zero will be slowest. The system default is 100
    $db->setPrefetch(200); // or 0
 
    $time = microtime(true);
    $db->execute($sql, "Equipment Report");
    echo "<table>";
    while (($row = $db->fetchRow()) != false) {
        $empname = htmlspecialchars($row['EMP_NAME'], ENT_NOQUOTES, 'UTF-8');
        $equipname = htmlspecialchars($row['EQUIP_NAME'], ENT_NOQUOTES, 'UTF-8');
        echo "<tr><td>$empname</td><td>$equipname</td></tr>";
    }
    echo "</table>";
    $time = microtime(true) - $time;
    echo "<p>Report generated in " . round($time, 3) . " seconds\n";
    echo "</div>";  // content
}
