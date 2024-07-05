<a class="anchor" id="habitatEditor"></a>
<h5 class="fw-bold">Avis sur les habitats</h5>

<br>

<div class="card editor-card">
	<form id="habitatForm" action="javascript:void(0);" autocomplete="off">
		<div class="mb-3">
			<label for="habitatSelect" class="form-label">Habitat</label>
			<select class="form-control" id="habitatSelect">
			</select>
		</div>

		<div class="mb-3">
			<label for="habitatComment" class="form-label">Commentaire</label>
			<textarea class="form-control" id="habitatComment" rows="3"></textarea>
		</div>

		<div class="mb-3">
			<p class="login-message" id="habitatMessage"></p>
		</div>

		<button type="submit" class="btn btn-success" id="habitatButton" disabled>Soumettre</button>
	</form>
</div>

<?php include("habitat_comment_list.php"); ?>

<script>
	const vetHabitatForm = document.getElementById("habitatForm");
	const vetHabitatData = [];

	if(vetHabitatForm)
	{
		function vetHabitatGenerateSelectOptions()
		{
			const messageField = document.getElementById("habitatMessage");
			const select = document.getElementById("habitatSelect");
			const submit = document.getElementById("habitatButton");

			const target = "habitatList.php";

			submit.setAttribute("disabled", "");
			select.setAttribute("disabled", "");

			let request = new XMLHttpRequest();

			request.onreadystatechange = (ev) => {
				if(request.readyState == 4)
				{
					if(request.status == 400 || request.status == 401 || request.status == 403)
						messageField.innerHTML = "Erreur lors du chargement des habitats: " + stripHTML(request.responseText);
					else if(request.status == 200)
					{
						vetHabitatData.length = 0;

						let old = select.value;
						select.options.length = 0;

						const data = JSON.parse(request.responseText);

						data.forEach((habitat) => {
							vetHabitatData.push(habitat);

							let option = document.createElement("option");

							option.setAttribute("value", vetHabitatData.length - 1);
							option.innerHTML = habitat.title;

							select.options.length++;
							select.options[select.options.length - 1] = option;
						});

						setupHabitatComments(vetHabitatData);

						if(vetHabitatData.length > 0)
						{
							submit.removeAttribute("disabled");
							select.removeAttribute("disabled");

							select.value = old;

							if(select.value == "")
								select.value = 0;

							select.dispatchEvent(new Event("change"));
						}
					}
					else
						messageField.innerHTML = "Erreur inconnue lors du chargement des animaux (" + request.status + ")";
				}
			};

			request.open("GET", target);
			request.send();
		}

		function vetHabitatSetupForm()
		{
			const messageField = document.getElementById("habitatMessage");
			const select = document.getElementById("habitatSelect");
			const submit = document.getElementById("habitatButton");

			const commentField = document.getElementById("habitatComment");

			vetHabitatGenerateSelectOptions();

			select.addEventListener("change", (evt) =>
			{
				messageField.innerHTML = "";

				let index = select.value;
				let habitat = vetHabitatData[index];

				if(habitat)
					commentField.value = "Commentaire...";
			});

			vetHabitatForm.addEventListener("submit", (evt) =>
			{
				const target = "habitatComment.php";

				const token = "<?= getCSRFToken() ?>";

				let comment = commentField.value;

				let request = new XMLHttpRequest();
				let data = new FormData();

				let habitat = vetHabitatData[select.value];

				if(!habitat)
					return;

				data.append("id", habitat.id);
				data.append("comment", comment);

				request.onreadystatechange = (ev) => {
					if(request.readyState == 4)
					{
						if(request.status == 400 || request.status == 401 || request.status == 403)
							messageField.innerHTML = "Erreur: " + stripHTML(request.responseText);
						else if(request.status == 200)
						{
							messageField.innerHTML = "Commentaire soumis avec succès";
							vetHabitatGenerateSelectOptions();
						}
						else
							messageField.innerHTML = "Erreur inconnue (" + request.status + ")";
					}
				};

				request.open("POST", target);
				request.setRequestHeader("Auth-Token", token);

				request.send(data);
			});
		}

		vetHabitatSetupForm();
	}
	else
	{
		console.error("Form element doesn't exist!");
		alert("Une erreur est survenue; connexion impossible...");
	}
</script>