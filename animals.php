<?php
require_once("./server/auth.php");
$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);
$page = $_GET["page"] ?? 1;
$search = $_GET["q"] ?? "";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = "Animaux"; include("./components/head.php"); ?>
</head>
<body>
	<div class="main-container">
		<?php include("./components/navbar.php"); ?>

		<br><br>

		<div class="main-row px-2 px-sm-5">
			<div class="row">
				<div class="col-12 col-md-8">
					<h2 class="text-success fw-bold">Animaux</h2>
				</div>
				<div class="col-12 col-md-4">
					<input class="form-control" type="text" value="<?= $search ?>" placeholder="Rechercher..." onchange="window.location.href = `?q=${encodeURIComponent(this.value)}`;">
				</div>
			</div>
			<br>
			<div class="container px-2">
				<div class="row row-cols-1 g-2 align-items-start justify-content-center">
					<?php
						$pageSize = 10;
						$count = 0;
						$name = "%" . $search . "%";

						$res = $sqli->execute_query(
							"SELECT c.count, a.animalId, a.name, a.race, a.health, h.name AS habitat FROM animals AS a
								LEFT JOIN (SELECT COUNT(*) AS count FROM animals WHERE name LIKE ?) AS c ON 1
								LEFT JOIN habitats AS h ON habitat = h.habitatId
								WHERE h.name != '' AND a.name LIKE ?
								ORDER BY LENGTH(a.name) ASC, a.name ASC LIMIT ?, ?;",
							[$name, $name, ($page - 1) * $pageSize, $pageSize]);

						if($res)
						{
							while($animal = $res->fetch_row())
							{
								$animalId = $animal[1];
								$animalTitle = htmlspecialchars($animal[2]);
								$animalRace = htmlspecialchars($animal[3]);
								$animalHealth = htmlspecialchars($animal[4]);
								$animalHabitat = htmlspecialchars($animal[5]);
								$count = floor(($animal[0] - 1) / $pageSize) + 1;

								$res2 = $sqli->execute_query("SELECT source FROM animalThumbnails WHERE animal = ? ORDER BY animalThumbId ASC LIMIT 1;", [$animalId]);

								$cardThumb = "";

								if($res2)
								{
									$thumb = $res2->fetch_row();

									if($thumb)
										$cardThumb = $thumb[0];
								}

								?>
								<div class="card mb-3 px-0">
									<div class="row g-0">
										<div class="col-md-3">
											<img src="<?= $cardThumb ?>" class="list-img rounded-start" alt="Image de <?= $animalTitle ?>">
										</div>
										<div class="col-md-9 card-body d-flex flex-column">
											<h5 class="card-title main-card-line"><?= $animalTitle ?></h5>
											<p class="card-subtitle mb-2 text-body-secondary main-card-line"><?= $animalRace ?></p>
											<p class="card-subtitle mb-2 text-body-secondary main-card-line"><?= $animalHealth ?></p>
											<p class="card-subtitle mb-2 text-body-secondary main-card-line"><?= $animalHabitat ?></p>
											<div class="mt-auto d-flex justify-content-end">
												<a class="btn btn-success" href="/view_animal.php?id=<?= $animalId ?>">Voir</a>
											</div>
										</div>
									</div>
								</div>
								<?php
							}
						}
					?>
				</div>
			</div>
		</div>

		<?php include("./components/page_nav.php"); ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>