<?php
require_once "../../config/settings.php";

function xssafe($data, $encoding = 'UTF-8')
{
    if (is_array($data)) {
        return $data;
    }
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
}


// Author:	Chris Karstens
// Date:	February 13, 2012
// Version:	PHP, JPGraph
// Purpose:	Generates meteogram from user-specified variables using available data

//putenv("TZ=UTC");
date_default_timezone_set('UTC');

if (isset($argv)) {
    for ($i = 1; $i < count($argv); $i++) {
        $it = split("=", $argv[$i]);
        $_GET[$it[0]] = $it[1];
    }
}


$hgt = isset($_GET["hgt"]) ? xssafe($_GET["hgt"]) : "80";
$ratio = isset($_GET["ratio"]) ? xssafe($_GET["ratio"]) : "11";


$vars_available = array('stn', 'date', 'pmsl', 'pres', 'sktc', 'stc1', 'snfl', 'wtns', 'p01m', 'c01m', 'stc2', 'lcld', 'mcld', 'hcld', 'snra', 'uwnd', 'vwnd', 'r01m', 'bfgr', 't2ms', 'q2ms', 'wxts', 'wxtp', 'wxtz', 'wxtr', 'ustm', 'vstm', 'hlcy', 'sllh', 'wsym', 'cdbp', 'vsbk', 'td2m', 'evap', 'p03m', 'c03m', 'swem', 's03m', 'show', 'lift', 'swet', 'kinx', 'lclp', 'pwat', 'totl', 'cape', 'lclt', 'cins', 'eqlv', 'lfct', 'brch', 'buf_snow_sr', 'buf_snow_maxt', 'snra_constant', 'snra_maxt', 'maxt', 'mom_wind_mean', 'mom_wind_max', 'tf', 'td', 'wspd', 'wdir', 'hiwc', 'qpf', 'qpf_accum', 'wagl', 'frz_rain', 'sleet', 'rh', 'buf_snow_sr_rate', 'buf_snow_maxt_rate');

$y_labels = array('stn', 'date', 'MSLP (mb)', 'SFC Pressure (mb)', 'sktc', 'stc1', 'snfl', 'wtns', 'QPF (mm)', 'c01m', 'stc2', 'lcld', 'mcld', 'hcld', 'snra', 'U-Wind (m/s)', 'V-Wind (m/s)', 'r01m', 'bfgr', 'Temp (C)', 'q2ms', 'wxts', 'wxtp', 'Freezing Rain Category', 'wxtr', 'ustm', 'vstm', 'Helicity (m^2/s^2)', 'sllh', 'wsym', 'cdbp', 'vsbk', 'Dewpoint (C)', 'evap', 'QPF (mm)', 'c03m', 'swem', 's03m', 'show', 'lift', 'swet', 'kinx', 'lclp', 'pwat', 'totl', 'CAPE (J/kg))', 'lclt', 'cins', 'eqlv', 'lfct', 'brch', 'Snow (in.)', 'Snow (in.)', 'Snow Ratio', 'Snow Ratio', 'Max-T (C)', 'Mean Mom. Trans. Wind (mph)', 'Max Mom. Trans. Wind (mph)', 'Temp (F)', 'Dewpoint (F)', 'Wind speed (mph)', 'Wind Direction (Deg.)', 'Feels-Like Temp (F)', 'QPF (in.)', 'QPF (in.)', 'Wind Speed (mph)', 'Freezing Rain (in.)', 'Sleet (in.)', 'Relative Humidity (%)', 'Snow Rate (in./hr)', 'Snow Rate (in./hr)');

$titles = array('stn', 'date', 'Mean Sea Level Pressure', 'Surface Pressure', 'sktc', 'stc1', 'snfl', 'wtns', '1-Hour QPF', 'c01m', 'stc2', 'lcld', 'mcld', 'hcld', 'snra', 'U-Wind', 'V_Wind', 'r01m', 'bfgr', 'Temperature', 'q2ms', 'wxts', 'wxtp', 'Freezing Rain Category', 'wxtr', 'ustm', 'vstm', '0-3 km Helicity', 'sllh', 'wsym', 'cdbp', 'vsbk', 'Dewpoint', 'evap', '3-Hour QPF', 'c03m', 'swem', 's03m', 'show', 'lift', 'swet', 'kinx', 'lclp', 'pwat', 'totl', 'CAPE', 'lclt', 'cins', 'eqlv', 'lfct', 'brch', 'Snowfall', 'Snowfall', 'Constant Snow Ratio', 'Max-T in Profile Snow Ratio', 'Max Temp in Profile', 'Wind Gust', 'Wind Gust', 'Temperature', 'Dewpoint', 'Wind Speed', 'Wind Direction', 'Apparent Temperature', 'Precip', 'Precip Accumulation', '' . $hgt . ' m AGL Wind Speed', 'Freezing Rain Accumulation', 'Sleet Accumulation', 'Relative Humidity', 'Snow Rate (' . $ratio . ':1 Ratio)', 'Snow Rate (Max-T Method)');

$now = strtotime("now");
$site = isset($_GET["site"]) ? xssafe($_GET["site"]) : "kdsm";
$var = isset($_GET["var"]) ? xssafe($_GET["var"]) : "tf";
$nam = isset($_GET["nam"]) ? xssafe($_GET["nam"]) : "1";
$namm = isset($_GET["namm"]) ? xssafe($_GET["namm"]) : "1";
$gfs = isset($_GET["gfs"]) ? xssafe($_GET["gfs"]) : "1";
$gfsm = isset($_GET["gfsm"]) ? xssafe($_GET["gfsm"]) : "1";
$rap = isset($_GET["rap"]) ? xssafe($_GET["rap"]) : "1";
$nam4km = isset($_GET["nam4km"]) ? xssafe($_GET["nam4km"]) : "1";
$nam_mos = isset($_GET["nam_mos"]) ? xssafe($_GET["nam_mos"]) : "1";
$namm_mos = isset($_GET["namm_mos"]) ? xssafe($_GET["namm_mos"]) : "1";
$gfs_mos = isset($_GET["gfs_mos"]) ? xssafe($_GET["gfs_mos"]) : "1";
$gfsm_mos = isset($_GET["gfsm_mos"]) ? xssafe($_GET["gfsm_mos"]) : "1";
$con = isset($_GET["con"]) ? xssafe($_GET["con"]) : "1";
$obs = isset($_GET["obs"]) ? xssafe($_GET["obs"]) : "1";
$nws = isset($_GET["nws"]) ? xssafe($_GET["nws"]) : "1";
$compaction = isset($_GET["compaction"]) ? xssafe($_GET["compaction"]) : "1";
$cobb = isset($_GET["cobb"]) ? xssafe($_GET["cobb"]) : "1";
$max_t = isset($_GET["max_t"]) ? xssafe($_GET["max_t"]) : "1";
$mean_mt = isset($_GET["mean_mt"]) ? xssafe($_GET["mean_mt"]) : "1";
$max_mt = isset($_GET["max_mt"]) ? xssafe($_GET["max_mt"]) : "1";
$mean = isset($_GET["mean"]) ? xssafe($_GET["mean"]) : "1";
$freese = isset($_GET["freese"]) ? xssafe($_GET["freese"]) : "no";
$date = isset($_GET["date"]) ? xssafe($_GET["date"]) : "";
$start_time = isset($_GET["start_time"]) ? xssafe($_GET["start_time"]) : "";
$end_time = isset($_GET["end_time"]) ? xssafe($_GET["end_time"]) : "";

//$cobb = 0;

$nam4km_cobb_time = array();

if (!empty($start_time) and !empty($end_time)) {
    $s = str_split($start_time);
    $start = strtotime($s[0] . $s[1] . $s[2] . $s[3] . "-" . $s[4] . $s[5] . "-" . $s[6] . $s[7] . " " . $s[8] . $s[9] . ":" . $s[10] . $s[11] . ":" . $s[12] . $s[13]);
    $s = str_split($end_time);
    $end = strtotime($s[0] . $s[1] . $s[2] . $s[3] . "-" . $s[4] . $s[5] . "-" . $s[6] . $s[7] . " " . $s[8] . $s[9] . ":" . $s[10] . $s[11] . ":" . $s[12] . $s[13]);
}
if ($var == "frz_rain" || $var == "sleet") {
    $nws = 0;
}

$site_upper_case = strtoupper($site);
$a = -0.08;
if ($var == "snow_accum") {
    $var1 = "snow_accum";
    $var = "buf_snow_sr";
} elseif ($var == "wind") {
    $var1 = "wind";
    $var = "wspd";
} else {
    $var1 = "";
}

// check if site is in master list.  If not, terminate script and tell user
$found = 0;
for ($z = 0; $z <= 1; $z++) {
    if ($z == 0) {
        $master_list = "nam_bufrstations.txt";
    } elseif ($z == 1) {
        $master_list = "gfs3_bufrstations.txt";
    }
    $data = file($master_list);
    $sites = array();
    foreach ($data as $line) {
        $d = explode(" ", trim(preg_replace('/\s+/', ' ', $line)));
        //print_r($d);
        //echo $line;
        $sites[] = strtolower($d[3]);
        if ($site == strtolower($d[3])) {
            $found = 1;
            if ($z == 0) {
                if ($site == "kaus") {
                    $lat = $d[1] - 0.1;
                    $lon = $d[2];
                } else {
                    $lat = $d[1];
                    $lon = $d[2];
                }
            } elseif ($z == 1) {
                if (strpbrk($d[1], "N")) {
                    $lat = trim($d[1], "N");
                } else {
                    $lat = trim($d[1], "S") * -1;
                }
                if (strpbrk($d[2], "E")) {
                    $lon = trim($d[2], "E");
                } else {
                    $lon = trim($d[2], "W") * -1;
                }
            }
            break;
        }
    }
    if ($found == 1) {
        break;
    }
}
if (!(in_array($site, $sites))) {
    $bad = imagecreatefrompng("not_available.png");
    header('Content-Type: image/png');
    imagepng($bad);
    die();
}

if ($date != "" && strlen($date) == 10) {
    $d = str_split($date);
    $year = "" . $d[0] . "" . $d[1] . "" . $d[2] . "" . $d[3] . "";
    $mon = "" . $d[4] . "" . $d[5] . "";
    $day = "" . $d[6] . "" . $d[7] . "";
    $hr = "" . $d[8] . "" . $d[9] . "";
    if (checkdate($mon, $day, $year) == False) {
        $bad = imagecreatefrompng("not_available.png");
        header('Content-Type: image/png');
        imagepng($bad);
        die();
    }
}

// convert # symbol for some sites
$sym = "#";
$site_l = $site;
preg_match_all(".$sym.", $site, $id);
$check1 = @$id[0][0];
if ($check1 == $sym) {
    $exam = str_split($site);
    if ($exam[0] == $sym) {
        $exam[0] = "%23";
    }
    if ($exam[1] == $sym) {
        $exam[1] = "%23";
    }
    if ($exam[2] == $sym) {
        $exam[2] = "%23";
    }
    $site_l = "" . $exam[0] . "" . $exam[1] . "" . $exam[2] . "";
}

