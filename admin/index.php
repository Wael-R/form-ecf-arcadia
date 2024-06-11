<?php
require_once("../server/auth.php");

$role = authCheck();
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

		<?php

		if($role == "admin")
			include("../components/admin_account_form.php");

		?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>