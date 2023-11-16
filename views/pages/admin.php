<?php

use Firebase\JWT\JWT;

$title = "Admin";

$METABASE_SITE_URL = "";
$METABASE_SECRET_KEY = "";

$payload = array(
    "resource" => array("dashboard" => 1),
    "params" => new stdClass(),
    "exp" => time() + (10 * 60) // 10 minute expiration
);

$alg = 'HS256'; // Spécifiez l'algorithme ici
$token = JWT::encode($payload, $METABASE_SECRET_KEY, $alg);

$iframeUrl = $METABASE_SITE_URL . "/embed/dashboard/" . $token . "#bordered=true&titled=true";
?>

<iframe
        src="<?php echo htmlspecialchars($iframeUrl); ?>"
        width="100%"
        height="1000"
        allowtransparency
></iframe>
