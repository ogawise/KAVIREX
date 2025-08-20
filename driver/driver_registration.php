<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>registration</title>
    <link rel="stylesheet" href="../assets/styles/d_auth.css">
     <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <script src="../assets/javascript/driver.js" defer></script>
</head>
<body>
     <main>

        <section class="driver-content">
                
         <form class="drivers_form" action="../action/create.php" method="POST"  id="register" enctype="multipart/form-data">
            <h1 > DRIVER REGISTRATION </h1>
      
         <div  >
             <label for="name" > Name </label>
             <input type="text"  name="name" id="name"
             placeholder="fill in your name " >
               <div id="Error">
                 
             <img  class="icon hidden" src="../assets/images/icon-error.svg" alt="error_icon" id="uNerrorIcon"  >
              <p style="color:hsl(0, 80%,60%);" id="uNerrorMessage" class="hidden">  </p>
       </div>
       
     
         </div>
          <div >
             <label for="email" > Email </label>
             <input type="email"  name="email" id="email"
             placeholder="fill in your email " >
                   <div id="Error">
                    
             <img   class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon" id="eAerrorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="eAerrorMessage" class="hidden">  </p>
        
       
        </div>
            <div >
             <label for="password" > Password</label>
             <input type="password"  name="password" id="password"
             placeholder="Type your password">
                   <div id="passwordError"  >
                     
             <img  class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon" id="pWeAerrorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="pWerrorMessage" >  </p>
        </div>
         </div>
         </div>

           <div >
             <label for="address" > Address</label>
             <input type="text"  name="address" id="address"
             placeholder="fill in your address">
                   <div id="Error"  >
             <img  class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon" id="aDeAerrorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="aDerrorMessage" >  </p>
        </div>
         </div>
         
           <div >
             <label for="phone-number" > Phone Number</label>
             <input type="text"  name="number" id="number"
             placeholder="fill in your number">
                   <div id="Error"  >
             <img  class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon" id="pNeAerrorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="pNerrorMessage" >  </p>
        </div>
         </div>
             
           <div >
             <label for="image" > Image</label>
             <input type="file"  name="image" id="image"
             placeholder="uploade a picture of yourself">
                   <div id="Error"  >
             <img  class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon" id="errorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="errorMessage" >  </p>
        </div>
         </div>
             
           <div >
             <label for="dateofbirth" > Date of birth</label>
             <input type="date"  name="dateofbirth" id="dateofbirth"
             placeholder="fill in your date of birth">
                   <div id="Error"  >
             <img  class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon"
             id="dBerrorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="dBerrorMessage" >  </p>
        </div>
         </div>
             
           <div >
             <label for="license" >Driver-License/Birth-Certificate/National-ID</label>
             <input type="file"  name="license" id="license"
             placeholder="send in the required doc">
                   <div id="Error"  >
             <img   class="icon hidden"  src="../assets/images/icon-error.svg" alt="error_icon" id="dLeAerrorIcon" >
              <p style="color:hsl(0, 80%,60%);" id="dLerrorMessage" >  </p>
        </div>
         </div>
         
         
         <button type="submit"  class="driver-btn" >Register</button>
         <P > Already have an account? <a href ="./d_login.php">Login </p>


                </form>
        </section>
    </main>
    
    
</body>
</html>