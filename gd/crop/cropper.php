<?php

$fileid = 'fitem';
if (isset($_POST['Submit']) && isset($_FILES[$fileid])){
  $file_info = $_FILES[$fileid];
  if ($file_info['size'] == 0) die('File error: '.$file_info['error']);
  if (strncmp($file_info['type'],'image/', 6) != 0) die('Wrong file type: '.$file_info['type']);

  $performance = microtime(true);

  $offsetX = 0;
  $offsetY = 0;
  $src = imagecreatefrompng($file_info['tmp_name']);
  $w = imagesx($src);
  $h = imagesy($src);
  $bufw = imagecreate($w, 1);
  $bufh = imagecreate(1, $h);

  while ($offsetX != $w){
    imagecopy($bufh, $src, 0, 0, $offsetX, 0, 1, $h);
    if (imagecolorstotal($bufh) > 1) break;
    ++$offsetX;
  }

  while ($offsetY != $h){
    imagecopy($bufw, $src, 0, 0, 0, $offsetY, $w, 1);
    if (imagecolorstotal($bufw) > 1) break;
    ++$offsetY;
  }

  $nw = $w - $offsetX;
  $nh = $h - $offsetY;
  $dst = imagecreate($nw, $nh) or die('Cannot initialize new image width: '.$nw.' height: '.$nh);
  imagecopy($dst, $src, 0, 0, $offsetX, $offsetY, $nw, $nh);

  $target_path = 'upload/' . basename($file_info['name']);
  if (imagepng($dst, $target_path)){
    $performance = microtime(true) - $performance;
    ?>
    <html>
    <head><title>.: Result :.</title></head>
    <body>
    <p>
    Offset X: <?php echo $offsetX ?> Y: <?php echo $offsetY ?><br/>
    performace: <?php echo $performance * 1000000 ?> usec
    </p>
    <img src=<?php echo $target_path ?> alt="Nubee Item" />
    </body>
    </html>
    <?php
  } else {
    echo 'There was an error uploading the file, please try again!';
  }
}
?>
