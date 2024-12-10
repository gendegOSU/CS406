<?php
// Run these scripts before starting to build the page
// These scripts will create a database connection and process any required UPDATEs, INSERTs, and DELETEs
include('dbconnection.php');
if(isset($_GET['page']) && $_GET['page'] == 'ManageWork'){
    include 'ManageWorkQueries.php';
}
include('update.php');
include('create.php');
include('delete.php');
?>