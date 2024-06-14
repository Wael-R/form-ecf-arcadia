<?php
require_once("./server/auth.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = ""; include("./components/head.php"); ?>
</head>
<body>
	<div class="main-container">
		<?php include("./components/navbar.php"); ?>

		<div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
			<div class="carousel-inner">
				<div class="carousel-item active">
					<img src="content/placeholder.png" class="d-block w-100" alt="todo (1)">
				</div>
				<div class="carousel-item">
					<img src="content/placeholder.png" class="d-block w-100" alt="todo (2)">
				</div>
				<div class="carousel-item">
					<img src="content/placeholder.png" class="d-block w-100" alt="todo (3)">
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

		<!-- todo? center rows -->

		<div class="main-row presentation px-2 px-sm-5">
			<h2 class="fw-bold">Zoo Arcadia</h2>
			<p>
				lorem ipsum, etc. longer placeholder text because //todo just won't cut it! Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
			</p>
		</div>

		<hr class="spacer">

		<div class="main-row services px-2 px-sm-5">
			<h2><a class="text-success fw-bold" href="services.php">Services</a></h2>
			<div class="container px-2">
				<div class="row row-cols-1 row-cols-md-2 g-2 align-items-start justify-content-center">
					<?php
						$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

						$res = $sqli->execute_query("SELECT serviceId, name, description FROM services ORDER BY serviceId ASC LIMIT 4;");
						// todo? add a showcase picker to select which services to show in the home page

						if($res)
						{
							while($service = $res->fetch_row())
							{
								$serviceTitle = $service[1];
								$serviceDesc = substr($service[2], 0, 40);

								if(strlen($service[2]) > 40)
									$serviceDesc .= "...";

								include("./components/svc_card.php");
							}
						}
					?>
				</div>
			</div>
		</div>

		<hr class="spacer">

		<div class="main-row habitats px-2 px-sm-5">
			<h2><a class="text-success fw-bold" href="habitats.php">Habitats</a></h2>
			<div class="row row-cols-1 row-cols-md-2 g-2 align-items-start justify-content-center">
					<?php
						$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

						$res = $sqli->execute_query("SELECT habitatId, name, description FROM habitats ORDER BY habitatId ASC LIMIT 4;");

						if($res)
						{
							while($habitat = $res->fetch_row())
							{
								$cardTitle = $habitat[1];
								$cardButton = "Voir plus";
								$cardLink = "#todo";
								$cardDesc = substr($habitat[2], 0, 40);

								if(strlen($habitat[2]) > 40)
									$cardDesc .= "...";

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
			<!-- todo: (php) display list of habitats (max 4?) -->
		</div>

		<hr class="spacer">

		<div class="main-row animals px-2 px-sm-5">
			<h2><a class="text-success fw-bold" href="animals.php">Animaux</a></h2>
			<div class="row row-cols-1 row-cols-md-2 g-2 align-items-start justify-content-center">
				<?php include("./components/other_card.php"); ?>
				<?php include("./components/other_card.php"); ?>
				<?php include("./components/other_card.php"); ?>
				<?php include("./components/other_card.php"); ?>
			</div>
			<!-- todo: (php) display list of animals (max 4?) -->
			<!-- todo? show only the most popular animals -->
		</div>
		<!-- * for each of the above, the header should link over to the full corresponding list page -->

		<hr class="spacer">
	
		<div class="main-row reviews px-2 px-sm-5">
			<h2>Avis</h2>
			<div class="reviews-inner">
				// todo
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>