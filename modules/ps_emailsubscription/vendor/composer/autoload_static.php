<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit812fd6482e6c82269eb7ec5974f594aa
{
    public static $classMap = array (
        'Ps_Emailsubscription' => __DIR__ . '/../..' . '/ps_emailsubscription.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit812fd6482e6c82269eb7ec5974f594aa::$classMap;

        }, null, ClassLoader::class);
    }
}
