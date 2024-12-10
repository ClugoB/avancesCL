<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>SIIMM</title>
	<link rel="stylesheet" href="style_mujer_principal.css">
	<link rel="stylesheet" href="boxicons-2.1.4/css/boxicons.min.css">
	<link rel="shortcut icon" href="imagenes/mujer3.jpg" type="image/x-icon">
	<!-- ICONOS -->
	<link rel="stylesheet" type="text/css" href="boxicons-2.1.4/css/boxicons.min.css">
</head>
<body>
<header>
<div class="container-navbar">
<nav class="navbar container">
<i class="fa-solid fa-bars"></i>
<ul class="menu">
<li><a href="ministeriodelamujer.php">Inicio</a></li>
<li><a href="que_hacemos.php">¿Qué hacemos?</a></li>
<li><a href="nosotros.php">Nosotros</a></li>
<li><a href="#">Más</a></li>
<li><a href="admin.php"><i class='bx bx-user'></i></i></a></li>
</ul>
<form class="search-form" onsubmit="return searchWord(event)">
<input type="search" id="searchInput" placeholder="Buscar..." required />
<button type="submit" class="btn-search">Buscar</button>
</form>
<div class="resultados" id="resultadosContainer">
<button id="cerrarBoton" onclick="closeSubmenu()">
<i class='bx bx-x'></i> 
</button>
<p id="contador"></p>
<div id="navegacion" class="navegacion">
<button id="anterior" onclick="navigate(-1)" disabled>
<i class='bx bx-left-arrow'></i> 
Anterior
</button>
<button id="despues" onclick="navigate(1)" disabled>
Siguiente    
<i class='bx bx-right-arrow'></i> 
</button>
</div>
</div>
</nav>
</div>
</header>
<section class="banner">
<div class="content-banner">
<p></p>
<h2>SISTEMA DE INFORMACIÓN INTEGRAL DEL MINISTERIO DE LA MUJER</h2>
</div>
</section>
<!-- GALERIA -->
<main class="main-content">
	<section class="container container-features">
	</section>
	<h1 class="heading-1">GALERIA</h1>
<br>
	<section class="container top-categories">
	</section>
	<section class="gallery">
		<img
			src="imagenes/minis1.jpeg"
			alt="Gallery Img1"
			class="gallery-img-1"
		/><img
			src="imagenes/minis2.jpeg"
			alt="Gallery Img2"
			class="gallery-img-2"
		/><img
			src="imagenes/minis11.jpeg"
			alt="Gallery Img3"
			class="gallery-img-3"
		/><img
			src="imagenes/minis5.jpeg"
			alt="Gallery Img4"
			class="gallery-img-4"
		/><img
			src="imagenes/minis10.jpeg"
			alt="Gallery Img5"
			class="gallery-img-5"
		/>
	</section>
<!-- NOTICIAS -->
<h1 class="heading-1">Noticias</h1>
<br>
<section class="container blogs">
    <div class="container-blogs">
        <div class="card-blog">
            <div class="container-img">
                <img src="imagenes/minis3.jpeg" alt="Imagen Blog 1"/>
                <div class="button-group-blog">
                    <span id="busqueda-1" style="cursor:pointer;">
                        <i class='bx bx-search'></i>
                    </span>
                    <span id="link-1" style="cursor:pointer;">
                        <i class='bx bx-link'></i>
                    </span>
                </div>
            </div>
            <div class="content-blog">
                <h3>FORMULARIO DE MOVIMIENTOS</h3>
                <span>FECHA</span>
                <p>Cada jefe de los movimientos tiene permitido rellenar un formulario, debe iniciar sesión para realizar el formulario</p>
                <a href="admin.php"><div class="btn-read-more">Ver más</div></a>
            </div>
        </div>
        <div class="card-blog">
            <div class="container-img">
                <img src="imagenes/minis7.jpeg" alt="Imagen Blog 2" />
                <div class="button-group-blog">
                    <span id="busqueda-2" style="cursor:pointer;">
                        <i class='bx bx-search'></i>
                    </span>
                    <span id="link-2" style="cursor:pointer;">
                        <i class='bx bx-link'></i>
                    </span>
                </div>
            </div>
            <div class="content-blog">
                <h3>IMPORTANTE</h3>
                <span>FECHA</span>
                <p>IMPORTANTE</p>
                <div class="btn-read-more">Leer más</div>
            </div>
        </div>
        <div class="card-blog">
            <div class="container-img">
                <img src="imagenes/minis8.jpeg" alt="Imagen Blog 3" />
                <div class="button-group-blog">
                    <span id="busqueda-3" style="cursor:pointer;">
                        <i class='bx bx-search'></i>
                    </span>
                    <span id="link-3" style="cursor:pointer;">
                        <i class='bx bx-link'></i>
                    </span>
                </div>
            </div>
            <div class="content-blog">
                <h3>VER MAS</h3>
                <p>Ver mas noticias</p>
                <div class="btn-read-more">Leer más</div>
            </div>
        </div>
    </div>
