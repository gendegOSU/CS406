<?php

if($pagetype == 'Admin') echo '
    <div id="sideNavContainer">
        <div id="sideNav">
            <a href="?page=Admin&sub=EmpRoles">Employee Roles</a><br>
            <a href="?page=Admin&sub=EquipTypes">Equipment Types</a><br>
            <a href="?page=Admin&sub=JobStatuses">Job Statuses</a><br>
            <a href="?page=Admin&sub=JobTypes">Job Types</a><br>
            <a href="?page=Admin&sub=Roles">Roles</a><br>
            <a href="?page=Admin&sub=TaskPris">Task Priorities</a><br>
            <a href="?page=Admin&sub=TaskTypes">Task Types</a><br>
            <a href="?page=Admin&sub=Vendors">Vendors</a><br>
            <a href="?page=Admin&sub=Backup">Data Backup</a>
        </div>
    <div id="navContent">
';


if($pagetype == 'Employees') echo '
    <div id="sideNavContainer">
        <div id="sideNav">
            <a href="?page=Employees&sub=EmpInfo">Employee Info</a><br>
            <a href="?page=Employees&sub=EmpTasks">Employee Tasks</a>
        </div>
    <div id="navContent">
';

?>