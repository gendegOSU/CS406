<?php
/*  Outputs two arrays with the necessary information to build a list-style page

    $list_rows[]
            ['#'] = Index for an array with data for each row (e.g. $list_rows['0'] = array with data for first row)
            ['#']['#'] = Index for data in a givn row and column, in that order (e.g. $list_rows['0']['1'] = data for first row, second column)

    $list_meta[]
            'num_cols' = Number of columns
            'add_form_row' = HTML for the <form> row used to add new items
            'update_names' = Array with in-row update form field names; use 'NA' to indicate fields that can't be updated on that page
            'no_update' = True/False flag to indicate that the update form should be skipped on this page
            'has_sup_header' = True/False based on if the table has a supplementary header row [no longer used, but keeping structure in case it's needed again]
            'sup_header_cols' = String with single-digit integers to represent header column spanning

*/

$list_rows = [];
$list_meta = [];


// Query data and build arrays for the 
//    ********** Jobs page **********
//
if ($_GET['page'] == "Jobs") {

    $base_query = "SELECT
    jobName, customerName, DATE_FORMAT(startDate,'%m/%d/%Y'), DATE_FORMAT(endDate,'%m/%d/%Y'), locationName, streetAddress, city, state, status, comments, jobId
	FROM Jobs j 
    LEFT JOIN Customers c ON j.customerId = c.customerId;";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['Job Name','Customer Name','Start Date','End Date','Location Name','Street Address','City','State','Status','Comments'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 10;
    $list_meta['has_sup_header'] = FALSE;
    $list_meta['no_update'] = TRUE;
    $list_meta['update_names'] = ['NA','NA','NA','NA','NA','NA','NA','NA','NA','NA'];

    // Create HTML for the Add form row
    $customer_result = $purple_db->query("SELECT customerId, customerName FROM Customers ORDER BY customerName ASC");

    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="Jobs0"><input type="Text" name="NewJobName" required></td>
        <td id="Jobs1"><select name="NewCustomerId"><option value="none"></option>';
    foreach($customer_result as $customer){
        $form_row .= "<option value=".$customer['customerId'].">".$customer['customerName']."</option>";
    }
    $form_row .= '</select></td>
        <td id="Jobs2"><input type="Date" name="NewStartDate" required></td>
        <td id="Jobs3"><input type="Date" name="NewEndDate" required></td>
        <td id="Jobs4"><input type="Text" name="NewLocationName"></td>
        <td id="Jobs5"><input type="Text" name="NewStreetAddress"></td>
        <td id="Jobs6"><input type="Text" name="NewCity"></td>
        <td id="Jobs7"><input type="Text" name="NewState"></td>
        <td id="Jobs8"><input type="Text" name="NewStatus"></td>
        <td id="Jobs9"><input type="Text" name="NewComments"></td>
        <td id="Jobs10"><input type="submit" value="Add Job"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;

}