$mins = array();
$maxs = array();
for ($z = 0; $z <= 5; $z++) {
    if ($z == 0) {
        $mdl = "nam";
        $dt = 1;
        $nam_var = array();
        $nam_var1 = array();
        $nam_var2 = array();
        $nam_var3 = array();
        $buf_t_nam = array();
        $nam_uwnd = array();
        $nam_vwnd = array();
        if ($date != "") {
            if ($hr >= 0 && $hr <= 11) {
                $parse_date = "" . $year . "" . $mon . "" . $day . "00";
            } else {
                $parse_date = "" . $year . "" . $mon . "" . $day . "12";
            }
        }
    } elseif ($z == 1) {
        $mdl = "namm";
        $dt = 1;
        $namm_var = array();
        $namm_var1 = array();
        $namm_var2 = array();
        $namm_var3 = array();
        $buf_t_namm = array();
        $namm_uwnd = array();
        $namm_vwnd = array();
        if ($date != "") {
            if ($hr >= 0 && $hr <= 5) {
                $temp_date = strtotime("" . $year . "-" . $mon . "-" . $day . " 00:00:00") - 21600;
                $parse_date = date("YmdH", $temp_date);
            } elseif ($hr >= 6 && $hr <= 17) {
                $parse_date = "" . $year . "" . $mon . "" . $day . "06";
            } else {
                $parse_date = "" . $year . "" . $mon . "" . $day . "18";
            }
        }
    } elseif ($z == 2) {
        $mdl = "gfs";
        $dt = 3;
        $gfs_var = array();
        $gfs_var1 = array();
        $gfs_var2 = array();
        $gfs_var3 = array();
        $buf_t_gfs = array();
        $gfs_uwnd = array();
        $gfs_vwnd = array();
        if ($date != "") {
            if ($hr >= 0 && $hr <= 11) {
                $parse_date = "" . $year . "" . $mon . "" . $day . "00";
            } else {
                $parse_date = "" . $year . "" . $mon . "" . $day . "12";
            }
        }
        @$parse_date_gfs = $parse_date;
    } elseif ($z == 3) {
        $mdl = "gfsm";
        $dt = 3;
        $gfsm_var = array();
        $gfsm_var1 = array();
        $gfsm_var2 = array();
        $gfsm_var3 = array();
        $buf_t_gfsm = array();
        $gfsm_uwnd = array();
        $gfsm_vwnd = array();
        if ($date != "") {
            if ($hr >= 0 && $hr <= 5) {
                $temp_date = strtotime("" . $year . "-" . $mon . "-" . $day . " 00:00:00") - 21600;
                $parse_date = date("YmdH", $temp_date);
            } elseif ($hr >= 6 && $hr <= 17) {
                $parse_date = "" . $year . "" . $mon . "" . $day . "06";
            } else {
                $parse_date = "" . $year . "" . $mon . "" . $day . "18";
            }
        }
        @$parse_date_gfsm = $parse_date;
    } elseif ($z == 4) {
        $mdl = "rap";
        $dt = 1;
        $rap_var = array();
        $rap_var1 = array();
        $rap_var2 = array();
        $rap_var3 = array();
        $buf_t_rap = array();
        $rap_uwnd = array();
        $rap_vwnd = array();
        $parse_date = $date;
    } elseif ($z == 5) {
        $mdl = "nam4km";
        $dt = 1;
        $nam4km_var = array();
        $nam4km_var1 = array();
        $nam4km_var2 = array();
        $nam4km_var3 = array();
        $buf_t_nam4km = array();
        $nam4km_uwnd = array();
        $nam4km_vwnd = array();
        if ($date != "") {
            if ($hr >= 0 && $hr <= 11) {
                $parse_date = "" . $year . "" . $mon . "" . $day . "00";
            } else {
                $parse_date = "" . $year . "" . $mon . "" . $day . "12";
            }
        }
    }
    $z2 = 0;
    $tz2 = 0;

    if ($date == "") {
        $link = ROOTURL . "data/parser.php?model=" . $mdl . "&site=" . $site_l . "&hgt=" . $hgt . "&ratio=" . $ratio . "&start_time=" . $start . "&end_time=" . $end;
    } else {
        $link = ROOTURL . "data/parser.php?model=" . $mdl . "&site=" . $site_l . "&hgt=" . $hgt . "&date=" . $parse_date . "&ratio=" . $ratio . "&start_time=" . $start . "&end_time=" . $end;
    }
    $temp_maxt = 0;
    $temp_sr = 0;
    $data = file($link);
    $snow = array();
    $snow1 = array();
    $h = -1;
    foreach ($data as $line) {
        $z2++;
        $h2 = $z2 - 2;
        if ($z2 == 1) {
            // determine variable to plot
            $d = explode("\t", trim($line));
            if (array_search($var, $d)) {
                if ($var1 == "snow_accum") {
                    $index1 = array_search("buf_snow_maxt", $d);
                } elseif ($var1 == "wind") {
                    $index1 = array_search("mom_wind_mean", $d);
                    $index2 = array_search("mom_wind_max", $d);
                }
                $index = array_search($var, $d);
                $uwnd_index = array_search("uwnd", $d);
                $vwnd_index = array_search("vwnd", $d);
                $y_label = $y_labels[$index];
                $title = $titles[$index];
            } else {
                die("Variable " . $var . " is not available.  Try again.");
            }
        }
        if ($z2 > 1) {
            $h++;
            $d = explode("\t", trim($line));
            if ($z2 == 2) {
                $d[51] = 0;
                $d[52] = 0;
            }
            if ($z == 0) {
                if ($var1 == "snow_accum") {
                    $snow[] = $d[$index];
                    $snow1[] = $d[$index1];
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        $temp_cobb1 = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($snow[$i] * $exp);
                            $temp_cobb1 = $temp_cobb1 + ($snow1[$i] * $exp);
                        }
                        if ($ratio != 0) {
                            $nam_var[] = $temp_cobb;
                        }
                        $nam_var1[] = $temp_cobb1;
                    } else {
                        if ($ratio != 0) {
                            $nam_var[] = array_sum($snow);
                        }
                        $nam_var1[] = array_sum($snow1);
                    }
                } elseif ($var1 == "wind") {
                    $nam_var[] = $d[$index];
                    if ($d[$index1] == 0) {
                        $nam_var1[] = $d[$index];
                    } else {
                        $nam_var1[] = $d[$index1] * 1.15077945;
                    }
                    if ($d[$index2] == 0) {
                        $nam_var2[] = $d[$index];
                    } else {
                        $nam_var2[] = $d[$index2] * 1.15077945;
                    }
                } else {
                    $nam_var[] = $d[$index];
                    $nam_uwnd[] = $d[$uwnd_index];
                    $nam_vwnd[] = $d[$vwnd_index];
                }
                $buf_t_nam[] = strtotime($d[1]);
                if ($h2 == 0) {
                    $buf_nam_init = date("H", strtotime($d[count($d) - 1]));
                    $nam_init = strtotime($d[count($d) - 1]);
                }
            } elseif ($z == 1) {
                if ($var1 == "snow_accum") {
                    $snow[] = $d[$index];
                    $snow1[] = $d[$index1];
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        $temp_cobb1 = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($snow[$i] * $exp);
                            $temp_cobb1 = $temp_cobb1 + ($snow1[$i] * $exp);
                        }
                        if ($ratio != 0) {
                            $namm_var[] = $temp_cobb;
                        }
                        $namm_var1[] = $temp_cobb1;
                    } else {
                        if ($ratio != 0) {
                            $namm_var[] = array_sum($snow);
                        }
                        $namm_var1[] = array_sum($snow1);
                    }
                } elseif ($var1 == "wind") {
                    $namm_var[] = $d[$index];
                    if ($d[$index1] == 0) {
                        $namm_var1[] = $d[$index];
                    } else {
                        $namm_var1[] = $d[$index1] * 1.15077945;
                    }
                    if ($d[$index2] == 0) {
                        $namm_var2[] = $d[$index];
                    } else {
                        $namm_var2[] = $d[$index2] * 1.15077945;
                    }
                } else {
                    $namm_var[] = $d[$index];
                    $namm_uwnd[] = $d[$uwnd_index];
                    $namm_vwnd[] = $d[$vwnd_index];
                }
                $buf_t_namm[] = strtotime($d[1]);
                if ($h2 == 0) {
                    $buf_namm_init = date("H", strtotime($d[count($d) - 1]));
                    $namm_init = strtotime($d[count($d) - 1]);
                }
            } elseif ($z == 2 && $var != "hlcy") {
                if ($var1 == "snow_accum" || $var == "qpf_accum" || $var == "frz_rain" || $var == "sleet" || $var == "buf_snow_sr_rate" || $var == "buf_snow_maxt_rate") {
                    if (($z2 - 2) % 3 != 0 && ($z2 - 2) <= 120) {
                        //continue;
                    }
                }
                if ($var1 == "snow_accum") {
                    $snow[] = $d[$index];
                    $snow1[] = $d[$index1];
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        $temp_cobb1 = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($snow[$i] * $exp);
                            $temp_cobb1 = $temp_cobb1 + ($snow1[$i] * $exp);
                        }
                        if ($ratio != 0) {
                            $gfs_var[] = $temp_cobb;
                        }
                        $gfs_var1[] = $temp_cobb1;
                    } else {
                        if ($ratio != 0) {
                            $gfs_var[] = array_sum($snow);
                        }
                        $gfs_var1[] = array_sum($snow1);
                    }
                } elseif ($var1 == "wind") {
                    $gfs_var[] = $d[$index];
                    if ($d[$index1] == 0) {
                        $gfs_var1[] = $d[$index];
                    } else {
                        $gfs_var1[] = $d[$index1] * 1.15077945;
                    }
                    if ($d[$index2] == 0) {
                        $gfs_var2[] = $d[$index];
                    } else {
                        $gfs_var2[] = $d[$index2] * 1.15077945;
                    }
                } else {
                    $gfs_var[] = $d[$index];
                    $gfs_uwnd[] = $d[$uwnd_index];
                    $gfs_vwnd[] = $d[$vwnd_index];
                }
                $buf_t_gfs[] = strtotime($d[1]);
                if ($h2 == 0) {
                    $buf_gfs_init = date("H", strtotime($d[count($d) - 1]));
                    $gfs_init = strtotime($d[count($d) - 1]);
                }
            } elseif ($z == 3 && $var != "hlcy") {
                if ($var1 == "snow_accum" || $var == "qpf_accum" || $var == "frz_rain" || $var == "sleet" || $var == "buf_snow_sr_rate" || $var == "buf_snow_maxt_rate") {
                    if (($z2 - 2) % 3 != 0 && ($z2 - 2) <= 120) {
                        //continue;
                    }
                }
                if ($var1 == "snow_accum") {
                    $snow[] = $d[$index];
                    $snow1[] = $d[$index1];
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        $temp_cobb1 = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($snow[$i] * $exp);
                            $temp_cobb1 = $temp_cobb1 + ($snow1[$i] * $exp);
                        }
                        if ($ratio != 0) {
                            $gfsm_var[] = $temp_cobb;
                        }
                        $gfsm_var1[] = $temp_cobb1;
                    } else {
                        if ($ratio != 0) {
                            $gfsm_var[] = array_sum($snow);
                        }
                        $gfsm_var1[] = array_sum($snow1);
                    }
                } elseif ($var1 == "wind") {
                    $gfsm_var[] = $d[$index];
                    if ($d[$index1] == 0) {
                        $gfsm_var1[] = $d[$index];
                    } else {
                        $gfsm_var1[] = $d[$index1] * 1.15077945;
                    }
                    if ($d[$index2] == 0) {
                        $gfsm_var2[] = $d[$index];
                    } else {
                        $gfsm_var2[] = $d[$index2] * 1.15077945;
                    }
                } else {
                    $gfsm_var[] = $d[$index];
                    $gfsm_uwnd[] = $d[$uwnd_index];
                    $gfsm_vwnd[] = $d[$vwnd_index];
                }
                $buf_t_gfsm[] = strtotime($d[1]);
                if ($h2 == 0) {
                    $buf_gfsm_init = date("H", strtotime($d[count($d) - 1]));
                    $gfsm_init = strtotime($d[count($d) - 1]);
                }
            } elseif ($z == 4) {
                if ($var1 == "snow_accum") {
                    $snow[] = $d[$index];
                    $snow1[] = $d[$index1];
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        $temp_cobb1 = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($snow[$i] * $exp);
                            $temp_cobb1 = $temp_cobb1 + ($snow1[$i] * $exp);
                        }
                        if ($ratio != 0) {
                            $rap_var[] = $temp_cobb;
                        }
                        $rap_var1[] = $temp_cobb1;
                    } else {
                        if ($ratio != 0) {
                            $rap_var[] = array_sum($snow);
                        }
                        $rap_var1[] = array_sum($snow1);
                    }
                } elseif ($var1 == "wind") {
                    $rap_var[] = $d[$index];
                    if ($d[$index1] == 0) {
                        $rap_var1[] = $d[$index];
                    } else {
                        $rap_var1[] = $d[$index1] * 1.15077945;
                    }
                    if ($d[$index2] == 0) {
                        $rap_var2[] = $d[$index];
                    } else {
                        $rap_var2[] = $d[$index2] * 1.15077945;
                    }
                } else {
                    $rap_var[] = $d[$index];
                    $rap_uwnd[] = $d[$uwnd_index];
                    $rap_vwnd[] = $d[$vwnd_index];
                }
                $buf_t_rap[] = strtotime($d[1]);
                if ($h2 == 0) {
                    $buf_rap_init = date("H", strtotime($d[count($d) - 1]));
                    $rap_init = strtotime($d[count($d) - 1]);
                }
            }
            if ($z == 5) {
                if ($var1 == "snow_accum") {
                    $snow[] = $d[$index];
                    $snow1[] = $d[$index1];
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        $temp_cobb1 = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($snow[$i] * $exp);
                            $temp_cobb1 = $temp_cobb1 + ($snow1[$i] * $exp);
                        }
                        if ($ratio != 0) {
                            $nam4km_var[] = $temp_cobb;
                        }
                        $nam4km_var1[] = $temp_cobb1;
                    } else {
                        if ($ratio != 0) {
                            $nam4km_var[] = array_sum($snow);
                        }
                        $nam4km_var1[] = array_sum($snow1);
                    }
                } elseif ($var1 == "wind") {
                    $nam4km_var[] = $d[$index];
                    if ($d[$index1] == 0) {
                        $nam4km_var1[] = $d[$index];
                    } else {
                        $nam4km_var1[] = $d[$index1] * 1.15077945;
                    }
                    if ($d[$index2] == 0) {
                        $nam4km_var2[] = $d[$index];
                    } else {
                        $nam4km_var2[] = $d[$index2] * 1.15077945;
                    }
                } else {
                    $nam4km_var[] = $d[$index];
                    $nam4km_uwnd[] = $d[$uwnd_index];
                    $nam4km_vwnd[] = $d[$vwnd_index];
                }
                $buf_t_nam4km[] = strtotime($d[1]);
                if ($h2 == 0) {
                    $buf_nam4km_init = date("H", strtotime($d[count($d) - 1]));
                    $nam4km_init = strtotime($d[count($d) - 1]);
                }
            }
        }
    }
}

