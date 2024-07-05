<div class="dropdown">
	<button id="<?= $datePickerPrefix ?>Button" type="button" class="btn btn-success w-100 dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
		Date...
	</button>
	<div class="dropdown-menu p-2">
		<div class="row g-2">
			<div class="col-auto">
				<label for="<?= $datePickerPrefix ?>Day" class="visually-hidden">Jour</label>
				<select class="form-control date-number" id="<?= $datePickerPrefix ?>Day">
				</select>
			</div>
			<div class="col-auto">
				<label for="<?= $datePickerPrefix ?>Month" class="visually-hidden">Mois</label>
				<select class="form-control date-month" id="<?= $datePickerPrefix ?>Month">
					<option value="0">Janvier</option>
					<option value="1">Février</option>
					<option value="2">Mars</option>
					<option value="3">Avril</option>
					<option value="4">Mai</option>
					<option value="5">Juin</option>
					<option value="6">Juillet</option>
					<option value="7">Aout</option>
					<option value="8">Septembre</option>
					<option value="9">Octobre</option>
					<option value="10">Novembre</option>
					<option value="11">Décembre</option>
				</select>
			</div>
			<div class="col-auto">
				<label for="<?= $datePickerPrefix ?>Year" class="visually-hidden">Année</label>
				<select class="form-control date-number" id="<?= $datePickerPrefix ?>Year">
					<?php for($i = date("Y"); $i >= 1970; $i--): ?>
					<option value="<?= $i ?>"><?= $i ?></option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="col-auto">
				<label for="<?= $datePickerPrefix ?>Hours" class="visually-hidden">Heure</label>
				<select class="form-control date-number" id="<?= $datePickerPrefix ?>Hours">
					<?php for($i = 0; $i <= 23; $i++): ?>
					<option value="<?= $i ?>"><?= str_pad($i, 2, "0", STR_PAD_LEFT) . " h" ?></option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="col-auto">
				<label for="<?= $datePickerPrefix ?>Minutes" class="visually-hidden">Minutes</label>
				<select class="form-control date-number" id="<?= $datePickerPrefix ?>Minutes">
					<?php for($i = 0; $i <= 59; $i++): ?>
					<option value="<?= $i ?>"><?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
	</div>
</div>

<script>
	const <?= $datePickerPrefix ?>DateProps = {
		button: document.getElementById("<?= $datePickerPrefix ?>Button"),
		day: document.getElementById("<?= $datePickerPrefix ?>Day"),
		month: document.getElementById("<?= $datePickerPrefix ?>Month"),
		year: document.getElementById("<?= $datePickerPrefix ?>Year"),
		hours: document.getElementById("<?= $datePickerPrefix ?>Hours"),
		minutes: document.getElementById("<?= $datePickerPrefix ?>Minutes"),

		min: new Date(0),
		max: new Date(),

		onChange: null
	};
</script>