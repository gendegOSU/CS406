<?php
// lists_read.php runs the appropriate SELECT queries
// and creates $list_rows and $list_meta arrays
include "lists_read.php";

// Initial HTML and display report messages from queries (if they exist)
$row_index = 0;
echo '<main><table border = 4 cellpadding = 5>
';

// Add header row
$rowoutput = '<tr>';
$col_index = 0;
foreach ($list_rows[$row_index++] as $col) {
    $rowoutput .= '<th class="header" id="'.$_GET['page'].$col_index.'">'.$col.'</th>';
    $col_index++;
}
$rowoutput .= '</tr>
';
echo $rowoutput;


// Create row with "Add New Item" form
echo $list_meta['add_form_row'];


// Add all data rows
while($row_index <= array_key_last($list_rows)) {

    // In some cases, row indexes may be skipped in the array, this catches them and moves to the next row index
    if (!isset($list_rows[$row_index])){
        $row_index++;
        continue;
    }

    $rowoutput = '<tr bgcolor="white">';
    if(!$list_meta['no_update']) $rowoutput .= '<form action="" method="post">';

    // Input row data from the database
    $col_index = 0;
    foreach ($list_rows[$row_index] as $col) {
        if($col_index < $list_meta['num_cols']){

            // Check if this cell is an updatable form field and insert the appropriate HTML
            //      Four special cases:
            //          First column on Jobs page - add link to Manage Work page for that job
            //          Second column on Employee Roles page - add dropdown
            //          Second column on Equipment page - add dropdown
            //          Fourth column on Equipment page - add dropdown

            // This if is equivalent to NOT(update_name==NA OR (page==Equipment AND (col_index == 1 OR 3)) OR page==EmployeesRoles)
            if(!($list_meta['update_names'][$col_index] == 'NA' || ($_GET['page'] == 'Equipment' && ($col_index == 1 || $col_index == 3)) || $_GET['page'] == 'EmployeesRoles' )){
                $rowoutput .= '<td class="cell" id="'.$_GET['page'].$col_index.'"><input type="text" name="'.$list_meta['update_names'][$col_index].'" value="'.$col.'"></td>';
            }
            elseif($_GET['page'] == 'Jobs' && $col_index == 0){
                $rowoutput .= '<td class="cell" id="'.$_GET['page'].$col_index.'"><a href="?page=ManageWork&jobId='.$list_rows[$row_index][$list_meta['num_cols']].'">'.$col.'</a></td>';
            }
            elseif($_GET['page'] == 'Equipment' && $col_index == 1){
                $rowoutput .= '<td class="cell" id="'.$_GET['page'].$col_index.'"><select name="equipmentTypeId" required>';
                foreach($equipmentTypes_result as $equipmentType){
                    $rowoutput .= '<option value='.$equipmentType["equipmentTypeId"];
                    if ($col == $equipmentType["equipmentTypeName"]) $rowoutput .= ' selected ';
                    $rowoutput .= '>'.$equipmentType["equipmentTypeName"].'</option>';
                }
                $rowoutput .= '</select></td>';
            }
            elseif($_GET['page'] == 'Equipment' && $col_index == 3){
                $rowoutput .= '<td class="cell" id="'.$_GET['page'].$col_index.'"><select name="external" required><option value="1"';
                if ($col == "1") $rowoutput .= ' selected ';
                $rowoutput .= '>Yes</option><option value="0"';
                if ($col == "0") $rowoutput .= ' selected ';
                $rowoutput .= '>No</option></select></td>';
            }
            elseif($_GET['page'] == 'EmployeesRoles' && $col_index == 1){
                $rowoutput .= '<td class="cell" id="'.$_GET['page'].$col_index.'"><select name="roleId" required>';
                foreach($role_result as $role){
                    $rowoutput .= '<option value='.$role["roleId"];
                    if ($col == $role["roleName"]) {
                        $rowoutput .= ' selected ';
                        $oldRoleId = $role["roleId"];
                    }
                    $rowoutput .= '>'.$role["roleName"].'</option>';
                }
                $rowoutput .= '</select></td>';
            }
            else{
                $rowoutput .= '<td class="cell" id="'.$_GET['page'].$col_index.'">'.$col.'</td>';
            }
        }

        $col_index++;
    }
    
    // Create update and delete buttons
    $rowoutput .= '
    <td id="'.$_GET['page'].$col_index.'"><div class="updateDeleteBox" >
        ';
    if(!$list_meta['no_update']) {
        $rowoutput .= '<input type="hidden" name="updateType" value="'.$_GET['page'].'">
        <input type="hidden" name="updateRef" value="'.$list_rows[$row_index][$list_meta['num_cols']].'">';
        if (isset($oldRoleId)) $rowoutput .= '
        <input type="hidden" name="oldRoleId" value="'.$oldRoleId.'">';
        $rowoutput .= '
        <input type="submit" value="Update">
    </form>';
    }
    $rowoutput .='<form action="" method="post">
        <input type="hidden" name="deleteRef1" value="'.$list_rows[$row_index][$list_meta['num_cols']].'">
        <input type="hidden" name="deleteRef2" value="';
    if(isset($list_rows[$row_index][$list_meta['num_cols']+1])) {
        $rowoutput .= $list_rows[$row_index][$list_meta['num_cols']+1];
    }
    $rowoutput .= '">
        <input type="hidden" name="deleteType" value="'.$_GET['page'].'">
        <input type="submit" value="Delete">
    </form></div></td>';

    $rowoutput .= '</tr>
';
    echo $rowoutput;
    $row_index++;
}


echo '
</table>
</main>
';

?>