//print_r($buf_t_gfs);
//print_r($gfs_var);
//die();

if (!empty($start_time) && !empty($end_time)) {
    $min = $start;
    $max = $end;
} elseif (!empty($buf_t_gfs) && !empty($buf_t_gfsm)) {
    $min = min($buf_t_gfs[0], $buf_t_gfsm[0]);
    $max = max($buf_t_gfs[count($buf_t_gfs) - 1], $buf_t_gfsm[count($buf_t_gfsm) - 1]);
    $start = $min;
    $end = $max;
} else {
    $link = ROOTURL . "data/parser2.php?model=gfs&site=kdsm&date=" . $parse_date_gfs . "";
    $data = file($link);
    foreach ($data as $line) {
        $d = explode("\t", trim($line));
        $buf_t_gfs[] = strtotime($d[1]);
    }
    $link = ROOTURL . "data/parser2.php?model=gfsm&site=kdsm&date=" . $parse_date_gfsm . "";
    $data = file($link);
    foreach ($data as $line) {
        $d = explode("\t", trim($line));
        $buf_t_gfsm[] = strtotime($d[1]);
    }
    $min = min($buf_t_gfs[1], $buf_t_gfsm[1]);
    $max = max($buf_t_gfs[count($buf_t_gfs) - 1], $buf_t_gfsm[count($buf_t_gfsm) - 1]);
    $start = $min;
    $end = $max;
}


if ($var1 == "snow_accum" && $date == "") {
    $nam_cobb_time = array();
    $namm_cobb_time = array();
    $gfs_cobb_time = array();
    $gfsm_cobb_time = array();
    for ($z = 0; $z <= 4; $z++) {
        $cobb_snow = array();
        $h = -1;
        if ($z == 0) {
            $dt = 1;
            $link = "../data/cobb_nam/nam_" . strtolower($site) . ".dat";
        } elseif ($z == 1) {
            $dt = 1;
            $link = "../data/cobb_namm/nam_" . strtolower($site) . ".dat";
        } elseif ($z == 2) {
            $dt = 3;
            $link = "../data/cobb_gfs/gfs3_" . strtolower($site) . ".dat";
        } elseif ($z == 3) {
            $dt = 3;
            $link = "../data/cobb_gfsm/gfs3_" . strtolower($site) . ".dat";
        } elseif ($z == 4) {
            $dt = 1;
            $link = "../data/cobb_nam4km/nam4km_" . strtolower($site) . ".dat";
        }
        $data = @file($link);
        if ($data == False) {
            continue;
        }
        foreach ($data as $line) {
            $d = str_split($line);
            if (@$d[11] == "Z") {
                $h++;
                $d2 = explode("|", trim($line));
                $cobb_snow[] = trim($d2[1]);
                $make_t = "20" . $d[0] . "" . $d[1] . "-" . $d[2] . "" . $d[3] . "-" . $d[4] . "" . $d[5] . " " . $d[7] . "" . $d[8] . ":" . $d[9] . "" . $d[10] . "";
                $cobb_time = strtotime($make_t);
                if ($h == 0) {
                    $cobb_init = $cobb_time;
                }
                if ($z == 0 && $cobb_time >= $start && $cobb_time <= $end) {
                    if ($h == 0) {
                        $nam_cobb_time[] = strtotime($make_t) - 3600;
                        $nam_var2[] = 0;
                    }
                    $nam_cobb_init = date('H', $cobb_init - 3600);
                    $nam_cobb_time[] = strtotime($make_t);
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($cobb_snow[$i] * $exp);
                        }
                        $nam_var2[] = $temp_cobb;
                    } else {
                        $nam_var2[] = array_sum($cobb_snow);
                    }
                } elseif ($z == 1 && $cobb_time >= $start && $cobb_time <= $end) {
                    if ($h == 0) {
                        $namm_cobb_time[] = strtotime($make_t) - 3600;
                        $namm_var2[] = 0;
                    }
                    $namm_cobb_init = date('H', $cobb_init - 3600);
                    $namm_cobb_time[] = strtotime($make_t);
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($cobb_snow[$i] * $exp);
                        }
                        $namm_var2[] = $temp_cobb;
                    } else {
                        $namm_var2[] = array_sum($cobb_snow);
                    }
                } elseif ($z == 2 && $cobb_time >= $start && $cobb_time <= $end) {
                    if ($h == 0) {
                        $gfs_cobb_time[] = strtotime($make_t) - (3600 * $dt);
                        $gfs_var2[] = 0;
                    } elseif ($h % 3 != 0 && $h <= 120) {
                        continue;
                    }
                    //$gfs_cobb_init = date('H',$cobb_init - (3600 * $dt));
                    $gfs_cobb_init = date('H', $cobb_init - 3600);
                    $gfs_cobb_time[] = strtotime($make_t);
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($cobb_snow[$i] * $exp);
                        }
                        $gfs_var2[] = $temp_cobb;
                    } else {
                        $gfs_var2[] = array_sum($cobb_snow);
                    }
                } elseif ($z == 3 && $cobb_time >= $start && $cobb_time <= $end) {
                    if ($h == 0) {
                        $gfsm_cobb_time[] = strtotime($make_t) - (3600 * $dt);
                        $gfsm_var2[] = 0;
                    } elseif ($h % 3 != 0 && $h <= 120) {
                        continue;
                    }
                    //$gfsm_cobb_init = date('H',$cobb_init - (3600 * $dt));
                    $gfsm_cobb_init = date('H', $cobb_init - 3600);
                    $gfsm_cobb_time[] = strtotime($make_t);
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($cobb_snow[$i] * $exp);
                        }
                        $gfsm_var2[] = $temp_cobb;
                    } else {
                        $gfsm_var2[] = array_sum($cobb_snow);
                    }
                }
                if ($z == 4 && $cobb_time >= $start && $cobb_time <= $end) {
                    if ($h == 0) {
                        $nam4km_cobb_time[] = strtotime($make_t) - 3600;
                        $nam4km_var2[] = 0;
                    }
                    $nam4km_cobb_init = date('H', $cobb_init - 3600);
                    $nam4km_cobb_time[] = strtotime($make_t);
                    if ($compaction == 1) {
                        $temp_cobb = 0;
                        for ($i = 0; $i <= $h; $i++) {
                            $exp = exp($a * sqrt(($h - $i) * $dt));
                            $temp_cobb = $temp_cobb + ($cobb_snow[$i] * $exp);
                        }
                        $nam4km_var2[] = $temp_cobb;
                    } else {
                        $nam4km_var2[] = array_sum($cobb_snow);
                    }
                }
            }
        }
        //print_r($gfs_var2);
        //print_r($gfs_cobb_time);
        //die();
    }
}


/*
if($nam == 1){
    $mins[] = $buf_t_nam[0];
    $maxs[] = $buf_t_nam[84];
}
if($namm == 1){
        $mins[] = $buf_t_namm[0];
        $maxs[] = $buf_t_namm[84];
}
if($gfs == 1){
        $mins[] = $buf_t_gfs[0];
        $maxs[] = $buf_t_gfs[60];
}
if($gfsm == 1){
        $mins[] = $buf_t_gfsm[0];
        $maxs[] = $buf_t_gfsm[60];
}
*/


$init_year = date('Y', $min);
$init_mon = date('m', $min);
$init_day = date('d', $min);
$init_h = date('H', $min);
$end_year = date('Y', $max);
$end_mon = date('m', $max);
$end_day = date('d', $max);
$end_h = date('H', $max);
$init_time =  "" . $init_year . "-" . $init_mon . "-" . $init_day . "T" . $init_h . "";
$end_t =  "" . $end_year . "-" . $end_mon . "-" . $end_day . "T" . $end_h . "";
$diff_time = $now - $min;

$obs_time = array();
$obs_temp = array();
$obs_dew = array();
$obs_wspd = array();
$obs_wdir = array();
$obs_precip = array();
$obs_precip_accum = array();
$obs_pres = array();
$obs_gust = array();
$obs_hiwc = array();
$ob_station = strtoupper($site);
if ($obs == 1 && in_array($site, $sites)) {
    $ob_vars = array('id', 'valid', 'tmpf', 'dwpf', 'sknt', 'drct', 'phour', 'alti', 'gust');
    if ($date != "" && $diff_time > 129600) {
        $link3 = "http://mesonet.agron.iastate.edu/request/asos/csv.php?lat=" . $lat . "&lon=" . $lon . "&date=" . date("Y-m-d", $min) . "";
    } else {
        $link3 = "http://mesonet.agron.iastate.edu/request/asos/csv.php?lat=" . $lat . "&lon=" . $lon . "";
    }
    $k = -1;
    $oHourLast = -99;
    $data = file($link3);
    foreach ($data as $line) {
        $k++;
        $d = explode(",", trim($line));
        $ob_time = strtotime("" . $d[1] . "Z");
        $minute = date("i", $ob_time);
        $oHour = date("H", $ob_time);

        if ($ob_time >= $min && $ob_time <= $max && $d[2] != -99 && $k >= 0 && $minute >= 45 && $minute <= 56 && !empty($d[2]) && !empty($d[3]) && $oHour != $oHourLast) {
            $obs_lat = $d[10];
            $obs_lon = $d[9];
            $lat_diff = abs($lat - $obs_lat);
            $lon_diff = abs($lon - $obs_lon);
            if ($lat_diff > 1 || $lon_diff > 1) {
                break;
            }
            if (strlen($d[0]) == 3) {
                $ob_station = "K" . $d[0] . "";
            } else {
                $ob_station = $d[0];
            }
            $obs_time[] = $ob_time;
            $obs_temp[] = $d[2];
            $obs_dew[] = $d[3];
            $obs_wspd[] = $d[4] * 1.15077945;
            $obs_wdir[] = $d[5];
            $obs_precip[] = $d[6];
            $obs_precip_accum[] = array_sum($obs_precip);
            $obs_pres[] = $d[7];
            if ($d[8] != 0) {
                $obs_gust[] = $d[8] * 1.15077945;
            } else {
                $obs_gust[] = "";
            }
            $temp_c = ($d[2] - 32) * (5 / 9);
            $dpt_c = ($d[3] - 32) * (5 / 9);
            $rh = 100 * (exp(((1 / ($dpt_c + 273.15)) - (1 / ($temp_c + 273.15))) / (-461.495 / 2500000)));
            if ($d[2] >= 80 && $dpt_c >= 12) {
                $obs_hiwc[] = -42.379 + (2.04901523 * $d[2]) + (10.14333127 * $rh) + (-0.22475541 * $d[2] * $rh) + (-0.00683783 * $d[2] * $d[2]) + (-0.05481717 * $rh * $rh) + (0.00122874 * $d[2] * $d[2] * $rh) + (0.00085282 * $d[2] * $rh * $rh) + (-0.00000199 * $d[2] * $d[2] * $rh * $rh);
            } elseif ($d[2] > 50 || $d[4] == 0) {
                $obs_hiwc[] = $d[2];
            } else {
                $obs_hiwc[] = 35.74 + (0.6215 * $d[2]) - (35.75 * pow($d[4], 0.16)) + ((0.4275 * $d[2]) * pow($d[4], 0.16));
            }
            $oHourLast = $oHour;
        }
    }
}

$mos_vars = array('station', 'model', 'runtime', 'ftime', 'n_x', 'tmp', 'dpt', 'cld', 'wdr', 'wsp', 'p06', 'p12', 'q06', 'q12', 't06', 't12', 'snw', 'cig', 'vis', 'obv', 'poz', 'pos', 'typ');

