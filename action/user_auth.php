<?php
require_once "../includes/function.php";
require_once "../includes/database.php";

if($_SERVER['REQUEST_METHOD'] !== "POST") {

    $queryString = http_build_query([
        "error" => "invalid_request_method"
    ]);
    redirect("../userlogin.php", $queryString);
}
else{
    $email = $_POST["email"];
     $password = $_POST["password"]; 

        if( !filter_var($email, FILTER_VALIDATE_EMAIL)){  $queryString = http_build_query([
        "error" => "invalid_email"     ]);
        redirect("../userlogin.php", $queryString);
    }

       if( empty($email) || empty($password)) {

         $queryString = http_build_query([ "error" => "empty_fields"]);
    redirect("../userlogin.php", $queryString);
     }

       //  check in the database is the user info exist
         $query = "SELECT * FROM users WHERE email = '$email' ";
    $result = mysqli_query($connection,$query);

     //check if at least one user is selected from the database
    if(mysqli_num_rows($result) !== 1){
              $queryString = http_build_query([ "error" => "invalid_credentials"]);
    redirect("../userlogin.php", $queryString);
     }

     
          //save the user info into the variable if user exist in the database

     $user = mysqli_fetch_assoc($result);
     echo "<pre>";
     print_r($user);    
     echo "<pre>";
  
if (!password_verify($password, $user['password'])) {
    $queryString = http_build_query(["error" => "invalid_credentials"]);
    redirect("../userlogin.php", $queryString);
}
 session_start();
   $_SESSION['userId'] =$user['user_id'];
   $_SESSION['name'] =$user['fullName'];
    $_SESSION["address"] =$user['address']; 
     $_SESSION["number"] =$user['phoneNumber']; 
   $_SESSION['email'] =$user['email'];
   $_SESSION["password"] =$user['password']; 
  
  
   redirect("../products.php");
        
        

}