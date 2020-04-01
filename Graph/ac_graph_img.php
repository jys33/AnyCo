<?php
 
/**
 * ac_graph_img.php: Create a JPEG image of the equipment allocation statistics
 *
 * do not have any text or white space before the "<?php" tag because it will
 * be incorporated into the image stream and corrupt the picture.
 *
 * @package Graph
 */
 
define('WEB_SERVICE_URL', "http://localhost/ac_get_json.php");
 
session_start();
require('ac_equip.inc.php');
 
$sess = new \Equipment\Session;
$sess->getSession();
if (!isset($sess->username) || empty($sess->username)
    || !$sess->isPrivilegedUser()) {
    header('Location: index.php');
    exit;
}
$data = callservice($sess);
do_graph("Equipment Count", 600, $data);
 
// Functions

/**
 * Call the service and return its results
 *
 * @param Session $sess
 * @return array Equipment name/count array
 */
function callservice($sess) {
    // Call the web "service" to get the Equipment statistics
    // Change the URL to match your system configuration
    $calldata = array('username' => $sess->username);
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($calldata)
        )
    );
    $ctx = stream_context_create($options);
    $result = file_get_contents(WEB_SERVICE_URL, false, $ctx);
    if (!$result) {
        $data = null;
    } else {
        $data = json_decode($result, true);
 
        // Sort an array by keys using an anonymous function
        uksort($data, function($a, $b) {
            if ($a == $b)
                return 0;
            else
                return ($a < $b) ? -1 : 1;
            });
        }
    return($data);
}

/**
 * Draw a bar graph, with bars projecting horizontally
 *
 * @param string $title The Graph's title
 * @param type $width Desired image width in pixels
 * @param array $items Array of (caption, value) tuples
 */
function do_graph($title, $width, $items) {
    $border = 50;             // border space around bars
    $caption_gap = 4;         // space between bar and its caption
    $bar_width = 20;          // width of each bar
    $bar_gap = 40;            // space between each bar
    $title_font_id = 5;       // font id for the main title
    $bar_caption_font_id = 5; // font id for each bar's title
 
    // Image height depends on the number of items
    $height = (2 * $border) + (count($items) * $bar_width) +
        ((count($items) - 1) * $bar_gap);
 
    // Find the horizontal distance unit for one item
    $unit = ($width - (2 * $border)) / max($items);
 
    // Create the image and add the title
    $im = ImageCreate($width, $height);
    if (!$im) {
        trigger_error("Cannot create image<br>\n", E_USER_ERROR);
    }
    $background_col = ImageColorAllocate($im, 255, 255, 255); // white
    $bar_col = ImageColorAllocate($im, 0, 64, 128);           // blue
    $letter_col = ImageColorAllocate($im, 0, 0, 0);           // black
    ImageFilledRectangle($im, 0, 0, $width, $height, $background_col);
    ImageString($im, $title_font_id, $border, 4, $title, $letter_col);

    // Draw each bar and add a caption
    $start_y = $border;
    foreach ($items as $caption => $value) {
        $end_x = $border + ($value * $unit);
        $end_y = $start_y + $bar_width;
        ImageFilledRectangle($im, $border, $start_y, $end_x, $end_y, $bar_col);
        ImageString($im, $bar_caption_font_id, $border,
            $start_y + $bar_width + $caption_gap, $caption, $letter_col);
        $start_y = $start_y + ($bar_width + $bar_gap);
    }
 
    // Output the complete image.
    // Any text, error message or even white space that appears before this
    // (including any white space before the "<?php" tag) will corrupt the
    // image data.  Comment out the "header" line to debug any issues.
    header("Content-type: image/jpg");
    ImageJpeg($im);
    ImageDestroy($im);
}