// MOS data
for ($z = 0; $z <= 2; $z++) {
    if ($z == 0) {
        $mos_year = @date('Y', $nam_init);
        $mos_mon = @date('m', $nam_init);
        $mos_day = @date('d', $nam_init);
        $mos_h = @date('H', $nam_init);
        $nam_mos_temp = array();
        $nam_mos_dew = array();
        $nam_mos_wspd = array();
        $nam_mos_wdir = array();
        $nam_mos_precip = array();
        $nam_mos_temp = array();
        $nam_mos_snow = array();
        $nam_mos_snow_accum = array();
        $nam_mos_time = array();
        $nam_mos_hiwc = array();
        $nam_mos_wind_x = array();
        $nam_mos_wind_y = array();
        $mos_time = "" . $mos_year . "-" . $mos_mon . "-" . $mos_day . "%20" . $mos_h . ":00";
        $link = "http://mesonet.agron.iastate.edu/mos/csv.php?station=" . $ob_station . "&runtime=" . $mos_time . "&model=NAM";
    } elseif ($z == 1) {
        $mos_year = date('Y', $gfs_init);
        $mos_mon = date('m', $gfs_init);
        $mos_day = date('d', $gfs_init);
        $mos_h = date('H', $gfs_init);
        $gfs_mos_temp = array();
        $gfs_mos_dew = array();
        $gfs_mos_wspd = array();
        $gfs_mos_wdir = array();
        $gfs_mos_precip = array();
        $gfs_mos_temp = array();
        $gfs_mos_snow = array();
        $gfs_mos_snow_accum = array();
        $gfs_mos_time = array();
        $gfs_mos_hiwc = array();
        $gfs_mos_wind_x = array();
        $gfs_mos_wind_y = array();
        $mos_time = "" . $mos_year . "-" . $mos_mon . "-" . $mos_day . "%20" . $mos_h . ":00";
        $link = "http://mesonet.agron.iastate.edu/mos/csv.php?station=" . $ob_station . "&runtime=" . $mos_time . "&model=GFS";
    } elseif ($z == 2) {
        $mos_year = date('Y', $gfsm_init);
        $mos_mon = date('m', $gfsm_init);
        $mos_day = date('d', $gfsm_init);
        $mos_h = date('H', $gfsm_init);
        $gfsm_mos_temp = array();
        $gfsm_mos_dew = array();
        $gfsm_mos_wspd = array();
        $gfsm_mos_wdir = array();
        $gfsm_mos_precip = array();
        $gfsm_mos_temp = array();
        $gfsm_mos_snow = array();
        $gfsm_mos_snow_accum = array();
        $gfsm_mos_time = array();
        $gfsm_mos_hiwc = array();
        $gfsm_mos_wind_x = array();
        $gfsm_mos_wind_y = array();
        $mos_time = "" . $mos_year . "-" . $mos_mon . "-" . $mos_day . "%20" . $mos_h . ":00";
        $link = "http://mesonet.agron.iastate.edu/mos/csv.php?station=" . $ob_station . "&runtime=" . $mos_time . "&model=GFS";
    }
    $k = -1;
    $data = file($link);
    foreach ($data as $line) {
        $k++;
        $d = explode(",", trim($line));
        if ($k >= 1) {
            $mos_time = strtotime($d[3]);
            if ($z == 0 && $nam_mos == 1 && in_array($site, $sites) && $mos_time >= $start && $mos_time <= $end) {
                $nam_mos_time[] = strtotime($d[3]);
                $nam_mos_temp[] = $d[5];
                $nam_mos_dew[] = $d[6];
                $nam_mos_wdir[] = $d[8];
                $nam_mos_wspd[] = $d[9] * 1.15077945;
                $nam_mos_snow[] = $d[16];
                $nam_mos_qpf[] = $d[12];
                $nam_mos_snow_accum[] = array_sum($nam_mos_snow);
                $nam_mos_qpf_accum[] = array_sum($nam_mos_qpf);
                $nam_mos_wind_x[] = cos(deg2rad(270 - $d[8])) * ($d[9] * 0.514444444);
                $nam_mos_wind_y[] = sin(deg2rad(270 - $d[8])) * ($d[9] * 0.514444444);
                $temp_c = ($d[5] - 32) * (5 / 9);
                $dpt_c = ($d[6] - 32) * (5 / 9);
                $rh = 100 * (exp(((1 / ($dpt_c + 273.15)) - (1 / ($temp_c + 273.15))) / (-461.495 / 2500000)));
                if ($d[5] >= 80 && $dpt_c >= 12) {
                    $nam_mos_hiwc[] = -42.379 + (2.04901523 * $d[5]) + (10.14333127 * $rh) + (-0.22475541 * $d[5] * $rh) + (-0.00683783 * $d[5] * $d[5]) + (-0.05481717 * $rh * $rh) + (0.00122874 * $d[5] * $d[5] * $rh) + (0.00085282 * $d[5] * $rh * $rh) + (-0.00000199 * $d[5] * $d[5] * $rh * $rh);
                } elseif ($d[5] > 50 || $d[9] == 0) {
                    $nam_mos_hiwc[] = $d[5];
                } else {
                    $nam_mos_hiwc[] = 35.74 + (0.6215 * $d[5]) - (35.75 * pow($d[9], 0.16)) + ((0.4275 * $d[5]) * pow($d[9], 0.16));
                }
            } elseif ($z == 1 && $gfs_mos == 1 && in_array($site, $sites) && $mos_time >= $start && $mos_time <= $end) {
                $gfs_mos_time[] = strtotime($d[3]);
                $gfs_mos_temp[] = $d[5];
                $gfs_mos_dew[] = $d[6];
                $gfs_mos_wdir[] = $d[8];
                $gfs_mos_wspd[] = $d[9] * 1.15077945;
                $gfs_mos_snow[] = $d[16];
                $gfs_mos_qpf[] = $d[12];
                $gfs_mos_snow_accum[] = array_sum($gfs_mos_snow);
                $gfs_mos_qpf_accum[] = array_sum($gfs_mos_qpf);
                $gfs_mos_wind_x[] = cos(deg2rad(270 - $d[8])) * ($d[9] * 0.514444444);
                $gfs_mos_wind_y[] = sin(deg2rad(270 - $d[8])) * ($d[9] * 0.514444444);
                $temp_c = ($d[5] - 32) * (5 / 9);
                $dpt_c = ($d[6] - 32) * (5 / 9);
                $rh = 100 * (exp(((1 / ($dpt_c + 273.15)) - (1 / ($temp_c + 273.15))) / (-461.495 / 2500000)));
                if ($d[5] >= 80 && $dpt_c >= 12) {
                    $gfs_mos_hiwc[] = -42.379 + (2.04901523 * $d[5]) + (10.14333127 * $rh) + (-0.22475541 * $d[5] * $rh) + (-0.00683783 * $d[5] * $d[5]) + (-0.05481717 * $rh * $rh) + (0.00122874 * $d[5] * $d[5] * $rh) + (0.00085282 * $d[5] * $rh * $rh) + (-0.00000199 * $d[5] * $d[5] * $rh * $rh);
                } elseif ($d[5] > 50 || $d[9] == 0) {
                    $gfs_mos_hiwc[] = $d[5];
                } else {
                    $gfs_mos_hiwc[] = 35.74 + (0.6215 * $d[5]) - (35.75 * pow($d[9], 0.16)) + ((0.4275 * $d[5]) * pow($d[9], 0.16));
                }
            } elseif ($z == 2 && $gfsm_mos == 1 && in_array($site, $sites) && $mos_time >= $start && $mos_time <= $end) {
                $gfsm_mos_time[] = strtotime($d[3]);
                $gfsm_mos_temp[] = $d[5];
                $gfsm_mos_dew[] = $d[6];
                $gfsm_mos_wdir[] = $d[8];
                $gfsm_mos_wspd[] = $d[9] * 1.15077945;
                $gfsm_mos_snow[] = $d[16];
                $gfsm_mos_qpf[] = $d[12];
                $gfsm_mos_snow_accum[] = array_sum($gfsm_mos_snow);
                $gfsm_mos_qpf_accum[] = array_sum($gfsm_mos_qpf);
                $gfsm_mos_wind_x[] = cos(deg2rad(270 - $d[8])) * ($d[9] * 0.514444444);
                $gfsm_mos_wind_y[] = sin(deg2rad(270 - $d[8])) * ($d[9] * 0.514444444);
                $temp_c = ($d[5] - 32) * (5 / 9);
                $dpt_c = ($d[6] - 32) * (5 / 9);
                $rh = 100 * (exp(((1 / ($dpt_c + 273.15)) - (1 / ($temp_c + 273.15))) / (-461.495 / 2500000)));
                if ($d[5] >= 80 && $dpt_c >= 12) {
                    $gfsm_mos_hiwc[] = -42.379 + (2.04901523 * $d[5]) + (10.14333127 * $rh) + (-0.22475541 * $d[5] * $rh) + (-0.00683783 * $d[5] * $d[5]) + (-0.05481717 * $rh * $rh) + (0.00122874 * $d[5] * $d[5] * $rh) + (0.00085282 * $d[5] * $rh * $rh) + (-0.00000199 * $d[5] * $d[5] * $rh * $rh);
                } elseif ($d[5] > 50 || $d[9] == 0) {
                    $gfsm_mos_hiwc[] = $d[5];
                } else {
                    $gfsm_mos_hiwc[] = 35.74 + (0.6215 * $d[5]) - (35.75 * pow($d[9], 0.16)) + ((0.4275 * $d[5]) * pow($d[9], 0.16));
                }
            }
        }
    }
}

$obs_var1 = array();
if ($var == "tf") {
    $nam_mos_var = $nam_mos_temp;
    $gfs_mos_var = $gfs_mos_temp;
    $gfsm_mos_var = $gfsm_mos_temp;
    $obs_var = $obs_temp;
    $ndfd = "temp";
} elseif ($var == "td") {
    $nam_mos_var = $nam_mos_dew;
    $gfs_mos_var = $gfs_mos_dew;
    $gfsm_mos_var = $gfsm_mos_dew;
    $obs_var = $obs_dew;
    $ndfd = "dew";
} elseif ($var == "wdir") {
    $nam_mos_var = $nam_mos_wdir;
    $gfs_mos_var = $gfs_mos_wdir;
    $gfsm_mos_var = $gfsm_mos_wdir;
    $obs_var = $obs_wdir;
    $ndfd = "wdir";
} elseif ($var == "hiwc") {
    $nam_mos_var = $nam_mos_hiwc;
    $gfs_mos_var = $gfs_mos_hiwc;
    $gfsm_mos_var = $gfsm_mos_hiwc;
    $obs_var = $obs_hiwc;
    $ndfd = "appt";
} elseif ($var == "qpf") {
    $nam_mos_var = array();
    $gfs_mos_var = array();
    $gfsm_mos_var = array();
    $obs_var = $obs_precip;
    $ndfd = "qpf";
} elseif ($var == "qpf_accum") {
    $nam_mos_var = array();
    $gfs_mos_var = array();
    $gfsm_mos_var = array();
    $obs_var = $obs_precip_accum;
    $ndfd = "qpf";
} elseif ($var == "buf_snow_sr" || $var == "buf_snow_maxt" || $var1 == "snow_accum") {
    $nam_mos_var = array();
    $gfs_mos_var = array();
    $gfsm_mos_var = array();
    $obs_var = array();
    $ndfd = "snow";
} elseif ($var == "frz_rain" || $var == "sleet") {
    $nam_mos_var = array();
    $gfs_mos_var = array();
    $gfsm_mos_var = array();
    $obs_var = array();
    $ndfd = "iceaccum";
} elseif ($var == "wspd") {
    $nam_mos_var = $nam_mos_wspd;
    $gfs_mos_var = $gfs_mos_wspd;
    $gfsm_mos_var = $gfsm_mos_wspd;
    $obs_var = $obs_wspd;
    $ndfd = "wspd";
    if ($var1 == "wind") {
        $obs_var1 = $obs_gust;
    }
} else {
    $nam_mos_var = array();
    $gfs_mos_var = array();
    $gfsm_mos_var = array();
    $obs_var = array();
    $nws = 0;
}

function file_curl($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    $array = explode("\n", $data);
    return $array;
}

$nws_time = array();
$nws_var = array();
$nws_time1 = array();
$nws_var1 = array();
if ($nws == 1 && in_array($site, $sites) && $date == "") {
    $link4_1 = "http://preview.weather.gov/xml/SOAP_server/ndfdXMLclient.php?whichClient=NDFDgen&lat=" . $lat . "&lon=" . $lon . "&listLatLon=&lat1=&lon1=&lat2=&lon2=&resolutionSub=&listLat1=&listLon1=";
    $link4_2 = "&listLat2=&listLon2=&resolutionList=&endPoint1Lat=&endPoint1Lon=&endPoint2Lat=&endPoint2Lon=&listEndPoint1Lat=&listEndPoint1Lon=&listEndPoint2Lat=&listEndPoint2Lon=&zipCodeList=";
    $link4_3 = "&listZipCodeList=&centerPointLat=&centerPointLon=&distanceLat=&distanceLon=&resolutionSquare=&listCenterPointLat=&listCenterPointLon=&listDistanceLat=&listDistanceLon=";
    $link4_4 = "&listResolutionSquare=&citiesLevel=&listCitiesLevel=&sector=&gmlListLatLon=&featureType=&requestedTime=&startTime=&endTime=&compType=&propertyName=";
    $link4_5 = "&product=time-series&begin=" . $init_time . "%3A00%3A00&end=" . $end_t . "%3A00%3A00&Unit=e&" . $ndfd . "=" . $ndfd . "&Submit=Submit";
    $link4 = "" . $link4_1 . "" . $link4_2 . "" . $link4_3 . "" . $link4_4 . "" . $link4_5 . "";
    //echo $link4;
    //die();

    $nws_t = "start-valid-time";
    $value = "value";

    //$data = file($link4);
    $data = file_curl($link4);
    foreach ($data as $line) {
        preg_match_all(".$nws_t.", $line, $id);
        $check1 = @$id[0][0];

        preg_match_all(".$value.", $line, $id2);
        $check2 = @$id2[0][0];

        if ($check1 == $nws_t) {
            $get_t_1 = explode(">", trim($line));
            $get_t_3 = explode("<", trim($get_t_1[1]));
            $get_t = $get_t_3[0];
            if ($var1 == "snow_accum" || $var == "qpf_accum") {
                $nws_time[] = strtotime($get_t) + 21600;
            } else {
                $nws_time[] = strtotime($get_t);
            }
        } elseif ($check2 == $value) {
            $get_nws_t1 = explode(">", trim($line));
            $get_nws_t2 = explode("<", $get_nws_t1[1]);
            if ($var == "qpf_accum" || $var1 == "snow_accum" || $var == "frz_rain" || $var == "sleet") {
                $nws_qpf[] = $get_nws_t2[0];
                $nws_var[] = array_sum($nws_qpf);
            } else {
                $nws_var[] = $get_nws_t2[0];
            }
        }
    }
    if ($var1 == "wind") {
        $link4_5 = "&product=time-series&begin=" . $init_time . "%3A00%3A00&end=" . $end_t . "%3A00%3A00&Unit=e&wgust=wgust&Submit=Submit";
        $link4 = "" . $link4_1 . "" . $link4_2 . "" . $link4_3 . "" . $link4_4 . "" . $link4_5 . "";
        $data = file_curl($link4);
        foreach ($data as $line) {
            preg_match_all(".$nws_t.", $line, $id);
            $check1 = @$id[0][0];

            preg_match_all(".$value.", $line, $id2);
            $check2 = @$id2[0][0];

            if ($check1 == $nws_t) {
                $get_t_1 = explode(">", trim($line));
                $get_t_3 = explode("<", trim($get_t_1[1]));
                $get_t = $get_t_3[0];
                $nws_time1[] = strtotime($get_t);
            } elseif ($check2 == $value) {
                $get_nws_t1 = explode(">", trim($line));
                $get_nws_t2 = explode("<", $get_nws_t1[1]);
                $nws_var1[] = $get_nws_t2[0];
            }
        }
    }
}

