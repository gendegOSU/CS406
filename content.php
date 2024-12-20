<?php

// Depending on the URL requested, return page associated with that URL name

$existing_pages = array('Jobs','TaskTypes','Customers', 'Employees', 'EmployeesRoles', 'Roles','Equipment', 'EquipmentTypes', 'ManageWork');

if(isset($_GET['page']) && $_GET['page'] == 'ManageWork') {
    include 'ManageWork.php';
} 
elseif (isset($_GET['page']) && in_array($_GET['page'], $existing_pages)) {
    include 'lists.php';
}


?>