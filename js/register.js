"use strict";

const url = "process/register.php";
let form = document.getElementById("form");
let inputsErr = document.getElementsByClassName("input-error");
let errName = document.getElementById("errName");
let errTel = document.getElementById("errTel");
let errEmail = document.getElementById("errEmail");
let errPass = document.getElementById("errPass");
let errPassRepeat = document.getElementById("errPass-repeat");

form.addEventListener('submit', e => {
    e.preventDefault();

    let formData = new FormData(e.target);

    // request
    fetch(url, {
        method: "POST",
        body: formData,
        mode: 'cors'
    })
    .then(data => data.json())
    .then(data =>{

        // se limpian las etiquetas de error, para mostras nuevos mensajes
        errName.innerText = ""; 
        errTel.innerText = ""; 
        errEmail.innerText = ""; 
        errPass.innerText = ""; 
        errPassRepeat.innerText = ""; 


        if (data.length == 0) window.location.href = "login.php";
        else {
            data.forEach(e => {
                let errorId = e[0];
                let errorMsg = e[1]
            
                if (errorId == "name") errName.innerText = errorMsg; 
                else if (errorId == "tel") errTel.innerText = errorMsg; 
                else if (errorId == "email") errEmail.innerText = errorMsg; 
                else if (errorId == "password") errPass.innerText = errorMsg; 
                else if (errorId == "password-repeat") errPassRepeat.innerText = errorMsg; 
                else if (errorId == "main") errMain.innerText = errorMsg;

                // enfocar y mostrar el elemento con el error
                for (let i = 0; i < inputsErr.length; i++) {
                    let element = inputsErr[i];
                    
                    if (element.textContent.length > 0) {
                        element.parentElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        element.focus();

                        i = inputsErr.length;

                        window.scrollBy(0, -20);
                    }
                }
            });
        }
    })
})