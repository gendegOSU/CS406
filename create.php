<?php

// Add New Tasks
if (isset($_POST['NewTaskStart'])) {

    $add_task_query = $purple_db->prepare("INSERT INTO Tasks (jobId, taskTypeId, startDate, endDate) VALUES (?, ?, ?, ?);");
    $add_task_query->bind_param("iiss", $_POST["jobId"], $_POST["NewTask_Type"], $_POST["NewTaskStart"], $_POST["NewTaskEnd"]);
    $add_task_query->execute();
}


// Add New Task Roles
if (isset($_POST['NewRole'])) {

    // Query if this task Role relationship already exists
    $check_task_role_query = $purple_db->prepare("SELECT * FROM TasksRoles WHERE taskId = ? and roleId = ? ");
    $check_task_role_query->bind_param("ii", $_POST['taskId'], $_POST['NewRole']);
    $check_task_role_query->execute();
    $check_task_role = $check_task_role_query->get_result()->fetch_assoc();

    // If it already exists, just update the quantity
    if (isset($check_task_role['taskId'], $check_task_role['roleId'])) {
        $update_taskRole_query = $purple_db->prepare("UPDATE TasksRoles SET roleQuantity = ((select coalesce(roleQuantity,0) from TasksRoles WHERE taskId = ? AND roleId = ?) + ?) WHERE taskId = ? AND roleId = ?;");
        $update_taskRole_query->bind_param("iiiii", $_POST['taskId'], $_POST['NewRole'], $_POST["NewRoleNum"], $_POST['taskId'], $_POST['NewRole']);
        $update_taskRole_query->execute();
    } else {

    $add_task_role_query = $purple_db->prepare("INSERT INTO TasksRoles (taskId, roleId, roleQuantity) VALUES (?, ?, ?);");
    $add_task_role_query->bind_param("iii", $_POST['taskId'], $_POST['NewRole'], $_POST['NewRoleNum']);
    $add_task_role_query->execute();

    }
}


// Assign Employee To TaskRole
if(isset($_POST['NewRoleEmp']) && $_POST['NewRoleEmp']!=="") {

    include 'ManageWorkQueries.php';

    foreach ($uniqueTaskRoles as $taskRole){
        if($taskRole['taskRoleId'] == $_POST['addTREtaskRoleId']){
            $roleId = $taskRole['roleId'];
        } else{
            continue;
        }
    }

    foreach($employeesRoles_result as $employeeRole){
        if($employeeRole['employeeId'] == $_POST['NewRoleEmp'] && $employeeRole['roleId'] != $roleId){
            // the $confirmed check is necesary so we don't get this message if the employee is qualified for multiple roles
            if(!isset($confirmed)) {
                $reportmessage = 'Employee is not assigned to the specified Role.';
            }
        } else{
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
        } else{
            continue;
        }
    };

    if(in_array($_POST['NewRoleEmp'], $emp_ids)){
        $reportmessage= "The Employee is already assigned to this Task Role combination.";     
    } elseif( $counter+1 > $roleQuantityCurr){
        $reportmessage = "Increase the role quantity from ".$roleQuantityCurr." before assigning more employees.";
    } elseif( !isset($reportmessage) || $reportmessage != 'Employee is not assigned to the specified Role.' ){

        $add_task_role_emp_query = $purple_db->prepare("INSERT INTO TasksRolesEmployees (taskRoleId, employeeId) VALUES ( ?, ?);");
        $add_task_role_emp_query->bind_param("ii", $_POST["addTREtaskRoleId"], $_POST["NewRoleEmp"]);
        $add_task_role_emp_query->execute();

    }
};

// Add New Task Equipment and/or Update Equipment Quantity
if (isset($_POST['NewEquip'])) {

    // Query if this task equipment relationship already exists
    $check_task_equip_query = $purple_db->prepare("SELECT * FROM TasksEquipment WHERE taskId = ? AND equipmentId = ? ;");
    $check_task_equip_query->bind_param("ii", $_POST["taskId"], $_POST["NewEquip"]);
    $check_task_equip_query->execute();
    $check_task_equip = $check_task_equip_query->get_result()->fetch_assoc();

    // If it already exists, just update the quantity
    if (isset($check_task_equip["taskId"], $check_task_equip["equipmentId"])) {
        $update_equip_query = $purple_db->prepare("UPDATE TasksEquipment SET quantityUsed = ((select coalesce(quantityUsed,0) from TasksEquipment WHERE taskId = ? AND equipmentId = ?) + ?)  WHERE taskId = ? AND equipmentId = ?;");
        $update_equip_query->bind_param("iiiii", $_POST["taskId"], $_POST["NewEquip"], $_POST["NewEquipNum"], $_POST["taskId"], $_POST["NewEquip"]);
        $update_equip_query->execute();
    }

    // If it doesn't arleady exist, add it
    else {
        $add_equip_query = $purple_db->prepare("INSERT INTO TasksEquipment (taskId, equipmentId, quantityUsed) VALUES (?, ?, ?);");
        $add_equip_query->bind_param("iii", $_POST["taskId"], $_POST["NewEquip"], $_POST["NewEquipNum"]);
        $add_equip_query->execute();
    }
}

