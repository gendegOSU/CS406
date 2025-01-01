<?php
$admin_list_pages = ['EquipTypes', 'JobStatuses', 'JobTypes', 'Roles', 'TaskPris', 'TaskTypes', 'Vendors'];

if (in_array($page_sub, $admin_list_pages)) {
    include 'lists.php';
}




//<a href="?page=Admin&sub=EmpRoles">Employee Roles</a><br>
//<a href="?page=Admin&sub=Backup">Data Backup</a>

?>