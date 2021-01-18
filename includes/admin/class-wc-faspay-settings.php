<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Faspay_Settings {

	public static $tab_name = 'faspay_settings';
	public static $option_prefix = 'faspay';
	public static function init() {
		$request = $_REQUEST;
		add_filter('woocommerce_settings_tabs_array', array(__CLASS__, 'add_faspay_settings_tab'), 50);
		add_action('woocommerce_settings_tabs_faspay_settings', array(__CLASS__, 'faspay_settings_page'));
		add_action('woocommerce_update_options_faspay_settings', array(__CLASS__, 'update_faspay_settings'));
		//      add_action( 'admin_enqueue_scripts', array(__CLASS__ , 'enqueue_scripts' ) );
	}

	public static function validate_configuration($request) {
		foreach ($request as $k => $v) {
			$key = str_replace('faspay_', '', $k);
			$options[$key] = $v;
		}
		return '';
	}

	public static function add_faspay_settings_tab($woocommerce_tab) {
		$woocommerce_tab[self::$tab_name] = 'Faspay Payment Gateway ' . __('Global Configuration', 'wc-faspay');
		return $woocommerce_tab;
	}

	public static function faspay_settings_fields() {
		global $faspay_payments;

		$settings = apply_filters('woocommerce_' . self::$tab_name, array(
			array(
				'title' => 'Faspay ' . __('Global Configuration', 'wc-faspay'),
				'id' => self::$option_prefix . '_global_settings',
				'desc' => __('Ini adalah pengaturan global Faspay Payment Gateway. Mohon mengisi form di bawah ini, untuk dapat menggunakan payment channel yang telah tersedia.
				<br \>  untuk mendapatkan API dan merchant code mohon kontak  <a href="mailto:customercare@faspay.co.id">customercare@faspay.co.id</a>', 'wc-faspay'),
				'type' => 'title',
				'default' => '',
			),
			array(
				'title' => __('Merchant Name', 'wc_faspay'),
				'desc' => '<br />' . __('masukkan nama toko anda.', 'wc-faspay'),
				'id' => self::$option_prefix . '_merchant_name',
				'type' => 'text',
				'default' => '',
			),
			array(
				'title' => __('Merchant Code', 'wc_faspay'),
				'desc' => '<br />' . __('masukkan kode merchant anda.', 'wc-faspay'),
				'id' => self::$option_prefix . '_merchant_code',
				'type' => 'text',
				'default' => '',
			),
			array(
				'title' => __('Mechant Password', 'wc_faspay'),
				'desc' => '<br />' . __('masukkan password merchant.', 'wc-faspay'),
				'id' => self::$option_prefix. '_merchant_password',
				'type' => 'text',
				'default' => '',
			),
			array(
				'title' => __('Merchant Credit Card Plan','wc_faspay'),
				'desc' => '<br/>' . __('Pilih jumlah MID Credit Card yang akan digunakan.','wc-faspay'),
				'id' => self::$option_prefix. '_merchant_mid_cc',
				'type' => 'select',
				'options' => array(
					'1' => __('1 MID','woothemes'),
					'2' => __('2 MID','woothemes'),
					'3' => __('3 MID','woothemes'),
					'4' => __('4 MID','woothemes'),
					'5' => __('5 MID','woothemes'),
					'6' => __('6 MID','woothemes'),
					'7' => __('7 MID','woothemes'),
					'8' => __('8 MID','woothemes'),
					'9' => __('9 MID','woothemes'),
					'10'=> __('10 MID','woothemes'),
					'11'=> __('11 MID','woothemes'),
					'12'=> __('12 MID','woothemes'),
				),
				'default' => '1',
			),
			array(
				'title' => __('Faspay Debug', 'wc_faspay'),
				'desc' => '<br />' . sprintf(__('Faspay Log dapat digunakan untuk melihat event, seperti notifikasi pembayaran.<br/>
                 <code>%s</code> ', 'woothemes'), wc_get_log_file_path('faspay')),
				'id' => self::$option_prefix . '_debug',
				'type' => 'checkbox',
				'default' => 'no',
			),
		));
		return apply_filters('woocommerce_' . self::$tab_name, $settings);
	}

	/**
	 * Adds settings fields to the individual sections
	 * Calls from the hook "woocommerce_settings_tabs_" {tab_name}
	 *
	 * @param none
	 * @return void
	 */
	public static function faspay_settings_page() {
		woocommerce_admin_fields(self::faspay_settings_fields());
	}

	/**
	 * Updates settings fields from individual sections
	 * Calls from the hook "woocommerce_update_options_" {tab_name}
	 *
	 * @param none
	 * @return void
	 */
	public static function update_faspay_settings() {
		woocommerce_update_options(self::faspay_settings_fields());
	}

}

Faspay_Settings::init();
