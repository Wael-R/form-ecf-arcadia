<div class="card editor-card editor-scroll mt-3 d-none" id="animalFoodReports">
	<label class="mb-2">Consommation de l'animal</label>
	<div class="btn-group btn-group-sm row align-items-center mb-2 px-3" role="group" aria-label="Filtrer les avis du vétérinaire">
		<label class="col-auto btn btn-success" for="animalFoodFilterFrom">Filtrer du</label>
		<input class="col-auto btn btn-success" type="datetime-local" id="animalFoodFilterFrom">

		<label class="col-auto btn btn-success" for="animalFoodFilterTo">au</label>
		<input class="col-auto btn btn-success" type="datetime-local" id="animalFoodFilterTo">
		
		<button class="col-auto btn btn-outline-success" type="button" id="animalFoodFilterReset">X</button>
	</div>

	<div class="form-check mb-2">
		<input class="form-check-input" type="checkbox" id="animalFoodFilterSelected" checked>
		<label class="form-check-label" for="animalFoodFilterSelected">Animal selectionné uniquement</label>
	</div>

	<div id="animalFoodReportContainer">
	</div>
</div>

<script>
	const foodReportsDiv = document.getElementById("animalFoodReports");
	const foodReportContainer = document.getElementById("animalFoodReportContainer");
	const foodReportFilterFrom = document.getElementById("animalFoodFilterFrom");
	const foodReportFilterTo = document.getElementById("animalFoodFilterTo");
	const foodReportFilterReset = document.getElementById("animalFoodFilterReset");
	const foodReportFilterSelect = document.getElementById("animalFoodFilterSelected");
	const foodReports = [];

	let foodReportAnimal = null;

	foodReportFilterFrom.addEventListener("change", (evt) => { displayFoodReports(); });
	foodReportFilterTo.addEventListener("change", (evt) => { displayFoodReports(); });
	foodReportFilterSelect.addEventListener("change", (evt) => { displayFoodReports(); });
	foodReportFilterReset.addEventListener("click", (evt) =>
	{
		foodReportFilterFrom.value = "";
		foodReportFilterTo.value = "";
		displayFoodReports();
	});

	function setupAnimalFoodReports(animals)
	{
		if(!animals || animals.length <= 0)
			foodReportsDiv.classList.add("d-none");
		else
		{
			foodReportsDiv.classList.remove("d-none");
			foodReports.length = 0;

			animals.forEach((animal) => {
				animal.food.forEach((foodReport) => {
					let dupe = {...foodReport};
					dupe.animal = animal;

					foodReports.push(dupe);
				});
			});

			foodReports.sort((a, b) => new Date(b.date) - new Date(a.date));
		}
	}

	function displayAnimalFoodReports(animal)
	{
		foodReportFilterFrom.value = "";
		foodReportFilterTo.value = "";
		foodReportFilterReset.classList.add("d-none");

		foodReportAnimal = animal;

		displayFoodReports();
	}

	function displayFoodReports()
	{
		let dateNow = new Date();
		let dateFrom = new Date(foodReportFilterFrom.value);
		let dateTo = new Date(foodReportFilterTo.value);

		foodReportFilterReset.classList.remove("d-none");

		if(!dateFrom.getTime())
		{
			dateFrom = new Date(0);
			if(!dateTo.getTime())
			{
				foodReportFilterReset.classList.add("d-none");
				dateTo = dateNow;
			}
		}
		else
		{
			if(!dateTo.getTime())
			{
				dateTo = dateNow;
			}
			else if(dateTo.getTime() < dateFrom.getTime())
			{
				let to = dateTo;
				dateTo = dateFrom;
				dateFrom = dateTo;

				let to2 = foodReportFilterTo.value;
				foodReportFilterTo.value = foodReportFilterFrom.value;
				foodReportFilterFrom.value = to2;
			}
			else if(dateTo.getTime() == dateFrom.getTime())
			{
				dateFrom = null;
				dateTo = null;
			}
		}

		foodReportFilterTo.min = getDateString(dateFrom);
		foodReportFilterTo.max = getDateString(dateNow);

		foodReportFilterFrom.min = getDateString(new Date(0));
		foodReportFilterFrom.max = getDateString(dateTo);

		foodReportContainer.innerHTML = "";

		foodReportFilterSelect.disabled = !foodReportAnimal;

		let first = true;

		foodReports.forEach((foodReport) => {
			if(foodReportFilterSelect.checked && foodReportAnimal && foodReport.animal != foodReportAnimal)
				return;

			let foodReportDate = new Date(foodReport.date);

			if(dateFrom && foodReportDate < dateFrom || dateTo && foodReportDate > dateTo)
				return;

			let div = document.createElement("div");
			div.classList.add("form-control");

			if(first)
				first = false;
			else
				div.classList.add("mt-3");

			const format = {
				year: "numeric",
				month: "long",
				day: "numeric",
				hour: "numeric",
				minute: "numeric"
			};

			let date = document.createElement("p");
			date.innerHTML = "<i>Pour <b>" + stripHTML(foodReport.animal.title) + "</b>, nourri le " + foodReportDate.toLocaleString("fr", format) + "</i>";

			div.appendChild(date);

			let food = document.createElement("p");
			food.innerHTML = "<b>Nourriture:</b> " + stripHTML(foodReport.food);

			div.appendChild(food);

			let amount = document.createElement("p");
			amount.innerHTML = "<b>Quantité:</b> " + stripHTML(foodReport.amount);

			div.appendChild(amount);

			foodReportContainer.appendChild(div);
		});

		if(first)
			foodReportContainer.innerHTML = "Aucun résultats";
	}
</script>