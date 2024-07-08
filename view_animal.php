<?php
require_once("./vendor/autoload.php");
require_once("./server/auth.php");
$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);
$mongo = new MongoDB\Client(getMongoQueryString());

$id = $_GET["id"] ?? 0;

if($id <= 0)
{
	http_response_code(404);
	exit("Animal invalide");
}

$res = $sqli->execute_query(
	"SELECT a.name, a.race, a.health, h.name, h.habitatId FROM animals AS a
		LEFT JOIN habitats AS h ON habitat = h.habitatId
		WHERE h.name != '' AND animalId = ?;",
	[$id]);

if(!$res)
{
	http_response_code(500);
	exit("Erreur lors du chargement de l'animal");
}

$animal = $res->fetch_row();

if(!$animal)
{
	http_response_code(404);
	exit("Animal invalide");
}

$animalName = $animal[0];
$animalRace = $animal[1];
$animalHealth = $animal[2];
$animalHabitat = $animal[3];
$animalHabitatId = $animal[4];
$animalThumbs = [];

$res2 = $sqli->execute_query("SELECT source FROM animalThumbnails WHERE animal = ?", [$id]);

if($res2)
{
	while($thumb = $res2->fetch_row())
		$animalThumbs[] = $thumb[0];
}

$anim = $mongo->arcadia->animals->findOne(["id" => $id]);

if(!$anim)
	$mongo->arcadia->animals->insertOne(["id" => $id, "views" => 1]);
else
	$mongo->arcadia->animals->updateOne(["id" => $id], ["\$set" => ["views" => $anim["views"] + 1]]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = $animalName; include("./components/head.php"); ?>
</head>
<body>
	<div class="main-container">
		<?php include("./components/navbar.php"); ?>

		<br><br>

		<div class="main-row px-2 px-sm-5">
			<h2 class="text-success fw-bold"><?= $animalName ?></h2>
			<br>
			<div class="container px-2">
				<?php
					if(count($animalThumbs) > 0)
					{
						?>
						<div id="carousel" class="main-carousel carousel slide">
							<?php if(count($animalThumbs) > 1): ?>
							<div class="carousel-indicators">
								<?php foreach($animalThumbs as $idx => $thumb): ?>
								<button type="button" data-bs-target="#carousel" data-bs-slide-to="<?= $idx ?>" <?= $idx == 0 ? 'class="active" aria-current="true"' : "" ?> aria-label="Image <?= $idx + 1 ?>"></button>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>

							<div class="carousel-inner">
								<?php foreach($animalThumbs as $idx => $thumb): ?>
								<div class="carousel-item <?= $idx == 0 ? "active" : "" ?>">
									<a href="<?= $thumb ?>" target="_blank"><img src="<?= $thumb ?>" class="main-carousel-img d-block" alt="Image de <?= $animalName ?>"></a>
								</div>
								<?php endforeach; ?>
							</div>

							<?php if(count($animalThumbs) > 1): ?>
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

				<p><?= htmlspecialchars($animalRace) ?></p>

				<p>État: <?= htmlspecialchars($animalHealth) ?></p>

				<p>Habitat: <a class="text-success" href="view_habitat.php?id=<?= $animalHabitatId ?>"><?= htmlspecialchars($animalHabitat) ?></a></p>

			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>