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

namespace Magebit\BlogIndexer\Model\Indexer\Action;

use Magebit\BlogIndexer\Model\ResourceModel\CmsBlogCategory as CmsBlogCategoryResource;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Divante\VsbridgeIndexerCore\Indexer\RebuildActionInterface;

/**
 * Class CmsBlogCategory
 */
class CmsBlogCategory implements RebuildActionInterface
{
    /**
     * @var CmsBlogCategoryResource
     */
    private $resourceModel;

    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * CmsBlog constructor.
     *
     * @param AreaList $areaList
     * @param CmsBlogCategoryResource $cmsBlogCategoryResource
     */
    public function __construct(
        AreaList $areaList,
        CmsBlogCategoryResource $cmsBlogCategoryResource
    ) {
        $this->areaList = $areaList;
        $this->resourceModel = $cmsBlogCategoryResource;
    }

    /**
     * @param int $storeId
     * @param array $categoryIds
     *
     * @return \Traversable
     */
    public function rebuild($storeId, array $categoryIds): \Traversable
    {
        $this->areaList->getArea(Area::AREA_FRONTEND)->load(Area::PART_DESIGN);
        $lastPageId = 0;

        do {
            $cmsBlogCategories = $this->resourceModel->loadPages($storeId, $categoryIds, $lastPageId);

            foreach ($cmsBlogCategories as $categoryData) {
                $categoryData['id'] = (int) $categoryData['id'];
                $lastPageId = $categoryData['id'];

                $categoryData['is_description_enabled'] = (int) $categoryData['is_description_enabled'];
                $categoryData['sort_order'] = (int) $categoryData['sort_order'];
                $categoryData['status'] = (int) $categoryData['status'];
                $categoryData['post_count'] = (int) $categoryData['post_count'];

                unset($categoryData['created_at'], $categoryData['updated_at'], $categoryData['parent_id']);
                unset($categoryData['path']);

                yield $lastPageId => $categoryData;
            }
        } while (!empty($cmsBlogCategories));
    }
}
