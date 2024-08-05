<?php
ob_start();
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../partials/flash.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}//hmk4   listings page

function deleteRealtyListing($listingId)
{
    $db = getDB();
    $query = "DELETE FROM IT202_S24_Realty WHERE id = :listingId";
    $stmt = $db->prepare($query);
    return $stmt->execute([':listingId' => $listingId]);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_listing_id'])) {
    $listingId = $_POST['delete_listing_id'];
    if (deleteRealtyListing($listingId)) {
        flash("Listing deleted successfully", "success");
    } else {
        flash("Failed to delete listing", "danger");
    }
    die(header("Location: " . get_url('admin/list_realty.php')));
}

function getClientRealtyListings($clientId)
{
    $db = getDB();
    $query = "SELECT * FROM IT202_S24_Realty WHERE assigned_user_id = :clientId";
    $params = [':clientId' => $clientId];

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$client_id = $_GET['client_id'] ?? '';
$listings = [];


if (!empty($client_id)) {

    $listings = getClientRealtyListings($client_id);
}

require_once(__DIR__ . "/../../../lib/flash_messages.php");

?>
<div class="container-fluid">
    <h3>View Listings by Client</h3>
    <br>
    <br>
    <form method="GET">


        <div class="col-md-6">


            <select name="client_id" class="form-control">
                <option value="">Select Client</option>

                <?php
                // Fetch clients from the database       hmk4
                //View Data Details Page
                $clients = get_all_clients(); 
                foreach ($clients ?? []  as $client) {
                    $selected = $client["id"] == $client_id ? "selected" : "";
                    echo '<option value="' . htmlspecialchars($client["id"]) . '" ' . $selected . '>' . htmlspecialchars($client["username"]) . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="col-md-4 mt-4">
            <button type="submit" class="btn btn-primary">Search Listings by Client</button>
        </div>
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
                            <form method="POST" action="<?php echo get_url('admin/list_realty.php')?>?client_id=<?= htmlspecialchars($client_id) ?>">
                                <input type="hidden" name="delete_listing_id" value="<?= htmlspecialchars($listing['id']) ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php } ?>


    </div>
</div>
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



ob_end_flush();?>
