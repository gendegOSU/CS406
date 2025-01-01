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
        return (in_array($pagetype, $side_nav_pages) ? '?page='.$pagetype.'&sub='.$page_sub : $pagetype);
    } else {
        return (in_array($pagetype, $side_nav_pages) ? $page_sub : $pagetype);
    }
}


?>