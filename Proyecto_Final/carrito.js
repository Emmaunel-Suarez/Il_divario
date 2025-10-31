// carrito.js
const buttons = document.querySelectorAll('button[data-id]');
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

function actualizarCarrito() {
    localStorage.setItem('carrito', JSON.stringify(carrito));
}

buttons.forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const nombre = btn.dataset.nombre;
        const precio = parseInt(btn.dataset.precio);

        const producto = carrito.find(item => item.id === id);
        if (producto) {
            producto.cantidad += 1;
        } else {
            carrito.push({ id, nombre, precio, cantidad: 1 });
        }

        actualizarCarrito();
        alert(`${nombre} agregado al carrito`);
    });
});
