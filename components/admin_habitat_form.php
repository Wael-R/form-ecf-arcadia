<?php
	$formTitle = "Modifier les habitats"; // form header title

	$formSelectLabel = "Habitats à modifier ou créer"; // label text for the select dropdown
	$formSelectEntry = "Créer un nouvel habitat..."; // text for the select dropdown's create option

	$formNameLabel = "Nom de l'habitat"; // label text for the name box

	$formDescLabel = "Description de l'habitat"; // label text for the description box
	$formDescMultiline = true; // turns the description input into a text area instead of a single line text box

	$formDelete = "Supprimer l'habitat"; // delete button text
	$formDeletePrompt = "Etes vous sûr de vouloir supprimer cet habitat?"; // delete confirmation modal text

	$formUseImages = true; // shows the image upload form
	$formImageLabel = "Image(s) de l'habitat"; // image upload header text

	$formUseAltButton = false; // adds an extra button next to the submit button
	$formAltButtonText = ""; // text for the extra button

	$formPrefix = "habitat"; // form id prefix

	include("../components/admin_generic_form.php");
?>

<?php include("habitat_comment_list.php"); ?>

<script>
const habitatProps = {
	entries: [],

	form: document.getElementById("habitatForm"),

	select: document.getElementById("habitatSelect"),

	onSelect: function(idx) {
		displayHabitatComments(habitatProps.entries[idx] ?? null);
	},

	onLoaded: function(entries) {
		setupHabitatComments(entries);
	},

	listTarget: "./habitatList.php",
	listErrorMsg: "Erreur lors du chargement des habitats",
	listUnknownErrorMsg: "Erreur inconnue lors du chargement des habitats",

	titleInput: document.getElementById("habitatTitle"),
	titleDefault: "Nouvel habitat",

	descInput: document.getElementById("habitatDescription"),
	descDefault: "Description...",

	useImages: true,
	imageUploadList: document.getElementById("habitatThumbs"), // div containing the image list
	imageUploadBtn: document.getElementById("habitatUploadButton"), // label acting as the upload button
	imageUploadField: document.getElementById("habitatUpload"), // the actual, hidden upload input
	imageSelectMsg: "Veuillez selectionner un habitat pour modifier ses images", // message to show when no existing entry is selected
	imageProgress: document.getElementById("habitatProgress"), // upload progress bar container
	imageProgressBar: document.getElementById("habitatProgressBar"), // inner progress bar div

	submitBtn: document.getElementById("habitatButton"),
	submitNew: "Créer un nouvel habitat",
	submitNewSuccessMsg: "Habitat crée avec succès",
	submitUpdate: "Mettre à jour l'habitat",
	submitUpdateSuccessMsg: "Habitat mis à jour avec succès",
	submitTarget: "./habitatUpdate.php",

	deleteBtn: document.getElementById("habitatDelete"),
	deleteConfirmBtn: document.getElementById("habitatDeleteConfirm"),
	deleteSuccessMsg: "Habitat supprimé avec succès",

	messageField: document.getElementById("habitatMessage"),
};

formSetup(habitatProps);
</script>