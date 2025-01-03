<?php

if ($page_sub == 'EmpTasks') {

}
elseif ($page_sub == NULL || $page_sub == 'EmpDetails') {


    $roles_array = $purple_db->query("SELECT roleName, roleId FROM Roles ORDER BY roleName ASC")->fetch_all(MYSQLI_ASSOC);
    $qualified_roles = []; 

    if ($id_set) {
        $empId = intval($_GET['Id']);

        $base_query = "SELECT e.firstName, e.lastName, e.employeePhone, e.employeeEmail, r.roleName, e.employeeId FROM Employees e
        LEFT JOIN EmployeesRoles er ON e.employeeId = er.employeeId LEFT JOIN Roles r ON er.roleId = r.roleId
        WHERE e.employeeId = ? ORDER BY firstName, lastName ASC;";

        $base_sql_query = $purple_db->prepare($base_query);
        $base_sql_query->bind_param("i", $empId);
        $base_sql_query->execute();
        $employee_details = $base_sql_query->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($employee_details as $row) {
            array_push($qualified_roles, $row['roleName']);
        }    
    }

    echo '<div id="EmpDetailsBox">
    <form action="" method="post">
        <input type="hidden" name="'.($id_set?'updateEmployee':'addNewEmployee').'" value="'.($id_set?$empId:'1').'">   
        <table>
            <tr>
                <td><label for="'.($id_set?'f':'NewF').'irstName"><strong>First Name:</strong></label> <input type="text" name="'.($id_set?'f':'NewF').'irstName" Value="'.($id_set ? $employee_details['0']['firstName']:'').'"></td>
            </tr>
            <tr>
                <td><label for="'.($id_set?'l':'NewL').'astName"><strong>Last Name:</strong></label> <input type="text" name="'.($id_set?'l':'NewL').'astName" Value="'.($id_set ? $employee_details['0']['lastName']:'').'"></td>
            </tr>
            <tr>
                <td><label for="'.($id_set?'e':'NewE').'mployeePhone"><strong>Phone Number:</strong></label> <input type="text" name="'.($id_set?'e':'NewE').'mployeePhone" Value="'.($id_set ? $employee_details['0']['employeePhone']:'').'"></td>
            </tr>
            <tr>
                <td><label for="'.($id_set?'e':'NewE').'mployeeEmail"><strong>Email Address:</strong></label> <input type="text" name="'.($id_set?'e':'NewE').'mployeeEmail" Value="'.($id_set ? $employee_details['0']['employeeEmail']:'').'"></td>
            </tr>
            <tr>
                <td colspan="2" class="right"><input type="submit" value="'.($id_set?'Update Employee / Qualified Roles':'Add Employee').'"></td>
            </tr>
            <tr><td colspan="2" class="trDivider">&nbsp;</td></tr>
            <tr>
                <td colspan="2" class="sectionLabel">--- Qualified Roles ---</td>
            </tr>
';

    $role_count = 0;
    foreach ($roles_array as $role) {
        if ($role_count % 2 === 0) echo '          <tr>';
        echo '              <td><input type="checkbox" name="roleId'.$role_count.'" value="'.$role['roleId'].'" '.(in_array($role['roleName'],$qualified_roles) ? 'checked ' : '').'><label for="roleId'.$role_count.'">'.$role['roleName'].'</label></td>';
        if ($role_count % 2 === 1) echo '          </tr>';
        $role_count++;
    }

    echo '            <tr><td colspan="2" class="trDivider">&nbsp;</td></tr>
            <tr>
                <td colspan="2" class="right"><input type="submit" value="'.($id_set?'Update Employee / Qualified Roles':'Add Employee').'"></td>
            </tr>
        </table>
    </form>
</div>';
}

// TODO change employee list layout (inlcude links to info and task views)
// TODO Tasks View

?>