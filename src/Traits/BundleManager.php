<?php

namespace Interart\Flywork\Traits;

/**
 * Functions for bundle files
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
trait BundleManager
{
    private $bundleCss = [];
    private $bundleJs = [];

    /**
     * Add file(s) by type to current request.
     *
     * @param string $type Bundle type ('js' or 'css')
     * @param string $file File (or array of files) to be bundled
     * @return void
     */
    protected function add_bundle(string $type, array $files)
    {
        if (!in_array($type, ['js', 'css'])) {
            throw new InvalidArgumentException("Type mismatch [js, css]");
        }

        $bundle_name = 'bundle' . ucfirst($type);
        if (is_array($files)) {
            $this->$bundle_name = array_merge($this->$bundle_name, $files);
        } else {
            $this->$bundle_name = $files;
        }
    }

    /**
     * Generate bundle file for specified type
     *
     * @param string $type Bundle type ('js' or 'css')
     * @param array $files Files to be bundled with default files
     * @return string Relative path to the bundle
     */
    public function bundle(string $type, array $files = [])
    {
        if (!in_array($type, ['js', 'css'])) {
            throw new InvalidArgumentException("Type mismatch [js, css]");
        }

        $bundle_name = 'bundle' . ucfirst($type);
        $bundle_class = 'MatthiasMullie\\Minify\\' . strtoupper($type);

        $files = array_merge($this->$bundle_name, $files);
        $path = WEBPATH . $type . DIRECTORY_SEPARATOR;
        $minifier = new $bundle_class();
        $prefix = str_replace('/', '-', trim(filter_input(INPUT_SERVER, 'PATH_INFO'), '/'));
        $key = strtolower($prefix) . '_' . md5(serialize($files)) . '.' . $type;

        if (ENV == 'dev' || !file_exists(WEB_PATH . 'bundles' . DIRECTORY_SEPARATOR . $key)) {

            if (!is_dir($bundle_path = WEBPATH . 'bundles')) {
                mkdir($bundle_path, 0744, true);
                chmod($bundle_path, 0744);
                chgrp($bundle_path, 'www-data');
                touch($bundle_path . 'index.html');
            }

            foreach ($files as $file) {
                $minifier->add($path . str_replace('/', DIRECTORY_SEPARATOR, $file) . '.' . $type);
            }
            
            $minifier->minify(WEBPATH . 'bundles' . DIRECTORY_SEPARATOR . $key);
        }

        return '/bundles/' . $key;
    }
}
