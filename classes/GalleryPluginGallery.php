<?php
/**
 * GalleryPluginGallery acts as the gallery model, installing itself as a custom post type.
 *
 * @since 1.0.0
 * @author: Stefan Boonstra
 */
class GalleryPluginGallery
{
	/** @var string */
	public static $postType = 'gp_gallery';

	/** @var string */
	public static $nonceAction = 'jquery-image-gallery-nonce-action';

	/** @var string */
	public static $nonceName = 'jquery-image-gallery-nonce-name';

	/** @var string */
	public static $imagesPostMetaKey = '_images';

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public static function init()
	{
		add_action('init'                 , array(__CLASS__, 'registerPostType'));
		add_action('save_post'            , array(__CLASS__, 'save'));
		add_action('wp_enqueue_scripts'   , array(__CLASS__, 'enqueueScripts'), 11);
		add_action('admin_enqueue_scripts', array(__CLASS__, 'localizeScript'), 11);

		add_filter('the_content', array(__CLASS__, 'filterTheContent'));
	}

	/**
	 * Enqueue scripts and styles for frontend use.
	 *
	 * @since 1.0.0
	 */
	public static function enqueueScripts()
	{
		if (get_post_type() !== self::$postType)
		{
			return;
		}

		wp_enqueue_style(
			'jquery-image-gallery-style-light',
			GalleryPluginMain::getPluginUrl() . '/css/light.css',
			array(),
			GalleryPluginMain::$version
		);
	}

	/**
	 * Enqueues styles and scripts necessary for the gallery page.
	 *
	 * @since 1.0.0
	 */
	public static function localizeScript()
	{
		// Return if function doesn't exist
		if (!function_exists('get_current_screen'))
		{
			return;
		}

		// Return when not on a slideshow edit page
		$currentScreen = get_current_screen();

		if ($currentScreen->post_type != self::$postType)
		{
			return;
		}

		wp_localize_script(
			'jquery-image-gallery-backend-script',
			'jquery_image_gallery_backend_script_editGallery',
			array(
				'data' => array(),
				'localization' => array(
					'confirm'       => __('Are you sure you want to delete this image?', 'jquery-image-gallery'),
					'uploaderTitle' => __('Insert images', 'jquery-image-gallery')
				)
			)
		);
	}

	/**
	 * Registers new post type slideshow
	 *
	 * @since 1.0.0
	 */
	public static function registerPostType()
	{
		register_post_type(
			self::$postType,
			array(
				'labels'               => array(
					'name'               => __('Galleries'         , 'jquery-image-gallery'),
					'singular_name'      => __('Gallery'           , 'jquery-image-gallery'),
					'menu_name'          => __('Galleries'         , 'jquery-image-gallery'),
					'add_new'            => __('Add New'           , 'jquery-image-gallery'),
					'add_new_item'       => __('Add New Gallery'   , 'jquery-image-gallery'),
					'new_item'           => __('New Gallery'       , 'jquery-image-gallery'),
					'edit_item'          => __('Edit Gallery'      , 'jquery-image-gallery'),
					'view_item'          => __('View Gallery'      , 'jquery-image-gallery'),
					'all_items'          => __('All Galleries'     , 'jquery-image-gallery'),
					'search_items'       => __('Search Galleries'  , 'jquery-image-gallery'),
					'parent_item_colon'  => __('Parent Galleries:' , 'jquery-image-gallery'),
					'not_found'          => __('No galleries found', 'jquery-image-gallery'),
					'not_found_in_trash' => __('No galleries found', 'jquery-image-gallery'),
				),
				'public'               => true,
				'publicly_queryable'   => true,
				'show_ui'              => true,
				'show_in_menu'         => true,
				'query_var'            => true,
				'rewrite'              => array('slug' => __('gallery', 'jquery-image-gallery')),
				'capability_type'      => 'post',
				'hierarchical'         => false,
				'menu_position'        => null,
				'menu_icon'            => 'dashicons-screenoptions',
				'supports'             => array('title', 'thumbnail'),
				'register_meta_box_cb' => array(__CLASS__, 'registerMetaBoxes')
			)
		);
	}

	/**
	 * Adds custom meta boxes.
	 *
	 * @since 1.0.0
	 */
	public static function registerMetaBoxes()
	{
		add_meta_box(
			'images',
			__('Images', 'jquery-image-gallery'),
			array(__CLASS__, 'imagesMetaBox'),
			self::$postType,
			'normal',
			'high'
		);
	}

	/**
	 * Shows all images in the gallery.
	 *
	 * @since 1.0.0
	 */
	public static function imagesMetaBox()
	{
		global $post;

		wp_nonce_field(self::$nonceAction, self::$nonceName);

		// Get all images
		$images = get_post_meta($post->ID, self::$imagesPostMetaKey, true);

		if (!is_array($images))
		{
			$images = array();
		}

		$data              = new stdClass();
		$data->postMetaKey = self::$imagesPostMetaKey;
		$data->images      = $images;

		GalleryPluginMain::includeFile('views' . DIRECTORY_SEPARATOR . 'images_meta_box.php', $data);
	}

	/**
	 * Hook into the content filter and add the images of a gallery.
	 *
	 * @since 1.0.0
	 * @param string $content
	 * @return string $content
	 */
	public static function filterTheContent($content)
	{
		global $post;

		if (get_post_type() !== self::$postType)
		{
			return $content;
		}

		// Get all images
		$images = get_post_meta($post->ID, self::$imagesPostMetaKey, true);

		if (!is_array($images))
		{
			$images = array();
		}

		$data         = new stdClass();
		$data->post   = $post;
		$data->images = $images;

		ob_start();

		GalleryPluginMain::includeFile('views' . DIRECTORY_SEPARATOR . 'gallery.php', $data);

		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Save.
	 *
	 * @since 1.0.0
	 * @param int $postID
	 * @return int $postID
	 */
	public static function save($postID)
	{
		// Verify nonce, check if user has sufficient rights and return on auto-save.
		if (get_post_type($postID) != self::$postType ||
			(!isset($_POST[self::$nonceName]) || !wp_verify_nonce($_POST[self::$nonceName], self::$nonceAction)) ||
			!current_user_can('edit_post', $postID) ||
			(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE))
		{
			return $postID;
		}

		// Get images
		$newPostImages = filter_input(INPUT_POST, self::$imagesPostMetaKey, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

		// Save
		update_post_meta($postID, self::$imagesPostMetaKey, $newPostImages);

		return $postID;
	}
}