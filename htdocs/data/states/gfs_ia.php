<?php

$c = 0;
$d = 5;
$link = "../gfs/gfs3_kdsm.buf";
$data = file($link);

foreach($data as $line){
     $c++;
     if($c == $d){
          $get_init = explode(" ",trim($line));
          $init_h = str_split($get_init[7]);
          $init_year = "".$init_h[0]."".$init_h[1]."";
          $init_mon = "".$init_h[2]."".$init_h[3]."";
          $init_day = "".$init_h[4]."".$init_h[5]."";
          $init = "".$init_h[7]."".$init_h[8]."";
     } 
}

$initialize = "GFS Initialized ".$init_mon."/".$init_day."/".$init_year." @ ".$init."z";

$im = @imagecreatefrompng("ia_sites.png");

$red = imagecolorallocate($im, 255, 0, 0);
$grey = imagecolorallocate($im, 191, 200, 197);
//imagefilledrectangle($im, 50, 50, 192, 65, $grey);
imagestring($im, 10, 50, 25, $initialize, $red);
header("Content-type: image/png");
imagepng($im);
