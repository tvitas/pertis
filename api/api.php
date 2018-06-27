<?php
$sku = $_GET['sku'];
$product = [];

if ($sku) {
    $product = getProduct($sku);
    echo $product;
} else {
    echo json_encode($product);
}
exit;

function getProduct($sku) {
    require("../lib/etc/connections.conf");
    $data = [];
    $connstring = 'host=' . $host . ' port=' . $port . ' dbname=' . $api_db . ' user=' . $db_user . ' password=' . $db_password;
	$dbhandle = pg_connect($connstring);
    if ($dbhandle) {
        $query = "SELECT product_title || ' ' || product_ser_no AS product_title,
        dim_title || '(' || dim_notes || ')' AS product_dim_title
        FROM unipharma.products
        LEFT JOIN lists.dimensions ON product_dim_id = dim_id";
        if ($sku !== 'all') {
            $query .= " WHERE product_no = '$sku'";
        }
        $result = pg_query($query);
        if (pg_num_rows($result)) {
            $data = pg_fetch_assoc($result);
        }
        pg_close($dbhandle);
    }
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}