//print_r($nws_var1);
//print_r($nws_time1);
//echo $link4;
//die();

$len = count($nws_time);
$nws_time_temp = array();
$nws_var_temp = array();
for ($i = 0; $i < $len; $i++) {
    if ($nws_time[$i] >= $start && $nws_time[$i] <= $end) {
        //echo date("Y-m-d H:i:s",$nws_time[$i]).",".date("Y-m-d H:i:s",$start).",".date("Y-m-d H:i:s",$end)."<br>";
        $nws_time_temp[] = $nws_time[$i];
        $nws_var_temp[] = $nws_var[$i];
    }
}
$nws_time = $nws_time_temp;
$nws_var = $nws_var_temp;

if ($var1 == "wind") {
    $len = count($nws_time1);
    $nws_time_temp1 = array();
    $nws_var_temp1 = array();
    for ($i = 0; $i < $len; $i++) {
        if ($nws_time1[$i] >= $start && $nws_time1[$i] <= $end) {
            $nws_time_temp1[] = $nws_time1[$i];
            $nws_var_temp1[] = $nws_var1[$i];
        }
    }
    $nws_time1 = $nws_time_temp1;
    $nws_var1 = $nws_var_temp1;
}


//3-hourly model consensus
$consensus = array();
$consensus1 = array();
$consensus2 = array();
$consensus_t = array();
$consensus_t1 = array();
$consensus_t2 = array();
$j = 0;
if ($con == 1) {
    for ($i = $min; $i <= $max; $i = $i + 10800) {
        $j++;
        $total = 0;
        $total1 = 0;
        $total2 = 0;
        $total_x = 0;
        $total_y = 0;
        $n = 0;
        $n1 = 0;
        $n2 = 0;
        $var_list = array();
        if (in_array($i, $buf_t_nam) && !empty($nam_var) && $nam == 1) {
            $n++;
            $index = array_search($i, $buf_t_nam);
            if ($var == "wdir") {
                $total_x = $total_x + $nam_uwnd[$index];
                $total_y = $total_y + $nam_vwnd[$index];
                $var_list[] = $nam_vwnd[$index];
            } else {
                $total = $total + $nam_var[$index];
            }
        }
        if (in_array($i, $buf_t_namm) && !empty($namm_var) && $namm == 1) {
            $n++;
            $index = array_search($i, $buf_t_namm);
            if ($var == "wdir") {
                $total_x = $total_x + $namm_uwnd[$index];
                $total_y = $total_y + $namm_vwnd[$index];
                $var_list[] = $namm_vwnd[$index];
            } else {
                $total = $total + $namm_var[$index];
            }
        }
        if (in_array($i, $buf_t_gfs) && !empty($gfs_var) && $gfs == 1) {
            $n++;
            $index = array_search($i, $buf_t_gfs);
            if ($var == "wdir") {
                $total_x = $total_x + $gfs_uwnd[$index];
                $total_y = $total_y + $gfs_vwnd[$index];
                $var_list[] = $gfs_vwnd[$index];
            } else {
                $total = $total + $gfs_var[$index];
            }
        }
        if (in_array($i, $buf_t_gfsm) && !empty($gfsm_var) && $gfsm == 1) {
            $n++;
            $index = array_search($i, $buf_t_gfsm);
            if ($var == "wdir") {
                $total_x = $total_x + $gfsm_uwnd[$index];
                $total_y = $total_y + $gfsm_vwnd[$index];
                $var_list[] = $gfsm_vwnd[$index];
            } else {
                $total = $total + $gfsm_var[$index];
            }
        }
        if (in_array($i, $buf_t_rap) && !empty($rap_var) && $rap == 1) {
            $n++;
            $index = array_search($i, $buf_t_rap);
            if ($var == "wdir") {
                $total_x = $total_x + $rap_uwnd[$index];
                $total_y = $total_y + $rap_vwnd[$index];
                $var_list[] = $rap_vwnd[$index];
            } else {
                $total = $total + $rap_var[$index];
            }
        }
        if (in_array($i, $buf_t_nam4km) && !empty($nam4km_var) && $nam4km == 1) {
            $n++;
            $index = array_search($i, $buf_t_nam4km);
            if ($var == "wdir") {
                $total_x = $total_x + $nam4km_uwnd[$index];
                $total_y = $total_y + $nam4km_vwnd[$index];
                $var_list[] = $nam4km_vwnd[$index];
            } else {
                $total = $total + $nam4km_var[$index];
            }
        }
        if (in_array($i, $gfs_mos_time) && !empty($gfs_mos_var) && $gfs_mos == 1) {
            $n++;
            $index = array_search($i, $gfs_mos_time);
            if ($var == "wdir") {
                $total_x = $total_x + $gfs_mos_wind_x[$index];
                $total_y = $total_y + $gfs_mos_wind_y[$index];
                $var_list[] = $gfs_mos_wind_y[$index];
            } else {
                $total = $total + $gfs_mos_var[$index];
            }
        }
        if (in_array($i, $gfsm_mos_time) && !empty($gfsm_mos_var) && $gfsm_mos == 1) {
            $n++;
            $index = array_search($i, $gfsm_mos_time);
            if ($var == "wdir") {
                $total_x = $total_x + $gfsm_mos_wind_x[$index];
                $total_y = $total_y + $gfsm_mos_wind_y[$index];
                $var_list[] = $gfsm_mos_wind_y[$index];
            } else {
                $total = $total + $gfsm_mos_var[$index];
            }
        }
        if (in_array($i, $nam_mos_time) && !empty($nam_mos_var) && $nam_mos == 1) {
            $n++;
            $index = array_search($i, $nam_mos_time);
            if ($var == "wdir") {
                $total_x = $total_x + $nam_mos_wind_x[$index];
                $total_y = $total_y + $nam_mos_wind_y[$index];
                $var_list[] = $nam_mos_wind_y[$index];
            } else {
                $total = $total + $nam_mos_var[$index];
            }
        }

        if (in_array($i, $buf_t_nam) && !empty($nam_var1) && $nam == 1) {
            $n1++;
            $index = array_search($i, $buf_t_nam);
            $total1 = $total1 + $nam_var1[$index];
        }
        if (in_array($i, $buf_t_namm) && !empty($namm_var1) && $namm == 1) {
            $n1++;
            $index = array_search($i, $buf_t_namm);
            $total1 = $total1 + $namm_var1[$index];
        }
        if (in_array($i, $buf_t_gfs) && !empty($gfs_var1) && $gfs == 1) {
            $n1++;
            $index = array_search($i, $buf_t_gfs);
            $total1 = $total1 + $gfs_var1[$index];
        }
        if (in_array($i, $buf_t_gfsm) && !empty($gfsm_var1) && $gfsm == 1) {
            $n1++;
            $index = array_search($i, $buf_t_gfsm);
            $total1 = $total1 + $gfsm_var1[$index];
        }
        if (in_array($i, $buf_t_rap) && !empty($rap_var1) && $rap == 1) {
            $n1++;
            $index = array_search($i, $buf_t_rap);
            $total1 = $total1 + $rap_var1[$index];
        }
        if (in_array($i, $buf_t_nam4km) && !empty($nam4km_var1) && $nam4km == 1) {
            $n1++;
            $index = array_search($i, $buf_t_nam4km);
            $total1 = $total1 + $nam4km_var1[$index];
        }

        if ($var1 == "snow_accum") {
            if (in_array($i, $nam_cobb_time) && !empty($nam_var2) && $nam == 1) {
                $n2++;
                $index = array_search($i, $nam_cobb_time);
                $total2 = $total2 + $nam_var2[$index];
            }
            if (in_array($i, $namm_cobb_time) && !empty($nam_var2) && $namm == 1) {
                $n2++;
                $index = array_search($i, $namm_cobb_time);
                $total2 = $total2 + $namm_var2[$index];
            }
            if (in_array($i, $gfs_cobb_time) && !empty($gfs_var2) && $gfs == 1) {
                $n2++;
                $index = array_search($i, $gfs_cobb_time);
                $total2 = $total2 + $gfs_var2[$index];
            }
            if (in_array($i, $gfsm_cobb_time) && !empty($gfsm_var2) && $gfsm == 1) {
                $n2++;
                $index = array_search($i, $gfsm_cobb_time);
                $total2 = $total2 + $gfsm_var2[$index];
            }
            if (in_array($i, $nam4km_cobb_time) && !empty($nam4km_var2) && $nam4km == 1) {
                $n2++;
                $index = array_search($i, $nam4km_cobb_time);
                $total2 = $total2 + $nam4km_var2[$index];
            }
        } else {
            if (in_array($i, $buf_t_nam) && !empty($nam_var2) && $nam == 1) {
                $n2++;
                $index = array_search($i, $buf_t_nam);
                $total2 = $total2 + $nam_var2[$index];
            }
            if (in_array($i, $buf_t_namm) && !empty($namm_var2) && $namm == 1) {
                $n2++;
                $index = array_search($i, $buf_t_namm);
                $total2 = $total2 + $namm_var2[$index];
            }
            if (in_array($i, $buf_t_gfs) && !empty($gfs_var2) && $gfs == 1) {
                $n2++;
                $index = array_search($i, $buf_t_gfs);
                $total2 = $total2 + $gfs_var2[$index];
            }
            if (in_array($i, $buf_t_gfsm) && !empty($gfsm_var2) && $gfsm == 1) {
                $n2++;
                $index = array_search($i, $buf_t_gfsm);
                $total2 = $total2 + $gfsm_var2[$index];
            }
            if (in_array($i, $buf_t_rap) && !empty($rap_var2) && $rap == 1) {
                $n2++;
                $index = array_search($i, $buf_t_rap);
                $total2 = $total2 + $rap_var2[$index];
            }
            if (in_array($i, $buf_t_nam4km) && !empty($nam4km_var2) && $nam4km == 1) {
                $n2++;
                $index = array_search($i, $buf_t_nam4km);
                $total2 = $total2 + $nam4km_var2[$index];
            }
        }


        if ($n > 1) {
            if ($var == "wdir") {
                $cx = $total_x / $n;
                $cy = $total_y / $n;
                $c_ang = 270 - rad2deg(atan2($cy, $cx));
                if ($c_ang < 0) {
                    $c_ang = $c_ang + 360;
                } elseif ($c_ang > 359) {
                    $c_ang = $c_ang - 360;
                }
                $consensus[] = $c_ang;
                //if($j == 21){
                //echo "".$nam_uwnd[$index]."\n".$namm_uwnd[$index]."\n".$gfs_uwnd[$index]."\n".$gfsm_uwnd[$index]."\n".$rap_uwnd[$index]."\n";
                //echo "".."\n".."\n".."\n";
                //print_r($var_list);
                //echo "".array_sum($var_list)."\n";
                //echo "".$total_x.",".$total_y."\n";
                //echo "".$cx.",".$cy."\n";
                //echo "".$c_ang."\n\n";
                //}
            } else {
                $consensus[] = ($total / $n);
            }
            $consensus_t[] = $i;
        }
        if ($n1 > 1) {
            $consensus1[] = ($total1 / $n1);
            $consensus_t1[] = $i;
        }
        if ($n2 > 1) {
            $consensus2[] = ($total2 / $n2);
            $consensus_t2[] = $i;
        }
    }
}

