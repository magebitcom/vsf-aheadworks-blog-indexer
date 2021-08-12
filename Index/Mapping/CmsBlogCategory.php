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

namespace Magebit\BlogIndexer\Index\Mapping;

use Divante\VsbridgeIndexerCore\Api\Mapping\FieldInterface;
use Divante\VsbridgeIndexerCore\Api\MappingInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Divante\VsbridgeIndexerCatalog\Index\Mapping\FieldMappingInterface;

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
     * @var FieldMappingInterface[]
     */
    private $additionalMapping = [];

    /**
     * CmsBlog constructor.
     *
     * @param EventManager $eventManager
     */
    public function __construct(
        EventManager $eventManager,
        array $additionalMapping = []
    ) {
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
            'post_count' => ['type' => FieldInterface::TYPE_INTEGER],
            'sort_order' => ['type' => FieldInterface::TYPE_LONG],
            'meta_title' => ['type' => FieldInterface::TYPE_TEXT],
            'meta_description' => ['type' => FieldInterface::TYPE_TEXT],
        ];

        $properties = array_merge($properties, $this->getCustomProperties());

        $mappingObject = new \Magento\Framework\DataObject();
        $mappingObject->setData('properties', $properties);

        $this->eventManager->dispatch(
            'elasticsearch_cms_blog_category_mapping_properties',
            ['mapping' => $mappingObject]
        );

        return $mappingObject->getData();
    }

    /**
    * @return array
    */
    protected function getCustomProperties(): array
    {
        $customProperties = [];

        foreach ($this->additionalMapping as $propertyName => $properties) {
            if ($properties instanceof FieldMappingInterface) {
                $customProperties[$propertyName] = $properties->get();
            }
        }

        return $customProperties;
    }
}
