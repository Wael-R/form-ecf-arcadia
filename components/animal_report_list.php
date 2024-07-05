<script src="/datepicker.js"></script>

<br><br>

<h5 class="fw-bold">Avis passés du vétérinaire</h5>

<br>

<div class="card editor-card editor-scroll d-none" id="animalReports">
	<div class="mb-2">
		<label class="form-label" for="animalFilterSelect">Animal</label>
		<select class="form-control" id="animalFilterSelect">
		</select>
	</div>

	<div class="form-check mb-3">
		<input class="form-check-input" type="checkbox" id="animalFilterSelected" checked>
		<label class="form-check-label" for="animalFilterSelected">Animal selectionné uniquement</label>
	</div>

	<div class="row mb-2 align-items-center">
		<label class="col-4 col-md-2 form-label" for="animalFilterFromButton">Date de début : </label>
		<div class="col-9 col-md-4">
			<?php $datePickerPrefix = "animalFilterFrom"; include("date_picker.php"); ?>
		</div>
	</div>

	<div class="row mb-3 align-items-center">
		<label class="col-4 col-md-2 form-label" for="animalFilterToButton">Date de fin : </label>
		<div class="col-9 col-md-4">
			<?php $datePickerPrefix = "animalFilterTo"; include("date_picker.php"); ?>
		</div>
	</div>

	<div id="animalReportContainer">
	</div>
</div>

<script>
	const reportsDiv = document.getElementById("animalReports");
	const reportContainer = document.getElementById("animalReportContainer");
	const reportFilterSelect = document.getElementById("animalFilterSelect");
	const reportFilterSelected = document.getElementById("animalFilterSelected");
	const reportFilterFrom = animalFilterFromDateProps;
	const reportFilterTo = animalFilterToDateProps;
	const reportFilterReset = document.getElementById("animalFilterReset");
	const reports = [];
	const reportAnimals = [];

	const reportTimeFormat = {
		year: "numeric",
		month: "long",
		day: "numeric",
		hour: "numeric",
		minute: "numeric"
	};

	let reportAnimal = null;

	const reportFrom = new Date();
	reportFrom.setDate(reportFrom.getDate() - 7);

	let reportFromDate = reportFrom;
	let reportToDate = new Date();

	reportFilterFrom.onChange = (from, to) => {
		reportFilterFrom.button.innerHTML = to.toLocaleString("fr", reportTimeFormat);
		reportFromDate = to;
		setDateMinimum(reportFilterTo, to);
		displayReports();
	};

	reportFilterTo.onChange = (from, to) => {
		reportFilterTo.button.innerHTML = to.toLocaleString("fr", reportTimeFormat);
		reportToDate = to;
		setDateMaximum(reportFilterFrom, to);
		displayReports();
	};

	setupDatePicker(reportFilterFrom);
	setupDatePicker(reportFilterTo);

	setDate(reportFilterFrom, reportFromDate);
	setDate(reportFilterTo, reportToDate);

	reportFilterSelected.addEventListener("change", (evt) => { displayReports(); });
	reportFilterSelect.addEventListener("change", (evt) => { displayAnimalReports(reportAnimals[reportFilterSelect.value]); });

	function setupAnimalReports(animals)
	{
		if(!animals || animals.length <= 0)
			reportsDiv.classList.add("d-none");
		else
		{
			reportsDiv.classList.remove("d-none");
			reports.length = 0;
			reportAnimals.length = 0;
			reportFilterSelect.options.length = 0;

			animals.forEach((animal, idx) => {
				reportAnimals.push(animal);

				let option = document.createElement("option");
				option.value = idx;
				option.innerHTML = stripHTML(animal.title);

				reportFilterSelect.options.length++;
				reportFilterSelect.options[reportFilterSelect.options.length - 1] = option;

				animal.reports.forEach((report) => {
					let dupe = {...report};
					dupe.animal = animal;

					reports.push(dupe);
				});
			});

			reports.sort((a, b) => new Date(b.date) - new Date(a.date));

			displayAnimalReports(reportAnimals[0]);
		}
	}

	function displayAnimalReports(animal)
	{
		setDate(reportFilterFrom, reportFrom);
		setDate(reportFilterTo, new Date());

		reportAnimal = animal;

		displayReports();
	}

	function displayReports()
	{
		let dateNow = new Date();
		let dateFrom = getDate(reportFilterFrom);
		let dateTo = getDate(reportFilterTo);

		reportContainer.innerHTML = "";

		let first = true;

		reports.forEach((report) => {
			if(reportFilterSelected.checked && reportAnimal && report.animal != reportAnimal)
				return;

			let reportDate = new Date(report.date);

			if(dateFrom && reportDate < dateFrom || dateTo && reportDate > dateTo)
				return;

			let div = document.createElement("div");
			div.classList.add("form-control");

			if(first)
				first = false;
			else
				div.classList.add("mt-3");

			let date = document.createElement("p");
			date.innerHTML = "<i>Pour <b>" + stripHTML(report.animal.title) + "</b>, passé le " + reportDate.toLocaleString("fr", reportTimeFormat) + "</i>";

			div.appendChild(date);

			let food = document.createElement("p");
			food.innerHTML = "<b>Nourriture proposée:</b> " + stripHTML(report.food);

			div.appendChild(food);

			let amount = document.createElement("p");
			amount.innerHTML = "<b>Quantité proposée:</b> " + stripHTML(report.amount);

			div.appendChild(amount);

			if(report.comment)
			{
				let comment = document.createElement("p");
				comment.innerHTML = "<b>Détails:</b><br>" + stripHTML(report.comment).replace(/\n/g, "<br>");

				div.appendChild(comment);
			}

			reportContainer.appendChild(div);
		});

		if(first)
			reportContainer.innerHTML = "Aucun résultats";
	}
</script>