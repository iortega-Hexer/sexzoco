<?php
/**
 * Powerful Form Generator
 *
 * This modules aims to provide for your customer any kind of form you want.
 *
 * If you find errors, bugs or if you want to share some improvments,
 * feel free to contact at contact@prestaddons.net ! :)
 * Si vous trouvez des erreurs, des bugs ou si vous souhaitez
 * tout simplement partager un conseil ou une amélioration,
 * n'hésitez pas à me contacter à contact@prestaddons.net
 *
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) April 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @license   Nicodème Cyril
 * @package   modules
 * @since     2014-04-15
 * @version   2.7.9
 */

include(dirname(__FILE__).'/../../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../../init.php');

define('PX_FONT_NAME_TTF', dirname(__FILE__).'/../../views/fonts/arial.ttf');
/* Set the image width and height */
define('PX_IMAGE_WIDTH', 505);
define('PX_IMAGE_HEIGHT', 200);

function center_text($string, $font_size)
{
    $dimensions = imagettfbbox($font_size, 10, PX_FONT_NAME_TTF, $string);
    return ceil((PX_IMAGE_WIDTH - $dimensions[4]) / 2);
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
    /* de cette manière, ca ne marche bien que pour les lignes orthogonales
    imagesetthickness($image, $thick);
    return imageline($image, $x1, $y1, $x2, $y2, $color);
    */
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

/* Let's generate a totally random string using md5 */
$md5_hash = md5(rand(0, 999));

/* We don't need a 32 character long string so we trim it down to 5 */
$security_code = Tools::substr($md5_hash, 15, 6);

/* Set the session to store the security code */
Context::getContext()->cookie->pfg_captcha_string = $security_code;

/* Create the image resource */
$image = imagecreate(PX_IMAGE_WIDTH, PX_IMAGE_HEIGHT);
if (!is_resource($image)) {
    die('An error occured.');
}

/* We are making three colors, white, black and gray */
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$grey = imagecolorallocate($image, 204, 204, 204);

/* Make the background black */
imagefill($image, 0, 0, $black);

/* Add randomly generated string in white to the image */
// ImageString($image, 5, 30, 3, $security_code, $white);
imagettftext($image, 100, 10, center_text($security_code, 100), 185, $white, PX_FONT_NAME_TTF, $security_code);

/* Throw in some lines to make it a little bit harder for any bots to break */
imagerectangle($image, 0, 0, PX_IMAGE_WIDTH - 1, PX_IMAGE_HEIGHT - 1, $grey);
imagelinethick($image, 0, PX_IMAGE_HEIGHT / 2, PX_IMAGE_WIDTH, PX_IMAGE_HEIGHT / 2, $grey, 8);
imagelinethick($image, PX_IMAGE_WIDTH / 2, 0, PX_IMAGE_WIDTH / 2, PX_IMAGE_HEIGHT, $grey, 8);

/* Tell the browser what kind of file is come in */
header('Content-Type: image/jpeg');

/* Output the newly created image in jpeg format */
imagejpeg($image);

/* Free up resources */
imagedestroy($image);

exit();
