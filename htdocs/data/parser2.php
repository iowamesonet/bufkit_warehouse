<?php
require_once "../../config/settings.php";
require_once "../../include/forms.php";
require_once "../../include/util.php";

putenv("TZ=UTC");
date_default_timezone_set('UTC');

header('Content-type: text/plain');

$model = get_str404("model", "nam");
$member = get_str404("member", "1");
$site = get_str404("site", "kdsm");
$ratio = get_str404("ratio", "11");
$hgt = get_str404("hgt", "80");
$psfc = get_str404("psfc", "500");
$z0 = get_str404("z0", "11");
$unleash = get_str404("unleash", "0");
$date = get_str404("date", "");
$start_time = get_str404("start_time", "");
$end_time = get_str404("end_time", "");

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

$europe = array('stpb', 'berl');
$sites = array();
// check if site is in master list.  If not, terminate script and tell user
for ($z = 0; $z <= 1; $z++) {
    if ($z == 0) {
        $master_list = "/opt/bufkit/bufrgruven/stations/nam_bufrstations.txt";
    } elseif ($z == 1) {
        $master_list = "/opt/bufkit/bufrgruven/stations/gfs3_bufrstations.txt";
    }
    $data = file($master_list);
    foreach ($data as $line) {
        $d = explode(" ", trim(preg_replace('/\s+/', ' ', $line)));
        $sites[] = strtolower($d[3]);
    }
}
if (!(in_array($site, $sites)) && !(in_array($site, $europe))) {
    die("Site " . $site . " is not available.  Try again.");
}

if ($date != "" && strlen($date) == 10) {
    $d = str_split($date);
    $year = "" . $d[0] . "" . $d[1] . "" . $d[2] . "" . $d[3] . "";
    $mon = "" . $d[4] . "" . $d[5] . "";
    $day = "" . $d[6] . "" . $d[7] . "";
    $hr = "" . $d[8] . "" . $d[9] . "";
    $dCheck = strtotime($d[0] . "" . $d[1] . "" . $d[2] . "" . $d[3] . "-" . $d[4] . "" . $d[5] . "-" . $d[6] . "" . $d[7] . " " . $d[8] . "" . $d[9] . ":00:00 UTC");
} else {
    $dCheck = strtotime(date("Y-m-d H:i:s"));
}


$var = array('stn', 'date', 'pmsl', 'pres', 'sktc', 'stc1', 'snfl', 'wtns', 'p01m', 'c01m', 'stc2', 'lcld', 'mcld', 'hcld', 'snra', 'uwnd', 'vwnd', 'r01m', 'bfgr', 't2ms', 'q2ms', 'wxts', 'wxtp', 'wxtz', 'wxtr', 'ustm', 'vstm', 'hlcy', 'sllh', 'wsym', 'cdbp', 'vsbk', 'td2m', 'evap', 'p03m', 'c03m', 'swem', 's03m', 'show', 'lift', 'swet', 'kinx', 'lclp', 'pwat', 'totl', 'cape', 'lclt', 'cins', 'eqlv', 'lfct', 'brch', 'buf_snow_sr', 'buf_snow_maxt', 'snra_constant', 'snra_maxt', 'maxt', 'mom_wind_mean', 'mom_wind_max', 'tf', 'td', 'wspd', 'wdir', 'hiwc', 'qpf', 'qpf_accum', 'wagl', 'frz_rain', 'sleet', 'rh', 'buf_snow_sr_rate', 'buf_snow_maxt_rate', 'init');

$vars = count($var) - 1;

