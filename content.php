<?php

// Depending on the URL requested, return page components associated with that URL name

$side_nav_pages = ['Calendar', 'Employees', 'Admin'];
$list_pages = ['Customers', 'Jobs', 'Materials', 'Employees', 'Equipment'];


if (in_array($pagetype, $side_nav_pages)) {
    include 'side_nav.php';
}


if ($pagetype === 'Calendar') {
    include 'calendar.php';
}
elseif ($pagetype === 'Customers') {
    include 'customers.php';
}
elseif ($pagetype === 'Jobs') {
    include 'jobs.php';
}
elseif ($pagetype === 'Employees') {
    include 'employees.php';
}
elseif ($pagetype === 'Admin') {
    include 'admin.php';
}


if (in_array($pagetype, $side_nav_pages)) {
    include 'close_side_nav.php';
}


if (in_array($pagetype, $list_pages)) {
    include 'lists.php';
}

?>