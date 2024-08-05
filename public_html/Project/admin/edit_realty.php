<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php
$id = se($_GET, "id", -1, false);
//TODO handle stock fetch
if (isset($_POST["symbol"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["symbol", "open", "low", "high", "price", "previous", "per_change", "volume", "latest"])) {
            unset($_POST[$k]);
        }
        $quote = $_POST;
        error_log("Cleaned up POST: " . var_export($quote, true));
    }
    //insert data
    $db = getDB();
    $query = "UPDATE `IT202-S24-Realty` SET ";

    $params = [];
    //per record
    foreach ($quote as $k => $v) {

        if ($params) {
            $query .= ",";
        }
        //be sure $k is trusted as this is a source of sql injection
        $query .= "$k=:$k";
        $params[":$k"] = $v;
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Updated record ", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
}

$property = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT symbol, open, low, high, price, per_change, latest,previous, volume FROM `IT202-S24-Realty` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $property = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    die(header("Location:" . get_url("admin/list_realty.php")));
}
if ($property) {
    $form = [
        ["type" => "text", "name" => "symbol", "placeholder" => "Realty Symbol", "label" => "Realty Symbol", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "open", "placeholder" => "Realty Open", "label" => "Realty Open", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "low", "placeholder" => "Realty Low", "label" => "Realty Low", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "high", "placeholder" => "Realty High", "label" => "Realty High", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "price", "placeholder" => "Realty Current Price", "label" => "Realty Current Price", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "previous", "placeholder" => "Realty Previous", "label" => "Realty Previous", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "per_change", "placeholder" => "Realty % change", "label" => "Realty % change", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "volume", "placeholder" => "Realty Volume", "label" => "Realty Volume", "rules" => ["required" => "required"]],
        ["type" => "date", "name" => "latest", "placeholder" => "Realty Date", "label" => "Realty Date", "rules" => ["required" => "required"]],

    ];
    $keys = array_keys($property);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $property[$v["name"]];
        }
    }
}
//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Edit Property</h3>
    <div>
        <a href="<?php echo get_url("admin/list_realty.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST">
        <?php foreach ($form as $k => $v) {

            render_input($v);
        } ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Update"]); ?>
    </form>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>