<?php if (isset($data) && $data instanceof stdClass): ?>

	<div class="gallery-overview-gallery-grid">

		<?php foreach ($data->galleries as $gallery): ?>

			<div class="gallery-overview-gallery">

				<div class="gallery-overview-gallery-border">

					<div class="gallery-overview-gallery-thumbnail">

						<a href="<?php echo get_permalink($gallery->ID); ?>" rel="nobox">
							<?php echo get_the_post_thumbnail($gallery->ID, 'thumbnail'); ?>
						</a>

					</div>

					<div class="gallery-overview-gallery-title">

						<a href="<?php echo get_permalink($gallery->ID); ?>">
							<?php echo $gallery->post_title; ?>
						</a>

					</div>

				</div>

			</div>

		<?php endforeach; ?>

	</div>

<?php endif; ?>