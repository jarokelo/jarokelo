<?php

namespace app\assets;

use Yii;
use yii\helpers\Url;
use yii\helpers\FileHelper;

class BundleManager extends \yii\base\Component
{
    /**
     * @var string $configPath to main config file
     */
    public $configPath;

    /**
     * @var array names of bundles to always check when deploying
     */
    public $deployBundles = [];

    /**
     * @var array names of default bundles, will be merged into $deployBundles
     */
    public $defaultBundles = [
        'yii\validators\ValidationAsset',
        'yii\validators\PunycodeAsset',
        'yii\widgets\MaskedInputAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\widgets\PjaxAsset',
        'yii\captcha\CaptchaAsset',
        'yii\grid\GridViewAsset',
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapThemeAsset',
    ];

    /**
     * Loads the config file
     * @return loaded config
     */
    public function loadConfigFile()
    {
        if (!isset($this->configPath)) {
            $configPath = Yii::getAlias('@app/config') . DIRECTORY_SEPARATOR . 'web.php';
        } else {
            $configPath = $this->configPath;
        }
        return require($configPath);
    }

    protected function collectModuleConfigs($configs, &$modules, $prefix = '')
    {
        foreach ($configs as $name => $config) {
            $modules[] = [
                'name' => $prefix . $name,
                'config' => $config,
            ];
            if (is_array($config) && !empty($config['modules'])) {
                $this->collectModuleConfigs($config['modules'], $modules, $prefix . $name . '/');
            }
        }
    }

    /**
     * Return paths to assets.
     * @return array
     */
    public function getPaths()
    {
        $mainConfig = $this->loadConfigFile();

        $paths = [[
            'path' => Yii::getAlias('@app'),
            'module' => '_app',
        ]];


        if (empty($mainConfig['modules'])) {
            return $paths;
        }
        $moduleConfig = $mainConfig['modules'];

        $modules = [];
        $this->collectModuleConfigs($moduleConfig, $modules);

        foreach ($modules as $module) {
            $className = null;
            $path = false;
            $name = $module['name'];
            $config = $module['config'];
            if (is_array($config)) {
                if (!empty($config['basePath'])) {
                    $path = realpath(Yii::getAlias($config['basePath']));
                } elseif (!empty($config['class'])) {
                    $className = $config['class'];
                }
            } else {
                $className = $config;
            }

            if ($className !== null) {
                try {
                    $class = new \ReflectionClass($className);
                } catch (\ReflectionException $e) {
                    continue;
                }
                $path = dirname($class->getFileName());
            }

            if ($path === false) {
                continue;
            }

            $paths[] = [
                'path' => $path,
                'module' => $name,
            ];
        }

        return $paths;
    }

    /**
     * Instantiates bundle by name and its dependencies.
     * Inserts the bundle into $bundles.
     *
     * @param string $bundleName
     * @param array $bundles
     */
    protected function collectBundle($bundleName, &$bundles)
    {
        if (isset($bundles[$bundleName])) {
            return;
        }
        try {
            $bundle = Yii::createObject($bundleName);
        } catch (\Exception $x) {
            return;
        }

        $bundles[$bundleName] = $bundle;

        foreach ($bundle->depends as $dependsName) {
            $this->collectBundle($dependsName, $bundles);
        }
    }

