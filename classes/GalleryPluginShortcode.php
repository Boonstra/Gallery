<?php
/**
 * Class GalleryPluginShortcode provides shortcode functionality for the gallery plugin.
 *
 * @since 1.0.0
 * @author: Stefan Boonstra
 */
class GalleryPluginShortcode
{
	/** @var string */
	public static $galleryOverviewShortcode = 'jquery_image_gallery_overview';

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public static function init()
	{
		add_shortcode(self::$galleryOverviewShortcode, array(__CLASS__, 'overview'));
	}

	/**
	 * This function adds a bookmark to where ever a shortcode is found and adds the post ID to an array. It is then
	 * loaded after WordPress has done its HTML checks.
	 *
	 * @since 1.0.0
	 * @param mixed $attributes
	 * @return String $output
	 */
	public static function overview($attributes)
	{
		wp_enqueue_style(
			'jquery-image-gallery-style-light-overview',
			GalleryPluginMain::getPluginUrl() . '/css/light-overview.css',
			array(),
			GalleryPluginMain::$version
		);

		$galleriesQuery = new WP_Query(array(
			'post_type'      => GalleryPluginGallery::$postType,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		));

		$data            = new stdClass();
		$data->galleries = $galleriesQuery->get_posts();

		ob_start();

		GalleryPluginMain::includeFile('views' . DIRECTORY_SEPARATOR . 'galleries.php', $data);

		return ob_get_clean();
	}
}