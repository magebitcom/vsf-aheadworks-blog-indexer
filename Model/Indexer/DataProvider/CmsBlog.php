<?php
/**
 * @package  Divante\VsbridgeIndexerCatalog
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Magebit\BlogIndexer\Model\Indexer\DataProvider;

use Divante\VsbridgeIndexerCatalog\Model\ResourceModel\Product\AttributeDataProvider;
use Divante\VsbridgeIndexerCore\Api\DataProviderInterface;
use Divante\VsbridgeIndexerCore\Indexer\DataFilter;
use Divante\VsbridgeIndexerCatalog\Api\CatalogConfigurationInterface;
use Divante\VsbridgeIndexerCatalog\Api\SlugGeneratorInterface;
use Divante\VsbridgeIndexerCatalog\Model\ProductUrlPathGenerator;
use Divante\VsbridgeIndexerCatalog\Model\Attributes\ProductAttributes;

/**
 * Class CmsBlog
 */
class CmsBlog implements DataProviderInterface
{
    /**
     * @param array $indexData
     * @param int   $storeId
     *
     * @return array
     * @throws \Exception
     */
    public function addData(array $indexData, $storeId)
    {
        return $indexData;
    }
}
