
<?php

    // Update Job details
    if (isset($_POST["updateJobId"])) {
        if($_POST["customerId"] == 'none') {
            $customer = NULL;
        } else {
            $customer = $_POST["customerId"];
        }

        $update_job_query = $purple_db->prepare("UPDATE Jobs SET status = ?, customerId = ?, startDate = ?, endDate = ?, locationName = ?, streetAddress = ?, city = ?, state = ?, comments = ?  WHERE jobId = ?;");
        $update_job_query->bind_param("sisssssssi", $_POST["status"], $customer, $_POST["jobStartDate"], $_POST["jobEndDate"], $_POST["locationName"], $_POST["streetAddress"], $_POST["city"], $_POST["state"], $_POST["comments"], $_POST['updateJobId']);
        $update_job_query->execute();
    }

    // Update TasksRoles details
    if (isset($_POST["updateRoleTaskRoleId"])) {

        $check_task_role_query = $purple_db -> prepare("SELECT * FROM TasksRoles WHERE roleId = ? and taskId = ?;");
        $check_task_role_query = $purple_db -> bind_param("ii",$_POST['roleId'], $_POST['updateRoleTaskId']);
        $check_task_role_query = $purple_db ->execute();
        $check_task_role = $check_task_role_query -> get_result().fetch_assoc();
    }

        if (isset($check_task_role["taskId"], $check_task_role["roleId"])) {

        $update_tasksRoles_query = $purple_db->prepare("UPDATE TasksRoles SET roleId = ?, roleQuantity = ?  WHERE taskRoleId = ?;");
        $update_tasksRoles_query->bind_param("iii", $_POST["roleId"], $_POST["roleQuantity"], $_POST["updateRoleTaskRoleId"]);
        $update_tasksRoles_query->execute();
        }

    // Update Employee
    if (isset($_POST['updateEmployeeTaskRoleId'])) {
        $update_TasksRolesEmployees_query = $purple_db->prepare("UPDATE TasksRolesEmployees SET employeeId = ? WHERE taskRoleId = ?;");
        $update_TasksRolesEmployees_query->bind_param("ii", $_POST["employeeId"], $_POST['updateEmployeeTaskRoleId']);
        $update_TasksRolesEmployees_query->execute();
    } 


    // Add New Task Equipment and/or Update Equipment Quantity
if (isset($_POST['UpdateEquip'])) {

    // Query if this task equipment relationship already exists
    $check_task_equip_query = $purple_db->prepare("SELECT * FROM TasksEquipment WHERE taskId = ? AND equipmentId = ? ;");
    $check_task_equip_query->bind_param("ii", $_POST["taskId"], $_POST["UpdateEquip"]);
    $check_task_equip_query->execute();
    $check_task_equip = $check_task_equip_query->get_result()->fetch_assoc();

    // If it already exists, just update the quantity
    if (isset($check_task_equip["taskId"], $check_task_equip["equipmentId"])) {
        $update_equip_query = $purple_db->prepare("UPDATE TasksEquipment SET quantityUsed = ? WHERE taskId = ? AND equipmentId = ?;");
        $update_equip_query->bind_param("iii", $_POST["UpdateEquipNum"], $_POST["taskId"], $_POST["UpdateEquip"]);
        $update_equip_query->execute();
    }

    // If it doesn't already exist, add it
    else {
        $add_equip_query = $purple_db->prepare("INSERT INTO TasksEquipment (taskId, equipmentId, quantityUsed) VALUES (?, ?, ?);");
        $add_equip_query->bind_param("iii", $_POST["taskId"], $_POST["UpdateEquip"], $_POST["UpdateEquipNum"]);
        $add_equip_query->execute();
    }
}




    // Process updates from list-style pages
    if(isset($_POST['updateType'])) {

        // Update TaskTypes
        if($_POST['updateType'] == "TaskTypes") {
            $update_query = $purple_db->prepare("UPDATE TaskTypes SET taskTypeName = ? WHERE taskTypeId = ?;");
            $update_query->bind_param("si", $_POST['taskTypeName'], $_POST["updateRef"]);
            $update_query->execute();

            $reportmessage = "The Task Type has been updated";
        }

        // Update Customers
        if($_POST['updateType'] == "Customers") {
            $update_query = $purple_db->prepare("UPDATE Customers SET customerName = ?, phoneNumber = ?, email = ? WHERE customerId = ?;");
            $update_query->bind_param("sssi", $_POST['customerName'], $_POST['phoneNumber'], $_POST['email'], $_POST["updateRef"]);
            $update_query->execute();

            $reportmessage = "The Customer has been updated";
        }

        // Update Employees
        if($_POST['updateType'] == "Employees") {
            $update_query = $purple_db->prepare("UPDATE Employees SET firstName = ?, lastName = ? WHERE employeeId = ?;");
            $update_query->bind_param("ssi", $_POST['firstName'], $_POST['lastName'], $_POST["updateRef"]);
            $update_query->execute();

            $reportmessage = "The Employee has been updated";
        }

        // Update EmployeesRoles
        if($_POST['updateType'] == "EmployeesRoles") {
            $update_query = $purple_db->prepare("UPDATE EmployeesRoles SET roleId = ? WHERE employeeId = ? AND roleId = ?;");
            $update_query->bind_param("iii", $_POST['roleId'], $_POST['updateRef'], $_POST['oldRoleId']);
            $update_query->execute();

            $reportmessage = "The Employee Role has been updated";
        }

        // Update Roles
        if($_POST['updateType'] == "Roles") {
            $update_query = $purple_db->prepare("UPDATE Roles SET roleName = ? WHERE roleId = ?;");
            $update_query->bind_param("si", $_POST['roleName'], $_POST["updateRef"]);
            $update_query->execute();

            $reportmessage = "The Role has been updated";
        }

        // Update Equipment
        if($_POST['updateType'] == "Equipment") {
            $update_query = $purple_db->prepare("UPDATE Equipment SET equipmentName = ?, equipmentTypeId = ?, quantityOnHand = ?, external = ? WHERE equipmentId = ?;");
            $update_query->bind_param("siiii", $_POST['equipmentName'], $_POST['equipmentTypeId'], $_POST['quantityOnHand'], $_POST['external'], $_POST["updateRef"]);
            $update_query->execute();

            $reportmessage = "The Equipment has been updated";
        }

        // Update EquipmentTypes
        if($_POST['updateType'] == "EquipmentTypes") {
            $update_query = $purple_db->prepare("UPDATE EquipmentTypes SET equipmentTypeName = ? WHERE equipmentTypeId = ?;");
            $update_query->bind_param("si", $_POST['equipmentTypeName'], $_POST["updateRef"]);
            $update_query->execute();

            $reportmessage = "The Equipment Type has been updated";
        }

    }