<?php

putenv("TZ=UTC");

if (isset($argv))
     for ($i=1;$i<count($argv);$i++){
          $it = split("=",$argv[$i]);
          $_GET[$it[0]] = $it[1];
     }

date_default_timezone_set('UTC');

header('Content-type: text/plain');

$model = isset($_GET["model"]) ? $_GET["model"] : "nam";
$member = isset($_GET["member"]) ? $_GET["member"] : "1";
$site = isset($_GET["site"]) ? $_GET["site"] : "kdsm";
$ratio = isset($_GET["ratio"]) ? $_GET["ratio"] : "11";
$hgt = isset($_GET["hgt"]) ? $_GET["hgt"] : "80";
$psfc = isset($_GET["psfc"]) ? $_GET["psfc"] : "500";
$z0 = isset($_GET["z0"]) ? $_GET["z0"] : "11";
$unleash = isset($_GET["unleash"]) ? $_GET["unleash"] : "0";
$date = isset($_GET["date"]) ? $_GET["date"] : "";
$start_time = isset($_GET["start_time"]) ? $_GET["start_time"] : "";
$end_time = isset($_GET["end_time"]) ? $_GET["end_time"] : "";

$i = 0;
$j = 0;
$k = 0;
$z = -1;
$trip = 0;
$rd = 287;
$g = 9.81;
$prof_begin = 2;
$frz_rain = 0;
$sleet = 0;

$europe = array('stpb','berl');
$sites = array();
// check if site is in master list.  If not, terminate script and tell user
for($z=0;$z<=1;$z++){
    if($z == 0){
        $master_list = "../images/nam_bufrstations.txt";
    }
    elseif($z == 1){
        $master_list = "../images/gfs3_bufrstations.txt";
    }
    $data = file($master_list);
    foreach($data as $line){
            $d = explode(" ", trim(preg_replace( '/\s+/', ' ', $line)));
            $sites[] = strtolower($d[3]);
    }
}
if(!(in_array($site, $sites)) && !(in_array($site,$europe))){
        die("Site ".$site." is not available.  Try again.");
}

if($date != "" && strlen($date) == 10){
    $d = str_split($date);
    $year = "".$d[0]."".$d[1]."".$d[2]."".$d[3]."";
    $mon = "".$d[4]."".$d[5]."";
    $day = "".$d[6]."".$d[7]."";
    $hr = "".$d[8]."".$d[9]."";
    $dCheck = strtotime($d[0]."".$d[1]."".$d[2]."".$d[3]."-".$d[4]."".$d[5]."-".$d[6]."".$d[7]." ".$d[8]."".$d[9].":00:00 UTC");
    //echo $dCheck."\n";
}
else{
    $dCheck = strtotime(date("Y-m-d H:i:s"));
}


$var = array('stn','date','pmsl','pres','sktc','stc1','snfl','wtns','p01m','c01m','stc2','lcld','mcld','hcld','snra','uwnd','vwnd','r01m','bfgr','t2ms','q2ms','wxts','wxtp','wxtz','wxtr','ustm','vstm','hlcy','sllh','wsym','cdbp','vsbk','td2m','evap','p03m','c03m','swem','s03m','show','lift','swet','kinx','lclp','pwat','totl','cape','lclt','cins','eqlv','lfct','brch','buf_snow_sr','buf_snow_maxt','snra_constant','snra_maxt','maxt','mom_wind_mean','mom_wind_max','tf','td','wspd','wdir','hiwc','qpf','qpf_accum','wagl','frz_rain','sleet','rh','buf_snow_sr_rate','buf_snow_maxt_rate','init');

$vars = count($var) - 1;

