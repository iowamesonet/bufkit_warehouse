<?php

function myround($val, $prec){
    if (is_null($val)) return "";
    if (is_string($val)) return $val;
    return round($val, $prec);
}
