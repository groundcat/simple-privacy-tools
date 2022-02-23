<?php

if(isset($_GET['alias_domain'])) {
    $alias_domain = $_GET['alias_domain'];
}

if(isset($_GET['email_des'])) {
    $email_des = $_GET['email_des'];
}

// Generate random handle
$f_contents = file("random_first_names.txt"); 
$first_name = strtolower($f_contents[rand(0, count($f_contents) - 1)]);
$l_contents = file("random_last_names.txt"); 
$last_name = strtolower($l_contents[rand(0, count($f_contents) - 1)]);
$alias_handle = $first_name . "." . $last_name;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Forwarder</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <h1>Email Alias</h1>

    <form action="create.php" method="post" target="_blank">
        Alias handle:<br>
        <input type="text" name="alias_handle" size="20" autocomplete="off" value="<?=$alias_handle?>" placeholder="your_alias_name"><br><br>
        Alias domain:<br>
        @<input type="text" name="alias_domain" size="18" autocomplete="off" value="<?=$alias_domain?>" placeholder="emailalias.com"><br><br>
        Destination (use comma to separate multiple emails):<br>
        <input type="text" name="email_des" size="20" autocomplete="off" value="<?=$email_des?>" placeholder="destination@example.com"><br><br>
        Description (optional):<br>
        <input type="text" name="description" size="20" autocomplete="off" placeholder="e.g. Used for Facebook"><br><br>
        <input type="submit" value="Create Alias">
    </form>

</body>
</html>