//intialize some of the arrays
for($y=0;$y<=200;$y++){
    if($model == "nam" || $model == "namm" || $model == "rap" || $model == "nam4km"){
        $evap[] = 0;
        $p03m[] = 0;
        $c03m[] = 0;
        $swem[] = 0;
        $s03m[] = 0;
    }
    elseif($model == "gfs" || $model == "gfsm"){
        $snfl[] = 0;
        $stns[] = 0;
        $wtns[] = 0;
        $p01m[] = 0;
        $c01m[] = 0;
        $stc2[] = 0;
        $snra[] = 0;
        $r01m[] = 0;
        $bfgr[] = 0;
        $ustm[] = 0;
        $vstm[] = 0;
        $hlcy[] = 0;
        $sllh[] = 0;
        $wsym[] = 0;
        $cdbp[] = 0;
        $vsbk[] = 0;
    }
}

if($model == "nam"){
    if($date == ""){
        $link = "nam/nam_".$site.".buf";
    }
    else{
        $link = "https://mtarchive.geol.iastate.edu/".$year."/".$mon."/".$day."/bufkit/".$hr."/nam/nam_".$site.".buf";
    }
    $line_start = 11230;
    $line_end = 11739;
    $hrs = 84;
}
elseif($model == "namm"){
    if($date == ""){
            $link = "namm/namm_".$site.".buf";
    }
    else{
        $link = "https://mtarchive.geol.iastate.edu/".$year."/".$mon."/".$day."/bufkit/".$hr."/nam/namm_".$site.".buf";
    }
        $line_start = 11230;
        $line_end = 11739;
        $hrs = 84;
}
elseif($model == "nam4km"){
        $link = "nam4km/nam4km_".$site.".buf";                
        $line_start = 8062;
        $line_end = 8427;
        $hrs = 60;
} 
elseif($model == "sref"){
    if($unleash == 1){
        system("unzip -oq sref/sref_".$site.".buz -d /home/ckarsten/WWW/bufkit/data/sref_temp/");
        $sref_file = "/home/ckarsten/WWW/bufkit/data/sref_temp/sref_".$site.".buf";
        $link = $sref_file;
    }
    else{
        $link = "/home/ckarsten/WWW/bufkit/data/sref_temp/sref_".$site.".buf";
    }
           $line_start = 6980;
        $prof_begin = $line_start - 6978;
    if($member == 2){
                $line_start = 14471;
                $prof_begin = $line_start - 6978;
    }
    elseif($member == 3){
        $line_start = 21962;
                $prof_begin = $line_start - 6978;
    }
        elseif($member == 4){
                $line_start = 29453;
                $prof_begin = $line_start - 6978;
        }
        elseif($member == 5){
                $line_start = 36944;
        $prof_begin = $line_start - 6978;
        }
        elseif($member == 6){
                $line_start = 46985;
        $prof_begin = $line_start - 9528;
        }
        elseif($member == 7){
                $line_start = 57026;
                $prof_begin = $line_start - 9528;
        }
        elseif($member == 8){
                $line_start = 67067;
                $prof_begin = $line_start - 9528;
        }
        elseif($member == 9){
                $line_start = 77108;
                $prof_begin = $line_start - 9528;
        }
        elseif($member == 10){
                $line_start = 87149;
                $prof_begin = $line_start - 9528;
        }
        elseif($member == 11){
                $line_start = 97190;
                $prof_begin = $line_start - 9528;
        }
        elseif($member == 12){
                $line_start = 107401;
                $prof_begin = $line_start - 9698;
        }
        elseif($member == 13){
                $line_start = 117612;
                $prof_begin = $line_start - 9698;
        }
        elseif($member == 14){
                $line_start = 127823;
                $prof_begin = $line_start - 9698;
        }
        elseif($member == 15){
                $line_start = 138034;
                $prof_begin = $line_start - 9698;
        }
        elseif($member == 16){
                $line_start = 148245;
                $prof_begin = $line_start - 9698;
        }
        elseif($member == 17){
                $line_start = 154546;
                $prof_begin = $line_start - 5788;
        }
        elseif($member == 18){
                $line_start = 160847;
                $prof_begin = $line_start - 5788;
        }
        elseif($member == 19){
                $line_start = 167148;
                $prof_begin = $line_start - 5788;
        }
        elseif($member == 20){
                $line_start = 173449;
                $prof_begin = $line_start - 5788;
        }
        elseif($member == 21){
                $line_start = 179750;
                $prof_begin = $line_start - 5788;
        }
        $line_end = $line_start + 509;
        $hrs = 84;
}
elseif($model == "gfs"){
    if($date == ""){
            $link = "gfs/gfs3_".$site.".buf";
    }
    else{
        $link = "http://mtarchive.geol.iastate.edu/".$year."/".$mon."/".$day."/bufkit/".$hr."/gfs/gfs3_".$site.".buf";
    }
    if($date == ""){
                $line_start = 19748;
                $line_end = 20311;
                $hrs = 140;
        }
        elseif($dCheck < 1500638400){
                $line_start = 8548;
                $line_end = 8791;
                $hrs = 60;
        }
        else{
                $line_start = 19748;
                $line_end = 20311;
                $hrs = 140;
        }
}
elseif($model == "gfsm"){
        if($date == ""){
            $link = "gfsm/gfs3_".$site.".buf";
    }
    else{
        $link = "http://mtarchive.geol.iastate.edu/".$year."/".$mon."/".$day."/bufkit/".$hr."/gfs/gfs3_".$site.".buf";
    }
    if($date == ""){
        $line_start = 19748;
                $line_end = 20311;
                $hrs = 140;
    }
    if($dCheck < 1500670800){
            $line_start = 8548;
            $line_end = 8791;
            $hrs = 60;
    }
    else{
        $line_start = 19748;
            $line_end = 20311;
            $hrs = 140;
    }
}
elseif($model == "rap" || $model == "ruc"){
    if($date == "" && $model == "rap"){
            $link = "rap/rap_".$site.".buf";
        $line_start = 2474;
            $line_end = 2605;
            $hrs = 21;
    }
    elseif($dCheck >= 1471953600){
                $link = "http://mtarchive.geol.iastate.edu/".$year."/".$mon."/".$day."/bufkit/".$hr."/rap/rap_".$site.".buf";
                $line_start = 2474;
                $line_end = 2605;
                $hrs = 21;
        }
    elseif($year >= 2012 && $mon >= 05 && $day >= 01){
        $link = "http://mtarchive.geol.iastate.edu/".$year."/".$mon."/".$day."/bufkit/".$hr."/rap/rap_".$site.".buf";
                $line_start = 2138;
                $line_end = 2251;
                $hrs = 18;
    }
    else{
        $link = "http://mtarchive.geol.iastate.edu/".$year."/".$mon."/".$day."/bufkit/".$hr."/ruc/ruc_".$site.".buf";
                $line_start = 2138;
                $line_end = 2251;
                $hrs = 18;
    }
}

