<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'bluejob_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password hash
        if (password_verify($password, $user['password'])) {
            // Password correct — start session
            $_SESSION['userid'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $email;

            // Redirect to jobs page
            header("Location: jobs.html");
            exit();
        } else {
            echo "Incorrect password. <a href='index.html'>Try again</a>.";
        }
    } else {
        echo "No account found with that email. <a href='signup.html'>Sign up</a>.";
    }

    $stmt->close();
}

$conn->close();
?>
