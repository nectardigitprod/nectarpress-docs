<?php
/**
 * NectarPress — Recipe: Copyscape Plagiarism Check Integration
 *
 * This is an EXAMPLE FILE only. It is NOT active code.
 * Place this hook in your child theme's functions.php or a site-specific plugin.
 *
 * Requires: Copyscape Premium account (https://www.copyscape.com/pro.php)
 *           Username + API key from copyscape.com/developers
 *
 * How it works:
 *   The nectarpress/nr/before_publish_check filter runs when an editor attempts
 *   to move a story to 'approved' or 'published'. If this filter returns a WP_Error,
 *   the transition is blocked and the editor sees the error message.
 *
 * @package NectarPress\Recipes
 */

// --- DO NOT include this file in production without removing the 'return' below ---
return;

add_filter( 'nectarpress/nr/before_publish_check', function ( $allowed, int $post_id, array $context ) {

	// Only run on transitions toward publication
	if ( ! in_array( $context['to'] ?? '', [ 'approved', 'published' ], true ) ) {
		return $allowed;
	}

	$copyscape_user = 'your_copyscape_username';
	$copyscape_key  = 'your_copyscape_api_key';

	$content    = get_post_field( 'post_content', $post_id );
	$first_200w = wp_trim_words( wp_strip_all_tags( $content ), 200, '' );

	if ( strlen( $first_200w ) < 50 ) {
		return $allowed; // too short to check meaningfully
	}

	$response = wp_remote_post( 'https://www.copyscape.com/api/', [
		'timeout' => 30,
		'body'    => [
			'u'    => $copyscape_user,
			'k'    => $copyscape_key,
			'o'    => 'csearch',
			'q'    => $first_200w,
			'f'    => 'json',
			'e'    => 'UTF-8',
		],
	] );

	if ( is_wp_error( $response ) ) {
		// Non-fatal: Copyscape is unreachable, let publish proceed
		error_log( 'NectarPress: Copyscape check failed — ' . $response->get_error_message() );
		return $allowed;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $data ) ) {
		return $allowed; // unexpected response — allow
	}

	$match_count = (int) ( $data['count'] ?? 0 );
	if ( $match_count > 0 ) {
		$top_match_url = $data['result'][0]['url'] ?? __( 'unknown source', 'nectarpress' );
		return new WP_Error(
			'plagiarism_detected',
			sprintf(
				/* translators: 1: match count, 2: top matching URL */
				__( 'Copyscape found %1$d potentially matching source(s). Top match: %2$s — Please review before publishing.', 'nectarpress' ),
				$match_count,
				$top_match_url
			)
		);
	}

	return $allowed;

}, 10, 3 );
