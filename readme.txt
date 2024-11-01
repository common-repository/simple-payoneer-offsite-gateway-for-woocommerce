=== Simple Payoneer Offsite Gateway for WooCommerce ===

Contributors: rynald0s
Tags: Payoneer, Payoneer gateway for WooCommerce, WooCommerce Payoneer, Payoneer gateway, Payoneer for WooCommerce, WooCommerce Payoneer
Requires at least: 4.6
Tested up to: 5.4.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a simple Payoneer Offsite payments gateway plugin for WooCommerce. 

== Description ==

This is a simple Payoneer Offsite payments gateway plugin for WooCommerce. It works in a similar fashion than the built-in BACS method, but the user will have a direct link in the checkout form that will allow to make a direct Payoneer to Payoneer payment. The checkout is completed as soon as the user fills in the Payoneer email and transaction number. 

== Installation ==

1. Download the .zip file
2. Upload and extract the contents of the zip file to your wp-content/plugins/folder
3. Activate the plugin from your WP-admin / Plugins
4. Enjoy!

== Frequently Asked Questions ==

= How exactly does the plugin work? = 

Once the plugin is activated and enabled from the "WooCommerce > Settings > Payments" screen your users will see an option to make a payment via Payoneer. 

There is a direct link in the checkout gateway form that will allow the user to make this Payoneer to Payoneer payment prior to checking out. 

Once the payment has been made, the user will add the transaction number to the checkout page, and then complete the order. 

The order will automatically be set to an "on-hold" status for manual verification from you. 

The Payoneer email (for the customer), as well as the transaction ID will be displayed in the order details screen and the edit order screen. 

Once you've verified the payment, you can set the status of the order to "processing" or "completed" 

Both you and customer will receive the necessary order notification emails as always.

= Does Payoneer not have an API? =

Payoneer does not offer any API integration with WooCommerce, as far as we are aware. 

== Screenshots ==

1. The checkout screen

== Upgrade Notice ==

== Changelog ==

= 1.0 =

* initial release