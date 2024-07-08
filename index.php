<?php
require_once("./vendor/autoload.php");
require_once("./server/auth.php");

updateCSRFToken();

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);
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

$daysString = "toute la semaine";

if($days == 1)
	$daysString = "du lundi au samedi";
else if($days == 2)
	$daysString = "du lundi au vendredi";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = ""; include("./components/head.php"); ?>
</head>
<body>
	<script src="/utility.js"></script>
	<div class="main-container">
		<?php include("./components/navbar.php"); ?>

		<div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
			<div class="carousel-inner">
				<div class="carousel-item active">
					<img src="content/slide0.jpg" class="d-block w-100" alt="Image de présentation 1">
				</div>
				<div class="carousel-item">
					<img src="content/slide1.jpg" class="d-block w-100" alt="Image de présentation 2">
				</div>
				<div class="carousel-item">
					<img src="content/slide2.jpg" class="d-block w-100" alt="Image de présentation 3">
				</div>
				<div class="carousel-item">
					<img src="content/slide3.jpg" class="d-block w-100" alt="Image de présentation 4">
				</div>
			</div>
			<button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>

		<div class="main-row presentation px-2 px-sm-5">
			<h2 class="text-center fw-bold">Zoo Arcadia</h2>
			<p class="text-center">
				Bienvenue à Arcadia, votre destination idéale pour découvrir la majesté et la diversité du monde animal dans un cadre naturel et accueillant.
				Situé au cœur de la nature, notre zoo s'engage à offrir une expérience enrichissante et éducative tout en mettant la santé et le bien-être de nos résidents au premier plan.
				À Arcadia, nous croyons fermement que chaque animal mérite une attention particulière, c'est pourquoi nous employons une équipe dévouée de vétérinaires expérimentés qui veillent quotidiennement sur leur santé et l'état de leurs habitats.

				<br><br>

				Notre engagement envers le bien-être animal se reflète également dans les environnements soigneusement conçus que nous offrons à nos animaux.
				Les habitats sont régulièrement contrôlés et entretenus pour garantir qu'ils répondent aux besoins spécifiques de chaque espèce, leur offrant ainsi un cadre de vie optimal et stimulant.
				Nous invitons nos visiteurs à découvrir cette attention portée aux détails à travers des visites guidées captivantes, où nos experts partagent leur passion et leurs connaissances sur nos fascinants résidents.

				<br><br>

				Pour rendre votre visite à Arcadia encore plus agréable, nous proposons divers services pour toute la famille.
				Profitez de nos délicieuses options de restauration, explorez le parc à bord de notre charmant petit train, ou participez à une de nos visites guidées pour une immersion complète dans l'univers animalier.
				Que vous soyez un amoureux des animaux ou simplement à la recherche d'une journée de découverte et de détente, Arcadia est l'endroit idéal pour créer des souvenirs inoubliables.
			</p>

			<br>

			<h5 class="text-center fw-bold">Horaires</h5>
			<p class="text-center">
				Ouvert de <?= $fromTime ?> heures à <?= $toTime ?> heures, <?= $daysString ?>
			</p>
		</div>

		<hr class="spacer">

		<div class="main-row services px-2 px-sm-5">
			<h2 class="text-center"><a class="text-success fw-bold" href="services.php">Services</a></h2>
			<div class="container px-2">
				<div class="row row-cols-1 row-cols-md-2 g-2 align-items-start justify-content-center">
					<?php
						$res = $sqli->execute_query("SELECT serviceId, name, description FROM services ORDER BY serviceId ASC LIMIT 4;");

						if($res)
						{
							while($service = $res->fetch_row())
							{
								$serviceTitle = htmlspecialchars($service[1]);
								$serviceDesc = htmlspecialchars($service[2]);

								include("./components/svc_card.php");
							}
						}
					?>
				</div>
			</div>
		</div>

		<hr class="spacer">

		<div class="main-row habitats px-2 px-sm-5">
			<h2 class="text-center"><a class="text-success fw-bold" href="habitats.php">Habitats</a></h2>
			<div class="row row-cols-1 row-cols-md-2 g-2 align-items-start justify-content-center">
				<?php
					$res = $sqli->execute_query("SELECT habitatId, name, description FROM habitats ORDER BY habitatId ASC LIMIT 4;");

					if($res)
					{
						while($habitat = $res->fetch_row())
						{
							$cardTitle = htmlspecialchars($habitat[1]);
							$cardButton = "Voir plus";
							$cardLink = "view_habitat.php?id=" . $habitat[0];
							$cardDesc = htmlspecialchars($habitat[2]);

							$res2 = $sqli->execute_query("SELECT source FROM habitatThumbnails WHERE habitat = ? ORDER BY habitatThumbId ASC LIMIT 1;", [$habitat[0]]);

							$cardThumb = "";

							if($res2)
							{
								$thumb = $res2->fetch_row();

								if($thumb)
									$cardThumb = $thumb[0];
							}

							include("./components/other_card.php");
						}
					}
				?>
			</div>
		</div>

		<hr class="spacer">

		<div class="main-row animals px-2 px-sm-5">
			<h2 class="text-center"><a class="text-success fw-bold" href="animals.php">Animaux</a></h2>
			<div class="row row-cols-1 row-cols-md-2 g-2 align-items-start justify-content-center">
				<?php
					$res = $sqli->execute_query("SELECT animalId, name, race FROM animals WHERE habitat != 0 ORDER BY animalId ASC LIMIT 4;");

					if($res)
					{
						while($animal = $res->fetch_row())
						{
							$cardTitle = htmlspecialchars($animal[1]);
							$cardButton = "Voir plus";
							$cardLink = "view_animal.php?id=" . $animal[0];
							$cardDesc = htmlspecialchars($animal[2]);

							$res2 = $sqli->execute_query("SELECT source FROM animalThumbnails WHERE animal = ? ORDER BY animalThumbId ASC LIMIT 1;", [$animal[0]]);

							$cardThumb = "";

							if($res2)
							{
								$thumb = $res2->fetch_row();

								if($thumb)
									$cardThumb = $thumb[0];
							}

							include("./components/other_card.php");
						}
					}
				?>
			</div>
		</div>

		<hr class="spacer">

		<div class="main-row reviews px-2 px-sm-5">
			<h2 class="text-center">Avis</h2>
			<button class="btn btn-success" id="reviewAddButton">Soumettre un avis</button>

			<div class="d-none card editor-card" id="reviewFormDiv">
					<form id="reviewForm" action="javascript:void(0);" autocomplete="off">
						<div class="mb-3">
							<label for="reviewUsername" class="form-label">Nom</label>
							<input type="name" class="form-control" id="reviewUsername" required>
						</div>

						<div class="mb-3">
							<label for="reviewContent" class="form-label">Avis</label>
							<textarea class="form-control" id="reviewContent" rows="3" required></textarea>
						</div>

						<button id="reviewSubmitButton" class="btn btn-success" type="submit">Soumettre l'avis</button>
						<button id="reviewCancelButton" class="btn btn-secondary" type="button">Annuler</button>
					</form>
			</div>

			<p class="login-message" id="reviewMessage"></p>

			<div class="card mt-3 p-3 pb-0 reviews-inner large-scroll" id="reviewContainer">
				<?php
				$res = $sqli->execute_query("SELECT name, text FROM reviews WHERE validated ORDER BY date DESC;");

				$success = false;

				if($res)
				{
					while($review = $res->fetch_row())
					{
						if(!$success)
							$success = true;

						$name = htmlspecialchars($review[0]);
						$content = str_replace("\n", "<br>", htmlspecialchars($review[1]));

						?>
						<div class="card card-body mb-3 p-3">
							<h5 class="card-title"><?= $name ?></h5>
							<p class="card-text"><?= $content ?></p>
						</div>
						<?php
					}
				}

				if(!$success):
				?>
				<p class="mb-3">Aucun avis</p>
				<?php endif; ?>
			</div>

			<script>
				const reviewContainer = document.getElementById("reviewContainer");
				const reviewFormDiv = document.getElementById("reviewFormDiv");
				const reviewMessage = document.getElementById("reviewMessage");

				const reviewAddBtn = document.getElementById("reviewAddButton");
				const reviewSubmitBtn = document.getElementById("reviewSubmitButton");
				const reviewCancelBtn = document.getElementById("reviewCancelButton");

				const reviewNameInput = document.getElementById("reviewUsername");
				const reviewContentInput = document.getElementById("reviewContent");

				function resetReviewForm()
				{
					reviewNameInput.value = "";
					reviewContentInput.value = "";
					reviewFormDiv.classList.add("d-none");
					reviewAddBtn.classList.remove("d-none");
				}

				reviewAddBtn.addEventListener("click", (evt) => {
					evt.preventDefault();
					reviewFormDiv.classList.remove("d-none");
					reviewAddBtn.classList.add("d-none");
				});
				
				reviewCancelBtn.addEventListener("click", (evt) => {
					resetReviewForm();
				});

				reviewFormDiv.addEventListener("submit", (evt) => {
					const target = "reviewSubmit.php";

					let request = new XMLHttpRequest();

					let data = new FormData();

					data.append("name", reviewNameInput.value);
					data.append("review", reviewContentInput.value);

					reviewSubmitBtn.setAttribute("disabled", "");
					reviewCancelBtn.setAttribute("disabled", "");

					request.onreadystatechange = (ev) => {
						if(request.readyState == 4)
						{
							reviewSubmitBtn.removeAttribute("disabled");
							reviewCancelBtn.removeAttribute("disabled");

							if(request.status == 400 || request.status == 401 || request.status == 403)
								reviewMessage.innerHTML = "Erreur: " + stripHTML(request.responseText);
							else if(request.status == 200)
							{
								reviewMessage.innerHTML = "Avis soumis avec succès";
								resetReviewForm();
								reviewAddBtn.classList.add("d-none");
							}
							else
								reviewMessage.innerHTML = "Erreur inconnue (" + request.status + ")";
						}
					};

					request.open("POST", target);
					request.setRequestHeader("Auth-Token", "<?= getCSRFToken() ?>");

					request.send(data);
				});
			</script>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>