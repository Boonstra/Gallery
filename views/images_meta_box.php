<?php if (isset($data) && $data instanceof stdClass): ?>

	<div class="images-toolbar">
		<button class="button images-insert-button"><?php _e('Insert image', 'jquery-image-gallery'); ?></button>
	</div>

	<div class="images-grid">
		<?php

		$imageData              = new stdClass();
		$imageData->postMetaKey = $data->postMetaKey;

		foreach ($data->images as $image)
		{
			$imageData->image = $image;

			GalleryPluginMain::includeFile('views' . DIRECTORY_SEPARATOR . 'backend_image.php', $imageData);
		}

		?>

		<div class="clear"></div>
	</div>

	<?php GalleryPluginMain::includeFile('views' . DIRECTORY_SEPARATOR . 'backend_image_template.php', $data); ?>

<?php endif; ?>