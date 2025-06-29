// Seleccionar los elementos de los modales y botones
const btnRegistrarUso = document.getElementById('btnRegistrarUso');
const btnFinalizarUso = document.getElementById('btnFinalizarUso');
const btnRegistrarUsuario = document.getElementById('btnRegistrarUsuario');

const modalRegistrarUso = document.getElementById('modalRegistrarUso');
const modalFinalizarUso = document.getElementById('modalFinalizarUso');
const modalRegistrarUsuario = document.getElementById('modalRegistrarUsuario');

const closeRegistrarUso = document.getElementById('closeRegistrarUso');
const closeFinalizarUso = document.getElementById('closeFinalizarUso');
const closeRegistrarUsuario = document.getElementById('closeRegistrarUsuario');

// Abrir los modales al hacer clic en los botones
btnRegistrarUso.onclick = function () {
    modalRegistrarUso.style.display = "block";
}

btnFinalizarUso.onclick = function () {
    modalFinalizarUso.style.display = "block";
}

btnRegistrarUsuario.onclick = function () {
    modalRegistrarUsuario.style.display = "block";
}

// Funciones para cerrar los modales
closeRegistrarUso.onclick = function () {
    modalRegistrarUso.style.display = "none";
}

closeFinalizarUso.onclick = function () {
    modalFinalizarUso.style.display = "none";
}

closeRegistrarUsuario.onclick = function () {
    modalRegistrarUsuario.style.display = "none";
}

// Cerrar los modales si se hace clic fuera de ellos
window.onclick = function (event) {
    if (event.target == modalRegistrarUso) {
        modalRegistrarUso.style.display = "none";
    } else if (event.target == modalFinalizarUso) {
        modalFinalizarUso.style.display = "none";
    } else if (event.target == modalRegistrarUsuario) {
        modalRegistrarUsuario.style.display = "none";
    }
}
