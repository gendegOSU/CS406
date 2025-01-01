<?php

// Add New Tasks
if (isset($_POST['NewTaskStart'])) {

    $add_task_query = $purple_db->prepare('INSERT INTO Tasks (jobId, taskTypeId, startDate, endDate) VALUES (?, ?, ?, ?);');
    $add_task_query->bind_param('iiss', $_POST['jobId'], $_POST['NewTask_Type'], $_POST['NewTaskStart'], $_POST['NewTaskEnd']);
    $add_task_query->execute();
}


// Add New Task Roles
if (isset($_POST['NewRole'])) {

    // Query if this task Role relationship already exists
    $check_task_role_query = $purple_db->prepare('SELECT * FROM TasksRoles WHERE taskId = ? and roleId = ?;');
    $check_task_role_query->bind_param('ii', $_POST['taskId'], $_POST['NewRole']);
    $check_task_role_query->execute();
    $check_task_role = $check_task_role_query->get_result()->fetch_assoc();

    // If it already exists, just update the quantity
    if (isset($check_task_role['taskId'], $check_task_role['roleId'])) {
        $update_taskRole_query = $purple_db->prepare('UPDATE TasksRoles SET roleQuantity = ((select coalesce(roleQuantity,0) from TasksRoles WHERE taskId = ? AND roleId = ?) + ?) WHERE taskId = ? AND roleId = ?;');
        $update_taskRole_query->bind_param('iiiii', $_POST['taskId'], $_POST['NewRole'], $_POST['NewRoleNum'], $_POST['taskId'], $_POST['NewRole']);
        $update_taskRole_query->execute();
    } else {

    $add_task_role_query = $purple_db->prepare('INSERT INTO TasksRoles (taskId, roleId, roleQuantity) VALUES (?, ?, ?);');
    $add_task_role_query->bind_param('iii', $_POST['taskId'], $_POST['NewRole'], $_POST['NewRoleNum']);
    $add_task_role_query->execute();

    }
}


// Assign Employee To TaskRole
if(isset($_POST['NewRoleEmp']) && $_POST['NewRoleEmp']!=='') {

    include 'ManageWorkQueries.php';

    foreach ($uniqueTaskRoles as $taskRole){
        if($taskRole['taskRoleId'] == $_POST['addTREtaskRoleId']){
            $roleId = $taskRole['roleId'];
        } else {
            continue;
        }
    }

    foreach($employeesRoles_result as $employeeRole){
        if($employeeRole['employeeId'] == $_POST['NewRoleEmp'] && $employeeRole['roleId'] != $roleId){
            // the $confirmed check is necesary so we don't get this message if the employee is qualified for multiple roles
            if(!isset($confirmed)) {
                $reportmessage = 'Employee is not assigned to the specified Role.';
            }
        } else {
            if($employeeRole['employeeId'] == $_POST['NewRoleEmp'] && $employeeRole['roleId'] == $roleId){
                $confirmed = 1;
                unset($reportmessage);
            }
            continue;
        }
    }

    foreach ($uniqueTaskRoles as $taskRole){
        if($taskRole['taskRoleId'] == $_POST['addTREtaskRoleId'] && isset($taskRole['roleQuantity'])){
            $roleQuantityCurr = $taskRole['roleQuantity'];
        } else{
            continue;
        }
    }
    $counter = 0;
    $emp_ids = [];
    foreach($rows as $taskRoleEmployee){
        if(isset($taskRoleEmployee['employeeId']) && $taskRoleEmployee['taskRoleId']== $_POST['addTREtaskRoleId']){
            $counter ++;  
            $emp_ids[] = $taskRoleEmployee['employeeId'];
        } else {
            continue;
        }
    };

    if(in_array($_POST['NewRoleEmp'], $emp_ids)){
        $reportmessage= 'The Employee is already assigned to this Task Role combination.';     
    } elseif( $counter+1 > $roleQuantityCurr){
        $reportmessage = 'Increase the role quantity from '.$roleQuantityCurr.' before assigning more employees.';
    } elseif( !isset($reportmessage) || $reportmessage != 'Employee is not assigned to the specified Role.' ){

        $add_task_role_emp_query = $purple_db->prepare('INSERT INTO TasksRolesEmployees (taskRoleId, employeeId) VALUES ( ?, ?);');
        $add_task_role_emp_query->bind_param('ii', $_POST['addTREtaskRoleId'], $_POST['NewRoleEmp']);
        $add_task_role_emp_query->execute();

    }
};