//echo $year.",".$mon.",".$day.",".$hr."\n";
//echo $line_start.",".$line_end.",".$hrs;
//die();

$fh = @fopen($link, "r");
if($fh == false){
    die();
}

while(($line = fgets($fh)) !== false) {
    $i++;
    if($i > $line_end){
        break;
    }
    $d = explode(" ",trim($line));
    $d2 = trim($line);
    if($d2 == "" || @$d[0] == "STN"){
        $trip = 0;
        $k = 0;
    }
    elseif($i >= $line_start && $i <= $line_end){
        $j++;
        if($j == 1){
            if($model == "nam" || $model == "namm" || $model == "rap" || $model == "ruc" || $model == "nam4km" || $model == "sref"){
                $stn[] = $d[0];
                $q = str_split($d[1]);
                $mdate[] = "20".$q[0]."".$q[1]."-".$q[2]."".$q[3]."-".$q[4]."".$q[5]." ".$q[7]."".$q[8].":00:00";
                $pmsl[] = $d[2];
                $pres[] = $d[3];
                $sktc[] = $d[4];
                $stc1[] = $d[5];
                $snfl[] = $d[6];
                $wtns[] = $d[7];
            }
                        elseif($model == "gfs" || $model == "gfsm"){
                $stn[] = $d[0];
                $q = str_split($d[1]);
                                $mdate[] = "20".$q[0]."".$q[1]."-".$q[2]."".$q[3]."-".$q[4]."".$q[5]." ".$q[7]."".$q[8].":00:00";
                                $pmsl[] = $d[2];
                                $pres[] = $d[3];
                                $sktc[] = $d[4];
                                $stc1[] = $d[5];
                                $evap[] = $d[6];
                if(count($pmsl) <= 121){
                    /*
                    if((count($pmsl)-2) % 3 == 0 || (count($pmsl)-1) % 3 == 0){
                        $offset1 = $p03m[count($p03m)-1];
                        $offset2 = $qpf[count($qpf)-1];
                        $p03m[] = $d[7] - $offset1;
                        $qpf = ($d[7] * 0.0393700787) - $offset2;
                        if($d[0] < 0){
                                                $qpf = 0;
                                        }
                                        $precip[] = $qpf; 
                                        $precip_accum[] = array_sum($precip);
                                }
                    else{
                        $p03m[] = $d[7];
                                                $qpf = $d[7] * 0.0393700787;
                                                if($d[0] < 0){
                                                        $qpf = 0;
                                                }
                                                $precip[] = $qpf;
                                                $precip_accum[] = array_sum($precip);
                    }
                    */
                    if((count($pmsl)-1) % 3 == 0){
                        $p03m[] = $d[7];
                                                $qpf = $d[7] * 0.0393700787;
                                                if($d[0] < 0){
                                                        $qpf = 0;
                                                }
                                                $precip[] = $qpf;
                                                $precip_accum[] = array_sum($precip);
                    }
                    else{
                                                $p03m[] = $d[7];
                                                $qpf = $d[7] * 0.0393700787;
                                                if($d[0] < 0){
                                                        $qpf = 0;
                                                }
                        //$p03m[] = 0;
                                                //$qpf = 0;
                                                $precip[] = $qpf;
                                                $precip_accum[] = array_sum($precip);
                    }
                }
                else{
                    $p03m[] = $d[7];
                                        $qpf = $d[7] * 0.0393700787;
                                        if($d[0] < 0){
                                                $qpf = 0;
                                        }
                                        $precip[] = $qpf;
                                        $precip_accum[] = array_sum($precip);
                }
            }
        }
        elseif($j == 2){
                        if($model == "nam" || $model == "namm" || $model == "rap" || $model == "ruc" || $model == "nam4km" || $model == "sref"){
                $p01m[] = $d[0];
                            $c01m[] = $d[1];
                            $stc2[] = $d[2];
                            $lcld[] = $d[3];
                            $mcld[] = $d[4];
                            $hcld[] = $d[5];
                $qpf = $d[0] * 0.0393700787;
                if($d[0] < 0){
                    $qpf = 0;
                }
                $precip[] = $qpf;
                $precip_accum[] = array_sum($precip);
            }
                        elseif($model == "gfs" || $model == "gfsm"){
                $c03m[] = $d[0];
                                $swem[] = $d[1];
                                $lcld[] = $d[2];
                                $mcld[] = $d[3];
                                $hcld[] = $d[4];
                                $uwnd[] = $d[5];
                $gfs_u = $d[5];
                        }
        }
                elseif($j == 3){
                        if($model == "nam" || $model == "namm" || $model == "rap" || $model == "ruc" || $model == "nam4km" || $model == "sref"){
                            $snra[] = $d[0];
                            $uwnd[] = $d[1];
                            $vwnd[] = $d[2];
                            $r01m[] = $d[3];
                            $bfgr[] = $d[4];
                            $t2ms[] = $d[5];
                $temp_c = $d[5];
                $temp_f = ((9/5) * $d[5]) + 32;
                                $tf[] = $temp_f; 
                $wind_10m = pow((($d[1]*$d[1])+($d[2]*$d[2])),(1/2))*2.23693629;
                $wspd[] = $wind_10m;
                                $wind_dir = @rad2deg(atan($d[2]/$d[1]));
                                if ($d[1] < 0 && $d[2] > 0) {
                                        $wind_dir = $wind_dir + 180;
                                }
                                if ($d[1] == 0 && $d[2] > 0) {
                                        $wind_dir = $wind_dir + 90;
                                }
                                elseif($d[1] < 0 && $d[2] <= 0) {
                                        $wind_dir = $wind_dir + 180;
                                }
                                $cam_ang = (($wind_dir * (-1)) + 270);
                                $wdir[] = $cam_ang;
            }
                        elseif($model == "gfs" || $model == "gfsm"){
                                $vwnd[] = $d[0];
                                $t2ms[] = $d[1];
                                $q2ms[] = $d[2];
                                $wxts[] = $d[3];
                                $wxtp[] = $d[4];
                                $wxtz[] = $d[5];
                $temp_c = $d[1];
                $temp_f = ((9/5) * $d[1]) + 32;
                $tf[] = $temp_f;
                $wind_10m = pow((($gfs_u*$gfs_u)+($d[0]*$d[0])),(1/2))*2.23693629;
                $wspd[] = $wind_10m;
                                $wind_dir = @rad2deg(atan($d[0]/$gfs_u));  
                                if ($gfs_u < 0 && $d[0] > 0) {  
                                        $wind_dir = $wind_dir + 180;
                                }
                                if ($gfs_u == 0 && $d[0] > 0) {  
                                        $wind_dir = $wind_dir + 90;
                                }
                                elseif($gfs_u < 0 && $d[0] <= 0) {  
                                        $wind_dir = $wind_dir + 180;
                                }
                                $cam_ang = (($wind_dir * (-1)) + 270);
                                $wdir[] = $cam_ang; 
            }
                }
                elseif($j == 4){
                        if($model == "nam" || $model == "namm" || $model == "rap" || $model == "ruc" || $model == "nam4km" || $model == "sref"){
                            $q2ms[] = $d[0];
                            $wxts[] = $d[1];
                            $wxtp[] = $d[2];
                            $wxtz[] = $d[3];
                            $wxtr[] = $d[4];
                            $ustm[] = $d[5];
            }
            elseif($model == "gfs" || $model == "gfsm"){
                $wxtr[] = $d[0];
                $s03m[] = $d[1];
                $td2m[] = $d[2];
                $dew_f = ((9/5) * $d[2]) + 32;
                $dew_c = $d[2];
                $td[] = $dew_f;
                $j = 0;
            }
                }
                elseif($j == 5){
                        if($model == "nam" || $model == "namm" || $model == "rap" || $model == "ruc" || $model == "nam4km" || $model == "sref"){
                            $vstm[] = $d[0];
                            $hlcy[] = $d[1];
                            $sllh[] = $d[2];
                            $wsym[] = $d[3];
                            $cdbp[] = $d[4];
                            $vsbk[] = $d[5] * 0.621371;
            }
                }
                elseif($j == 6){
                        if($model == "nam" || $model == "namm" || $model == "rap" || $model == "ruc" || $model == "nam4km" || $model == "sref"){
                            $td2m[] = $d[0];
                $dew_f = ((9/5) * $d[0]) + 32;
                $dew_c = $d[0];
                                $td[] = $dew_f; 
                $j = 0;
            }
        }
        if($j == 0){
                    $rh = 100 * (exp(((1/($dew_c + 273.15))-(1/($temp_c + 273.15)))/(-461.495/2500000)));
                        $hi = -42.379 + (2.04901523 * $temp_f) + (10.14333127 * $rh) + (-0.22475541 * $temp_f * $rh) + (-0.00683783 * $temp_f * $temp_f) + (-0.05481717 * $rh * $rh) + (0.00122874 * $temp_f * $temp_f * $rh) + (0.00085282 * $temp_f * $rh * $rh) + (-0.00000199 * $temp_f * $temp_f * $rh * $rh);
                        $wc = 35.74 + (0.6215 * $temp_f) - (35.75 * pow($wind_10m,0.16)) + ((0.4275 * $temp_f) * pow($wind_10m,0.16));
                        if($temp_f >= 80 && $dew_f >= 12){
                                $hi_wc[] = $hi;
                        }
                        elseif($temp_f >= 50 || $wind_10m == 0){
                                $hi_wc[] = $temp_f;
                        }
                        else{
                                $hi_wc[] = $wc;
                        }
            $rhs[] = $rh;
        }
    }
    elseif($trip == 1){
        // read 2D sounding data
        $k++;
        $int = $k/2;
        if(is_int($int)){
            if($model == "nam" || $model == "namm" || $model == "rap" || $model == "ruc" || $model == "nam4km" || $model == "sref"){
                $cfrl[$z][] = $d[0];
                $hght[$z][] = $d[1];
                $h_now = $d[1];
                if($k == 2){
                    $lr[] = "-------------------";
                    $h_last = $h_now;
                    $w_last = 0;
                }
            }
            elseif($model == "gfs" || $model == "gfsm"){
                $hght[$z][] = $d[0];
                                $h_now = $d[0];
                                if($k == 2){
                                        $h_last	= $h_now;
                    $w_last = 0;
                                }
            }
            // momentum transfer
            if($k > 2){
                                $dt = $t_now - $t_last2;
                                $dz = $h_now - $h_last;
                                $lapse_rate = ($dt / $dz) * 1000;
                $lr[] = $lapse_rate;
                                if($lapse_rate < -7){
                                        $mom_wind[] = $w_now;
                                }
                else{
                    if($mom_trip == 1){
                        if(count($mom_wind) == 0){
                            $mom_wind_mean[$z] = 0;
                        }
                        else{
                            $mom_wind_mean[$z] = round(array_sum(@$mom_wind)/count(@$mom_wind),2);
                                        $mom_wind_max[$z] = @$mom_wind[count(@$mom_wind) - 1];
                        }
                        //print_r($mom_wind);
                        //echo "".$mom_wind_mean[$z].",".$mom_wind_max[$z]."\n";
                    }
                    $mom_trip = 0;
                }
                // winds AGL
                $h_diff = $h_now - $selv;
                $h_diff2 = $h_last - $selv;
                if($hgt <= $h_diff && $hgt > $h_diff2){
                    $h_ratio = ($hgt - $h_diff2) / ($h_diff - $h_diff2);
                    $wagl[] = ($w_last + (($w_now - $w_last) * $h_ratio)) * 1.15077945;
                }
                                $t_last2 = $t_now;
                $h_last = $h_now;
                $w_last = $w_now;
                         }
        }
        else{
                        $prez[$z][] = $d[0];
                        $tmpc[$z][] = $d[1];
                        $tmwc[$z][] = $d[2];
                        $dwpc[$z][] = $d[3];
                        $thte[$z][] = $d[4];
                        $drct[$z][] = $d[5];
                        $sknt[$z][] = $d[6];
                        $omeg[$z][] = $d[7];
            $t_now = $d[1];
            $p_now = $d[0];
            $w_now = $d[6];
            // max temp in profile
            if($k == 1){
                $maxt[$z] = $d[1];
                $t_last1 = $d[1];
                $t_last2 = $t_now;
                $mom_wind = array();
                $w_last = $w_now;
            }
            elseif($d[1] > $t_last1){
                $maxt[$z] = $d[1];
                                $t_last1 = $d[1];
            }
        }		
    }
    elseif($i >= $prof_begin && $i < $line_start){
        if(@$d[0] == "CFRL" || @$d[0] == "HGHT"){
            $trip = 1;		
            $mom_trip = 1;
            $z++;
        }
        elseif(@$d[0] == "SHOW"){
            $show[] = $d[2];
            $lift[] = $d[5];
            $swet[] = $d[8];
            $kinx[] = $d[11];
        }
            elseif(@$d[0] == "LCLP"){
                    $lclp[]	= $d[2];
                    $pwat[]	= $d[5] * 0.0393700787;
                    $totl[]	= $d[8];
                    $cape[] = $d[11];
            }
            elseif(@$d[0] == "LCLT"){
                    $lclt[] = $d[2];
                    $cins[]	= $d[5];
                    $eqlv[]	= $d[8];
                    $lfct[] = $d[11];
            }
            elseif(@$d[0] == "BRCH"){
                    $brch[] = $d[2];
            }
        elseif(@$d[6] == "SELV"){
            $selv = $d[8];
        }
    }
}

