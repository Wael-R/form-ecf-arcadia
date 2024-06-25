<?php
require_once("./server/auth.php");
$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);
$page = $_GET["page"] ?? 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<?php $title = "Services"; include("./components/head.php"); ?>
</head>
<body>
	<div class="main-container">
		<?php include("./components/navbar.php"); ?>

		<br><br>

		<div class="main-row px-2 px-sm-5">
			<h2 class="text-success fw-bold">Services</h2>
			<br>
			<div class="container px-2">
				<div class="row row-cols-1 g-2 align-items-start justify-content-center">
					<?php
						$pageSize = 10;
						$count = 0;

						$res = $sqli->execute_query(
							"SELECT c.count, serviceId, name, description FROM services
								LEFT JOIN (SELECT COUNT(*) AS count FROM services) AS c ON 1
								ORDER BY name ASC LIMIT ?, ?;",
							[($page - 1) * $pageSize, $pageSize]);

						if($res)
						{
							while($service = $res->fetch_row())
							{
								$serviceTitle = htmlspecialchars($service[2]);
								$serviceDesc = htmlspecialchars($service[3]);
								$count = floor(($service[0] - 1) / $pageSize) + 1;

								?>
								<div class="card card-body mb-3">
									<h5 class="card-title main-card-line"><?= $serviceTitle ?></h5>
									<p class="card-text"><?= $serviceDesc ?></p>
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