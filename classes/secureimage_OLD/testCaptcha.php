<?php

/**
Securimage Test Script
Version 2.0 - 11/15/2009

Upload this PHP script to your web server and call it from the browser.
The script will tell you if you meet the requirements for running Securimage.

http://www.phpcaptcha.org
*/


if (isset($_GET['testimage']) && $_GET['testimage'] == '1') {
  $im = imagecreate(290, 120);
  $white = imagecolorallocate($im, 255, 255, 255);
  $black = imagecolorallocate($im, 0, 0, 0);
 
  $red   = imagecolorallocate($im, 255,   0,   0);
  $blue  = imagecolorallocate($im,   0,   0, 255);

  imagestring($im, 5, 45, 1, 'Securimage Will Work!!', $blue);
  imagestring($im, 2, 5, 2, ':) :)', $red);
  imagestring($im, 2, 255, 2, '(: (:', $red);


  imagestring($im, 3, 5, 25, 'Can you see the ', $black);
  imagestring($im, 3, 190, 25, 'word?*', $black);
  imageline($im, 114, 38, 184, 38, $black);
  $ba = (function_exists('imagecolorallocatealpha') ? 
        @imagecolorallocatealpha($im, 0, 0, 0, 80)  :
        null);

  if ($ba != null) {
    imagestring($im, 3, 115, 25, 'underlined', $ba);
  }

  imagestring($im, 3, 5, 45, '*If the word "underlined" is not visible', $black);
  imagestring($im, 3, 5, 60, 'Securimage will work but you will not be', $black);
  imagestring($im, 3, 5, 75, 'able to use transparent text in your', $black); 
  imagestring($im, 3, 5, 90, 'CAPTCHA image.', $black);
  
  imagepng($im, null, 3);
  exit;
}

function print_status($supported)
{
  if ($supported) {
    echo "<span style=\"color: #00f\">Yes!</span>";
  } else {
    echo "<span style=\"color: #f00; font-weight: bold\">No</span>";
  }
}

?>
<html>
<head>
  <title>Securimage Test Script</title>
</head>

<body>

<h2>Securimage Test Script</h2>
<p>
  This script will test your PHP installation to see if Securimage will run on your server.
</p>

<ul>
  <li>
    <strong>PHP Version:</strong> <?php echo phpversion(); ?>
  <li>
    <strong>GD Support:</strong>
    <?php print_status($gd_support = extension_loaded('gd')); ?>
  </li>
  <?php if ($gd_support) $gd_info = gd_info(); else $gd_info = array(); ?>
  <?php if ($gd_support): ?>
  <li>
    <strong>GD Version:</strong>
    <?php echo $gd_info['GD Version']; ?>
  </li>
  <?php endif; ?>
  <li>
    <strong>TTF Support (FreeType):</strong>
    <?php print_status($gd_support && $gd_info['FreeType Support']); ?>
    <?php if ($gd_support && $gd_info['FreeType Support'] == false): ?>
    <br />No FreeType support.  Cannot use TTF fonts, but you can use GD fonts
    <?php endif; ?>
  </li> 
  <li>
    <strong>imagettfbbox() function:</strong>
    <?php print_status($gd_support && function_exists('imagettfbbox')); ?>
    <?php if ($gd_support && !function_exists('imagettfbbox')): ?>
    <br />PHP function imagettfbbox is not supported.  Font spacing will be estimated.
    <?php endif; ?>
  <li>
    <strong>JPEG Support:</strong>
    <?php print_status($gd_support && $gd_info['JPG Support']); ?>
  </li>
  <li>
    <strong>PNG Support:</strong>
    <?php print_status($gd_support && $gd_info['PNG Support']); ?>
  </li>
  <li>
    <strong>GIF Read Support:</strong>
    <?php print_status($gd_support && $gd_info['GIF Read Support']); ?>
  </li>
  <li>
    <strong>GIF Create Support:</strong>
    <?php print_status($gd_support && $gd_info['GIF Create Support']); ?>
  </li>
 
</ul>

<?php if ($gd_support): ?>
Since you can see this...<br /><br />
<img src="<?php echo $_SERVER['PHP_SELF']; ?>?testimage=1" alt="Test Image" align="bottom" />
<?php else: ?>
Based on the requirements, you do not have what it takes to run Securimage :(
<?php endif; ?>

</body>
</html>
