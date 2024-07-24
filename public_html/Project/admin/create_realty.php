<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php

//TODO handle property fetch
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $symbol =  strtoupper(se($_POST, "symbol", "", false));
    $quote = [];
    if ($symbol) {
        if ($action === "fetch") {
            $result = fetch_quote($symbol);
            error_log("Data from API" . var_export($result, true));
            if ($result) {
                $quote = $result;
            }
        } else if ($action === "create") {
            foreach ($_POST as $k => $v) {
                if (!in_array($k, ["symbol", "open", "low", "high", "price", "previous", "per_change", "volume", "latest"])) {
                    unset($_POST[$k]);
                }
                $quote = $_POST;
                error_log("Cleaned up POST: " . var_export($quote, true));
            }
        }
    } else {
        flash("You must provide a symbol", "warning");
    }
    //insert data
    $db = getDB();
    $query = "INSERT INTO `IT202-S24-Realty` ";
    $columns = [];
    $params = [];
    //per record
    foreach ($quote as $k => $v) {
        array_push($columns, "`$k`");
        $params[":$k"] = $v;
    }
    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",", array_keys($params)) . ")";
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record " . $db->lastInsertId(), "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
}

//TODO handle manual create realty
?>
<div class="container-fluid">
    <h3>Create or Fetch Property</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "search", "name" => "symbol", "placeholder" => "Realty Symbol", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit",]); ?>
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">

            <?php render_input(["type" => "text", "name" => "zpid", "placeholder" => "zpid", "label" => "zpid", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "open", "placeholder" => "Realty Open", "label" => "Realty Open", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "low", "placeholder" => "Realty Low", "label" => "Realty Low", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "high", "placeholder" => "Realty High", "label" => "Realty High", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "price", "placeholder" => "Realty Current Price", "label" => "Realty Current Price", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "previous", "placeholder" => "Realty Previous", "label" => "Realty Previous", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "per_change", "placeholder" => "Realty % change", "label" => "Realty % change", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "volume", "placeholder" => "Realty Volume", "label" => "Realty Volume", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "date", "name" => "latest", "placeholder" => "Realty Date", "label" => "Realty Date", "rules" => ["required" => "required"]]); ?>

            <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit", "text" => "Create"]); ?>
        </form>
    </div>
</div>
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
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>