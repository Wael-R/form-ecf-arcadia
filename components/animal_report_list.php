<div class="card editor-card editor-scroll mt-3 d-none" id="animalReports">
	<label class="mb-2">Avis passés du vétérinaire</label>
	<div class="btn-group btn-group-sm row align-items-center mb-2 px-3" role="group" aria-label="Filtrer les avis du vétérinaire">
		<label class="col-auto btn btn-success" for="animalFilterFrom">Filtrer du</label>
		<input class="col-auto btn btn-success" type="datetime-local" id="animalFilterFrom">

		<label class="col-auto btn btn-success" for="animalFilterTo">au</label>
		<input class="col-auto btn btn-success" type="datetime-local" id="animalFilterTo">
		
		<button class="col-auto btn btn-outline-success" type="button" id="animalFilterReset">X</button>
	</div>

	<div class="form-check mb-2">
		<input class="form-check-input" type="checkbox" id="animalFilterSelected" checked>
		<label class="form-check-label" for="animalFilterSelected">Animal selectionné uniquement</label>
	</div>

	<div id="animalReportContainer">
	</div>
</div>

<script>
	const reportsDiv = document.getElementById("animalReports");
	const reportContainer = document.getElementById("animalReportContainer");
	const reportFilterFrom = document.getElementById("animalFilterFrom");
	const reportFilterTo = document.getElementById("animalFilterTo");
	const reportFilterReset = document.getElementById("animalFilterReset");
	const reportFilterSelect = document.getElementById("animalFilterSelected");
	const reports = [];

	let reportAnimal = null;

	reportFilterFrom.addEventListener("change", (evt) => { displayReports(); });
	reportFilterTo.addEventListener("change", (evt) => { displayReports(); });
	reportFilterSelect.addEventListener("change", (evt) => { displayReports(); });
	reportFilterReset.addEventListener("click", (evt) =>
	{
		reportFilterFrom.value = "";
		reportFilterTo.value = "";
		displayReports();
	});

	function getDateString(date)
	{
		if(!date || !date.getTime())
			return "";

		let date2 = new Date(date);
		date2.setSeconds(0, 0);
		date2.setMinutes(date2.getMinutes() - date2.getTimezoneOffset());
		return date2.toISOString().split(".")[0]; // toISOString returns milliseconds which dont fit the datetime-local format
	}

	function setupAnimalReports(animals)
	{
		if(!animals || animals.length <= 0)
			reportsDiv.classList.add("d-none");
		else
		{
			reportsDiv.classList.remove("d-none");
			reports.length = 0;

			animals.forEach((animal) => {
				animal.reports.forEach((report) => {
					let dupe = {...report};
					dupe.animal = animal;

					reports.push(dupe);
				});
			});

			reports.sort((a, b) => new Date(b.date) - new Date(a.date));
		}
	}

	function displayAnimalReports(animal)
	{
		reportFilterFrom.value = "";
		reportFilterTo.value = "";
		reportFilterReset.classList.add("d-none");

		reportAnimal = animal;

		displayReports();
	}

	function displayReports()
	{
		let dateNow = new Date();
		let dateFrom = new Date(reportFilterFrom.value);
		let dateTo = new Date(reportFilterTo.value);

		reportFilterReset.classList.remove("d-none");

		if(!dateFrom.getTime())
		{
			dateFrom = new Date(0);
			if(!dateTo.getTime())
			{
				reportFilterReset.classList.add("d-none");
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

				let to2 = reportFilterTo.value;
				reportFilterTo.value = reportFilterFrom.value;
				reportFilterFrom.value = to2;
			}
			else if(dateTo.getTime() == dateFrom.getTime())
			{
				dateFrom = null;
				dateTo = null;
			}
		}

		reportFilterTo.min = getDateString(dateFrom);
		reportFilterTo.max = getDateString(dateNow);

		reportFilterFrom.min = getDateString(new Date(0));
		reportFilterFrom.max = getDateString(dateTo);

		reportContainer.innerHTML = "";

		reportFilterSelect.disabled = !reportAnimal;

		let first = true;

		reports.forEach((report) => {
			if(reportFilterSelect.checked && reportAnimal && report.animal != reportAnimal)
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

			const format = {
				year: "numeric",
				month: "long",
				day: "numeric",
				hour: "numeric",
				minute: "numeric"
			};

			let date = document.createElement("p");
			date.innerHTML = "<i>Pour <b>" + stripHTML(report.animal.title) + "</b>, passé le " + reportDate.toLocaleString("fr", format) + "</i>";

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