<?php
/**
 * This class organizes registration of connections to MenuItems
 *
 * @package WPGraphQL\Connection
 */

namespace WPGraphQL\Connection;

use WPGraphQL\Data\DataSource;

/**
 * Class Menu_Items
 */
class Menu_Items {

	/**
	 * Register connections to MenuItems
	 */
	public static function register_connections() {

		/**
		 * Register the RootQueryToMenuItemsConnection
		 */
		register_graphql_connection( self::get_connection_config() );

		/**
		 * Registers the ChildItems connection to the MenuItem Type
		 * MenuItemToMenuItemConnection
		 */
		register_graphql_connection(
			self::get_connection_config(
				[
					'fromType'      => 'MenuItem',
					'fromFieldName' => 'childItems',
				]
			)
		);

		/**
		 * Register the MenuToMenuItemsConnection
		 */
		register_graphql_connection(
			self::get_connection_config(
				[
					'fromType' => 'Menu',
				]
			)
		);

	}

	/**
	 * Given an array of $args, returns the args for the connection with the provided args merged
	 *
	 * @param array $args  Connection configurations.
	 *
	 * @return array
	 */
	public static function get_connection_config( $args = [] ) {
		return array_merge(
			[
				'fromType'       => 'RootQuery',
				'fromFieldName'  => 'menuItems',
				'toType'         => 'MenuItem',
				'connectionArgs' => [
					'id'       => [
						'type'        => 'Int',
						'description' => __( 'The ID of the object', 'wp-graphql' ),
					],
					'location' => [
						'type'        => 'MenuLocationEnum',
						'description' => __( 'The menu location for the menu being queried', 'wp-graphql' ),
					],
				],
				'resolve'        => function( $root, $args, $context, $info ) {
					return DataSource::resolve_menu_item_connection( $root, $args, $context, $info );
				},
			],
			$args
		);
	}

}
