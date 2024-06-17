<?php
	$formTitle = "Modifier les services"; // form header title

	$formSelectLabel = "Service à modifier ou créer"; // label text for the select dropdown
	$formSelectEntry = "Créer un nouveau service..."; // text for the select dropdown's create option

	$formNameLabel = "Nom du service"; // label text for the name box

	$formDescLabel = "Description du service"; // label text for the description box
	$formDescMultiline = true; // turns the description input into a text area instead of a single line text box

	$formDelete = "Supprimer le service"; // delete button text
	$formDeletePrompt = "Etes vous sûr de vouloir supprimer ce service?"; // delete confirmation modal text

	$formUseImages = false; // shows the image upload form
	$formImageLabel = ""; // image upload header text

	$formPrefix = "service"; // form id prefix

	include("../components/admin_generic_form.php");
?>

<script>
const serviceProps = {
	entries: [],

	form: document.getElementById("serviceForm"),

	select: document.getElementById("serviceSelect"),

	listTarget: "./serviceList.php",
	listErrorMsg: "Erreur lors du chargement des services",
	listUnknownErrorMsg: "Erreur inconnue lors du chargement des services",

	titleInput: document.getElementById("serviceTitle"),
	titleDefault: "Nouveau Service",

	descInput: document.getElementById("serviceDescription"),
	descDefault: "Description...",

	useImages: false,
	imageUploadList: null, // div containing the image list
	imageUploadBtn: null, // label acting as the upload button
	imageUploadField: null, // the actual, hidden upload input
	imageSelectMsg: "", // message to show when no existing entry is selected
	imageProgress: null, // upload progress bar container
	imageProgressBar: null, // inner progress bar div

	submitBtn: document.getElementById("serviceButton"),
	submitNew: "Créer un nouveau service",
	submitNewSuccessMsg: "Service crée avec succès",
	submitUpdate: "Mettre à jour le service",
	submitUpdateSuccessMsg: "Service mis à jour avec succès",
	submitTarget: "./serviceUpdate.php",

	deleteBtn: document.getElementById("serviceDelete"),
	deleteConfirmBtn: document.getElementById("serviceDeleteConfirm"),
	deleteSuccessMsg: "Service supprimé avec succès",

	messageField: document.getElementById("serviceMessage"),
};

formSetup(serviceProps);
</script>