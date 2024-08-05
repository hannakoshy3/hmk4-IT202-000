<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/dbzillow_helper.php");
require_once(__DIR__ . "/../../../partials/flash.php");

$clientId = $_SESSION['user']['id'];


if (!has_role("Client")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

function getClientRealtyListings($clientId, $state, $streetAddress)
{
    $db = getDB();
    $query = "SELECT * FROM IT202_S24_Realty WHERE assigned_user_id = :clientId";
    $params = [':clientId' => $clientId];

    if (!empty($state) && $state !== NULL) {

        echo $state;
        $query .= " AND `state` = :state";
        $params[":state"] = $state;
    }

    if (!empty($streetAddress)) {
        $query .= " AND streetAddress LIKE :streetAddress";
        $params[':streetAddress'] = '%' . $streetAddress . '%';
    }

    $stmt = $db->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$state = $_GET['state'] ?? '';

$streetAddress = $_GET['streetAddress'] ?? '';

$listings = getClientRealtyListings($clientId, $state, $streetAddress);

?>
<div class="container-fluid">
    <h3>Fetch Client Listings</h3>
    <form method="GET">
        <div class="row">
            <div class="col-md-4">
                <?php render_input(["type" => "search", "name" => "streetAddress", "placeholder" => "Street Address"]); ?>
            </div>
            <div class="col-md-4">
                <?php render_input(["type" => "text",  "name" => "state", "placeholder" => "State"]); ?>
            </div>

        </div>
        <div></div>
        <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
        <?php render_button(["text" => "Search", "type" => "submit",]); ?>
    </form>
</div>


<div class="container-fluid">
    <br>
    <br>
    <br>
    <div class="row">

        <?php if (isset($listings)) { ?>
            <?php foreach ($listings as $listing) : ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
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

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php } ?>


    </div>
</div>