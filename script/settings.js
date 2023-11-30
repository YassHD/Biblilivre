const Prenom = document.getElementById("Prenom");
const Nom = document.getElementById("nom");
const email = document.getElementById("email");
const tel = document.getElementById("tel");

const old_password = document.getElementById("old_password");
const password = document.getElementById("password");
const confirm_password = document.getElementById("confirm_password");

const EditInfo = document.getElementById("EditInfo");
const EditPass = document.getElementById("EditPass");

const img_easter_egg = document.getElementById("img_easter_egg")

const ChangeInformations = document.getElementById("ChangeInformations");
const ChangePassword = document.getElementById("ChangePassword");

const submitInfo = document.getElementById("submitInfo");
const submitPass = document.getElementById("submitPass");

let egg = 0
function easter_egg() {
    if (egg == 5) {
        img_easter_egg.style.display = "block";
    } else {
        egg++
        console.log(egg);
    }
}

$('#submitInfo').click(function (e) {
    if (Prenom.value == "" || Nom.value == "" || email.value == "" || tel.value == "") {
        e.preventDefault();
        alert("Veuillez remplir tous les champs !");
    } else {
        $('#ChangeInformations').submit();
    }
});

$('#submitPass').click(function (e) {
    if (password.value != confirm_password.value || password.value == "" || confirm_password.value == "" || old_password.value == "") {
        e.preventDefault();
        alert("Les mots de passe ne correspondent pas ou sont vides !");
    } else {
        $('#ChangePassword').submit();
    }

});

EditInfo.addEventListener("click", function () {
    if (Prenom.disabled) {
        EditInfo.style.border = "1px solid white";
        Change_Info_State(false)
        submitInfo.style.display = "block";
        submitInfo.disabled = false;
    } else {
        EditInfo.style.border = "none";
        Change_Info_State(true)
        submitInfo.style.display = "none";
        submitInfo.disabled = true;
    }
});

EditPass.addEventListener("click", function () {
    if (password.disabled) {
        EditPass.style.border = "1px solid white";
        Change_Pass_State(false)
        submitPass.style.display = "block";
        submitPass.disabled = false;
    } else {
        EditPass.style.border = "none";
        Change_Pass_State(true)
        submitPass.style.display = "none";
        submitPass.disabled = true;
    }
});

window.onload = function () {
    submitInfo.style.border = "none";
    submitInfo.disabled = true;
    submitPass.style.border = "none";
    submitPass.disabled = true;
    Change_Info_State(true)
    Change_Pass_State(true)
}

function Change_Info_State(state) {
    Prenom.disabled = state;
    Nom.disabled = state;
    email.disabled = state;
    tel.disabled = state;
}

function Change_Pass_State(state) {
    old_password.disabled = state;
    password.disabled = state;
    confirm_password.disabled = state;
}