//intialize some of the arrays
for ($y = 0; $y <= 200; $y++) {
    if ($model == "nam" || $model == "namm" || $model == "rap" || $model == "nam4km") {
        $evap[] = 0;
        $p03m[] = 0;
        $c03m[] = 0;
        $swem[] = 0;
        $s03m[] = 0;
    } elseif ($model == "gfs" || $model == "gfsm") {
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

if ($model == "nam") {
    if ($date == "") {
        $link = METFS1 . "bufkit/nam/nam_" . $site . ".buf";
    } else {
        $link = MTARCHIVE . $year . "/" . $mon . "/" . $day . "/bufkit/" . $hr . "/nam/nam_" . $site . ".buf";
    }
    $line_start = 11230;
    $line_end = 11739;
    $hrs = 84;
} elseif ($model == "namm") {
    if ($date == "") {
        $link = METFS1 . "bufkit/namm/namm_" . $site . ".buf";
    } else {
        $link = MTARCHIVE . $year . "/" . $mon . "/" . $day . "/bufkit/" . $hr . "/nam/namm_" . $site . ".buf";
    }
    $line_start = 11230;
    $line_end = 11739;
    $hrs = 84;
} elseif ($model == "nam4km") {
    $link = METFS1 . "bufkit/nam4km/nam4km_" . $site . ".buf";
    $line_start = 8062;
    $line_end = 8427;
    $hrs = 60;
} elseif ($model == "gfs") {
    if ($date == "") {
        $link = METFS1 . "bufkit/gfs/gfs3_" . $site . ".buf";
    } else {
        $link = MTARCHIVE . $year . "/" . $mon . "/" . $day . "/bufkit/" . $hr . "/gfs/gfs3_" . $site . ".buf";
    }
    if ($date == "") {
        $line_start = 19748;
        $line_end = 20311;
        $hrs = 140;
    } elseif ($dCheck < 1500638400) {
        $line_start = 8548;
        $line_end = 8791;
        $hrs = 60;
    } else {
        $line_start = 19748;
        $line_end = 20311;
        $hrs = 140;
    }
} elseif ($model == "gfsm") {
    if ($date == "") {
        $link = METFS1 . "bufkit/gfsm/gfs3_" . $site . ".buf";
    } else {
        $link = MTARCHIVE . $year . "/" . $mon . "/" . $day . "/bufkit/" . $hr . "/gfs/gfs3_" . $site . ".buf";
    }
    if ($date == "") {
        $line_start = 19748;
        $line_end = 20311;
        $hrs = 140;
    }
    if ($dCheck < 1500670800) {
        $line_start = 8548;
        $line_end = 8791;
        $hrs = 60;
    } else {
        $line_start = 19748;
        $line_end = 20311;
        $hrs = 140;
    }
} elseif ($model == "rap" || $model == "ruc") {
    if ($date == "" && $model == "rap") {
        $link = MTARCHIVE . "bufkit/rap/rap_" . $site . ".buf";
        $line_start = 2474;
        $line_end = 2605;
        $hrs = 21;
    } elseif ($dCheck >= 1471953600) {
        $link = MTARCHIVE . $year . "/" . $mon . "/" . $day . "/bufkit/" . $hr . "/rap/rap_" . $site . ".buf";
        $line_start = 2474;
        $line_end = 2605;
        $hrs = 21;
    } elseif ($year >= 2012 && $mon >= 05 && $day >= 01) {
        $link = MTARCHIVE . $year . "/" . $mon . "/" . $day . "/bufkit/" . $hr . "/rap/rap_" . $site . ".buf";
        $line_start = 2138;
        $line_end = 2251;
        $hrs = 18;
    } else {
        $link = MTARCHIVE . $year . "/" . $mon . "/" . $day . "/bufkit/" . $hr . "/ruc/ruc_" . $site . ".buf";
        $line_start = 2138;
        $line_end = 2251;
        $hrs = 18;
    }
}

$fh = cached_fetch($link, 600);
if ($fh === false) {
    die("$link is not available");
}

$maxtime = new DateTime('1970-01-01 00:00:00', new DateTimeZone('UTC'));
$mintime = new DateTime('2050-01-01 00:00:00', new DateTimeZone('UTC'));
foreach (explode("\n", $fh) as $line) {
    // Look for TIME = on this line
    if (strpos($line, "TIME =") !== false) {
        $d = explode("TIME =", $line);
        $dtime = "20" . trim($d[1]);
        $dt = DateTime::createFromFormat('Ymd/Hi', $dtime, new DateTimeZone('UTC'));
        if ($dt === false){
            continue;
        }
        $timestamp = $dt->getTimestamp();
        if ($dt > $maxtime) {
            $maxtime = $dt;
        }
        if ($dt < $mintime) {
            $mintime = $dt;
        }
    }
}
echo sprintf("%s,%s", $mintime->format("Y-m-d H:i"), $maxtime->format("Y-m-d H:i"));
