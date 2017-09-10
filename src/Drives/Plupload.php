<?php

namespace CrCms\Upload\Drives;

use CrCms\Upload\Contracts\FileUpload;
use CrCms\Upload\Exceptions\UploadException;
use CrCms\Upload\File;
use CrCms\Upload\Traits\ExtensionTrait;
use CrCms\Upload\Traits\FileTrait;
use CrCms\Upload\Traits\MimeTrait;
use CrCms\Upload\Traits\SizeTrait;
use Illuminate\Config\Repository as Config;

class PlUpload implements FileUpload
{
    use SizeTrait, MimeTrait, ExtensionTrait;
    use FileTrait {
        setUploadFile as parentSetUploadFile;
    }

    /**
     * current chunk
     *
     * @var integer
     */
    protected $chunk = 0;

    /**
     * total chunk
     *
     * @var integer
     */
    protected $chunks = 0;

    /**
     * 上传的字节数据，也检测是否上传完成
     *
     * @var integer
     */
    protected $status = 0;

    /**
     * @var Config
     */
    protected $config;

    /**
     * PlUpload constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $file
     * @return array
     */
    public function upload(array $file): array
    {
        $this->setUploadFile($file);

        $this->checkUploadFile();

        $file = $this->moveUploadFile();
        if ($file instanceof File) {
            return $this->getUploadInfo($file);
        }

        return [];
    }

    /**
     * @param array $file
     * @return FileTrait
     */
    protected function setUploadFile(array $file)
    {
        $file['name'] = $_REQUEST[$this->config->get('upload.drives.plupload.old_name')];
        return $this->parentSetUploadFile($file);
    }

    /**
     * @return void
     */
    protected function checkUploadFile()
    {
        $this->checkUploadedFile();

        $this->checkUploadSelf();

        /* 一个是验证总大小,一个是验证块大小 */
        $this->checkSize($this->size);
        $this->checkSize(intval($_REQUEST[$this->config->get('upload.drives.plupload.size_name')]));

        $this->checkExtension($this->extension);

        $this->checkMime($this->file->getMime());
    }

    /**
     * 分块上传大小设置
     */
    protected function setChunking()
    {
        $chunkName = $_REQUEST[$this->config->get('upload.drives.plupload.chunk_name')];
        $chunksName = $_REQUEST[$this->config->get('upload.drives.plupload.chunks_name')];

        $this->chunk = isset($_POST[$chunkName]) ? intval($_POST[$chunkName]) : 0;
        $this->chunks = isset($_POST[$chunksName]) ? intval($_POST[$chunksName]) : 0;
    }

    /**
     * @return void
     */
    protected function writeFile()
    {
        //块上传大小
        $this->setChunking();

        //读取并写入数据流
        if (!(boolean)$fpIn = fopen($this->temp, "rb")) {
            throw new UploadException($this->name, UploadException::READ_FILE_STREAM_ERR);
        }

        //获取文件路径
        $file = $this->getTempFile();

        //读取文件
        if (!(boolean)@$fpOut = fopen($file, $this->chunks ? "ab" : "wb")) {
            //关闭文件流
            fclose($fpIn);
            throw new UploadException($this->name, UploadException::WRITER_ERR_NO_TMP_DIR);
        }

        //循环按照指定字节读取文件
        while ((boolean)$buff = fread($fpIn, 4096)) {
            $this->status = fwrite($fpOut, $buff);
        }

        //关闭文件流
        fclose($fpOut);
        fclose($fpIn);
    }

    /**
     * @return File
     */
    protected function moveUploadFile()
    {
        //自动创建目录
        $this->createDirToFullPath();

        $this->writeFile();

        //上传完成
        if ($this->status && (!$this->chunks || $this->chunk == $this->chunks - 1)) {
            rename($this->getTempFile(), $this->getFullPath());
            return new File($this->getFullPath());
        }
    }

    /**
     * @return string
     */
    protected function getTempFile(): string
    {
        return $this->getFullPath() . '.part';
    }

    /**
     * 清理目录临时文件
     */
//    private function _clearTempFile()
//    {
//        $filelists = scandir($this->_savePath);
//        foreach ($filelists as $fvalue) {
//            if ($fvalue === '.' || $fvalue === '..') continue;
//            $tmpfilePath = $this->_savePath . DIRECTORY_SEPARATOR . $fvalue;
//            // If temp file is current file proceed to the next
//            if ($tmpfilePath === "{$this->__newFileName}.part") {
//                continue;
//            }
//            // Remove temp file if it is older than the max age and is not the current file
//            if (preg_match('/\.part$/', $fvalue) && (filemtime($tmpfilePath) < time() - $this->_maxFileAge)) {
//                @unlink($tmpfilePath);
//            }
//        }
//    }

//    /**
//     * 重命名文件
//     * @param unknown $filename
//     */
//    private function _renameFile($filename)
//    {
//        //完成文件读取后，去除分区临时文件名.part
//        if (!$this->__chunks || $this->__chunk == $this->__chunks - 1) {
//            $newname = substr($filename, 0, -5);
//            if (rename($filename, $newname)) {
//                $this->__newFileName = basename($newname);
//                //设置上传完成标识
//                $this->__status = true;
//            }
//        }
//    }
}