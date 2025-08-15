<?php
require_once "../includes/function.php";
require_once "../includes/database.php";

// Check request method
if($_SERVER['REQUEST_METHOD'] !== "POST") {
    $queryString = http_build_query(["error" => "invalid_request_method"]);
    // header("Location: ../driver/driver.php");
    redirect("../driver/driver.php", $queryString);
}else {
    // Initialize variables with null coalescing
    $name = $_POST["name"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $number = $_POST["number"] ?? '';
    $address = $_POST["address"] ?? '';
    $dateofbirth = $_POST["dateofbirth"] ?? '';
    $profileImageName = '';
    $licenseImageName = '';
    
    // Validate email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $queryString = http_build_query(["error" => "invalid_email"]);
        redirect("../driver/driver.php", $queryString);
        exit;
    }
    
    // Check required fields
    if(empty($name) || empty($email) || empty($password) || 
    empty($number) || empty($address) || empty($dateofbirth)) {
        $queryString = http_build_query(["error" => "empty_fields"]);
        redirect("../driver/driver.php", $queryString);
        exit;
    }
     function handleFileUpload($file, $targetDir) {
        $fileName = $file['name'];
        $fileTempName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        
        // Validate file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        
        if(!in_array($fileExt, $allowed)) {
            return false;
        }
        
        // Validate file is actually an image
        if(!getimagesize($fileTempName)) {
            return false;
        }
        
        // Validate file size (1MB max)
        if($fileSize > 1000000) {
            return false;
        }
        
        // Generate unique filename
        $fileNameNew = uniqid('', true).".".$fileExt;
        $uploadDir = "../".$targetDir."/";
        
        // Create directory if it doesn't exist
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Move uploaded file
        if(!move_uploaded_file($fileTempName, $uploadDir.$fileNameNew)) {
            return false;
        }
        
        return $fileNameNew;
    }

    
    // Handle Profile Image Upload
    //We check if the varriable exit and is not null befor using it
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $profileImageName = handleFileUpload($_FILES['image'], 'profiles');
        if(!$profileImageName) {
            $queryString = http_build_query(["error" => "profile_upload_failed"]);
            redirect("../driver/driver.php", $queryString);
            exit;
        }
    } else {
        $queryString = http_build_query(["error" => "no_profile_uploaded"]);
        redirect("../driver/driver.php", $queryString);
        exit;
    }
    
    // Handle License Image Upload
    if(isset($_FILES['license']) && $_FILES['license']['error'] === UPLOAD_ERR_OK) {
        $licenseImageName = handleFileUpload($_FILES['license'], 'licenses');
        if(!$licenseImageName) {
            @unlink("../profiles/".$profileImageName); // Clean up profile image
            $queryString = http_build_query(["error" => "license_upload_failed"]);
            redirect("../driver/driver.php", $queryString);
            exit;
        }
    } else {
        @unlink("../profiles/".$profileImageName); // Clean up profile image
        $queryString = http_build_query(["error" => "no_license_uploaded"]);
        redirect("../driver/driver.php", $queryString);
        exit;
    }
    
    // Hash password
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Database insertion
    $sql = "INSERT INTO drivers(userName, email, password, phoneNumber, address, yourImage, yourLicense, dateofbirth) 
            VALUES(?,?,?,?,?,?,?,?)";
    
    $statement = $connection->prepare($sql);
    if(!$statement) {
        @unlink("../profiles/".$profileImageName);
        @unlink("../licenses/".$licenseImageName);
        $queryString = http_build_query(["error" => "database_error"]);
        redirect("../driver/driver.php", $queryString);
        exit;
    }
    
    $statement->bind_param("ssssssss", $name, $email, $hashPassword, $number, $address, $profileImageName, $licenseImageName, $dateofbirth);
    $execute = $statement->execute();
    
    if(!$execute) {
        @unlink("../profiles/".$profileImageName);
        @unlink("../licenses/".$licenseImageName);
        $queryString = http_build_query(["error" => "user_not_saved"]);
        redirect("../driver/driver.php", $queryString);
    } else {
        $driver_id= $statement->insert_id;
        redirect("../driver/d_login.php");
        
    
    }
    

   

}

