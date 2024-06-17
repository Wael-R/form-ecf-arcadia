<?php
	$formTitle = "Modifier les animaux"; // form header title

	$formSelectLabel = "Animaux à modifier ou créer"; // label text for the select dropdown
	$formSelectEntry = "Créer un nouvel animal..."; // text for the select dropdown's create option

	$formNameLabel = "Nom de l'animal"; // label text for the name box

	$formDescLabel = "Race de l'animal"; // label text for the description box
	$formDescMultiline = false; // turns the description input into a text area instead of a single line text box

	$formDelete = "Supprimer l'animal"; // delete button text
	$formDeletePrompt = "Etes vous sûr de vouloir supprimer cet animal?"; // delete confirmation modal text

	$formUseImages = true; // shows the image upload form
	$formImageLabel = "Image(s) de l'animal"; // image upload header text

	$formPrefix = "animal"; // form id prefix

	include("../components/admin_generic_form.php");
?>

<script>
const animalProps = {
	entries: [],

	form: document.getElementById("animalForm"),

	select: document.getElementById("animalSelect"),

	listTarget: "./animalList.php",
	listErrorMsg: "Erreur lors du chargement des animaux",
	listUnknownErrorMsg: "Erreur inconnue lors du chargement des animaux",

	titleInput: document.getElementById("animalTitle"),
	titleDefault: "Nouvel animal",

	descInput: document.getElementById("animalDescription"),
	descDefault: "Race...",

	useImages: true,
	imageUploadList: document.getElementById("animalThumbs"), // div containing the image list
	imageUploadBtn: document.getElementById("animalUploadButton"), // label acting as the upload button
	imageUploadField: document.getElementById("animalUpload"), // the actual, hidden upload input
	imageSelectMsg: "Veuillez selectionner un animal pour modifier ses images", // message to show when no existing entry is selected
	imageProgress: document.getElementById("animalProgress"), // upload progress bar container
	imageProgressBar: document.getElementById("animalProgressBar"), // inner progress bar div

	submitBtn: document.getElementById("animalButton"),
	submitNew: "Créer un nouvel animal",
	submitNewSuccessMsg: "Animal crée avec succès",
	submitUpdate: "Mettre à jour l'animal",
	submitUpdateSuccessMsg: "Animal mis à jour avec succès",
	submitTarget: "./animalUpdate.php",

	deleteBtn: document.getElementById("animalDelete"),
	deleteConfirmBtn: document.getElementById("animalDeleteConfirm"),
	deleteSuccessMsg: "Animal supprimé avec succès",

	messageField: document.getElementById("animalMessage"),
};

formSetup(animalProps);
</script>