// Add New Task Equipment and/or Update Equipment Quantity
if (isset($_POST['NewEquip'])) {

    // Query if this task equipment relationship already exists
    $check_task_equip_query = $purple_db->prepare('SELECT * FROM TasksEquipment WHERE taskId = ? AND equipmentId = ? ;');
    $check_task_equip_query->bind_param('ii', $_POST['taskId'], $_POST['NewEquip']);
    $check_task_equip_query->execute();
    $check_task_equip = $check_task_equip_query->get_result()->fetch_assoc();

    // If it already exists, just update the quantity
    if (isset($check_task_equip['taskId'], $check_task_equip['equipmentId'])) {
        $update_equip_query = $purple_db->prepare('UPDATE TasksEquipment SET quantityUsed = ((select coalesce(quantityUsed,0) from TasksEquipment WHERE taskId = ? AND equipmentId = ?) + ?)  WHERE taskId = ? AND equipmentId = ?;');
        $update_equip_query->bind_param('iiiii', $_POST['taskId'], $_POST['NewEquip'], $_POST['NewEquipNum'], $_POST['taskId'], $_POST['NewEquip']);
        $update_equip_query->execute();
    }

    // If it doesn't arleady exist, add it
    else {
        $add_equip_query = $purple_db->prepare('INSERT INTO TasksEquipment (taskId, equipmentId, quantityUsed) VALUES (?, ?, ?);');
        $add_equip_query->bind_param('iii', $_POST['taskId'], $_POST['NewEquip'], $_POST['NewEquipNum']);
        $add_equip_query->execute();
    }
}

// Add New Jobs
if (isset($_POST['NewJobName'])) {

    // Check if the end date is before the start date
    if( preg_replace('/\D+/','',$_POST['NewStartDate']) > preg_replace('/\D+/','',$_POST['NewEndDate']) ){
        $reportmessage = 'The Job was not added because the end date is before the start date';
    }
    // If all is good, add the new job
    else {
        if($_POST['NewCustomerId'] == 'none'){
            $newCustId = NULL;
        } else {
            $newCustId = $_POST['NewCustomerId'];
        }

        $add_job_query = $purple_db->prepare('INSERT INTO Jobs (jobName, customerId, startDate, endDate, locationName, streetAddress, city, state, status, comments)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ? );');
        $add_job_query->bind_param('sissssssss', $_POST['NewJobName'], $newCustId, $_POST['NewStartDate'], $_POST['NewEndDate'], $_POST['NewLocationName'], $_POST['NewStreetAddress'], 
            $_POST['NewCity'], $_POST['NewState'], $_POST['NewStatus'], $_POST['NewComments']);
        $add_job_query->execute();

        $reportmessage = 'The new Job has been added';
    }
}

// Add New Task Types
if (isset($_POST['NewTaskTypeName'])) {

    $add_task_type_query = $purple_db->prepare('INSERT INTO TaskTypes (taskTypeName) VALUES(?);');
    $add_task_type_query->bind_param('s', $_POST['NewTaskTypeName']);
    $add_task_type_query->execute();

    append_report_message('The new Task Type has been added');
}

// Add New Customers
if (isset($_POST['addNewCustomer'])) {

    try {
        $add_customer_query = $purple_db->prepare('INSERT INTO Customers (customerName, primaryContactName, primaryPhone, primaryEmail, customerNotes) VALUES(?, ?, ?, ?, ?);');
        $add_customer_query->bind_param('sssss', $_POST['customerName'], $_POST['primaryContactName'], $_POST['primaryPhone'], $_POST['primaryEmail'], $_POST['customerNotes']);
        $add_customer_query->execute();
        append_report_message('The new Customer has been added');

        if ($_POST['altContactName'] != '' || $_POST['altContactPhone'] != '' || $_POST['altContactEmail'] != '' ) {
            $find_customer_id_query = $purple_db->prepare('SELECT customerId FROM Customers WHERE customerName = ? AND primaryContactName = ? AND primaryPhone = ? AND primaryEmail = ? ;');
            $find_customer_id_query->bind_param('ssss', $_POST['customerName'], $_POST['primaryContactName'], $_POST['primaryPhone'], $_POST['primaryEmail']);
            $find_customer_id_query->execute();
            $new_customer_id = $find_customer_id_query->get_result()->fetch_row();
    
            $add_alt_customer_query = $purple_db->prepare('INSERT INTO AltContacts (customerId, altContactName, altContactPhone, altContactEmail) VALUES(?, ?, ?, ?);');
            $add_alt_customer_query->bind_param('isss', $new_customer_id['0'], $_POST['altContactName'], $_POST['altContactPhone'], $_POST['altContactEmail']);
            $add_alt_customer_query->execute();
    
            append_report_message('The alternate Contact has been added');
        }

    } catch (Exception $error) {
        if(strpos($error->getMessage(),'uplicate entry')){
            append_report_message('Customer not added; Customer name must be unique');
        } else {
            append_report_message($error->getMessage());
        }
    }
}

// Add New Employees
if (isset($_POST['addNewEmployee'])) {

    $add_employee_query = $purple_db->prepare('INSERT INTO Employees (firstName, lastName, employeePhone, employeeEmail) VALUES(?, ?, ?, ?);');
    $add_employee_query->bind_param('ssss', $_POST['NewFirstName'], $_POST['NewLastName'], $_POST['NewEmployeePhone'], $_POST['NewEmployeeEmail']);
    $add_employee_query->execute();

    append_report_message('The new Employee has been added');
}

