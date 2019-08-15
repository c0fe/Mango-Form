/*!
  * MangoForm Vanilla Fetch v1.0.0
  * Website (https://github.com/c0fe/MangoForm-Vanilla-JS)
  * Licensed under MIT (https://github.com/c0fe/MangoForm-Vanilla-JS/blob/master/LICENSE)
  */

document.querySelector("#mangoForm").addEventListener("submit", function(e) {
 //create variable for contact form url
 var formURL = 'mangoForm.php';
 //prevent default submission
 event.preventDefault();
 //define form fields
 var mangoForm = {
  'firstName': document.querySelector('input[name=firstName]').value,
  'lastName': document.querySelector('input[name=lastName]').value,
  'companyName': document.querySelector('input[name=companyName]').value,
  'companyAddress': document.querySelector('input[name=companyAddress]').value,
  'city': document.querySelector('input[name=city]').value,
  'state': document.querySelector('select[name=state]').value,
  'zipcode': document.querySelector('input[name=zipcode]').value,
  'emailAddress': document.querySelector('input[name=emailAddress]').value,
  'phoneNumber': document.querySelector('input[name=phoneNumber]').value,
  'message': document.querySelector('input[name=message]').value,
  'g-recaptcha-response': grecaptcha.getResponse()
 }


 //define request variable
 var formRequest = new Request(formURL, {
  method: 'POST',
  body: JSON.stringify(mangoForm),
  headers: {
   "content-type": "application/json; charset=utf-8"
  }
 });
 
 fetch(formRequest)
  .then(function(formResponse) {
   return formResponse.json();
  })
  
  .then(function(data) {
   //handle server responses
   if (!data.success) {
    //handle error messages
    //console.log(data);
    if (data.errors.firstName && !document.querySelector('#firstName-group .help-block')) {
     document.getElementById("firstName-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.firstName;
     document.getElementById("firstName-group").append(helpBlock);
    }
    //handle errors for lastName
    if (data.errors.lastName && !document.querySelector('#lastName-group .help-block')) {
     document.getElementById("lastName-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.lastName;
     document.getElementById("lastName-group").appendChild(helpBlock);
    }
    //handle errors for companyName
    if (data.errors.companyName && !document.querySelector('#companyName-group .help-block')) {
     document.getElementById("companyName-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.companyName;
     document.getElementById("companyName-group").appendChild(helpBlock);
    }
    //handle errors for companyAddress
    if (data.errors.companyAddress && !document.querySelector('#companyAddress-group .help-block')) {
     document.getElementById("companyAddress-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.companyAddress;
     document.getElementById("companyAddress-group").appendChild(helpBlock);
    }
    //handle errors for city
    if (data.errors.city && !document.querySelector('#city-group .help-block')) {
     document.getElementById("city-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.city;
     document.getElementById("city-group").appendChild(helpBlock);
    }
    //handle errors for state
    if (data.errors.state && !document.querySelector('#state-group .help-block')) {
     document.getElementById("state-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.state;
     document.getElementById("state-group").appendChild(helpBlock);
    }
    //handle errors for zipcode
    if (data.errors.zipcode && !document.querySelector('#zipcode-group .help-block')) {
     document.getElementById("zipcode-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.zipcode;
     document.getElementById("zipcode-group").appendChild(helpBlock);
    }
    //handle errors for emailAddress
    if (data.errors.emailAddress && !document.querySelector('#emailAddress-group .help-block')) {
     document.getElementById("emailAddress-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.emailAddress;
     document.getElementById("emailAddress-group").appendChild(helpBlock);
    }
    //handle errors for phoneNumber
    if (data.errors.phoneNumber && !document.querySelector('#phoneNumber-group .help-block')) {
     document.getElementById("phoneNumber-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.phoneNumber;
     document.getElementById("phoneNumber-group").appendChild(helpBlock);
    }
    //handle errors for message
    if (data.errors.message && !document.querySelector('#message-group .help-block')) {
     document.getElementById("message-group").classList.add("has-error");
     let helpBlock = document.createElement('div');
     helpBlock.classList.add('help-block');
     helpBlock.innerHTML = data.errors.message;
     document.getElementById("message-group").appendChild(helpBlock);
    }
    // handle errors for captcha ---------------
    if (data.errors.captcha) {
     swal({
      title: "Error!",
      text: data.errors.captcha,
      icon: "error",
     });
    }
    // handle errors for phpmailer ---------------
    if (data.message) {
     swal({
      title: "Error!",
      text: data.message,
      icon: "error",
     });
    }
   }
   document.getElementById("form").reset();
   if (data.success) {
    swal({
     title: "Success!",
     text: data.message,
     icon: "success",
    });

   }
  });
})