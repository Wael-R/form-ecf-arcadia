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

			<h5>
				<?php
				if($role == "admin")
				{
					echo("<a class=\"text-success fw-bold\" href=\"#accountEditor\">Comptes</a> - ");
					echo("<a class=\"text-success fw-bold\" href=\"#serviceEditor\">Services</a> - ");
					echo("<a class=\"text-success fw-bold\" href=\"#habitatEditor\">Habitats</a>\n");
				}
				else if($role == "employee")
				{
					echo("<a class=\"text-success fw-bold\" href=\"#serviceEditor\">Services</a>\n");
				}
				else if($role == "veterinarian")
				{

				}
				?>
			</h5>
		</div>

		<br>

		<hr class="spacer">

		<div class="main-row px-2 px-sm-5">
			<?php
			$spacer = "\n</div><hr class=\"spacer\"><div class=\"main-row px-2 px-sm-5\">\n";

			function serviceForm()
			{
				$formTitle = "Modifier les services"; // form header title
				$formSelectLabel = "Service à modifier ou créer"; // label text for the select dropdown
				$formSelectEntry = "Créer un nouveau service..."; // text for the select dropdown's create option

				$formNameHeader = "Nom du service"; // label text for the name box
				$formName = "Nouveau Service"; // default name value

				$formDescHeader = "Description du service"; // label text for the description box
				$formDesc = "Description..."; // default description value
				$formDescMultiline = true; // turns the description input into a text area instead of a single line text box

				$formCreate = "Créer un nouveau service"; // create button text
				$formUpdate = "Mettre à jour le service"; // update button text
				$formDelete = "Supprimer le service"; // delete button text
				$formDeletePrompt = "Etes vous sûr de vouloir supprimer ce service?"; // delete confirmation modal text

				$formUseImages = false; // enables the image upload feature
				$formImageHeader = ""; // image upload header text
				$formImageSelect = ""; // "select an entry" message

				$formPrefix = "service"; // form id prefix
				$formShortPrefix = "svc"; // form code name

				$formListTarget = "serviceList.php"; // api link to query for entry list
				$formListError = "Erreur lors du chargement des services"; // error message when a known error occurs while loading
				$formListUnknownError = "Erreur inconnue lors du chargement des services"; // error message when an unknown error occurs while loading

				$formUpdateTarget = "serviceUpdate.php"; // api link to query to update entries
				$formUpdateSuccess = "Service mis à jour avec succès"; // message for updating an entry
				$formCreateSuccess = "Service crée avec succès"; // message for creating an entry
				$formDeleteSuccess = "Service supprimé avec succès"; // message for deleting an entry

				include("../components/admin_generic_form.php");
			}

			function habitatForm()
			{
				$formTitle = "Modifier les habitats";
				$formSelectLabel = "Habitat à modifier ou créer";
				$formSelectEntry = "Créer un nouvel habitat...";

				$formNameHeader = "Nom de l'habitat";
				$formName = "Nouvel Habitat";

				$formDescHeader = "Description de l'habitat";
				$formDesc = "Description...";
				$formDescMultiline = true;

				$formCreate = "Créer un nouvel habitat";
				$formUpdate = "Mettre à jour l'habitat";
				$formDelete = "Supprimer l'habitat";
				$formDeletePrompt = "Etes vous sûr de vouloir supprimer cet habitat?";

				$formUseImages = true;
				$formImageHeader = "Image(s) de l'habitat";
				$formImageSelect = "Veuillez selectionner un habitat pour modifier les images";

				$formPrefix = "habitat";
				$formShortPrefix = "hab";

				$formListTarget = "habitatList.php";
				$formListError = "Erreur lors du chargement des habitats";
				$formListUnknownError = "Erreur inconnue lors du chargement des habitats";

				$formUpdateTarget = "habitatUpdate.php";
				$formUpdateSuccess = "Habitat mis à jour avec succès";
				$formCreateSuccess = "Habitat crée avec succès";
				$formDeleteSuccess = "Habitat supprimé avec succès";

				include("../components/admin_generic_form.php");
			}

			if($role == "admin")
			{
				include("../components/admin_account_form.php");

				echo($spacer);

				serviceForm();

				echo($spacer);

				habitatForm();
			}
			else if($role == "employee")
			{
				include("../components/admin_services_form.php");
			}
			else if($role == "veterinarian")
			{
				// todo
			}
			?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>