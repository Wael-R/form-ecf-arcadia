<?php
require_once("./server/auth.php");

updateCSRFToken();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = "Contact"; include("./components/head.php"); ?>
</head>
<body>
	<script src="/utility.js"></script>
	<div class="main-container">
		<?php include("./components/navbar.php"); ?>

		<br><br>

		<div class="main-row px-2 px-sm-5">
			<h2 class="text-success fw-bold">Contact</h2>
			<br>

			<div class="card p-3" id="reviewContainer">
				<form id="contactForm" action="javascript:void(0);">
					<div class="mb-3">
						<label for="contactEmail" class="form-label">Addresse e-mail</label>
						<input type="email" class="form-control" id="contactEmail" required>
					</div>

					<div class="mb-3">
						<label for="contactTitle" class="form-label">Sujet</label>
						<input type="text" class="form-control" id="contactTitle" required>
					</div>

					<div class="mb-3">
						<label for="contactContent" class="form-label">Message</label>
						<textarea class="form-control" id="contactContent" rows="6" required></textarea>
					</div>

					<div class="mb-3">
						<p class="login-message" id="contactMessage"></p>
					</div>

					<button type="submit" class="btn btn-success" id="contactSubmit">Envoyer</button>
				</form>
			</div>
		</div>
		</div>

	<script>
		const contactForm = document.getElementById("contactForm");
		const contactSubmit = document.getElementById("contactSubmit");

		const contactEmail = document.getElementById("contactEmail");
		const contactTitle = document.getElementById("contactTitle");
		const contactContent = document.getElementById("contactContent");
		const contactMessage = document.getElementById("contactMessage");

		contactForm.addEventListener("submit", (evt) => {
			const target = "contactSubmit.php";

			let request = new XMLHttpRequest();

			let data = new FormData();

			data.append("email", contactEmail.value);
			data.append("title", contactTitle.value);
			data.append("message", contactContent.value);

			contactSubmit.setAttribute("disabled", "");

			request.onreadystatechange = (ev) => {
				if(request.readyState == 4)
				{
					if(request.status == 400 || request.status == 401 || request.status == 403)
					{
						contactMessage.innerHTML = "Erreur: " + stripHTML(request.responseText);
						contactSubmit.removeAttribute("disabled");
					}
					else if(request.status == 200)
						contactMessage.innerHTML = "Message envoyé avec succès";
					else
					{
						contactMessage.innerHTML = "Erreur inconnue (" + request.status + ")";
						contactSubmit.removeAttribute("disabled");
					}
				}
			};

			request.open("POST", target);
			request.setRequestHeader("Auth-Token", "<?= getCSRFToken() ?>");

			request.send(data);
		});
	</script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>