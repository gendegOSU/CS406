<?php
include 'ManageWorkQueries.php';
?>

<main>
    <!--Select Job-->
    <label for="selectJob" class="JobSelector">Select Job:</label>
    <form action="" method="get" class="getJob">
        <input type="hidden" name="page" value="ManageWork">
        <select name="jobId" required>
            <?php foreach($job_result as $job){
                if($jobId == $job['jobId']){
                    echo '<option value ="'.$job['jobId'].'"selected>'.$job['jobName'].'</option>';
                } else {
                    echo '<option value="'.$job['jobId'].'">'.$job['jobName'].'</option>';
                }
            }
            ?>
</select> <input type="submit" value="Select Job"></form>

    <!-- Job Details html --> 
    <div class="SectionLabel">--- Job Details ---</div>
    <form action="" method="post" class="UpdateJob" >
    <input type="hidden" name="updateJobId" value="<?php echo $base_output['jobId']?>">
        <table class="JobHeader">
            <tr>
                <th class="left"><?php echo $base_output['jobName']?></th>
                <th class="right">
                    <label for="status">Status:</label>
                    <input type="text" name="status" alignment="left" value="<?php echo $base_output['status']?>">
                </th>
            </tr>
            <tr>
                <td>
                    <strong>Customer:</strong>
                    <select name="customerId" required>
                    <option value="none">--no customer--</option>
                      <?php
                        foreach($customer_result as $customer){
                        ?>
                       <option value = "<?php echo $customer['customerId']?>"<?php if($customer['customerName'] == $rows[0]["customerName"]) echo " selected"; ?>> <?php echo $customer['customerName'] ?> </option>
                        <?php } ?>
                </td>
                <td><strong>Location:</strong></td>
            </tr> 
            <tr>
                <td>
                    <strong>Dates:</strong>
                    <input type="date" name="jobStartDate" value="<?php echo substr($base_output['jobStartDate'],0,10) ?>">
                    <i>to</i>
                    <input type="date" name="jobEndDate" value="<?php echo substr($base_output['jobEndDate'],0,10) ?>">
                </td>
                <td>
                    <label for="locationName">Name: </label><input type="text" name="locationName" Value="<?php echo $base_output['locationName'] ?>">
                    <br><label for="locationName">Address: </label><input type="text" name="streetAddress" Value="<?php echo $base_output['streetAddress'] ?>" >
                    <br><label for="locationName">City, State: </label><input type="text" name="city" id="city" Value="<?php echo $base_output['city'] ?>">
                    <input type="text" name="state" id="state" Value="<?php echo $base_output['state'] ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Comments:</strong>
                    <br><textarea name="comments" id="comments" ><?php echo $base_output['comments']?></textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="right"><input type="submit" value="Update Job"></td>
            </tr>
        </table>
    </form>

 <!-- Task Details html -->
         <!--Begin Add Task Form -->

         <div class="SectionLabel">--- Add New Task ---</div>
    <table>
        <tr>
            <td>
                <form action="" method="post" class="AddTask" >
                    <input type="hidden" name="jobId" value="<?php echo $jobId?>">
                    <label for="NewTask_Type">Type:</label>
                    <select name="NewTask_Type" required>
                    <option value="">Select Task Type</option>
                    <?php foreach($taskTypes_result as $taskType){?>
                        <option value="<?php echo $taskType['taskTypeId']?>"><?php echo $taskType['taskTypeName']?></option>
                    <?php } ?>
                        
                    
                    </select>
                    <label for="NewTaskStart">Start Date:</label>
                    <input type="date" name="NewTaskStart" value="<?php echo substr($base_output['jobStartDate'],0,10)?>" min="<?php echo substr($base_output['jobStartDate'],0,10)?>" max="<?php echo substr($base_output['jobEndDate'],0,10)?>">
                    <label for="NewTaskEnd">End Date:</label>
                    <input type="date" name="NewTaskEnd" value="<?php echo substr($base_output['jobEndDate'],0,10)?>" min="<?php echo substr($base_output['jobStartDate'],0,10)?>" max="<?php echo substr($base_output['jobEndDate'],0,10)?>">
                    <input type="submit" value="Add Task" style= "float: right">
                </form>
            </td>
        </tr>
    </table>
    </div>  
 
 <div class="TaskList">
    <?php 
     // Begin loop to generate task list (table format)

    if($uniqueTaskRoles['0']['taskId'] != '') {

     echo '<div class="SectionLabel">--- Job Task List ---</div>
        ';

     $idx = 0;
     foreach($uniqueTasks as $task){
        echo '<table> <tr> <th class="left">Task ';
        echo $idx+1;
        echo ': '.$task['taskTypeName'].'</th><th></th><th class="right" style="font-weight: normal"><form action="" method="post"><input type="hidden" name="DeleteTask" value="'.$task['taskId'].'"><input type="submit" value="Delete Task"></form></th></tr>';
        echo '<tr> <td><strong>Dates: </strong>'.date_format(date_create($task['taskStartDate']),'m/d/Y').' - '.date_format(date_create($task['taskEndDate']),'m/d/Y').'</td> </tr>
        ';
        

        // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        //begin roles table
        echo '  <table class="innertable"><tr> <th colspan="3">Roles</th></tr><tr><th>Name</th><th>Quantity</th><th>Actions</th></tr>';
        
        //begin form for add new role
        echo '<form action="" method="post"><tr><td>' ;
        echo '<input type="hidden" name="taskId" value='.$task['taskId'].'>';
        echo '<select class="mwinput" name="NewRole" required>';
        echo '<option value="none">Select Role</option>';
        foreach($role_result as $role){
            echo '<option value='.$role['roleId'].'>'.$role['roleName'].'</option>';
        }
        echo '</select></td>';
        echo '<td><input class="mwinput" type="text" name="NewRoleNum" required placeholder="Qty"></td>';
        echo '</select><td><input class="mwinput" type="submit" value="Add Role"> </form> </td></tr>';
        //end Add Role form

        //begin loop to generate list of TaskRoles
        foreach($uniqueTaskRoles as $taskRole){ 
            
            if($taskRole['taskId'] == $task['taskId'] && isset($taskRole['taskRoleId']) ){
                echo '<form action="" method="post"><tr><td>'.$taskRole['roleName'].'</td><td>'.$taskRole['roleQuantity'].'</td><td><input class="mwinput" type="submit" value="Delete Role"></td></tr>';
                echo '<input type="hidden" name="deleteTaskRoleId" value="'.$taskRole['taskRoleId'].'">';
                echo '<input type="hidden" name="taskId" value="'.$taskRole['taskId'].'"></form>';
            } 
        }
        echo '</table>
        ';

        // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        //begin assigned employee table
        echo '  <table class="innertable"><tr> <th colspan="3">Assigned Employees</th></tr><tr><th>Role Name</th><th>Employee Name</th><th>Actions</th></tr>';
        
        //begin form for assign new employee
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="taskId" value='.$task['taskId'].'><tr><td>';
        echo '<select class="mwinput" name="addTREtaskRoleId" required>';
        echo '<option value="none">Select Role</option>';
        foreach($uniqueTaskRoles as $taskRole){
            if($taskRole['taskId'] == $task['taskId'] && isset($taskRole['taskRoleId'])){
                echo '<option value ="'.$taskRole['taskRoleId'].'">'.$taskRole['roleName'].'</option>';
                
            } 
            else {
                continue;
            }
        }
        echo '</select></td>';
        echo '<td><select class="mwinput" name="NewRoleEmp" required>';
        echo '<option value="none">Select Employee</option>';
        $added = [];
        foreach($employeesRoles_result as $employeeRole){
            foreach($uniqueTaskRoles as $taskRole){
                if($employeeRole['roleId'] == $taskRole['roleId'] && (in_array($employeeRole['employeeId'],$added)== FALSE) && $taskRole['taskId']== $task['taskId']){
                    echo '<option value='.$employeeRole['employeeId'].'>'.$employeeRole['firstName'].' '.$employeeRole['lastName'][0].'</option>';
                    $added[] = $employeeRole['employeeId'];
                } else {
                continue;
            }
        }
    }
        echo '</select></td>';
        echo '<td><input class="mwinput" type="submit" value="Assign"> </td></tr></form> ';


        //begin loop to generate list of AssignedEmployees
        foreach($rows as $taskRoleEmployee){
            if($taskRoleEmployee['taskId']== $task['taskId'] && isset($taskRoleEmployee['taskRoleId'])) {
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="taskRoleId" value="'.$taskRoleEmployee['taskRoleId'].'">';
                echo '<input type="hidden" name="deleteTaskRoleEmp" value ="'.$taskRoleEmployee['employeeId'].'"';
                echo '<tr><td>'.$taskRoleEmployee['roleName'].'</td>';
                echo '<td>';
                if(isset($taskRoleEmployee['employeeId'])){
                    echo $taskRoleEmployee['firstName'].' '.$taskRoleEmployee['lastName'][0].'</td>';
                } else { 
                    echo 'Unassigned </td>';
                    }
                echo '<td><input class="mwinput" type="submit" value="Delete">';
                echo '</td></tr></form>';
                 } else {
                continue;
            }
        }
        echo '</table>
        ';


        // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        //begin equipment table
        echo '  <table class="innertable"><tr> <th colspan="3">Equipment</th></tr><tr><th>Name</th><th>Quantity</th><th>Actions</th></tr>';

        //begin form form add new equipment
        echo '<form action="" method="post"><tr><td>';
        echo '<input type="hidden" name="taskId" value='.$task['taskId'].'>';
        echo '<select class="mwinput" name="NewEquip" required>';
        echo '<option value="none">Select Equipment</option>';
        foreach($equipment_result as $equipment){
            echo '<option value='.$equipment['equipmentId'].'>'.$equipment['equipmentName'].'</option>';
        }
        echo '</select></td>';
        echo '<td><input class="mwinput" type="text" name="NewEquipNum" required placeholder="Qty"></td>';
        echo '</select><td><input class="mwinput" type="submit" value="Add Equipment"> </form> </td></tr>';
        //end Add Equipment form

        //begin loop to generate list of Equipment
        foreach($tasksEquipment_result as $tasksEquipment){ 
            
            if($tasksEquipment['taskId'] == $task['taskId'] && isset($tasksEquipment['taskId']) ){

                echo '<form action="" method="post"><tr><td>'.$tasksEquipment['equipmentName'].'</td><td>'.$tasksEquipment['quantityUsed'].'</td><td><input class="mwinput" type="submit" value="Delete Equipment"></td></tr>';
                echo '<input type="hidden" name="deleteTaskEquip" value ="'.$tasksEquipment['equipmentId'].'">';
                echo '<input type="hidden" name="taskId" value="'.$tasksEquipment['taskId'].'"></form>';
            } 
        }
        echo '</table>
        ';
        echo '</table>
        ';

        //end tasks Table


        $idx++;
        }
    }

// end Tasks loop
    ?>
</div>
</main>