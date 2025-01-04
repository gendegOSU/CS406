<?php
// lists_read.php runs the appropriate SELECT queries
// and creates $list_rows and $list_meta arrays
include "lists_read.php";

// Initial HTML and display report messages from queries (if they exist)
$row_index = 0;
echo '<div id="'.get_pagetype_ref().'Box"><table border = 4 cellpadding = 5>
';

// Add header row
$rowoutput = '<tr>';
$col_index = 0;
foreach ($list_rows[$row_index++] as $col_content) {
    $rowoutput .= '<th class="header" id="'.get_pagetype_ref().$col_index.'">'.$col_content.'</th>';
    $col_index++;
}
$rowoutput .= '</tr>
';
echo $rowoutput;


// Create row with "Add New Item" form
if (isset($list_meta['add_form_row'])) echo $list_meta['add_form_row'];


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
    foreach ($list_rows[$row_index] as $col_content) {
        if($col_index < $list_meta['num_cols']){

            // Check if this cell is an updatable form field and insert the appropriate HTML
            //      Four cases:
            //          A link in the first column
            //          A dropdown in a form
            //          A text field in a form
            //          A standard cell with no extra requirements (i.e. not part of a form)

            $is_update_cell = ($list_meta['no_update'] == FALSE && $list_meta['update_names'][$col_index] != 'NA');

            if ($list_meta['link_first_col'] == TRUE && $col_index == 0) {
                $rowoutput .= '<td class="cell" id="'.get_pagetype_ref().$col_index.'"><a href="?page='.$pagetype.'&Id='.$list_rows[$row_index][$list_meta['num_cols']].'">'.$col_content.'</a></td>';
            }
            elseif ($is_update_cell && $list_meta['dropdowns'][$col_index] != 'NA') {
                $rowoutput .= '<td class="cell" id="'.get_pagetype_ref().$col_index.'"><select name="'.$list_meta['update_names'][$col_index].'">';
                
                foreach($list_meta['dropdowns'][$col_index] as $key => $option){
                    $rowoutput .= '<option value="'.$key.'" ';
                    if ($col_content == $key || $col_content == $option) $rowoutput .= ' selected ';
                    $rowoutput .= '>'.$option.'</option>';
                }
                $rowoutput .= '</select></td>';
            }
            elseif ($is_update_cell) {
                $rowoutput .= '<td class="cell" id="'.get_pagetype_ref().$col_index.'"><input type="'.(isset($list_meta['input_type'])?$list_meta['input_type'][$col_index]:'text').'" name="'.$list_meta['update_names'][$col_index].'" value="'.$col_content.'"></td>';
            }
            else {
                $rowoutput .= '<td class="cell" id="'.get_pagetype_ref().$col_index.'">'.$col_content.'</td>';
            }

            $rowoutput .= '
        ';
    
            $col_index++;
        }
    }
    
    // Create update and delete buttons
    $rowoutput .= '<td id="'.get_pagetype_ref().$col_index.'"><div class="updateDeleteBox" >
        ';
    if(!$list_meta['no_update']) {
        $rowoutput .= '<input type="hidden" name="updateType" value="'.get_pagetype_ref().'">
        <input type="hidden" name="updateRef" value="'.$list_rows[$row_index][$list_meta['num_cols']].'">';
        if (isset($oldRoleId)) $rowoutput .= '
        <input type="hidden" name="oldRoleId" value="'.$oldRoleId.'">';
        $rowoutput .= '
        <input type="submit" value="Update">
    </form>';
    }
    $rowoutput .='<form action="'.get_pagetype_ref('link').'" method="post" >
        <input type="hidden" name="deleteRef1" value="'.$list_rows[$row_index][$list_meta['num_cols']];
    if(isset($list_rows[$row_index][$list_meta['num_cols']+1])) {
        $rowoutput .= '">
        <input type="hidden" name="deleteRef2" value="';
        $rowoutput .= $list_rows[$row_index][$list_meta['num_cols']+1];
    }
    $rowoutput .= '">
        <input type="hidden" name="deleteType" value="'.get_pagetype_ref().'">
        <input type="submit" value="Delete">
    </form></div></td>';

    $rowoutput .= '</tr>
';
    echo $rowoutput;
    $row_index++;
}


echo '
</table></div>
';

?>