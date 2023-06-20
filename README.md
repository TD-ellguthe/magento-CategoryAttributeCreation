# Product EAV attribute creation per Data Patch
This project contains an example module to show the creation of a category EAV attribute per Data Patch.

## Magento-Version
2.4.2-p1

## Customizations
<details>
  <summary>Text Attribute</summary>

This is the default used in our Data Patch
</details>
<details>
  <summary>Boolean Attribute</summary>

- Attribute configuration
    - `type`: `int`
    - `'input'`: `'boolean'`
```xml
<!-- app/code/MagentoKompendium/CategoryAttrCreation/view/adminhtml/ui_component/category_form.xml -->
<field name="my_attribute">
    <argument name="data">
        <item name="config" xsi:type="array">
            <item name="dataType" xsi:type="string">boolean</item>
            <item name="formElement" xsi:type="string">checkbox</item>
            <item name="prefer" xsi:type="string">toggle</item>
            <item name="valueMap" xsi:type="array">
                <item name="true" xsi:type="string">1</item>
                <item name="false" xsi:type="string">0</item>
            </item>
        </item>
    </argument>
</field>
```
</details>
<details>
  <summary>HTML Attribute (Page Builder)</summary>

- Attribute configuration
    - `is_html_allowed_on_front`: `true`
- set the following in your `category_form.xml`:
```xml
<!-- app/code/MagentoKompendium/CategoryAttrCreation/view/adminhtml/ui_component/category_form.xml -->
<field name="my_attribute">
    <argument name="data">
        <item name="config" xsi:type="array">
            <item name="formElement" xsi:type="string">wysiwyg</item>
        </item>
    </argument>
</field>
```
</details>
<details>
  <summary>Image Attribute</summary>

- Attribute configuration
    - `input`: `image`
    - `type`: `varchar`
    - `backend`: `\Magento\Catalog\Model\Category\Attribute\Backend\Image::class`

```xml
<!-- app/code/MagentoKompendium/CategoryAttrCreation/view/adminhtml/ui_component/category_form.xml -->
<field name="my_image_attribute" sortOrder="50" formElement="imageUploader">
    <argument name="data" xsi:type="array">
        <item name="config" xsi:type="array">
            <item name="source" xsi:type="string">category</item>
        </item>
    </argument>
    <settings>
        <elementTmpl>ui/form/element/uploader/image</elementTmpl>
        <dataType>string</dataType>
        <label translate="true">My Image Attribute</label>
        <visible>true</visible>
        <required>false</required>
    </settings>
    <formElements>
        <imageUploader>
            <settings>
                <required>false</required>
                <uploaderConfig>
                    <param xsi:type="url" name="url" path="catalog/category_image/upload"/>
                </uploaderConfig>
                <previewTmpl>Magento_Catalog/image-preview</previewTmpl>
                <openDialogTitle>Media Gallery</openDialogTitle>
                <initialMediaGalleryOpenSubpath>catalog/category</initialMediaGalleryOpenSubpath>
                <allowedExtensions>jpg jpeg gif png</allowedExtensions>
                <maxFileSize>4194304</maxFileSize>
            </settings>
        </imageUploader>
    </formElements>
</field>
```
</details>
