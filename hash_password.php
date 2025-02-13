<?php
include('db.php');

// Update Admin Passwords
$update_admins = $conn->prepare("SELECT * FROM admin");
$update_admins->execute();
$admins = $update_admins->get_result();
while ($admin = $admins->fetch_assoc()) {
    $hashed_password = password_hash($admin['password'], PASSWORD_DEFAULT); // Hash the password
    $update_query = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
    $update_query->bind_param("si", $hashed_password, $admin['id']);
    $update_query->execute();
}

// Update User Passwords
$update_users = $conn->prepare("SELECT * FROM users");
$update_users->execute();
$users = $update_users->get_result();
while ($user = $users->fetch_assoc()) {
    $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT); // Hash the password
    $update_query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update_query->bind_param("si", $hashed_password, $user['id']);
    $update_query->execute();
}

echo "Passwords updated successfully!";
?>
