// verify_email.php
if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];
    
    // Check if the verification code matches
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code = ?");
    $stmt->bind_param("s", $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update user status to verified
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE verification_code = ?");
        $stmt->bind_param("s", $verification_code);
        $stmt->execute();
        
        echo "<p>Your email has been successfully verified!</p>";
    } else {
        echo "<p>Invalid verification code.</p>";
    }
}
