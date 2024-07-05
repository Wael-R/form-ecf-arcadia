<a class="anchor" id="animalEditor"></a>
<h5 class="fw-bold">Consommation des animaux</h5>

<br>

<div class="card editor-card">
	<form id="animalFoodForm" action="javascript:void(0);" autocomplete="off">
		<div class="mb-3">
			<label for="animalSelect" class="form-label">Animal</label>
			<select class="form-control" id="animalSelect">
			</select>
		</div>

		<div class="mb-3">
			<label for="animalFood" class="form-label">Nourriture</label>
			<input type="text" class="form-control" id="animalFood">
		</div>

		<div class="mb-3">
			<label for="animalFoodAmount" class="form-label">Quantité de nourriture</label>
			<input type="text" class="form-control" id="animalFoodAmount">
		</div>

		<div class="mb-3">
			<label for="animalDate" class="form-label">Date de passage</label>
			<input type="datetime-local" class="form-control" id="animalDate">
		</div>

		<div class="mb-3">
			<p class="login-message" id="animalMessage"></p>
		</div>

		<button type="submit" class="btn btn-success" id="animalFoodButton" disabled>Ajouter</button>
	</form>
</div>

<?php include("animal_food_list.php"); ?>

<script>
	const animalFoodForm = document.getElementById("animalFoodForm");
	const animalFoodData = [];

	if(animalFoodForm)
	{
		function empGenerateSelectOptions()
		{
			const messageField = document.getElementById("animalMessage");
			const select = document.getElementById("animalSelect");
			const submit = document.getElementById("animalFoodButton");

			const target = "animalList.php";

			submit.setAttribute("disabled", "");
			select.setAttribute("disabled", "");

			let request = new XMLHttpRequest();

			request.onreadystatechange = (ev) => {
				if(request.readyState == 4)
				{
					if(request.status == 400 || request.status == 401 || request.status == 403)
						messageField.innerHTML = "Erreur lors du chargement des animaux: " + stripHTML(request.responseText);
					else if(request.status == 200)
					{
						animalFoodData.length = 0;

						let old = select.value;
						select.options.length = 0;

						const data = JSON.parse(request.responseText);

						data.forEach((animal) => {
							animalFoodData.push(animal);

							let option = document.createElement("option");

							option.setAttribute("value", animalFoodData.length - 1);
							option.innerHTML = animal.title;

							select.options.length++;
							select.options[select.options.length - 1] = option;
						});

						setupAnimalFoodReports(animalFoodData);

						if(animalFoodData.length > 0)
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

		function empSetupForm()
		{
			const messageField = document.getElementById("animalMessage");
			const select = document.getElementById("animalSelect");
			const submit = document.getElementById("animalFoodButton");

			const foodField = document.getElementById("animalFood");
			const amountField = document.getElementById("animalFoodAmount");
			const dateField = document.getElementById("animalDate");

			empGenerateSelectOptions();

			select.addEventListener("change", (evt) =>
			{
				messageField.innerHTML = "";

				let index = select.value;
				let anim = animalFoodData[index];

				if(anim)
				{
					foodField.value = "Nourriture...";
					amountField.value = "Quantité...";
					dateField.value = getDateString(new Date());
					dateField.max = dateField.value;
				}
			});

			animalFoodForm.addEventListener("submit", (evt) =>
			{
				const target = "animalFoodReport.php";

				const token = "<?= getCSRFToken() ?>";

				let food = foodField.value;
				let amount = amountField.value;
				let date = dateField.value;

				let request = new XMLHttpRequest();
				let data = new FormData();

				let animal = animalFoodData[select.value];

				if(!animal)
					return;

				data.append("id", animal.id);
				data.append("food", food);
				data.append("amount", amount);
				data.append("date", date);

				request.onreadystatechange = (ev) => {
					if(request.readyState == 4)
					{
						if(request.status == 400 || request.status == 401 || request.status == 403)
							messageField.innerHTML = "Erreur: " + stripHTML(request.responseText);
						else if(request.status == 200)
						{
							messageField.innerHTML = "Avis soumis avec succès";
							empGenerateSelectOptions();
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

		empSetupForm();
	}
	else
	{
		console.error("Form element doesn't exist!");
		alert("Une erreur est survenue; connexion impossible...");
	}
</script>