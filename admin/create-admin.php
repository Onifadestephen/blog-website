<?php
// Include the database connection
require_once __DIR__ . '/../includes/db.php';

// Admin credentials (set these to what you want)
$admin_username = 'admin'; // change this
$admin_password = 'Godisgood08038587309'; // change this

// Hash the password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// SQL query to insert into 'admins' table
$sql = "INSERT INTO admins (username, password) VALUES (:username, :password)";

// Prepare and execute
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':username' => $admin_username,
    ':password' => $hashed_password
]);

echo "✅ Admin account created successfully!<br>";
echo "Username: <strong>$admin_username</strong><br>";
echo "Password: <strong>$admin_password</strong><br>";
echo "⚠️ Now delete this file for security!";
?>
