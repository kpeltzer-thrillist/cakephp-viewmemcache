<?php
App::uses('MemcacheEngine', 'Cache/Engine');
/**
 * Thrillist storage engine for cache.  This cache keeps '/'s in the key.
 * Based off of ViewMemcache engine (https://github.com/salgo/cakephp-viewmemcache) with added
 * Thrillist-related logic.
 * Memcache has some limitations in the amount of
 * control you have over expire times far in the future.  See MemcacheEngine::write() for
 * more information.
 *
 */
class ThrillistMemcacheEngine extends MemcacheEngine {
	/**
	 * Don't convert / to _
	 *
	 * @param string $key key to use in memcache
	 * @return mixed string $key or false
	 */
	public function key($key) {
		if (empty($key)) {
			return false;
		}
// 		CakeLog::write('debug', "ViewMemCacheEngine: key (minus cache prefix): {$key}");
		return $key;
	}

	public function 
}
