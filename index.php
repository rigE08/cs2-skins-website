<?php
require_once 'class/config.php';
require_once 'class/database.php';
require_once 'steamauth/steamauth.php';
require_once 'class/utils.php';

$db = new DataBase();
if (isset($_SESSION['steamid'])) {

	include('steamauth/userInfo.php');
	$steamid = $steamprofile['steamid'];

	$weapons = UtilsClass::getWeaponsFromArray();
	$skins = UtilsClass::skinsFromJson();
	$querySelected = $query3 = $db->select("SELECT `weapon_defindex`, `weapon_paint_id`, `weapon_wear` FROM `wp_player_skins` WHERE `wp_player_skins`.`steamid` = :steamid", ["steamid" => $steamid]);
	$selectedSkins = UtilsClass::getSelectedSkins($querySelected);
	$selectedKnife = $db->select("SELECT * FROM `wp_player_knife` WHERE `wp_player_knife`.`steamid` = :steamid", ["steamid" => $steamid])[0];
	$knifes = UtilsClass::getKnifeTypes();

	if (isset($_POST['forma'])) {
		$ex = explode("-", $_POST['forma']);

		if ($ex[0] == "knife") {
			$db->query("INSERT INTO `wp_player_knife` (`steamid`, `knife`) VALUES(:steamid, :knife) ON DUPLICATE KEY UPDATE `knife` = :knife", ["steamid" => $steamid, "knife" => $knifes[$ex[1]]['weapon_name']]);
		} else {
			if (array_key_exists($ex[1], $skins[$ex[0]]) && isset($_POST['wear']) && $_POST['wear'] >= 0.00 && $_POST['wear'] <= 1.00 && isset($_POST['seed'])) {
				$wear = floatval($_POST['wear']); // wear
				$seed = intval($_POST['seed']); // seed
				if (array_key_exists($ex[0], $selectedSkins)) {
					$db->query("UPDATE wp_player_skins SET weapon_paint_id = :weapon_paint_id, weapon_wear = :weapon_wear, weapon_seed = :weapon_seed WHERE steamid = :steamid AND weapon_defindex = :weapon_defindex", ["steamid" => $steamid, "weapon_defindex" => $ex[0], "weapon_paint_id" => $ex[1], "weapon_wear" => $wear, "weapon_seed" => $seed]);
				} else {
					$db->query("INSERT INTO wp_player_skins (`steamid`, `weapon_defindex`, `weapon_paint_id`, `weapon_wear`, `weapon_seed`) VALUES (:steamid, :weapon_defindex, :weapon_paint_id, :weapon_wear, :weapon_seed)", ["steamid" => $steamid, "weapon_defindex" => $ex[0], "weapon_paint_id" => $ex[1], "weapon_wear" => $wear, "weapon_seed" => $seed]);
				}
			}
		}
		print_r($ex[0]);
		header("Location: {$_SERVER['PHP_SELF']}");
	}
}

?>

<!DOCTYPE html>
<html lang="en" <?php if (WEB_STYLE_DARK) echo 'data-bs-theme="dark"' ?>>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="Zutv Skins Slector, cs2 skins selector, cs2 weapons skins">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
	<script src="https://kit.fontawesome.com/817984bbcd.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="stylesheet" href="style.css">
	<script src="tabs.js"></script>
	<title>ZUTV | Skins</title>



</head>

