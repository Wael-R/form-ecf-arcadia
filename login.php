<?php
require("./server/auth.php");
updateCSRFToken();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = "Connexion"; include("./components/head.php"); ?>
</head>
<body>
	<div class="main-container">
		<?php include("./components/navbar.php"); ?>

		<br><br>

		<div class="card login-card">
			<h2 class="text-success fw-bold">Connexion</h2>
			<h6>Pour administrateurs et employés</h6>

			<hr class="spacer">

			<form id="loginForm" action="javascript:void(0);">
				<input type="hidden" id="token" value="<?= getCSRFToken() ?>">

				<div class="mb-3">
					<label for="email" class="form-label">Adresse Email</label>
					<input type="email" class="form-control" id="email">
				</div>

				<div class="mb-3">
					<label for="pass" class="form-label">Mot de passe</label>
					<input type="password" class="form-control" id="pass">
				</div>

				<div class="mb-3">
					<p class="login-message" id="loginMessage"></p>
				</div>

				<button type="submit" class="btn btn-success">Connexion</button>
			</form>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

	<script>
		const form = document.getElementById("loginForm");

		if(form)
		{
			form.addEventListener("submit", (evt) =>
			{
				const auth = "admin/authLogin.php";
				const target = "admin/index.php";

				let loginMessage = document.getElementById("loginMessage");

				let token = document.getElementById("token").value;
				let email = document.getElementById("email").value;
				let pass = document.getElementById("pass").value;

				let http = new XMLHttpRequest();

				http.onreadystatechange = (ev) => {
					if(http.readyState == 4)
					{
						if(http.status == 401 || http.status == 403)
							loginMessage.innerHTML = "Erreur: " + http.responseText;
						else if(http.status == 200)
							window.location.href = target;
						else
							loginMessage.innerHTML = "Erreur inconnue (" + http.status + ")";
					}
				};

				http.open("POST", auth);
				http.setRequestHeader("Auth-Token", token);
				http.setRequestHeader("Auth-Username", btoa(email));
				http.setRequestHeader("Auth-Password", btoa(pass));

				http.send();
			});
		}
		else
		{
			console.error("Form element doesn't exist!");
			alert("Une erreur est survenue; connexion impossible...");
		}
	</script>
</body>
</html>