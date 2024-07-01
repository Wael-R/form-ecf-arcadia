<a class="anchor" id="reviewEditor"></a>
<h5 class="fw-bold">Valider les avis</h5>

<br>

<div class="card p-3 reviews-inner large-scroll">
	<p class="login-message" id="reviewMessage"></p>

	<div id="reviewContainer">

	</div>
</div>

<script>
	const reviewMessage = document.getElementById("reviewMessage");
	const reviewContainer = document.getElementById("reviewContainer");

	function validateReview(id, approved)
	{
		const target = "reviewValidate.php";

		let request = new XMLHttpRequest();

		let data = new FormData();

		data.append("id", id);

		if(approved)
			data.append("approved", "1");

		request.onreadystatechange = (ev) => {
			if(request.readyState == 4)
			{
				if(request.status == 400 || request.status == 401 || request.status == 403)
					reviewMessage.innerHTML = "Erreur: " + stripHTML(request.responseText);
				else if(request.status == 200)
				{
					if(approved)
						reviewMessage.innerHTML = "Avis validé avec succès";
					else
						reviewMessage.innerHTML = "Avis supprimé avec succès";

					loadReviews();
				}
				else
					reviewMessage.innerHTML = "Erreur inconnue (" + request.status + ")";
			}
		};

		request.open("POST", target);
		request.setRequestHeader("Auth-Token", "<?= getCSRFToken() ?>");

		request.send(data);
	}

	function loadReviews()
	{
		const target = "reviewList.php";

		let request = new XMLHttpRequest();

		request.onreadystatechange = (ev) => {
			if(request.readyState == 4)
			{
				if(request.status == 400 || request.status == 401 || request.status == 403)
					reviewMessage.innerHTML = "Erreur lors du chargement des avis: " + stripHTML(request.responseText);
				else if(request.status == 200)
				{
					reviewContainer.innerHTML = "";

					let data = JSON.parse(request.responseText);
					let last = null;

					data.forEach(review => {
						let div = document.createElement("div");
						div.classList.add("card", "card-body", "mb-3", "d-flex", "flex-column");

						let name = document.createElement("h5");
						name.classList.add("card-title");
						name.innerHTML = stripHTML(review.name);

						div.appendChild(name);

						let content = document.createElement("p");
						content.classList.add("card-text");
						content.innerHTML = stripHTML(review.content).replace(/\n/g, "<br>");

						div.appendChild(content);

						let subDiv = document.createElement("div");
						subDiv.classList.add("mt-auto", "d-flex", "justify-content-end");

						let approve = document.createElement("button");
						approve.classList.add("btn", "btn-success", "mx-1");
						approve.innerHTML = "Valider";

						approve.addEventListener("click", (evt) => validateReview(review.id, true));

						subDiv.appendChild(approve);

						let deny = document.createElement("button");
						deny.classList.add("btn", "btn-danger");
						deny.innerHTML = "Supprimer";

						deny.addEventListener("click", (evt) => validateReview(review.id, false));

						subDiv.appendChild(deny);

						div.appendChild(subDiv);

						reviewContainer.appendChild(div);

						last = div;
					});

					if(data.length <= 0)
						reviewContainer.innerHTML = "Aucun avis à valider";
					else if(last)
						last.classList.remove("mb-3");
				}
				else
					reviewMessage.innerHTML = "Erreur inconnue lors du chargement des avis (" + request.status + ")";
			}
		};

		request.open("GET", target);

		request.send();
	}

	loadReviews();
</script>