<?php

class FtpControl
{
    private $curlTimeout;
    private $uploadServerInfo;

    public function __construct()
    {
        $this->curlTimeout = '30';
        $this->uploadServerInfo = array(
            'protocol' => 'ftp',
            'host' => '[hostname]',
            'port' => '[port]',
            'user' => '[user]',
            'password' => '[pass]'
        );
    }

    public function fileUpload($localPath, $remotePath)
    {
        try {
            //接続情報
            $protocol = $this->uploadServerInfo['protocol'];
            $host = $this->uploadServerInfo['host'];
            $port = $this->uploadServerInfo['port'];
            $user = $this->uploadServerInfo['user'];
            $password = $this->uploadServerInfo['password'];

            // ファイルオープン
            $file = fopen($localPath, 'r');
            // CURL設定
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $protocol . '://' . $user . ':' . $password . '@' . $host . $remotePath);
            if ($protocol == 'sftp') {
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
            } elseif ($protocol == 'ftp') {
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_FTP);
            }
            curl_setopt($ch, CURLOPT_UPLOAD, true);
            curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, true);
            curl_setopt($ch, CURLOPT_INFILE, $file);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localPath));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);

            // CURL実行
            curl_exec($ch);

            // エラーコード取得
            $error_msg = curl_error($ch);
            $error_no = curl_errno($ch);

            // ファイルクローズ
            fclose($file);
            // CURLクローズ
            curl_close($ch);

            return array('errorNo' => $error_no, 'errorMsg' => $error_msg);
        } catch (Exception $e) {
            error_log(print_r($e, true));
            return array('errorNo' => -1, 'errorMsg' => $e->getMessage());
        }
    }

    public function fileDelete($remotePath)
    {
        try {
            //接続情報
            $protocol = $this->uploadServerInfo['protocol'];
            $host = $this->uploadServerInfo['host'];
            $port = $this->uploadServerInfo['port'];
            $user = $this->uploadServerInfo['user'];
            $password = $this->uploadServerInfo['password'];

            // CURL設定
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $protocol . '://' . $user . ':' . $password . '@' . $host);
            if ($protocol == 'sftp') {
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
            } elseif ($protocol == 'ftp') {
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_FTP);
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_QUOTE, array('DELE ' . $remotePath));          

            // CURL実行
            curl_exec($ch);

            // エラーコード取得
            $error_msg = curl_error($ch);
            $error_no = curl_errno($ch);

            // CURLクローズ
            curl_close($ch);

            return array('errorNo' => $error_no, 'errorMsg' => $error_msg);
        } catch (Exception $e) {
            error_log(print_r($e, true));
            return array('errorNo' => -1, 'errorMsg' => $e->getMessage());
        }
    }
};
