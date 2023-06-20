<?php
declare(strict_types=1);

namespace TdEllguthe\CatalogAttrCreation\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddMyCategoryAttribute implements DataPatchInterface, PatchRevertableInterface
{
    public const ATTR_CODE = 'my_attribute';

    /** @var ModuleDataSetupInterface */
    private ModuleDataSetupInterface $moduleDataSetup;
    /** @var CategorySetup */
    private CategorySetup $categorySetup;
    /** @var int */
    private int $categoryEntityTypeId;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     * @throws LocalizedException
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup      = $moduleDataSetup;
        $this->categorySetup        = $categorySetupFactory->create();
        $this->categoryEntityTypeId = (int) $this->categorySetup->getEntityTypeId(Category::ENTITY);
    }

    /**
     * @throws LocalizedException|\Zend_Validate_Exception
     */
    public function apply(): void
    {
        // disable foreign key checks & prevent auto-increment values when inserting 0
        $this->moduleDataSetup->getConnection()->startSetup();

        /**
         * @var string $inputType
         * possible types: static, varchar, int, decimal, datetime
         * static: data is stored in the main table (catalog_category_entity), not in the type-specific table
         */
        $inputType = 'varchar';

        /**
         * @var string $input
         * possible types: text, int, select, multiselect, date, hidden, boolean, multiline, image
         */
        $input = 'text';

        /**
         * @var string $groupName
         * name of the accordion element of category page in admin area
         * @see table "eav_attribute_group"
         */
        $groupName = 'my_group';

        /**
         * class name for options (boolean, select, multiselect, dropdown, radio)
         * @var string | null $sourceModelClassName
         * @see \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
         */
        $sourceModelClassName = null;

        /**
         * Model for attribute data modification before saving
         * including validate, afterLoad, beforeSave, afterSave, beforeDelete, afterDelete
         * @var string | null $backendModelClassName
         * @see \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
         */
        $backendModelClassName = null;

        $this->categorySetup->addAttribute(
            Category::ENTITY,
            self::ATTR_CODE,
            [
                'label'                    => 'My Attribute', // admin area & frontend
                'type'                     => $inputType,
                'input'                    => $input,
                'source'                   => $sourceModelClassName,
                'backend'                  => $backendModelClassName,
                'sort_order'               => 50,             // position among other attributes (admin area & frontend)
                'global'                   => ScopedAttributeInterface::SCOPE_GLOBAL, // lowest applicable scope
                'visible'                  => true,           // admin area & frontend
                'required'                 => false,          // kinda obvious ...
                'default'                  => null,           // default value
                'is_html_allowed_on_front' => false, // TODO: test
                'visible_on_front'         => true, // TODO: necessary?
            ]
        );

        // add attribute to group
        $attribute = $this->categorySetup->getAttribute($this->categoryEntityTypeId, self::ATTR_CODE);
        $this->addAttributeToGroup((int) $attribute['attribute_id'], $groupName, 5, 10);

        // enable foreign key checks & restore original SQL_MODE
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @param int $attributeId
     * @param string $groupName
     * @param int $groupPosInEntity
     * @param int $attrPosInGroup
     */
    private function addAttributeToGroup(
        int $attributeId,
        string $groupName,
        int $groupPosInEntity,
        int $attrPosInGroup
    ): void {
        $attributeSetId = $this->categorySetup->getDefaultAttributeSetId(Category::ENTITY);

        if (!$this->categorySetup->getAttributeGroup($this->categoryEntityTypeId, $attributeSetId, $groupName)) {
            // group does not exist yet => create it
            $this->categorySetup->addAttributeGroup(
                $this->categoryEntityTypeId,
                $attributeSetId,
                $groupName,
                $groupPosInEntity
            );
        }

        // add attribute to group
        $this->categorySetup->addAttributeToGroup(
            $this->categoryEntityTypeId,
            $attributeSetId,
            $groupName,
            $attributeId,
            $attrPosInGroup
        );
    }

    /**
     * calling this function should uninstall this patch, if already applied
     */
    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->categorySetup->removeAttribute(Category::ENTITY, self::ATTR_CODE);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return string[]
     * some patches with could change their names during development
     * to keep track of already installed patches and dependencies,
     *   all previously used (and other) names can be entered here
     */
    public function getAliases(): array
    {
        return [
            // \SomeVendor\SomeModule\Setup\Patch\Data\SomePatch::class
        ];
    }

    /**
     * @return string[]
     * if some data patches must be applied before this one: list them here
     */
    public static function getDependencies(): array
    {
        return [
            // \SomeVendor\SomeModule\Setup\Patch\Data\SomePatch::class
        ];
    }
}
