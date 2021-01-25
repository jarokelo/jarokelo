<?php

namespace tests\unit\commands;

use Yii;
use app\assets\PackagesController;
use app\assets\BundleManager;
use yii\codeception\TestCase;
use yii\helpers\Json;

class PackagesControllerTest extends TestCase
{
    protected $tempPath = '';

    protected function setUp()
    {
        parent::setUp();
        $this->tempPath = Yii::getAlias('@runtime/test_packages');
        $this->createDir($this->tempPath);
    }

    protected function tearDown()
    {
        $this->removeDir($this->tempPath);
        parent::tearDown();
    }

    /**
     * Creates directory.
     * @param $dirName directory full name
     */
    protected function createDir($dirName)
    {
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
    }

    /**
     * Removes directory.
     * @param $dirName directory full name
     */
    protected function removeDir($dirName)
    {
        if (!empty($dirName) && is_dir($dirName)) {
            \yii\helpers\FileHelper::removeDirectory($dirName);
        }
    }

    /**
     * @return \app\assets\PackagesController packages controller instance
     */
    protected function createPackagesController()
    {
        $module = $this->getMockBuilder('yii\\base\\Module')
            ->setMethods(['fake'])
            ->setConstructorArgs(['console'])
            ->getMock();
        $packagesController = new PackagesController('packages', $module);
        $packagesController->interactive = false;

        return $packagesController;
    }

    /**
     * Return a mock PackagesController with a custom config
     * @param array $config custom config
     * @return \app\assets\PackagesController packages controller instance
     */
    protected function createMockPackagesController(array $config)
    {
        $module = $this->getMockBuilder('yii\\base\\Module')
            ->setMethods(['fake'])
            ->setConstructorArgs(['console'])
            ->getMock();
        $bundleManagerMock = $this->getMockBuilder('app\\assets\\BundleManager')
            ->setMethods(['loadConfigFile'])
            ->getMock();
        $packagesController = new PackagesController('packages', $module, ['bundleManager' => $bundleManagerMock]);
        $packagesController->interactive = false;
        $bundleManagerMock->expects($this->any())
            ->method('loadConfigFile')
            ->will($this->returnValue($config));

        return $packagesController;
    }

    /**
     * Emulates running of the packages controller.
     * @param  string $actionId id of action to be run.
     * @param null|array $config configuration to use in mock config loader
     * @param array $args controller shell arguments
     * @return string controller output
     */
    protected function runPackagesControllerAction($actionId, $config = null, array $args = [])
    {
        if ($config === null) {
            $controller = $this->createPackagesController();
        } else {
            $controller = $this->createMockPackagesController($config);
        }
        ob_start();
        ob_implicit_flush(false);
        $controller->run($actionId, $args);
        return ob_get_clean();
    }

    /**
     * Data provider for testLoadConfig
     */
    public function loadConfigProvider()
    {
        return [
            [null, null],
            [['name' => 'testapp'], 'testapp']
        ];
    }

    /**
     * @param array configuration
     * @return string|null full path to config file
     */
    public function createConfigFile($config)
    {
        if ($config === null) {
            return null;
        }
        $configFile = $this->tempPath . '/main' . md5(microtime()) . '.php';
        $fileContent = '<?php return ' . var_export($config, true) . ';';
        file_put_contents($configFile, $fileContent);
        return $configFile;
    }

