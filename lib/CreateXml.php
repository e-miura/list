<?php
ini_set('display_errors', 0);

require_once('FtpControl.php');

class Xml {
    // file path
    const TMP_FILE_PATH = "./xml/";
    const FILE_NAME = "TEST.xml";
    const UPLOAD_DIR1 = "/tmp/";
    const UPLOAD_DIR2 = "/xml/";

    function createXML($dir_name, $title = '', $opentime, $closetime, $status = '') {

        $tmpFile = self::TMP_FILE_PATH . self::FILE_NAME;

        // XMLファイル作成
        if (!file_exists(self::TMP_FILE_PATH)) {
            return "XML作成に失敗しました。";
        }

        if (file_exists($tmpFile)) {
            if (!unlink($tmpFile)){
                return "XMLが使用中またはアクセス権限がないため作成できませんでした。";
            }
        }

        // Domを生成
        $dom = new DomDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        // 元になるノードの追加
        $root = $dom->appendChild($dom->createElement('root'));
        $xxxx = $root->appendChild($dom->createElement('xxxx'));
        $xxx = $root->appendChild($dom->createElement('xxx'));

        // ノードの追加
        $xxxx->appendChild($dom->createElement('title', $title));
        $xxxx->appendChild($dom->createElement('opentime', $opentime));
        $xxxx->appendChild($dom->createElement('closetime', $closetime));
        $xxxx->appendChild($dom->createElement('status', $status));
        $xxx->appendChild($dom->createElement('title', $title));
        $xxx->appendChild($dom->createElement('opentime', $opentime));
        $xxx->appendChild($dom->createElement('closetime', $closetime));
        $xxx->appendChild($dom->createElement('status', $status));
        $file = $xxx->appendChild($dom->createElement('file'));
        $filevalue = "file.html";
        $file->setAttribute('value', $filevalue);

        $dom->save($tmpFile);

        // XML FTP転送
        if (!file_exists($tmpFile)) {  // XML作成できなかった場合はエラー
            return "XML作成に失敗しました。";
        }

        $ftp = new FtpControl();
        $remote = self::UPLOAD_DIR1 . $dir_name . self::UPLOAD_DIR2 . self::FILE_NAME;
        $ftpResult = $ftp->fileUpload($tmpFile, $remote);

        if ($ftpResult['errorNo'] != '0') {
            return "XMLの転送に失敗しました。";
        }

        return "OK";
    }

    function deleteXML($dir_name) {
        $ftp = new FtpControl();
        $remote = self::UPLOAD_DIR1 . $dir_name . self::UPLOAD_DIR2 . self::FILE_NAME;        

        $ftpResult = $ftp->fileDelete(self::UPLOAD_DIR1 . $dir_name . self::UPLOAD_DIR2 . self::FILE_NAME);

        if ($ftpResult['errorNo'] != '0') {
            return "XMLが削除できませんでした。";
        }

        return "OK";
    }
}
?>