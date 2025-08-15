document.addEventListener("DOMContentLoaded", function(){
    
    event.preventDefault();
const form = document.querySelector('#register');
const inputname= document.querySelector("#name");
const uNerrorMessage = document.querySelector("#uNerrorMessage");
const uNerrorIcon = document.querySelector("#uNerrorIcon");
const inputemail = document.querySelector("#email");
const eAerrorMessage = document.querySelector("#eAerrorMessage");
const eAerrorIcon = document.querySelector("#eAerrorIcon");
const inputaddress= document.querySelector("#address");
const aDerrorMessage = document.querySelector("#aDerrorMessage");
const aDeAerrorIcon = document.querySelector("#aDeAerrorIcon");
const inputnumber = document.querySelector('#number');
const pNerrorMessage = document.querySelector("#pNerrorMessage");
const pNeAerrorIcon= document.querySelector("#pNeAerrorIcon");
const inputimage= document.querySelector("#image");
const errorMessage = document.querySelector("#errorMessage");
const errorIcon= document.querySelector("#errorIcon");
const inputdateofbirth = document.querySelector("#dateofbirth");
const dBerrorMessage = document.querySelector("#dBerrorMessage");
const dBerrorIcon = document.querySelector("#dBerrorIcon");
const inputlicense = document.querySelector("#license");
const dLerrorMessage = document.querySelector("#dLerrorMessage");
const dLeAerrorIcon = document.querySelector("#dLeAerrorIcon");
const inputpassword= document.querySelector("#password");
const pWerrorMessage = document.querySelector("#pWerrorMessage");
const pWeAerrorIcon = document.querySelector("#pWeAerrorIcon");

form.addEventListener('submit', function(event){

       event.preventDefault();
       let isValid = true;

            let name = inputname.value.trim();
       if(name === ""){
        uNerrorMessage.classList.remove('hidden');
        uNerrorMessage.innerText = "Please provide your name";
        uNerrorIcon.classList.remove('hidden');
         inputname.classList.add('invalid');
         isValid =false;
   }else {
        uNerrorMessage.classList.add('hidden');
        uNerrorIcon.classList.add('hidden');
       inputname.classList.remove('invalid');
   }
             let email  = inputemail.value.trim();
       if(email === ""){
        eAerrorMessage.classList.remove('hidden');
        eAerrorMessage.innerText = "Please provide your email";
        eAerrorIcon.classList.remove('hidden');
         inputemail.classList.add('invalid');
         isValid =false;
   }else {
       eAerrorMessage.classList.add('hidden');
       eAerrorIcon.classList.add('hidden');
      inputemail.classList.remove('invalid');
   }
                  let password = inputpassword.value.trim();
       if(password === ""){
        pWerrorMessage.classList.remove('hidden');
       pWerrorMessage.innerText = "Please provide a password";
        pWeAerrorIcon.classList.remove('hidden');
         inputpassword.classList.add('invalid');
         isValid =false;
   }else {
    pWerrorMessage.classList.add('hidden');
    pWeAerrorIcon.classList.add('hidden');
      inputpassword.classList.remove('invalid');
   }
               let address= inputaddress.value.trim();
       if(address === ""){
         aDerrorMessage.classList.remove('hidden');
         aDerrorMessage.innerText = "Please provide an address";
         aDeAerrorIcon.classList.remove('hidden');
         inputaddress.classList.add('invalid');
         isValid =false;
   }else {
         aDerrorMessage.classList.add('hidden');
         aDeAerrorIcon.classList.add('hidden');
         inputaddress.classList.remove('invalid');
   }
               let phoneNumber = inputnumber .value.trim();
       if(phoneNumber === ""){
         pNerrorMessage.classList.remove('hidden');
         pNerrorMessage.innerText = "Please provide a valide phoneNumber";
         pNeAerrorIcon.classList.remove('hidden');
         inputnumber.classList.add('invalid');
         isValid =false;
   }else {
         pNerrorMessage.classList.add('hidden');
         pNeAerrorIcon.classList.add('hidden');
         inputnumber.classList.remove('invalid');
   }
                 let yourImage = inputimage.value.trim();
       if(yourImage === ""){
         errorMessage.classList.remove('hidden');
         errorMessage.innerText = "Please provide an image of yourself";
         errorIcon.classList.remove('hidden');
         inputimage.classList.add('invalid');
         isValid =false;
   }else {
          errorMessage.classList.add('hidden');
         errorIcon.classList.add('hidden');
         inputimage.classList.remove('invalid');
   }
                 let birthDate= inputdateofbirth.value.trim();
       if(address === ""){
        dBerrorMessage.classList.remove('hidden');
         dBerrorMessage.innerText = "Please provide your date of birth";
        dBerrorIcon.classList.remove('hidden');
         inputdateofbirth.classList.add('invalid');
         isValid =false;
   }else {
         dBerrorMessage.classList.add('hidden');
         dBerrorIcon.classList.add('hidden');
         inputdateofbirth.classList.remove('invalid');
   }
                 let license= inputlicense.value.trim();
       if(license === ""){
         dLerrorMessage.classList.remove('hidden');
         dLerrorMessage.innerText = "Please provide the necessary documents";
         dLeAerrorIcon.classList.remove('hidden');
         inputlicense.classList.add('invalid');
         isValid =false;
   }else {
         dLerrorMessage.classList.add('hidden');
         dLeAerrorIcon.classList.add('hidden');
         inputlicense.classList.remove('invalid');
   }
    if(isValid){
     form.submit();
    }
});
function validateEmail($email) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test($email);
}


});