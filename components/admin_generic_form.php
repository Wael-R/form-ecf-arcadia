<a class="anchor" id="<?= $formPrefix ?>Editor"></a>
<h5 class="fw-bold"><?= $formTitle ?></h5>

<br>

<div class="card editor-card">
	<form id="<?= $formPrefix ?>Form" action="javascript:void(0);" autocomplete="off">
		<div class="mb-3">
			<label for="<?= $formShortPrefix ?>Select" class="form-label"><?= $formSelectLabel ?></label>
			<select class="form-control" id="<?= $formShortPrefix ?>Select">
				<option value="" selected><?= $formSelectEntry ?></option>
			</select>
		</div>

		<div class="mb-3">
			<label for="<?= $formShortPrefix ?>Title" class="form-label"><?= $formNameHeader ?></label>
			<input type="text" class="form-control" id="<?= $formShortPrefix ?>Title" value="<?= $formName ?>">
		</div>

		<div class="mb-3">
			<label for="<?= $formShortPrefix ?>Description" class="form-label"><?= $formDescHeader ?></label>
		<?php if($formDescMultiline): ?>
			<textarea class="form-control" id="<?= $formShortPrefix ?>Description" rows="3"><?= $formDesc ?></textarea>
		<?php else: ?>
			<input type="text" class="form-control" id="<?= $formShortPrefix ?>Description" rows="3" value="<?= $formDesc ?>">
		<?php endif; ?>
		</div>

		<?php if($formUseImages): ?>
		<div class="mb-3">
			<label class="form-label"><?= $formImageHeader ?></label>
			<div class="editor-box">
				<div id="<?= $formShortPrefix ?>Thumbs"><?= $formImageSelect ?></div>
				<br>
				<label for="<?= $formShortPrefix ?>Upload" id="<?= $formShortPrefix ?>UploadButton" class="btn btn-success disabled">Ajouter...</label>
				<input type="file" id="<?= $formShortPrefix ?>Upload" class="d-none" accept=".png,.jpg,.jpeg,.webp" disabled>
				<div class="editor-progress progress d-none" id="<?= $formShortPrefix ?>Progress">
					<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" id="<?= $formShortPrefix ?>ProgressBar" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<div class="mb-3">
			<p class="login-message" id="<?= $formPrefix ?>Message"></p>
		</div>

		<button type="submit" class="btn btn-success" id="<?= $formShortPrefix ?>Button" disabled><?= $formCreate ?></button>
		<button type="button" class="btn btn-danger" id="<?= $formShortPrefix ?>Delete" data-bs-toggle="modal" data-bs-target="#<?= $formShortPrefix ?>DeleteModal" disabled><?= $formDelete ?></button>
	</form>
</div>

<div class="modal fade" id="<?= $formShortPrefix ?>DeleteModal" tabindex="-1" role="dialog" aria-labelledby="<?= $formShortPrefix ?>DeleteModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="<?= $formShortPrefix ?>DeleteModalTitle"><?= $formDelete ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
				<?= $formDeletePrompt ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="<?= $formShortPrefix ?>DeleteConfirm">Supprimer</button>
      </div>
    </div>
  </div>
</div>

