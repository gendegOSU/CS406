<?php
if ($id_set) {
    $custId = intval($_GET['Id']);

    $base_query = 'SELECT customerName, primaryContactName, primaryPhone, primaryEmail, customerNotes, altContactName, altContactPhone, altContactEmail
        FROM Customers c LEFT JOIN AltContacts ac ON c.customerId = ac.customerId WHERE c.customerId = ? ;';

    $base_sql_query = $purple_db->prepare($base_query);
    $base_sql_query->bind_param("i", $custId);
    $base_sql_query->execute();
    $customer_details = $base_sql_query->get_result()->fetch_all(MYSQLI_ASSOC);
}


echo '    <div class="sectionLabel">--- Customer Details ---</div>
    <form action="" method="post" class="'.($id_set?'updateCustomer':'addCustomer').'">
        <input type="hidden" name="'.($id_set?'updateCustomer':'addNewCustomer').'" value="'.($id_set?$custId:'1').'">
    <table class="contentHeader">
        <tr>
            <th colspan="2" class="left"><input type="text" name="customerName" Value="'.($id_set?$customer_details['0']['customerName']:'Customer Name').'"></th>
        </tr>
        <tr>
            <td colspan="2"><label for="primaryContactName"><strong>Primary Contact:</strong></label> <input type="text" name="primaryContactName" Value="'.($id_set?$customer_details['0']['primaryContactName']:'').'"></td>
        </tr>
        <tr>
            <td><label for="primaryPhone"><strong>Phone Number:</strong></label> <input type="text" name="primaryPhone" Value="'.($id_set?$customer_details['0']['primaryPhone']:'').'"></td>
            <td><label for="primaryEmail"><strong>Email:</strong></label> <input type="text" name="primaryEmail" Value="'.($id_set?$customer_details['0']['primaryEmail']:'').'"></td>
        </tr>
        <tr>
            <td colspan="2"><label for="altContactName"><strong>Alternate Contact:</strong></label> <input type="text" name="altContactName" Value="'.($id_set?$customer_details['0']['altContactName']:'').'"></td>
        </tr>
        <tr>
            <td><label for="altContactPhone"><strong>Phone Number:</strong></label> <input type="text" name="altContactPhone" Value="'.($id_set?$customer_details['0']['altContactPhone']:'').'"></td>
            <td><label for="altContactEmail"><strong>Email:</strong></label> <input type="text" name="altContactEmail" Value="'.($id_set?$customer_details['0']['altContactEmail']:'').'"></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Notes:</strong>
            <br><textarea name="customerNotes" id="notes">'.($id_set?$customer_details['0']['customerNotes']:'').'</textarea></td>
        </tr>
        <tr>
            <td colspan="2">--- Quick Job ---</td>
        </tr>
        <tr>
            <td><label for="streetAddress"><strong>*Street Address:</strong></label> <input type="text" name="streetAddress" Value=""></td>
            <td><label for="startDate"><strong>Start Date:</strong></label> <input type="date" name="startDate" Value=""></td>
        </tr>
        <tr>
            <td><label for="city"><strong>City,</strong></label> <label for="state"><strong>State:</strong></label><input type="text" name="city" Value="" id="city"> <input type="text" name="state" Value="" id="state"></td>
            <td><label for="endDate"><strong>End Date:</strong></label> <input type="date" name="endDate" Value=""></td>
        </tr>
        <tr>
            <td> </td>
            <td><label for="fieldVisitDate"><strong>Field Visit:</strong></label> <input type="date" name="fieldVisitDate" Value=""></td>
        </tr>
        <tr>
            <td colspan="2" class="right"><input type="submit" value="Update Customer / Add Job"></td>
        </tr>
    </table>
    </form>';


// TODO Add Jobs list
// TODO Support multiple alternate contacts
// TODO Update Alternate Customers
// TODO Add Quick Job code to create.php

?>