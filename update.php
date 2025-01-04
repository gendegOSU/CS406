
<?php

// Update Job details
if (isset($_POST['updateJobId'])) {

    if ($_POST['streetAddress'] != '') {
        $newJobName = $_POST['streetAddress'];
    } else {
        $customer_query = $purple_db->prepare('SELECT customerName FROM Customers WHERE customerId = ?;');
        $customer_query->bind_param('i', $_POST['customerId']);
        $customer_query->execute();
        $customer_result = $customer_query->get_result()->fetch_assoc();

        $newJobName = $customer_result['customerName'].' - '.$_POST['jobStartDate'].' to '.$_POST['jobEndDate'];
    }

    $update_job_query = $purple_db->prepare('UPDATE Jobs SET customerId = ?, jobTypeId = ?, jobStatusId = ?, jobName = ?, jobStartDate = ?, jobEndDate = ?, jobNotes = ?, locationName = ?, streetAddress = ?, city = ?, state = ?, followUpDate = ?  WHERE jobId = ?;');
    $update_job_query->bind_param('iiisssssssssi', $_POST['customerId'], $_POST['jobTypeId'], $_POST['jobStatusId'], $newJobName, $_POST['jobStartDate'], $_POST['jobEndDate'], $_POST['jobNotes'], $_POST['locationName'], $_POST['streetAddress'], $_POST['city'], $_POST['state'], $_POST['followUpDate'], $_POST['updateJobId']);
    $update_job_query->execute();
}

    // Update TasksRoles details
    if (isset($_POST['updateRoleTaskRoleId'])) {

        $check_task_role_query = $purple_db -> prepare('SELECT * FROM TasksRoles WHERE roleId = ? and taskId = ?;');
        $check_task_role_query = $purple_db -> bind_param('ii',$_POST['roleId'], $_POST['updateRoleTaskId']);
        $check_task_role_query = $purple_db ->execute();
        $check_task_role = $check_task_role_query -> get_result().fetch_assoc();
    }

        if (isset($check_task_role['taskId'], $check_task_role['roleId'])) {

        $update_tasksRoles_query = $purple_db->prepare('UPDATE TasksRoles SET roleId = ?, roleQuantity = ?  WHERE taskRoleId = ?;');
        $update_tasksRoles_query->bind_param('iii', $_POST['roleId'], $_POST['roleQuantity'], $_POST['updateRoleTaskRoleId']);
        $update_tasksRoles_query->execute();
        }

    // Update Employee
    if (isset($_POST['updateEmployeeTaskRoleId'])) {
        $update_TasksRolesEmployees_query = $purple_db->prepare('UPDATE TasksRolesEmployees SET employeeId = ? WHERE taskRoleId = ?;');
        $update_TasksRolesEmployees_query->bind_param('ii', $_POST['employeeId'], $_POST['updateEmployeeTaskRoleId']);
        $update_TasksRolesEmployees_query->execute();
    } 


    // Add New Task Equipment and/or Update Equipment Quantity
if (isset($_POST['UpdateEquip'])) {

    // Query if this task equipment relationship already exists
    $check_task_equip_query = $purple_db->prepare('SELECT * FROM TasksEquipment WHERE taskId = ? AND equipmentId = ? ;');
    $check_task_equip_query->bind_param('ii', $_POST['taskId'], $_POST['UpdateEquip']);
    $check_task_equip_query->execute();
    $check_task_equip = $check_task_equip_query->get_result()->fetch_assoc();

    // If it already exists, just update the quantity
    if (isset($check_task_equip['taskId'], $check_task_equip['equipmentId'])) {
        $update_equip_query = $purple_db->prepare('UPDATE TasksEquipment SET quantityUsed = ? WHERE taskId = ? AND equipmentId = ?;');
        $update_equip_query->bind_param('iii', $_POST['UpdateEquipNum'], $_POST['taskId'], $_POST['UpdateEquip']);
        $update_equip_query->execute();
    }

    // If it doesn't already exist, add it
    else {
        $add_equip_query = $purple_db->prepare('INSERT INTO TasksEquipment (taskId, equipmentId, quantityUsed) VALUES (?, ?, ?);');
        $add_equip_query->bind_param('iii', $_POST['taskId'], $_POST['UpdateEquip'], $_POST['UpdateEquipNum']);
        $add_equip_query->execute();
    }
}




// Update Customers
if(isset($_POST['updateCustomer'])) {
    try{
        $update_query = $purple_db->prepare('UPDATE Customers SET customerName = ?, primaryContactName = ?, primaryPhone = ?, primaryEmail = ?, customerNotes = ? WHERE customerId = ?;');
        $update_query->bind_param('sssssi', $_POST['customerName'], $_POST['primaryContactName'], $_POST['primaryPhone'], $_POST['primaryEmail'], $_POST['customerNotes'], $_POST['updateCustomer']);
        $update_query->execute();
        append_report_message('The Customer has been updated');
    } catch (Exception $error) {
        if(strpos($error->getMessage(),'uplicate entry')){
            append_report_message('Customer not updated; Customer Name must be unique');
        } else {
            append_report_message($error->getMessage());
        }
    }
}


// Update Employees
if(isset($_POST['updateEmployee'])) {
    $update_query = $purple_db->prepare('UPDATE Employees SET firstName = ?, lastName = ?, employeePhone = ?, employeeEmail = ? WHERE employeeId = ?;');
    $update_query->bind_param('ssssi', $_POST['firstName'], $_POST['lastName'], $_POST['employeePhone'], $_POST['employeeEmail'], $_POST['updateEmployee']);
    $update_query->execute();

    append_report_message('The Employee has been updated');
}