//Harvey Freese
$freese_time = array();
$freese_wind = array();
if ($freese != "no") {
    $z = 0;
    $link = "http://www.vorticity.weather.net/public/images/" . $freese . ".txt";
    $data = @file($link);
    if ($data == False) {
        die("Unable to plot data.  Likely problem with link: " . $link . "");
    } else {
        foreach ($data as $line) {
            $z++;
            if ($z >= 5 && $z <= 12) {
                $d = explode(",", trim($line));
                $freese_id = $d[0];
                $d2 = str_split($d[2]);
                $fmon = "" . $d2[0] . "" . $d2[1] . "";
                $fday = "" . $d2[2] . "" . $d2[3] . "";
                $fyear = "" . $d2[4] . "" . $d2[5] . "" . $d2[6] . "" . $d2[7] . "";
                for ($i = 3; $i <= 26; $i++) {
                    $ftime = strtotime("" . $fyear . "-" . $fmon . "-" . $fday . " " . ($i - 3) . ":00:00 " . $f_local . "");
                    if ($ftime >= $min && $ftime <= $max) {
                        $freese_time[] = $ftime;
                        $freese_wind[] = $d[$i];
                    }
                }
            } elseif ($z == 3) {
                $d = str_split($line);
                $f_local = "" . $d[5] . "" . $d[6] . "" . $d[7] . "";
            }
        }
    }
}


//echo "here";
//die();

require_once "../../include/jpgraph/jpgraph.php";
require_once "../../include/jpgraph/jpgraph_line.php";
require_once "../../include/jpgraph/jpgraph_date.php";
require_once "../../include/jpgraph/jpgraph_scatter.php";
require_once "../../include/jpgraph/jpgraph_iconplot.php";

$graph = new Graph(1100, 450);
if ($var == "wdir") {
    $graph->SetScale("datlin", 0, 360, $min, $max);
    $graph->yscale->ticks->Set(45, 22.5);
}
//elseif($var == "hlcy"){
//      $graph->SetScale("datlin",0,1000,$min,$max);
//}
else {
    $graph->SetScale("datlin", "", "", $min, $max);
}
$graph->title->Set("" . $site_upper_case . " - Hourly " . $title . " Forecast");
$graph->yaxis->title->Set($y_label);
$graph->SetMarginColor('white');
$graph->SetBox();
$graph->SetFrame(false);
$graph->yaxis->SetTitleMargin(40);
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->scale->SetDateFormat('D H e');
$graph->xaxis->SetPos("min");

$graph->img->SetMargin(60, 140, 40, 90);
$graph->SetColor('gray9');
$graph->ygrid->SetColor('gray');
$graph->ygrid->SetFill(true, '#DDDDDD@0.5', '#BBBBBB@0.5');
$graph->xgrid->Show();
$graph->xgrid->SetLineStyle('dashed');
$graph->xgrid->SetColor('gray');
$graph->legend->SetColumns(1);
$graph->legend->SetAbsPos(30, 40, 'right', 'top');
$graph->legend->SetShadow(false);
$graph->legend->SetFillColor("gray8");
if ($var1 == "snow_accum" || $var1 == "wind" || $freese != "no") {
    $graph->legend->SetAbsPos(2, 40, 'right', 'top');
    if ($var1 == "snow_accum") {
        $graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 5.4);
    } else {
        $graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 5.5);
    }
    if ($compaction == 1 && $var1 == "snow_accum") {
        $graph->title->Set("" . $site_upper_case . " - Accumulated " . $title . " Forecast (with compaction)");
    } elseif ($var1 == "snow_accum") {
        $graph->title->Set("" . $site_upper_case . " - Accumulated " . $title . " Forecast (no compaction)");
    } elseif ($var1 == "wind") {
        $graph->title->Set("" . $site_upper_case . " - 10 m AGL " . $title . " Forecast (Gusts via Momentum Transfer)");
    }
}

if ($nam == 1 && !empty($nam_var) && !empty($buf_t_nam) && count($nam_var) == count($buf_t_nam)) {
    if ($var == "wdir") {
        $lineplot_nam = new ScatterPlot($nam_var, $buf_t_nam);
        $lineplot_nam->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_nam->mark->SetWidth(3);
        $lineplot_nam->mark->SetFillColor("red");
    } else {
        $lineplot_nam = new LinePlot($nam_var, $buf_t_nam);
        $lineplot_nam->SetColor("red");
        $lineplot_nam->SetWeight(3);
    }
    if ($var1 == "snow_accum") {
        if ($ratio != 0) {
            $lineplot_nam->SetLegend("" . $buf_nam_init . "z NAM " . $ratio . ":1");
            $graph->Add($lineplot_nam);
            $lineplot_nam->SetColor("red");
        }
    } elseif ($var1 == "wind") {
        if ($mean == 1) {
            $lineplot_nam->SetLegend("" . $buf_nam_init . "z NAM");
            $graph->Add($lineplot_nam);
            $lineplot_nam->SetColor("red");
        }
    } else {
        $lineplot_nam->SetLegend("" . $buf_nam_init . "z NAM");
        $graph->Add($lineplot_nam);
        $lineplot_nam->SetColor("red");
    }
}

if ($nam == 1 && !empty($nam_var1) && !empty($buf_t_nam) && count($nam_var1) == count($buf_t_nam)) {
    $lineplot_nam1 = new LinePlot($nam_var1, $buf_t_nam);
    $lineplot_nam1->SetColor("red");
    $lineplot_nam1->SetWeight(3);
    $lineplot_nam1->SetStyle("dashed");
    if ($var1 == "snow_accum") {
        if ($max_t == 1) {
            $lineplot_nam1->SetLegend("" . $buf_nam_init . "z NAM Max-T Prof");
            $graph->Add($lineplot_nam1);
            $lineplot_nam1->SetColor("red");
        }
    } elseif ($var1 == "wind") {
        if ($mean_mt == 1) {
            $lineplot_nam1->SetLegend("" . $buf_nam_init . "z NAM Mean MT");
            $graph->Add($lineplot_nam1);
            $lineplot_nam1->SetColor("red");
        }
    } else {
        $lineplot_nam1->SetLegend("" . $buf_nam_init . "z NAM");
        $graph->Add($lineplot_nam1);
        $lineplot_nam1->SetColor("red");
    }
}

if ($var1 == "snow_accum") {
    if ($nam == 1 && $cobb == 1 && !empty($nam_var2) && !empty($nam_cobb_time) && count($nam_var2) == count($nam_cobb_time)) {
        $lineplot_nam2 = new LinePlot($nam_var2, $nam_cobb_time);
        $lineplot_nam2->SetColor("deeppink");
        $lineplot_nam2->SetWeight(3);
        $lineplot_nam2->SetStyle("dashed");
        $lineplot_nam2->SetLegend("" . $nam_cobb_init . "z NAM Cobb v5.5");
        $graph->Add($lineplot_nam2);
        $lineplot_nam2->SetColor("deeppink");
    }
} else {
    if ($nam == 1 && !empty($nam_var2) && !empty($buf_t_nam) && count($nam_var2) == count($buf_t_nam)) {
        $lineplot_nam2 = new LinePlot($nam_var2, $buf_t_nam);
        $lineplot_nam2->SetColor("deeppink");
        $lineplot_nam2->SetWeight(3);
        $lineplot_nam2->SetStyle("dashed");
        if ($var1 == "wind") {
            $lineplot_nam2->SetLegend("" . $buf_nam_init . "z NAM Max MT");
            if ($max_mt == 1) {
                $graph->Add($lineplot_nam2);
                $lineplot_nam2->SetColor("deeppink");
            }
        } else {
            $lineplot_nam2->SetLegend("" . $buf_nam_init . "z NAM");
            $graph->Add($lineplot_nam2);
            $lineplot_nam2->SetColor("deeppink");
        }
    }
}


if ($namm == 1 && !empty($namm_var) && !empty($buf_t_namm) && count($namm_var) == count($buf_t_namm)) {
    if ($var == "wdir") {
        $lineplot_namm = new ScatterPlot($namm_var, $buf_t_namm);
        $lineplot_namm->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_namm->mark->SetWidth(3);
        $lineplot_namm->mark->SetFillColor("darkred");
    } else {
        $lineplot_namm = new LinePlot($namm_var, $buf_t_namm);
        $lineplot_namm->SetColor("darkred");
        $lineplot_namm->SetWeight(3);
    }
    if ($var1 == "snow_accum") {
        if ($ratio != 0) {
            $lineplot_namm->SetLegend("" . $buf_namm_init . "z NAM " . $ratio . ":1");
            $graph->Add($lineplot_namm);
            $lineplot_namm->SetColor("darkred");
        }
    } elseif ($var1 == "wind") {
        if ($mean == 1) {
            $lineplot_namm->SetLegend("" . $buf_namm_init . "z NAM");
            $graph->Add($lineplot_namm);
            $lineplot_namm->SetColor("darkred");
        }
    } else {
        $lineplot_namm->SetLegend("" . $buf_namm_init . "z NAM");
        $graph->Add($lineplot_namm);
        $lineplot_namm->SetColor("darkred");
    }
}

if ($namm == 1 && !empty($namm_var1) && !empty($buf_t_namm) && count($namm_var1) == count($buf_t_namm)) {
    $lineplot_namm1 = new LinePlot($namm_var1, $buf_t_namm);
    $lineplot_namm1->SetColor("darkred");
    $lineplot_namm1->SetWeight(3);
    $lineplot_namm1->SetStyle("dashed");
    if ($var1 == "snow_accum") {
        if ($max_t == 1) {
            $lineplot_namm1->SetLegend("" . $buf_namm_init . "z NAM Max-T Prof");
            $graph->Add($lineplot_namm1);
            $lineplot_namm1->SetColor("darkred");
        }
    } elseif ($var1 == "wind") {
        if ($mean_mt == 1) {
            $lineplot_namm1->SetLegend("" . $buf_namm_init . "z NAM Mean MT");
            $graph->Add($lineplot_namm1);
            $lineplot_namm1->SetColor("darkred");
        }
    } else {
        $lineplot_nam1->SetLegend("" . $buf_namm_init . "z NAM");
        $graph->Add($lineplot_namm1);
        $lineplot_namm1->SetColor("darkred");
    }
}

if ($var1 == "snow_accum") {
    if ($namm == 1 && $cobb == 1 && !empty($namm_var2) && !empty($namm_cobb_time) && count($namm_var2) == count($namm_cobb_time)) {
        $lineplot_namm2 = new LinePlot($namm_var2, $namm_cobb_time);
        $lineplot_namm2->SetColor("orange");
        $lineplot_namm2->SetWeight(3);
        $lineplot_namm2->SetStyle("dashed");
        $lineplot_namm2->SetLegend("" . $namm_cobb_init . "z NAM Cobb v5.5");
        $graph->Add($lineplot_namm2);
        $lineplot_namm2->SetColor("orange");
    }
} else {
    if ($namm == 1 && !empty($namm_var2) && !empty($buf_t_namm) && count($namm_var2) == count($buf_t_namm)) {
        $lineplot_namm2 = new LinePlot($namm_var2, $buf_t_namm);
        $lineplot_namm2->SetColor("orange");
        $lineplot_namm2->SetWeight(3);
        $lineplot_namm2->SetStyle("dashed");
        if ($var1 == "wind") {
            $lineplot_namm2->SetLegend("" . $buf_namm_init . "z NAM Max MT");
            if ($max_mt == 1) {
                $graph->Add($lineplot_namm2);
                $lineplot_namm2->SetColor("orange");
            }
        } else {
            $lineplot_namm2->SetLegend("" . $buf_namm_init . "z NAM");
            $graph->Add($lineplot_namm2);
            $lineplot_namm2->SetColor("orange");
        }
    }
}

if ($nam4km == 1 && !empty($nam4km_var) && !empty($buf_t_nam4km) && count($nam4km_var) == count($buf_t_nam4km)) {
    if ($var == "wdir") {
        $lineplot_nam4km = new ScatterPlot($nam4km_var, $buf_t_nam4km);
        $lineplot_nam4km->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_nam4km->mark->SetWidth(3);
        $lineplot_nam4km->mark->SetFillColor("dimgray");
    } else {
        $lineplot_nam4km = new LinePlot($nam4km_var, $buf_t_nam4km);
        $lineplot_nam4km->SetColor("dimgray");
        $lineplot_nam4km->SetWeight(3);
    }
    if ($var1 == "snow_accum") {
        if ($ratio != 0) {
            $lineplot_nam4km->SetLegend("" . $buf_nam4km_init . "z NAM 4km " . $ratio . ":1");
            $graph->Add($lineplot_nam4km);
            $lineplot_nam4km->SetColor("dimgray");
        }
    } elseif ($var1 == "wind") {
        if ($mean == 1) {
            $lineplot_nam4km->SetLegend("" . $buf_nam4km_init . "z NAM 4km");
            $graph->Add($lineplot_nam4km);
            $lineplot_nam4km->SetColor("dimgray");
        }
    } else {
        $lineplot_nam4km->SetLegend("" . $buf_nam4km_init . "z NAM 4km");
        $graph->Add($lineplot_nam4km);
        $lineplot_nam4km->SetColor("dimgray");
    }
}

