<?php
/*  Outputs two arrays with the necessary information to build a list-style page

    $list_rows[]
            ['#'] = Index for an array with data for each row (e.g. $list_rows['0'] = array with data for first row)
            ['#']['#'] = Index for data in a givn row and column, in that order (e.g. $list_rows['0']['1'] = data for first row, second column)
            - Each row has an extra column at the end with the database's ID number for the item in the row (e.g. the last column on a row for Customers will be customerId)
            - Some rows have an additional extra column with identifying information necessary for deleting the row

    $list_meta[]
            'add_form_row' = HTML for the <form> row used to add new items
            'dropdowns' = Array with sub-arrays to represent dropdown list options; use 'NA' in fields don't have a dropdown
            'link_first_col' = True/False flag to indicate if the first column should contain a link to further details
            'no_update' = True/False flag to indicate that the update form should be skipped on this page
            'num_cols' = Number of columns
            'update_names' = Array with in-row update form field names; use 'NA' to indicate fields that can't be updated on that page

*/

$list_rows = [];
$list_meta = [];


// Query data and build arrays for the 
//    ********** Jobs page **********
//

if ($pagetype == "Jobs") {

    $base_query = "SELECT
    j.jobName, c.customerName, DATE_FORMAT(j.jobStartDate,'%m/%d/%Y'), DATE_FORMAT(j.jobEndDate,'%m/%d/%Y'), DATE_FORMAT(j.followUpDate,'%m/%d/%Y'), jt.jobTypeName, js.jobStatusName, jobId
	FROM Jobs j 
    LEFT JOIN Customers c ON j.customerId = c.customerId
    LEFT JOIN JobStatuses js ON j.jobStatusId = js.jobStatusId
    LEFT JOIN JobTypes jt ON j.jobTypeId = jt.jobTypeId
    WHERE js.hidden != 1;";

    $base_result = $purple_db->query($base_query);

    // Add first row as colmun headers
    $list_rows['0'] = ['Job Name','Customer Name','Start Date','End Date','Follow Up Date','Job Type','Status'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 7;
    $list_meta['no_update'] = TRUE;
    $list_meta['update_names'] = ['NA','NA','NA','NA','NA','NA','NA'];
    $list_meta['link_first_col'] = TRUE;
    $list_meta['dropdowns'] = ['NA','NA','NA','NA','NA','NA','NA'];

}


// Query data and build arrays for the 
//    ********** Task Types page **********
//
if ($page_sub == "TaskTypes") {

    $base_query = "SELECT taskTypeName, taskTypeId FROM TaskTypes ORDER BY taskTypeName ASC;";
    $base_result = $purple_db->query($base_query);

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
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['taskTypeName'];
    $list_meta['link_first_col'] = FALSE;
    $list_meta['dropdowns'] = ['NA'];

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
if ($pagetype == "Customers") {

    $base_query = "SELECT customerName, primaryContactName, primaryPhone, primaryEmail, COUNT(j.customerId) JobCount, c.customerId 
	FROM Customers c 
    LEFT JOIN Jobs j ON c.customerId = j.customerId 
    GROUP BY customerName, primaryPhone, primaryEmail;";

    $base_result = $purple_db->query($base_query);

    // Add first row as colmun headers
    $list_rows['0'] = ['Customer','Contact Name','Phone Number','Email','Job Count'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM)) {
        
        $list_rows[$index++] = $row;

        $alt_contacts = $purple_db->query('SELECT altContactName, altContactPhone, altContactEmail, altContactId FROM AltContacts WHERE customerId = '.$row['5'].' ;');

        if ($alt_contacts->num_rows != 0) {
            $list_rows[$index++] = ['', 'Alternate Contacts', '', '', '', ''];

            while($alt_row = mysqli_fetch_array($alt_contacts, MYSQLI_NUM)) {
                $list_rows[$index++] = array_merge([''], array_slice($alt_row, 0, 3), [''], [end($alt_row)], ['alt']);
            }
        }
    }

    // Create meta data array
    $list_meta['num_cols'] = 5;
    $list_meta['no_update'] = TRUE;
    $list_meta['update_names'] = ['NA','NA','NA','NA','NA'];
    $list_meta['link_first_col'] = TRUE;
    $list_meta['dropdowns'] = ['NA','NA','NA','NA','NA'];

}


// Query data and build arrays for the
//    ********** Employees page **********
//
if ($pagetype == "Employees") {

    $base_query = "SELECT 'View', e.firstName, e.lastName, r.roleName, e.employeeId
	FROM Employees e 
    LEFT JOIN EmployeesRoles er ON e.employeeId = er.employeeId 
    LEFT JOIN Roles r ON er.roleId = r.roleId
    ORDER BY firstName, lastName ASC";

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->execute();
    $base_result = $base_sql_query->get_result();

    // Add first row as colmun headers
    $list_rows['0'] = ['Details','First Name','Last Name','Qualified Roles'];

    // Add the rest of the rows
    $index = 1;
    $list_rows[$index++] = mysqli_fetch_array($base_result, MYSQLI_NUM);
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $last_key = array_key_last($list_rows);

        if($list_rows[$last_key]['1'].$list_rows[$last_key]['2'] == $row['1'].$row['2']){
            $list_rows[$last_key]['3'] = $list_rows[$last_key]['3'].' // '.$row['3'];
            $index++;
        }
        else {
            $list_rows[$index++] = $row;
        }
    }

    // Create meta data array
    $list_meta['num_cols'] = 4;
    $list_meta['no_update'] = TRUE;
    $list_meta['update_names'] = ['NA','NA','NA','NA'];
    $list_meta['link_first_col'] = TRUE;
    $list_meta['dropdowns'] = ['NA','NA','NA','NA'];

}


