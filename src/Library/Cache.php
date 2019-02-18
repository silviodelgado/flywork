<?php

namespace Interart\Flywork\Library;

/**
 * Cache manipulation.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     1.1
 */
final class Cache
{

    /**
     * Default constructor
     */
    public function __construct()
    {

    }

    /**
     * Returns cache content for given $key
     *
     * @param string $key Cache reference
     * @param mixed $defaultValue Default value it should returns.
     * @return Cache content string, if exists
     */
    public function get(string $key, $defaultValue = null)
    {
        if (!file_exists($file = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache')) {
            return $defaultValue;
        }

        $data = file_get_contents($file);
        if ($data === false) {
            throw new Exception('Fail on getting cache data');
        }

        $obj = unserialize($data);
        if ($obj['deadline'] >= time()) {
            return $obj['content'];
        }

        $this->delete($key);
        return $defaultValue;
    }

    /**
     * Save a content to cache
     *
     * @param string $key Cache reference
     * @param mixed $content Content to save
     * @param integer $ttl Defines cache life in seconds (default: 60 seconds)
     * @return void
     */
    public function save(string $key, $content, int $ttl = 60)
    {
        $data = [
            'deadline' => time() + $ttl,
            'content'  => $content,
        ];
        if (!is_dir(WRITEPATH . 'cache') && !mkdir(WRITEPATH . 'cache', 0664)) {
            throw new Exception('Unable to create cache folder');
        }

        file_put_contents(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache', serialize($data));
    }

    /**
     * Delete a specific cache content
     *
     * @param string $key Cache reference
     * @return void
     */
    public function remove(string $key)
    {
        if (file_exists($file = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache')) {
            @unlink($file);
        }
    }

    /**
     * Clear all cache content
     *
     * @return void
     */
    public function clear()
    {
        @array_map('unlink', glob(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . '*.cache') ?? []);
    }

}