if ($nam4km == 1 && !empty($nam4km_var1) && !empty($buf_t_nam4km) && count($nam4km_var1) == count($buf_t_nam4km)) {
    $lineplot_nam4km1 = new LinePlot($nam4km_var1, $buf_t_nam4km);
    $lineplot_nam4km1->SetColor("dimgray");
    $lineplot_nam4km1->SetWeight(3);
    $lineplot_nam4km1->SetStyle("dashed");
    if ($var1 == "snow_accum") {
        if ($max_t == 1) {
            $lineplot_nam4km1->SetLegend("" . $buf_nam4km_init . "z NAM 4km Max-T Prof");
            $graph->Add($lineplot_nam4km1);
            $lineplot_nam4km1->SetColor("dimgray");
        }
    } elseif ($var1 == "wind") {
        if ($mean_mt == 1) {
            $lineplot_nam4km1->SetLegend("" . $buf_nam4km_init . "z NAM 4km Mean MT");
            $graph->Add($lineplot_nam4km1);
            $lineplot_nam4km1->SetColor("dimgray");
        }
    } else {
        $lineplot_nam4km1->SetLegend("" . $buf_nam4km_init . "z NAM 4km");
        $graph->Add($lineplot_nam4km1);
        $lineplot_nam4km1->SetColor("dimgray");
    }
}

if ($var1 == "snow_accum") {
    if ($nam4km == 1 && $cobb == 1 && !empty($nam4km_var2) && !empty($nam4km_cobb_time) && count($nam4km_var2) == count($nam4km_cobb_time)) {
        $lineplot_nam4km2 = new LinePlot($nam4km_var2, $nam4km_cobb_time);
        $lineplot_nam4km2->SetColor("turquoise1");
        $lineplot_nam4km2->SetWeight(3);
        $lineplot_nam4km2->SetStyle("dashed");
        $lineplot_nam4km2->SetLegend("" . $nam4km_cobb_init . "z NAM 4km Cobb v5.5");
        $graph->Add($lineplot_nam4km2);
        $lineplot_nam4km2->SetColor("turquoise1");
    }
} else {
    if ($nam4km == 1 && !empty($nam4km_var2) && !empty($buf_t_nam4km) && count($nam4km_var2) == count($buf_t_nam4km)) {
        $lineplot_nam4km2 = new LinePlot($nam4km_var2, $buf_t_nam4km);
        $lineplot_nam4km2->SetColor("turquoise1");
        $lineplot_nam4km2->SetWeight(3);
        $lineplot_nam4km2->SetStyle("dashed");
        if ($var1 == "wind") {
            $lineplot_nam4km2->SetLegend("" . $buf_nam4km_init . "z NAM 4km Max MT");
            if ($max_mt == 1) {
                $graph->Add($lineplot_nam4km2);
                $lineplot_nam4km2->SetColor("turquoise1");
            }
        } else {
            $lineplot_nam4km2->SetLegend("" . $buf_nam4km_init . "z NAM 4km");
            $graph->Add($lineplot_nam4km2);
            $lineplot_nam4km2->SetColor("turquoise1");
        }
    }
}


if ($gfs == 1 && !empty($gfs_var) && !empty($buf_t_gfs) && count($gfs_var) == count($buf_t_gfs)) {
    if ($var == "wdir") {
        $lineplot_gfs = new ScatterPlot($gfs_var, $buf_t_gfs);
        $lineplot_gfs->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_gfs->mark->SetWidth(3);
        $lineplot_gfs->mark->SetFillColor("blue");
    } else {
        $lineplot_gfs = new LinePlot($gfs_var, $buf_t_gfs);
        $lineplot_gfs->SetColor("blue");
        $lineplot_gfs->SetWeight(3);
    }
    if ($var1 == "snow_accum") {
        if ($ratio != 0) {
            $lineplot_gfs->SetLegend("" . $buf_gfs_init . "z GFS " . $ratio . ":1");
            $graph->Add($lineplot_gfs);
        }
    } elseif ($var1 == "wind") {
        if ($mean == 1) {
            $lineplot_gfs->SetLegend("" . $buf_gfs_init . "z GFS");
            $graph->Add($lineplot_gfs);
        }
    } else {
        $lineplot_gfs->SetLegend("" . strval($buf_gfs_init) . "z GFS");
        $graph->Add($lineplot_gfs);
    }
    if ($var == "wdir") {
        $lineplot_gfs->mark->SetFillColor("blue");
    } else {
        $lineplot_gfs->SetColor("blue");
    }
}

if ($gfs == 1 && !empty($gfs_var1) && !empty($buf_t_gfs) && count($gfs_var1) == count($buf_t_gfs)) {
    $lineplot_gfs1 = new LinePlot($gfs_var1, $buf_t_gfs);
    $lineplot_gfs1->SetColor("blue");
    $lineplot_gfs1->SetWeight(3);
    $lineplot_gfs1->SetStyle("dashed");
    if ($var1 == "snow_accum") {
        if ($max_t == 1) {
            $lineplot_gfs1->SetLegend("" . $buf_gfs_init . "z GFS Max-T Prof");
            $graph->Add($lineplot_gfs1);
        }
    } elseif ($var1 == "wind") {
        if ($mean_mt == 1) {
            $lineplot_gfs1->SetLegend("" . $buf_gfs_init . "z GFS Mean MT");
            $graph->Add($lineplot_gfs1);
        }
    } else {
        $lineplot_gfs1->SetLegend("" . $buf_gfs_init . "z GFS");
        $graph->Add($lineplot_gfs1);
    }
    $lineplot_gfs1->SetColor("blue");
}

if ($var1 == "snow_accum") {
    if ($gfs == 1 && $cobb == 1 && !empty($gfs_var2) && !empty($gfs_cobb_time) && count($gfs_var2) == count($gfs_cobb_time)) {
        $lineplot_gfs2 = new LinePlot($gfs_var2, $gfs_cobb_time);
        $lineplot_gfs2->SetColor("purple");
        $lineplot_gfs2->SetWeight(3);
        $lineplot_gfs2->SetStyle("dashed");
        $lineplot_gfs2->SetLegend("" . $gfs_cobb_init . "z GFS Cobb v5.5");
        $graph->Add($lineplot_gfs2);
        $lineplot_gfs2->SetColor("purple");
    }
} else {
    if ($gfs == 1 && !empty($gfs_var2) && !empty($buf_t_gfs) && count($gfs_var2) == count($buf_t_gfs)) {
        $lineplot_gfs2 = new LinePlot($gfs_var2, $buf_t_gfs);
        $lineplot_gfs2->SetColor("purple");
        $lineplot_gfs2->SetWeight(3);
        $lineplot_gfs2->SetStyle("dashed");
        if ($var1 == "wind") {
            $lineplot_gfs2->SetLegend("" . $buf_gfs_init . "z GFS Max MT");
            if ($max_mt == 1) {
                $graph->Add($lineplot_gfs2);
            }
        } else {
            $lineplot_gfs2->SetLegend("" . $buf_gfs_init . "z GFS");
            $graph->Add($lineplot_gfs2);
        }
        $lineplot_gfs2->SetColor("purple");
    }
}

if ($gfsm == 1 && !empty($gfsm_var) && !empty($buf_t_gfsm) && count($gfsm_var) == count($buf_t_gfsm)) {
    if ($var == "wdir") {
        $lineplot_gfsm = new ScatterPlot($gfsm_var, $buf_t_gfsm);
        $lineplot_gfsm->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_gfsm->mark->SetWidth(3);
        $lineplot_gfsm->mark->SetFillColor("darkblue");
    } else {
        $lineplot_gfsm = new LinePlot($gfsm_var, $buf_t_gfsm);
        $lineplot_gfsm->SetColor("darkblue");
        $lineplot_gfsm->SetWeight(3);
    }
    if ($var1 == "snow_accum") {
        if ($ratio != 0) {
            $lineplot_gfsm->SetLegend("" . $buf_gfsm_init . "z GFS " . $ratio . ":1");
            $graph->Add($lineplot_gfsm);
        }
    } elseif ($var1 == "wind") {
        if ($mean == 1) {
            $lineplot_gfsm->SetLegend("" . $buf_gfsm_init . "z GFS");
            $graph->Add($lineplot_gfsm);
        }
    } else {
        $lineplot_gfsm->SetLegend("" . strval($buf_gfsm_init) . "z GFS");
        $graph->Add($lineplot_gfsm);
    }
    if ($var != "wdir") {
        $lineplot_gfsm->SetColor("darkblue");
    }
}

if ($gfsm == 1 && !empty($gfsm_var1) && !empty($buf_t_gfsm) && count($gfsm_var1) == count($buf_t_gfsm)) {
    $lineplot_gfsm1 = new LinePlot($gfsm_var1, $buf_t_gfsm);
    $lineplot_gfsm1->SetColor("darkblue");
    $lineplot_gfsm1->SetWeight(3);
    $lineplot_gfsm1->SetStyle("dashed");
    if ($var1 == "snow_accum") {
        if ($max_t == 1) {
            $lineplot_gfsm1->SetLegend("" . $buf_gfsm_init . "z GFS Max-T Prof");
            $graph->Add($lineplot_gfsm1);
        }
    } elseif ($var1 == "wind") {
        if ($mean_mt == 1) {
            $lineplot_gfsm1->SetLegend("" . $buf_gfsm_init . "z GFS Mean MT");
            $graph->Add($lineplot_gfsm1);
        }
    } else {
        $lineplot_gfsm1->SetLegend("" . $buf_gfsm_init . "z GFS");
        $graph->Add($lineplot_gfsm1);
    }
    $lineplot_gfsm1->SetColor("darkblue");
}

if ($var1 == "snow_accum") {
    if ($gfsm == 1 && $cobb == 1 && !empty($gfsm_var2) && !empty($gfsm_cobb_time) && count($gfsm_var2) == count($gfsm_cobb_time)) {
        $lineplot_gfsm2 = new LinePlot($gfsm_var2, $gfsm_cobb_time);
        $lineplot_gfsm2->SetColor("yellow");
        $lineplot_gfsm2->SetWeight(3);
        $lineplot_gfsm2->SetStyle("dashed");
        $lineplot_gfsm2->SetLegend("" . $gfsm_cobb_init . "z GFS Cobb v5.5");
        $graph->Add($lineplot_gfsm2);
        $lineplot_gfsm2->SetColor("yellow");
    }
} else {
    if ($gfsm == 1 && !empty($gfsm_var2) && !empty($buf_t_gfsm) && count($gfsm_var2) == count($buf_t_gfsm)) {
        $lineplot_gfsm2 = new LinePlot($gfsm_var2, $buf_t_gfsm);
        $lineplot_gfsm2->SetColor("yellow");
        $lineplot_gfsm2->SetWeight(3);
        $lineplot_gfsm2->SetStyle("dashed");
        if ($var1 == "wind") {
            $lineplot_gfsm2->SetLegend("" . $buf_gfsm_init . "z GFS Max MT");
            if ($max_mt == 1) {
                $graph->Add($lineplot_gfsm2);
            }
        } else {
            $lineplot_gfsm2->SetLegend("" . $buf_gfsm_init . "z GFS");
            $graph->Add($lineplot_gfsm2);
        }
        $lineplot_gfsm2->SetColor("yellow");
    }
}


if ($nam_mos == 1 && !empty($nam_mos_var) && !empty($nam_mos_time) && count($nam_mos_var) == count($nam_mos_time)) {
    if ($var == "wdir") {
        $lineplot_nam_mos = new ScatterPlot($nam_mos_var, $nam_mos_time);
        $lineplot_nam_mos->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_nam_mos->mark->SetWidth(3);
        $lineplot_nam_mos->mark->SetFillColor("orange2");
    } else {
        $lineplot_nam_mos = new LinePlot($nam_mos_var, $nam_mos_time);
        $lineplot_nam_mos->SetColor("orange2");
        $lineplot_nam_mos->SetWeight(3);
    }
    $lineplot_nam_mos->SetLegend("" . strval($buf_nam_init) . "z NAM MOS");
    $graph->Add($lineplot_nam_mos);
    if ($var != "wdir") {
        $lineplot_nam_mos->SetColor("orange2");
    }
}

if ($gfs_mos == 1 && !empty($gfs_mos_var) && !empty($gfs_mos_time) && count($gfs_mos_var) == count($gfs_mos_time)) {
    if ($var == "wdir") {
        $lineplot_gfs_mos = new ScatterPlot($gfs_mos_var, $gfs_mos_time);
        $lineplot_gfs_mos->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_gfs_mos->mark->SetWidth(3);
        $lineplot_gfs_mos->mark->SetFillColor("purple");
    } else {
        $lineplot_gfs_mos = new LinePlot($gfs_mos_var, $gfs_mos_time);
        $lineplot_gfs_mos->SetColor("purple");
        $lineplot_gfs_mos->SetWeight(3);
    }
    $lineplot_gfs_mos->SetLegend("" . strval($buf_gfs_init) . "z GFS MOS");
    $graph->Add($lineplot_gfs_mos);
    if ($var != "wdir") {
        $lineplot_gfs_mos->SetColor("purple");
    }
}

