    <?php

$existing_pages = array('Jobs','TaskTypes','Customers', 'Employees', 'EmployeesRoles', 'Roles','Equipment', 'EquipmentTypes', 'ManageWork');

// Depending on the URL requested, return page associated with that URL name
// Citation for the following switch function
// Date: 9 Nov 2024
// Copied from Source URL: https://stackoverflow.com/questions/31341637/dynamic-navigation-using-php-include
if(isset($_GET['page']) && in_array($_GET['page'], $existing_pages))
    {
        switch($_GET['page']){
            case 'Jobs':
                $page = 'lists.php';
                break;
            case 'TaskTypes':
                $page = 'lists.php';
                break;
            case 'Customers':
                $page = 'lists.php';
                break;
            case 'Employees':
                $page = 'lists.php';
                break;
            case 'EmployeesRoles':
                $page = 'lists.php';
                break;
            case 'Roles':
                $page = 'lists.php';
                break;
            case 'Equipment':
                $page = 'lists.php';
                break;
            case 'EquipmentTypes':
                $page = 'lists.php';
                break;
            case 'ManageWork':
                $page = 'ManageWork.php';
                break;            
            default:
                $page = 'lists.php';
                break;
        }
        include($page);
    }

?>