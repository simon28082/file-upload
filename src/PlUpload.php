<?php

namespace CrCms\Upload;

use CrCms\Upload\Exceptions\SizeException;
use CrCms\Upload\Exceptions\UploadException;
use CrCms\Upload\Contracts\FileUpload as FileUploadContract;
use Illuminate\Contracts\Config\Repository as Config;

class PlUpload extends FileUpload implements FileUploadContract
{
    protected $currentChunk;

    protected $totalChunk;

    protected $status = 0;


    public function __construct(Config $config,UploadHandler $uploadHandle)
    {
        parent::__construct($config,$uploadHandle);
        $this->defaultHeader();
        $this->setChunk();
    }


    /**
     * @param array $fileInfo
     * @param string $path
     * @return array
     */
    public function handle(array $fileInfo): array
    {
        $this->uploadHandler->setUploadFile($fileInfo);//or
        //$this->setName()->setTemp()->setSize()->setError()->setExtension();

        $this->setName();

        //$this->uploadHandler->checkUploadFile();//or
        $this->uploadHandler->checkFileExtension()->checkFileMime()->checkUploadedFile()->checkUploadSelf();
        $this->checkFileSize();

        $this->uploadHandler->upload();
//        $this->uploadHandler->setFullPath();
//        $this->moveUploadFile();

        return $this->uploadHandler->getUploadInfo();
    }

    /**
     *
     */
    protected function checkFileSize()
    {
        if ($this->uploadHandler->getFileSize() < intval($_REQUEST[$this->config->get('upload.plupload.size_name')])) {
            throw new SizeException($this->uploadHandler->getName());
        }
    }

    /**
     * @return PlUpload
     */
    public function setName(): self
    {
        $this->uploadHandler->setName(addslashes($_REQUEST[$this->config->get('upload.plupload.old_name')]));

        return $this;
    }


    /**
     * 分块上传大小设置
     */
    public function setChunk()
    {
        $this->currentChunk = isset($_REQUEST[$this->config->get('upload.plupload.chunk_name')]) ? intval($_REQUEST[$this->config->get('upload.plupload.chunk_name')]) : 0;
        $this->totalChunk = isset($_REQUEST[$this->config->get('upload.plupload.chunks_name')]) ? intval($_REQUEST[$this->config->get('upload.plupload.chunks_name')]) : 0;
    }

    /**
     * 清理目录临时文件
     */
//    private function clearFile() {
//        $filelists = scandir($this->_savePath);
//        foreach ($filelists as $fvalue) {
//            if ($fvalue === '.' || $fvalue==='..') continue;
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


    protected function writeFile(): self
    {
        //读取并写入数据流
        if (!(boolean)$fpIn = fopen($this->uploadHandler->getTemp(), "rb")) {
            throw new UploadException($this->uploadHandler->getName(), UploadException::READ_FILE_STREAM_ERR);
        }

        //读取文件
        if (!(boolean)$fpOut = fopen($this->getTempFile(), $this->totalChunk ? "ab" : "wb")) {
            //关闭文件流
            fclose($fpIn);
            throw new UploadException($this->uploadHandler->getName(), UploadException::WRITER_ERR_NO_TMP_DIR);
        }

        //循环按照指定字节读取文件
        while ((boolean)$buff = fread($fpIn, 4096)) {
            $this->status = fwrite($fpOut, $buff);
        }

        //关闭文件流
        @fclose($fpOut);
        @fclose($fpIn);

        return $this;
    }


    /**
     * 处理文件上传
     * @param array $upload
     * @author simon
     */
    public function moveUploadFile(): self
    {
        //$this->uploadHandler->moveUploadFile();

        $this->writeFile();

        //upload complete
        if ($this->status && (!$this->totalChunk || $this->currentChunk == $this->totalChunk - 1)) {
            rename($this->getTempFile(), $this->uploadHandler->getPath());
        }

        return $this;
    }

    protected function getTempFile()
    {
        return $this->uploadHandler->getPath() . '.part';
    }


    protected function defaultHeader()
    {
        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        /*
        // Support CORS
        header("Access-Control-Allow-Origin: *");
        // other CORS headers if any...
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }
        */
    }
}
