<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Connexion - Zoo Arcadia</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link href="styles.css" rel="stylesheet">
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
				<div class="mb-3">
					<label for="email" class="form-label">Adresse Email</label>
					<input type="email" class="form-control" id="email">
				</div>

				<div class="mb-3">
					<label for="pass" class="form-label">Mot de passe</label>
					<input type="password" class="form-control" id="pass">
				</div>

				<button type="submit" class="btn btn-success">Connexion</button>
			</form>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>