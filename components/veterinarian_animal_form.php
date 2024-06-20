<a class="anchor" id="animalEditor"></a>
<h5 class="fw-bold">Avis sur les animaux</h5>

<br>

<div class="card editor-card">
	<form id="animalForm" action="javascript:void(0);" autocomplete="off">
		<div class="mb-3">
			<label for="animalSelect" class="form-label">Animal</label>
			<select class="form-control" id="animalSelect">
			</select>
		</div>

		<div class="mb-3">
			<label for="animalStatus" class="form-label">État de l'animal</label>
			<input type="text" class="form-control" id="animalStatus">
		</div>

		<div class="mb-3">
			<label for="animalFood" class="form-label">Nourriture proposée</label>
			<input type="text" class="form-control" id="animalFood">
		</div>

		<div class="mb-3">
			<label for="animalFoodAmount" class="form-label">Quantité de nourriture proposée</label>
			<input type="text" class="form-control" id="animalFoodAmount">
		</div>

		<div class="mb-3">
			<label for="animalComment" class="form-label">Détails</label>
			<textarea class="form-control" id="animalComment" rows="3"></textarea>
		</div>

		<div class="mb-3">
			<label for="animalDate" class="form-label">Date de passage</label>
			<input type="datetime-local" class="form-control" id="animalDate">
		</div>

		<div class="mb-3">
			<p class="login-message" id="animalMessage"></p>
		</div>

		<button type="submit" class="btn btn-success" id="animalButton" disabled>Soumettre</button>
	</form>
</div>
