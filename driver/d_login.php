<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/d_auth.css">
     <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <script src="../assets/javascript/auth.js" defer></script>
    <title>Login Page</title>
</head>
<body>
    <main>

        <section class="main-container">
             
         <form class="general_login" action="../action/auth.php" method="POST" id="login">
        
            <h1  > LOGIN</h1>
      
         </div>
          <div >
             <label for="email" > Email Address </label>
             <input type="email"  name="email" id="email"
             placeholder="Type your email " >
                   <div id="emailError">
                    
             <img class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon" id="e_errorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="eAerrorMessagee" class="hidden">  </p>
        
       
        </div>
         </div>

           <div >
             <label for="password" > Password</label>
             <input type="password"  name="password" id="password"
             placeholder="Type your password">
                   <div id="passwordError"  >
                     
             <img  class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon" id="p_eAerrorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="pWerrorMessage" >  </p>
        </div>
         </div>
         
         <button type="submit" >Login
          
         </button>
        <P style="margin-bottom: 16px;"> Don't have an account? <a href ="driver_registration.php"> register </p>


                </form>
        </section>
    </main>
    
</body>
</html>