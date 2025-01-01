<?php

$existing_pages = ['Calendar', 'Customers', 'Jobs', 'Employees', 'Equipment', 'Materials', 'Admin'];
if (isset($_GET['page']) && in_array($_GET['page'], $existing_pages)) {
    $pagetype = $_GET['page'];
} else {
    $pagetype = 'Customers';
}

$existing_page_subs = ['Backup', 'EmpInfo', 'EmpRoles', 'EmpTasks', 'EquipTypes', 'JobStatuses', 'JobTypes', 'Roles', 'TaskPris', 'TaskTypes', 'Vendors'];
if (isset($_GET['sub']) && in_array($_GET['sub'], $existing_page_subs)) {
    $page_sub = $_GET['sub'];
} else {
    $page_sub = NULL;
}

$id_set = isset($_GET['Id']);


// Run these scripts before starting to build the page
// These scripts will create a database connection and process any required UPDATEs, INSERTs, and DELETEs
// print_r($_POST);
require 'functions.php';
require 'dbconnection.php';
require 'create.php';
require 'update.php';
require 'delete.php';
?>