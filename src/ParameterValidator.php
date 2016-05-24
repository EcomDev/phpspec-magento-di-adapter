<?php

namespace EcomDev\PHPSpec\MagentoDiAdapter;

use Magento\Framework\Code\Generator\DefinedClasses;
use Magento\Framework\Code\Generator\Io;

/**
 * Validates parameters for Magento DI container
 */
class ParameterValidator
{
    /**
     * IO object used by generation io
     *
     * @var Io
     */
    private $generationIo;

    /**
     * Defined class check
     *
     * @var DefinedClasses
     */
    private $definedClasses;

    /**
     * List of generator wrappers
     *
     * @var GeneratorWrapper[]
     */
    private $generators = [];

    /**
     * Configures dependencies of parameter validator
     *
     * @param Io $generationIo
     * @param DefinedClasses $definedClasses
     */
    public function __construct(Io $generationIo, DefinedClasses $definedClasses)
    {
        $this->generationIo = $generationIo;
        $this->definedClasses = $definedClasses;
    }

    /***
     * Adds a new generator
     *
     * @param string $generatorClass
     * @param string $suffix
     * @param \Closure|null $factory
     *
     * @return $this
     */
    public function addGenerator($generatorClass, $suffix, \Closure $factory = null)
    {
        $this->generators[] = new GeneratorWrapper($this->createGeneratorFactory($generatorClass, $factory), $suffix);
        return $this;
    }

    /**
     * Generates a generator from class name rules
     *
     * @param string $className
     *
     * @return bool|\Magento\Framework\Code\Generator\EntityAbstract
     */
    public function generator($className)
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($className)) {
                return $generator->createGenerator($className);
            }
        }

        return false;
    }

    /**
     * Returns generator factory
     *
     * @param string $generatorClass
     * @param \Closure|null $factory
     *
     * @return \Closure
     */
    private function createGeneratorFactory($generatorClass, \Closure $factory = null)
    {
        return function ($sourceClass, $prefixClass) use ($factory, $generatorClass) {
            if ($factory) {
                return $factory($sourceClass, $prefixClass, $this->generationIo, $this->definedClasses, $generatorClass);
            }

            return new $generatorClass($sourceClass, $prefixClass, $this->generationIo, null, $this->definedClasses);
        };
    }

    /**
     * Validates method signature and tries to generate missing classes
     *
     * @param \ReflectionFunctionAbstract $reflectionFunction
     *
     * @return $this
     */
    public function validate(\ReflectionFunctionAbstract $reflectionFunction)
    {
        foreach ($reflectionFunction->getParameters() as $parameter) {
            $this->validateParameter($parameter);
        }

        return $this;
    }

    /**
     * Reflection parameter
     *
     * @param \ReflectionParameter $parameter
     *
     * @return $this
     */
    private function validateParameter(\ReflectionParameter $parameter)
    {
        $catcher = function ($className) {
            $generator = $this->generator($className);
            if ($generator) {
                include $generator->generate();
            }
        };

        spl_autoload_register($catcher);
        try {
            $parameter->getClass();
        } catch (\ReflectionException $e) {
            // Ignore reflection exception, as it is an intended behaviour for our catcher
        }
        spl_autoload_unregister($catcher);
        return $this;
    }
}