// Update EmployeesRoles
if(isset($_POST['updateEmployee']) || isset($_POST['addNewEmployee'])) {

    if (isset($_POST['addNewEmployee'])){
        $emp_search = $purple_db->prepare('SELECT employeeId FROM Employees WHERE firstName = ? AND lastName = ? AND employeePhone = ? AND employeeEmail = ?;');
        $emp_search->bind_param('ssss', $_POST['NewFirstName'], $_POST['NewLastName'], $_POST['NewEmployeePhone'], $_POST['NewEmployeeEmail']);
        $emp_search->execute();

        $empId = $emp_search->get_result()->fetch_row()['0'];
    } else {
        $empId = $_POST['updateEmployee'];
    }

    $roles_to_add = [];
    foreach($_POST as $key => $value) {
        if (strpos($key,'oleId')) {
            array_push($roles_to_add, $value);
        }
    }

    $purple_db->begin_transaction();

    try{
        $clear_old_roles = $purple_db->prepare('DELETE FROM EmployeesRoles WHERE employeeId = ?;');
        $clear_old_roles->bind_param('i', $empId);
        $clear_old_roles->execute();

        foreach($roles_to_add as $role) {
            $add_new_roles = $purple_db->prepare('INSERT INTO EmployeesRoles (roleId, employeeId) VALUES(?, ?);');
            $add_new_roles->bind_param('ii', $role, $empId);
            $add_new_roles->execute();
        }

        $purple_db->commit();
        append_report_message('The qualified Roles have been updated');

    } catch (Exception $error) {
        $purple_db->rollback();
        append_report_message($error);
    }
}


// Process updates from list-style pages
if(isset($_POST['updateType'])) {

    // Update TaskTypes
    if($_POST['updateType'] == 'TaskTypes') {
        $update_query = $purple_db->prepare('UPDATE TaskTypes SET taskTypeName = ? WHERE taskTypeId = ?;');
        $update_query->bind_param('si', $_POST['taskTypeName'], $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Task Type has been updated');
    }

    // Update Roles
    if($_POST['updateType'] == 'Roles') {
        $update_query = $purple_db->prepare('UPDATE Roles SET roleName = ? WHERE roleId = ?;');
        $update_query->bind_param('si', $_POST['roleName'], $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Role has been updated');
    }

    // Update Equipment
    if($_POST['updateType'] == 'Equipment') {

        if ($_POST['vendorId'] == 'none') {
            $vendorId = NULL;
        } else {
            $vendorId = $_POST['vendorId'];
        }

        $update_query = $purple_db->prepare('UPDATE Equipment SET equipmentName = ?, equipmentTypeId = ?, equipmentIdentifier = ?, external = ?, vendorId = ? WHERE equipmentId = ?;');
        $update_query->bind_param('sisiii', $_POST['equipmentName'], $_POST['equipmentTypeId'], $_POST['equipmentIdentifier'], $_POST['external'], $vendorId, $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Equipment has been updated');
    }

    // Update EquipmentTypes
    if($_POST['updateType'] == 'EquipTypes') {
        $update_query = $purple_db->prepare('UPDATE EquipmentTypes SET equipmentTypeName = ? WHERE equipmentTypeId = ?;');
        $update_query->bind_param('si', $_POST['equipmentTypeName'], $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Equipment Type has been updated');
    }

    // Update JobStatuses
    if($_POST['updateType'] == 'JobStatuses') {
        $update_query = $purple_db->prepare('UPDATE JobStatuses SET jobStatusName = ?, jobStatusSortOrder = ?, hidden = ? WHERE jobStatusId = ?;');
        $update_query->bind_param('siii', $_POST['jobStatusName'], $_POST['jobStatusSortOrder'], $_POST['hidden'], $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Job Status has been updated');
    }

    // Update JobTypes
    if($_POST['updateType'] == 'JobTypes') {
        $update_query = $purple_db->prepare('UPDATE JobTypes SET jobTypeName = ? WHERE jobTypeId = ?;');
        $update_query->bind_param('si', $_POST['jobTypeName'], $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Job Type has been updated');
    }

    // Update TaskPriorities
    if($_POST['updateType'] == 'TaskPris') {
        $update_query = $purple_db->prepare('UPDATE TaskPriorities SET taskPriorityName = ?, taskPrioritySortOrder = ? WHERE taskPriorityId = ?;');
        $update_query->bind_param('sii', $_POST['taskPriorityName'], $_POST['taskPrioritySortOrder'], $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Task Priority has been updated');
    }

    // Update Vendors
    if($_POST['updateType'] == 'Vendors') {
        $update_query = $purple_db->prepare('UPDATE Vendors SET vendorName = ?, vendorPhone = ?, vendorEmail = ?, vendorStreetAddress = ?, vendorCity = ?, vendorState = ? WHERE vendorId = ?;');
        $update_query->bind_param('ssssssi', $_POST['vendorName'], $_POST['vendorPhone'], $_POST['vendorEmail'], $_POST['vendorStreetAddress'], $_POST['vendorCity'], $_POST['vendorState'], $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Vendor has been updated');
    }

    // Update Materials
    if($_POST['updateType'] == 'Materials') {
        $update_query = $purple_db->prepare('UPDATE Materials SET quantity = ?, lastValidated = ? WHERE materialId = ?;');
        $update_query->bind_param('isi', $_POST['quantity'], $_POST['lastValidated'], $_POST['updateRef']);
        $update_query->execute();

        append_report_message('The Material has been updated');
    }

}