if ($gfsm_mos == 1 && !empty($gfsm_mos_var) && !empty($gfsm_mos_time) && count($gfsm_mos_var) == count($gfsm_mos_time)) {
    if ($var == "wdir") {
        $lineplot_gfsm_mos = new ScatterPlot($gfsm_mos_var, $gfsm_mos_time);
        $lineplot_gfsm_mos->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_gfsm_mos->mark->SetWidth(3);
        $lineplot_gfsm_mos->mark->SetFillColor("yellow");
    } else {
        $lineplot_gfsm_mos = new LinePlot($gfsm_mos_var, $gfsm_mos_time);
        $lineplot_gfsm_mos->SetColor("yellow");
        $lineplot_gfsm_mos->SetWeight(3);
    }
    $lineplot_gfsm_mos->SetLegend("" . strval($buf_gfsm_init) . "z GFS MOS");
    $graph->Add($lineplot_gfsm_mos);
    if ($var != "wdir") {
        $lineplot_gfsm_mos->SetColor("yellow");
    }
}

if ($rap == 1 && !empty($rap_var) && !empty($buf_t_rap) && count($rap_var) == count($buf_t_rap)) {
    if ($var == "wdir") {
        $lineplot_rap = new ScatterPlot($rap_var, $buf_t_rap);
        $lineplot_rap->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_rap->mark->SetWidth(3);
        $lineplot_rap->mark->SetFillColor("green");
    } else {
        $lineplot_rap = new LinePlot($rap_var, $buf_t_rap);
        $lineplot_rap->SetColor("green");
        $lineplot_rap->SetWeight(3);
    }
    if ($var1 == "snow_accum") {
        if ($ratio != 0) {
            if ($date >= 2012050100 || $date == "") {
                $lineplot_rap->SetLegend("" . $buf_rap_init . "z RAP " . $ratio . ":1");
            } else {
                $lineplot_rap->SetLegend("" . $buf_rap_init . "z RUC " . $ratio . ":1");
            }
            $graph->Add($lineplot_rap);
        }
    } elseif ($var1 == "wind") {
        if ($mean == 1) {
            if ($date >= 2012050100 || $date == "") {
                $lineplot_rap->SetLegend("" . $buf_rap_init . "z RAP");
            } else {
                $lineplot_rap->SetLegend("" . $buf_rap_init . "z RUC");
            }
            $graph->Add($lineplot_rap);
        }
    } else {
        if ($date >= 2012050100 || $date == "") {
            $lineplot_rap->SetLegend("" . strval($buf_rap_init) . "z RAP");
        } else {
            $lineplot_rap->SetLegend("" . strval($buf_rap_init) . "z RUC");
        }
        $graph->Add($lineplot_rap);
    }
    if ($var != "wdir") {
        $lineplot_rap->SetColor("green");
    }
}

if ($rap == 1 && !empty($rap_var1) && !empty($buf_t_rap) && count($rap_var1) == count($buf_t_rap)) {
    $lineplot_rap1 = new LinePlot($rap_var1, $buf_t_rap);
    $lineplot_rap1->SetColor("green");
    $lineplot_rap1->SetWeight(3);
    $lineplot_rap1->SetStyle("dashed");
    if ($var1 == "snow_accum") {
        if ($max_t == 1) {
            if ($date >= 2012050100 || $date == "") {
                $lineplot_rap1->SetLegend("" . $buf_rap_init . "z RAP Max-T Prof");
            } else {
                $lineplot_rap1->SetLegend("" . $buf_rap_init . "z RUC Max-T Prof");
            }
            $graph->Add($lineplot_rap1);
            $lineplot_rap1->SetColor("green");
        }
    } elseif ($var1 == "wind") {
        if ($mean_mt == 1) {
            if ($date >= 2012050100 || $date == "") {
                $lineplot_rap1->SetLegend("" . $buf_rap_init . "z RAP Mean MT");
            } else {
                $lineplot_rap1->SetLegend("" . $buf_rap_init . "z RUC Mean MT");
            }
            $graph->Add($lineplot_rap1);
            $lineplot_rap1->SetColor("green");
        }
    } else {
        if ($date >= 2012050100 || $date == "") {
            $lineplot_rap1->SetLegend("" . $buf_rap_init . "z RAP");
        } else {
            $lineplot_rap1->SetLegend("" . $buf_rap_init . "z RUC");
        }
        $graph->Add($lineplot_rap1);
        $lineplot_rap1->SetColor("green");
    }
}

if ($rap == 1 && !empty($rap_var2) && !empty($buf_t_rap) && count($rap_var2) == count($buf_t_rap)) {
    $lineplot_rap2 = new LinePlot($rap_var2, $buf_t_rap);
    $lineplot_rap2->SetColor("darkgreen");
    $lineplot_rap2->SetWeight(3);
    $lineplot_rap2->SetStyle("dashed");
    if ($var1 == "wind") {
        if ($date >= 2012050100 || $date == "") {
            $lineplot_rap2->SetLegend("" . $buf_rap_init . "z RAP Max MT");
        } else {
            $lineplot_rap2->SetLegend("" . $buf_rap_init . "z RUC Max MT");
        }
        if ($max_mt == 1) {
            $graph->Add($lineplot_rap2);
        }
    } else {
        if ($date >= 2012050100 || $date == "") {
            $lineplot_rap2->SetLegend("" . $buf_rap_init . "z RAP");
        } else {
            $lineplot_rap2->SetLegend("" . $buf_rap_init . "z RUC");
        }
        $graph->Add($lineplot_rap2);
    }
    $lineplot_rap2->SetColor("darkgreen");
}

if ($con == 1 && !empty($consensus) && !empty($consensus_t) && count($consensus) == count($consensus_t)) {
    if ($var == "wdir") {
        $lineplot_c = new ScatterPlot($consensus, $consensus_t);
        $lineplot_c->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_c->mark->SetWidth(3);
        $lineplot_c->mark->SetFillColor("white");
    } else {
        $lineplot_c = new LinePlot($consensus, $consensus_t);
        $lineplot_c->SetColor("white");
        $lineplot_c->SetWeight(3);
        $lineplot_c->mark->SetType(MARK_SQUARE);
        $lineplot_c->mark->SetFillColor('white');
        $lineplot_c->mark->SetWidth(3);
    }
    if ($var1 == "snow_accum") {
        $lineplot_c->SetLegend("Model Avg. " . $ratio . ":1");
        $graph->Add($lineplot_c);
    } elseif ($var1 == "wind") {
        if ($mean == 1) {
            $lineplot_c->SetLegend("Model Avg. " . $ratio . ":1");
            $graph->Add($lineplot_c);
        }
    } else {
        $lineplot_c->SetLegend("Model Avg.");
        $graph->Add($lineplot_c);
    }
    if ($var != "wdir") {
        $lineplot_c->SetColor("white");
    }
}

if ($con == 1 && !empty($consensus1) && !empty($consensus_t1) && count($consensus1) == count($consensus_t1)) {
    $lineplot_c1 = new LinePlot($consensus1, $consensus_t1);
    $lineplot_c1->SetColor("white");
    $lineplot_c1->SetWeight(3);
    $lineplot_c1->mark->SetType(MARK_FILLEDCIRCLE);
    $lineplot_c1->mark->SetFillColor('white');
    $lineplot_c1->mark->SetWidth(3);
    if ($var1 == "snow_accum") {
        if ($max_t == 1) {
            $lineplot_c1->SetLegend("Max-T Prof Avg.");
            $graph->Add($lineplot_c1);
        }
    } elseif ($var1 == "wind") {
        if ($mean_mt == 1) {
            $lineplot_c1->SetLegend("Mean MT Avg.");
            $graph->Add($lineplot_c1);
        }
    } else {
        $lineplot_c1->SetLegend("Model Avg.");
        $graph->Add($lineplot_c1);
    }
    $lineplot_c1->SetColor("white");
}

if ($con == 1 && !empty($consensus2) && !empty($consensus_t2) && count($consensus2) == count($consensus_t2)) {
    $lineplot_c2 = new LinePlot($consensus2, $consensus_t2);
    $lineplot_c2->SetColor("white");
    $lineplot_c2->SetWeight(3);
    $lineplot_c2->mark->SetType(MARK_DTRIANGLE);
    $lineplot_c2->mark->SetFillColor('white');
    $lineplot_c2->mark->SetWidth(3);
    if ($var1 == "snow_accum") {
        if ($cobb == 1) {
            $lineplot_c2->SetLegend("Model Cobb Avg.");
            $graph->Add($lineplot_c2);
        }
    } elseif ($var1 == "wind") {
        if ($max_mt == 1) {
            $lineplot_c2->SetLegend("Max MT Avg.");
            $graph->Add($lineplot_c2);
        }
    } else {
        $lineplot_c2->SetLegend("Model Avg.");
        $graph->Add($lineplot_c2);
    }
    $lineplot_c2->SetColor("white");
}


if ($nws == 1 && !empty($nws_var) && !empty($nws_time) && count($nws_var) == count($nws_time)) {
    if ($var == "wdir") {
        $lineplot_nws = new ScatterPlot($nws_var, $nws_time);
        $lineplot_nws->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot_nws->mark->SetWidth(3);
        $lineplot_nws->mark->SetFillColor("darkgreen");
    } else {
        $lineplot_nws = new LinePlot($nws_var, $nws_time);
        $lineplot_nws->SetColor("darkgreen");
        $lineplot_nws->mark->SetType(MARK_SQUARE);
        $lineplot_nws->mark->SetFillColor('darkgreen');
        $lineplot_nws->SetWeight(3);
    }
    if ($var1 == "wind") {
        if ($mean == 1) {
            $lineplot_nws->SetLegend("NWS Mean");
            $graph->Add($lineplot_nws);
        }
    } else {
        $lineplot_nws->SetLegend("NWS");
        $graph->Add($lineplot_nws);
    }
    if ($var != "wdir") {
        $lineplot_nws->SetColor("darkgreen");
    }
}

if ($nws == 1 && !empty($nws_var1) && !empty($nws_time1) && count($nws_var1) == count($nws_time1)) {
    $lineplot_nws1 = new LinePlot($nws_var1, $nws_time1);
    $lineplot_nws1->SetColor("darkgreen");
    $lineplot_nws1->mark->SetType(MARK_FILLEDCIRCLE);
    $lineplot_nws1->mark->SetFillColor('darkgreen');
    $lineplot_nws1->mark->SetWidth(3);
    $lineplot_nws1->SetWeight(3);
    if ($var1 == "wind") {
        if ($max_mt == 1 || $mean_mt == 1) {
            $lineplot_nws1->SetLegend("NWS Gust");
            $graph->Add($lineplot_nws1);
        }
    } else {
        $lineplot_nws1->SetLegend("NWS");
        $graph->Add($lineplot_nws1);
    }
}

if ($obs == 1 && !empty($obs_var) && !empty($obs_time) && count($obs_var) == count($obs_time)) {
    $lineplot_obs = new ScatterPlot($obs_var, $obs_time);
    $lineplot_obs->mark->SetType(MARK_FILLEDCIRCLE);
    $lineplot_obs->mark->SetWidth(3);
    $lineplot_obs->mark->SetFillColor("black");
    if ($var1 == "wind") {
        if ($mean == 1) {
            $lineplot_obs->SetLegend("OBS Mean - " . $ob_station . "");
            $graph->Add($lineplot_obs);
        }
    } else {
        $lineplot_obs->SetLegend("OBS - " . $ob_station . "");
        $graph->Add($lineplot_obs);
    }
}

if ($obs == 1 && !empty($obs_var1) && !empty($obs_time) && count($obs_var1) == count($obs_time) && array_sum($obs_var1) > 0) {
    $lineplot_obs1 = new ScatterPlot($obs_var1, $obs_time);
    $lineplot_obs1->mark->SetType(MARK_FILLEDCIRCLE);
    $lineplot_obs1->mark->SetWidth(4);
    $lineplot_obs1->mark->SetFillColor("cyan1");
    if ($var1 == "wind") {
        if ($mean_mt = 1 || $max_mt == 1) {
            $lineplot_obs1->SetLegend("OBS Gust - " . $ob_station . "");
            $graph->Add($lineplot_obs1);
        }
    } else {
        $lineplot_obs1->SetLegend("OBS - " . $ob_station . "");
        $graph->Add($lineplot_obs1);
    }
    $lineplot_obs1->mark->SetFillColor("cyan1");
}

if ($freese != "no" && !empty($freese_time)) {
    $lineplot_freese = new LinePlot($freese_wind, $freese_time);
    $lineplot_freese->SetColor("black");
    $lineplot_freese->SetWeight(6);
    $lineplot_freese->SetLegend("Freese-Notis - " . $freese_id . "");
    $graph->Add($lineplot_freese);
    $lineplot_freese->SetColor("black");
}

if ($var1 == "snow_accum") {
    if ($compaction == 1) {
        $icon = new IconPlot('sa_correction.png', 0.835, 0, 0.38, 100);
    } else {
        $icon = new IconPlot('sa_no_correction.png', 0.88, 0, 0.38, 100);
    }
    $graph->Add($icon);
}

$isu = new IconPlot('isu.png', 0.37, 0.29, 1, 15);
$graph->Add($isu);

$txt = new Text("Start: " . date("Y-m-d H:i:s", $min) . " UTC", 26, 0);
$graph->Add($txt);
$txt = new Text("End: " . date("Y-m-d H:i:s", $max) . " UTC", 38, 10);
$graph->Add($txt);
$txt = new Text("Generated: " . date("Y-m-d H:i:s") . " UTC", 2, 20);
$graph->Add($txt);

$graph->Stroke();
