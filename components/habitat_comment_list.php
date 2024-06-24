<div class="card editor-card editor-scroll mt-3 d-none" id="habitatComments">
	<label class="mb-2">Commentaires du vétérinaire</label>
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
	const commentFilterSelect = document.getElementById("habitatFilterSelected");
	const comments = [];

	let commentHabitat = null;

	commentFilterSelect.addEventListener("change", (evt) => { displayComments(); });

	function setupHabitatComments(habitats)
	{
		if(!habitats || habitats.length <= 0)
			commentsDiv.classList.add("d-none");
		else
		{
			commentsDiv.classList.remove("d-none");
			comments.length = 0;

			habitats.forEach((habitat) => {
				habitat.comments.forEach((comment) => {
					let dupe = {...comment};
					dupe.habitat = habitat;

					comments.push(dupe);
				});
			});

			comments.sort((a, b) => new Date(b.date) - new Date(a.date));
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

		commentFilterSelect.disabled = !commentHabitat;

		let first = true;

		comments.forEach((comment) => {
			if(commentFilterSelect.checked && commentHabitat && comment.habitat != commentHabitat)
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
			date.innerHTML = "<i>Pour <b>" + stripHTML(comment.habitat.title) + "</b>, passé le " + new Date(comment.date).toLocaleString("fr", format) + "</i>";

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