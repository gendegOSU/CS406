<?php

$status_query = "SELECT jobStatusId, jobStatusName FROM JobStatuses ORDER BY jobStatusSortOrder ASC";
$status_result = $purple_db->query($status_query);

$customer_query = "SELECT customerId, customerName FROM Customers ORDER BY customerName ASC";
$customer_result = $purple_db->query($customer_query);

$job_type_query = "SELECT jobTypeId, jobTypeName FROM JobTypes ORDER BY jobTypeName ASC";
$job_type_result = $purple_db->query($job_type_query);


if ($id_set) {
    $jobId = intval($_GET['Id']);

    $base_query = 'SELECT j.jobName, c.customerName, j.jobStartDate, j.jobEndDate, j.followUpDate, jt.jobTypeName, js.jobStatusName, j.jobNotes, j.locationName, j.streetAddress, j.city, j.state
	    FROM Jobs j 
        LEFT JOIN Customers c ON j.customerId = c.customerId
        LEFT JOIN JobStatuses js ON j.jobStatusId = js.jobStatusId
        LEFT JOIN JobTypes jt ON j.jobTypeId = jt.jobTypeId
        WHERE j.jobId = ? ;';

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->bind_param("i", $jobId);
    $base_sql_query->execute();
    $job_details = $base_sql_query->get_result()->fetch_assoc();
} else {
    $job_details = ['jobStatusName' => '', 'customerName' => '', 'jobTypeName' => ''];
}

echo '    <div class="SectionLabel">--- Job Details ---</div>
    <form action="" method="post" class="NewJob">
    '.($id_set?'<input type="hidden" name="updateJobId" value="'.$jobId.'">':'').'
        <table class="JobHeader">
            <tr>
                <th class="left">'.($id_set ? $job_details['jobName'] : '').'</th>
                <th class="right">
                    <label for="'.($id_set?'j':'NewJ').'obStatusId">Status:</label>
                    <select name="'.($id_set?'j':'NewJ').'obStatusId" required>
                    ';
echo get_dropdown_options($status_result, $job_details['jobStatusName']);
echo '
                    </select>
                </th>
            </tr>
            <tr>
                <td>
                    <strong>Customer:</strong>
                    <select name="'.($id_set?'c':'NewC').'ustomerId" required>
                    <option></option>';
echo get_dropdown_options($customer_result, $job_details['customerName']);
echo '
                    </select>
                </td>
                <td>
                    <strong>Job Type:</strong>
                    <select name="'.($id_set?'j':'NewJ').'obTypeId" required>
                    <option></option>';
echo get_dropdown_options($job_type_result, $job_details['jobTypeName']);
echo '
                    </select>
                </td>
            </tr> 
            <tr>
                <td>
                    <strong>Job Dates:</strong>
                    <input type="date" name="'.($id_set?'j':'NewJ').'obStartDate" value="'.($id_set ? substr($job_details['jobStartDate'],0,10) : '').'">
                    <i>to</i>
                    <input type="date" name="'.($id_set?'j':'NewJ').'obEndDate" value="'.($id_set ? substr($job_details['jobEndDate'],0,10) : '').'">
                    <br><strong>Follow Up Date:</strong>
                    <input type="date" name="'.($id_set?'f':'NewF').'ollowUpDate" value="'.($id_set ? substr($job_details['followUpDate'],0,10) : '').'">
                </td>
                <td>
                    <strong>Location:</strong>
                    <br><label for="'.($id_set?'l':'NewL').'ocationName">Name: </label><input type="text" name="'.($id_set?'l':'NewL').'ocationName" Value="'.($id_set ? $job_details['locationName'] : '').'">
                    <br><label for="'.($id_set?'s':'NewS').'treetAddress">Address: </label><input type="text" name="'.($id_set?'s':'NewS').'treetAddress" Value="'.($id_set ? $job_details['streetAddress'] : '').'">
                    <br><label for="'.($id_set?'c':'NewC').'ity">City, State: </label><input type="text" name="'.($id_set?'c':'NewC').'ity" id="city" Value="'.($id_set ? $job_details['city'] : '').'">
                    <input type="text" name="'.($id_set?'s':'NewS').'tate" id="state" Value="'.($id_set ? $job_details['state'] : '').'">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Notes:</strong>
                    <br><textarea name="'.($id_set?'j':'NewJ').'obNotes" id="jobNotes">'.($id_set ? $job_details['jobNotes'] : '').'</textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="right"><input type="submit" value="'.($id_set?'Update Job':'Add Job').'"></td>
            </tr>
        </table>
    </form>';

// TODO All of the task presentation and management functions
// TODO Job Employee Notes
// TODO Show hidden statuses flag
?>