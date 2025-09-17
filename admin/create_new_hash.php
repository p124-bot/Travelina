<?php

$newPassword = 'admin123';


$newHash = password_hash($newPassword, PASSWORD_DEFAULT);


echo "Your new password is: " . $newPassword . "<br>";
echo "Copy this hash: <br><br>";
echo "<b>" . $newHash . "</b>";
?>