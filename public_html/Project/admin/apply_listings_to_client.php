<?php
session_start();
require(__DIR__ . "/../../../lib/db.php");
require(__DIR__ . "/../../../lib/flash_messages.php");
require(__DIR__ . "/../../../partials/flash.php");
require(__DIR__ . "/../../../lib/get_url.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $clientId = $_POST["client_id"] ?? null;
    $selectedListings = $_POST["selected_listings"] ?? [];

    if ($clientId && !empty($selectedListings)) {
        try {
            foreach ($selectedListings as $uniqueIdentifier) {
                $listing = $_POST['listings'][$uniqueIdentifier];
                $query = "INSERT INTO IT202_S24_Realty (zipcode, city, state, streetAddress, bathrooms, bedrooms, price, location, lotAreaValue, homeStatus, homeType, assigned_user_id)
                          VALUES (:zipcode, :city, :state, :streetAddress, :bathrooms, :bedrooms, :price, :location, :lotAreaValue, :homeStatus, :homeType, :assigned_user_id)
                          ON DUPLICATE KEY UPDATE
                          city=VALUES(city), state=VALUES(state),
                          location=VALUES(location), lotAreaValue=VALUES(lotAreaValue), homeStatus=VALUES(homeStatus), homeType=VALUES(homeType)";

                $db = getDB();

                $stmt = $db->prepare($query);
                $stmt->execute([
                    ":zipcode" => $listing["zipcode"],
                    ":city" => $listing["city"] ?? null,
                    ":state" => $listing["state"] ?? null,
                    ":streetAddress" => $listing["streetAddress"] ?? null,
                    ":bathrooms" => isset($listing["bathrooms"]) ? $listing["bathrooms"] : null,
                    ":bedrooms" => $listing["bedrooms"] ?? null,
                    ":price" => $listing["price"] ?? null,
                    ":location" => $listing["location"] ?? null,
                    ":lotAreaValue" => $listing["lotAreaValue"] ?? null,
                    ":homeStatus" => $listing["homeStatus"] ?? null,
                    ":homeType" => $listing["homeType"] ?? null,
                    ":assigned_user_id" => $clientId
                ]);
            }
          
        } catch (Exception $e) {
            print_r($e);
            flash("An error occurred while assigning listings", "danger");
        }
        flash("Listings successfully assigned to client", "success");
        die(header("Location: " . get_url('Project/admin/create_realty.php')));
        
     
    }
}
