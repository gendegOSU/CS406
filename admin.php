<?php
$admin_list_pages = ['EquipTypes', 'JobStatuses', 'JobTypes', 'Roles', 'TaskPris', 'TaskTypes', 'Vendors'];

if (in_array($page_sub, $admin_list_pages)) {
    include 'lists.php';
}

if ($page_sub == 'Backup') {
    include 'backup.php';
}

?>