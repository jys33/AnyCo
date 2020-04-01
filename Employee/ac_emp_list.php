<?php
 
/**
 * ac_emp_list.php: list of employees
 * @package Employee
 */
 
define('NUMRECORDSPERPAGE', 5);
 
session_start();
require('ac_db.inc.php');
require('ac_equip.inc.php');
 
$sess = new \Equipment\Session;
$sess->getSession();

if (!isset($sess->username) || empty($sess->username)) {
    header('Location: index.php');
    exit;
}
 
$page = new \Equipment\Page;
$page->printHeader("AnyCo Corp. Employees List");
$page->printMenu($sess->username, $sess->isPrivilegedUser());

printcontent($sess, calcstartrow($sess));

$page->printFooter();
 
// Functions

/**
 * Print the main body of the page
 *
 * @param Session $sess
 * @param integer $startrow The first row of the table to be printed
 */
function printcontent($sess, $startrow) {
    echo "<div id='content'>";
 
    $db = new \Oracle\Db("Equipment", $sess->username);
    $sql = "SELECT employee_id, first_name || ' ' || last_name AS name,
            phone_number FROM employees ORDER BY employee_id";
    $res = $db->execFetchPage($sql, "Equipment Query", $startrow, NUMRECORDSPERPAGE);
    
    if ($res) {
        printrecords($sess, ($startrow === 1), $res);
    } else {
        printnorecords();
    }
 
    echo "</div>";  // content
    // Save the session, including the current data row number
    $sess->empstartrow = $startrow;
    $sess->setSession();
}

/**
 * Return the row number of the first record to display.
 *
 * The calculation is based on the current position
 * and whether the Next or Previous buttons were clicked
 *
 * @param Session $sess
 * @return integer The row number that the page should start at
 */
function calcstartrow($sess) {
    if (empty($sess->empstartrow)) {
        $startrow = 1;
    } else {
        $startrow = $sess->empstartrow;
        if (isset($_POST['prevemps'])) {
            $startrow -= NUMRECORDSPERPAGE;
            if ($startrow < 1) {
                $startrow = 1;
            }
        } else if (isset($_POST['nextemps'])) {
            $startrow += NUMRECORDSPERPAGE;
        }
    }
    return($startrow);
}

/**
 * Print the Employee records
 *
 * @param Session $sess
 * @param boolean $atfirstrow True if the first array entry is the first table row
 * @param array $res Array of rows to print
 */
function printrecords($sess, $atfirstrow, $res) {
    echo <<< EOF
        <table border='1'>
        <tr><th>Name</th><th>Phone Number</th><th>Equipment</th></tr>
EOF;
    foreach ($res as $row) {
        $name = htmlspecialchars($row['NAME'], ENT_NOQUOTES, 'UTF-8');
        $pn   = htmlspecialchars($row['PHONE_NUMBER'], ENT_NOQUOTES, 'UTF-8');
        $eid  = (int)$row['EMPLOYEE_ID'];
        echo "<tr><td>$name</td>";
        echo "<td>$pn</td>";
        echo "<td><a href='ac_show_equip.php?empid=$eid'>Show</a> ";
        if ($sess->isPrivilegedUser()) {
            echo "<a href='ac_add_one.php?empid=$eid'>Add One</a>";
            echo "<a href='ac_add_multi.php?empid=$eid'> Add Multiple</a>\n";
        }
        echo "</td></tr>\n";
    }
    echo "</table>";
    printnextprev($atfirstrow, count($res));
}

/**
 * Print Next/Previous buttons as needed to page through the records
 *
 * @param boolean $atfirstrow True if the first array entry is the first table row
 * @param integer $numrows Number of rows the current query retrieved
 */
function printnextprev($atfirstrow, $numrows) {
    if (!$atfirstrow || $numrows == NUMRECORDSPERPAGE) {
        echo "<form method='post' action='ac_emp_list.php'><div>";
        if (!$atfirstrow)
            echo "<input type='submit' value='< Previous' name='prevemps'>";
        if ($numrows == NUMRECORDSPERPAGE)
            echo "<input type='submit' value='Next >' name='nextemps'>";
        echo "</div></form>\n";
    }
}

/**
 * Print a message that there are no records
 *
 * This can be because the table is empty or the final page of results
 * returned no more records
 */
function printnorecords() {
    if (!isset($_POST['nextemps'])) {
        echo "<p>No Records Found</p>";
    } else {
        echo <<<EOF
            <p>No More Records</p>
            <form method='post' action='ac_emp_list.php'>
            <input type='submit' value='< Previous' name='prevemps'></form>
EOF;
    }
}
