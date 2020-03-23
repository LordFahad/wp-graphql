<?php
/**
 * This class organizes the registration of connections to Taxonomies
 *
 * @package WPGraphQL\Connection
 */

namespace WPGraphQL\Connection;

use WPGraphQL\Data\DataSource;

/**
 * Class Taxonomies
 */
class Taxonomies {
	/**
	 * Registers connections
	 */
	public static function register_connections() {

		register_graphql_connection(
			[
				'fromType'      => 'RootQuery',
				'toType'        => 'Taxonomy',
				'fromFieldName' => 'taxonomies',
				'resolve'       => function( $source, $args, $context, $info ) {
					return DataSource::resolve_taxonomy_connection( $source, $args, $context, $info );
				},
			]
		);

		$taxonomies = get_taxonomies( [ 'show_in_graphql' => true ], 'OBJECT' );

		if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				register_graphql_connection(
					[
						'fromType'      => $taxonomy->graphql_single_name,
						'toType'        => 'Taxonomy',
						'fromFieldName' => 'taxonomy',
						'oneToOne'      => true,
						'resolve'       => function( $source, $args, $context, $info ) {
							return DataSource::resolve_taxonomy_connection( $source, $args, $context, $info );
						},
					]
				);
			}
		}

	}
}
