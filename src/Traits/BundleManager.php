<?php

namespace Interart\Flywork\Traits;

/**
 * Functions for bundle files.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
trait BundleManager
{
    private $bundleCss = [];
    private $bundleJs = [];

    private function check_type(string $type)
    {
        if (!in_array($type, ['js', 'css'])) {
            throw new InvalidArgumentException('Type mismatch [js, css]');
        }
    }

    private function get_bundle_folder()
    {
        if (!is_dir($bundle_path = WEBPATH . 'bundles' . DIRECTORY_SEPARATOR)) {
            throw new \Exception('Bundle folder doesn\'t exist in public web path.');
        }

        return $bundle_path;
    }

    private function check_files(array $files)
    {
        if (empty($files)) {
            throw new \InvalidArgumentException('File list cannot be empty');
        }
    }

    /**
     * Add file(s) by type to current request.
     *
     * @param string $type Bundle type ('js' or 'css')
     * @param string $file File (or array of files) to be bundled
     *
     * @return void
     */
    protected function addBundle(string $type, array $files)
    {
        $type = strtolower($type);
        $this->check_type($type);

        $this->check_files($files);

        $bundle_name = 'bundle' . ucfirst($type);
        $this->$bundle_name = array_merge($this->$bundle_name, $files);
    }

    /**
     * Generate bundle file for specified type.
     *
     * @param string $type Bundle type ('js' or 'css')
     * @param array $files Files to be bundled with default files
     *
     * @return string Relative path to the bundle
     */
    public function bundle(string $type, array $files = [])
    {
        $type = strtolower($type);
        $this->check_type($type);

        $bundle_path = $this->get_bundle_folder();
        $file_path = WEBPATH . $type . DIRECTORY_SEPARATOR;

        $bundle_name = 'bundle' . ucfirst($type);
        $this->$bundle_name = array_merge($this->$bundle_name, $files);
        $this->check_files($this->$bundle_name);

        $bundle_class = 'MatthiasMullie\\Minify\\' . strtoupper($type);
        $minifier = new $bundle_class();

        $path = trim(filter_input(INPUT_SERVER, 'PATH_INFO'), '/');
        $parts = explode('/', $path);
        $idx = 0;
        if (count($parts) > 0) {
            $path = $parts[$idx];
            $idx++;
            if ($parts[0] == 'rest') {
                $path.= '/' . $parts[$idx];
                $idx++;
            }
        }
        if (count($parts) > ($idx - 1)) {
            $path .= '/' . $parts[$idx];
        }
        $prefix = str_replace('/', '-', $path);
        $key = strtolower($prefix) . '_' . md5(serialize($files)) . '.' . $type;

        if (ENV == 'dev' || !file_exists($bundle_path . $key)) {
            foreach ($this->$bundle_name as $file) {
                $minifier->add($file_path . str_replace('/', DIRECTORY_SEPARATOR, $file) . '.' . $type);
            }

            $minifier->minify($bundle_path . $key);
        }

        return '/bundles/' . $key;
    }

    /**
     * Wipes clean the entire bundle folder by type.
     *
     * @param string $type
     *
     * @return void
     */
    public function clearBundles(string $type)
    {
        $type = strtolower($type);
        $this->check_type($type);

        @array_map('unlink', glob(WEBPATH . 'bundles' . DIRECTORY_SEPARATOR . '*.' . $type) ?? []);
    }

    /**
     * Wipes clean bundle list by type.
     *
     * @param string $type
     *
     * @return void
     */
    public function resetBundles(string $type)
    {
        $bundle_name = 'bundle' . ucfirst(strtolower($type));
        $this->$bundle_name = [];
    }

    /**
     * Wipes clean bundle list of all types.
     *
     * @return void
     */
    public function resetAllBundles()
    {
        $this->resetBundles('css');
        $this->resetBundles('js');
    }
}
