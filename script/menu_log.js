const SignUp = document.getElementById("SignUp");
const LogIn = document.getElementById("LogIn");
const MeConnecter = document.getElementById("MeConnecter");
const chevron = document.getElementById("chevron");

const addr_liv = document.getElementById("addr_liv")
const I_compte = document.getElementById("I_compte");
const command = document.getElementById("command");

let show_log = false;
let show_setting = false;


function log() {
    if (show_log == false) {
        show_logs()
        show_log = true
    } else {
        Hide_logs()
        show_log = false
    }
}


function show_logs() {
    show_log = true
    SignUp.style.transform = "translateX(110px)"
    LogIn.style.transform = "translateX(110px)"
    chevron.style.rotate = "90deg"
}

function Hide_logs() {
    show_log = false
    SignUp.style.transform = "translateX(0)"
    LogIn.style.transform = "translateX(0)"
    chevron.style.rotate = "0deg"
}



function setting() {
    if (show_setting == false) {
        show_settings()
        show_setting = true
    } else {
        Hide_settings()
        show_setting = false
    }
}


function show_settings() {
    show_setting = true
    addr_liv.style.transform = "translateX(-227px)"
    I_compte.style.transform = "translateX(-224px)"
    command.style.transform = "translateX(-240px)"
}

function Hide_settings() {
    show_setting = false
    addr_liv.style.transform = "translateX(+150px)"
    I_compte.style.transform = "translateX(+150px)"
    command.style.transform = "translateX(+150px)"
}

function shopping() {
    window.location.href = "/panier"
}
