<?php
if(isset($_GET['page'])) {
  if ($_GET['page'] == 'ManageWork') {
    $pagename = 'Manage Work';
  } elseif ($_GET['page'] == 'EmployeesRoles') {
    $pagename = 'Employee Roles';
  } elseif ($_GET['page'] == 'TaskTypes') {
    $pagename = 'Task Types';
  } elseif ($_GET['page'] == 'EquipmentTypes') {
    $pagename = 'Equipment Types';
  } else {
    $pagename = $_GET['page'];
  }
}
?><html>
  <head>
    <title>Purple Rain Job Scheduler<?php if(isset($pagename)) echo " - $pagename"; ?></title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <header>
      <h1>Purple Rain Job Scheduler<?php if(isset($pagename)) echo " - $pagename"; ?></h1>
      <div class ="topnav">
        <a class = "active" href="?page=Jobs">Jobs</a>
        <a class = "active" href="?page=TaskTypes">Task Types</a>
        <a class = "active" href="?page=Customers">Customers</a>
        <a class = "active" href="?page=Employees">Employees</a>
        <a class = "active" href="?page=EmployeesRoles">Employee Roles</a>
        <a class = "active" href="?page=Roles">Roles</a>
        <a class = "active" href="?page=Equipment">Equipment</a>
        <a class = "active" href="?page=EquipmentTypes">Equipment Types</a>
        <a class = "active" href="?page=ManageWork">Manage Work</a>
      </div>
    <?php
if(isset($reportmessage)){
    echo '<div id="reportMessage">--- '.$reportmessage.' ---</div>';
}
?>
    </header>