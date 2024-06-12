<h5 class="fw-bold">Modifier les services</h5>

<br>

<div class="card editor-card">
	<form id="serviceForm" action="javascript:void(0);">
		<div class="mb-3">
			<label for="svcSelect" class="form-label">Service à modifier ou créer</label>
			<select class="form-control" id="svcSelect">
				<option value="" selected>Créer un nouveau service...</option>
			</select>
		</div>

		<div class="mb-3">
			<label for="svcTitle" class="form-label">Nom du service</label>
			<input type="text" class="form-control" id="svcTitle" autocomplete="off">
		</div>

		<div class="mb-3">
			<label for="svcDescription" class="form-label">Description du service</label>
			<textarea class="form-control" id="svcDescription" rows="2">Description du service...</textarea>
		</div>

		<div class="mb-3">
			<p class="login-message" id="serviceMessage"></p>
		</div>

		<button type="submit" class="btn btn-success" id="svcButton" disabled>Créer un nouveau service</button>
		<button type="button" class="btn btn-danger" id="svcDelete" data-bs-toggle="modal" data-bs-target="#svcDeleteModal" disabled>Supprimer le service</button>
	</form>
</div>

<div class="modal fade" id="svcDeleteModal" tabindex="-1" role="dialog" aria-labelledby="svcDeleteModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="svcDeleteModalTitle">Supprimer le service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
				Etes vous sûr de vouloir supprimer ce service?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="svcDeleteConfirm">Supprimer</button>
      </div>
    </div>
  </div>
</div>

<script>
	const svcForm = document.getElementById("serviceForm");
	const svcSelect = document.getElementById("svcSelect");
	const svcButton = document.getElementById("svcButton");
	const svcDelete = document.getElementById("svcDelete");
	const svcDeleteConfirm = document.getElementById("svcDeleteConfirm");

	if(svcForm)
	{
		const svcList = [];

		function generateSvcSelectOptions()
		{
			svcButton.setAttribute("disabled", "");

			const target = "serviceList.php";

			let serviceMessage = document.getElementById("serviceMessage");
			
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
				svcSelect.options.length = 1;

				svcList.length = 0;

				let lines = http.responseText.split("\t\n");

				lines.forEach(line => {
					let fields = line.split("\t");

					if(fields.length < 3)
						return;

					let id = fields[0];
					let title = fields[1];
					let desc = fields[2];

					svcList.push([id, title, desc]);

					let option = document.createElement("option");

					option.setAttribute("value", svcList.length - 1);
					option.innerHTML = title;

					svcSelect.options.length++;
					svcSelect.options[svcSelect.options.length - 1] = option;
				});

				svcButton.removeAttribute("disabled");
			}).catch(() => {
				if(http.status == 400 || http.status == 401 || http.status == 403)
					serviceMessage.innerHTML = "Erreur lors du chargement des services: " + http.responseText;
				else
					serviceMessage.innerHTML = "Erreur inconnue lors du chargement des services (" + http.status + ")";
			});
		}

		generateSvcSelectOptions();

		svcSelect.addEventListener("change", (evt) =>
		{
			document.getElementById("serviceMessage").innerHTML = "";

			let titleField = document.getElementById("svcTitle");
			let descField = document.getElementById("svcDescription");

			if(svcSelect.value == "")
			{
				svcButton.innerHTML = "Créer un nouveau service";

				titleField.value = "Nouveau Service";
				descField.value = "Description du service...";

				svcDelete.setAttribute("disabled", "");
			}
			else
			{
				let idx = parseInt(svcSelect.value);

				svcButton.innerHTML = "Mettre à jour le service";

				titleField.value = svcList[idx][1];
				descField.value = svcList[idx][2];

				svcDelete.removeAttribute("disabled");
			}
		});

		svcDeleteConfirm.addEventListener("click", (evt) =>
		{
			const target = "serviceUpdate.php";
			
			const token = "<?= getCSRFToken() ?>";

			let serviceMessage = document.getElementById("serviceMessage");
			let serviceIdx = svcSelect.value;

			let titleField = document.getElementById("svcTitle");
			let descField = document.getElementById("svcDescription");
			
			let http = new XMLHttpRequest();

			let data = new FormData();
			let updating = false;

			if(serviceIdx == "")
				return;
			else
				data.append("id", svcList[serviceIdx][0]);

			data.append("delete", "1");

			svcButton.setAttribute("disabled", "");
			svcDelete.setAttribute("disabled", "");

			http.onreadystatechange = (ev) => {
				if(http.readyState == 4)
				{
					svcButton.removeAttribute("disabled");

					if(http.status == 400 || http.status == 401 || http.status == 403)
					{
						svcDelete.removeAttribute("disabled");
						serviceMessage.innerHTML = "Erreur: " + http.responseText;
					}
					else if(http.status == 200)
					{
						serviceMessage.innerHTML = "Service supprimé avec succès";

						svcButton.innerHTML = "Créer un nouveau service";
						titleField.value = "Nouveau Service";
						descField.value = "Description du service...";

						generateSvcSelectOptions();
					}
					else
					{
						svcDelete.removeAttribute("disabled");
						serviceMessage.innerHTML = "Erreur inconnue (" + http.status + ")";
					}
				}
			};

			http.open("POST", target);
			http.setRequestHeader("Auth-Token", token);

			http.send(data);
		});

		svcForm.addEventListener("submit", (evt) =>
		{
			const target = "serviceUpdate.php";

			const token = "<?= getCSRFToken() ?>";

			let serviceMessage = document.getElementById("serviceMessage");
			let serviceIdx = svcSelect.value;

			let titleField = document.getElementById("svcTitle");
			let title = titleField.value;

			let descField = document.getElementById("svcDescription");
			let desc = descField.value;

			let http = new XMLHttpRequest();

			let data = new FormData();
			let updating = false;

			if(serviceIdx == "")
				data.append("id", 0);
			else
			{
				data.append("id", svcList[serviceIdx][0]);
				updating = true;
			}

			data.append("title", title);
			data.append("description", desc);

			svcButton.setAttribute("disabled", "");

			if(updating)
				svcDelete.setAttribute("disabled", "");

			http.onreadystatechange = (ev) => {
				if(http.readyState == 4)
				{
					svcButton.removeAttribute("disabled");

					if(http.status == 400 || http.status == 401 || http.status == 403)
					{
						if(updating)
							svcDelete.removeAttribute("disabled");

						serviceMessage.innerHTML = "Erreur: " + http.responseText;
					}
					else if(http.status == 200)
					{
						if(updating)
							serviceMessage.innerHTML = "Service mis à jour avec succès";
						else
							serviceMessage.innerHTML = "Service crée avec succès";

						svcButton.innerHTML = "Créer un nouveau service";
						titleField.value = "Nouveau Service";
						descField.value = "Description du service...";

						generateSvcSelectOptions();
					}
					else
					{
						if(updating)
							svcDelete.removeAttribute("disabled");

						serviceMessage.innerHTML = "Erreur inconnue (" + http.status + ")";
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