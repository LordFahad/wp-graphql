<?php
namespace WPGraphQL\Type;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

class EditLock {
	public static function register_type() {
		register_graphql_object_type( 'EditLock', [
			'description' => __( 'Info on whether the object is locked by another user editing it', 'wp-graphql' ),
			'fields'      => [
				'editTime' => [
					'type'        => 'String',
					'description' => __( 'The time when the object was last edited', 'wp-graphql' ),
					'resolve'     => function ( $edit_lock, array $args, AppContext $context, ResolveInfo $info ) {
						$time = ( is_array( $edit_lock ) && ! empty( $edit_lock[0] ) ) ? $edit_lock[0] : null;

						return ! empty( $time ) ? date( 'Y-m-d H:i:s', $time ) : null;
					},
				],
				'user'     => [
					'type'        => 'User',
					'description' => __( 'The user that most recently edited the object', 'wp-graphql' ),
					'resolve'     => function ( $edit_lock, array $args, AppContext $context, ResolveInfo $info ) {
						$user_id = ( is_array( $edit_lock ) && ! empty( $edit_lock[1] ) ) ? $edit_lock[1] : null;

						return ! empty( $user_id ) ? DataSource::resolve_user( $user_id ) : null;
					},
				],
			],
		] );
	}
}