    /**
     * Data provider for createFiles
     */
    public function filesProvider()
    {
        $globalPackages = [
            'nocompile' => [
                'name' => 'NocompileAsset',
                'namespace' => 'app\assets',
                'type' => '\app\assets\FallbackAssetBundle',
                'sourcePath' => '@app/assets/nocompile/src',
                'devJs' => ['js/test.js'],
                'js' => ['js/test.min.js'],
            ],
            'compile' => [
                'name' => 'CompileAsset',
                'namespace' => 'app\assets',
                'type' => '\app\assets\AssetBundle',
                'devPath' => "@app/assets/compile/src",
                'distPath' => "@app/assets/compile/dist",
                'devJs' => ['js/combined1.js' => ['js/file1.js', 'js/file2.js'], 'js/combined2.js' => ['js/file3.js', 'js/file4.js'], 'js/nocombine.js'],
                'js' => ['js/combined1.js', 'js/combined2.js', 'js/nocombine.js'],
                'scssPath' => 'scss',
                'imgPath' => 'img',
                'fontPath' => 'fonts',
                'otherPaths' => ['others1', 'others2', 'others3/foo/bar'],
                'css' => ['css/screen.css','css/print.css','css/main.css','css/form.css'],
            ],
        ];

        $modulePackages = [
            'test' => [
                'test' => [
                    'name' => 'TestAsset',
                    'namespace' => 'app\modules\test\assets',
                    'type' => '\app\assets\AssetBundle',
                    'devPath' => "@app/modules/test/assets/test/src",
                    'distPath' => "@app/modules/test/assets/test/dist",
                    'devJs' => ['js/combined1.js' => ['js/file1.js', 'js/file2.js'], 'js/nocombine.js'],
                    'js' => ['js/combined1.js', 'js/nocombine.js'],
                    'cssSourcePaths' => ['scss', 'less'],
                    'imgPath' => 'img',
                    'fontPath' => 'fonts',
                    'css' => ['css/screen.css','css/print.css','css/main.css','css/form.css'],
                ],
            ],
            'nametest' => [
                'test' => [
                    'name' => 'TestAsset',
                    'namespace' => 'app\modules\nametest\assets',
                    'type' => '\app\assets\AssetBundle',
                    'devPath' => "@app/modules/nametest/assets/test/src",
                    'distPath' => "@app/modules/nametest/assets/test/dist",
                    'devJs' => ['js/combined1.js' => ['js/file1.js', 'js/file2.js'], 'js/nocombine.js'],
                    'js' => ['js/combined1.js', 'js/nocombine.js'],
                    'scssPath' => 'scss',
                    'imgPath' => 'img',
                    'fontPath' => 'fonts',
                    'css' => ['css/screen.css','css/print.css','css/main.css','css/form.css'],
                    'extraParams' => [
                        'extra1' => 'Extra 1',
                        'extra2' => true,
                        'extra3' => 47,
                    ]
                ],
            ],
        ];

        return [
            [$globalPackages, $modulePackages],
        ];
    }

