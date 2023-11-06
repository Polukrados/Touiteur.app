<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6fd47091725e2631df453631a01ada0b
{
    public static $prefixLengthsPsr4 = array (
        'i' => 
        array (
            'iutnc\\deefy\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'iutnc\\deefy\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/classes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6fd47091725e2631df453631a01ada0b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6fd47091725e2631df453631a01ada0b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6fd47091725e2631df453631a01ada0b::$classMap;

        }, null, ClassLoader::class);
    }
}
