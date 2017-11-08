$(function() {
     
    $('#tbl_add').click(function() {
        // 必須チェック
        var hiduke = $('#hiduke').val();
        var from = $('#from').val();
        var to = $('#to').val();

        $("#message").html("");

        if (hiduke == "") {
            alert('日付を入力してください。');
            return false;
        }

        if (from == "") {
            alert('開始時刻を入力してください。');
            return false;
        }

        if (to == "") {
            alert('終了時刻を入力してください。');
            return false;
        }

        if(!ckDate(hiduke)) {
            alert('日付のフォーマットが正しくありません。');
            return false;
        }

        if(!ckTime(from)) {
            alert('開始時刻のフォーマットが正しくありません。');
            return false;
        }

        if(!ckTime(to)) {
            alert('終了時刻のフォーマットが正しくありません。');
            return false;
        }

        var opentime = new Date(hiduke.substr(0,4) + "/" + hiduke.substr(5,2) + "/" + hiduke.substr(8,2) + " " 
                   + from.substr(0,2) + ":" + from.substr(3,2) + ":" + from.substr(6,2));                                             
        var closetime = new Date(hiduke.substr(0,4) + "/" + hiduke.substr(5,2) + "/" + hiduke.substr(8,2) + " " 
                   + to.substr(0,2) + ":" + to.substr(3,2) + ":" + to.substr(6,2)); 
                                                           
        if (opentime >= closetime) {
            alert("終了時刻は開始時刻より後でなければなりません。");
            return false;
        }       
    
        var hostUrl= './tbl_insert.php';
        var title = $("#title").val();
        var status = $("#status").val();

        $.ajax({
            type: "post",
            url: hostUrl,
            dataType : 'text',
            data: {
                "title": title, 
                "opentime": hiduke + " " + from, 
                "closetime": hiduke + " " + to, 
                "status": status
            }
        }).done(function (data) {          
            var json_data = $.parseJSON(data);
            
            if ('status' in json_data) {
                switch (json_data['status']) {
                    case '0':
                        alert('登録しました。');
                        break;
                    case '-1':
                       var html = '<div class="alert alert-danger" role="alert">';
                       html += json_data['message'];
                       html += '</div>';
                       $("#message").append(html);
                       break;            
                }
            }
        }).fail(function (data) {
            var html = '<div class="alert alert-danger" role="alert">';
                html += "通信エラーが発生しました。";
                html += '</div>';
            $("#message").append(html);
        });
    });

    $('#tbl_upd').click(function() {
        // 必須チェック
        var hiduke = $('#hiduke').val();
        var from = $('#from').val();
        var to = $('#to').val();

        $("#message").html("");

        if (hiduke == "") {
            alert('日付を入力してください。');
            return false;
        }

        if (to == "") {
            alert('開始時刻を入力してください。');
            return false;
        }

        if (closetime == "") {
            alert('終了時刻を入力してください。');
            return false;
        }

        if(!ckDate(hiduke)) {
            alert('日付のフォーマットが正しくありません。');
            return false;
        }

        if(!ckTime(from)) {
            alert('開始時刻のフォーマットが正しくありません。');
            return false;
        }

        if(!ckTime(to)) {
            alert('終了時刻のフォーマットが正しくありません。');
            return false;
        }

        var opentime = new Date(hiduke.substr(0,4) + "/" + hiduke.substr(5,2) + "/" + hiduke.substr(8,2) + " " 
                   + from.substr(0,2) + ":" + from.substr(3,2) + ":" + from.substr(6,2));                                             
        var closetime = new Date(hiduke.substr(0,4) + "/" + hiduke.substr(5,2) + "/" + hiduke.substr(8,2) + " " 
                   + to.substr(0,2) + ":" + to.substr(3,2) + ":" + to.substr(6,2)); 
                                                           
        if (opentime >= closetime) {
            alert("終了時刻は開始時刻より後でなければなりません。");
            return false;
        }       
   
        var hostUrl= './tbl_update.php';
        var id = $("#u_id").val()
        var title = $("#title").val();
        var status = $("#status").val();

        $.ajax({
            type: "post",
            url: hostUrl,
            dataType : 'text',
            data: {
                "id": id, 
                "title": title, 
                "opentime": hiduke + " " + from, 
                "closetime": hiduke + " " + to, 
                "status": status
            }
        }).done(function (data) {
            var json_data = $.parseJSON(data);
            
            if ('status' in json_data) {
                switch (json_data['status']) {
                    case '0':
                        alert('更新しました。');
                        break;
                    case '-1':
                       var html = '<div class="alert alert-danger" role="alert">';
                       html += json_data['message'];
                       html += '</div>';
                       $("#message").append(html);
                       break;
                }
            }
        }).fail(function (data) {
            var html = '<div class="alert alert-danger" role="alert">';
                html += "通信エラーが発生しました。";
                html += '</div>';
            $("#message").append(html);
        });
    });
});

function ckDate(datestr) {
    if (datestr.length != 10) {
        return false;
    }

    // 日付書式チェック 
    if (!datestr.substr(0,10).match(/^\d{4}\/\d{2}\/\d{2}$/)) {
        return false;
    }

    var vYear = datestr.substr(0, 4) - 0;
    // Javascriptは、0-11で表現
    var vMonth = datestr.substr(5, 2) - 1;
    var vDay = datestr.substr(8, 2) - 0;
    // 月,日の妥当性チェック
    if (vMonth >= 0 && vMonth <= 11 && vDay >= 1 && vDay <= 31) {
    var vDt = new Date(vYear, vMonth, vDay);
        if(isNaN(vDt)){
            return false;
        } else if (vDt.getFullYear() == vYear && vDt.getMonth() == vMonth  && vDt.getDate() == vDay) {
            // OK
        } else {
            return false;
        }
    } else {
        return false;
    }

    return true;
}

function ckTime(timestr) {
    if (timestr.length != 8) {
        return false;
    }

    // 時刻チェック
    if (!timestr.substr(0,8).match(/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/)) {
        return false;
    }

    return true;
}

function deleteTable(id, dir_name) {

    if (!window.confirm('削除してもよろしいですか？')){
        return false;
    } 

    var hostUrl= './tbl_delete.php';
    $("#message").html("");

    $.ajax({
        type: "post",
        url: hostUrl,
        dataType : 'text',
        data: {
            "id": id,
            "dir_name": dir_name
        }
    }).done(function (data) {
        var json_data = $.parseJSON(data);

        if ('status' in json_data) {
            switch (json_data['status']) {
                case '0':
                    alert('削除しました。');
                    location.reload();
                    break;
                case '-1':
                    var html = '<div class="alert alert-danger" role="alert">';
                    html += json_data['message'];
                    html += '</div>';
                    $("#message").append(html);
                    break;
            }
        }
    }).fail(function (data) {
        var html = '<div class="alert alert-danger" role="alert">';
            html += "通信エラーが発生しました。";
            html += '</div>';
        $("#message").append(html);
    });
}
