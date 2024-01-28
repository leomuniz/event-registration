var page = ( function( document, $ ) {

	var app = {

		/**
		 * is_ready() method - Executes the method received when the document is ready.
		 * @since 1.0.0
		 * @param {Function} callback Function to be executed when the document is ready.
		 */
		isReady: function( callback ) {
			$( document ).ready( function() {
				callback();
			});
		},

		/**
		 * start() method - Page start.
		 * @since 1.0.0
		 */
		start: function() {

			app.setVariables();
			app.setEvents();
		},

		/**
		 * setVariables() method - Set app variables.
		 * @since 1.0.0
		 */
		setVariables: function() {

			app.scrollOffset = 100;

			// `plugin` variable is defined by wp_localize_script().
			/* eslint-disable no-undef */
			app.userTable = $( 'table#' + plugin.usersTableId );
			app.userDetailsContainer = $( '#' + plugin.userDetailsId );
			app.loadingGif = $( '#' + plugin.loadingGifId );
			/* eslint-enable no-undef */
		},

		/**
		 * setEvents() method - Set events to be executed
		 * @since 1.0.0
		 */
		setEvents: function() {
			app.userTable.on( 'click', 'tbody tr', app.fetchUserDetails );
		},

		/**
		 * fetchUserDetails() method - AJAX call to fetch user details.
		 * @param {object} event Click event.
		 * @since 1.0.0
		 */
		fetchUserDetails: function( event ) {

			var wasSelected = $( this ).hasClass( 'selected' );
			var ajaxData = {};

			event.preventDefault();

			app.userTable.find( 'tr' ).removeClass( 'selected' );
			app.userDetailsContainer.html( '' );

			if ( wasSelected ) {
				return;
			}

			$( this ).addClass( 'selected' );
			app.loadingGif.show();
			app.animateTo( app.userDetailsContainer, 1000 );

			// `plugin` variable is defined by wp_localize_script().
			/* eslint-disable no-undef */
			ajaxData.action = plugin.userDetailsAjaxAction;
			ajaxData.userId = $( this ).data( 'user-id' );
			ajaxData._wpnonce = plugin._wpnonce;

			$.ajax({
				type: 'POST',
				url: plugin.ajaxUrl,
				data: ajaxData,
				dataType: 'html',
				success: function( response ) {
					app.userDetailsContainer.html( response );
				},
				error: function( xhr, status, error ) {
					console.error( 'Error: ' + xhr.status + ' - ' + error );
				},
				complete: function() {
					app.loadingGif.hide();
				}
			});
			/* eslint-enable no-undef */
		},

		/**
		 * animateTo() method - Scroll animation to the element.
		 * @param {object} element Element to animate.
		 * @param {number} seconds Animation duration.
		 * @since 1.0.0
		 */
		animateTo: function( element, seconds ) {
			$( 'html, body' ).animate({
				scrollTop: element.offset().top - app.scrollOffset
			}, seconds );
		}
	};

	return app;

}( document, jQuery ) );

page.isReady( page.start );