if(empty($start_time) && empty($end_time)){
    $start_time = strtotime(@$mdate[0]);
    $end_time = strtotime(@$mdate[$hrs]);
}

for($i=-1;$i<=$hrs;$i++){
    if($i == -1){
        for($j=0;$j<=$vars-1;$j++){
            echo "".$var[$j]."\t";
        }
        echo "".$var[$vars]."\n";
    }
    elseif($i == 0 && $model == "nam4km"){
        continue;
    }
    else{
        if(@$wxts[$i] == 1 || @$wsym[$i] == 70 || @$wsym[$i] == 71 || @$wsym[$i] == 72 || @$wsym[$i] == 73 || @$wsym[$i] == 74 || @$wsym[$i] == 75){
                // calculate max temp in profile snow ratio
                    if(@$maxt[$i] >= 2){
                            $maxr = 0;
                    }
                    elseif(@$maxt[$i] >= 0){
                            //$m = -8;
                            //$b = 16;
                            //$maxr = round((($m*$maxt[$i]) + $b),0);
                $maxr = 10;
                    }
                    elseif(@$maxt[$i] >= -10){
                            $m = -17/11;
                            $b = 10;
                            $maxr = round((($m*$maxt[$i]) + $b),0);
                    }
                    elseif(@$maxt[$i] >= 18){
                            $maxr = 25;
                    }
                    elseif(@$maxt[$i] >= 22){
                               $m = 5/2;
                            $b = 67.5;
                            $maxr = round((($m*$maxt[$i]) + $b),0);
                    }
                    elseif(@$maxt[$i] < 22){
                            $maxr = 15;
                    }
        }
        else{
            $maxr = 0;
        }
        if($model == "nam" || $model == "namm" || $model == "rap" || $model == "ruc" || $model == "nam4km" || $model == "sref"){
            if(@$wsym[$i] >= 70 && @$wsym[$i] <= 75){
                $buf_snow_sr = @round($p01m[$i] * $ratio * 0.0393700787,1);
            }
            else{
                $buf_snow_sr = 0;
            }
            $buf_snow_maxt = @round($p01m[$i] * $maxr * 0.0393700787,1);
            if($wxtz[$i] == 1){                 
                                $frz_rain = $frz_rain + ($p01m[$i] * 0.0393700787);    
                        }
            if($wxtp[$i] == 1){                 
                                $sleet = $sleet + ($p01m[$i] * 0.0393700787);    
                        }
        }
        elseif($model == "gfs" || $model == "gfsm"){
            if(@$wxts[$i] == 1){
                            $buf_snow_sr = @round(@$p03m[$i] * $ratio * 0.0393700787,1);
            }
            else{
                                $buf_snow_sr = 0;
                        }
                        $buf_snow_maxt = @round(@$p03m[$i] * $maxr * 0.0393700787,1);
            if($wxtz[$i] == 1){                 
                            $frz_rain = $frz_rain + ($p03m[$i] * 0.0393700787);    
                    }
            if($wxtp[$i] == 1){                 
                                $sleet = $sleet + ($p03m[$i] * 0.0393700787);    
                        }
        }
        if(@$mom_wind_mean[$i] == 0){
            @$mom_wind_mean[$i] = @$wspd[$i];
        }
                if(@$mom_wind_max[$i] == 0){
                        @$mom_wind_max[$i] = @$wspd[$i];
                }
        if($i == 0){
            $buf_snow_sr_rate[$i] = 0;
            $buf_snow_maxt_rate[$i] = 0;
        }
        elseif($model == "gfs" || $model == "gfsm"){
            $s = explode(" ",$mdate[$i]);
            $curhr = explode(":",$s[1]);
            if($curhr[0] % 3 == 0){
                $buf_snow_sr_rate[$i] = $buf_snow_sr / 3;
                            $buf_snow_maxt_rate[$i] = $buf_snow_maxt / 3;
            }
            else{
                $buf_snow_sr_rate[$i] = 0;
                            $buf_snow_maxt_rate[$i] = 0;
            }
        }
        else{
            $buf_snow_sr_rate[$i] = $buf_snow_sr;
            $buf_snow_maxt_rate[$i] = $buf_snow_maxt;
        }
        if(strtotime(@$mdate[$i]) >= $start_time && strtotime(@$mdate[$i]) <= $end_time){
            echo "".@$stn[$i]."\t".@$mdate[$i]."\t".@$pmsl[$i]."\t".@$pres[$i]."\t".@$sktc[$i]."\t".@$stc1[$i]."\t".@$snfl[$i]."\t".@$wtns[$i]."\t".@$p01m[$i]."\t".@$c01m[$i]."\t".@$stc2[$i]."\t".@$lcld[$i]."\t".@$mcld[$i]."\t".@$hcld[$i]."\t".@$snra[$i]."\t".@$uwnd[$i]."\t".@$vwnd[$i]."\t".@$r01m[$i]."\t".@$bfgr[$i]."\t".@$t2ms[$i]."\t".@$q2ms[$i]."\t".@$wxts[$i]."\t".@$wxtp[$i]."\t".@$wxtz[$i]."\t".@$wxtr[$i]."\t".@$ustm[$i]."\t".@$vstm[$i]."\t".@$hlcy[$i]."\t".@$sllh[$i]."\t".@$wsym[$i]."\t".@$cdbp[$i]."\t".@$vsbk[$i]."\t".@$td2m[$i]."\t".@$evap[$i]."\t".@$p03m[$i]."\t".@$c03m[$i]."\t".@$swem[$i]."\t".@$s03m[$i]."\t".@$show[$i]."\t".@$lift[$i]."\t".@$swet[$i]."\t".@$kinx[$i]."\t".@$lclp[$i]."\t".@$pwat[$i]."\t".@$totl[$i]."\t".@$cape[$i]."\t".@$lclt[$i]."\t".@$cins[$i]."\t".@$eqlv[$i]."\t".@$lfct[$i]."\t".@$brch[$i]."\t".@$buf_snow_sr."\t".@$buf_snow_maxt."\t".@$ratio."\t".@$maxr."\t".@$maxt[$i]."\t".@$mom_wind_mean[$i]."\t".@$mom_wind_max[$i]."\t".@round($tf[$i],1)."\t".@round($td[$i],1)."\t".@$wspd[$i]."\t".@$wdir[$i]."\t".@$hi_wc[$i]."\t".@$precip[$i]."\t".@$precip_accum[$i]."\t".@$wagl[$i]."\t".$frz_rain."\t".$sleet."\t".$rhs[$i]."\t".@$buf_snow_sr_rate[$i]."\t".@$buf_snow_maxt_rate[$i]."\t".@$mdate[0]."\n";
        }
    }
}

fclose($fh);

if($model == "sref" && $unleash == 1){
    system("rm $sref_file");
}
