<?php declare(strict_types = 1);
/**
 * Magebit_BlogIndexer
 *
 * @category  Magebit
 * @package   Magebit_BlogIndexer
 * @author    Emils Malovka <emils.malovka@magebit.com>
 * @copyright 2020 [Magebit, Ltd.(http://www.magebit.com/)]
 */

namespace Magebit\BlogIndexer\Index\Mapping;

use Divante\VsbridgeIndexerCore\Api\Mapping\FieldInterface;
use Divante\VsbridgeIndexerCore\Api\MappingInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * Class CmsBlog
 */
class CmsBlog implements MappingInterface
{

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var string
     */
    private $type;

    /**
     * CmsBlog constructor.
     *
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getMappingProperties()
    {
        $properties = [
            'id' => ['type' => FieldInterface::TYPE_LONG],
            'title' => ['type' => FieldInterface::TYPE_TEXT],
            'url_key' => ['type' => FieldInterface::TYPE_KEYWORD],
            'featured_image_file' => ['type' => FieldInterface::TYPE_TEXT],
            'featured_image_title' => ['type' => FieldInterface::TYPE_TEXT],
            'featured_image_alt' => ['type' => FieldInterface::TYPE_TEXT],
            'short_content' => ['type' => FieldInterface::TYPE_TEXT],
            'content' => ['type' => FieldInterface::TYPE_TEXT],
            'author_name' => ['type' => FieldInterface::TYPE_TEXT],
            'publish_date' => ['type' => FieldInterface::TYPE_INTEGER],
            'meta_title' => ['type' => FieldInterface::TYPE_TEXT],
            'meta_description' => ['type' => FieldInterface::TYPE_TEXT],
            'blog_category_ids' => ['type' => FieldInterface::TYPE_TEXT],
            'blog_categories' => ['type' => FieldInterface::TYPE_TEXT],
            'status' => ['type' => FieldInterface::TYPE_INTEGER],

        ];

        $mappingObject = new \Magento\Framework\DataObject();
        $mappingObject->setData('properties', $properties);

        $this->eventManager->dispatch(
            'elasticsearch_cms_blog_mapping_properties',
            ['mapping' => $mappingObject]
        );

        return $mappingObject->getData();
    }
}
