<?php

namespace Interart\Flywork\Library;

/**
 * Cache handler.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     1.1
 */
final class Cache
{
    private function parse_key(string $key)
    {
        return preg_replace('/[^a-zA-Z0-9-_\.]/', '', $key);
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @param string $key The cache item key
     *
     * @return bool
     */
    public function has(string $key)
    {
        $key = $this->parse_key($key);

        return file_exists(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache');
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key The cache item key
     * @param mixed $default_value Default value it should returns
     *
     * @return mixed The value of the item from the cache, or $default_value in case of cache miss
     */
    public function get(string $key, $default_value = null)
    {
        $key = $this->parse_key($key);

        if (!$this->has($key)) {
            return $default_value;
        }

        $data = file_get_contents(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache');
        if ($data === false) {
            throw new \Exception('Fail on getting cache data');
        }

        $obj = unserialize($data);
        if ($obj['deadline'] >= time()) {
            return $obj['content'];
        }

        $this->delete($key);

        return $default_value;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key The cache item key
     * @param mixed $value Content to save
     * @param int $ttl Defines cache life in seconds (default: 60 seconds)
     *
     * @return void
     */
    public function save(string $key, $value, int $ttl = 60)
    {
        $key = $this->parse_key($key);

        $data = [
            'deadline' => time() + $ttl,
            'content'  => $value,
        ];
        if (!is_dir(WRITEPATH . 'cache')) {
            throw new \Exception('Cache folder does not exist.');
        }

        $file = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache';
        file_put_contents($file, serialize($data));
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The cache item key
     *
     * @return void
     */
    public function delete(string $key)
    {
        $key = $this->parse_key($key);

        if (file_exists($file = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache')) {
            @unlink($file);
        }
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return void
     */
    public function clear()
    {
        @array_map('unlink', glob(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . '*.cache') ?? []);
    }
}