// Query data and build arrays for the 
//    ********** Task Types page **********
//
if ($_GET['page'] == "TaskTypes") {

    $base_query = "SELECT taskTypeName, taskTypeId FROM TaskTypes ORDER BY taskTypeName ASC;";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['Task Type'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 1;
    $list_meta['has_sup_header'] = FALSE;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['taskTypeName'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="TaskTypes0"><input type ="text" name="NewTaskTypeName" required></td>
        <td id="TaskTypes1"><input type="submit" value="Add Task Type"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;

}


// Query data and build arrays for the
//    ********** Customers page **********
//
if ($_GET['page'] == "Customers") {

    $base_query = "SELECT customerName, phoneNumber, email, COUNT(j.customerId) JobCount, c.customerId 
	FROM Customers c 
    LEFT JOIN Jobs j ON c.customerId = j.customerId 
    GROUP BY customerName, phoneNumber, email;";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['Customer','Phone Number','Email','Job Count'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 4;
    $list_meta['has_sup_header'] = FALSE;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['customerName','phoneNumber','email','NA'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="Customers0"><input type ="Text" name ="NewCustomerName" required></td>
        <td id="Customers1"><input type ="Text" name ="NewPhoneNumber"></td>
        <td id="Customers2"><input type ="Text" name ="NewEmail"></td>
        <td id="Customers3">N/A</td>
        <td id="Customers4"><input type="submit" value="Add Customer"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;

}


// Query data and build arrays for the
//    ********** Employees page **********
//
if ($_GET['page'] == "Employees") {

    $base_query = "SELECT e.firstName, e.lastName, r.roleName, e.employeeId
	FROM Employees e 
    LEFT JOIN EmployeesRoles er ON e.employeeId = er.employeeId 
    LEFT JOIN Roles r ON er.roleId = r.roleId
    ORDER BY firstName, lastName ASC";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['First Name','Last Name','Qualified Roles'];

    // Add the rest of the rows
    $index = 1;
    $list_rows[$index++] = mysqli_fetch_array($base_result, MYSQLI_NUM);
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $last_key = array_key_last($list_rows);

        if($list_rows[$last_key]['0'].$list_rows[$last_key]['1'] == $row['0'].$row['1']){
            $list_rows[$last_key]['2'] = $list_rows[$last_key]['2'].' // '.$row['2'];
            $index++;
        }
        else {
            $list_rows[$index++] = $row;
        }
    }

    // Create meta data array
    $list_meta['num_cols'] = 3;
    $list_meta['has_sup_header'] = FALSE;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['firstName','lastName','NA'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="Employees0"><input type="Text" name="NewFirstName" required></td>
        <td id="Employees1"><input type="Text" name="NewLastName" required></td>
        <td id="Employees2">N/A</td>
        <td id="Employees3"><input type="submit" value="Add Employee"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;

}


// Query data and build arrays for the
//    ********** Employees Roles page **********
//
if ($_GET['page'] == "EmployeesRoles") {

    $base_query = "SELECT CONCAT(firstName,' ', lastName) employeeName, roleName, e.employeeId, r.roleId
	FROM Employees e 
    LEFT JOIN EmployeesRoles er ON e.employeeId = er.employeeId 
    JOIN Roles r ON er.roleId = r.roleId
    ORDER BY employeeName ASC;";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['Employee Name','Role Name'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 2;
    $list_meta['has_sup_header'] = FALSE;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['NA','roleId'];

    // Create HTML for the Add form row
    $employee_result = $purple_db->query("SELECT employeeId, firstName, lastName FROM Employees ORDER BY firstName, lastName ASC");
    $role_result = $purple_db->query("SELECT roleId, roleName FROM Roles ORDER BY roleName ASC");

    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="EmployeesRoles0"><select name="NewEmployeeId" required>';
    foreach($employee_result as $employee){
        $form_row .= "<option value=".$employee['employeeId'].">".$employee['firstName']." ".$employee['lastName']."</option>";
    }
    $form_row .= '</select></td>
        <td id="EmployeesRoles1"><select name="NewRoleId" required>';
    foreach($role_result as $role){
        $form_row .= "<option value=".$role['roleId'].">".$role['roleName']."</option>";
    }
    $form_row .= '</select></td>
        <td id="EmployeesRoles2"><input type="submit" value="Add Employee Role"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;

}


// Query data and build arrays for the
//    ********** Roles page **********
//
if ($_GET['page'] == "Roles") {

    $base_query = "SELECT roleName, roleId FROM Roles ORDER BY roleName ASC;";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['Role'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 1;
    $list_meta['has_sup_header'] = FALSE;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['roleName'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="Roles0"><input type="text" name="NewRoleName" required></td>
        <td id="Roles1"><input type="submit" value="Add Role"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;
    
}


// Query data and build arrays for the
//    ********** Equipment page **********
//
if ($_GET['page'] == "Equipment") {

    $base_query = "SELECT e.equipmentName, et.equipmentTypeName, quantityOnHand, external, equipmentId
	FROM Equipment e 
    JOIN EquipmentTypes et ON e.equipmentTypeId = et.equipmentTypeId;";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['Equipment Name','Equipment Type','Quantity On hand','External Source'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 4;
    $list_meta['has_sup_header'] = FALSE;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['equipmentName','equipmentTypeId','quantityOnHand','external'];

    // Create HTML for the Add form row
    $equipmentTypes_result = $purple_db->query("SELECT equipmentTypeId, equipmentTypeName FROM EquipmentTypes ORDER BY equipmentTypeName ASC");

    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="Equipment0"><input type="Text" name="NewEquipmentName" required></td>
        <td id="Equipment1"><select name="NewEquipmentTypeId" required>';
    foreach($equipmentTypes_result as $equipmentType){
        $form_row .= "<option value=".$equipmentType['equipmentTypeId'].">".$equipmentType['equipmentTypeName']."</option>";
    }
    $form_row .= '</select></td>
        <td id="Equipment2"><input type=number name="NewQuantityOnHand"></td>
        <td id="Equipment3"><select name="NewExternal" required><option value="1">Yes</option><option value="0" selected>No</option></select></td>
        <td id="Equipment4"><input type="submit" value="Add Equipment"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;

}


// Query data and build arrays for the
//    ********** Equipment Types page **********
//
if ($_GET['page'] == "EquipmentTypes") {

    $base_query = "SELECT equipmentTypeName, equipmentTypeId FROM EquipmentTypes ORDER BY equipmentTypeName ASC;";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['Equipment Type'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 1;
    $list_meta['has_sup_header'] = FALSE;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['equipmentTypeName'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="EquipmentTypes0"><input type="text" name="NewEquipmentTypeName" required></td>
        <td id="EquipmentTypes1"><input type="submit" value="Add Equipment Type"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;
}

?>