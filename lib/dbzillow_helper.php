<?php


function insert_realty_listings($listings)
{
               $pdo = getDB();
    try {
       
        $sql = "INSERT INTO `IT202_S24_Realty` (
                    `zipcode`, 
                    `city`, 
                    `country`, 
                    `state`, 
                    `streetAddress`, 
                    `bathrooms`, 
                    `bedrooms`, 
                    `price`, 
                    `location`, 
                    `lotAreaValue`, 
                    `homeStatus`, 
                    `homeType`
                ) VALUES (
                    :zipcode, 
                    :city, 
                    :country, 
                    :state, 
                    :streetAddress, 
                    :bathrooms, 
                    :bedrooms, 
                    :price, 
                    :location, 
                    :lotAreaValue, 
                    :homeStatus, 
                    :homeType
                ) ON DUPLICATE KEY UPDATE 
                    `city` = VALUES(`city`),
                    `country` = VALUES(`country`),
                    `state` = VALUES(`state`),
                    `bathrooms` = VALUES(`bathrooms`),
                    `bedrooms` = VALUES(`bedrooms`),
                    `price` = VALUES(`price`),
                    `location` = VALUES(`location`),
                    `lotAreaValue` = VALUES(`lotAreaValue`),
                    `homeStatus` = VALUES(`homeStatus`),
                    `homeType` = VALUES(`homeType`)";

       
        $stmt = $pdo->prepare($sql);


        foreach ($listings as $listing) {
          
            $zipcode = $listing['zipcode'] ?? null;
            $city = $listing['city'] ?? null;
            $country = $listing['country'] ?? null;
            $state = $listing['state'] ?? null;
            $streetAddress = $listing['streetAddress'] ?? null;
            $bathrooms = $listing['bathrooms'] ?? null;
            $bedrooms = $listing['bedrooms'] ?? null;
            $price = $listing['price'] ?? null;
            $location = $listing['location'] ?? null;
            $lotAreaValue = $listing['lotAreaValue'] ?? null;
            $homeStatus = $listing['homeStatus'] ?? null;
            $homeType = $listing['homeType'] ?? null;

            
            $stmt->bindParam(':zipcode', $zipcode);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':country', $country);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':streetAddress', $streetAddress);
            $stmt->bindParam(':bathrooms', $bathrooms);
            $stmt->bindParam(':bedrooms', $bedrooms);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':lotAreaValue', $lotAreaValue);
            $stmt->bindParam(':homeStatus', $homeStatus);
            $stmt->bindParam(':homeType', $homeType);

            
            $stmt->execute();
        }

        echo "Listings inserted successfully.";
    } catch (PDOException $e) {
      print_r($e);
        echo "Error inserting listings: " . $e->getMessage();
    }
}
?>