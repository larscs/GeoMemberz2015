<?php
function formatDate($mydate){
    // Take the format from the config
    global $dateformat;
    $format_date = date_create($mydate);
    // If the input is empty, return empty (or else today's date is returned...)
    if($mydate=="") {
        return "";
    } else {
        return date_format($format_date,$dateformat);
    }
}
function connectDB() {
    global $dbhost, $dbuser, $dbpass, $dbname;
    $link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname)
        or die(_("Could not connect:") . mysql_error());
    mysqli_set_charset($link,"utf8");
    return $link;
}
function disconnectDB($link) {
    mysqli_close($link);
}
function countRecords($link, $q) {
    $result=mysqli_query($link,$q);
    if($result) {
        $rowcnt = mysqli_num_rows($result);
        mysqli_free_result($result);
        return $rowcnt;
    } else {
        return false;
    }
}
?>