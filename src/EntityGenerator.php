<?php

namespace EcomDev\PHPSpec\MagentoDiAdapter;

class EntityGenerator implements EntityGeneratorInterface
{
    /**
     * Abstract entity code generator
     *
     * @var \Closure
     */
    private $generatorFactory;

    /**
     * Class suffix to be matched as supported class name
     *
     * @var string
     */
    private $classSuffix;

    /**
     * Constructs a wrapper around generator instantiation
     *
     * @param \Closure $generatorFactory
     * @param string $classSuffix
     */
    public function __construct(\Closure $generatorFactory, $classSuffix)
    {
        $this->generatorFactory = $generatorFactory;
        $this->classSuffix = $classSuffix;
    }

    /**
     * Returns true if class has been generated
     *
     * @param string $className
     *
     * @return bool
     */
    public function supports($className)
    {
        return (strpos($className, ucfirst($this->classSuffix)) === strlen($className) - strlen($this->classSuffix));
    }

    /**
     * Returns generated class file path, if class was generated
     *
     * @param string $className
     *
     * @return bool|string
     */
    public function generate($className)
    {
        $factory = $this->generatorFactory;
        $sourceClass = rtrim(substr($className, 0, strpos($className, ucfirst($this->classSuffix))), '\\');
        $entityGenerator = $factory($sourceClass, $className);
        return $entityGenerator->generate();
    }
}
