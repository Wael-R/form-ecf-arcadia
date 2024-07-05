<br><br>

<h5 class="fw-bold">Commentaires du vétérinaire</h5>

<br>

<div class="card editor-card editor-scroll d-none" id="habitatComments">
	<div class="mb-2">
		<label class="form-label" for="habitatFilterSelect">Habitat</label>
		<select class="form-control" id="habitatFilterSelect">
		</select>
	</div>

	<div class="form-check mb-2">
		<input class="form-check-input" type="checkbox" id="habitatFilterSelected" checked>
		<label class="form-check-label" for="habitatFilterSelected">Habitat selectionné uniquement</label>
	</div>

	<div id="habitatCommentContainer">
	</div>
</div>

<script>
	const commentsDiv = document.getElementById("habitatComments");
	const commentContainer = document.getElementById("habitatCommentContainer");
	const commentFilterSelect = document.getElementById("habitatFilterSelect");
	const commentFilterSelected = document.getElementById("habitatFilterSelected");
	const comments = [];
	const commentHabitats = [];

	let commentHabitat = null;

	commentFilterSelected.addEventListener("change", (evt) => { displayComments(); });
	commentFilterSelect.addEventListener("change", (evt) => { displayHabitatComments(commentHabitats[commentFilterSelect.value]); });

	function setupHabitatComments(habitats)
	{
		if(!habitats || habitats.length <= 0)
			commentsDiv.classList.add("d-none");
		else
		{
			commentsDiv.classList.remove("d-none");
			comments.length = 0;
			commentHabitats.length = 0;
			commentFilterSelect.options.length = 0;

			habitats.forEach((habitat, idx) => {
				commentHabitats.push(habitat);

				let option = document.createElement("option");
				option.value = idx;
				option.innerHTML = stripHTML(habitat.title);

				commentFilterSelect.options.length++;
				commentFilterSelect.options[commentFilterSelect.options.length - 1] = option;

				habitat.comments.forEach((comment) => {
					let dupe = {...comment};
					dupe.habitat = habitat;

					comments.push(dupe);
				});
			});

			comments.sort((a, b) => new Date(b.date) - new Date(a.date));

			displayHabitatComments(commentHabitats[0]);
		}
	}

	function displayHabitatComments(habitat)
	{
		commentHabitat = habitat;

		displayComments();
	}

	function displayComments()
	{
		commentContainer.innerHTML = "";

		let first = true;

		comments.forEach((comment) => {
			if(commentFilterSelected.checked && commentHabitat && comment.habitat != commentHabitat)
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
			date.innerHTML = "<i>Pour <b>" + stripHTML(comment.habitat.title) + "</b>, soumis le " + new Date(comment.date).toLocaleString("fr", format) + "</i>";

			div.appendChild(date);

			let content = document.createElement("p");
			content.innerHTML = "<b>Avis:</b><br>" + stripHTML(comment.comment).replace(/\n/g, "<br>");

			div.appendChild(content);

			commentContainer.appendChild(div);
		});

		if(first)
			commentContainer.innerHTML = "Aucun résultats";
	}
</script>