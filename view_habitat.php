<?php
require_once("./server/auth.php");
$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);
$id = $_GET["id"] ?? 0;

if($id <= 0)
{
	http_response_code(404);
	exit("Habitat invalide");
}

$res = $sqli->execute_query("SELECT name, description FROM habitats WHERE habitatId = ?", [$id]);

if(!$res)
{
	http_response_code(500);
	exit("Erreur lors du chargement de l'habitat");
}

$habitat = $res->fetch_row();

if(!$habitat)
{
	http_response_code(404);
	exit("Habitat invalide");
}

$habitatTitle = $habitat[0];
$habitatDesc = $habitat[1];
$habitatThumbs = [];
$habitatAnimals = [];

$res2 = $sqli->execute_query("SELECT source FROM habitatThumbnails WHERE habitat = ?", [$id]);

if($res2)
{
	while($thumb = $res2->fetch_row())
		$habitatThumbs[] = $thumb[0];
}

$res3 = $sqli->execute_query(
	"SELECT animalId AS id, name, race, health, t.source AS thumb FROM animals
		LEFT JOIN animalThumbnails AS t ON t.animalThumbId = (
			SELECT MIN(t.animalThumbId) FROM animalThumbnails AS t
			WHERE animalId = t.animal LIMIT 1
		)
		WHERE habitat = ?
		ORDER BY name ASC",
	[$id]);

if($res3)
{
	while($animal = $res3->fetch_assoc())
		$habitatAnimals[] = $animal;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = "Habitats"; include("./components/head.php"); ?>
</head>
<body>
	<div class="main-container">
		<?php include("./components/navbar.php"); ?>

		<br><br>

		<div class="main-row px-2 px-sm-5">
			<h2 class="text-success fw-bold"><?= htmlspecialchars($habitatTitle) ?></h2>
			<br>
			<div class="container px-2">
				<?php
					if(count($habitatThumbs) > 0)
					{
						?>
						<div id="carousel" class="main-carousel carousel slide">
							<?php if(count($habitatThumbs) > 1): ?>
							<div class="carousel-indicators">
								<?php foreach($habitatThumbs as $idx => $thumb): ?>
								<button type="button" data-bs-target="#carousel" data-bs-slide-to="<?= $idx ?>" <?= $idx == 0 ? 'class="active" aria-current="true"' : "" ?> aria-label="Image <?= $idx + 1 ?>"></button>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>

							<div class="carousel-inner">
								<?php foreach($habitatThumbs as $idx => $thumb): ?>
								<div class="carousel-item <?= $idx == 0 ? "active" : "" ?>">
									<a href="<?= $thumb ?>" target="_blank"><img src="<?= $thumb ?>" class="main-carousel-img d-block" alt="Image de <?= $habitatTitle ?>"></a>
								</div>
								<?php endforeach; ?>
							</div>

							<?php if(count($habitatThumbs) > 1): ?>
							<button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
								<span class="carousel-control-prev-icon" aria-hidden="true"></span>
								<span class="visually-hidden">Previous</span>
							</button>
							<button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
								<span class="carousel-control-next-icon" aria-hidden="true"></span>
								<span class="visually-hidden">Next</span>
							</button>
							<?php endif; ?>
						</div>
						<br><br>
						<?php
					}
				?>

				<p><?= str_replace("\n", "<br>", htmlspecialchars($habitatDesc)) ?></p>

				<?php
					if(count($habitatAnimals) > 0)
					{
						?>
						<br><br>

						<h5 class="mb-3">Animaux de l'habitat:</h5>

						<div class="card large-scroll p-3 pb-0">
							<?php foreach($habitatAnimals as $animal): ?>
							<div class="card mb-3 px-0">
								<div class="row g-0">
									<div class="col-md-3">
										<img src="<?= $animal["thumb"] ?>" class="list-img rounded-start" alt="Image de <?= htmlspecialchars($animal["name"]) ?>">
									</div>
									<div class="col-md-9 card-body d-flex flex-column">
										<h5 class="card-title main-card-line"><?= htmlspecialchars($animal["name"]) ?></h5>
										<p class="card-subtitle mb-2 text-body-secondary main-card-line"><?= htmlspecialchars($animal["race"]) ?></p>
										<div class="mt-auto d-flex justify-content-end">
											<a class="btn btn-success" href="/view_animal?id=<?= $animal["id"] ?>">Voir</a>
										</div>
									</div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
						<?php
					}
				?>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>