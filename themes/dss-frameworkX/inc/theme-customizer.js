( function( $ ){
	wp.customize( 'theme_heading', function( value ) {
		value.bind( function( to ) {
			$( '#site-title a' ).html( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '#site-description' ).html( to );
		} );
	} );
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '#theme_heading a' ).html( to );
		} );
	} );
} )( jQuery );
