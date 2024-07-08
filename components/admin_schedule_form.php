<?php
require_once("../vendor/autoload.php");
$mongo = new MongoDB\Client(getMongoQueryString());

$schedule = $mongo->arcadia->schedule->findOne(["id" => 0]);

$fromTime = 10;
$toTime = 18;
$days = 0;

if(!$schedule)
	$mongo->arcadia->schedule->insertOne(["id" => 0, "from" => $fromTime, "to" => $toTime, "days" => $days]);
else
{
	$fromTime = $schedule["from"];
	$toTime = $schedule["to"];
	$days = $schedule["days"];
}
?>
<a class="anchor" id="scheduleEditor"></a>
<h5 class="fw-bold">Horaires d'ouverture</h5>

<br>

<div class="card editor-card">
	<form id="scheduleForm" action="javascript:void(0);">
		<div class="mb-3">
			<label for="openingHours" class="form-label">Heure d'ouverture</label>
			<select class="form-control" id="openingHours">
				<?php for($i = 6; $i <= 22; $i++): ?>
				<option value="<?= $i ?>" <?= $i == $fromTime ? "selected" : ""?>><?= $i . " h" ?></option>
				<?php endfor; ?>
			</select>
		</div>

		<div class="mb-3">
			<label for="pass" class="form-label">Heure de fermeture</label>
			<select class="form-control" id="closingHours">
				<?php for($i = 6; $i <= 23; $i++): ?>
				<option value="<?= $i ?>" <?= $i == $toTime ? "selected" : ""?>><?= $i . " h" ?></option>
				<?php endfor; ?>
			</select>
		</div>

		<div class="mb-3">
			<label for="pass" class="form-label">Jours d'ouverture</label>
			<select class="form-control" id="openingDays">
				<option value="0" <?= $days == 0 ? "selected" : ""?>>Toute la semaine</option>
				<option value="1" <?= $days == 1 ? "selected" : ""?>>Lundi au samedi</option>
				<option value="2" <?= $days == 2 ? "selected" : ""?>>Lundi au vendredi</option>
			</select>
		</div>

		<div class="mb-3">
			<p class="login-message" id="scheduleMessage"></p>
		</div>

		<div class="col-auto fit-md">
			<button type="submit" class="btn btn-success w-100">Modifier les horaires</button>
		</div>
	</form>
</div>

<script>
	const schedForm = document.getElementById("scheduleForm");
	const schedFromField = document.getElementById("openingHours");
	const schedToField = document.getElementById("closingHours");
	const schedDaysField = document.getElementById("openingDays");

	schedFromField.addEventListener("change", (evt) => {
		for(const option of schedToField.options)
		{
			if(parseInt(option.value) <= parseInt(schedFromField.value))
			{
				if(option.selected)
				{
					schedToField.value = parseInt(schedFromField.value) + 1;
					option.selected = false;
				}

				option.disabled = true;
			}
			else
				option.disabled = false;
		}
	});

	schedFromField.dispatchEvent(new Event("change"));

	if(schedForm)
	{
		schedForm.addEventListener("submit", (evt) =>
		{
			const target = "scheduleUpdate.php";

			const token = "<?= getCSRFToken() ?>";

			let scheduleMessage = document.getElementById("scheduleMessage");

			let data = new FormData();

			data.append("from", schedFromField.value);
			data.append("to", schedToField.value);
			data.append("days", schedDaysField.value);

			let request = new XMLHttpRequest();

			request.onreadystatechange = (ev) => {
				if(request.readyState == 4)
				{
					if(request.status == 400 || request.status == 401 || request.status == 403)
						scheduleMessage.innerHTML = "Erreur: " + stripHTML(request.responseText);
					else if(request.status == 200)
						scheduleMessage.innerHTML = "Horaires modifiées avec succès";
					else
						scheduleMessage.innerHTML = "Erreur inconnue (" + request.status + ")";
				}
			};

			request.open("POST", target);
			request.setRequestHeader("Auth-Token", token);

			request.send(data);
		});
	}
	else
	{
		console.error("Form element doesn't exist!");
		alert("Une erreur est survenue; connexion impossible...");
	}
</script>