<?php

namespace PMazur\Cats\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\ScopeInterface;

/**
 * Class DownloadImg
 * @package PMazur\Cats\Service
 */
class DownloadImg
{
    /**
     * @var string
     */
    public const IMAGES_DIRECTORY = 'images';

    /**
     * @var string
     */
    public const IMAGE_TEXT_CONFIG_PATH = 'cat/cat_opinion/picture_text';

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var Filesystem
     */
    protected $file;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ApiRequest
     */
    protected $apiRequest;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param DirectoryList $directoryList
     * @param Filesystem $filesystem
     * @param File $file
     * @param ApiRequest $apiRequest
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DirectoryList $directoryList,
        Filesystem $filesystem,
        File $file,
        ApiRequest $apiRequest,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->apiRequest = $apiRequest;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $text
     * @return string
     * @throws FileSystemException
     */
    public function downloadRandomCatMeme(string $text=''): string
    {
        if (!$text) {
            $text = $this->scopeConfig->getValue(
                self::IMAGE_TEXT_CONFIG_PATH,
                ScopeInterface::SCOPE_STORE
            );
        }
        $dir = $this->filesystem
            ->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath(self::IMAGES_DIRECTORY . '/');
        $this->file->checkAndCreateFolder($dir);
        $response = $this->apiRequest->doRequest(
            ApiRequest::RANDOM_IMAGE_WITH_TEXT,
            [
                'text' => $text
            ]
        );
        $body = json_decode($response->getBody());
        if (isset($body->file, $body->url)) {
            $file = $dir . basename($body->file);
        } else {
            return '';
        }

        $result = $this->file->read(ApiRequest::BASIC_URL . $body->url, $file);

        if ($result) {
            return $file;
        } else {
            return '';
        }
    }
}
