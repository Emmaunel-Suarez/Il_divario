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
        e.stopPropagation();
        contador = 0;
        contadorSpan.textContent = contador;
        fetch("limpiar_carrito.php", { method: "POST" });
    });

    // Agregar productos al carrito
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
