<?php

namespace PMazur\Cats\Console;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use PMazur\Cats\Service\DownloadImg;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddCats
 * @package PMazur\Cats\Console
 */
class AddCats extends Command
{
    /**
     * @var string
     */
    protected const SKU = 'sku';

    /**
     * @var int
     */
    public const PRODUCT_COLLECTION_PAGE_SIZE = 30;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var DownloadImg
     */
    protected $downloadImg;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var State
     */
    protected $state;

    /**
     * @param ProductCollectionFactory $productCollectionFactory
     * @param DownloadImg $downloadImg
     * @param ProductRepositoryInterface $productRepository
     * @param State $state
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        DownloadImg $downloadImg,
        ProductRepositoryInterface $productRepository,
        State $state
    ) {
        parent::__construct();
        $this->productCollectionFactory = $productCollectionFactory;
        $this->downloadImg = $downloadImg;
        $this->productRepository = $productRepository;
        $this->state = $state;
    }
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('cats:addCats');
        $this->setDescription('Add cat pictures to all products');
        $this->addOption(
            self::SKU,
            null,
            InputOption::VALUE_OPTIONAL,
            'Sku of the product'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(Area::AREA_ADMINHTML);
        $collection = $this->productCollectionFactory->create();
        if ($sku = $input->getOption(self::SKU)) {
            $collection->addAttributeToFilter('sku', $sku);
        }
        $collection->addAttributeToSelect(['sku']);
        $collection->setPageSize(self::PRODUCT_COLLECTION_PAGE_SIZE);
        $numberOfPages = $collection->getLastPageNumber();
        $successes = 0;
        $fails = 0;
        for ($pageNumber = 1; $pageNumber <= $numberOfPages; $pageNumber++) {
            $collection->setCurPage($pageNumber);
            foreach ($collection as $product) {
                $sku = $product->getSku();
                $path = $this->downloadImg->downloadRandomCatMeme();
                $product->addImageToMediaGallery($path, ['image', 'small_image', 'thumbnail', 'base_image'], true);
                try {
                    $this->productRepository->save($product);
                    $successes++;
                } catch (Exception $e) {
                    $fails++;
                    $output->writeln('Failed to change product image for: '. $sku);
                    $output->writeln($e->getMessage());
                }
            }
            $collection->clear();
            $output->writeln(
                'Adding images completed. Successfully modified products: ' . $successes .
                '   Failures: ' . $fails
            );
        }
    }
}
