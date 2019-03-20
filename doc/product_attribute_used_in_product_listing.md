Use Custom Entity product attribute in product listing
======================================================

Like other [product attributes](https://docs.magento.com/m2/ce/user_guide/stores/attributes-product.html), in Storefront Properties change value of "Used in Product Listing" to "Yes".

And as explained in the Magento doc, the display of the attribute depends on your theme.

In your template, which displays the list of products, to display a "Custom Entity" attribute you can use:
```phtml
<?
$pictograms = $this->helper('\Smile\CustomEntityProductLink\Helper\Product')->getCustomEntities($_product, 'picto');
if (!empty($pictograms)) :
?>
     <img src="<?= reset($pictograms)->getImageUrl('image'); ?>" width="70" />
<?php endif; ?>
```  

In this example, we display the image of the first CustomEntity Pictogram.
