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
class ViewMemcacheEngine extends MemcacheEngine {
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
//    CakeLog::write('debug', "ViewMemCacheEngine: key (minus cache prefix): {$key}");
    return $key;
  }

  /**
   * Overwriting write function of Cake's memcache, adding the support for bins similar to Thrillist implementation.
   * We want to make sure ths is backwards compatible with any Cake functions automatically using this, so we
   * need defaults for our new params table 
   * @param  [array] $key      An array with key=> and table=>. If not an array, will use default table as 'cache'
   * @param  [array] $value    An array with value=> and type=>
   * @param  [type] $duration [description]
   * @return [type]           [description]
   */
  public function write($key_array, $value_array, $duration) {
    $created = time();
    if (is_array($key_array)){
      $key = isset($key_array['key']) ? $key_array['key'] : '';
      $table = isset($key_array['table']) ? $key_array['table'] : 'cache';
    }
    else{
      $key = $key_array;
      $table = 'cache';
    }

    if (is_array($value_array)){
      $value = isset($value_array['value']) ? $value_array['value'] : '';
      $type = isset($value_array['type']) ? $value_array['type'] : null;
    }
    else{
      $type = null;
      $value = $value_array;
    }
    //if (!is_null($duration)) Cache::set(array('duration' => $duration,null,'view_memcache'));
    $cid = $this->generateMemcacheKey($key, $table);

    // Create new cache object.
    $cache = new stdClass;
    $cache->key = $key;
    $cache->cid = $cid;
    $cache->type = $type;
    $cache->data = is_object($data) ? clone($value) : $value; //Warning - PHP5 only!
    $cache->created = $created;

    return parent::write($cid, $cache, $duration);
  }

  /**
   * A function to clear the cache based on the key and table.
   * @param  [type] $key   [description]
   * @param  string $table [description]
   * @return [type]        [description]
   */
  public function clear($key, $table = 'cache'){
    if (!$key){
      return false;
    }

    $key = $this->generateMemcacheKey($key, $table);

    return parent::delete($key);
  }

  /**
   * Overwriting read function to allow us to return the full object.
   * Takes either a single key, or an array('key' => '', table => '')
   * @param  [type]  $key          [description]
   * @param  string  $table        [description]
   * @param  boolean $returnObject [description]
   * @return [type]                [description]
   */
  public function read($params){
    //If its an array, treat as 
    if (is_array($params)){
      $key = isset($params['key']) ? $params['key'] : '';
      $table = isset($params['table']) ? $params['table'] : 'cache';
    }
    else{
      $key = $params;
      $table = 'cache';
    }
    $cid = $this->generateMemcacheKey($key, $table);
    $data = parent::read($cid);
    return $data;
  }

  private function generateMemcacheKey($key, $table){
    if (!$key){
      return false;
    }
    return $table . '-'  . $key;
  }

  
}
