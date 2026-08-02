// ============================================================
// PETCHIP - app.js
// ============================================================

document.addEventListener("DOMContentLoaded", function () {

    // ---- Sidebar responsive ----
    const sidebar = document.getElementById("pcSidebar");
    const toggle = document.getElementById("pcMenuToggle");
    if (toggle && sidebar) {
        toggle.addEventListener("click", () => sidebar.classList.toggle("abierto"));
        document.addEventListener("click", function (e) {
            if (window.innerWidth <= 991 && sidebar.classList.contains("abierto")) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove("abierto");
                }
            }
        });
    }

    // ---- Modo oscuro persistente ----
    const temaBtn = document.getElementById("pcTemaToggle");
    const raiz = document.documentElement;
    const temaGuardado = localStorage.getItem("pc_tema");
    if (temaGuardado === "oscuro") {
        raiz.setAttribute("data-tema", "oscuro");
        if (temaBtn) temaBtn.innerHTML = '<i class="bi bi-sun"></i>';
    }
    if (temaBtn) {
        temaBtn.addEventListener("click", function () {
            const actual = raiz.getAttribute("data-tema") === "oscuro" ? "claro" : "oscuro";
            if (actual === "oscuro") {
                raiz.setAttribute("data-tema", "oscuro");
                temaBtn.innerHTML = '<i class="bi bi-sun"></i>';
            } else {
                raiz.removeAttribute("data-tema");
                temaBtn.innerHTML = '<i class="bi bi-moon-stars"></i>';
            }
            localStorage.setItem("pc_tema", actual);
        });
    }

    // ---- Validacion visual Bootstrap ----
    document.querySelectorAll("form.necesita-validacion").forEach(function (form) {
        form.addEventListener("submit", function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add("was-validated");
        }, false);
    });

    // ---- Vista previa de foto antes de subir ----
    document.querySelectorAll(".input-foto").forEach(function (input) {
        input.addEventListener("change", function () {
            const previsualizacion = document.querySelector(input.dataset.preview);
            if (this.files && this.files[0] && previsualizacion) {
                const lector = new FileReader();
                lector.onload = e => previsualizacion.src = e.target.result;
                lector.readAsDataURL(this.files[0]);
            }
        });
    });

    // ---- Auto-cierre de alertas ----
    document.querySelectorAll(".alert-auto").forEach(function (alerta) {
        setTimeout(() => {
            alerta.classList.add("fade");
            setTimeout(() => alerta.remove(), 400);
        }, 4500);
    });

    // ---- Buscador global (redirige a resultado_tag / listas por nombre) ----
    const buscadorGlobal = document.getElementById("pcBuscadorGlobal");
    if (buscadorGlobal) {
        buscadorGlobal.addEventListener("keypress", function (e) {
            if (e.key === "Enter" && this.value.trim() !== "") {
                e.preventDefault();
                window.location.href = (this.dataset.base || "") + "buscador.php?q=" + encodeURIComponent(this.value.trim());
            }
        });
    }

    // ---- Mostrar / ocultar contraseña (login y formularios) ----
    document.querySelectorAll(".pc-toggle-pass").forEach(function (boton) {
        boton.addEventListener("click", function () {
            const campo = document.getElementById(this.dataset.target);
            if (!campo) return;
            const icono = this.querySelector("i");
            if (campo.type === "password") {
                campo.type = "text";
                if (icono) { icono.classList.remove("bi-eye"); icono.classList.add("bi-eye-slash"); }
            } else {
                campo.type = "password";
                if (icono) { icono.classList.remove("bi-eye-slash"); icono.classList.add("bi-eye"); }
            }
        });
    });
});

// ---- Generar QR de un animal (usa libreria qrcode local, sin depender de internet) ----
function generarQR(contenedorId, texto) {
    const contenedor = document.getElementById(contenedorId);
    if (!contenedor) return;

    if (typeof QRCode === "undefined") {
        console.error("No se encontró la librería QRCode. Verifica que exista el archivo assets/js/vendor/qrcode.min.js");
        contenedor.innerHTML = '<p class="text-danger small mb-0">No se pudo generar el QR: falta el archivo de la librería (assets/js/vendor/qrcode.min.js).</p>';
        return;
    }

    contenedor.innerHTML = "";
    new QRCode(contenedor, { text: texto, width: 150, height: 150, colorDark: "#123E99" });
}
