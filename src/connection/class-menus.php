<?php
/**
 * This class organizes the registration of connections to Menus
 *
 * @package WPGraphQL\Connection
 */

namespace WPGraphQL\Connection;

use WPGraphQL\Data\DataSource;

/**
 * Class Menus
 */
class Menus {

	/**
	 * Registers connections to Menus
	 */
	public static function register_connections() {

		/**
		 * Registers the RootQueryToMenuConnection
		 */
		register_graphql_connection(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Menu',
				'fromFieldName'  => 'menus',
				'connectionArgs' => [
					'id'       => [
						'type'        => 'Int',
						'description' => __( 'The ID of the object', 'wp-graphql' ),
					],
					'location' => [
						'type'        => 'MenuLocationEnum',
						'description' => __( 'The menu location for the menu being queried', 'wp-graphql' ),
					],
					'slug'     => [
						'type'        => 'String',
						'description' => __( 'The slug of the menu to query items for', 'wp-graphql' ),
					],
				],
				'resolve'        => function( $root, $args, $context, $info ) {
					return DataSource::resolve_menu_connection( $root, $args, $context, $info );
				},
			]
		);
	}
}
