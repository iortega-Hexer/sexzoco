<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdfaef7d25fd006d0dbf8a3639909dbea
{
    public static $files = array (
        '8cd2fca4db21bffce1ad0612f7caeec4' => __DIR__ . '/..' . '/ramsey/array_column/src/array_column.php',
    );

    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'ReduxWeb\\AdvancedEmailGuard\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ReduxWeb\\AdvancedEmailGuard\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $prefixesPsr0 = array (
        'J' => 
        array (
            'JasonGrimes' => 
            array (
                0 => __DIR__ . '/..' . '/jasongrimes/paginator/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdfaef7d25fd006d0dbf8a3639909dbea::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdfaef7d25fd006d0dbf8a3639909dbea::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitdfaef7d25fd006d0dbf8a3639909dbea::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitdfaef7d25fd006d0dbf8a3639909dbea::$classMap;

        }, null, ClassLoader::class);
    }
}
