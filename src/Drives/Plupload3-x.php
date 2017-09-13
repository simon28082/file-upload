<?php

namespace CrCms\Upload\Drives;

use CrCms\Upload\Contracts\FileUpload;
use CrCms\Upload\Exceptions\UploadException;
use CrCms\Upload\File;
use CrCms\Upload\Traits\ExtensionTrait;
use CrCms\Upload\Traits\FileTrait;
use CrCms\Upload\Traits\MimeTrait;
use CrCms\Upload\Traits\RenameTrait;
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
     * @var bool
     */
    protected $combineChunksOnComplete = true;

    /**
     * @var resource|null
     */
    protected $out = null;

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
     * @param string $oldName
     * @param callable|null $callable
     * @return PlUpload
     */
    public function setNewName(string $oldName, callable $callable = null): self
    {
        $this->newName = $_REQUEST[$this->config->get('upload.drives.plupload.new_name')];
        return $this;
    }

    /**
     * @param array $file
     * @return FileTrait
     */
    protected function setUploadFile(array $file)
    {
        $file['name'] = $_REQUEST[$this->config->get('upload.drives.plupload.old_name')];
        $result = $this->parentSetUploadFile($file);
        return $result;
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
        //intval($_REQUEST[$this->config->get('upload.drives.plupload.size_name')])
        $this->checkSize(intval($_REQUEST[$this->config->get('upload.drives.plupload.size_name')]));

        $this->checkExtension($this->extension);

        $this->checkMime($this->file->getMime());
    }

    /**
     * 分块上传大小设置
     */
    protected function setChunking()
    {
        $chunkName = $this->config->get('upload.drives.plupload.chunk_name');
        $chunksName = $this->config->get('upload.drives.plupload.chunks_name');

        $this->chunk = isset($_REQUEST[$chunkName]) ? intval($_REQUEST[$chunkName]) : 0;
        $this->chunks = isset($_REQUEST[$chunksName]) ? intval($_REQUEST[$chunksName]) : 0;
    }

    /**
     * @return void
     */
    protected function writeFile()
    {
        //块上传大小
        $this->setChunking();

        /*//读取并写入数据流
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
        fclose($fpIn);*/



        $this->lockTheFile($this->getFullPath());

        // Write file or chunk to appropriate temp location
        if ($this->chunks) {
            $result = $this->handleChunk($this->chunk, $this->getFullPath());
        } else {
            $result = $this->handleFile($this->getFullPath());
        }
dd($result);
        $this->unlockTheFile($this->getFullPath());
    }

    protected function handleFile($file_name)
    {
        $file_path = $file_name;
        $tmp_path = $this->writeUploadTo($this->temp,$file_path . ".part");
        return $this->rename($tmp_path, $file_path);
    }

    protected function handleChunk($chunk, $file_name)
    {
        $file_path = $file_name;
        $this->createDir("$file_path.dir.part");
        $chunk_path = $this->writeUploadTo($this->temp,"$file_path.dir.part" . DIRECTORY_SEPARATOR . "$chunk.part");

        if ($this->combineChunksOnComplete && $this->isLastChunk($file_name)) {
            return $this->combineChunksFor($file_name);
        }

        return array(
            'name' => $file_name,
            'path' => $chunk_path,
            'chunk' => $chunk,
            'size' => filesize($chunk_path)
        );
    }

    protected function rename($tmp_path, $file_path)
    {
        // Upload complete write a temp file to the final destination
//        if (!$this->fileIsOK($tmp_path)) {
//            if ($this->conf['cleanup']) {
//                @unlink($tmp_path);
//            }
//            throw new Exception('', PLUPLOAD_SECURITY_ERR);
//        }

        if (rename($tmp_path, $file_path)) {

            return array(
                'name' => basename($file_path),
                'path' => $file_path,
                'size' => filesize($file_path)
            );
        } else {
            return false;
        }
    }

    protected function writeChunksToFile($chunk_dir, $target_path)
    {
        $chunk_paths = array();

        for ($i = 0; $i < $this->chunks; $i++) {
            $chunk_path = $chunk_dir . DIRECTORY_SEPARATOR . "$i.part";
            if (!file_exists($chunk_path)) {
                throw new \Exception('error');
            }
            //$chunk_paths[] = $chunk_path;
            $this->writeToFile($chunk_path, $target_path, 'ab');
        }



//        $this->log("$chunk_dir combined into $target_path");
//
//        // Cleanup
//        if ($this->conf['cleanup']) {
//            $this->rrmdir($chunk_dir);
//        }

        return $target_path;
    }

    protected function filesize($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        static $iswin;
        if (!isset($iswin)) {
            $iswin = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
        }

        static $exec_works;
        if (!isset($exec_works)) {
            $exec_works = (function_exists('exec') && !ini_get('safe_mode') && @exec('echo EXEC') == 'EXEC');
        }

        // try a shell command
        if ($exec_works) {
            $cmd = ($iswin) ? "for %F in (\"$file\") do @echo %~zF" : "stat -c%s \"$file\"";
            @exec($cmd, $output);
            if (is_array($output) && is_numeric($size = trim(implode("\n", $output)))) {
                $this->log("filesize obtained via exec.");
                return $size;
            }
        }

        // try the Windows COM interface
        if ($iswin && class_exists("COM")) {
            try {
                $fsobj = new COM('Scripting.FileSystemObject');
                $f = $fsobj->GetFile(realpath($file));
                $size = $f->Size;
            } catch (Exception $e) {
                $size = null;
            }
            if (ctype_digit($size)) {
                $this->log("filesize obtained via Scripting.FileSystemObject.");
                return $size;
            }
        }

        // if everything else fails
        $this->log("filesize obtained via native filesize.");
        return @filesize($file);
    }

    function combineChunksFor($file_name)
    {
        $file_path = $file_name;
        if (!$tmp_path = $this->writeChunksToFile("$file_path.dir.part", "$file_path.part")) {
            return false;
        }
        return $this->rename($tmp_path, $file_path);
    }


    protected function writeUploadTo($tmpPath, $file_path, $mode = 'wb')
    {

        return $this->writeToFile($tmpPath, $file_path, $mode);
    }


    protected function writeToFile($source_path, $target_path, $mode = 'wb')
    {

        if (!$out = @fopen($target_path, $mode)) {
            throw new \Exception( 'open error');
        }

        if (!$in = @fopen($source_path, "rb")) {
            throw new \Exception('open error 2');
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($in);
        fflush($out);
        @fclose($out);

        return $target_path;
    }

    protected function isLastChunk($file_path)
    {
        $chunks = sizeof(glob("$file_path.dir.part/*.part"));

        return $chunks == $this->chunks;
    }

    private function lockTheFile($file_name)
    {
        $this->out = fopen("{$file_name}.lock", 'w');
        flock($this->out, LOCK_EX); // obtain blocking lock
    }


    /**
     * Release the blocking lock on the specified file.
     *
     * @param string $file_name File to lock
     */
    private function unlockTheFile($file_name)
    {
        if ($this->out) {
            fclose($this->out);
        }
        @unlink("{$file_name}.lock");
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
     * @return int
     */
    protected function getTempFileSize(): int
    {
        $tmpFile = $this->getTempFile();
        if (file_exists($tmpFile)) {
            return filesize($this->getTempFile());
        }

        return 0;
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





