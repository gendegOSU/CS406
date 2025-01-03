<?php

if (isset($_POST['makeBackup']) && $_POST['makeBackup'] == '1') {
    $options_file_name = '';
    $i = '';

    while ($options_file_name == '') {
        if (!file_exists('c:\xampp\backup'.$i.'.cnf')) {
            $options_file_name = 'c:\xampp\backup'.$i.'.cnf';
        }
        $i .= '_';
    }

    $options_file_contents = '[mysqldump]
    user='.$db_user_name.'
    password='.$db_password;

    file_put_contents($options_file_name, $options_file_contents);

    $command = 'C:\xampp\mysql\bin\mysqldump --defaults-extra-file='.$options_file_name.' --single-transaction --result-file=c:\xampp\dump.sql --skip-add-locks purple_rain';
    exec($command);

    unlink($options_file_name);

}
elseif (isset($_POST['loadBackup']) && $_POST['loadBackup'] == '1') {
    $options_file_name = '';
    $i = '';

    while ($options_file_name == '') {
        if (!file_exists('c:\xampp\backup'.$i.'.cnf')) {
            $options_file_name = 'c:\xampp\backup'.$i.'.cnf';
        }
        $i .= '_';
    }

    $options_file_contents = '[mysql]
    user='.$db_user_name.'
    password='.$db_password;

    file_put_contents($options_file_name, $options_file_contents);
    
    $command = 'C:\xampp\mysql\bin\mysql --defaults-extra-file='.$options_file_name.' -e "source c:\xampp\dump.sql" purple_rain';
    exec($command);    

    unlink($options_file_name);
}

echo '<form action="" method="post">
    <input type="hidden" name="makeBackup" value="1">
    <input type="submit" value="Create Backup">
</form>
<form action="" method="post">
    <input type="hidden" name="loadBackup" value="1">
    <input type="submit" value="Load Backup">
</form>';




// TODO Backup scheduling
// TODO Change backup path
// TODO Show prior backups
// TODO confirmations

?>

