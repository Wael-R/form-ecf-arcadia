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
	<script src="/utility.js"></script>
	<script>
		function formGenerateSelectOptions(props)
		{
			const {entries, select, onNewEntry, onLoaded, listTarget: target, listErrorMsg, listUnknownErrorMsg, submitBtn, deleteBtn, messageField} = props;
			const {useImages, imageUploadBtn, imageUploadField} = props;

			let old = select.value;

			select.setAttribute("disabled", "");
			submitBtn.setAttribute("disabled", "");
			deleteBtn.setAttribute("disabled", "");

			if(useImages)
			{
				imageUploadBtn.classList.add("disabled");
				imageUploadField.setAttribute("disabled", "");
			}

			let request = new XMLHttpRequest();

			request.onreadystatechange = (evt) => {
				if(request.readyState == 4)
				{
					if(request.status == 400 || request.status == 401 || request.status == 403)
						messageField.innerHTML = listErrorMsg + " : " + stripHTML(request.responseText);
					else if(request.status == 200)
					{
						select.options.length = 1;

						entries.length = 0;

						let data = JSON.parse(request.responseText);

						data.forEach(entry => {
							entries.push(entry);

							let option = document.createElement("option");

							option.setAttribute("value", entries.length - 1);
							option.innerHTML = entry.title;

							if(onNewEntry)
								onNewEntry(entry, option);

							select.options.length++;
							select.options[select.options.length - 1] = option;
						});

						select.removeAttribute("disabled");
						submitBtn.removeAttribute("disabled");

						if(useImages)
						{
							imageUploadBtn.classList.remove("disabled");
							imageUploadField.removeAttribute("disabled");
						}

						select.value = old;

						if(select.value == "")
							select.value = ""; // not resetting this causes out of bounds values to be blank

						if(onLoaded)
							onLoaded(entries);

						select.dispatchEvent(new Event("change"));

						formUpdateFields(props);
					}
					else
					{
						messageField.innerHTML = listUnknownErrorMsg + " (" + request.status + ")";
						console.error("Unknown error while fetching entries: " + request.statusText);
					}
				}
			};

			request.open("GET", target);
			request.send();
		}

		function formResetFields(props)
		{
			const {select, titleInput, titleDefault, descInput, descDefault, submitBtn, submitNew, deleteBtn} = props;
			const {useImages, imageUploadList, imageUploadBtn, imageUploadField, imageSelectMsg} = props;

			select.value = "";
			submitBtn.innerHTML = submitNew;
			titleInput.value = titleDefault;
			descInput.value = descDefault;
			deleteBtn.setAttribute("disabled", "");

			if(useImages)
			{
				imageUploadList.innerHTML = imageSelectMsg;
				imageUploadBtn.classList.add("disabled");
				imageUploadField.setAttribute("disabled", "");
			}
		}

		function formUpdateFields(props)
		{
			const {entries, select, submitTarget: target, titleInput, descInput, submitBtn, submitUpdate, deleteBtn, messageField} = props;
			const {useImages, imageUploadList, imageUploadBtn, imageUploadField} = props;

			if(select.value == "")
				formResetFields(props);
			else
			{
				let index = parseInt(select.value);

				submitBtn.innerHTML = submitUpdate;

				titleInput.value = entries[index].title;
				descInput.value = entries[index].desc;

				deleteBtn.removeAttribute("disabled");

				if(useImages)
				{
					imageUploadList.innerHTML = "";

					let first = true;

					entries[index].thumbs.forEach((thumb) => {
						let div = document.createElement("div");
						div.classList.add("form-control", "d-flex", "justify-content-between", "align-items-center");

						if(first)
							first = false;
						else
							div.classList.add("mt-2");

						let link = document.createElement("a");
						div.appendChild(link);

						let dirs = thumb.src.split("/");

						link.innerHTML = dirs[dirs.length - 1];
						link.href = thumb.src;
						link.target = "_blank";

						let button = document.createElement("button");
						button.classList.add("btn", "btn-danger");

						button.innerHTML = "x";

						button.addEventListener("click", (evt) => {
							evt.preventDefault();

							let request = new XMLHttpRequest();

							let data = new FormData();

							if(index === "")
								return;
							else
								data.append("id", entries[index].id);

							data.append("thumb", 1);
							data.append("thumbId", thumb.id);
							data.append("delete", 1);

							select.setAttribute("disabled", "");
							imageUploadBtn.classList.add("disabled");
							imageUploadField.setAttribute("disabled", "");

							request.onreadystatechange = (evt) => {
								if(request.readyState == 4)
								{
									select.removeAttribute("disabled");
									imageUploadBtn.classList.remove("disabled");
									imageUploadField.removeAttribute("disabled");

									if(request.status == 400 || request.status == 401 || request.status == 403)
										messageField.innerHTML = "Erreur: " + stripHTML(request.responseText);
									else if(request.status == 200)
									{
										messageField.innerHTML = "Image supprimée avec succès";
										formGenerateSelectOptions(props);
									}
									else
									{
										messageField.innerHTML = "Erreur inconnue (" + request.status + ")";
										console.error("Unknown error while deleting images: " + request.statusText);
									}
								}
							};

							request.open("POST", target);
							request.setRequestHeader("Auth-Token", "<?= getCSRFToken() ?>");

							request.send(data);
						});

						div.appendChild(button);

						imageUploadList.appendChild(div);
					});

					if(entries[index].thumbs.length <= 0)
						imageUploadList.innerHTML = "Aucune image";

					imageUploadBtn.classList.remove("disabled");
					imageUploadField.removeAttribute("disabled");
				}
			}
		}

		function formSetup(props)
		{
			const {form, entries, select, onSelect, submitTarget: target, titleInput, descInput, messageField} = props;
			const {submitBtn, submitNewSuccessMsg, submitUpdateSuccessMsg, deleteBtn, deleteConfirmBtn, deleteSuccessMsg} = props;
			const {useImages, imageUploadList, imageUploadBtn, imageUploadField, imageProgress, imageProgressBar} = props;

			formGenerateSelectOptions(props);
			formResetFields(props);

			select.addEventListener("change", (evt) =>
			{
				messageField.innerHTML = "";
				formUpdateFields(props);

				if(onSelect)
					onSelect(select.value);
			});

			if(useImages)
			{
				imageUploadField.addEventListener("cancel", (evt) =>
				{
					const files = imageUploadField.files;

					if(files.length > 0)
						imageUploadField.dispatchEvent(new Event("change"));
				});

				imageUploadField.addEventListener("change", (evt) =>
				{
					const files = imageUploadField.files;

					if(files.length > 0)
					{
						let index = select.value;

						let request = new XMLHttpRequest();

						let data = new FormData();

						if(index === "")
							return;
						else
							data.append("id", entries[index].id);

						data.append("thumb", 1);
						data.append("source", files[0]);

						select.setAttribute("disabled", "");
						imageUploadBtn.classList.add("disabled");
						imageUploadField.setAttribute("disabled", "");

						imageProgress.classList.remove("d-none");

						request.onreadystatechange = (evt) => {
							if(request.readyState == 4)
							{
								select.removeAttribute("disabled");
								imageUploadBtn.classList.remove("disabled");
								imageUploadField.removeAttribute("disabled");
								imageProgress.classList.add("d-none");

								if(request.status == 400 || request.status == 401 || request.status == 403)
									messageField.innerHTML = "Erreur: " + stripHTML(request.responseText);
								else if(request.status == 200)
								{
									messageField.innerHTML = "Image ajoutée avec succès";
									formGenerateSelectOptions(props);
								}
								else
								{
									messageField.innerHTML = "Erreur inconnue (" + request.status + ")";
									console.error("Unknown error while uploading images: " + request.statusText);
								}
							}
						};

						request.upload.onprogress = (evt) => {
							if(evt.lengthComputable)
							{
								perc = (evt.loaded / evt.total) * 100;
								imageProgressBar.style.width = `${perc}%`;
								imageProgressBar.setAttribute("aria-valuenow", Math.round(perc));
							}
							else
							{
								imageProgressBar.style.width = "100%";
								imageProgressBar.setAttribute("aria-valuenow", "100");
							}
						}

						request.open("POST", target);
						request.setRequestHeader("Auth-Token", "<?= getCSRFToken() ?>");

						request.send(data);
					}
				});
			}

			deleteConfirmBtn.addEventListener("click", (evt) =>
			{
				let index = select.value;

				let request = new XMLHttpRequest();

				let data = new FormData();

				if(index === "")
					return;
				else
					data.append("id", entries[index].id);

				data.append("delete", "1");

				submitBtn.setAttribute("disabled", "");
				deleteBtn.setAttribute("disabled", "");

				request.onreadystatechange = (ev) => {
					if(request.readyState == 4)
					{
						submitBtn.removeAttribute("disabled");

						if(request.status == 400 || request.status == 401 || request.status == 403)
						{
							deleteBtn.removeAttribute("disabled");
							messageField.innerHTML = "Erreur: " + stripHTML(request.responseText);
						}
						else if(request.status == 200)
						{
							messageField.innerHTML = deleteSuccessMsg;

							formGenerateSelectOptions(props);
						}
						else
						{
							deleteBtn.removeAttribute("disabled");
							messageField.innerHTML = "Erreur inconnue (" + request.status + ")";
							console.error("Unknown error while deleting an entry: " + request.statusText);
						}
					}
				};

				request.open("POST", target);
				request.setRequestHeader("Auth-Token", "<?= getCSRFToken() ?>");

				request.send(data);
			});

			form.addEventListener("submit", (evt) =>
			{
				let index = select.value;

				let title = titleInput.value;
				let desc = descInput.value;

				let request = new XMLHttpRequest();

				let data = new FormData();
				let updating = false;

				if(index === "")
					data.append("id", 0);
				else
				{
					data.append("id", entries[index].id);
					updating = true;
				}

				data.append("title", title);
				data.append("description", desc);

				submitBtn.setAttribute("disabled", "");

				if(updating)
					deleteBtn.setAttribute("disabled", "");

				request.onreadystatechange = (ev) => {
					if(request.readyState == 4)
					{
						submitBtn.removeAttribute("disabled");

						if(request.status == 400 || request.status == 401 || request.status == 403)
						{
							if(updating)
								deleteBtn.removeAttribute("disabled");

							messageField.innerHTML = "Erreur: " + stripHTML(request.responseText);
						}
						else if(request.status == 200)
						{
							if(updating)
								messageField.innerHTML = submitUpdateSuccessMsg;
							else
								messageField.innerHTML = submitNewSuccessMsg;

							formGenerateSelectOptions(props);
						}
						else
						{
							if(updating)
								deleteBtn.removeAttribute("disabled");

							messageField.innerHTML = "Erreur inconnue (" + request.status + ")";
							console.error("Unknown error while updating an entry: " + request.statusText);
						}
					}
				};

				request.open("POST", target);
				request.setRequestHeader("Auth-Token", "<?= getCSRFToken() ?>");

				request.send(data);
			});
		}
	</script>
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
					echo("<a class=\"text-success fw-bold\" href=\"#habitatEditor\">Habitats</a> - ");
					echo("<a class=\"text-success fw-bold\" href=\"#animalEditor\">Animaux</a>\n");
				}
				else if($role == "employee")
				{
					echo("<a class=\"text-success fw-bold\" href=\"#serviceEditor\">Services</a> - ");
					echo("<a class=\"text-success fw-bold\" href=\"#animalEditor\">Animaux</a>\n");
				}
				else if($role == "veterinarian")
				{
					echo("<a class=\"text-success fw-bold\" href=\"#animalEditor\">Animaux</a> - ");
					echo("<a class=\"text-success fw-bold\" href=\"#habitatComment\">Habitats</a>\n");
				}
				?>
			</h5>
		</div>

		<br>

		<hr class="spacer">

		<div class="main-row px-2 px-sm-5">
			<?php
			$spacer = "\n</div><hr class=\"spacer\"><div class=\"main-row px-2 px-sm-5\">\n";

			if($role == "admin")
			{
				include("../components/admin_account_form.php");

				echo($spacer);

				include("../components/admin_service_form.php");

				echo($spacer);

				include("../components/admin_habitat_form.php");

				echo($spacer);

				include("../components/admin_animal_form.php");
			}
			else if($role == "employee")
			{
				include("../components/admin_service_form.php");

				echo($spacer);

				include("../components/employee_animal_form.php");
			}
			else if($role == "veterinarian")
			{
				include("../components/veterinarian_animal_form.php");

				echo($spacer);

				include("../components/veterinarian_habitat_form.php");
			}
			?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>