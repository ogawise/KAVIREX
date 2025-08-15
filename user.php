<?php
require_once "includes/database.php";
require_once "includes/function.php";

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    // Input filtering and validation
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $emailaddress = trim($_POST['emailaddress'] ?? '');
    $phonenumber = trim($_POST['phonenumber'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';
    
    // Validation
    $errors = [];
    
    if(empty($firstname)) {
        $errors[] = "First name is required";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $firstname)) {
        $errors[] = "First name can only contain letters and spaces";
    }
    
    if(empty($lastname)) {
        $errors[] = "Last name is required";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $lastname)) {
        $errors[] = "Last name can only contain letters and spaces";
    }
    
    if(empty($emailaddress)) {
        $errors[] = "Email address is required";
    } elseif (!filter_var($emailaddress, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // if(empty($phonenumber)) {
    //     $errors[] = "Phone number is required";
    // } elseif (!preg_match('/^[0-9]{10,15}$/', $phonenumber)) {
    //     $errors[] = "Invalid phone number format";
    // }
    
    if(empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    if($password !== $confirmpassword) {
        $errors[] = "Passwords do not match";
    }
    
    // If there are errors, display them
    if(!empty($errors)) {
        foreach($errors as $error) {
            echo "<p>Error: $error</p>";
        }
        exit;
    }
    
    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Use prepared statement to prevent SQL injection
    $query = "INSERT INTO users (fullName, Address, email, phoneNumber, password, confirmpassword) 
              VALUES (?, ?, ?, ?, ?, '')"; // Empty confirmpassword as we don't store it
    
    $stmt = mysqli_prepare($connection, $query);
    
    if($stmt === false) {
        die("Database error: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "sssss", $firstname, $lastname, $emailaddress, $phonenumber, $passwordHash);
    
    if(mysqli_stmt_execute($stmt)) {
        echo "Client created successfully";
         redirect("userlogin.php" );
    } else {
        echo "Error" . mysqli_error($connection);
    }
    
    mysqli_stmt_close($stmt);
}