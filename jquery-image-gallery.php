<?php
/*
 Plugin Name: Gallery
 Plugin URI: http://wordpress.org/extend/plugins/jquery-image-gallery/
 Description:
 Version: 1.0.0
 Requires at least: 3.8
 Author: StefanBoonstra
 Author URI: http://stefanboonstra.com/
 License: GPLv2
 Text Domain: jquery-image-gallery
*/

/**
 * Class GalleryPluginMain fires up the application on plugin load and provides some basic methods.
 *
 * @since 1.0.0
 * @author Stefan Boonstra
 */
class GalleryPluginMain
{
	/** @var string $version */
	public static $version = '1.0.0';

	/**
	 * Bootstraps the application by assigning the right functions to
	 * the right action hooks.
	 *
	 * @since 1.0.0
	 */
	public static function bootStrap()
	{
		self::autoInclude();

		// Initialize localization on init
		add_action('init', array(__CLASS__, 'localize'));

		// Include backend scripts and styles
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueueBackendScripts'));

		// Initialize gallery
		GalleryPluginGallery::init();

		// Initialize shortcode
		GalleryPluginShortcode::init();
	}

	/**
	 * Includes backend styles and scripts.
	 *
	 * Should always be called on the admin_enqueue_scrips hook.
	 *
	 * @since 2.2.12
	 */
	static function enqueueBackendScripts()
	{
		// Function get_current_screen() should be defined, as this method is expected to fire at 'admin_enqueue_scripts'
		if (!function_exists('get_current_screen'))
		{
			return;
		}

		$currentScreen = get_current_screen();

		// Enqueue 3.5 uploader
		if ($currentScreen->post_type === GalleryPluginGallery::$postType &&
			function_exists('wp_enqueue_media'))
		{
			wp_enqueue_media();
		}

		wp_enqueue_script(
			'jquery-image-gallery-backend-script',
			self::getPluginUrl() . '/js/min/all.backend.min.js',
			array(
				'jquery',
				'jquery-ui-sortable',
			),
			GalleryPluginMain::$version
		);

		wp_enqueue_style(
			'jquery-image-gallery-backend-style',
			self::getPluginUrl() . '/css/all.backend.css',
			array(),
			GalleryPluginMain::$version
		);
	}

	/**
	 * Translates the plugin
	 *
	 * @since 1.0.0
	 */
	public static function localize()
	{
		load_plugin_textdomain(
			'jquery-image-gallery',
			false,
			dirname(plugin_basename(__FILE__)) . '/languages/'
		);
	}

	/**
	 * Returns url to the base directory of this plugin.
	 *
	 * @since 1.0.0
	 * @return string pluginUrl
	 */
	public static function getPluginUrl()
	{
		return plugins_url('', __FILE__);
	}

	/**
	 * Returns path to the base directory of this plugin
	 *
	 * @since 1.0.0
	 * @return string pluginPath
	 */
	public static function getPluginPath()
	{
		return dirname(__FILE__);
	}

	/**
	 * Includes the passed file.
	 *
	 * @param string $file
	 * @param mixed $data (optional, defaults to null)
	 */
	public static function includeFile($file, $data = null)
	{
		include self::getPluginPath() . DIRECTORY_SEPARATOR . $file;
	}

	/**
	 * This function will load classes automatically on-call.
	 *
	 * @since 1.0.0
	 */
	public static function autoInclude()
	{
		if (!function_exists('spl_autoload_register'))
		{
			return;
		}

		function galleryPluginAutoLoader($name)
		{
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $name . '.php';

			if (is_file($file))
			{
				require_once $file;
			}
		}

		spl_autoload_register('galleryPluginAutoLoader');
	}
}

/**
 * Activate plugin
 */
GalleryPluginMain::bootStrap();