    /**
     * Deploy checks if any of the files are newer than their directory,
     * and touches the directory to force Yii to publish it again.
     * @return string[] touched directories.
     */
    public function deploy()
    {
        $paths = $this->getPaths();

        $ret = [];

        $bundles = [];

        foreach ($paths as $pathConfig) {
            $module = $pathConfig['module'];
            $path = $pathConfig['path'];
            $bundlesFile = $path . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'bundles.php';
            if (!file_exists($bundlesFile)) {
                continue;
            }
            $bundleNames = include($bundlesFile);
            foreach ($bundleNames as $bundleName) {
                $this->collectBundle($bundleName, $bundles);
            }
        }

        foreach (array_merge($this->deployBundles, $this->defaultBundles) as $bundleName) {
            $this->collectBundle($bundleName, $bundles);
        }

        foreach ($bundles as $bundle) {
            if ($bundle instanceof AssetBundle && $bundle->distPath !== null) {
                $directory = Yii::getAlias($bundle->distPath);
                $jsFiles = $bundle->getDistJs();
                $cssFiles = $bundle->getDistCss();
            } elseif ($bundle->sourcePath !== null) {
                $directory = Yii::getAlias($bundle->sourcePath);
                $jsFiles = $bundle->js;
                $cssFiles = $bundle->css;
            } else {
                continue;
            }

            $files = [];

            foreach (array_merge($jsFiles, $cssFiles) as $file) {
                if (Url::isRelative($file)) {
                    $files[] = $directory . DIRECTORY_SEPARATOR . $file;
                }
            }

            if ($bundle instanceof AssetBundle) {
                if ($bundle->imgPath !== null) {
                    $dir = $directory . DIRECTORY_SEPARATOR . $bundle->imgPath;
                    if (is_dir($dir)) {
                        $files = array_merge($files, FileHelper::findFiles($dir));
                    }
                }
                if ($bundle->fontPath !== null) {
                    $dir = $directory . DIRECTORY_SEPARATOR . $bundle->fontPath;
                    if (is_dir($dir)) {
                        $files = array_merge($files, FileHelper::findFiles($dir));
                    }
                }
            }

            $dirtime = @filemtime($directory);

            if ($dirtime === false) {
                continue;
            }

            foreach ($files as $file) {
                $time = @filemtime($file);
                if ($time === false) {
                    continue;
                }
                if ($time > $dirtime) {
                    $ret[] = $directory;
                    touch($directory);
                    clearstatcache();
                    continue 2;
                }
            }
        }

        return $ret;
    }

    /**
     * Returns bundle info that will be passed to gulp/grunt.
     * @return array bundle info
     */
    public function getBundleInfo()
    {
        $paths = $this->getPaths();

        $ret = [
            'packages' => [],
        ];

        foreach ($paths as $pathConfig) {
            $module = $pathConfig['module'];
            $path = $pathConfig['path'];
            $bundlesFile = $path . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'bundles.php';
            if (!file_exists($bundlesFile)) {
                continue;
            }
            $bundles = include($bundlesFile);
            foreach ($bundles as $bundleName) {
                $bundle = Yii::createObject($bundleName);

                if (!$bundle instanceof AssetBundle) {
                    continue;
                }

                if ($bundle->devPath === null || $bundle->distPath === null) {
                    continue;
                }
                $config = [
                    'sources' => Yii::getAlias($bundle->devPath),
                    'dist' => Yii::getAlias($bundle->distPath),
                    'module' => $module,
                ];
                $cssSourcePaths = [];
                if ($bundle->scssPath !== null) {
                    $config['scssPath'] = $bundle->scssPath;
                    $cssSourcePaths = [$bundle->scssPath];
                }
                if (is_array($bundle->cssSourcePaths)) {
                    $cssSourcePaths = array_unique(array_merge($cssSourcePaths, $bundle->cssSourcePaths));
                }
                if (count($cssSourcePaths)) {
                    $config['cssfiles'][] = [
                        'sources' => $cssSourcePaths,
                        'dev' => $config['sources'] . DIRECTORY_SEPARATOR . 'css', /** @todo hardcoded */
                        'dist' => $config['dist'] . DIRECTORY_SEPARATOR . 'css',
                    ];
                }
                if (!empty($bundle->devJs)) {
                    foreach ($bundle->devJs as $name => $scripts) {
                        if (!is_array($scripts)) {
                            continue;
                        }
                        $fullpaths = [];
                        foreach ($scripts as $script) {
                            $fullpaths[] = $config['sources'] . DIRECTORY_SEPARATOR . $script;
                        }
                        $destPath = $config['dist'] . DIRECTORY_SEPARATOR . $name;
                        $config['jsfiles'][] = [
                            'sources' => $fullpaths,
                            'dist' => $destPath,
                        ];
                    }
                }
                if ($bundle->imgPath !== null) {
                    $config['imgPath'] = $bundle->imgPath;
                }
                if ($bundle->fontPath !== null) {
                    $config['fontPath'] = $bundle->fontPath;
                }
                if ($bundle->extraParams !== null) {
                    $config['extraParams'] = $bundle->extraParams;
                }
                if (is_array($bundle->otherPaths)) {
                    $config['otherpaths'] = $bundle->otherPaths;
                }
                $config['package'] = $bundle::className();
                $ret['packages'][] = $config;
            }
        }
        return $ret;
    }
}
