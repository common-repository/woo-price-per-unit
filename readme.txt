=== WooCommerce Price Per Unit ===
Contributors: mechuram 
Tags: woocommerce, price, weight, price customization, unit price
Stable tag: 2.2.4
Requires PHP: 7.0
Tested up to: 6.5.3
Requires at least: 4.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

WooCommerce Price Per Unit allows the user to show prices recalculated per units(weight) and do some other customization to the appearance of prices

== Description ==
This is an extension for WooCommerce which will help you to sell products where can be important for the customer to know how much a weight unit costs.
For example when selling food. Price is recalculated per weight and then shown according to your liking. The rules can be set store-wide or just for certain products.
**Main function of the plug-in is, that it takes the price of your product and divides this price by its weight, this is then displayed to your liking**
== Another Must-Have Plugin: Sell by Weight PRO ==

This plug-in allows you to sell easily products, where you want to have several weight option to sell at the same price for kilogram.
It works in a similar way as Variable products, but it is easier to manage because you will enter the price only once and the options price is calculated automatically.

More info here - [Sell by Weight PRO](https://mechcomp.cz/sell-by-weight-pro/)

== New plug-in available - Unit Based Stock PRO ==

Manage your stock in Weight units!

Handle inventory in weight units - a capability unprecedented in WooCommerce, which traditionally manages stock solely based on purchased quantity.
Supports decimal values for stock inventory as well.

More info here - [Unit Based Stock PRO](https://mechcomp.cz/unit-based-stock-pro/)

== PRO version available ==

[Price per Unit PRO](https://mechcomp.cz/price-per-unit-pro/)

**PRO version features:**

* Single product settings - possibility to set the settings for each product individually and override the general settings
* Better variations support - The variation shows price per unit after selecting.
* It’s possible to enter custom number of units(for example kilograms) which is different from the weight – for the purpose you sell products with packing and you need to display recalculation for net weight and at the same time you need to keep gross weight for shipping purposes
* This feature can now also be entered differently for separate variations on variable products
* Change of recalculation per different unit (keeps the original weight, but shows the price per new unit)
* Recalculation ratio – multiplies the price with this ratio – work for example if you want to show price per 100 grams (if you have shop in kg you enter 0.1)

**Description**


This is an extension for WooCommerce which will help you to sell products where can be important for the customer to know how much a weight unit costs.
For example when selling food. Price is recalculated per weight and then shown according to your liking. The rules can be set store-wide or just for certain products.
**Main function of the plug-in is, that it takes the price of your product and divides this price by its weight, this is then displayed to your liking**

== Important notice ==

**This plug-in works only if you have weight set on the product. This is independent setting used for shipping purposes.**

**You can find it here:**
Products->All products->some of your products->Shipping->Weight

**For variable products with different weight of variations:**
Products->All products->some of your products->Variations->some variation->Weight

**Recalculated price can be shown**

* Instead of original price
* As a new row after original price

You can also add some text after recalculated price for example "/Kg"
Price is recalculated only when the weight is set on the product.

**There are also two different settings depending on viewed page**

* Settings for store page
* Settings for single product page
  
**This plug-in can do also some other customization to the appearance of the price**

* Additional custom text for all prices
* You can hide original price when product is on sale
* Additional custom text for variations
* You can hide maximum price for variable products

Plug-in is compatible with WooCommerce versions from 3.0.0 to 4.0.x

**There are two sets of settings**

**General settings** - which will affect every product in the store
  It is located under WooCommerce -> Settings -> Products -> Price Per Unit
**Single product settings** - affects just single product, can also override general settings
  It is located in product editor - tab Price Per Unit
  


== Installation ==

1. Install and activate the plug-in in your WordPress dashboard by going to Plug-ins -> Add New.
2. Search for "WooCommerce Price Per Unit" to find the plug-in.
3. When you see WooCommerce Price Per Unit, click "Install Now" to install the plug-in.
4. Click "Activate" to activate the plug-in.



== Frequently Asked Questions ==

= Is it possible to set recalculation just for one product? =

Yes. You have to go to product editor, tab Price per unit and set an override to the rule you want. It works then even despite the global settings is turned off.

= I have recalculation on, but the price seems untouched. What's wrong?  =

Recalculated price is shown only when the recalculation takes place. That means if you don't have the weight set on the product, nothing happens to the price. Make sure you have weight set.

= I want to use only other features, not recalculation. Is it necessary to have recalculation on?  =

No. Some features are independent on recalculation, you will find them as "General price options" and "Options for variable products" those settings are store wide - they will affect all products.



== Screenshots ==

1. General options - those settings will affect all products
2. Single product settings - these settings affect only current product
3. Important setting - for this plug-in to work the weight of the product needs to be set 
4. Single product page with recalculated price as new row
5. Example of shop page with different overrides. Recalculated price replaced original one, recalculated price as new row, no recalculation at all.
6. Store with recalculation set for all products   

== Changelog ==

= 2.2.4 =
- New feature - Options on product detail to disable recalculation
- Improvement - Variable products performance optimization
- Bug-fix - small bugfixes
- Improvement - HPOS compatibility
- Improvement - Code refactoring
- Compatibility with WooCommerce 8.8.3
- Compatibility with WordPress 6.5.3

= 2.0.9 =
- Improvement - Warning displayed when no weight is set
- Compatibility with WooCommerce 6.1.1
- Compatibility with WordPress 5.9.0

= 2.0.8 =
- Bug-fix - Do not display additional text on empty price
- Bug-fix - Division by zero in some rare circumstances
- Scripts updated
- RTL languages improvement
- WPML configuration created
- Compatibility with WooCommerce 5.6.0
- Compatibility with WordPress 5.8.1

= 2.0.7 =
- Backward compatibility for recent bug-fix

= 2.0.6 =
- Bug-fix - In certain configurations price with taxes was displayed improperly in Cart

= 2.0.5 =
- Bug-fix - Improper price display with taxes

= 2.0.4 =
- Bug-fix - "Variations - prefix for variable price" not visible for replaced row

= 2.0.3 =
- New free version - due to huge changes in code(security hardening) the free version is created from scratch
- For sake of easier parallel development of free and PRO version there is a change in free version approach
- Removal - Single product settings removed
- Paid feature released for free - display of recalculated price in Cart
- New feature - automatic recalculation text

= 2.0.1 =
- Fixed info messages in admin for WooCommerce 4.0.1

= 2.0.0 =
- Added option to trim recalculated prices instead of rounding
- Added recalculated price prefix
- Security hardening - complete revision of the code to comply with latest wordpress security recommendations

= 1.9.7 =
- Code revision and rewrite

= 1.9.5 =
- Bug-fix - recalculated price was displayed on products with zero price

= 1.9.4 =
- New feature/bug-fix - displaying recalculate price on cart was influenced by Shop page price behaviour - now it has option by itself
The settings "Show if displayed on Shop page" and "Show if displayed on Single product page" take respective override from product settings
Default setting is to show the text always

= 1.9.3 =
- New feature - Automatic recalculation text - it takes the weight unit from your shop settings and creates the text from it (example: /kg). Works together with other setting of this plug-in so it can create text for Different weight unit and also Custom number of units.
- Added option to control the appearance of the price text
- Added warning for no weight set(many people is asking why they don't see the recalculated price)

= 1.9.2 =
- Dropped support for WooCommerce pre 3.0

= 1.9.1 =
- Bug-fix - Settings automatically deleted on uninstall - now there is a setting in general options to delete on uninstall - caused troubles during manual update or upgrade to Pro version.

= 1.9 =
- New approach to entering additional text - predefined values

= 1.8.1 =
- Bug fix - "General price additional text" - not showing properly on recalculation

= 1.8 =
- Compatibility with WordPress 5.1

= 1.7 =
- Added Custom recalculation ratio for variations

= 1.6 =
- Added Custom recalculation ratio
- Recalculated price can be shown also on variation selection
- Translation fix
- Core rewrite

= 1.5 =
- Plug-in is set to display recalculation for all products by default after installation. Doesn't affect current installations.
- Fixed compatibility with WooCommerce POS  

= 1.4 =
- Fixed bug of improperly displayed price with TAX

= 1.3 =
- Added CSS class for whole new row. The class name is mcmp_recalc_price_row.
- Added option for predefined styling of new row (off by default). New row will be in different size and italics.

= 1.2 =
- Improved handling of variable products. Now it works properly even with different weight on variations.
- Added CSS classes for modification of additional texts appearance. Classes can be found in general settings help texts.

= 1.1 =
- Changed behaviour on variable products to conform with WooCommerce 3.x
- Don't show sale price on variable products

= 1.0 =
- Initial Release

== Upgrade Notice ==

= 2.2.4 =
- New feature - Options on product detail to disable recalculation
- Improvement - Variable products performance optimization
- Bug-fix - small bugfixes
- Improvement - HPOS compatibility
- Improvement - Code refactoring
- Compatibility with WooCommerce 8.8.3
- Compatibility with WordPress 6.5.3

= 2.0.9 =
- Improvement - Warning displayed when no weight is set
- Compatibility with WooCommerce 6.1.1
- Compatibility with WordPress 5.9.0