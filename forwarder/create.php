<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Forwarder</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <h1>Submitted!</h1>

    <p>API Response:</p>

<?php

include("config.php");

if(!isset($_POST['alias_handle']) or empty($_POST['alias_handle'])) {
    echo "Invalid handle. Usage: https://<SERVICE_DOMAIN>?alias_domain=aliasdomain.com&email_des=destination@example.com";
    exit;
}

if(!isset($_POST['alias_domain']) or empty($_POST['alias_domain'])) {
    echo "Invalid domain. Usage: https://<SERVICE_DOMAIN>?alias_domain=aliasdomain.com&email_des=destination@example.com";
    exit;
}

if(!isset($_POST['email_des']) or empty($_POST['email_des'])) {
    echo "Invalid email destination. Usage: https://<SERVICE_DOMAIN>?alias_domain=aliasdomain.com&email_des=destination@example.com";
    exit;
}

$alias_handle = addslashes(sprintf("%s",$_POST['alias_handle']));
$alias_domain = addslashes(sprintf("%s",$_POST['alias_domain']));
$alias = $alias_handle . "@" . $alias_domain;
$email_des = addslashes(sprintf("%s",$_POST['email_des']));
$description = addslashes(sprintf("%s",$_POST['description']));

// Validate email
if (!filter_var($alias, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email alias.\n";
    exit;
}

// Create email account

$ch_new_alias = curl_init();

curl_setopt($ch_new_alias, CURLOPT_URL, 'https://api.forwardemail.net/v1/domains/'.$alias_domain.'/aliases');
curl_setopt($ch_new_alias, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_new_alias, CURLOPT_POST, 1);
curl_setopt($ch_new_alias, CURLOPT_POSTFIELDS, "email=".$alias_handle."@".$alias_domain."&name=".$alias_handle."&recipients=".$email_des."&description=".$description."&labels=API&is_enabled=true");
curl_setopt($ch_new_alias, CURLOPT_USERPWD, API_TOKEN . ':' . '');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch_new_alias, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch_new_alias);
if (curl_errno($ch_new_alias)) {
    echo 'Error:' . curl_error($ch_new_alias);
}

echo $result;

curl_close($ch_new_alias);

if (LOG_ENABLED == TRUE) {
    // Add the log
    $file = 'log.txt';
    // Open the file to get existing content
    $current = file_get_contents($file);
    // Append a new line to the file
    $current .= $result."\n";
    // Write the contents back to the file
    file_put_contents($file, $current);
}

?>

</body>
</html>