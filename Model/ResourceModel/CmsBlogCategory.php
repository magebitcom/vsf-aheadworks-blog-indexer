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

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\ResourceModel\Post;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class CmsBlogCategory
 */
class CmsBlogCategory
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
     * @param array $categoryIds
     * @param int $fromId
     * @param int $limit
     *
     * @return array
     * @throws \Exception
     */
    public function loadPages($storeId = 1, array $categoryIds = [], $fromId = 0, $limit = 1000)
    {
        $metaData = $this->getCmsBlogCategoryMetaData();

        $subSelect = $this->getConnection()->select()->from(
            ['blog_category_posts' => Post::BLOG_POST_CATEGORY_TABLE],
            'COUNT(blog_post.id)'
        );

        $subSelect->joinLeft(
            ['blog_post' => Post::BLOG_POST_TABLE],
            'blog_post.id = blog_category_posts.post_id',
            []
        );

        $subSelect->where('blog_category_posts.category_id = blog_category.id');
        $subSelect->where('blog_post.status = ?', 'publication');


        $select = $this->getConnection()->select()->from(
            ['blog_category' => $metaData->getEntityTable()]
        )->columns([
            '*',
            'post_count' => $subSelect
        ]);

        if (!empty($categoryIds)) {
            $select->where('blog_category.id IN (?)', $categoryIds);
        }

        $select->where('status = ?', 1);
        $select->where('blog_category.id > ?', $fromId)
            ->limit($limit)
            ->order('blog_category.id');

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
    private function getCmsBlogCategoryMetaData()
    {
        return $this->metaDataPool->getMetadata(CategoryInterface::class);
    }
}
