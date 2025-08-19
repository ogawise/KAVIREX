const form = document.querySelector("#registration");

form.addEventListener("submit", function (event) {
  event.preventDefault();

  const fields = [
    {
      name: "firstname",
      input: document.querySelector("#firstname"),
      error: document.querySelector("#firstnameError"),
      icon: document.querySelector("#firstnameError + .error-icon")
    },
    {
      name: "lastname",
      input: document.querySelector("#lastname"),
      error: document.querySelector("#lastnameError"),
      icon: document.querySelector("#lastnameError + .error-icon")
    },
    {
      name: "emailaddress",
      input: document.querySelector("#emailaddress"),
      error: document.querySelector("#emailaddressError"),
      icon: document.querySelector("#emailaddressError + .error-icon")
    },
    {
      name: "phonenumber",
      input: document.querySelector("#phonenumber"),
      error: document.querySelector("#phonenumberError"),
      icon: document.querySelector("#phonenumberError + .error-icon")
    },
    {
      name: "password",
      input: document.querySelector("#password"),
      error: document.querySelector("#passwordError"),
      icon: document.querySelector("#passwordError + .error-icon")
    },
    {
      name: "confirmpassword",
      input: document.querySelector("#confirmpassword"),
      error: document.querySelector("#confirmpasswordError"),
      icon: document.querySelector("#confirmpasswordError + .error-icon")
    }
  ];

  let isValid = true;

  // Validate each field
  fields.forEach(field => {
    if (field.input.value.trim() === "") {
      field.input.classList.add("invalid");
      field.error.textContent = `${field.name} cannot be empty`;
      field.error.classList.remove("hidden");
      field.icon.classList.remove("hidden");
      isValid = false;
    } else {
      field.input.classList.remove("invalid");
      field.error.classList.add("hidden");
      field.icon.classList.add("hidden");
    }
  });

  // Check password match
  const password = document.querySelector("#password").value;
  const confirmPassword = document.querySelector("#confirmpassword").value;
  if (password && confirmPassword && password !== confirmPassword) {
    const confirmField = fields.find(f => f.name === "confirmpassword");
    confirmField.input.classList.add("invalid");
    confirmField.error.textContent = "Passwords do not match";
    confirmField.error.classList.remove("hidden");
    confirmField.icon.classList.remove("hidden");
    isValid = false;
  }

  // Submit only if valid
  if (isValid) {
    const formData = new URLSearchParams();
    fields.forEach(field => {
      formData.append(field.name, field.input.value);
    });

    
fetch("user.php", {
  method: "POST",
  headers: { "Content-Type": "application/x-www-form-urlencoded" },
  body: formData
})
.then(response => {
  if (response.redirected) {
    window.location.href = response.url; // Follow PHP redirect
  } else {
    return response.text();
  }
})
.then(data => {
  if (data && data.includes("Client created successfully")) {
    window.location.href = "userlogin.php"; // JS redirect
  } else if (data) {
    alert("Error: " + data);
  }
})
.catch(error => {
  console.error("Error:", error);
  alert("An error occurred during registration");
});
  }
});
