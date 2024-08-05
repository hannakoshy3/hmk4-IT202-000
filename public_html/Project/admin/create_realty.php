<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/dbzillow_helper.php");
require_once(__DIR__ . "/../../../partials/flash.php");
require_once(__DIR__ . "/../../../lib/flash_messages.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
//hmk4
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $location = se($_POST, "location", "", false);
    $min_bedrooms = se($_POST, "min_bedrooms", "", false);
    $min_bathrooms = se($_POST, "min_bathrooms", "", false);
    $min_price = se($_POST, "min_price", "", false);
    $max_price = se($_POST, "max_price", "", false);

    $listings = [];
    $processed_listing = [];
    if ($location) {
        if ($action === "fetch") {
            $result = fetch_realty_listings($location, $min_bedrooms, $min_bathrooms, $min_price, $max_price);
            error_log("Data from API" . var_export($result, true));
            if ($result) {
                $processed_listing = $result;
                $processed_listing["is_api"] = 1;
            }
        }
    } else {
        flash("You must provide a location", "warning");
    }
    try {
        $opts = ["debug" => true, "update_duplicate" => false, "columns_to_update" => []];
    } catch (InvalidArgumentException $e1) {
        error_log("Invalid arg" . var_export($e1, true));
        flash("Invalid data passed", "danger");
    } catch (PDOException $e2) {
        if ($e2->errorInfo[1] == 1062) {
            flash("An entry for this location already exists", "warning");
        } else {
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    } catch (Exception $e3) {
        error_log("Invalid data records" . var_export($e3, true));
        flash("Invalid data records", "danger");
    }
}
?>

<!-- hmk4 Each list item should show a summary of the data you want to show in this view-->
<div class="container-fluid">
    <h3>Fetch Realty Listings</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <div class="row">
                <div class="col-md-4">
                    <?php render_input(["type" => "search", "name" => "location", "placeholder" => "Location", "rules" => ["required" => "required"]]); ?>
                </div>
                <div class="col-md-4">
                    <?php render_input(["type" => "number",  "name" => "min_bedrooms", "placeholder" => "Minimum Bedrooms", "rules" => ["min" => "1"]]); ?>
                </div>
                <div class="col-md-4">
                    <?php render_input(["type" => "number",  "name" => "min_bathrooms", "placeholder" => "Minimum Bathrooms", "rules" => ["min" => "1"]]); ?>
                </div>
                <div class="col-md-4">
                    <?php render_input(["type" => "number",  "name" => "min_price", "placeholder" => "Minimum Price", "rules" => ["min" => "1"]]); ?>
                </div>
                <div class="col-md-4">
                    <?php render_input(["type" => "number",  "name" => "max_price", "placeholder" => "Maximum Price", "rules" => ["min" => "1", "max" => "9000000000"]]); ?>
                </div>
            </div>
            <div></div>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit",]); ?>
        </form>
    </div>

</div>
<form method="POST" action="apply_listings_to_client.php">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">




                <select name="client_id" class="form-control">
                    <option value="">Select Client</option>
                    
                    <?php
                    // Fetch clients from the database
                    $clients = get_all_clients(); // You'll need to implement this function
                    foreach ($clients ?? []  as $client) {
                        echo '<option value="' . htmlspecialchars($client["id"]) . '">' . htmlspecialchars($client["username"]) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Apply Listings to Client</button>
            </div>
        </div>

    </div>

    </div>
    </div>
    <div class="container-fluid">
<br>
<br>
<br>
        <div class="row">
            
            <?php if (isset($processed_listing)) { ?>
                <?php foreach ($processed_listing as $listing) : ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <!--hmk4 Design/Style is your choice but must be applied, no plaintext dump to the screen-->
                                <h5 class="card-title">
                                    <?= htmlspecialchars($listing["streetAddress"] ?? "N/A") ?>
                                </h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <?= htmlspecialchars($listing["city"] ?? "N/A") ?>,
                                    <?= htmlspecialchars($listing["state"] ?? "N/A") ?>
                                </h6>
                                <p class="card-text">
                                    <strong>Zip Code:</strong> <?= htmlspecialchars($listing["zipcode"] ?? "N/A") ?><br>
                                    <strong>Bathrooms:</strong> <?= htmlspecialchars($listing["bathrooms"] ?? "N/A") ?><br>
                                    <strong>Bedrooms:</strong> <?= htmlspecialchars($listing["bedrooms"] ?? "N/A") ?><br>
                                    <strong>Price:</strong> $<?= htmlspecialchars(number_format($listing["price"] ?? 0)) ?><br>
                                    <strong>Location:</strong> <?= htmlspecialchars($listing["location"] ?? "N/A") ?><br>
                                    <strong>Lot Area:</strong> <?= htmlspecialchars($listing["lotAreaValue"] ?? "N/A") ?><br>
                                    <strong>Home Status:</strong> <?= htmlspecialchars($listing["homeStatus"] ?? "N/A") ?><br>
                                    <strong>Home Type:</strong> <?= htmlspecialchars($listing["homeType"] ?? "N/A") ?><br>
                                </p>
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][streetAddress]" value="<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][zipcode]" value="<?= htmlspecialchars($listing["zipcode"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][city]" value="<?= htmlspecialchars($listing["city"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][state]" value="<?= htmlspecialchars($listing["state"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][bathrooms]" value="<?= htmlspecialchars($listing["bathrooms"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][bedrooms]" value="<?= htmlspecialchars($listing["bedrooms"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][price]" value="<?= htmlspecialchars($listing["price"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][location]" value="<?= htmlspecialchars($listing["location"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][lotAreaValue]" value="<?= htmlspecialchars($listing["lotAreaValue"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][homeStatus]" value="<?= htmlspecialchars($listing["homeStatus"] ?? "") ?>">
                                <input type="hidden" name="listings[<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>][homeType]" value="<?= htmlspecialchars($listing["homeType"] ?? "") ?>">
                                <input type="checkbox" name="selected_listings[]" value="<?= htmlspecialchars($listing["streetAddress"] ?? "") ?>_<?= htmlspecialchars($listing["zipcode"] ?? "") ?>"> Select Listing
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php } ?>


        </div>
    </div>
</form>


<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }
</script>

<?php

function get_all_clients()
{
    try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT Users.id, Users.username 
        FROM Users
        JOIN UserRoles ON Users.id = UserRoles.user_id
        JOIN Roles ON UserRoles.role_id = Roles.id
        WHERE Roles.name = 'client' AND UserRoles.is_active = 1
    ");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $clients;
} catch (Exception $e) {
    return []; 
}
}

?>