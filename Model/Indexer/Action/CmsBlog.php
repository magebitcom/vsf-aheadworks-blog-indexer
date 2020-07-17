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

use Aheadworks\Blog\Api\PostRepositoryInterface;
use Magebit\BlogIndexer\Model\ResourceModel\CmsBlog as CmsBlogResource;
use Magebit\BlogIndexer\Model\ResourceModel\CmsBlogCategory as CmsBlogCategoryResource;
use Magebit\BlogIndexer\Model\ResourceModel\CmsBlogTag as CmsBlogTagResource;
use Magebit\StaticContentProcessor\Helper\Resolver;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CmsBlog
 */
class CmsBlog
{
    /**
     * @var CmsBlogResource
     */
    protected $resourceModel;

    /**
     * @var AreaList
     */
    protected $areaList;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Resolver
     */
    protected $resolver;
    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;
    /**
     * @var CmsBlogCategoryResource
     */
    protected $cmsBlogCategoryResource;

    /**
     * @var CmsBlogTagResource
     */
    protected $cmsBlogTag;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * CmsBlog constructor.
     *
     * @param AreaList $areaList
     * @param CmsBlogResource $cmsBlogResource
     * @param CmsBlogCategoryResource $cmsBlogCategoryResource
     * @param StoreManagerInterface $storeManager
     * @param Resolver $resolver
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        AreaList $areaList,
        CmsBlogResource $cmsBlogResource,
        CmsBlogCategoryResource $cmsBlogCategoryResource,
        CmsBlogTagResource $cmsBlogTag,
        StoreManagerInterface $storeManager,
        Resolver $resolver,
        ScopeConfigInterface $scopeConfig,
        PostRepositoryInterface $postRepository
    ) {
        $this->areaList = $areaList;
        $this->resourceModel = $cmsBlogResource;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->resolver = $resolver;
        $this->postRepository = $postRepository;
        $this->cmsBlogCategoryResource = $cmsBlogCategoryResource;
        $this->cmsBlogTag = $cmsBlogTag;
    }

    /**
     * @param int $storeId
     * @param array $blogIds
     * @return \Traversable
     */
    public function rebuild($storeId = 1, array $blogIds = [])
    {
        $this->areaList->getArea(Area::AREA_FRONTEND)->load(Area::PART_DESIGN);
        $rewritesEnabled = $this->scopeConfig->getValue(
            'vsbridge_indexer_settings/url_rewrites/blog_enabled',
            ScopeInterface::SCOPE_STORE
        );

        /** @var \Magento\Store\Model|Store $store */
        $store = $this->storeManager->getStore($storeId);

        $lastBlogId = 0;

        do {
            $cmsBlogs = $this->resourceModel->loadBlogs($storeId, $blogIds, $lastBlogId);

            foreach ($cmsBlogs as $blogData) {
                $blogData['id'] = (int) $blogData['id'];
                $lastBlogId = $blogData['id'];

                $post = $this->postRepository->get($blogData['id']);

                $blogData['status'] = $post->getStatus() == 'publication' ? 1 : 0;

                if ($rewritesEnabled) {
                    $blogData['content'] = $this->resolver->resolve($post->getContent());
                    $blogData['featured_image_file'] = $this->resolver->resolve($store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $post->getFeaturedImageFile());
                } else {
                    $blogData['content'] = $post->getContent();
                    $blogData['featured_image_file'] = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $post->getFeaturedImageFile();
                }

                $author = $post->getAuthor();

                if ($author !== null) {
                    $blogData['author_name'] = $author->getFirstname() . " " . $author->getLastname();
                } else {
                    $blogData['author_name'] = null;
                }

                $blogData['blog_category_ids'] = [];

                $categories = $this->cmsBlogCategoryResource->loadPages(1, $post->getCategoryIds());
                $categoryArray = [];
                foreach ($categories as $category) {
                    $categoryArray[$category['url_key']] = $category['name'];
                    $blogData['blog_category_ids'][] = (int) $category['id'];
                }

                $tags = $this->cmsBlogTag->loadTags($lastBlogId);
                $tagsArray = [];
                foreach ($tags as $tag) {
                    if (!empty($tag['name'])) {
                        $tagsArray[] = $tag['name'];
                    }
                }

                $blogData['tags'] = implode(',', $tagsArray);
                $blogData['short_content'] = strip_tags($blogData['short_content']);

                $blogData['blog_categories'] = (string)json_encode($categoryArray);
                $blogData['publish_date'] = strtotime($blogData['publish_date']);

                unset($blogData['author_id'], $blogData['created_at'], $blogData['updated_at']);
                unset($blogData['canonical_category_id'], $blogData['is_allow_comments'], $blogData['customer_groups']);
                unset($blogData['product_condition'], $blogData['meta_twitter_site']);

                yield $lastBlogId => $blogData;
            }
        } while (!empty($cmsBlogs));
    }
}
