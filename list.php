<?php

    require_once './config/Config.php';
    require_once './lib/MySQLComponent.php';

    // page
    define("INDEX_PER_PAGE", 5); // 1ページあたりのボタン表示数
    define("LINE_PER_PAGE", 20);  // 1ページあたりの表示行数

    $columnList = [
        '0' => 'ID',
        '1' => 'タイトル',
        '2' => '開始時刻',
        '3' => '終了時刻',
        '4' => 'ステータス',
        '5' => '編集/削除',
        '6' => 'リンク'
    ];

    $currentPage = 1; // 現在のページ
    $count = 0;      // 取得レコード件数
    $totalPage = 1;  // 全体のページ数  
    
    $html = '';
    $page = '';

    $errorMsg = '';

    if (isset($_GET["page"])) {
        $currentPage = $_GET["page"];
    }

    // DB接続
    try {
        $db = new MySQLComponent(Config::$DB_HOST, Config::$DB_NAME, Config::$DB_USER, Config::$DB_PASSWORD);
        $db->connect();

        $sql = "SELECT COUNT(*) FROM xml_tbl ";
        $sql .= "WHERE del_flg = 0 ";

        $count = $db->getCount($sql);
        $offset = ($currentPage - 1) * LINE_PER_PAGE;

        $sql = "";
        $sql .= "SELECT id, title, opentime, closetime, status ";
        $sql .= "FROM xml_tbl ";
        $sql .= "WHERE del_flg = 0 ";
        $sql .= "ORDER BY DATE_FORMAT(opentime, '%Y-%m-%d') DESC, DATE_FORMAT(opentime, '%k:%i:%s'), DATE_FORMAT(closetime, '%k:%i:%s') ";
        $sql .= "LIMIT " . $offset . ", " . LINE_PER_PAGE . " ";

        $rows = $db->select($sql);

        foreach ($rows as $index => $row) {
            $html .= '<tr>';
            // ID
            // $html .= '<td>' . ($offset + $index + 1) . '</td>';
            $html .= '<td>' . $row["id"] . '</td>';
            // タイトル
            $html .= '<td>' . $row["title"] . '</td>';
            // 開始時刻
            $html .= '<td>' . str_replace('-', '/', $row["opentime"]) . '</td>';
            // 終了時刻
            $html .= '<td>' . str_replace('-', '/', $row["closetime"]) . '</td>';
            // ステータス
            $html .= '<td>' . $row["status"] . '</td>';
            // ダウンロード
            $html .= '<td>';
            $html .= '<a href="./xml_edit.php?id='. $row["id"] . '"><button type="button" class="btn btn-default">編集</button></a>&nbsp<button type="button" class="btn btn-default" onclick="deleteTable(' . $row["id"] . ',' . substr(str_replace('-', '', $row["opentime"]), '2', '6') . ')">削除</button>';
            $html .= '</td>';
            // リンク
            $html .= '<td>';
            $html .= '<a href="#" target="_blank"><i class="glyphicon glyphicon-circle-arrow-right">リンク</i></a>';
            $html .= '</td>';
            $html .= '</tr>'; 
        }

        $db->close();

        // ページング処理
        $page .= '<ul class="pagination">';

        // 総ページ数
        if ($count > 0) {
            $totalPage = ceil($count / LINE_PER_PAGE);
        }

        if ($currentPage == 1) {
            $page .= '<li class="disabled"><a href="#"> prev </a></li>';
        } else {
            $currentStartPage = $currentPage - 1; // 現在の先頭ページ
            $page .= '<li><a href="?page='. $currentStartPage . '" > prev </a></li>';
        }

        // 最終ページの調整
        $start = $currentPage;
 
        if (INDEX_PER_PAGE > $totalPage){
             $start = 1;
        } else {
            if (($currentPage + INDEX_PER_PAGE - 1) > $totalPage) {
                $tmp_page = ($currentPage + INDEX_PER_PAGE - 1) - $totalPage;
                $start = $currentPage - $tmp_page;
            } 
        }

        for ($i = $start; $i <= ($start + INDEX_PER_PAGE - 1) && $i <= $totalPage; $i++) {
            if ($currentPage == $i) {
                $page .= '<li class="active"><a href="?page='. $i . '" >' . $i . '</a></li> ';
            } else {
                $page .= '<li><a href="?page='. $i . '" > ' . $i . ' </a></li> ';
            }
        }       

        if ($currentPage == $totalPage) {
            $page .= '<li class="disabled"><a href="#"> next </a></li>';
        } else {
            $page .= '<li><a href="?page='. ($currentPage + 1) . '" > next </a></li>';
        }
        
        $page .= '</ul>';

    } catch(PDOException $e) {
        $errorMsg = 'DB接続エラーです。';
    } finally {
        $db->close();
    }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>管理画面（一覧）</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="./js/post.js"></script>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="page-header">
            <div class="clearfix">
                <div class="header-left">
                    <h2>管理画面</h2>
                </div>
            </div><!-- crearfix  end -->            
        </div><!-- page-header  end -->
        <p>
            <?php if ($currentPage == '1' || $currentPage == ''): ?>
                TOP | <a href="./xml_edit.php">新規登録</a>
                        <?php else: ?>
                <a href="./list.php">TOP</a> | <a href="./xml_edit.php">新規登録</a>
            <?php endif ?>
        </p>
        <div class="wrapper-list">
            <?php if($errorMsg != ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMsg; ?>
                </div>
            <?php endif ?>
            <span id="message"></span>
            <div class="list-contents">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <?php 
                                    foreach ($columnList as $key => $col) {
                                        echo '<th>'.$col.'</th>';
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if(isset($html)) echo $html;
                            ?>
                        </tbody>
                        <!-- <tfoot></tfoot> -->
                    </table>
                    <div class="list-paginate">
                        <?php 
                            if(isset($page)) echo $page;
                        ?>
                    </div>
                </div><!-- table-responsive  end -->
            </div><!-- list-contents  end-->
        </div><!-- div wrapper-list  end -->
    </div><!-- container-fluid  end -->
</body>
</html>