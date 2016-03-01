<?php
/**
 * Plugin name: Taxonomy/Date Search Hack
 * Description: A terrible method of making standard WP search aware of taxonomy terms and potentially other things. Not recommended for general use ;-)
 * Version:     2016.02.29
 * Author:      Barry Hughes
 * Author URI:  http://codingkills.me
 * License:     GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 *     Taxonomy/Date Search Hack: a utility to improve search.
 *     Copyright (C) 2016 Barry Hughes
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace CodingKillsMe\WP;

use WP_Post;


class ImprovedSearchHack {
	const MARKER = '__I_S_H__';

	protected $post_id = 0;


	public function __construct() {
		add_action( 'wp_insert_post_data', [ $this, 'update_extra_search_terms' ], 10, 2 );
		add_action( 'the_content', [ $this, 'hide_extra_search_terms' ], 5 );
	}

	public function update_extra_search_terms( $data, array $post ) {
		$this->post_id = absint( $post['ID'] );
		$terms = join( ' ', $this->get_taxonomy_terms() );
		$terms = apply_filters( 'ImprovedSearchHack.hidden_terms', $terms, $this->post_id );
		$data['post_content'] = $this->insert_terms( $terms, $data['post_content'] );
		return $data;
	}

	public function get_taxonomy_terms() {
		$all_terms = [];
		$term_list = [];

		foreach ( get_taxonomies( [ 'public' => true ] ) as $taxonomy ) {
			$tax_terms = wp_get_post_terms( $this->post_id, $taxonomy );
			$all_terms = array_merge( $all_terms, $tax_terms );
		}

		foreach ( $all_terms as $term_object ) {
			$term_list[] = $term_object->name;
		}

		return $term_list;
	}

	protected function insert_terms( $terms, $content ) {
		$terms = esc_html( $terms );
		$content = $this->remove_existing_hidden_terms( $content );
		$content .= "\n<!-- " . self::MARKER . " $terms -->\n";
		return $content;
	}

	public function hide_extra_search_terms( $content ) {
		return $this->remove_existing_hidden_terms( $content );
	}

	protected function remove_existing_hidden_terms( $content ) {
		$starts = strpos( $content, '<!-- ' . self::MARKER );
		if ( false === $starts ) return $content;

		$ends = strpos( $content, '-->', $starts );
		if ( false === $ends ) return $content;

		return trim( substr_replace( $content, '', $starts, $ends - $starts + 3 ) );
	}
}

/**
 * @return ImprovedSearchHack
 */
function search_hack() {
	static $object;
	if ( empty( $object ) ) $object = new ImprovedSearchHack;
	return $object;
}

add_action( 'init', function() {
	search_hack();
} );