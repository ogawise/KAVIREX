<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles/u_style.css">
     <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logo.png">
    <title>CLIENT PAGE</title>
</head>
<body>
  <main>
  <form action="user.php" id="registration" method="POST">
      <h2>CREAT AN ACCOUNT </h2><br>
      <div class="client_form">
      <label for="firstname">Full Name:</label>
      <input type="text"  name="firstname" id="firstname" placeholder="enter your firstname"><br><br>
      <p style="color: hsl(0, 80%,60%);" id="firstnameError" class="hidden"></p>
      <img src="./assets/images/icon-error.svg" alt="error icon" class="error-icon hidden">
        </div>
        <div class="client_form">
      <label for="lastname">Address:</label>
      <input type="text"  name="lastname" id="lastname" placeholder="enter your lastname"><br><br>
      <p style="color: hsl(0, 80%,60%)" id="lastnameError" class="hidden"></p>
      <img  src="./assets/images/icon-error.svg" alt="error icon" class="error-icon hidden">
      </div>
      <div class="client_form">
      <label for="emailaddress">Email Address:</label>
      <input type="email"  name="emailaddress" id="emailaddress" placeholder="enter your emailadddress"><br><br>
      <p style="color: hsl(0, 80%,60%)" id="emailaddressError" class="hidden"></p>
      <img  src="./assets/images/icon-error.svg" alt="error icon" class="error-icon hidden">
      </div>
      <div class="client_form">
      <label for="phonenumber">Phone Number:</label>
      <input type="tel"  name="phonenumber" id="phonenumber" placeholder="enter your phonenumber"><br><br>
      <p style="color: hsl(0, 80%,60%)" id="phonenumberError" class="hidden"></p>
      <img  src="./assets/images/icon-error.svg" alt="error icon" class="error-icon hidden">
      </div>
    <div class="client_form">
      <label for="password">Password:</label>
      <input type="password"  name="password" id="password" placeholder="enter your password"><br><br>
      <p style="color: hsl(0, 80%,60%)" id="passwordError" class="hidden"></p>
      <img  src="./assets/images/icon-error.svg" alt="error icon" class="error-icon hidden">
      </div>
      <div class="client_form">
      <label for="confirmpassword">Confirm Password:</label>
      <input type="password"  name="confirmpassword" id="confirmpassword" placeholder="enter your confirmpassword"><br><br>
      <p style="color: hsl(0, 80%,60%)" id="confirmpasswordError" class="hidden"></p>
      <img  src="./assets/images/icon-error.svg"alt="error icon" class="error-icon hidden">
      </div>
      
      <button class="submit">Register</button>
    <P > Already have an account? <a href ="userlogin.php">Login </p>
    </form>
  </main>
      <script src="assets/javascript/u_script.js" defer ></script>
</body>
</html>