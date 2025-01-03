<?php

function append_report_message($input_str) {
    /* Adds the given string to $reportmessage */
    global $reportmessage;

    if (isset($reportmessage)) {
        $reportmessage .= ' --- <br> --- '.$input_str;
    } else {
        $reportmessage = $input_str;
    }
}

function get_pagetype_ref($type = '') {
    /* Returns $page_sub if it exsits, otherwise returns $pagetype
       Passing 'link' as an argument returns the full URL GET reference with $pagetype and $page_sub */
    global $pagetype;
    global $page_sub;
    global $side_nav_pages;

    if ($type === 'link') {
        return (in_array($pagetype, $side_nav_pages) ? '?page='.$pagetype.'&sub='.$page_sub : '?page='.$pagetype);
    } else {
        return (in_array($pagetype, $side_nav_pages) && isset($page_sub) ? $page_sub : $pagetype);
    }
}

function get_dropdown_options($results, $selected = NULL) {
    /* Take a mysqli_result object with option names and option IDs as input and returns an HTML list of options for a dropdown input
       Passing a $selected value will make that option selected */

    $key_list = array_keys($results->fetch_assoc());
    foreach ($key_list as $key) {
        if (substr($key,-4) == 'Name') $name = $key;
        elseif (substr($key,-2) == 'Id') $id = $key;
    }

    $options = '';
    $results->data_seek(0);
    foreach($results as $row){
        $options .= '<option value="'.$row[$id].'"';
        $options .= ($selected === $row[$name] ? ' selected' : '');
        $options .= '>'.$row[$name].'</option>';
    }
    return $options;
}


?>