<?php
if ($_COOKIE['current_table'] == 'unipharma.orders') {
    $dbhandle = NULL;
    $was_connected = TRUE;
    if (!$_SESSION['connected']) {
        $dbhandle = db::connect();
        $was_connected = FALSE;
    } else {
        $dbhandle = $_SESSION['dbhandle'];
    }
    if ($dbhandle) {
        $result = pg_query("SELECT order_reg_id FROM unipharma.orders WHERE order_id = {$_COOKIE['key']}");
        if ($result) {
            $row = pg_fetch_assoc($result);
            $_POST['order_recource_order_id'] = $row['order_reg_id'];
        }
    }
    if (!$was_connected) {
        db::close($dbhandle);
    }
}
