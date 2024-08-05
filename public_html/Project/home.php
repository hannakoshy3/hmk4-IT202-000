<?php
require(__DIR__ . "/../../partials/nav.php");
?>

<body class="bg_img home">
    <div class="overlay banner">
        <div class="container-fluid">
            <h1>Home</h1>
        </div>
    </div>
</body>


<?php
if (is_logged_in(true)) {
    //echo "Welcome home, " . get_username();
    //comment this out if you don't want to see the session variables
    error_log("Session data: " . var_export($_SESSION, true));
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>