<?php

    require_once './config/Config.php';
    require_once './lib/MySQLComponent.php';

    $id = "";
    if (isset($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
    }
    
    $message = "";
    $error = 0;
    $title = "";
    $status = "";
    $hiduke = "";
    $from = "";
    $to = "";

    // 更新の場合のみ
    if (!empty($id)) {
        try {
            $db = new MySQLComponent(Config::$DB_HOST, Config::$DB_NAME, Config::$DB_USER, Config::$DB_PASSWORD);
            $db->connect();
        
            $sql = "";
            $sql .= "SELECT title, opentime, closetime, status ";
            $sql .= "FROM xml_tbl ";
            $sql .= "WHERE id = ? ";

            $bind_values = [['value' => $id]];

            $rows = $db->select($sql, $bind_values);
            
            foreach ($rows as $index => $row) {
                $title = $row["title"]; 
                $hiduke = substr(str_replace('-', '/', $row["opentime"]), '0', '10');
                $from = substr($row["opentime"], '11', '8');
                $to = substr($row["closetime"], '11', '8');
                $status = $row["status"];
            }
        } catch(Exception $e) {
            $message = 'DBの接続に失敗しました。';
        } finally {
            $db->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php if ($id == ''): ?>
        <title>管理画面（新規登録）</title>
    <?php else: ?>
        <title>管理画面（更新）</title>
    <?php endif ?>    

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <script src="./js/datetime.js"></script>
    <script src="./js/post.js"></script>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="page-header">
            <div class="clearfix">
                <div class="header-left">
                    <?php if ($id == ''): ?>
                        <h2>管理画面 新規登録</h2>
                    <?php else: ?>
                        <h2>管理画面 更新</h2>
                    <?php endif ?>
                </div>           
            </div><!-- crearfix  end -->
        </div><!-- page-header  end -->
        <p>
            <?php if ($id == ''): ?>
                <a href="./list.php">TOP</a> | 新規登録
            <?php else: ?>
                <a href="./list.php">TOP</a> | <a href="./xml_edit.php">新規登録</a>
            <?php endif ?>
        </p>
        <span id="message"></span>
        <span class="text-danger">&nbsp*は必須</span>
        <div class="wrapper-list">
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php if ($id == ''): ?>
                            新規登録
                            <?php else: ?>
                            更新
                            <?php endif ?>                            
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <form role="form">
                                    <div class="col-lg-12">
                                        <div class="col-lg-12">
                                        <p>
                                        <?php if ($id != ''): ?>
                                            <label>ID:&nbsp&nbsp<?php echo $id; ?></label>
                                            <input type="hidden" id="u_id" value="<?php echo $id ?>">
                                        <?php endif ?>
                                        </p>
                                        <label>タイトル</label>
                                        <?php if ($id == ''): ?>
                                            <input class="form-control" id="title" value="タイトル">
                                        <?php else: ?>
                                            <input class="form-control" id="title" value="<?php echo $title ?>">
                                        <?php endif ?>
                                        <br />
                                        </div><!-- /.col-lg-12 (nested) -->
                                    </div><!-- /.col-lg-12 (nested) -->

                                    <div class="col-lg-8">
                                        <div class="col-lg-4">
                                        <label>日付</label><span class="text-danger">&nbsp*</span>
                                        <?php if ($hiduke == ''): ?>
                                        <input class="form-control datepicker" id="hiduke">
                                        <?php else: ?>
                                        <input class="form-control datepicker" id="hiduke" value="<?php echo $hiduke ?>">
                                        <?php endif ?>    
                                        <br />
                                        </div><!-- /.col-lg-4 (nested) -->
                                        <div class="col-lg-4">
                                            <label>開始時刻</label><span class="text-danger">&nbsp*</span>
                                            <?php if ($from == ''): ?>
                                            <input class="form-control timepicker" id="from">
                                            <?php else: ?>
                                            <input class="form-control" id="from" value="<?php echo $from ?>">
                                            <?php endif ?>
                                            <p class="help-block">(hh:mm:ssで入力)</p>
                                            </div><!-- /.col-lg-4 (nested) -->
                                            <div class="col-lg-4">                            
                                            <label>終了時刻</label><span class="text-danger">&nbsp*</span>
                                            <?php if ($to == ''): ?>
                                            <input class="form-control" id="to">
                                            <?php else: ?>
                                            <input class="form-control" id="to" value="<?php echo $to ?>">
                                            <?php endif ?>
                                            <p class="help-block">(hh:mm:ssで入力)</p>
                                            </div><!-- /.col-lg-4 (nested) -->
                                            <div class="col-lg-4">  
                                            <label>ステータス</label>
                                            <select class="form-control" id="status">
                                                <option value="">未選択</option>
                                                <?php if ($status == ''): ?>
                                                <option value="standby">準備中</option>
                                                <option value="open">公開中</option>
                                                <option value="close">公開終了</option>
                                                <?php else: ?>
                                                    <?php if ($status == 'stanby'): ?>
                                                    <option value="standby" selected>準備中</option>
                                                    <option value="open">公開中</option>
                                                    <option value="close">公開終了</option>
                                                    <?php elseif($status == 'open'): ?>
                                                    <option value="standby">準備中</option>
                                                    <option value="open" selected>公開中</option>
                                                    <option value="close">公開終了</option>
                                                    <?php elseif($status == 'close'): ?>
                                                    <option value="standby">準備中</option>
                                                    <option value="open">公開中</option>
                                                    <option value="close" selected>公開終了</option>
                                                    <?php else: ?>
                                                    <option value="standby">準備中</option>
                                                    <option value="open">公開中</option>
                                                    <option value="close">公開終了</option>
                                                    <?php endif ?>                        
                                                <?php endif ?>     
                                            </select>
                                            </div><!-- /.col-lg-4 (nested) -->
                                        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                                        <div class="col-lg-4"> 
                                        <p>
                                            <?php if ($id == ''): ?>
                                                <button type="button" class="btn btn-lg" id="tbl_add" >登録</button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-lg" id="tbl_upd" >更新</button>
                                            <?php endif ?>
                                        </p>
                                        </div><!-- /.col-lg-4 (nested) -->
                                    </div><!-- /.col-lg-8 (nested) -->
                                </form>
                            </div><!-- /.row (nested) -->
                        </div><!-- /.panel-body -->
                    </div><!-- /.panel -->
                </div><!-- /.col-lg-6 -->
            </div><!-- /.row -->
        </div><!-- div wrapper-list  end -->
    </div><!-- container-fluid  end -->
</body>
</html>