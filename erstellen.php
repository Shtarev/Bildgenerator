<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$db = new MySQLi('localhost', 'root', '2222', 'test');

$hexBlack = [
    '#2F2F2F','#483C32','#D2B48C','#000000','#191970','#000080','#5D6D7E','#808000','#006400','#00A86B','#B22222','#800000','#E30B5D','#8A2BE2','#8B4513'
];
$hexWhite = [
    '#FFFAFA','#FFFFF0','#D2B48C','#87CEEB','#90EE90','#E2725B','#FFD700','#FFDB58','#B87333','#FBCEB1','#E6E6FA'
];

$black = '#000000';
$white = '#FFFFFF';

$front = 'media'  . DIRECTORY_SEPARATOR . 'front.png';
$overlay = 'media'  . DIRECTORY_SEPARATOR . 'front_overlay.png';
$adventure = 'media'  . DIRECTORY_SEPARATOR . 'adventure.png';

list($width, $height, $type_i) = getimagesize($front);

$black_adventure = sizeChange($adventure, $black, $width, $height);
$white_adventure = sizeChange($adventure, $white, $width, $height);

$inDbArr = array();

$inDbArr = array_merge($inDbArr, imgBild($hexWhite, $black_adventure, $front, $overlay, $width, $height));
$inDbArr = array_merge($inDbArr, imgBild($hexBlack, $white_adventure, $front, $overlay, $width, $height));

$inDbStr = implode(',', $inDbArr);
$res = $db->query("INSERT INTO `fotos` (`img`) VALUE $inDbStr");

if($res == 1) {header('Location:index.php');}

//----------
// function
function imgBild($hexType, $advent, $front, $overlay, $width, $height) {
    $inDbArr = array();
    foreach($hexType as $hex) {
    
        $front_new = colorChange($front, $hex);
        $overlay_new = colorChange($overlay, $hex);
    
        $matrix = imagecreatetruecolor(1500, 1500);
        $white = imagecolorallocate($matrix, 255, 255, 255);
        imagefilledrectangle($matrix, 0, 0, $width, $height, $white);

        imagecopyresized($matrix,$front_new,0,0,0,0,$width,$height,$width,$height);
        imagecopyresized($matrix,$advent,0,0,0,0,$width,$height,$width,$height);
        imagecopyresized($matrix,$overlay_new,0,0,0,0,$width,$height,$width,$height);

        imagejpeg($matrix, $imgLink ='fotos'  . DIRECTORY_SEPARATOR . uniqid() . '.jpg');
        array_push($inDbArr, "('$imgLink')");
    }
    return $inDbArr;
}

function colorChange($image, $hex) {
    $rgb = sscanf($hex, '#%02x%02x%02x');
    $rgb = array(255-$rgb[0],255-$rgb[1],255-$rgb[2]);

    $matrix = imagecreatefrompng($image);

    imagefilter($matrix, IMG_FILTER_NEGATE); 
    imagefilter($matrix, IMG_FILTER_COLORIZE, $rgb[0], $rgb[1], $rgb[2]); 
    imagefilter($matrix, IMG_FILTER_NEGATE); 

    imagealphablending( $matrix, false );
    imagesavealpha( $matrix, true );

    return $matrix;
}

function sizeChange($image, $hex, $width, $height) {
    list($w_l, $h_l, $type_i) = getimagesize($image);
    $rgb = sscanf($hex, '#%02x%02x%02x');
    $img_l = imagecreatefrompng($image);

    $matrix = imagecreatetruecolor($width, $height);
    imagealphablending($matrix, false);
    $tr = imagecolorallocatealpha($matrix, 0, 0, 0, 127);
    imagefill($matrix, 0, 0, $tr);
    imagesavealpha($matrix, true);

    $new_w_l = round($w_l/2);
    $new_h_l = round($h_l/2);

    $width_center = $width/2;
    $height_center = $height/2;

    $new_w_l_center = $new_w_l/2;
    $new_h_l_center = $new_h_l/2;

    $x_pos = $width_center - $new_w_l_center;
    $y_pos = $height_center - $new_h_l_center;

    imagecopyresized($matrix,$img_l,$x_pos,$y_pos,0,0,$new_w_l,$new_h_l,$w_l,$h_l);

    imagefilter($matrix, IMG_FILTER_GRAYSCALE);
    imagefilter($matrix, IMG_FILTER_BRIGHTNESS, -100);
    imagefilter($matrix, IMG_FILTER_CONTRAST, 10);
    imagefilter($matrix, IMG_FILTER_COLORIZE, $rgb[0],$rgb[1],$rgb[2]);

    return $matrix;
}