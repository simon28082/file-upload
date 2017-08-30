<?php

namespace CrCms\Upload\Exceptions;

/**
 * Class UploadException
 *
 * @package CrCms\Upload\Exceptions
 * @author simon
 */
class UploadException extends \RuntimeException
{
    /**
     * 非正常上传文件
     * @var integer
     * @author simon
     */
    const IS_NOT_UPLOAD_FILE = 99;

    /**
     * 无法写入临时目录
     * @var integer
     * @author simon
     */
    const WRITER_ERR_NO_TMP_DIR = 100;

    /**
     * 读取文件流失败
     * @var integer
     * @author simon
     */
    const READ_FILE_STREAM_ERR = 101;

    /**
     * 文件移动失败
     * @var integer
     * @author simon
     */
    const MOVE_TMP_FILE_ERR = 102;

    /**
     * UploadException constructor.
     * @param string $filename
     * @param int $error
     */
    public function __construct($filename, $error)
    {
        parent::__construct(sprintf("%s upload error %s", $filename, $this->handleError($error)));
    }

    /**
     * @param int $error
     * @return int|string
     */
    protected function handleError(int $error)
    {
        switch ($error) {
            case UPLOAD_ERR_CANT_WRITE:
                $error = '文件写入失败';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = '文件只有部分被上传';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error = '文件大小超过表单大小限制';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $error = '文件大小超过服务器大小限制';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error = '没有文件被上传';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error = '找不到临时文件夹';
                break;
            case static::IS_NOT_UPLOAD_FILE:
                $error = '非正常上传文件！';
                break;
            case static::READ_FILE_STREAM_ERR:
                $error = '读取文件流失败！';
                break;
            case static::MOVE_TMP_FILE_ERR:
                $error = '移动文件夹失败！';
                break;
        }

        return $error;
    }
}