<?php 

// Define db queries, starting with dropdown queries

$job_query = "SELECT jobId, jobName FROM Jobs Order by jobId ASC";
$employeesRoles_query = "SELECT er.employeeId, er.roleId, firstName, lastName FROM EmployeesRoles er join Employees e on er.employeeId = e.employeeId ORDER BY roleId, firstName, lastName ASC";

if(isset($MWstart)) {

    $customer_query = "SELECT customerId, customerName FROM Customers ORDER BY customerName ASC";
    $role_query = "SELECT roleId, roleName FROM Roles ORDER BY roleName ASC";
    $employee_query = "SELECT employeeId, firstName, lastName FROM Employees ORDER BY firstName, lastName ASC";
    $equipment_query = "SELECT equipmentId, equipmentName FROM Equipment ORDER BY equipmentName ASC";
    $tasksEquipment_query = "SELECT te.taskId, te.quantityUsed, te.equipmentId, equipmentName FROM TasksEquipment te join Equipment e on te.equipmentId = e.equipmentId ORDER BY equipmentName ASC";
    $taskTypes_query = "SELECT tt.taskTypeName, tt.taskTypeId FROM TaskTypes tt ORDER BY tt.taskTypeName ASC";

    //execute base Customers query -- MW
    $customer_sql_query = $purple_db->prepare($customer_query);
    $customer_sql_query-> execute();
    $customer_result = $customer_sql_query-> get_result();

    //execute base Roles query -- MW
    $role_sql_query = $purple_db->prepare($role_query);
    $role_sql_query-> execute();
    $role_result = $role_sql_query-> get_result();

    //execute base Employees query
    $employee_sql_query = $purple_db->prepare($employee_query);
    $employee_sql_query-> execute();
    $employee_result = $employee_sql_query-> get_result();

    //execute base Equipment query -- MW
    $equipment_sql_query = $purple_db->prepare($equipment_query);
    $equipment_sql_query-> execute();
    $equipment_result = $equipment_sql_query-> get_result();

    //execute base Tasks Equipment query -- MW
    $tasksEquipment_sql_query = $purple_db->prepare($tasksEquipment_query);
    $tasksEquipment_sql_query-> execute();
    $tasksEquipment_result = $tasksEquipment_sql_query-> get_result();

    //execute base Tasks Types query -- MW
    $taskTypes_sql_query = $purple_db->prepare($taskTypes_query);
    $taskTypes_sql_query-> execute();
    $taskTypes_result = $taskTypes_sql_query-> get_result();
}

//execute base Jobs query -- MW, (MWQ)
$job_sql_query = $purple_db->prepare($job_query);
$job_sql_query-> execute();
$job_result = $job_sql_query-> get_result();
$job_output = $job_result -> fetch_assoc();

//execute base Employees Roles query -- C, MW
$employeesRoles_sql_query = $purple_db->prepare($employeesRoles_query);
$employeesRoles_sql_query-> execute();
$employeesRoles_result = $employeesRoles_sql_query-> get_result();


//main Manage Work page information query
if(isset($_GET['jobId'])) {
    $displayJobId = $_GET['jobId'];
}
else {
    $displayJobId = $job_output['jobId'];
}
$base_query = "SELECT
	j.jobId, j.jobName, j.startDate AS jobStartDate, j.endDate AS jobEndDate, j.locationName, j.streetAddress, j.city, j.state, j.status, j.comments, c.customerName, 
	t.taskId, t.startDate AS taskStartDate, t.endDate AS taskEndDate, tt.taskTypeName, tr.taskRoleId, tr.roleId, r.roleName, tr.roleQuantity, e.employeeId, e.firstName, e.lastName
	FROM Jobs j
    LEFT JOIN Customers c ON j.customerId = c.customerId
	LEFT JOIN Tasks t ON j.jobId = t.jobId
    LEFT JOIN TaskTypes tt ON t.taskTypeId = tt.taskTypeId
    LEFT JOIN TasksRoles tr ON t.taskId = tr.taskId
    LEFT JOIN Roles r ON tr.roleId = r.roleId
    LEFT JOIN TasksRolesEmployees tre ON tr.taskRoleId = tre.taskRoleId
    LEFT JOIN Employees e ON tre.employeeId = e.employeeId
	WHERE j.jobId = ?
    ORDER BY taskStartDate ASC;";

//execute base Jobs query
$base_sql_query = $purple_db->prepare($base_query);
$base_sql_query ->bind_param("i", $displayJobId);
$base_sql_query->execute();
$base_result = $base_sql_query->get_result();
$base_output = $base_result->fetch_assoc();

//extract jobId
$jobId = $base_output['jobId'];


// get a list of unique tasks, roles, role quantities, employees, equipment, and equipment quantities to use later -- C, MW
 $rows = [];
 $base_result->data_seek(0);
 while($row = mysqli_fetch_array($base_result, MYSQLI_ASSOC))
 {
    $rows[] = $row;
 }


if(isset($MWstart)) {

    //tasks -- MW
    $uniqueTasks= [];
    foreach ($rows as $item) {
        $entry = [
            'taskId' => $item['taskId'],
            'taskTypeName' => $item['taskTypeName'],
            'taskStartDate' => $item['taskStartDate'],
            'taskEndDate' => $item['taskEndDate'],
        ];
        if (!in_array($entry, $uniqueTasks)) {
            $uniqueTasks[] = $entry;
        }
    }
}

// task roles -- C, MW
$uniqueTaskRoles= [];
foreach ($rows as $item) {
    $entry = [
        'taskId' => $item['taskId'],
        'taskRoleId' => $item['taskRoleId'],
        'roleId' => $item['roleId'],
        'roleName' => $item['roleName'],
        'roleQuantity' => $item['roleQuantity']
    ];
    if (!in_array($entry, $uniqueTaskRoles)) {
        $uniqueTaskRoles[] = $entry;
    }
}


?>