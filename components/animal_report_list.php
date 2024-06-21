<div class="card editor-card editor-scroll" id="animalReports">
	<label class="mb-2">Avis passés</label>

	<div id="animalReportContainer">
	</div>
</div>

<script>
	function displayAnimalReports(animal)
	{
		const reportsDiv = document.getElementById("animalReports");
		const reportContainer = document.getElementById("animalReportContainer");

		reportContainer.innerHTML = "";

		if(animal.reports.length <= 0)
			reportsDiv.classList.add("d-none");
		else
		{
			let last = null;

			animal.reports.forEach((report) => {
				let div = document.createElement("div");
				div.classList.add("form-control", "mt-3");

				const format = {
					year: "numeric",
					month: "long",
					day: "numeric",
					hour: "numeric",
					minute: "numeric"
				};

				let date = document.createElement("p");
				date.innerHTML = "<i>Passé le " + new Date(report.date).toLocaleString("fr", format) + "</i>";

				div.appendChild(date);

				let food = document.createElement("p");
				food.innerHTML = "<b>Nourriture proposée:</b> " + report.food;

				div.appendChild(food);
				
				let amount = document.createElement("p");
				amount.innerHTML = "<b>Quantité proposée:</b> " + report.amount;

				div.appendChild(amount);

				if(report.comment)
				{
					let comment = document.createElement("p");
					comment.innerHTML = "<b>Détails:</b><br>" + report.comment.replace(/\n/g, "<br>");

					div.appendChild(comment);
				}

				reportContainer.insertBefore(div, last);
				last = div;
			});

			reportContainer.firstChild.classList.remove("mt-3");
		}
	}
</script>