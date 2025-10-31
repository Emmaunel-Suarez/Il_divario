
const btnregistro = document.getElementById('registerForm')

btnregistro.addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    localStorage.setItem("Usuario", name);
    localStorage.setItem("Correo", email);
    localStorage.setItem("Contraseña", password);

    alert('Usuario registrado exitosamente');
    window.location.href = "6.html";
});z