</section>
</main>
	<footer class="footer">
		<div class="container container-footer">
			<div class="menu-footer">
				<div class="contact-info">
					<p class="title-footer">Información de Contacto</p>
					<ul>
						<li><b>Dirección:</b> <b><p class="textos-pie">La Gran Avenida, Caracas 1052, Distrito Capital, Venezuela</p></b></li>
						<li><b>Teléfono:</b> <b><p class="textos-pie">La Gran Avenida, Caracas 1052, Distrito Capital, Venezuela</p></b></li>
						<li><b>Email:</b> <b><p class="textos-pie">directoradespachovigmd@gmail.com</p></b></li>
					</ul>
					<div class="social-icons">
						<span class="facebook">
						<i class='bx bxl-facebook'></i>
						</span>
						<span class="twitter">
						<i class='bx bxl-twitter'></i>
						</span>
						<span class="youtube">
						<i class='bx bxl-youtube'></i>
						</span>
						<span class="pinterest">
						<i class='bx bxl-pinterest'></i>
						</span>
						<span class="instagram">
						<i class='bx bxl-instagram'></i>
						</span>
				</div>
			</div>
			<div class="information">
				<p class="title-footer">Información</p>
				<ul>
					<li><a href="#">Acerca de Nosotros</a></li>
					<li><a href="#">Información</a></li>
					<li><a href="#">Politicas de Privacidad</a></li>
					<li><a href="#">Términos y condiciones</a></li>
					<li><a href="#">Contactános</a></li>
				</ul>
			</div>
			<div class="my-account">
				<p class="title-footer">Mi cuenta</p>
				<ul>
					<li><a href="admin.php">Mi cuenta</a></li>
				</ul>
			</div>
			<div class="newsletter">
				<p class="title-footer">EVENTOS</p>
				<div class="content">
					<p>Si quieres recibir actualizaciones y noticias que realiza el Ministerio de la Mujer ingresa tu correo</p>
					<input type="email" placeholder="Ingresa el correo aquí..." maxlength="30">
					<button>Suscríbete</button>
				</div>
			</div>
		</div>
		<div class="copyright">
			<p class="sistema_pie">SISTEMA DE INFORMACIÓN INTEGRAL DEL MINISTERIO DE LA MUJER &copy; 	<title>SIIMM</title>
 2024</p>
				<img src="imagenes/mujer3.jpg" alt="Pagos">
		</div>
	</div>
