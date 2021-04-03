<?php
/**
 * Cache helper
 */

namespace Pets;

/**
 * Class Pets_Cache
 *
 * @package Pets
 */
class Pets_Cache {

	/**
	 * Get the cache prefix.
	 */
	public static function get_cache_prefix() {
		return apply_filters( 'pets_cache_get_cache_prefix', 'pets_' );
	}

	/**
	 * Setting the cache value.
	 *
	 * @param string  $key      Key of cache.
	 * @param mixed   $value    Value of the cache.
	 * @param integer $duration Duration of the cache.
	 */
	public static function set_cache( $key, $value, $duration = 0 ) {
		$prefix = self::get_cache_prefix();
		set_transient( $prefix . $key, $value, $duration );
	}

	/**
	 * Get the cached value.
	 *
	 * @param string $key Cache key.
	 * @return mixed
	 */
	public static function get_cache( $key ) {
		$prefix = self::get_cache_prefix();
		return get_transient( $prefix . $key );
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public static function delete_cache( $key ) {
		$prefix = self::get_cache_prefix();
		return delete_transient( $prefix . $key );
	}
}