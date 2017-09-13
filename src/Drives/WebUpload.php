<?php
/**
 * Applies to webUpload 0.1.5
 *
 * Related license, follow the webUpload license
 *
 * License: https://github.com/fex-team/webuploader/blob/master/LICENSE
 */

namespace CrCms\Upload\Drives;

use CrCms\Upload\Contracts\FileUpload;
use CrCms\Upload\Exceptions\UploadException;
use CrCms\Upload\File;
use CrCms\Upload\Traits\ExtensionTrait;
use CrCms\Upload\Traits\FileTrait;
use CrCms\Upload\Traits\MimeTrait;
use CrCms\Upload\Traits\SizeTrait;
use Illuminate\Config\Repository as Config;

class WebUpload implements FileUpload
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
        $this->setHeader();
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
        $this->newName = $_REQUEST[$this->config->get('upload.drives.webupload.new_name')] . '.' . $this->extension;
        return $this;
    }

    /**
     * @return void
     */
    protected function checkUploadFile()
    {
        $this->checkUploadedFile();

        $this->checkUploadSelf();

        /* 一个是验证总大小,一个是验证块大小 */
        $this->checkSize(intval($_REQUEST[$this->config->get('upload.drives.webupload.size_name')]));
        $this->checkSize($this->size);

        $this->checkExtension($this->extension);

        $this->checkMime($this->file->getMime());
    }

    /**
     * 分块上传大小设置
     */
    protected function setChunking()
    {
        $chunkName = $this->config->get('upload.drives.webupload.chunk_name');
        $chunksName = $this->config->get('upload.drives.webupload.chunks_name');

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

        $tmp = "{$this->getTempFile($this->chunk)}tmp";
        // Open temp file
        if (!$out = @fopen($tmp, "wb")) {
            throw new UploadException($tmp, UploadException::READ_FILE_STREAM_ERR);
        }

        if (!$in = @fopen($this->temp, "rb")) {
            @fclose($out);
            throw new UploadException($this->temp, UploadException::READ_FILE_STREAM_ERR);
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        rename("{$tmp}", $this->getTempFile($this->chunk));
    }

    /**
     * @return File|null
     */
    protected function moveUploadFile()
    {
        //自动创建目录
        $this->createDirToFullPath();

        $this->writeFile();

        //上传完成
        if (count(glob("{$this->getFullPath()}*.part")) === $this->chunks) {
            return $this->completeHandle();
        }

        return null;
    }

    /**
     * @return File
     */
    protected function completeHandle(): File
    {
        $file = $this->getFullPath();

        if (!$out = @fopen($file, "wb")) {
            throw new UploadException($file, UploadException::READ_FILE_STREAM_ERR);
        }

        if (flock($out, LOCK_EX)) {
            for ($index = 0; $index < $this->chunks; $index++) {
                if (!$in = @fopen($this->getTempFile($index), "rb")) {
                    break;
                }

                while ($buff = fread($in, 4096)) {
                    fwrite($out, $buff);
                }

                @fclose($in);
                @unlink($this->getTempFile($index));
            }

            flock($out, LOCK_UN);
        }
        @fclose($out);

        return new File($file);
    }

    /**
     * @return string
     */
    protected function getTempFile(int $chunk): string
    {
        return $this->getFullPath() . $chunk . '.part';
    }

    /**
     * @return void
     */
    protected function setHeader()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }
}
