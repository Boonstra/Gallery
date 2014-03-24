jquery_image_gallery_backend_script.editGallery = function()
{
	var $    = jQuery,
		self = { };

	self.uploader = null;

	/**
	 * Initialize.
	 */
	self.init = function()
	{
		if (window.pagenow === 'gp_gallery')
		{
			self.isCurrentPage = true;

			// Elements
			self.$imagesMetaBox = $('#images');
			self.$imagesGrid    = self.$imagesMetaBox.find('.images-grid');
			self.$imageTemplate = self.$imagesMetaBox.find('.image-template');

			// Activate
			self.activateUploader();
			self.activate();
		}
	};

	/**
	 * Activates the WordPress 3.5 uploader.
	 */
	self.activateUploader = function()
	{
		$('.images-insert-button').on('click', function(event)
		{
			event.preventDefault();

			var uploaderTitle,
				externalData;

			// Reopen file frame if it has already been created
			if (self.uploader)
			{
				self.uploader.open();

				return;
			}

			externalData = window.jquery_image_gallery_backend_script_editGallery;

			uploaderTitle = 'Insert images';

			if (typeof externalData === 'object' &&
				typeof externalData.localization === 'object' &&
				externalData.localization.uploaderTitle !== undefined &&
				externalData.localization.uploaderTitle.length > 0)
			{
				uploaderTitle = externalData.localization.uploaderTitle;
			}

			// Create the uploader
			self.uploader = wp.media.frames.jquery_image_galler_uploader = wp.media({
				frame   : 'select',
				title   : uploaderTitle,
				multiple: true,
				library :
				{
					type: 'image'
				}
			});

			// Create image slide on select
			self.uploader.on('select', function()
			{
				var attachments = self.uploader.state().get('selection').toJSON(),
					attachment,
					attachmentID;

				for (attachmentID in attachments)
				{
					if (!attachments.hasOwnProperty(attachmentID))
					{
						continue;
					}

					attachment = attachments[attachmentID];

					self.insertImage(attachment.id, attachment.title, attachment.description, attachment.url, attachment.alt);
				}
			});

			self.uploader.open();
		});
	};

	/**
	 * Activate.
	 */
	self.activate = function()
	{
		// Index first
		self.indexImageOrder();

		// Make images sortable, exclude elements using the cancel option
		self.$imagesGrid.sortable({
			revert: true,
			placeholder: 'sortable-placeholder',
			forcePlaceholderSize: true,
			stop: function()
			{
				self.indexImageOrder();
			},
			cancel: 'input, select, textarea, span'
		});

        // Delete image on click
		self.$imagesGrid.find('.image-delete-button').click(function(event)
		{
			self.deleteImage($(event.currentTarget).closest('.image'));
		});
	};

	/**
	 * Deletes slide from DOM
	 *
	 * @param $image
	 */
	self.deleteImage = function($image)
	{
		var confirmMessage = 'Are you sure you want to delete this image?',
			extraData      = window.jquery_image_gallery_backend_script_editGallery;

		if (typeof extraData === 'object' &&
			typeof extraData.localization === 'object' &&
			extraData.localization.confirm !== undefined &&
			extraData.localization.confirm.length > 0)
		{
			confirmMessage = extraData.localization.confirm;
		}

		if(!confirm(confirmMessage))
		{
			return;
		}

		// Remove slide from DOM
		$image.remove();
	};

	/**
	 * Loop through images, indexing their order.
	 */
	self.indexImageOrder = function()
	{
		// Loop through images
		self.$imagesGrid.find('.image').each(function(imageID, image)
		{
			// Loop through all fields to set their name attributes with the new index
			$.each($(image).find('input, select, textarea'), function(key, input)
			{
				var $input = $(input),
					name   = $input.attr('name');

				if (name === undefined ||
					name.length <= 0)
				{
					return;
				}

				name = name.replace(/[\[\]']+/g, ' ').split(' ');

				// Put name with new order ID back on the page
				$input.attr('name', name[0] + '[' + (imageID + 1) + '][' + name[2] + ']');
			});
		});
	};

	/**
	 * Inserts image into the images grid.
	 *
	 * @param id
	 * @param title
	 * @param description
	 * @param src
	 * @param alternativeText
	 */
	self.insertImage = function(id, title, description, src, alternativeText)
	{
		// Find and clone the image slide template
		var $image = self.$imageTemplate.find('.image').clone(true, true);

		// Fill slide with data
		$image.find('.image-thumbnail').attr('src', src);
		$image.find('.image-thumbnail').attr('title', title);
		$image.find('.image-thumbnail').attr('alt', alternativeText);
		$image.find('.title').attr('value', title);
		$image.find('.alternativeText').attr('value', alternativeText);
		$image.find('.postID').attr('value', id);

		// Set names to be saved to the database
		$image.find('.title').attr('name', '_images[0][title]');
		$image.find('.alternativeText').attr('name', '_images[0][alternativeText]');
		$image.find('.postID').attr('name', '_images[0][postID]');

		// Put slide in the sortables list.
		self.$imagesGrid.prepend($image);

		// Reindex
		self.indexImageOrder();
	};

	$(document).bind('galleryBackendReady', self.init);

	return self;
}();