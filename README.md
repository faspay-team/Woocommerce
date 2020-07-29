# Faspay  WooCommerce - Wordpress Payment Gateway Module

Faspay  ❤️ WooCommerce! Receive online payment on your WooCommerce store with Faspay payment gateway integration plugin.


# Description

This plugin will allow secure online payment on your WooCommerce store, without your customer ever need to leave your WooCommerce store! With beautiful responsive payment interface built-in.  Support WooCommerce the latest Version.

* _Payment Method Feature:_
	- Credit card fullpayment and other payment methods.
	- Bank transfer, internet banking for various banks
	- Credit card Online & offline installment payment.
	- Credit card MIGS acquiring channel.
	- Cardless channels like Akulau, Kredivo, Indodana.
	- Payment channel QRIS. 


### Installation

### Minimum Requirements
	- WordPress v3.9 or greater (tested up to v5.4.2)
	- WooCommerce v2 or greater (tested up to v4.2.2)
	- PHP version v5.4 or greater
	- MySQL version v5.0 or greater
	- PHP CURL enabled server/host


### Simple Installation
	1. Login to your Wordpress admin panel.
	2. Go to Plugins menu, click add new. Search on your local folder.
	3. Install and follow on screen instructions.
	4. Proceed to step 5 below.

### Manual Installation
1. [Download](https://raw.githubusercontent.com/faspay-team/Woocommerce/master/woocommerce-gateway-faspay.zip) the plugin from this repository.
2. Extract the plugin, Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation's `wp-content/plugins/ directory.`
3. Install & Activate the plugin from the Plugins menu within the WordPress admin panel.
4. Go to menu __WooCommerce > Settings > Faspay Payment Gateway Global Configuration__ , fill the configuration fields.
	- Fill in the __merchant code, merchant name, merchant password__  with your corresonding Faspay  account credentials and __merchant credit card Plan__ (1 MID or more)
	- Note: Credentials for Development  & Production is different, make sure you use the correct one.
	- Other configuration are optional, you may leave it as is.
5. Go to menu __WooCommerce > Settings > Payment >__ for activate for each channels:
	- Fill Title payment channels with text button that you want to display to customer.
	- Select Environment, Development is for testing transaction, Production is for real transaction.
	- Define the expired time for each payment channels
	
	
