CREATE TABLE IF NOT EXISTS `IT202_S24_Realty` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `zipcode` VARCHAR(10) NOT NULL,
    `city` VARCHAR(50) NOT NULL,
    `county` VARCHAR(50) NOT NULL,
    `country` VARCHAR(50) NOT NULL,
    `state` VARCHAR(50) NOT NULL,
    `streetAddress` VARCHAR(100) NOT NULL,
    `dateSoldString` VARCHAR(50) NOT NULL,
    `datePriceChanged` VARCHAR(50) NOT NULL,
    `datePostedString` VARCHAR(50) NOT NULL,
    `bathrooms` INT NOT NULL,
    `bedrooms` INT NOT NULL,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE(`zipcode`, `streetAddress`)
);