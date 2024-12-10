<?php
// Delete Tasks
if(isset($_POST['DeleteTask'])) {

    $delete_query = $purple_db->prepare("DELETE FROM Tasks WHERE taskId = ?;");
    $delete_query->bind_param("i", $_POST["DeleteTask"]);
    $delete_query->execute();
}


// Delete TasksRoles
if(isset($_POST['deleteTaskRoleId'])) {

    $delete_query = $purple_db->prepare("DELETE FROM TasksRoles WHERE taskRoleId = ?;");
    $delete_query->bind_param("i", $_POST["deleteTaskRoleId"]);
    $delete_query->execute();
}

// Delete TasksRolesEmployees
if(isset($_POST['deleteTaskRoleEmp'])) {

    $delete_query = $purple_db->prepare("DELETE FROM TasksRolesEmployees WHERE taskRoleId = ? and employeeId = ? ;");
    $delete_query->bind_param("ii",$_POST['taskRoleId'], $_POST["deleteTaskRoleEmp"]);
    $delete_query->execute();
}

// Delete TasksEquipment
if(isset($_POST['deleteTaskEquip'])) {

    $delete_query = $purple_db->prepare("DELETE FROM TasksEquipment WHERE taskId= ? and equipmentId = ?;");
    $delete_query->bind_param("ii", $_POST['taskId'], $_POST["deleteTaskEquip"]);
    $delete_query->execute();
}


// Process deletes from list-style pages
//      Items with delete restrictions due to foreign keys (e.g. "ON DELETE RESTRICT") have a try/catch to set
//      a report message that informs the user when deletes are restricted
if(isset($_POST['deleteType'])) {

    // Delete from Jobs
    if($_POST['deleteType'] == "Jobs") {
        $delete_query = $purple_db->prepare("DELETE FROM Jobs WHERE jobId = ?;");
        $delete_query->bind_param("i", $_POST["deleteRef1"]);
        $delete_query->execute();

        $reportmessage = "The Job has been deleted";
    }

    // Delete from TaskTypes
    elseif($_POST['deleteType'] == "TaskTypes") {
        $delete_query = $purple_db->prepare("DELETE FROM TaskTypes WHERE taskTypeId = ?;");
        $delete_query->bind_param("i", $_POST["deleteRef1"]);
        
        // check if foreign key restriction exists
        try {
            $delete_query->execute();
            $reportmessage = "The Task Type has been deleted";
        } catch (Exception $error) {
            if(strpos($error->getMessage(),"a foreign key constraint fails")){
                $reportmessage = "Unable to delete this Task Type while it is assigned to a task";
            } else {
                $reportmessage = $error->getMessage();
            }
        }
    }

    // Delete from Customers
    elseif($_POST['deleteType'] == "Customers") {
        $delete_query = $purple_db->prepare("DELETE FROM Customers WHERE customerId = ?;");
        $delete_query->bind_param("i", $_POST["deleteRef1"]);

        // check if foreign key restriction exists
        try {
            $delete_query->execute();
            $reportmessage = "The Customer has been deleted";
        } catch (Exception $error) {
            if(strpos($error->getMessage(),"a foreign key constraint fails")){
                $reportmessage = "Unable to delete this Customer type while it is assigned to a job";
            } else {
                $reportmessage = $error->getMessage();
            }
        }
    }

    // Delete from Employees
    elseif($_POST['deleteType'] == "Employees") {
        $delete_query = $purple_db->prepare("DELETE FROM Employees WHERE employeeId = ?;");
        $delete_query->bind_param("i", $_POST["deleteRef1"]);
        $delete_query->execute();

        $reportmessage = "The Employee has been deleted";
    }

    // Delete from EmployeesRoles
    elseif($_POST['deleteType'] == "EmployeesRoles") {
        $delete_query = $purple_db->prepare("DELETE FROM EmployeesRoles WHERE employeeId = ? AND roleId = ?;");
        $delete_query->bind_param("ii", $_POST["deleteRef1"], $_POST["deleteRef2"]);
        $delete_query->execute();

        $reportmessage = "The Employee Role has been deleted";
    }

    // Delete from Roles
    elseif($_POST['deleteType'] == "Roles") {
        $delete_query = $purple_db->prepare("DELETE FROM Roles WHERE roleId = ?;");
        $delete_query->bind_param("i", $_POST["deleteRef1"]);

        // check if foreign key restriction exists
        try {
            $delete_query->execute();
            $reportmessage = "The Role has been deleted";
        } catch (Exception $error) {
            if(strpos($error->getMessage(),"a foreign key constraint fails")){
                $reportmessage = "Unable to delete this Role while it is assigned to an employee or a task";
            } else {
                $reportmessage = $error->getMessage();
            }
        }
    }

    // Delete from Equipment
    elseif($_POST['deleteType'] == "Equipment") {
        $delete_query = $purple_db->prepare("DELETE FROM Equipment WHERE equipmentId = ?;");
        $delete_query->bind_param("i", $_POST["deleteRef1"]);
        $delete_query->execute();

        $reportmessage = "The Equipment has been deleted";
    }

    // Delete from EquipmentTypes
    elseif($_POST['deleteType'] == "EquipmentTypes") {
        $delete_query = $purple_db->prepare("DELETE FROM EquipmentTypes WHERE equipmentTypeId = ?;");
        $delete_query->bind_param("i", $_POST["deleteRef1"]);

        // check if foreign key restriction exists
        try {
            $delete_query->execute();
            $reportmessage = "The Equipment Type has been deleted";
        } catch (Exception $error) {
            if(strpos($error->getMessage(),"a foreign key constraint fails")){
                $reportmessage = "Unable to delete this Equipment Type while it is assigned to equipment";
            } else {
                $reportmessage = $error->getMessage();
            }
        }
    }
}
?>