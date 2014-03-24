/**
 * Gallery backend script
 *
 * @author Stefan Boonstra
 * @version 2.2.12
 */
jquery_image_gallery_backend_script = function()
{
	var $    = jQuery,
		self = {};

	self.isBackendInitialized = false;

	/**
	 * Called by either jQuery's document ready event or JavaScript's window load event in case document ready fails to
	 * fire.
	 *
	 * Triggers the galleryBackendReady on the document to inform all backend scripts they can start.
	 */
	self.init = function()
	{
		if (self.isBackendInitialized)
		{
			return;
		}

		self.isBackendInitialized = true;

		$(document).trigger('galleryBackendReady');
	};

	$(document).ready(self.init);

	$(window).load(self.init);

	return self;
}();

// @codekit-append backend/editGallery.js
