<?php

namespace EcomDev\PHPSpec\MagentoDiAdapter\Generator;

use Magento\Framework\Code\Generator\DefinedClasses;

class SimplifiedDefinedClasses extends DefinedClasses
{
    /**
     * Checks if class is loadable from disk
     *
     * @param string $className
     *
     * @return bool
     */
    public function isClassLoadableFromDisc($className)
    {
        return class_exists($className) || interface_exists($className);
    }
}
