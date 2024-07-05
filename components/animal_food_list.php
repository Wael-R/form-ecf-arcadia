<script src="/datepicker.js"></script>

<br><br>

<h5 class="fw-bold">Consommation des animaux</h5>

<br>

<div class="card editor-card editor-scroll d-none" id="animalFoodReports">
	<div class="mb-2">
		<label class="form-label" for="animalFoodFilterSelect">Animal</label>
		<select class="form-control" id="animalFoodFilterSelect">
		</select>
	</div>

	<div class="form-check mb-2">
		<input class="form-check-input" type="checkbox" id="animalFoodFilterSelected" checked>
		<label class="form-check-label" for="animalFoodFilterSelected">Animal selectionné uniquement</label>
	</div>

	<div class="row mb-2 align-items-center">
		<label class="col-4 col-md-2 form-label" for="animalFoodFilterFromButton">Date de début : </label>
		<div class="col-9 col-md-4">
			<?php $datePickerPrefix = "animalFoodFilterFrom"; include("date_picker.php"); ?>
		</div>
	</div>

	<div class="row mb-2 align-items-center">
		<label class="col-4 col-md-2 form-label" for="animalFoodFilterToButton">Date de fin : </label>
		<div class="col-9 col-md-4">
			<?php $datePickerPrefix = "animalFoodFilterTo"; include("date_picker.php"); ?>
		</div>
	</div>

	<div id="animalFoodReportContainer">
	</div>
</div>

<script>
	const foodReportsDiv = document.getElementById("animalFoodReports");
	const foodReportContainer = document.getElementById("animalFoodReportContainer");
	const foodReportFilterSelect = document.getElementById("animalFoodFilterSelect");
	const foodReportFilterSelected = document.getElementById("animalFoodFilterSelected");
	const foodReportFilterFrom = animalFoodFilterFromDateProps;
	const foodReportFilterTo = animalFoodFilterToDateProps;
	const foodReports = [];
	const foodReportAnimals = [];

	const foodTimeFormat = {
		year: "numeric",
		month: "long",
		day: "numeric",
		hour: "numeric",
		minute: "numeric"
	};

	let foodReportAnimal = null;

	const foodReportFrom = new Date();
	foodReportFrom.setDate(foodReportFrom.getDate() - 7);

	let foodFromDate = foodReportFrom;
	let foodToDate = new Date();

	foodReportFilterFrom.onChange = (from, to) => {
		foodReportFilterFrom.button.innerHTML = to.toLocaleString("fr", foodTimeFormat);
		foodFromDate = to;
		setDateMinimum(foodReportFilterTo, to);
		displayFoodReports();
	};

	foodReportFilterTo.onChange = (from, to) => {
		foodReportFilterTo.button.innerHTML = to.toLocaleString("fr", foodTimeFormat);
		foodToDate = to;
		setDateMaximum(foodReportFilterFrom, to);
		displayFoodReports();
	};

	setupDatePicker(foodReportFilterFrom);
	setupDatePicker(foodReportFilterTo);

	setDate(foodReportFilterFrom, foodFromDate);
	setDate(foodReportFilterTo, foodToDate);

	foodReportFilterSelected.addEventListener("change", (evt) => { displayFoodReports(); });
	foodReportFilterSelect.addEventListener("change", (evt) => { displayAnimalFoodReports(foodReportAnimals[foodReportFilterSelect.value]); });

	function setupAnimalFoodReports(animals)
	{
		if(!animals || animals.length <= 0)
			foodReportsDiv.classList.add("d-none");
		else
		{
			foodReportsDiv.classList.remove("d-none");
			foodReports.length = 0;
			foodReportAnimals.length = 0;
			foodReportFilterSelect.options.length = 0;

			animals.forEach((animal, idx) => {
				foodReportAnimals.push(animal);

				let option = document.createElement("option");
				option.value = idx;
				option.innerHTML = stripHTML(animal.title);

				foodReportFilterSelect.options.length++;
				foodReportFilterSelect.options[foodReportFilterSelect.options.length - 1] = option;

				animal.food.forEach((foodReport) => {
					let dupe = {...foodReport};
					dupe.animal = animal;

					foodReports.push(dupe);
				});
			});

			foodReports.sort((a, b) => new Date(b.date) - new Date(a.date));

			displayAnimalFoodReports(foodReportAnimals[0]);
		}
	}

	function displayAnimalFoodReports(animal)
	{
		setDate(foodReportFilterFrom, foodReportFrom);
		setDate(foodReportFilterTo, new Date());

		foodReportAnimal = animal;

		displayFoodReports();
	}

	function displayFoodReports()
	{
		let dateNow = new Date();
		let dateFrom = getDate(foodReportFilterFrom);
		let dateTo = getDate(foodReportFilterTo);

		foodReportContainer.innerHTML = "";

		let first = true;

		foodReports.forEach((foodReport) => {
			if(foodReportFilterSelected.checked && foodReportAnimal && foodReport.animal != foodReportAnimal)
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

			let date = document.createElement("p");
			date.innerHTML = "<i>Pour <b>" + stripHTML(foodReport.animal.title) + "</b>, nourri le " + foodReportDate.toLocaleString("fr", foodTimeFormat) + "</i>";

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