</footer>
<!-- SCRIPT -->
<script>
const textToSearch = "SISTEMA DE INFORMACIÓN INTEGRAL DEL MINISTERIO DE LA MUJER, Inicio, ¿Qué hacemos?, Nosotros, Más, Buscar, GALERIA, Noticias, FORMULARIO DE MOVIMIENTOS, FECHA, Cada jefe de los movimientos tiene permitido rellenar un formulario, debe iniciar sesión para realizar el formulario, Ver más, TITULO 2, EJEMPLO 2, VER MAS, Ver mas noticias, Información de Contacto, Dirección, La Gran Avenida, Caracas 1052, Distrito Capital, Venezuela, Teléfono, Email, directoradespachovigmd@gmail.com, facebook, twitter, youtube, pinterest, instagram, Acerca de Nosotros, Información, Politicas de Privacidad, Términos y condiciones, Contactános, Mi cuenta, Suscríbete, eventos, Si quieres recibir actualizaciones y noticias que realiza el Ministerio de la Mujer ingresa tu correo, Ingresa el correo aquí..., SICMMM 2024.";
let matches = [];
let currentIndex = -1;
function searchWord(event) {
event.preventDefault(); 
const searchInput = document.getElementById('searchInput').value;
const resultadossContainer = document.getElementById('resultadosContainer');
const contador = document.getElementById('contador');
const anterior = document.getElementById('anterior');
const despues = document.getElementById('despues');
const regex = new RegExp(searchInput, 'gi');
matches = [...textToSearch.matchAll(regex)];
resultadossContainer.style.display = matches.length > 0 ? 'block' : 'none';
currentIndex = matches.length > 0 ? 0 : -1;
updateHighlight();
anterior.disabled = currentIndex <= 0;
despues.disabled = currentIndex < 0 || currentIndex >= matches.length - 1;
}
function closeSubmenu() {
const resultadosContainer = document.getElementById('resultadosContainer');
resultadosContainer.style.display = 'none'; 
}
function navigate(direction) { 
currentIndex += direction;
updateHighlight();
}
function updateHighlight() {
const resultadosContainer = document.getElementById('resultadosContainer');
const matchCount = document.getElementById('contador');
const anterior = document.getElementById('anterior');
const despues = document.getElementById('despues');
// LIMPIA
matchCount.innerHTML = ''; 
if (currentIndex >= 0 && currentIndex < matches.length) {
const match = matches[currentIndex];
const highlightedWord = match[0];
// ACTUALIZA EL CONTEO
matchCount.innerHTML = `Coincidencia ${currentIndex + 1} de ${matches.length}: <span class="highlight">${highlightedWord}</span>`;
// HABILITAR Y DESHABILITAR BOTONES
anterior.disabled = currentIndex <= 0;
despues.disabled = currentIndex >= matches.length - 1;
} else {
// OCULTAR SI NO HAY
resultadosContainer.style.display = 'none';
}
}
</script>  
 <!-- ALERTAS -->
 <script>
setTimeout(function() {
document.querySelectorAll('.mensaje_exitoso,.mensaje_erroneo').forEach(function(element) {
element.style.display = 'none';
});
}, 5000);
</script>
<!-- TELEFONO -->
<script>
const phoneNumberInput = document.getElementById('phone-number');
const phonePrefixSelect = document.getElementById('phone-prefix');
phoneNumberInput.addEventListener('input', (e) => {
const phoneNumber = e.target.value;
const onlyNumbers = phoneNumber.replace(/[^0-9]/g, '');
e.target.value = onlyNumbers;
if (onlyNumbers.length > 7) {
e.target.value = onlyNumbers.substring(0, 7);
}
});
phonePrefixSelect.addEventListener('change', (e) => {
const selectedPrefix = e.target.value;
phoneNumberInput.value = selectedPrefix + phoneNumberInput.value;
});
</script>
<script>
const iconoBarras = document.querySelector('.fa-solid.fa-bars');
const menu = document.querySelector('.menu');
iconoBarras.addEventListener('click', () => {
  menu.classList.toggle('mostrar');
});
</script>
<script>
// REDIRIGIR CON LA LUPA
document.getElementById('busqueda-1').addEventListener('click', function() {
window.location.href = 'admin.php';
});
document.getElementById('busqueda-2').addEventListener('click', function() {
window.location.href = '#';
});
document.getElementById('busqueda-3').addEventListener('click', function() {
window.location.href = '#';
});
// COPIA EL URL AL DARLE AL LINK
document.getElementById('link-1').addEventListener('click', function() {
const url = 'localhost/mujer/admin.php';
navigator.clipboard.writeText(url).then(function() {
alert('URL copiada: ' + url);
}, function(err) {
console.error('Error al copiar la URL: ', err);
});
});
document.getElementById('link-2').addEventListener('click', function() {
const url = '#';
navigator.clipboard.writeText(url).then(function() {
alert('URL copiada: ' + url);
}, function(err) {
console.error('Error al copiar la URL: ', err);
});
});
document.getElementById('link-3').addEventListener('click', function() {
const url = '#';
navigator.clipboard.writeText(url).then(function() {
alert('URL copiada: ' + url);
}, function(err) {
console.error('Error al copiar la URL: ', err);
});
});
</script>
<script
src="https://kit.fontawesome.com/81581fb069.js"
crossorigin="anonymous">
</script>
</body>
</html>
