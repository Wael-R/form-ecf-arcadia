<a class="anchor" id="animalEditor"></a>
<h5 class="fw-bold">Avis sur les animaux</h5>

<br>

<div class="card editor-card mb-3">
	<form id="animalForm" action="javascript:void(0);" autocomplete="off">
		<div class="mb-3">
			<label for="animalSelect" class="form-label">Animal</label>
			<select class="form-control" id="animalSelect">
			</select>
		</div>

		<div class="mb-3">
			<label for="animalHealth" class="form-label">État de l'animal</label>
			<input type="text" class="form-control" id="animalHealth">
		</div>

		<div class="mb-3">
			<label for="animalFood" class="form-label">Nourriture proposée</label>
			<input type="text" class="form-control" id="animalFood">
		</div>

		<div class="mb-3">
			<label for="animalFoodAmount" class="form-label">Quantité de nourriture proposée</label>
			<input type="text" class="form-control" id="animalFoodAmount">
		</div>

		<div class="mb-3">
			<label for="animalComment" class="form-label">Détails</label>
			<textarea class="form-control" id="animalComment" rows="3"></textarea>
		</div>

		<div class="mb-3">
			<label for="animalDate" class="form-label">Date de passage</label>
			<input type="datetime-local" class="form-control" id="animalDate">
		</div>

		<div class="mb-3">
			<p class="login-message" id="animalMessage"></p>
		</div>

		<button type="submit" class="btn btn-success" id="animalButton" disabled>Soumettre</button>
	</form>
</div>

<?php include("animal_report_list.php"); ?>

<script>
	const vetForm = document.getElementById("animalForm");
	const vetData = [];

	if(vetForm)
	{
		function getCurrentDate()
		{
			let date = new Date();
			date.setSeconds(0, 0);
			return date.toISOString().split(".")[0]; // toISOString returns milliseconds which dont fit the datetime-local format
		}

		function vetGenerateSelectOptions()
		{
			const messageField = document.getElementById("animalMessage");
			const select = document.getElementById("animalSelect");
			const submit = document.getElementById("animalButton");

			const target = "animalList.php";

			submit.setAttribute("disabled", "");
			select.setAttribute("disabled", "");

			let request = new XMLHttpRequest();

			request.onreadystatechange = (ev) => {
				if(request.readyState == 4)
				{
					if(request.status == 400 || request.status == 401 || request.status == 403)
						messageField.innerHTML = "Erreur lors du chargement des animaux: " + request.responseText;
					else if(request.status == 200)
					{
						vetData.length = 0;

						let old = select.value;
						select.options.length = 0;

						const data = JSON.parse(request.responseText);

						data.forEach((animal) => {
							vetData.push(animal);

							let option = document.createElement("option");

							option.setAttribute("value", vetData.length - 1);
							option.innerHTML = animal.title;

							select.options.length++;
							select.options[select.options.length - 1] = option;
						});

						if(vetData.length > 0)
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

		function vetSetupForm()
		{
			const messageField = document.getElementById("animalMessage");
			const select = document.getElementById("animalSelect");
			const submit = document.getElementById("animalButton");
			
			const healthField = document.getElementById("animalHealth");
			const foodField = document.getElementById("animalFood");
			const amountField = document.getElementById("animalFoodAmount");
			const commentField = document.getElementById("animalComment");
			const dateField = document.getElementById("animalDate");

			dateField.value = getCurrentDate();

			vetGenerateSelectOptions();

			select.addEventListener("change", (evt) =>
			{
				messageField.innerHTML = "";

				let index = select.value;
				let anim = vetData[index];

				if(anim)
				{
					healthField.value = anim.health;
					foodField.value = anim.reports[0]?.food ?? "Nourriture...";
					amountField.value = anim.reports[0]?.amount ?? "Quantité...";
					commentField.value = anim.reports[0]?.comment ?? "";
					dateField.value = anim.reports[0]?.date ?? getCurrentDate();

					displayAnimalReports(anim);
				}
			});

			vetForm.addEventListener("submit", (evt) =>
			{
				const target = "animalReport.php";

				const token = "<?= getCSRFToken() ?>";

				let health = healthField.value;
				let food = foodField.value;
				let amount = amountField.value;
				let comment = commentField.value;
				let date = dateField.value;

				let request = new XMLHttpRequest();
				let data = new FormData();

				let animal = vetData[select.value];

				if(!animal)
					return;

				data.append("id", animal.id);
				data.append("health", health);
				data.append("food", food);
				data.append("amount", amount);
				data.append("comment", comment);
				data.append("date", date);

				request.onreadystatechange = (ev) => {
					if(request.readyState == 4)
					{
						if(request.status == 400 || request.status == 401 || request.status == 403)
							messageField.innerHTML = "Erreur: " + request.responseText;
						else if(request.status == 200)
						{
							messageField.innerHTML = "Avis soumis avec succès";
							vetGenerateSelectOptions();
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

		vetSetupForm();
	}
	else
	{
		console.error("Form element doesn't exist!");
		alert("Une erreur est survenue; connexion impossible...");
	}
</script>