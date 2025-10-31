import { datos } from "./3-datos.js";

const container = document.getElementById("container");

// Crear tarjetas con estilo original
datos.forEach(producto => {
  const { id, nombre, precio, IMG } = producto;

  // Crear contenedor de la tarjeta
  const card = document.createElement("div");
  card.classList.add("card"); // Mantiene la clase para CSS

  card.innerHTML = `
    <div class="card-head">
      <h4>${nombre}</h4>
      <img src="${IMG}" alt="${nombre}">
    </div>
    <div class="card-body">
      <p>Precio: $${precio.toLocaleString()}</p>
      <button class="add-to-cart" data-id="${id}">Añadir al carrito</button>
    </div>
  `;

  container.appendChild(card);
});

// Event delegation para botones
container.addEventListener("click", e => {
  if(e.target.classList.contains("add-to-cart")){
    const idProducto = e.target.dataset.id;

    fetch("agregar_carrito.php", {
      method: "POST",
      headers: {"Content-Type":"application/x-www-form-urlencoded"},
      body: `id=${idProducto}`
    })
    .then(res => res.text())
    .then(data => alert(data))
    .catch(err => console.error(err));
  }
});
document.addEventListener("DOMContentLoaded", () => {
    let contador = 0;
    const container = document.getElementById("container");
    const contadorSpan = document.getElementById("contador-carrito");
    const carritoBtn = document.getElementById("carrito-flotante");
    const limpiarBtn = document.getElementById("limpiar-carrito");

    // Ir al carrito
    carritoBtn.addEventListener("click", (e) => {
        if(e.target !== limpiarBtn) {
            window.location.href = "carrito.php";
        }
    });

    // Limpiar carrito
    limpiarBtn.addEventListener("click", (e) => {
        e.stopPropagation(); // No dispara ir al carrito
        contador = 0;
        contadorSpan.textContent = contador;
        fetch("limpiar_carrito.php", { method: "POST" });
    });

    // Agregar productos
    container.addEventListener("click", (e) => {
        if(e.target.classList.contains("add-to-cart")){
            const idProducto = e.target.dataset.id;
            const cantidadInput = document.getElementById('cantidad_' + idProducto);
            const cantidad = cantidadInput ? parseInt(cantidadInput.value) : 1;

            // Actualizar contador visual
            contador += cantidad;
            contadorSpan.textContent = contador;

            // Enviar datos al PHP
            fetch("agregar_carrito.php", {
                method: "POST",
                headers: {"Content-Type":"application/x-www-form-urlencoded"},
                body: `id=${idProducto}&cantidad=${cantidad}`
            })
            .then(res => res.text())
            .then(data => console.log(data))
            .catch(err => console.error(err));
        }
    });
});
