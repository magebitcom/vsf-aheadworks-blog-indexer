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

namespace Magebit\BlogIndexer\Plugin\Indexer\Save;

use Aheadworks\Blog\Model\Category;
use Magebit\BlogIndexer\Model\Indexer\BlogProcessor;
use Magebit\BlogIndexer\Model\Indexer\CategoryProcessor;

/**
 * Class UpdateAuthor
 */
class UpdateCategory
{
    /**
     * @var CategoryProcessor
     */
    private $categoryProcessor;
    /**
     * @var BlogProcessor
     */
    private $blogProcessor;

    /**
     * Save constructor.
     *
     * @param CategoryProcessor $categoryProcessor
     * @param BlogProcessor $blogProcessor
     */
    public function __construct(CategoryProcessor $categoryProcessor, BlogProcessor $blogProcessor)
    {
        $this->categoryProcessor = $categoryProcessor;
        $this->blogProcessor = $blogProcessor;
    }

    /**
     * @param Page $cmsPage
     * @param Page $result
     *
     * @return Page
     */
    public function afterAfterSave(Category $category, Category $result)
    {
        $result->getResource()->addCommitCallback(function () use ($category) {
            $this->categoryProcessor->reindexRow($category->getId());
            $this->blogProcessor->reindexAll();
        });

        return $result;
    }

    /**
     * @param Page $cmsBlock
     * @param Page $result
     *
     * @return Page
     */
    public function afterAfterDeleteCommit(Category $category, Category $result)
    {
        $this->categoryProcessor->reindexRow($category->getId());
        $this->blogProcessor->reindexAll();

        return $result;
    }
}