// Query data and build arrays for the
//    ********** Roles page **********
//
if ($page_sub == "Roles") {

    $base_query = "SELECT roleName, roleId FROM Roles ORDER BY roleName ASC;";
    $base_result = $purple_db->query($base_query);

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
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['roleName'];
    $list_meta['link_first_col'] = FALSE;
    $list_meta['dropdowns'] = ['NA'];

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
if ($pagetype == "Equipment") {

    $base_query = "SELECT e.equipmentName, et.equipmentTypeName, e.equipmentIdentifier, e.external, v.vendorName, e.equipmentId
	FROM Equipment e 
    LEFT JOIN EquipmentTypes et ON e.equipmentTypeId = et.equipmentTypeId
    LEFT JOIN Vendors v ON e.vendorId = v.vendorId;";

    $base_result = $purple_db->query($base_query);

    // Add first row as colmun headers
    $list_rows['0'] = ['Equipment Name','Equipment Type','Equipment Identifier','External Source', 'Vendor'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 5;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['equipmentName','equipmentTypeId','equipmentIdentifier','external', 'vendorId'];
    $list_meta['link_first_col'] = FALSE;

    $equipmentTypes_query = "SELECT equipmentTypeName, equipmentTypeId FROM EquipmentTypes ORDER BY equipmentTypeName ASC;";
    $equipmentTypes_result = $purple_db->query($equipmentTypes_query);

    $equipmentType_options = [];
    foreach($equipmentTypes_result as $equipmentType){
        $equipmentType_options[$equipmentType['equipmentTypeId']] = $equipmentType['equipmentTypeName'];
    }

    $vendors_query = "SELECT vendorName, vendorId FROM Vendors ORDER BY vendorName ASC;";
    $vendors_result = $purple_db->query($vendors_query);

    $vendor_options = ['none' => ''];
    foreach($vendors_result as $vendor){
        $vendor_options[$vendor['vendorId']] = $vendor['vendorName'];
    }

    $external_options = ['1' => 'Yes', '0' => 'No'];
    $list_meta['dropdowns'] = ['NA',$equipmentType_options,'NA',$external_options,$vendor_options];

    // Create HTML for the Add form row

    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor=white>
        <td id="Equipment0"><input type="text" name="NewEquipmentName" required></td>
        <td id="Equipment1"><select name="NewEquipmentTypeId" required>';
    $form_row .= get_dropdown_options($equipmentTypes_result);
    $form_row .= '</select></td>
        <td id="Equipment2"><input type="text" name="NewEquipmentIdentifier"></td>
        <td id="Equipment3"><select name="NewExternal" required><option value="1">Yes</option><option value="0" selected>No</option></select></td>
        <td id="Equipment4"><select name="NewVendorId"><option></option>';
    $form_row .= get_dropdown_options($vendors_result);    
    $form_row .= '</select></td>
        <td id="Equipment5"><input type="submit" value="Add Equipment"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;

}


// Query data and build arrays for the
//    ********** Equipment Types page **********
//
if ($page_sub == "EquipTypes") {

    $base_query = "SELECT equipmentTypeName, equipmentTypeId FROM EquipmentTypes ORDER BY equipmentTypeName ASC;";
    $base_result = $purple_db->query($base_query);

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
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['equipmentTypeName'];
    $list_meta['link_first_col'] = FALSE;
    $list_meta['dropdowns'] = ['NA'];

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

// Query data and build arrays for the
//    ********** Job Statuses page **********
//
if ($page_sub == "JobStatuses") {

    $base_query = "SELECT jobStatusName, jobStatusSortOrder, hidden, jobStatusId FROM JobStatuses ORDER BY jobStatusSortOrder ASC;";
    $base_result = $purple_db->query($base_query);

    // Add first row as colmun headers
    $list_rows['0'] = ['Job Status', 'Sort Priority', 'Hidden'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 3;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['jobStatusName','jobStatusSortOrder', 'hidden'];
    $list_meta['link_first_col'] = FALSE;

    $hidden_options = ['1' => 'Yes', '0' => 'No'];
    $list_meta['dropdowns'] = ['NA','NA',$hidden_options];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="JobStatuses0"><input type="text" name="NewJobStatusName" required></td>
        <td id="JobStatuses1"><input type="text" name="NewJobStatusSortOrder" required></td>
        <td id="JobStatuses2"><select name="NewHidden"><option value="1" >Yes</option><option value="0" selected>No</option></select></td>
        <td id="JobStatuses3"><input type="submit" value="Add Job Status"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;
}

// Query data and build arrays for the
//    ********** Job Types page **********
//
if ($page_sub == "JobTypes") {

    $base_query = "SELECT jobTypeName, jobTypeId FROM JobTypes ORDER BY jobTypeName ASC;";
    $base_result = $purple_db->query($base_query);

    // Add first row as colmun headers
    $list_rows['0'] = ['Job Type'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 1;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['jobTypeName'];
    $list_meta['link_first_col'] = FALSE;
    $list_meta['dropdowns'] = ['NA','NA','NA'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="JobTypes0"><input type="text" name="NewJobTypeName" required></td>
        <td id="JobTypes1"><input type="submit" value="Add Job Type"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;
}

// Query data and build arrays for the
//    ********** Task Priorities page **********
//
if ($page_sub == "TaskPris") {

    $base_query = "SELECT taskPriorityName, taskPrioritySortOrder, taskPriorityId FROM TaskPriorities ORDER BY taskPrioritySortOrder ASC;";
    $base_result = $purple_db->query($base_query);

    // Add first row as colmun headers
    $list_rows['0'] = ['Task Priority', 'Sort Priority'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 2;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['taskPriorityName','taskPrioritySortOrder'];
    $list_meta['link_first_col'] = FALSE;
    $list_meta['dropdowns'] = ['NA','NA','NA'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="TaskPris0"><input type="text" name="NewTaskPriorityName" required></td>
        <td id="TaskPris1"><input type="text" name="NewTaskPrioritySortOrder" required></td>
        <td id="TaskPris2"><input type="submit" value="Add Task Priority"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;
}

// Query data and build arrays for the
//    ********** Vendors page **********
//
if ($page_sub == "Vendors") {

    $base_query = "SELECT vendorName, vendorPhone, vendorEmail, vendorStreetAddress, vendorCity, vendorState, vendorId FROM Vendors ORDER BY vendorName ASC;";
    $base_result = $purple_db->query($base_query);

    // Add first row as colmun headers
    $list_rows['0'] = ['Vendor Name', 'Phone Number', 'Email', 'Street Address', 'City', 'State'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 6;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['vendorName', 'vendorPhone', 'vendorEmail', 'vendorStreetAddress', 'vendorCity', 'vendorState'];
    $list_meta['link_first_col'] = FALSE;
    $list_meta['dropdowns'] = ['NA','NA','NA','NA','NA','NA'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor = white>
        <td id="Vendors0"><input type="text" name="NewVendorName" required></td>
        <td id="Vendors1"><input type="text" name="NewVendorPhone"></td>
        <td id="Vendors2"><input type="text" name="NewVendorEmail"></td>
        <td id="Vendors3"><input type="text" name="NewVendorStreetAddress"></td>
        <td id="Vendors4"><input type="text" name="NewVendorCity"></td>
        <td id="Vendors5"><input type="text" name="NewVendorState"></td>
        <td id="Vendors6"><input type="submit" value="Add New Vendor"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;
}


// Query data and build arrays for the
//    ********** Materials page **********
//
if ($pagetype == "Materials") {

    $base_query = "SELECT description, quantity, unitOfIssue, quality, length, width, height, finish, lastValidated, materialId
    FROM Materials WHERE quantity != 0 ORDER BY description ASC;";
    $base_result = $purple_db->query($base_query);

    // Add first row as colmun headers
    $list_rows['0'] = ['Description', 'Quantity', 'Unit of Issue', 'Quality', 'Length', 'Width', 'Height', 'Finish', 'Last Validated'];

    // Add the rest of the rows
    $index = 1;
    while($row = mysqli_fetch_array($base_result, MYSQLI_NUM))
    {
        $row['8'] = substr($row['8'],0,10);
        $list_rows[$index++] = $row;
    }

    // Create meta data array
    $list_meta['num_cols'] = 9;
    $list_meta['no_update'] = FALSE;
    $list_meta['update_names'] = ['NA','quantity','NA','NA','NA','NA','NA','NA','lastValidated'];
    $list_meta['link_first_col'] = FALSE;
    $list_meta['dropdowns'] = ['NA','NA','NA','NA','NA','NA','NA','NA','NA'];
    $list_meta['input_type'] = ['text','number','text','text','text','text','text','text','date'];

    // Create HTML for the Add form row
    $form_row = '
<form action="" method="post" class="Add">
    <tr bgcolor=white>
        <td id="Materials0"><input type="text" name="NewDescription" required></td>
        <td id="Materials1"><input type="number" name="NewQuantity" required></td>
        <td id="Materials2"><input type="text" name="NewUnitOfIssue"></td>
        <td id="Materials3"><input type="text" name="NewQuality"></td>
        <td id="Materials4"><input type="text" name="NewLength"></td>
        <td id="Materials5"><input type="text" name="NewWidth"></td>
        <td id="Materials6"><input type="text" name="NewHeight"></td>
        <td id="Materials7"><input type="text" name="NewFinish"></td>
        <td id="Materials8"><input type="date" name="NewLastValidated" value="'.date('Y-m-d').'"></td>
        <td id="Materials9"><input type="submit" value="Add Material"></td>
    </tr>
</form>';

    $list_meta['add_form_row'] = $form_row;


    // TODO add a validate all button
}


?>