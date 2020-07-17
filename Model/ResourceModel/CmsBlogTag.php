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

use Aheadworks\Blog\Model\ResourceModel\Post as BlogPostTag;
use Aheadworks\Blog\Model\ResourceModel\Tag as BlogTag;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class CmsBlogTag
 */
class CmsBlogTag
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
     * @param int $postId
     *
     * @return array
     */
    public function loadTags($postId = 0)
    {
        $select = $this->getConnection()->select()->from(['blog_post_tag' => BlogPostTag::BLOG_POST_TAG_TABLE]);

        $select->where('blog_post_tag.post_id = ?', (int)$postId);

        $select->joinLeft(
            ['blog_tag' => BlogTag::BLOG_TAG_TABLE],
            'blog_post_tag.tag_id = blog_tag.id',
            ['name']
        );

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }
}
