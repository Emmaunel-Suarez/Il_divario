
const login = document.getElementById('loginForm');

login.addEventListener('submit', function(e) {
    e.preventDefault();

    const loginEmail = document.getElementById('email').value;
    const loginPassword = document.getElementById('password').value;

    const storeEmail = localStorage.getItem("Correo");
    const storePassword = localStorage.getItem("Contraseña");

    if (loginEmail === storeEmail && loginPassword === storePassword) {
        window.location.href = "1.html";
        alert('Inicio de sesión exitoso');

    } else {
        alert('Correo o contraseña incorrecto');
    }
});