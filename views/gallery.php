<?php if (isset($data) && $data instanceof stdClass): ?>

	<div class="gallery-grid">

		<?php foreach ($data->images as $image): ?>

			<?php $thumbnailImageSource = wp_get_attachment_image_src($image['postID'], 'thumbnail'); ?>
			<?php $fullImageSource      = wp_get_attachment_image_src($image['postID'], 'full'); ?>

			<?php if (!$thumbnailImageSource || !$fullImageSource) continue; ?>

			<div class="gallery-image">

				<div class="gallery-image-border">

					<a href="<?php echo $fullImageSource[0]; ?>"
					   title="<?php echo $image['title']; ?>"
					   rel="lightbox[gallery_group_<?php echo $data->post->ID; ?>]" >

						<img src="<?php echo $thumbnailImageSource[0]; ?>"
						     alt="<?php echo $image['alternativeText']; ?>"
						     title="<?php echo $image['title']; ?>" />

					</a>

				</div>

			</div>

		<?php endforeach; ?>

	</div>

<?php endif; ?>