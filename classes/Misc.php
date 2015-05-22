<?php

class Misc {
	public static function formatDate($mydate) {
    // Take the format from the config
    $dateformat = Config::get('dateformat');
    $format_date = date_create($mydate);
    // If the input is empty, return empty (or else today's date is returned...)
    if($mydate=="") {
        return "";
    } else {
        return date_format($format_date,$dateformat);
    }

	}

	public static function dump($data,$stop) {
		echo "<pre>";
        var_dump($data);
        echo "</pre>";
        if($stop) {
        	exit();
        }
	}
    public static function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}