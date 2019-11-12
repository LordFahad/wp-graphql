<?php
namespace WPGraphQL\Connection;

use WPGraphQL\Data\DataSource;
use WPGraphQL\Registry\TypeRegistry;

class Revisions {

	/**
	 * Register connections to Revisions
	 *
	 * @param TypeRegistry $type_registry Instance of the TypeRegistry
	 */
	public static function register_connections( TypeRegistry $type_registry ) {

		/**
		 * The Root Query
		 */
		register_graphql_connection( [
			'fromType'       => 'RootQuery',
			'toType'         => 'ContentRevisionUnion',
			'queryClass'     => 'WP_Query',
			'resolveNode'    => function( $id, $args, $context, $info ) {
				return DataSource::resolve_post_object( $id, $context );
			},
			'fromFieldName'  => 'revisions',
			'connectionArgs' => PostObjects::get_connection_args(),
			'resolve'        => function( $root, $args, $context, $info ) {
				return DataSource::resolve_post_objects_connection( $root, $args, $context, $info, 'revision' );
			},
		] );

	}
}
