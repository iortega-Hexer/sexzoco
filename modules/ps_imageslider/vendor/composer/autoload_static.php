<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0c352ca796e51ac94bd43796663dafaa
{
    public static $classMap = array (
        'Ps_ImageSlider' => __DIR__ . '/../..' . '/ps_imageslider.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit0c352ca796e51ac94bd43796663dafaa::$classMap;

        }, null, ClassLoader::class);
    }
}