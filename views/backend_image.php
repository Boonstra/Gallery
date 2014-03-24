<?php if (isset($data) && $data instanceof stdClass): ?>

	<?php $attachmentImageSource = wp_get_attachment_image_src($data->image['postID'], 'medium'); ?>

	<?php if ($attachmentImageSource !== false): ?>

		<div class="image sortable-image">

			<div class="image-border">

				<div class="image-thumbnail-container">
					<img src="<?php echo $attachmentImageSource[0]; ?>"
					     alt="<?php echo $data->image['alternativeText']; ?>"
					     title="<?php echo $data->image['title']; ?>"
					     class="image-thumbnail" />
				</div>

				<div class="image-group">
					<label>
						<?php _e('Title', 'jquery-image-gallery'); ?><br />
						<input type="text" name="<?php echo $data->postMetaKey ?>[0][title]" value="<?php echo $data->image['title']; ?>" />
					</label>
				</div>

				<div class="image-group">
					<label>
						<?php _e('Alternative text', 'jquery-image-gallery'); ?><br />
						<input type="text" name="<?php echo $data->postMetaKey ?>[0][alternativeText]" value="<?php echo $data->image['alternativeText']; ?>" />
					</label>
				</div>

				<div class="image-group">
					<span class="image-delete-button"><?php _e('Delete image', 'jquery-image-gallery'); ?></span>
				</div>

				<input type="hidden" name="<?php echo $data->postMetaKey ?>[0][postID]" value="<?php echo $data->image['postID']; ?>" />

			</div>

		</div>

	<?php endif; ?>

<?php endif; ?>