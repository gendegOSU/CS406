<?php
if(isset($pagetype)) {
  if ($pagetype == 'Admin') {
    $pagename = 'Site Admin';
  } else {
    $pagename = $pagetype;
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
        <a href="?page=Calendar">Calendar</a>
        <a href="?page=Customers">Customers</a>
        <a href="?page=Jobs">Jobs</a>
        <a href="?page=Employees">Employees</a>
        <a href="?page=Equipment">Equipment</a>
        <a href="?page=Materials">Materials</a>
        <a href="?page=Admin">Site Admin</a>
      </div>
    <?php
if(isset($reportmessage)){
    echo '<div id="reportMessage">--- '.$reportmessage.' ---</div>';
}
?>
    </header>
    <main>