    /**
     * creates all the required files for the test
     * @return array config
     */
    public function createFiles($globalPackages, $modulePackages)
    {
        $packageAttributes = [
            'devJs',
            'js',
            'sourcePath',
            'devPath',
            'distPath',
            'scssPath',
            'cssSourcePaths',
            'imgPath',
            'fontPath',
            'otherPaths',
            'css',
            'extraParams'
        ];

        $mainAssets = $this->tempPath . '/assets';
        $modulesDir = $this->tempPath . '/modules';

        $this->createDir($mainAssets);
        $mainFile = $mainAssets . '/bundles.php';
        $mainBundles = [];

        foreach ($globalPackages as $package) {
            $class =
"<?php
namespace " . $package['namespace'] . ";
class " . $package['name'] . " extends " . $package['type'] . "
{
";
            foreach ($packageAttributes as $attr) {
                if (array_key_exists($attr, $package)) {
                    $class .= 'public $' . $attr . ' = ' . var_export($package[$attr], true) . ';';
                    $class .= "\n";
                }
            }
            $class .= "}";
            $fileName = $mainAssets . '/' . $package['name'] . '.php';
            file_put_contents($fileName, $class);
            $mainBundles[] = $package['namespace'] . '\\' . $package['name'];
        }

        $fileContent = '<?php return ' . var_export($mainBundles, true) . ';';
        file_put_contents($mainFile, $fileContent);

        $modules = [];

        foreach ($modulePackages as $module => $packages) {
            $moduleAssets = $modulesDir . '/' . $module . '/assets';
            $this->createDir($moduleAssets);

            $moduleBundles = [];

            foreach ($packages as $package) {
                $class =
"<?php
namespace " . $package['namespace'] . ";
class " . $package['name'] . " extends " . $package['type'] . "
{
";
                foreach ($packageAttributes as $attr) {
                    if (array_key_exists($attr, $package)) {
                        $class .= 'public $' . $attr . ' = ' . var_export($package[$attr], true) . ';';
                        $class .= "\n";
                    }
                }
                $class .= "}";
                $fileName = $moduleAssets . '/' . $package['name'] . '.php';
                file_put_contents($fileName, $class);
                $moduleBundles[] = $package['namespace'] . '\\' . $package['name'];
            }

            $fileContent = '<?php return ' . var_export($moduleBundles, true) . ';';
            file_put_contents($moduleAssets . '/bundles.php', $fileContent);
            $modules[$module] = ['basePath' => '@app/modules/' . $module ];
        }

        $this->createDir($modulesDir . '/parent/assets');
        $this->createDir($modulesDir . '/child/assets');

        $modules['parent'] = [
            'basePath' => '@app/modules/parent',
            'modules' => [
                'child' => [
                    'basePath' => '@app/modules/child',
                ]
            ],
        ];

        // use class to find path
        $this->createDir($modulesDir . '/classtest/assets');
        file_put_contents($modulesDir . '/classtest/ClassTestModule.php', '<?php namespace app\modules\classtest; class ClassTestModule extends \yii\base\Module {}');
        $modules['class_test'] = [
            'class' => 'app\modules\classtest\ClassTestModule'
        ];

        return ['modules' => $modules];
    }

    /**
     * Tests loading of a config file
     * @dataProvider loadConfigProvider
     */
    public function testLoadConfig($configFile, $expectedName)
    {
        if ($expectedName === null) {
            $expectedName = Yii::$app->name;
        }
        $controller = $this->createPackagesController();
        $controller->bundleManager->configPath = $this->createConfigFile($configFile);
        $config = $controller->bundleManager->loadConfigFile();
        $this->assertArrayHasKey('name', $config);
        $this->assertEquals($expectedName, $config['name']);
    }

    /**
     * Tests mock loadConfigFile
     */
    public function testLoadMockConfig()
    {
        $mockConfig = ['name' => 'test mock app'];
        $controller = $this->createMockPackagesController($mockConfig);
        $config = $controller->bundleManager->loadConfigFile();
        $this->assertEquals($mockConfig, $config);
    }

    /**
     * Tests getPaths with no modules
     * @depends testLoadMockConfig
     */
    public function testGetPathsNoModules()
    {
        $config = [
            'modules' => [],
        ];
        $alias = Yii::getAlias('@app');
        Yii::setAlias('@app', $this->tempPath);

        $controller = $this->createMockPackagesController($config);
        $paths = $controller->bundleManager->getPaths();
        $expected = [
            '_app' => Yii::getAlias('@app'),
        ];

        foreach ($expected as $name => $path) {
            $this->assertContains([
                'path' => $path,
                'module' => $name,
            ], $paths, 'Expected module missing from paths');
        }
        $this->assertCount(count($expected), $paths);

        Yii::setAlias('@app', $alias);
    }

    /**
     * Tests getPaths with missing module
     * @depends testLoadMockConfig
     */
    public function testGetPathsMissingModule()
    {
        $config = [
            'modules' => [
                'doesnotexist' => [
                    'class' => 'app\modules\doesnotexist\DoesNotExistModule'
                ],
            ],
        ];
        $alias = Yii::getAlias('@app');
        Yii::setAlias('@app', $this->tempPath);

        $controller = $this->createMockPackagesController($config);
        $paths = $controller->bundleManager->getPaths();
        $expected = [
            '_app' => Yii::getAlias('@app'),
        ];

        foreach ($expected as $name => $path) {
            $this->assertContains([
                'path' => $path,
                'module' => $name,
            ], $paths, 'Expected module missing from paths');
        }
        $this->assertCount(count($expected), $paths);

        Yii::setAlias('@app', $alias);
    }

    /**
     * Tests getPaths with basePath
     * @depends testLoadMockConfig
     */
    public function testGetPathsBasePath()
    {
        $basePath = $this->tempPath . '/modules/basepath';
        $this->createDir($basePath);
        $config = [
            'modules' => [
                'basepath' => [
                    'basePath' => $basePath
                ],
            ],
        ];
        $alias = Yii::getAlias('@app');
        Yii::setAlias('@app', $this->tempPath);

        $controller = $this->createMockPackagesController($config);
        $paths = $controller->bundleManager->getPaths();
        $expected = [
            '_app' => Yii::getAlias('@app'),
            'basepath' => $basePath,
        ];

        foreach ($expected as $name => $path) {
            $this->assertContains([
                'path' => $path,
                'module' => $name,
            ], $paths, 'Expected module missing from paths');
        }
        $this->assertCount(count($expected), $paths);

        Yii::setAlias('@app', $alias);
    }

    /**
     * Tests getPaths
     * @depends testLoadMockConfig
     * @dataProvider filesProvider
     */
    public function testGetPaths($globalPackages, $modulePackages)
    {
        $config = $this->createFiles($globalPackages, $modulePackages);
        $alias = Yii::getAlias('@app');
        Yii::setAlias('@app', $this->tempPath);

        $controller = $this->createMockPackagesController($config);
        $paths = $controller->bundleManager->getPaths();
        $expected = [
            '_app' => Yii::getAlias('@app'),
            'test' => Yii::getAlias('@app/modules/test'),
            'nametest' => Yii::getAlias('@app/modules/nametest'),
            'parent' => Yii::getAlias('@app/modules/parent'),
            'parent/child' => Yii::getAlias('@app/modules/child'),
            'class_test' => Yii::getAlias('@app/modules/classtest'),
        ];

        foreach ($expected as $name => $path) {
            $this->assertContains([
                'path' => $path,
                'module' => $name,
            ], $paths, 'Expected module missing from paths');
        }

        Yii::setAlias('@app', $alias);
    }

    /**
     * Tests that the comand returns a json
     * @coversNothing
     */
    public function testReturnsJson()
    {
        $output = $this->runPackagesControllerAction('index');
        $decoded = Json::decode($output);
        $this->assertNotNull($decoded, 'Returned json is invalid');
    }

    /**
     * Tests main action
     * @depends testReturnsJson
     * @depends testLoadMockConfig
     * @dataProvider filesProvider
     */
    public function testActionIndex($globalPackages, $modulePackages)
    {
        $config = $this->createFiles($globalPackages, $modulePackages);
        $alias = Yii::getAlias('@app');
        Yii::setAlias('@app', $this->tempPath);

        $output = $this->runPackagesControllerAction('index', $config);

        $decoded = Json::decode($output);
        $this->assertNotNull($decoded, 'Returned json is invalid');
        $this->assertArrayHasKey('packages', $decoded, 'Returned json is missing packages');

        $toTest = [];
        foreach ($decoded['packages'] as $config) {
            $this->assertArrayHasKey('sources', $config);
            $this->assertArrayHasKey('dist', $config);
            $this->assertArrayHasKey('module', $config);
            $toTest[] = [
                'sources' => $config['sources'],
                'dist' => $config['dist'],
                'module' => $config['module'],
            ];
        }
        $expected = [
            '_app' => [
                'yes' => ['compile'],
                'no' => ['nocompile'],
                'path' => Yii::getAlias('@app'),
            ],
            'test' => [
                'yes' => ['test'],
                'path' => Yii::getAlias('@app/modules/test'),
            ],
            'nametest' => [
                'yes' => ['test'],
                'path' =>  Yii::getAlias('@app/modules/nametest'),
            ],
        ];

        foreach ($expected as $name => $config) {
            $path = $config['path'];
            $cssSources = [];
            foreach ($config['yes'] as $package) {
                $this->assertContains([
                    'sources' => $path . '/assets/' . $package . '/src',
                    'dist' => $path . '/assets/' . $package . '/dist',
                    'module' => $name,
                ], $toTest, 'Expected package missing from packages');
            }
        }

        $expected = [];

        $allPackages = array_values($globalPackages);
        foreach ($modulePackages as $oneModulePackages) {
            $allPackages = array_merge($allPackages, array_values($oneModulePackages));
        }

        foreach ($allPackages as $package) {
            if (!array_key_exists('devPath', $package)) {
                continue;
            }
            $found = false;

            $packageDistPath = Yii::getAlias($package['distPath']);
            $packageDevPath = Yii::getAlias($package['devPath']);

            foreach ($decoded['packages'] as $config) {

                $cssDev = $packageDevPath . '/css';
                $cssDist = $packageDistPath . '/css';

                $cssSources = false;

                if (array_key_exists('cssSourcePaths', $package)) {
                    $cssSources = $package['cssSourcePaths'];
                } elseif (array_key_exists('scssPath', $package)) {
                    $cssSources = [$package['scssPath']];
                }

                $actualCssSources = false;
                $actualCssDev = false;
                $actualCssDist = false;
                if (isset($config['cssfiles']) && count($config['cssfiles'])) {
                    $actualCssSources = $config['cssfiles'][0]['sources'];
                    $actualCssDev = $config['cssfiles'][0]['dev'];
                    $actualCssDist = $config['cssfiles'][0]['dist'];
                }

                if ($actualCssDev !== $cssDev || $actualCssDist !== $cssDist || $actualCssSources !== $cssSources) {
                    continue;
                }

                if (array_key_exists('devJs', $package)) {

                    if (!array_key_exists('jsfiles', $config)) {
                        continue;
                    }

                    $jsfiles = $config['jsfiles'];

                    $jssources = [];

                    foreach ($package['devJs'] as $combined => $files) {
                        if (is_numeric($combined)) {
                            continue;
                        }
                        foreach ($files as &$sourceFile) {
                            $sourceFile = rtrim($packageDevPath, '/') . '/' . ltrim($sourceFile, '/');
                        }
                        $jssources[] = [
                            'sources' => $files,
                            'dist' => rtrim($packageDistPath, '/') . '/' . ltrim($combined, '/'),
                        ];
                    }

                    if (count($jssources) !== count($jsfiles)) {
                        continue;
                    }

                    $allFound = true;
                    foreach ($jssources as $expectedJs) {
                        $found2 = false;
                        foreach ($jsfiles as $actualJs) {
                            if (!array_key_exists('dist', $actualJs) || !array_key_exists('sources', $actualJs)) {
                                continue;
                            }
                            if ($expectedJs['dist'] === $actualJs['dist'] && $expectedJs['sources'] === $actualJs['sources']) {
                                $found2 = true;
                                break;
                            }
                        }
                        if (!$found2) {
                            $allFound = false;
                        }
                    }
                    if (!$allFound) {
                        continue;
                    }
                }

                //'otherPaths' => ['others1', 'others2', 'others3/foo/bar'],

                $otherPaths = [];
                if (array_key_exists('otherPaths', $package)) {
                    $otherPaths = $package['otherPaths'];
                    $this->assertArrayHasKey('otherpaths', $config, 'Package is missing otherpaths');
                }
                // otherpaths is optional, but if present, it must match what is in the bundle file
                if (array_key_exists('otherpaths', $config)) {
                    foreach ($otherPaths as $path) {
                        $this->assertContains($path, $config['otherpaths'], "Package is missing other path $path");
                    }
                    $this->assertCount(count($otherPaths), $config['otherpaths'], 'Package has extra other paths');
                }

                if (array_key_exists('extraParams', $package)) {
                    $this->assertArrayHasKey('extraParams', $config, 'Package is missing extra params');
                    $this->assertEquals($package['extraParams'], $config['extraParams'], 'Extra params does not match');
                }

                $found = true;
                break;
            }

            $this->assertTrue($found, 'Package not found');
        }

        Yii::setAlias('@app', $alias);
    }
}
