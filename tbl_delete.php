<?php

    require_once './config/Config.php';
    require_once './lib/MySQLComponent.php';
    require_once './lib/CreateXml.php';

    $id = "";
    $message = [];
    $error = 0;

    if (isset($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
    }

    if (isset($_REQUEST['dir_name'])) {
        $dir_name = $_REQUEST['dir_name'];
    }

    if (empty($id)) {
        $message = array('status' => '-1', 'message' => '削除データがありません。');
        $error = 1;
        exit();
    }

    try {
        $db = new MySQLComponent(Config::$DB_HOST, Config::$DB_NAME, Config::$DB_USER, Config::$DB_PASSWORD);
        $db->connect();
     
        $sql = "";
        $sql .= "SELECT count(*) as count FROM xml_tbl ";
        $sql .= "WHERE del_flg = 0 AND id = ? ";
        $bind_values = [['value' => $id]];

        $rows = $db->select($sql, $bind_values);

        if ($rows[0]['count'] <= 0) {
            $message = array('status' => '-1', 'message' => '削除データがありません。');
            $error = 1;
        }

        if ($error != 1) {
            $db->beginTransaction();

            $sql = "update xml_tbl set ";
            $sql .= "del_flg = ?, ";
            $sql .= "update_date = now() ";
            $sql .= "where id = ? ";

            $bind_values = [
                             ['value' => '1'],
                             ['value' => $id]
                           ];
            $db->insert($sql, $bind_values); // insert()と同一関数でOK
            $db->commit();
        }
    } catch(PDOException $e) {
        $db->rollback();
        $message = array('status' => '-1', 'message' => 'DB削除に失敗しました。');
        $error = 1;
    } finally {
        $db->close();
    }

    // xml出力
    $xml = new Xml();
    
    $result = $xml->deleteXML($dir_name);

    if ($result != "OK") {
        $message = array('status' => '-1', 'message' => $result);
        $error = 1;
    }

    if ($error == 0) {
        $message = array('status' => '0', 'message' => '');
    }

    print json_encode($message);
    
?>