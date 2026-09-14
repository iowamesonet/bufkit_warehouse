<?php
// Utility functions
require_once dirname(__FILE__) . "/../config/settings.php";

/**
 * Fetches the contents of a given URL and returns it as an array of lines.
 * @param string $partial The partial URL to fetch data from.
 * @return array An array of lines from the fetched data.
 */
function get_realtime_lines($partial) {
    $data = file_get_contents(METFS1 . "bufkit/". $partial);
    if ($data === FALSE) {
        die("Failed to retrieve data from `$partial`.");
    }
    return explode("\n", $data);
}

/**
 * Compute the archive-mode `date=` query param passed to data/parser.php per model
 *
 * @param string $mdl The model name (e.g., "nam", "gfs", "rap")
 * @param int $archive_check Flag indicating if archive mode is active (1 for archive mode)
 * @param int $year The year component of the date
 * @param int $month The month component of the date
 * @param int $day The day component of the date
 * @param int $hour The hour component of the date
 * @return string|null The computed parse date string or null if not applicable
 */
function compute_model_parse_date($mdl, $archive_check, $year, $month, $day, $hour)
{
    if ($mdl == "rap") {
        return "" . $year . "" . $month . "" . $day . "" . $hour . "";
    }
    if ($archive_check != 1) {
        return null;
    }
    if (in_array($mdl, array("nam", "gfs", "nam4km"))) {
        if ($hour >= 0 && $hour <= 11) {
            return "" . $year . "" . $month . "" . $day . "00";
        }
        return "" . $year . "" . $month . "" . $day . "12";
    }
    // namm, gfsm
    if ($hour >= 0 && $hour <= 5) {
        $temp_date = strtotime("" . $year . "-" . $month . "-" . $day . " 00:00:00") - 21600;
        return date("YmdH", $temp_date);
    } elseif ($hour >= 6 && $hour <= 17) {
        return "" . $year . "" . $month . "" . $day . "06";
    }
    return "" . $year . "" . $month . "" . $day . "18";
}

/**
 * Get the wind direction from the wind angle
 * @param float $cam_ang The wind angle in degrees
 * @return string The wind direction as a compass point
 */
function get_wind_dir($cam_ang)
{
    $dir = "N";
    if (11 < $cam_ang && $cam_ang <= 34) {
        $dir = "NNE";
    } elseif (34 < $cam_ang && $cam_ang <= 45) {
        $dir = "NE";
    } elseif (45 < $cam_ang && $cam_ang <= 56) {
        $dir = "NE";
    } elseif (56 < $cam_ang && $cam_ang <= 79) {
        $dir = "ENE";
    } elseif (79 < $cam_ang && $cam_ang <= 101) {
        $dir = "E";
    } elseif (101 < $cam_ang && $cam_ang <= 124) {
        $dir = "ESE";
    } elseif (124 < $cam_ang && $cam_ang <= 135) {
        $dir = "SE";
    } elseif (135 < $cam_ang && $cam_ang <= 146) {
        $dir = "SE";
    } elseif (146 < $cam_ang && $cam_ang <= 169) {
        $dir = "SSE";
    } elseif (169 < $cam_ang && $cam_ang <= 191) {
        $dir = "S";
    } elseif (191 < $cam_ang && $cam_ang <= 214) {
        $dir = "SSW";
    } elseif (214 < $cam_ang && $cam_ang <= 225) {
        $dir = "SW";
    } elseif (225 < $cam_ang && $cam_ang <= 236) {
        $dir = "SW";
    } elseif (236 < $cam_ang && $cam_ang <= 259) {
        $dir = "WSW";
    } elseif (259 < $cam_ang && $cam_ang <= 281) {
        $dir = "W";
    } elseif (281 < $cam_ang && $cam_ang <= 304) {
        $dir = "WNW";
    } elseif (304 < $cam_ang && $cam_ang <= 315) {
        $dir = "NW";
    } elseif (315 < $cam_ang && $cam_ang <= 326) {
        $dir = "NW";
    } elseif (326 < $cam_ang && $cam_ang <= 349) {
        $dir = "NNW";
    } elseif (349 < $cam_ang && $cam_ang <= 360) {
        $dir = "N";
    }
    return $dir;
}

/**
 * Compute the "apparent temperature" used for MOS output, considering wind chill and heat index effects.
 *
 * @param float $tempF The air temperature in degrees Fahrenheit
 * @param float $dptF The dew point temperature in degrees Fahrenheit
 * @param float $rawWind The wind speed in mph
 * @return float The computed apparent temperature in degrees Fahrenheit
 */
function mos_hiwc($tempF, $dptF, $rawWind)
{
    $tempC = ($tempF - 32) * (5 / 9);
    $dptC = ($dptF - 32) * (5 / 9);
    $rh = 100 * (exp(((1 / ($dptC + 273.15)) - (1 / ($tempC + 273.15))) / (-461.495 / 2500000)));
    if ($tempF >= 80 && $dptC >= 12) {
        return -42.379 + (2.04901523 * $tempF) + (10.14333127 * $rh) + (-0.22475541 * $tempF * $rh) + (-0.00683783 * $tempF * $tempF) + (-0.05481717 * $rh * $rh) + (0.00122874 * $tempF * $tempF * $rh) + (0.00085282 * $tempF * $rh * $rh) + (-0.00000199 * $tempF * $tempF * $rh * $rh);
    } elseif ($tempF > 50 || $rawWind == 0) {
        return $tempF;
    }
    return 35.74 + (0.6215 * $tempF) - (35.75 * pow($rawWind, 0.16)) + ((0.4275 * $tempF) * pow($rawWind, 0.16));
}


/**
 * Render the HTML table cells for a single data source's row in the legend.
 *
 * @param string $label The label for the data source (e.g., "NAM", "GFS")
 * @param string|null $date The date string for the row, or null if not applicable
 * @param array $cols The array of column values to display
 * @param int $width The width of each table cell (default: 83)
 * @param bool $bold Whether to render the text in bold (default: false)
 * @return array An array of HTML table cell strings
 */
function render_source_cells($label, $date, $cols, $width = 83, $bold = false)
{
    $colors = array("0000FF", "000099", "CC0000", "006600", "000000", "000000");
    $b1 = $bold ? "<b>" : "";
    $b2 = $bold ? "</b>" : "";
    $cells = array("<td width=$width align=center><font color=000000>" . $b1 . $label . $b2 . "</font></td>");
    if ($date === null) {
        for ($c = 0; $c < 7; $c++) {
            $cells[] = "<td width=$width align=center></td>";
        }
        return $cells;
    }
    $cells[] = "<td width=$width align=center><font color=000000>" . $b1 . $date . $b2 . "</font></td>";
    foreach ($cols as $c => $val) {
        $cells[] = "<td width=$width align=center><font color=" . $colors[$c] . ">" . $b1 . $val . $b2 . "</font></td>";
    }
    return $cells;
}

/**
 * Append a unit string to each value in an array.
 *
 * @param array $values The array of values to which the unit should be appended
 * @param string $unit The unit string to append
 * @return array The array of values with the unit appended
 */
function append_unit($values, $unit)
{
    $out = array();
    foreach ($values as $v) {
        $out[] = $v . $unit;
    }
    return $out;
}
