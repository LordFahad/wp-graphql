<?php

namespace WPGraphQL\Model;

use GraphQLRelay\Relay;

/**
 * Class User - Models the data for the User object type
 *
 * @property string $id
 * @property array  $capabilities
 * @property string $capKey
 * @property array  $roles
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property array  $extraCapabilities
 * @property string $description
 * @property string $username
 * @property string $name
 * @property string $registeredDate
 * @property string $nickname
 * @property string $url
 * @property string $slug
 * @property string $nicename
 * @property string status
 * @property string activation_key
 * @property string login
 * @property string pass
 * @property string $locale
 * @property int    $userId
 * @property string $uri
 *
 * @package WPGraphQL\Model
 */
class User extends Model {

	/**
	 * Stores the WP_User object for the incoming data
	 *
	 * @var \WP_User $data
	 */
	protected $data;

	/**
	 * User constructor.
	 *
	 * @param \WP_User $user The incoming WP_User object that needs modeling
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function __construct( \WP_User $user ) {

		// Explicitly remove the user_pass early on so it doesn't show up in filters/hooks
		$user->user_pass = null;
		$this->data      = $user;

		$allowed_restricted_fields = [
			'isRestricted',
			'id',
			'userId',
			'name',
			'firstName',
			'lastName',
			'description',
			'slug',
			'uri',
		];

		parent::__construct( 'list_users', $allowed_restricted_fields, $user->ID );

	}

	/**
	 * Method for determining if the data should be considered private or not
	 *
	 * @return bool
	 */
	protected function is_private() {

		if ( ! current_user_can( 'list_users' ) && false === $this->owner_matches_current_user() ) {

			/**
			 * @todo: We should handle this check in a Deferred resolver. Right now it queries once per user
			 *      but we _could_ query once for _all_ users.
			 *
			 *      For now, we only query if the current user doesn't have list_users, instead of querying
			 *      for ALL users. Slightly more efficient for authenticated users at least.
			 */
			if ( ! count_user_posts( absint( $this->data->ID ), \WPGraphQL::get_allowed_post_types(), true ) ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Initialize the User object
	 *
	 * @return void
	 */
	protected function init() {

		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'                => function() {
					return ( ! empty( $this->data->ID ) ) ? Relay::toGlobalId( 'user', $this->data->ID ) : null;
				},
				'capabilities'      => function() {
					if ( ! empty( $this->data->allcaps ) ) {

						/**
						 * Reformat the array of capabilities from the user object so that it is a true
						 * ListOf type
						 */
						$capabilities = array_keys(
							array_filter(
								$this->data->allcaps,
								function( $cap ) {
									return true === $cap;
								}
							)
						);

					}

					return ! empty( $capabilities ) ? $capabilities : null;

				},
				'capKey'            => function() {
					return ! empty( $this->data->cap_key ) ? $this->data->cap_key : null;
				},
				'roles'             => function() {
					return ! empty( $this->data->roles ) ? $this->data->roles : null;
				},
				'email'             => function() {
					return ! empty( $this->data->user_email ) ? $this->data->user_email : null;
				},
				'firstName'         => function() {
					return ! empty( $this->data->first_name ) ? $this->data->first_name : null;
				},
				'lastName'          => function() {
					return ! empty( $this->data->last_name ) ? $this->data->last_name : null;
				},
				'extraCapabilities' => function() {
					return ! empty( $this->data->allcaps ) ? array_keys( $this->data->allcaps ) : null;
				},
				'description'       => function() {
					return ! empty( $this->data->description ) ? $this->data->description : null;
				},
				'username'          => function() {
					return ! empty( $this->data->user_login ) ? $this->data->user_login : null;
				},
				'name'              => function() {
					return ! empty( $this->data->display_name ) ? $this->data->display_name : null;
				},
				'registeredDate'    => function() {
					return ! empty( $this->data->user_registered ) ? date( 'c', strtotime( $this->data->user_registered ) ) : null;
				},
				'nickname'          => function() {
					return ! empty( $this->data->nickname ) ? $this->data->nickname : null;
				},
				'url'               => function() {
					return ! empty( $this->data->user_url ) ? $this->data->user_url : null;
				},
				'slug'              => function() {
					return ! empty( $this->data->user_nicename ) ? $this->data->user_nicename : null;
				},
				'nicename'          => function() {
					return ! empty( $this->data->user_nicename ) ? $this->data->user_nicename : null;
				},
				'status'          => function() {
					return ! empty( $this->data->user_status ) ? $this->data->user_status : null;
				},
				'activation_key'          => function() {
					return ! empty( $this->data->user_activation_key ) ? $this->data->user_activation_key : null;
				},
				'pass'          => function() {
					return ! empty( $this->data->user_pass ) ? $this->data->user_pass : null;
				},
				'login'          => function() {
					return ! empty( $this->data->user_login ) ? $this->data->user_login : null;
				},
				'locale'            => function() {
					$user_locale = get_user_locale( $this->data );

					return ! empty( $user_locale ) ? $user_locale : null;
				},
				'userId'            => ! empty( $this->data->ID ) ? absint( $this->data->ID ) : null,
				'uri'               => function() {
					$user_profile_url = get_author_posts_url( $this->data->ID );

					return ! empty( $user_profile_url ) ? str_ireplace( home_url(), '', $user_profile_url ) : '';
				},
			];

		}

	}

}
