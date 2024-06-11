<div class="card login-card">
	<form id="accountForm" action="javascript:void(0);">
		<input type="hidden" id="token" value="<?= $csrfToken ?? "" ?>">

		<div class="mb-3">
			<label for="email" class="form-label">Adresse Email</label>
			<input type="email" class="form-control" id="email">
		</div>

		<div class="mb-3">
			<label for="pass" class="form-label">Mot de passe</label>
			<input type="password" class="form-control" id="pass" autocomplete="new-password">
		</div>

		<div class="mb-3">
			<div class="form-check form-check-inline">
				<input type="radio" name="role" class="form-check-input" id="roleEmployee" value="employee" checked>
				<label for="roleEmployee" class="form-check-label">Employé</label>
			</div>
			<div class="form-check form-check-inline">
				<input type="radio" name="role" class="form-check-input" id="roleVeterinarian" value="veterinarian">
				<label for="roleVeterinarian" class="form-check-label">Vétérinaire</label>
			</div>
		</div>

		<div class="mb-3">
			<p class="login-message" id="loginMessage"></p>
		</div>

		<button type="submit" class="btn btn-success">Créer un compte</button>
	</form>
</div>

<script>
	const form = document.getElementById("accountForm");

	if(form)
	{
		form.addEventListener("submit", (evt) =>
		{
			const target = "accountCreate.php";

			let loginMessage = document.getElementById("loginMessage");

			let token = document.getElementById("token").value;

			let emailField = document.getElementById("email");
			let email = emailField.value;

			let passField = document.getElementById("pass");
			let pass = passField.value;

			let roleField = document.getElementById("roleEmployee");
			let role = (roleField.value ? "employee" : "veterinarian");

			let http = new XMLHttpRequest();

			http.onreadystatechange = (ev) => {
				if(http.readyState == 4)
				{
					if(http.status == 400 || http.status == 401 || http.status == 403)
						loginMessage.innerHTML = "Erreur: " + http.responseText; // todo? strip html tags from here
					else if(http.status == 200)
					{
						loginMessage.innerHTML = "Compte crée avec succès";
						emailField.value = "";
						passField.value = "";
						roleField.value = "";
					}
					else
						loginMessage.innerHTML = "Erreur inconnue (" + http.status + ")";
				}
			};

			http.open("POST", target);
			http.setRequestHeader("Auth-Token", token);
			http.setRequestHeader("Acc-Username", btoa(email));
			http.setRequestHeader("Acc-Password", btoa(pass));
			http.setRequestHeader("Acc-Role", role);

			http.send();
		});
	}
	else
	{
		console.error("Form element doesn't exist!");
		alert("Une erreur est survenue; connexion impossible...");
	}
</script>