<body>
<?php
		if (!isset($_SESSION['steamid'])) {
			include("notconnected.php");
		} else {
			include('steamauth/userInfo.php'); // Include this line to get user information
		?>
	<div id="loader">
		<div class="spinner-grow center-loader" style="width: 3rem; height: 3rem;">
			<!-- <span class="visually-hidden">Loading...</span> -->
		</div>
	</div>

	<div class="container-lg">

			<br />
			<nav class="navbar navbar-expand-lg bg-body-tertiary rounded">
				<div class="container-fluid">
					<a class="navbar-brand" href="#">ZUTV</a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav me-auto mb-2 mb-lg-0">
							<li class="nav-item">
								<a class="nav-link active" aria-current="page" href="/skins">Home</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="../forum">Forum</a>
							</li>
							<!-- <li class="nav-item">
								<a class="nav-link" href="#">Stats</a>
							</li> -->

						</ul>

						<ul class="bg-body-secondary d-flex justify-content-evenly align-items-center rounded" style="width: 280px; height: 45px">
							<ul class="d-flex align-items-center w-75">
								<li class="ms-2">
									<img class="img-fluid rounded" src="<?= $steamprofile['avatar'] ?>" alt="profile photo">
								</li>
								<li class="ms-2">
									<?= $steamprofile["personaname"] ?>
								</li>
							</ul>
							<li class="vr"></li>
							<li class="">
								<?= logoutbutton() ?>
							</li>
						</ul>
					</div>
				</div>
			</nav>

			<br />

			<ul class="tab nav nav-pills nav-fill text-uppercase fs-6">
				<li class="nav-item">
					<button class="tablinks nav-link  link-secondary link-offset-2 link-underline-opacity-0" onclick="showCategory('all')">All</button>
				</li>
				<li class="nav-item">
					<button class="tablinks nav-link  link-secondary link-offset-2 link-underline-opacity-0" onclick="showCategory('knives')">Knives</button>
				</li>
				<li class="nav-item">
					<button class="tablinks nav-link  link-secondary link-offset-2 link-underline-opacity-0" onclick="showCategory('pistols')">Pistols</button>
				</li>
				<li class="nav-item">
					<button class="tablinks nav-link  link-secondary link-offset-2 link-underline-opacity-0" onclick="showCategory('rifles')">Rifles</button>
				</li>
				<li class="nav-item">
					<button class="tablinks nav-link  link-secondary link-offset-2 link-underline-opacity-0" onclick="showCategory('smg')">SMG</button>
				</li>
				<li class="nav-item">
					<button class="tablinks nav-link  link-secondary link-offset-2 link-underline-opacity-0" onclick="showCategory('machine guns')">Machine Guns</button>
				</li>
				<li class="nav-item">
					<button class="tablinks nav-link  link-secondary link-offset-2 link-underline-opacity-0" onclick="showCategory('snipers')">Snipers</button>
				</li>
				<li class="nav-item">
					<button class="tablinks nav-link  link-secondary link-offset-2 link-underline-opacity-0" onclick="showCategory('shotguns')">Shotguns</button>
				</li>
			</ul>

			<br />

			<div class="container-lg text-center overflow-auto scrollbar scrollbar-primary" style="height: 71vh;">
				<div class="row align-items-start row-cols-lg-4 row-cols-md-3 row-cols-sm-2">

					<!-- ======================# KNIVES #====================== -->
					<div class="col mb-4 knifelist" data-category="knifes">
						<div class="card text-center">
							<?php
							$actualKnife = $knifes[0];

							foreach ($knifes as $knife) {
								if ($selectedKnife["knife"] == $knife["weapon_name"]) {
									$actualKnife = $knife;
									break;
								}
							}
							?>
							<div class="card-header item-name">
								<?= $actualKnife["paint_name"] ?>
							</div>
							<div class="card-body">
								<img class="skin-image" src="<?= $actualKnife["image_url"] ?>" alt="knife img" loading="lazy">
							</div>
							<div class="card-footer text-body-secondary">
								<form action="" method="post">
									<select name="forma" class="form-select" onchange="this.form.submit()" class="SelectWeapon">
										<option disabled>Select knife</option>
										<?php
										foreach ($knifes as $knifeKey => $knife) {
											if ($selectedKnife['knife'] == $knife['weapon_name']) { ?>
												<option disabled selected value="knife-<?= $knifeKey ?>"><?= $knife['paint_name'] ?></option>
											<?php } else { ?>
												<option value="knife-<?= $knifeKey ?>"><?= $knife['paint_name'] ?></option>
										<?php }
										}
										?>
									</select>
								</form>
							</div>
						</div>
					</div>
					<!-- ======================# WEAPONS #====================== -->
					<?php
					foreach ($weapons as $defindex => $default) {
					?>
						<div class="col mb-4 skinlist" data-defindex="<?= $defindex ?>">
							<div class="card text-center">
								<?php if (array_key_exists($defindex, $selectedSkins)) { ?>
									<div class="card-header item-name">
										<?= $skins[$defindex][$selectedSkins[$defindex]]["paint_name"] ?>
									</div>
									<div class="card-body">
										<img class="skin-image" src="<?= $skins[$defindex][$selectedSkins[$defindex]]['image_url'] ?>" alt="skin img" loading="lazy">
									</div>
								<?php } else { ?>
									<div class="card-header item-name">
										<?= $default["paint_name"] ?>
									</div>
									<div class="card-body">
										<img class="skin-image" src="<?= $default["image_url"] ?>" alt="skin img">
									</div>
								<?php } ?>
								<div class="card-footer text-body-secondary ">
									<form class="d-flex align-items-center justify-content-between" method="post">
										<select name="forma" class="form-select" onchange="this.form.submit()" class="SelectWeapon">
											<option disabled>Select skin</option>
											<?php
											foreach ($skins[$defindex] as $paintKey => $paint) {
												if (array_key_exists($defindex, $selectedSkins) && $selectedSkins[$defindex] == $paintKey) { ?>
													<option selected value="<?= $defindex ?>-<?= $paintKey ?>"><?= $paint['paint_name'] ?></option>
												<?php } else { ?>
													<option value="<?= $defindex ?>-<?= $paintKey ?>"><?= $paint['paint_name'] ?></option>
											<?php }
											}
											?>
										</select>
										<?php
										$selectedSkinInfo = isset($selectedSkins[$defindex]) ? $selectedSkins[$defindex] : null;
										$steamid = $_SESSION['steamid'];
										$defindex = $defindex; // get defindex here, correct if not
										$queryCheck = $db->select("SELECT 1 FROM `wp_player_skins` WHERE `steamid` = :steamid AND `weapon_defindex` = :defindex", ["steamid" => $steamid, "defindex" => $defindex]);
										$hasSkinData = !empty($queryCheck);

										if ($selectedSkinInfo && $hasSkinData) {
										?>
											<button type="button" data-toggle="modal" data-target="#weaponModal<?= $defindex ?>"> <i class="text-secondary-emphasis fa-solid fa-gears ms-2" style="font-size: 25px;"></i>
											</button>
										<?php } else { ?>
											<button type="button" data-swal="true">
												<i class="text-secondary-emphasis fa-solid fa-gears ms-2" style="font-size: 25px;"></i>
											</button>
											<script>
												Swal.mixin({
													text: "You need to select a skin first.",
													icon: "warning",
													background: "#212529",
													color: "#dee2e6",
													confirmButtonColor: "#2b3035"
												}).bindClickHandler("data-swal");
											</script>
										<?php } ?>
								</div>
							</div>
						</div>




						<?php
						// wear value 
						$queryWear = $db->select("SELECT `weapon_wear` FROM `wp_player_skins` WHERE `steamid` = :steamid AND `weapon_defindex` = :weapon_defindex", ["steamid" => $steamid, "weapon_defindex" => $defindex]);
						$selectedSkinInfo = isset($selectedSkins[$defindex]) ? $selectedSkins[$defindex] : null;
						$initialWearValue = isset($selectedSkinInfo['weapon_wear']) ? $selectedSkinInfo['weapon_wear'] : (isset($queryWear[0]['weapon_wear']) ? $queryWear[0]['weapon_wear'] : 0);

						// seed value 
						$querySeed = $db->select("SELECT `weapon_seed` FROM `wp_player_skins` WHERE `steamid` = :steamid AND `weapon_defindex` = :weapon_defindex", ["steamid" => $steamid, "weapon_defindex" => $defindex]);
						$selectedSkinInfo = isset($selectedSkins[$defindex]) ? $selectedSkins[$defindex] : null;
						$initialSeedValue = isset($selectedSkinInfo['weapon_seed']) ? $selectedSkinInfo['weapon_seed'] : (isset($querySeed[0]['weapon_seed']) ? $querySeed[0]['weapon_seed'] : 0);
						?>


						<div class="modal fade" id="weaponModal<?php echo $defindex ?>" tabindex="-1" role="dialog" aria-labelledby="weaponModalLabel<?php echo $defindex ?>" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class='card-title item-name'>
											<?php
											if (array_key_exists($defindex, $selectedSkins)) {
												echo "{$skins[$defindex][$selectedSkins[$defindex]]["paint_name"]} Settings";
											} else {
												echo "{$default["paint_name"]} Settings";
											}
											?>
										</h5>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<select class="form-select" id="wearSelect<?php echo $defindex ?>" name="wearSelect" onchange="updateWearValue<?php echo $defindex ?>(this.value)">
												<option disabled>Select Wear</option>
												<option value="0.00" <?php echo ($initialWearValue == 0.00) ? 'selected' : ''; ?>>Factory New</option>
												<option value="0.07" <?php echo ($initialWearValue == 0.07) ? 'selected' : ''; ?>>Minimal Wear</option>
												<option value="0.15" <?php echo ($initialWearValue == 0.15) ? 'selected' : ''; ?>>Field-Tested</option>
												<option value="0.38" <?php echo ($initialWearValue == 0.38) ? 'selected' : ''; ?>>Well-Worn</option>
												<option value="0.45" <?php echo ($initialWearValue == 0.45) ? 'selected' : ''; ?>>Battle-Scarred</option>
											</select>
											<script>
												// wear
												function updateWearValue<?php echo $defindex ?>(selectedValue) {
													const wearInputElement = document.getElementById("wearSelect<?php echo $defindex ?>");

													wearInputElement.value = selectedValue;

												}

												function validateWear(inputElement) {
													let sanitizedValue = inputElement.value.replace(/[^0-9.,]/g, '');
													sanitizedValue = sanitizedValue.replace(',', '.');
													sanitizedValue = sanitizedValue.replace(/(\..*)\./g, '$1');
													inputElement.value = sanitizedValue;
												}

												// seed
												function validateSeed(input) {
													// Check entered value
													let inputValue = input.value.replace(/[^0-9]/g, '');

													if (inputValue === "" || isNaN(inputValue)) {
														input.value = 0;
													} else {
														const numericValue = parseInt(inputValue);
														input.value = Math.min(1000, Math.max(1, numericValue));
													}
												}
											</script>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="wear">Wear:</label>
													<input type="text" value="<?php echo $initialWearValue; ?>" class="form-control" id="wear<?php echo $defindex ?>" name="wear" oninput="validateWear(this)">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="seed">Seed:</label>
													<input type="text" value="<?php echo $initialSeedValue; ?>" class="form-control" id="seed<?php echo $defindex ?>" name="seed" oninput="validateSeed(this)">
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-success">Use</button>
										</form>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<footer class="footer py-3">
		<div class="container-lg">
			<hr />
			<div class="d-flex justify-content-between">
				<span class="text-body-secondary">© 2024 <a class="text-dark-emphasis" href="https://steamcommunity.com/id/rigee" target="_blank">Rige</a></span>
				<a class="text-dark-emphasis" href="https://github.com/Nereziel/cs2-WeaponPaints" target="_blank">Nereziel/cs2-WeaponPaints</a>
			</div>
		</div>
	</footer>
	<script>
		const loader = document.querySelector('#loader');

		window.onload = (event) => {
			setTimeout(() => {
				loader.classList.add("disappear");
			}, 800);
			showCategory(sessionStorage.getItem('selectedCategory') || 'all');
		};
	</script>
</body>

</html>