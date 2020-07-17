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
 * Class CmsBlogCategory
 */
class CmsBlogCategory implements MappingInterface
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
     * @inheritdoc
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
            'name' => ['type' => FieldInterface::TYPE_TEXT],
            'url_key' => ['type' => FieldInterface::TYPE_KEYWORD],
            'status' => ['type' => FieldInterface::TYPE_INTEGER],
            'sort_order' => ['type' => FieldInterface::TYPE_LONG],
            'meta_title' => ['type' => FieldInterface::TYPE_TEXT],
            'meta_description' => ['type' => FieldInterface::TYPE_TEXT],
        ];

        $mappingObject = new \Magento\Framework\DataObject();
        $mappingObject->setData('properties', $properties);

        $this->eventManager->dispatch(
            'elasticsearch_cms_blog_category_mapping_properties',
            ['mapping' => $mappingObject]
        );

        return $mappingObject->getData();
    }
}
