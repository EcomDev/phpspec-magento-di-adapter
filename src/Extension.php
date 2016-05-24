<?php

namespace EcomDev\PHPSpec\MagentoDiAdapter;

use EcomDev\PHPSpec\MagentoDiAdapter\Generator\SimplifiedDefinedClasses;
use EcomDev\PHPSpec\MagentoDiAdapter\Runner\CollaboratorMaintainer;
use Magento\Framework\Code\Generator\Io;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\ObjectManager\Code\Generator;
use Magento\Framework\ObjectManager\Profiler\Code\Generator as ProfilerGenerator;
use Magento\Framework\Api\Code\Generator\Mapper as MapperGenerator;
use Magento\Framework\Api\Code\Generator\SearchResults;

use org\bovigo\vfs\vfsStream;
use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;

/**
 * Magento 2.0 DI adapter PHPSpec extension
 *
 * We are suppressing mess detector rules,
 * as it is the only way to configure PHPSpec container
 *
 * @SuppressWarnings(CouplingBetweenObjects)
 * @SuppressWarnings(StaticAccess)
 */
class Extension implements ExtensionInterface
{

    /**
     * Load collaborator into PHPSpec ServiceContainer
     *
     * @param ServiceContainer $container
     */
    public function load(ServiceContainer $container)
    {
        $container->set(
            'ecomdev.phpspec.magento_di_adapter.vfs',
            $this->vfsFactory()
        );

        $container->set(
            'ecomdev.phpspec.magento_di_adapter.code_generator.io',
            $this->ioFactory()
        );

        $container->set(
            'ecomdev.phpspec.magento_di_adapter.code_generator.defined_classes',
            $this->simplifiedDefinedClassesFactory()
        );

        $container->set(
            'ecomdev.phpspec.magento_di_adapter.parameter_validator',
            $this->parameterValidatorFactory()
        );

        $container->set(
            'runner.maintainers.ecomdev_magento_collaborator',
            $this->collaboratorMaintainerFactory()
        );
    }

    /**
     * Factory for instantiation of parameter validator
     *
     * @return \Closure
     */
    public function parameterValidatorFactory()
    {
        return function (ServiceContainer $container) {
            $parameterValidator = new ParameterValidator(
                $container->get('ecomdev.phpspec.magento_di_adapter.code_generator.io'),
                $container->get('ecomdev.phpspec.magento_di_adapter.code_generator.defined_classes')
            );

            $parameterValidator
                ->addGenerator(Generator\Factory::class, Generator\Factory::ENTITY_TYPE)
                ->addGenerator(Generator\Repository::class, Generator\Repository::ENTITY_TYPE)
                ->addGenerator(Generator\Converter::class, Generator\Converter::ENTITY_TYPE)
                ->addGenerator(Generator\Persistor::class, Generator\Persistor::ENTITY_TYPE)
                ->addGenerator(MapperGenerator::class, MapperGenerator::ENTITY_TYPE)
                ->addGenerator(SearchResults::class, SearchResults::ENTITY_TYPE)
            ;

            return $parameterValidator;
        };
    }

    /**
     * Factory for instantiating Magento code generator IO adapter
     *
     * @return \Closure
     */
    public function ioFactory()
    {
        return function (ServiceContainer $container) {
            return new Io(new File(), $container->get('ecomdev.phpspec.magento_di_adapter.vfs')->url());
        };
    }

    /**
     * Factory for instantiating vfs
     *
     * @return \Closure
     */
    public function vfsFactory()
    {
        return function () {
            return vfsStream::setup(uniqid('ecomdev_phpspec_magento_di'));
        };
    }

    /**
     * Defined classes factory
     *
     * @return \Closure
     */
    public function simplifiedDefinedClassesFactory()
    {
        return function () {
            return new SimplifiedDefinedClasses();
        };
    }

    /**
     * Collaborator maintainer factory
     *
     * @return \Closure
     */
    public function collaboratorMaintainerFactory()
    {
        return function (ServiceContainer $container) {
            return new CollaboratorMaintainer(
                $container->get('ecomdev.phpspec.magento_di_adapter.parameter_validator')
            );
        };
    }
}
