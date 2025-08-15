document.addEventListener("DOMContentLoaded", function(){
    
    event.preventDefault();
const form = document.querySelector('#login');
// const inputuserName = document.querySelector("#userName");
// const uNerrorMessage = document.querySelector("#uNerrorMessage");
// const uNerrorIcon = document.querySelector("#uNerrorIcon");
const inputemail = document.querySelector("#email");
const eAerrorMessage = document.querySelector("#eAerrorMessagee");
const e_errorIcon = document.querySelector("#e_errorIcon");
const inputpassword= document.querySelector("#password");
const pWerrorMessage = document.querySelector("#pWerrorMessage");
const p_eAerrorIcon = document.querySelector("#p_eAerrorIcon");

form.addEventListener('submit', function(event){

       event.preventDefault();
       let isValid = true;
             let email  = inputemail.value.trim();
       if(email === ""){
        eAerrorMessage.classList.remove('hidden');
       eAerrorMessage.innerText = "Please provide your email";
        e_errorIcon.classList.remove('hidden');
         inputemail.classList.add('invalid');
         isValid =false;
   }else {
       eAerrorMessage.classList.add('hidden');
       e_errorIcon.classList.add('hidden');
      inputemail.classList.remove('invalid');
   }
               let password = inputpassword.value.trim();
       if(password === ""){
        pWerrorMessage.classList.remove('hidden');
       pWerrorMessage.innerText = "Please provide a password";
        p_eAerrorIcon.classList.remove('hidden');
         inputpassword.classList.add('invalid');
         isValid =false;
   }else {
    pWerrorMessage.classList.add('hidden');
    p_eAerrorIcon.classList.add('hidden');
    inputpassword.classList.remove('invalid');
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