<script>
	// ? this is probably not a very good way of making code like this modular (considering how confused the ide gets lol)
	// all the code already existed, though; this is easier than rewriting it all

	const <?= $formShortPrefix ?>Form = document.getElementById("<?= $formPrefix ?>Form");

	const <?= $formShortPrefix ?>Select = document.getElementById("<?= $formShortPrefix ?>Select");
	const <?= $formShortPrefix ?>Button = document.getElementById("<?= $formShortPrefix ?>Button");
	const <?= $formShortPrefix ?>Delete = document.getElementById("<?= $formShortPrefix ?>Delete");
	const <?= $formShortPrefix ?>DeleteConfirm = document.getElementById("<?= $formShortPrefix ?>DeleteConfirm");

	const <?= $formShortPrefix ?>TitleField = document.getElementById("<?= $formShortPrefix ?>Title");
	const <?= $formShortPrefix ?>DescField = document.getElementById("<?= $formShortPrefix ?>Description");

	<?php if($formUseImages): ?>
	const <?= $formShortPrefix ?>UploadField = document.getElementById("<?= $formShortPrefix ?>Upload");
	const <?= $formShortPrefix ?>UploadButton = document.getElementById("<?= $formShortPrefix ?>UploadButton");
	const <?= $formShortPrefix ?>UploadList = document.getElementById("<?= $formShortPrefix ?>Thumbs");

	const <?= $formShortPrefix ?>Progress = document.getElementById("<?= $formShortPrefix ?>Progress");
	const <?= $formShortPrefix ?>ProgressBar = document.getElementById("<?= $formShortPrefix ?>ProgressBar");
	<?php endif; ?>

	if(<?= $formShortPrefix ?>Form)
	{
		const <?= $formShortPrefix ?>List = [];

		function <?= $formShortPrefix ?>GenerateSelectOptions()
		{
			<?= $formShortPrefix ?>Button.setAttribute("disabled", "");

			const target = "<?= $formListTarget ?>";

			let <?= $formPrefix ?>Message = document.getElementById("<?= $formPrefix ?>Message");
			
			let http = new XMLHttpRequest();

			new Promise(function(resolve, reject) {
				http.onreadystatechange = (ev) => {
					if(http.readyState == 4)
					{
						if(http.status == 200)
							resolve(http);
						else
							reject(http);
					}
				};
	
				http.open("GET", target);
				http.send();
			}).then(() => {
				<?= $formShortPrefix ?>Select.options.length = 1;

				<?= $formShortPrefix ?>List.length = 0;

				let data = JSON.parse(http.responseText);
				
				data.forEach(entry => {
					<?= $formShortPrefix ?>List.push(entry);

					let option = document.createElement("option");

					option.setAttribute("value", <?= $formShortPrefix ?>List.length - 1);
					option.innerHTML = entry.title;

					<?= $formShortPrefix ?>Select.options.length++;
					<?= $formShortPrefix ?>Select.options[<?= $formShortPrefix ?>Select.options.length - 1] = option;
				});

				<?= $formShortPrefix ?>Button.removeAttribute("disabled");
			}).catch(() => {
				if(http.status == 400 || http.status == 401 || http.status == 403)
					<?= $formPrefix ?>Message.innerHTML = "<?= $formListError ?> : " + http.responseText;
				else
					<?= $formPrefix ?>Message.innerHTML = "<?= $formListUnknownError ?> (" + http.status + ")";
			});
		}

		function <?= $formShortPrefix ?>ResetFields()
		{
			<?= $formShortPrefix ?>Button.innerHTML = "<?= $formCreate ?>";
			<?= $formShortPrefix ?>TitleField.value = "<?= $formName   ?>";
			<?= $formShortPrefix ?>DescField.value  = "<?= $formDesc   ?>";
		}

		<?= $formShortPrefix ?>GenerateSelectOptions();
		<?= $formShortPrefix ?>ResetFields();

		<?= $formShortPrefix ?>Select.addEventListener("change", (evt) =>
		{
			document.getElementById("<?= $formPrefix ?>Message").innerHTML = "";

			if(<?= $formShortPrefix ?>Select.value == "")
			{
				<?= $formShortPrefix ?>ResetFields();

				<?= $formShortPrefix ?>Delete.setAttribute("disabled", "");
				<?php if($formUseImages): ?>
				<?= $formShortPrefix ?>UploadList.innerHTML = "<?= $formImageSelect ?>";
				<?= $formShortPrefix ?>UploadButton.classList.add("disabled");
				<?= $formShortPrefix ?>UploadField.setAttribute("disabled", "");
				<?php endif; ?>
			}
			else
			{
				let idx = parseInt(<?= $formShortPrefix ?>Select.value);

				<?= $formShortPrefix ?>Button.innerHTML = "<?= $formUpdate ?>";

				<?= $formShortPrefix ?>TitleField.value = <?= $formShortPrefix ?>List[idx].title;
				<?= $formShortPrefix ?>DescField.value = <?= $formShortPrefix ?>List[idx].desc;

				<?= $formShortPrefix ?>Delete.removeAttribute("disabled");
				<?php if($formUseImages): ?>
				<?= $formShortPrefix ?>UploadList.innerHTML = "";

				<?= $formShortPrefix ?>List[idx].thumbs.forEach(thumb => {
					let div = document.createElement("div");
					div.classList.add("form-control", "d-flex", "justify-content-between", "align-items-center");

					let link = document.createElement("a");
					div.appendChild(link);

					let dirs = thumb.src.split("/");

					link.innerHTML = dirs[dirs.length - 1];
					link.href = thumb.src;
					link.target = "_blank";

					let button = document.createElement("button");
					button.classList.add("btn", "btn-danger");

					button.innerHTML = "x";

					div.appendChild(button);

					<?= $formShortPrefix ?>UploadList.appendChild(div);
				});

				<?= $formShortPrefix ?>UploadButton.classList.remove("disabled");
				<?= $formShortPrefix ?>UploadField.removeAttribute("disabled");
				<?php endif; ?>
			}
		});

		<?php if($formUseImages): ?>
		<?= $formShortPrefix ?>UploadField.addEventListener("cancel", (evt) =>
		{
			const files = <?= $formShortPrefix ?>UploadField.files;

			if(files.length > 0)
				<?= $formShortPrefix ?>UploadField.dispatchEvent(new Event("change"));
		});

		<?= $formShortPrefix ?>UploadField.addEventListener("change", (evt) =>
		{
			const files = <?= $formShortPrefix ?>UploadField.files;

			if(files.length > 0)
			{
				const target = "<?= $formUpdateTarget ?>";

				const token = "<?= getCSRFToken() ?>";

				let <?= $formPrefix ?>Message = document.getElementById("<?= $formPrefix ?>Message");
				let index = <?= $formShortPrefix ?>Select.value;

				let http = new XMLHttpRequest();

				let data = new FormData();

				if(index == "")
					return;
				else
					data.append("id", <?= $formShortPrefix ?>List[index].id);

				data.append("thumb", 1);
				data.append("source", files[0]);

				<?= $formShortPrefix ?>Select.setAttribute("disabled", "");
				<?= $formShortPrefix ?>UploadButton.classList.add("disabled");
				<?= $formShortPrefix ?>UploadField.setAttribute("disabled", "");

				<?= $formShortPrefix ?>Progress.classList.remove("d-none");

				http.onreadystatechange = (ev) => {
					if(http.readyState == 4)
					{
						<?= $formShortPrefix ?>Select.removeAttribute("disabled");
						<?= $formShortPrefix ?>UploadButton.classList.remove("disabled");
						<?= $formShortPrefix ?>UploadField.removeAttribute("disabled");
						<?= $formShortPrefix ?>Progress.classList.add("d-none");

						if(http.status == 400 || http.status == 401 || http.status == 403)
							<?= $formPrefix ?>Message.innerHTML = "Erreur: " + http.responseText;
						else if(http.status == 200)
							<?= $formPrefix ?>Message.innerHTML = "<?= $formImageSuccess ?>";
						else
							<?= $formPrefix ?>Message.innerHTML = "Erreur inconnue (" + http.status + ")";
					}
				};

				http.upload.onprogress = (ev) => {
					if(ev.lengthComputable)
					{
						perc = (ev.loaded / ev.total) * 100;
						<?= $formShortPrefix ?>ProgressBar.style.width = `${perc}%`;
						<?= $formShortPrefix ?>ProgressBar.setAttribute("aria-valuenow", Math.round(perc));
					}
					else
					{
						<?= $formShortPrefix ?>ProgressBar.style.width = "100%";
						<?= $formShortPrefix ?>ProgressBar.setAttribute("aria-valuenow", "100");
					}
				}

				http.open("POST", target);
				http.setRequestHeader("Auth-Token", token);

				http.send(data);
			}
			// else
			// 	console.log("cleared"); // canceled after selecting
		});
		<?php endif; ?>

		<?= $formShortPrefix ?>DeleteConfirm.addEventListener("click", (evt) =>
		{
			const target = "<?= $formUpdateTarget ?>";

			const token = "<?= getCSRFToken() ?>";

			let <?= $formPrefix ?>Message = document.getElementById("<?= $formPrefix ?>Message");
			let index = <?= $formShortPrefix ?>Select.value;

			let http = new XMLHttpRequest();

			let data = new FormData();

			if(index == "")
				return;
			else
				data.append("id", <?= $formShortPrefix ?>List[index].id);

			data.append("delete", "1");

			<?= $formShortPrefix ?>Button.setAttribute("disabled", "");
			<?= $formShortPrefix ?>Delete.setAttribute("disabled", "");

			http.onreadystatechange = (ev) => {
				if(http.readyState == 4)
				{
					<?= $formShortPrefix ?>Button.removeAttribute("disabled");

					if(http.status == 400 || http.status == 401 || http.status == 403)
					{
						<?= $formShortPrefix ?>Delete.removeAttribute("disabled");
						<?= $formPrefix ?>Message.innerHTML = "Erreur: " + http.responseText;
					}
					else if(http.status == 200)
					{
						<?= $formPrefix ?>Message.innerHTML = "<?= $formDeleteSuccess ?>";

						<?= $formShortPrefix ?>ResetFields();
						<?= $formShortPrefix ?>GenerateSelectOptions();
					}
					else
					{
						<?= $formShortPrefix ?>Delete.removeAttribute("disabled");
						<?= $formPrefix ?>Message.innerHTML = "Erreur inconnue (" + http.status + ")";
					}
				}
			};

			http.open("POST", target);
			http.setRequestHeader("Auth-Token", token);

			http.send(data);
		});

		<?= $formShortPrefix ?>Form.addEventListener("submit", (evt) =>
		{
			const target = "<?= $formUpdateTarget ?>";

			const token = "<?= getCSRFToken() ?>";

			let <?= $formPrefix ?>Message = document.getElementById("<?= $formPrefix ?>Message");
			let index = <?= $formShortPrefix ?>Select.value;

			let title = <?= $formShortPrefix ?>TitleField.value;
			let desc = <?= $formShortPrefix ?>DescField.value;

			let http = new XMLHttpRequest();

			let data = new FormData();
			let updating = false;

			if(index == "")
				data.append("id", 0);
			else
			{
				data.append("id", <?= $formShortPrefix ?>List[index].id);
				updating = true;
			}

			data.append("title", title);
			data.append("description", desc);

			<?= $formShortPrefix ?>Button.setAttribute("disabled", "");

			if(updating)
				<?= $formShortPrefix ?>Delete.setAttribute("disabled", "");

			http.onreadystatechange = (ev) => {
				if(http.readyState == 4)
				{
					<?= $formShortPrefix ?>Button.removeAttribute("disabled");

					if(http.status == 400 || http.status == 401 || http.status == 403)
					{
						if(updating)
							<?= $formShortPrefix ?>Delete.removeAttribute("disabled");

						<?= $formPrefix ?>Message.innerHTML = "Erreur: " + http.responseText;
					}
					else if(http.status == 200)
					{
						if(updating)
							<?= $formPrefix ?>Message.innerHTML = "<?= $formUpdateSuccess ?>";
						else
							<?= $formPrefix ?>Message.innerHTML = "<?= $formCreateSuccess ?>";

						<?= $formShortPrefix ?>ResetFields();
						<?= $formShortPrefix ?>GenerateSelectOptions();
					}
					else
					{
						if(updating)
							<?= $formShortPrefix ?>Delete.removeAttribute("disabled");

						<?= $formPrefix ?>Message.innerHTML = "Erreur inconnue (" + http.status + ")";
					}
				}
			};

			http.open("POST", target);
			http.setRequestHeader("Auth-Token", token);

			http.send(data);
		});
	}
	else
	{
		console.error("Form element doesn't exist!");
		alert("Une erreur est survenue; connexion impossible...");
	}
</script>