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

	$formUseAltButton = true; // adds an extra button next to the submit button
	$formAltButtonText = "Affecter à un habitat"; // text for the extra button

	$formPrefix = "animal"; // form id prefix

	include("../components/admin_generic_form.php");
?>

<div class="modal fade" id="animalHabitatModal" tabindex="-1" role="dialog" aria-labelledby="animalHabitatModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="animalHabitatModalTitle">Affecter à un habitat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
				Choisissez un habitat au quel affecter cet animal:
				<select class="form-control" id="animalHabitatSelect"></select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="animalHabitatConfirm">Affecter</button>
      </div>
    </div>
  </div>
</div>

<script>
const animalProps = {
	entries: [],

	form: document.getElementById("animalForm"),

	select: document.getElementById("animalSelect"),

	altSelect: document.getElementById("animalHabitatSelect"),
	altButton: document.getElementById("animalAltButton"),

	onSelect: function(idx) {
		if(idx !== "" && habitatProps.entries.length > 0)
		{
			animalProps.altButton.removeAttribute("disabled");
			habitatProps.entries.forEach(habitat => {
				if(habitat.id != animalProps.entries[idx].habitat)
				{
					let option = document.createElement("option");

					option.setAttribute("value", habitat.id);
					option.innerHTML = habitat.title;

					animalProps.altSelect.options.length++;
					animalProps.altSelect.options[animalProps.altSelect.options.length - 1] = option;
				}
			});
		}
		else
			animalProps.altButton.setAttribute("disabled", "");
	},

	onNewEntry: function(entry, option) {
		let found = false;
		habitatProps.entries.forEach(habitat => {
			if(habitat.id == entry.habitat)
			{
				option.innerHTML = `(${habitat.title}) ${option.innerHTML}`;
				found = true;
				return;
			}
		});

		if(!found)
			option.innerHTML = `(Aucun habitat) ${option.innerHTML}`;
	},

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

animalProps.altButton.setAttribute("data-bs-toggle", "modal");
animalProps.altButton.setAttribute("data-bs-target", "#animalHabitatModal");
document.getElementById("animalHabitatConfirm").addEventListener("click", (evt) => {
	const {entries, submitTarget: target, select, altSelect, messageField} = animalProps;

	let index = select.value;

	let request = new XMLHttpRequest();

	let data = new FormData();

	if(index === "")
		return;
	else
		data.append("id", entries[index].id);

	data.append("habitat", altSelect.value);

	request.onreadystatechange = (ev) => {
		if(request.readyState == 4)
		{
			if(request.status == 400 || request.status == 401 || request.status == 403)
				messageField.innerHTML = "Erreur: " + request.responseText;
			else if(request.status == 200)
			{
				messageField.innerHTML = "Animal affecté a l'habitat avec succès";
				formGenerateSelectOptions(animalProps);
			}
			else
			{
				messageField.innerHTML = "Erreur inconnue (" + request.status + ")";
				console.error("Unknown error while setting a habitat: " + request.statusText);
			}
		}
	};

	request.open("POST", target);
	request.setRequestHeader("Auth-Token", "<?= getCSRFToken() ?>");

	request.send(data);
});

formSetup(animalProps);
</script>