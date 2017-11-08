<?php

    require_once './config/Config.php';
    require_once './lib/MySQLComponent.php';
    require_once './lib/CreateXml.php';

    $id = "";
    $title = "";
    $opentime = "";
    $closetime = "";
    $status = "";
    $message = [];
    $error = 0;

    if (isset($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
    }
    if (isset($_REQUEST['title'])) {
        $title = $_REQUEST['title'];
    }
    if (isset($_REQUEST['opentime'])) {
        $opentime = $_REQUEST['opentime'];
    }
    if (isset($_REQUEST['closetime'])) {
        $closetime = $_REQUEST['closetime'];
    }
    if (isset($_REQUEST['status'])) {
        $status = $_REQUEST['status'];
    }

    if (empty($id)) {
        $message = array('status' => '-1', 'message' => '更新データがありません。');
        $error = 1;
        exit();
    }
    $opentime = str_replace('/', '-', $opentime);
    $closetime = str_replace('/', '-', $closetime);
    if (empty($title)) {
        $title = NULL;
    }
    if (empty($status)) {
        $status = NULL;
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
            $message = array('status' => '-1', 'message' => '更新データがありません。');
            $error = 1;
        }

        if ($error != 1) {
            $db->beginTransaction();

            $sql = "update xml_tbl set ";
            $sql .= "title = ?, opentime = ?, closetime = ?, status = ?, ";
            $sql .= "update_date = now() ";
            $sql .= "where id = ? ";

            $bind_values = [
                               ['value' => $title], 
                               ['value' => $opentime], 
                               ['value' => $closetime],
                               ['value' => $status],
                               ['value' => $id]
                           ];
            $db->insert($sql, $bind_values); // insert()と同一関数でOK
            $db->commit();
        }
    } catch(PDOException $e) {
        $db->rollback();
        $message = array('status' => '-1', 'message' => 'DB更新に失敗しました。');
        $error = 1;
    } finally {
        $db->close();
    }

    // xml出力
    $xml = new Xml();
    $dir_name = substr(str_replace('-', '', $opentime), '2', '6');
    $otime = str_replace('-', '/', $opentime);
    $ctime = str_replace('-', '/', $closetime);
    $result = $xml->createXML($dir_name, $title, $otime, $ctime, $status);

    if ($result != "OK") {
        $message = array('status' => '-1', 'message' => $result);
        $error = 1;
    }

    if ($error == 0) {
        $message = array('status' => '0', 'message' => '');
    }
    print json_encode($message);
    
?>