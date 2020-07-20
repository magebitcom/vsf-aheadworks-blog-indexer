<?php declare(strict_types = 1);
/**
 * This file is part of the Magebit_BlogIndexer package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magebit_BlogIndexer
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2020 Magebit, Ltd. (https://magebit.com/)
 * @author    Emīls Malovka <emils.malovka@magebit.com>
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magebit\BlogIndexer\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class CmsBlog
 */
class CmsBlog
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var MetadataPool
     */
    private $metaDataPool;

    /**
     * Rates constructor.
     *
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resource = $resourceConnection;
        $this->metaDataPool = $metadataPool;
    }

    /**
     * @param int $storeId
     * @param array $blogIds
     * @param int $fromId
     * @param int $limit
     *
     * @return array
     * @throws \Exception
     */
    public function loadBlogs($storeId = 1, array $blogIds = [], $fromId = 0, $limit = 1000)
    {
        $metaData = $this->getCmsBlogMetadata();

        $select = $this->getConnection()->select()->from(['cms_blog' => $metaData->getEntityTable()]);

        if (!empty($blogIds)) {
            $select->where('cms_blog.id IN (?)', $blogIds);
        }

        $select->where('status = ?', "publication");
        $select->where('cms_blog.id > ?', $fromId)
            ->limit($limit)
            ->order('cms_blog.id');

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }

    /**
     * @return \Magento\Framework\EntityManager\EntityMetadataInterface
     * @throws \Exception
     */
    private function getCmsBlogMetadata()
    {
        return $this->metaDataPool->getMetadata(PostInterface::class);
    }
}