// Add New Jobs
if (isset($_POST['NewJobName'])) {

    // Check if the end date is before the start date
    if( preg_replace('/\D+/','',$_POST["NewStartDate"]) > preg_replace('/\D+/','',$_POST["NewEndDate"]) ){
        $reportmessage = "The Job was not added because the end date is before the start date";
    }
    // If all is good, add the new job
    else {
        if($_POST["NewCustomerId"] == 'none'){
            $newCustId = NULL;
        } else {
            $newCustId = $_POST["NewCustomerId"];
        }

        $add_job_query = $purple_db->prepare("INSERT INTO Jobs (jobName, customerId, startDate, endDate, locationName, streetAddress, city, state, status, comments)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ? );");
        $add_job_query->bind_param("sissssssss", $_POST["NewJobName"], $newCustId, $_POST["NewStartDate"], $_POST["NewEndDate"], $_POST["NewLocationName"], $_POST["NewStreetAddress"], 
            $_POST["NewCity"], $_POST["NewState"], $_POST["NewStatus"], $_POST["NewComments"]);
        $add_job_query->execute();

        $reportmessage = "The new Job has been added";
    }
}

// Add New Task Types
if (isset($_POST['NewTaskTypeName'])) {

    $add_task_type_query = $purple_db->prepare("INSERT INTO TaskTypes (taskTypeName) VALUES(?);");
    $add_task_type_query->bind_param("s", $_POST["NewTaskTypeName"]);
    $add_task_type_query->execute();

    $reportmessage = "The new Task Type has been added";
}

// Add New Customers
if (isset($_POST['NewCustomerName']) && !isset($_POST['NewJobName'])) {

    $add_customer_query = $purple_db->prepare("INSERT INTO Customers (customerName, phoneNumber, email) VALUES(?, ?, ?);");
    $add_customer_query->bind_param("sss", $_POST["NewCustomerName"], $_POST["NewPhoneNumber"], $_POST["NewEmail"]);
    $add_customer_query->execute();

    $reportmessage = "The new Customer has been added";
}

// Add New Employees
if (isset($_POST['NewFirstName'])) {

    $add_employee_query = $purple_db->prepare("INSERT INTO Employees(firstName, lastName) VALUES(?, ?);");
    $add_employee_query->bind_param("ss", $_POST["NewFirstName"], $_POST["NewLastName"]);
    $add_employee_query->execute();

    $reportmessage = "The new Employee has been added";
}

// Add New Employee Roles
if (isset($_POST['NewEmployeeId'],$_POST["NewRoleId"])) {

    $add_employee_role_query = $purple_db->prepare("INSERT INTO EmployeesRoles(employeeId, roleId) VALUES(?, ?);");
    $add_employee_role_query->bind_param("ii", $_POST["NewEmployeeId"], $_POST["NewRoleId"]);
    $add_employee_role_query->execute();

    $reportmessage = "The new Employee Role has been added";
}

// Add New Roles
if (isset($_POST['NewRoleName'])) {

    $add_role_query = $purple_db->prepare("INSERT INTO Roles(roleName) VALUES(?);");
    $add_role_query->bind_param("s", $_POST["NewRoleName"]);
    $add_role_query->execute();

    $reportmessage = "The new Role has been added";
}

// Add New Equipment
if (isset($_POST['NewEquipmentName'])) {
    $add_equipment_query = $purple_db->prepare("INSERT INTO Equipment(equipmentName, equipmentTypeId, quantityOnHand, external) VALUES(?, ?, ?, ? );");
    $add_equipment_query->bind_param("siii", $_POST["NewEquipmentName"], $_POST["NewEquipmentTypeId"], $_POST["NewQuantityOnHand"], $_POST["NewExternal"]);
    $add_equipment_query->execute();

    $reportmessage = "The new Equipment has been added";
}

// Add New Equipment Types
if (isset($_POST['NewEquipmentTypeName'])) {

    $add_equipment_type_query = $purple_db->prepare("INSERT INTO EquipmentTypes(equipmentTypeName) VALUES(?);");
    $add_equipment_type_query->bind_param("s", $_POST["NewEquipmentTypeName"]);
    $add_equipment_type_query->execute();

    $reportmessage = "The new Equipment Type has been added";
}

?>