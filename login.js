// Gestione Switch form SignIn - SignUp
document.addEventListener("DOMContentLoaded", () => {
    const signInDiv = document.querySelector("div.signIn");
    const signUpDiv = document.querySelector("div.signUp");
    const showSignUp = document.getElementById("showSignUp");
    const showSignIn = document.getElementById("showSignIn");
    showSignUp.addEventListener("click", () => {
        signInDiv.style.display = "none";
        signUpDiv.style.display = "unset";
    });
    showSignIn.addEventListener("click", () => {
        signUpDiv.style.display = "none";
        signInDiv.style.display = "unset";
    });
});


// Validazione lato client form SignIn
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signInForm");
    form.addEventListener("submit", (event) => {
        const username = form.querySelector("#username");
        const password = form.querySelector("#password");
        if(username.value.trim() == "") {
            alert("Devi inserire un username!");
            username.focus();
            event.preventDefault();
            return;
        }
        if(password.value.trim() == "") {
            alert("Devi inserire una password!");
            password.focus();
            event.preventDefault();
            return;
        }
    });
});

// Validazione lato client form SignUp
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signUpForm");
    form.addEventListener("submit", (event) => {
        const nome = form.querySelector("#nome");
        const email = form.querySelector("#email");
        const username = form.querySelector("#username");
        const password = form.querySelector("#password");
        const conferma_password = form.querySelector("#conferma_password");
        if(nome.value.trim() == "") {
            alert("Devi inserire il tuo nome!");
            nome.focus();
            event.preventDefault();
            return;
        }
        if(email.value.trim() == "") {
            alert("Devi inserire un'email valida!");
            email.focus();
            event.preventDefault();
            return;
        }
        if(username.value.trim() == "") {
            alert("Devi scegliere un username!");
            username.focus();
            event.preventDefault();
            return;
        }
        if(password.value.trim() == "") {
            alert("Devi scegliere una password!");
            password.focus();
            event.preventDefault();
            return;
        }
        if(password.value.trim() != conferma_password.value.trim()) {
            alert("Le password non corrispondono!");
            conferma_password.value = "";
            conferma_password.focus();
            event.preventDefault();
            return;
        }
    });
});
