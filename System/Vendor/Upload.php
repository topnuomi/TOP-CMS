<?php
/**
 * @author TOP糯米 2017
 */

namespace Vendor;

/**
 * 文件上传类
 * @author TOP糯米
 */
class Upload {
    private static $instance;
    private static $fileType;
    private static $dirName;
    private $error;

    private function __construct() {
    }
    
    /**
     * 静态调用时传入保存目录以及文件类型
     * @param string $dirName
     * @param string $fileType
     * @return \Vendor\Upload
     */
    public static function init($dirName = '', $fileType = '') {
        if (!self::$instance) {
            self::$instance = new self();
        }
        self::$dirName = $dirName;
        self::$fileType = ($fileType) ? $fileType : ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
        return self::$instance;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 上传图片
     * @param string $fileName
     * @return string|boolean
     */
    public function uploadPicture($fileName = '') {
        $verifyToken = md5('unique_salt' . $_POST['timestamp']);
        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $type = getimagesize($tempFile)['mime'];
            $targetPath = self::$dirName;
            if (!is_dir($targetPath)) mkdir($targetPath, 0777, true);
            $targetFile = rtrim($targetPath, '/') . '/' . ((!$fileName) ? $_FILES['Filedata']['name'] : $fileName);
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            if (in_array($fileParts['extension'], self::$fileType)) {
                if (move_uploaded_file($tempFile, $targetFile . '.' . $fileParts['extension'])) {
                    return rtrim(self::$dirName, '/') . '/' . $fileName . '.' . $fileParts['extension'];
                } else {
                    $this->error = '上传失败';
                    return false;
                }
            }
            $this->error = '文件类型不被允许';
            return false;
        }
    }

    public function uploadFile($fileName = '') {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        $targetPath = self::$dirName;
        if (!is_dir($targetPath)) mkdir($targetPath, 0777, true);
        $targetFile = rtrim($targetPath, '/') . '/' . ((!$fileName) ? $_FILES['Filedata']['name'] : $fileName);
        if (in_array($fileParts['extension'], self::$fileType)) {
            if (move_uploaded_file($tempFile, $targetFile . '.' . $fileParts['extension'])) {
                return rtrim(self::$dirName, '/') . '/' . $fileName . '.' . $fileParts['extension'];
            } else {
                $this->error = '上传失败';
                return false;
            }
        }
        $this->error = '文件类型不被允许';
        return false;
    }
}