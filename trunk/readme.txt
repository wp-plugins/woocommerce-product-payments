=== Woocommerce Payment Gateway per Product ===
Contributors: dreamfox
Donate link: http://www.dreamfox.nl
Tags: woocommerce,payments,plugin,free
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin for woocommerce lets you select the available payment gateways for each individual product. 

== Description ==

This plugin for woocommerce lets you select the available payment gateways for each individual product. 
You can select for eacht individual product the payment gateway that will be used by checkout. If no selection is made, then the default payment gateways are displayed. If you for example only select paypal then only paypal will available for that product by checking out.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

= What happens if i add more then one product in the shoppingcard with different selected payment gateways? =

Allowed payment gateways goes before denied payment gateways. so if for example you set:
- product 1 to paypal
- product 2 to paypal & creditcard
Then both payment gateways are shown by checkout

== Screenshots ==

1. screenshot1.png
2. screenshot2.png
3. screenshot3.png

== Changelog ==

= 1.0.1 =
* added author

== Upgrade notice ==

= 1.0 =
Upgrade for new version

= 0.5 =
This version fixes a bug.  Upgrade immediately.
