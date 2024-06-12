<?php
require_once("../server/auth.php");

$role = authCheck();
updateCSRFToken();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = "Admin"; include("../components/head.php"); ?>
</head>
<body>
	<div class="main-container">
		<?php include("../components/navbar.php"); ?>

		<br><br>

		<div class="main-row px-2 px-sm-5">
			<h2 class="fw-bold">Espace <?= $role == "admin" ? "Administrateur" : ($role == "veterinarian" ? "Vétérinaire" : "Employé") ?></h2>
		</div>

		<br>

		<div class="main-row px-2 px-sm-5">
			<?php

			$spacer = "\n</div><hr class=\"spacer\"><div class=\"main-row px-2 px-sm-5\">\n";

			if($role == "admin")
			{
				include("../components/admin_account_form.php");

				echo($spacer);

				include("../components/admin_services_form.php");
			}
			else if($role == "employee")
			{
				include("../components/admin_services_form.php");
			}
			?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>