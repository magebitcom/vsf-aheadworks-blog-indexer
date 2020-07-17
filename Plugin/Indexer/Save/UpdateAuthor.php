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

use Aheadworks\Blog\Model\Author;
use Magebit\BlogIndexer\Model\Indexer\BlogProcessor;

/**
 * Class UpdateAuthor
 */
class UpdateAuthor
{
    /**
     * @var BlogProcessor
     */
    private $blogProcessor;

    /**
     * Save constructor.
     *
     * @param BlogProcessor $blogProcessor
     */
    public function __construct(BlogProcessor $blogProcessor)
    {
        $this->blogProcessor = $blogProcessor;
    }

    /**
     * @param Author $post
     * @param Author $result
     *
     * @return Author
     */
    public function afterAfterSave(Author $post, Author $result)
    {
        $result->getResource()->addCommitCallback(function () use ($post) {
            $this->blogProcessor->reindexAll();
        });

        return $result;
    }

    /**
     * @param Author $post
     * @param Author $result
     *
     * @return Author
     */
    public function afterAfterDeleteCommit(Author $post, Author $result)
    {
        $this->blogProcessor->reindexAll();

        return $result;
    }
}