// Add New Roles
if (isset($_POST['NewRoleName'])) {
    try{
        $add_role_query = $purple_db->prepare('INSERT INTO Roles (roleName) VALUES(?);');
        $add_role_query->bind_param('s', $_POST['NewRoleName']);
        $add_role_query->execute();

        append_report_message('The new Role has been added');

    } catch (Exception $error) {
        if(strpos($error->getMessage(),'uplicate entry')){
            append_report_message('Role not added; Role name must be unique');
        } else {
            append_report_message($error->getMessage());
        }
    }
}

// Add New Equipment
if (isset($_POST['NewEquipmentName'])) {
    $add_equipment_query = $purple_db->prepare('INSERT INTO Equipment (equipmentName, equipmentTypeId, quantityOnHand, external) VALUES(?, ?, ?, ? );');
    $add_equipment_query->bind_param('siii', $_POST['NewEquipmentName'], $_POST['NewEquipmentTypeId'], $_POST['NewQuantityOnHand'], $_POST['NewExternal']);
    $add_equipment_query->execute();

    $reportmessage = 'The new Equipment has been added';
}

// Add New Equipment Types
if (isset($_POST['NewEquipmentTypeName'])) {
    try {
        $add_equipment_type_query = $purple_db->prepare('INSERT INTO EquipmentTypes (equipmentTypeName) VALUES(?);');
        $add_equipment_type_query->bind_param('s', $_POST['NewEquipmentTypeName']);
        $add_equipment_type_query->execute();

        append_report_message('The new Equipment Type has been added');

    } catch (Exception $error) {
        if(strpos($error->getMessage(),'uplicate entry')){
            append_report_message('Equipment Type not added; Equipment Type name must be unique');
        } else {
            append_report_message($error->getMessage());
        }
    }
}

// Add New Job Status
if (isset($_POST['NewJobStatusName'])) {
    try {
        $add_equipment_type_query = $purple_db->prepare('INSERT INTO JobStatuses (jobStatusName, jobStatusSortOrder, hidden) VALUES(?, ?, ?);');
        $add_equipment_type_query->bind_param('sii', $_POST['NewJobStatusName'], $_POST['NewJobStatusSortOrder'], $_POST['NewHidden']);
        $add_equipment_type_query->execute();

        append_report_message('The new Job Status has been added');

    } catch (Exception $error) {
        if(strpos($error->getMessage(),'uplicate entry')){
            append_report_message('Job Status not added; Job Status name must be unique');
        } else {
            append_report_message($error->getMessage());
        }
    }
}

// Add New Job Type
if (isset($_POST['NewJobTypeName'])) {
    try{
    $add_equipment_type_query = $purple_db->prepare('INSERT INTO JobTypes (jobTypeName) VALUES(?);');
    $add_equipment_type_query->bind_param('s', $_POST['NewJobTypeName']);
    $add_equipment_type_query->execute();

    append_report_message('The new Job Type has been added');

    } catch (Exception $error) {
        if(strpos($error->getMessage(),'uplicate entry')){
            append_report_message('Job Type not added; Job Type name must be unique');
        } else {
            append_report_message($error->getMessage());
        }
    }
}

// Add New Task Priority
if (isset($_POST['NewTaskPriorityName'])) {
    try {
    $add_equipment_type_query = $purple_db->prepare('INSERT INTO TaskPriorities (taskPriorityName, taskPrioritySortOrder) VALUES(?, ?);');
    $add_equipment_type_query->bind_param('si', $_POST['NewTaskPriorityName'], $_POST['NewTaskPrioritySortOrder']);
    $add_equipment_type_query->execute();

    append_report_message('The new Task Priority has been added');
    
    } catch (Exception $error) {
        if(strpos($error->getMessage(),'uplicate entry')){
            append_report_message('Task Priority not added; Task Priority name must be unique');
        } else {
            append_report_message($error->getMessage());
        }
    }
}

// Add New Vendor
if (isset($_POST['NewVendorName'])) {
    try {
    $add_equipment_type_query = $purple_db->prepare('INSERT INTO Vendors (vendorName, vendorPhone, vendorEmail, vendorStreetAddress, vendorCity, vendorState) VALUES(?, ?, ?, ?, ?, ?);');
    $add_equipment_type_query->bind_param('ssssss', $_POST['NewVendorName'], $_POST['NewVendorPhone'], $_POST['NewVendorEmail'], $_POST['NewVendorStreetAddress'], $_POST['NewVendorCity'], $_POST['NewVendorState']);
    $add_equipment_type_query->execute();

    append_report_message('The new Vendor has been added');

    } catch (Exception $error) {
        if(strpos($error->getMessage(),'uplicate entry')){
            append_report_message('Vendor not added; Vendor name must be unique');
        } else {
            append_report_message($error->getMessage());
        }
    }
}

?>