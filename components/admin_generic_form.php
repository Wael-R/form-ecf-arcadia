<a class="anchor" id="<?= $formPrefix ?>Editor"></a>
<h5 class="fw-bold"><?= $formTitle ?></h5>

<br>

<div class="card editor-card">
	<form id="<?= $formPrefix ?>Form" action="javascript:void(0);" autocomplete="off">
		<div class="mb-3">
			<label for="<?= $formPrefix ?>Select" class="form-label"><?= $formSelectLabel ?></label>
			<select class="form-control" id="<?= $formPrefix ?>Select">
				<option value="" selected><?= $formSelectEntry ?></option>
			</select>
		</div>

		<div class="mb-3">
			<label for="<?= $formPrefix ?>Title" class="form-label"><?= $formNameLabel ?></label>
			<input type="text" class="form-control" id="<?= $formPrefix ?>Title">
		</div>

		<div class="mb-3">
			<label for="<?= $formPrefix ?>Description" class="form-label"><?= $formDescLabel ?></label>
		<?php if($formDescMultiline): ?>
			<textarea class="form-control" id="<?= $formPrefix ?>Description" rows="3"></textarea>
		<?php else: ?>
			<input type="text" class="form-control" id="<?= $formPrefix ?>Description">
		<?php endif; ?>
		</div>

		<?php if($formUseImages): ?>
		<div class="mb-3">
			<label class="form-label"><?= $formImageLabel ?></label>
			<div class="editor-box">
				<div id="<?= $formPrefix ?>Thumbs"></div>
				<br>
				<label for="<?= $formPrefix ?>Upload" id="<?= $formPrefix ?>UploadButton" class="btn btn-success disabled">Ajouter...</label>
				<input type="file" id="<?= $formPrefix ?>Upload" class="d-none" accept=".png,.jpg,.jpeg,.webp" disabled>
				<div class="editor-progress progress d-none" id="<?= $formPrefix ?>Progress">
					<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" id="<?= $formPrefix ?>ProgressBar" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<div class="mb-3">
			<p class="login-message" id="<?= $formPrefix ?>Message"></p>
		</div>

		<div class="col-auto fit-md">
			<button type="submit" class="btn btn-success w-100" id="<?= $formPrefix ?>Button" disabled></button>
		</div>
		<?php if($formUseAltButton): ?>
		<div class="col-auto fit-md">
			<button type="button" class="btn btn-success w-100 mt-1 mt-md-0" id="<?= $formPrefix ?>AltButton" disabled><?= $formAltButtonText ?></button>
		</div>
		<?php endif; ?>
		<div class="col-auto fit-md">
			<button type="button" class="btn btn-danger w-100 mt-1 mt-md-0" id="<?= $formPrefix ?>Delete" data-bs-toggle="modal" data-bs-target="#<?= $formPrefix ?>DeleteModal" disabled><?= $formDelete ?></button>
		</div>
	</form>
</div>

<div class="modal fade" id="<?= $formPrefix ?>DeleteModal" tabindex="-1" role="dialog" aria-labelledby="<?= $formPrefix ?>DeleteModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="<?= $formPrefix ?>DeleteModalTitle"><?= $formDelete ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
				<?= $formDeletePrompt ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="<?= $formPrefix ?>DeleteConfirm">Supprimer</button>
      </div>
    </div>
  </div>
</div>