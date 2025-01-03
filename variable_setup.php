<?php

$existing_pages = ['Calendar', 'Customers', 'Jobs', 'Employees', 'Equipment', 'Materials', 'Admin'];
$existing_page_subs = ['Backup', 'EmpDetails', 'EmpTasks', 'EquipTypes', 'JobStatuses', 'JobTypes', 'Roles', 'TaskPris', 'TaskTypes', 'Vendors'];

$id_set = isset($_GET['Id']);

if (isset($_GET['page']) && in_array($_GET['page'], $existing_pages)) {
    $pagetype = $_GET['page'];
} else {
    $pagetype = 'Customers';}

if (isset($_GET['sub']) && in_array($_GET['sub'], $existing_page_subs)) {
    $page_sub = $_GET['sub'];
} else {
    $page_sub = NULL;}

?>