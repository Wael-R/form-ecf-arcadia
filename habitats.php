<?php
require_once("./server/auth.php");
$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);
$page = $_GET["page"] ?? 1;
$search = $_GET["q"] ?? "";
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
			<div class="row">
				<div class="col-12 col-md-8">
					<h2 class="text-success fw-bold">Habitats</h2>
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
							"SELECT c.count, habitatId, name, description FROM habitats
								LEFT JOIN (SELECT COUNT(*) AS count FROM habitats WHERE name LIKE ?) AS c ON 1
								WHERE name LIKE ?
								ORDER BY LENGTH(name) ASC, name ASC LIMIT ?, ?;",
							[$name, $name, ($page - 1) * $pageSize, $pageSize]);

						if($res)
						{
							while($habitat = $res->fetch_row())
							{
								$habitatId = $habitat[1];
								$habitatTitle = htmlspecialchars($habitat[2]);
								$habitatDesc = htmlspecialchars($habitat[3]);
								$count = floor(($habitat[0] - 1) / $pageSize) + 1;

								$res2 = $sqli->execute_query("SELECT source FROM habitatThumbnails WHERE habitat = ? ORDER BY habitatThumbId ASC LIMIT 1;", [$habitatId]);

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
											<img src="<?= $cardThumb ?>" class="list-img rounded-start" alt="Image de <?= $habitatTitle ?>">
										</div>
										<div class="col-md-9 card-body d-flex flex-column">
											<h5 class="card-title main-card-line"><?= $habitatTitle ?></h5>
											<p class="card-text main-card-line"><?= $habitatDesc ?></p>
											<div class="mt-auto d-flex justify-content-end">
												<a class="btn btn-success" href="/view_habitat.php?id=<?= $habitatId ?>">Voir</a>
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