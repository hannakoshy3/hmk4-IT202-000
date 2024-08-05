<?php

function fetch_realty_listings($location, $min_bedrooms = null, $min_bathrooms = null, $min_price = null, $max_price = null )
{
    $data = ["location" => $location, "output" => "json"];
    if(isset($min_bedrooms)) {
        $data["beds_min"] = $min_bedrooms;

    } 
    if(isset($min_bathrooms)) {
        $data["baths_min"] = $min_bathrooms;

    } 
   

    if(isset($min_price)) {
        $data["price_min"] = $min_price;

    } 
    if(isset($max_price)) {
        $data["price_max"] = $max_price;

    } 
   

    $endpoint = "https://zillow56.p.rapidapi.com/search";
    $isRapidAPI = true;
    $rapidAPIHost = "zillow56.p.rapidapi.com";
    $result = get($endpoint, "REALTY_API", $data, $isRapidAPI, $rapidAPIHost);
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
 
    $processed_results = [];
    if (isset($result["results"])) {
        foreach ($result["results"] as $listing) {
            $processed_listing = [
                "zipcode" => $listing["zipcode"] ?? null,
                "city" => $listing["city"] ?? null,
                "country" => $listing["country"] ?? null,
                "state" => $listing["state"] ?? null,
                "streetAddress" => $listing["streetAddress"] ?? null,
                "bathrooms" => $listing["bathrooms"] ?? null,
                "bedrooms" => $listing["bedrooms"] ?? null,
                "price" => $listing["price"] ?? null,
                "location" => $listing["location"] ?? null,
                "lotAreaValue" => $listing["lotAreaValue"] ?? null,
                "homeStatus" => $listing["homeStatus"] ?? null,
                "homeType" => $listing["homeType"] ?? null
            ];
            $processed_results[] = $processed_listing;
        }
    }
   
    return $processed_results;
}