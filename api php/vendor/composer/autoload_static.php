<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf54f5c44d68c811a6cc2e7426535498c
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf54f5c44d68c811a6cc2e7426535498c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf54f5c44d68c811a6cc2e7426535498c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf54f5c44d68c811a6cc2e7426535498c::$classMap;

        }, null, ClassLoader::class);
    }
}
