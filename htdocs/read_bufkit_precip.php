<?php

// Script to read a bufkit file and parse it into a more friendly format.
// Written by Chris Karstens - 07/2008.

$line_count = 11237;
$j = 0;
$i = -1;
$file = 'http://www.crh.noaa.gov/bufkit/dmx/eta_kdsm.buf' or die();

$data = file($file) or die('Could not read file!');

foreach ($data as $line) {

     $j++;

     if ($j == $line_count){

          $i++;
          $line_count = $line_count + 6;
          $found_it = explode (" ", trim($line));
          $mm = $found_it[0];
          $in = 0.03937008;
          $mm_to_in = $mm * $in;
          $store[$i] = $mm_to_in;
     }
}

$x = Array('00',1,2,3,4,5,'06',7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84);

include ("/var/www/jpgraph/jpgraph.php");
include ("/var/www/jpgraph/jpgraph_bar.php");

$graph = new Graph(1000,400);    
$graph->SetScale("textlin");
$graph->title->Set("NAM Hourly Precip Forecast - KDSM");
$graph->xaxis->SetTitle('Hour','center');
$graph->yaxis->title->Set("QPF (inches)");
$graph->SetMarginColor('white');
$graph->SetBox();
$graph->SetFrame(false);  
$graph->yaxis->SetTitleMargin(40);
$graph->img->SetMargin(60,40,40,40);
$graph->xaxis->SetTickLabels($x);
$graph->xaxis->SetTextLabelInterval(6);
$graph->xaxis->SetLabelAlign('right','top','center'); 

$bar1 = new BarPlot($store);
$bar1->SetWidth(1.0); 
$bar1->SetFillColor('forestgreen');

$graph->Add($bar1);
$graph->Stroke();
