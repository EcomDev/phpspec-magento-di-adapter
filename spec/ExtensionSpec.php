<?php

namespace spec\EcomDev\PHPSpec\MagentoDiAdapter;

use EcomDev\PHPSpec\MagentoDiAdapter\Generator\SimplifiedDefinedClasses;
use EcomDev\PHPSpec\MagentoDiAdapter\ParameterValidator;
use EcomDev\PHPSpec\MagentoDiAdapter\Runner\CollaboratorMaintainer;
use Magento\Framework\Code\Generator\Io;
use Magento\Framework\ObjectManager\Code\Generator;
use Magento\Framework\ObjectManager\Profiler\Code\Generator as ProfilerGenerator;
use Magento\Framework\Api\Code\Generator\Mapper as MapperGenerator;
use Magento\Framework\Api\Code\Generator\SearchResults;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ObjectBehavior;
use PhpSpec\ServiceContainer;
use Prophecy\Argument;

class ExtensionSpec extends ObjectBehavior
{
    /**
     * @var ServiceContainer
     */
    private $serviceContainer;

    function let(ServiceContainer $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    function it_should_implement_extension_interface()
    {
        $this->shouldImplement(ExtensionInterface::class);
    }

    function it_creates_parameter_validator_factory_with_internal_io(Io $io)
    {
        $this->serviceContainer->get('ecomdev.phpspec.magento_di_adapter.code_generator.io')
            ->willReturn($io);
        $this->serviceContainer->get('ecomdev.phpspec.magento_di_adapter.code_generator.defined_classes')
            ->willReturn(new SimplifiedDefinedClasses());

        $validatorFactory = $this->parameterValidatorFactory();
        $validatorFactory->shouldImplement(\Closure::class);
        $parameterValidator = $validatorFactory($this->serviceContainer);
        $parameterValidator->shouldImplement(ParameterValidator::class);

        // Test pre configured generators
        $parameterValidator->generator('ItemFactory')
            ->shouldImplement(Generator\Factory::class);
        $parameterValidator->generator('ItemRepository')
            ->shouldImplement(Generator\Repository::class);
        $parameterValidator->generator('ItemConverter')
            ->shouldImplement(Generator\Converter::class);
        $parameterValidator->generator('ItemPersistor')
            ->shouldImplement(Generator\Persistor::class);
        $parameterValidator->generator('ItemMapper')
            ->shouldImplement(MapperGenerator::class);
        $parameterValidator->generator('ItemSearchResults')
            ->shouldImplement(SearchResults::class);

    }

    function it_creates_internal_io_factory_with_vfs_stream()
    {
        $this->serviceContainer->get('ecomdev.phpspec.magento_di_adapter.vfs')
            ->willReturn(vfsStream::setup('custom_root_dir'));

        $factory = $this->ioFactory();
        $factory->shouldImplement(\Closure::class);
        $factory($this->serviceContainer)->shouldImplement(Io::class);
    }

    function it_creates_vfs_stream_factory()
    {
        $factory = $this->vfsFactory();
        $factory->shouldImplement(\Closure::class);
        $factory()->shouldImplement(vfsStreamDirectory::class);
    }


    function it_creates_simplified_defined_classes_factory()
    {
        $factory = $this->simplifiedDefinedClassesFactory();
        $factory->shouldImplement(\Closure::class);
        $factory()->shouldImplement(SimplifiedDefinedClasses::class);
    }

    function it_creates_collaborator_maintainer_factory(ParameterValidator $parameterValidator)
    {
        $this->serviceContainer->get('ecomdev.phpspec.magento_di_adapter.parameter_validator')
            ->willReturn($parameterValidator)
            ->shouldBeCalled();

        $factory = $this->collaboratorMaintainerFactory();
        $factory->shouldImplement(\Closure::class);
        $factory($this->serviceContainer)->shouldImplement(CollaboratorMaintainer::class);
    }

    function it_adds_those_factories_into_container_on_load()
    {
        $this->serviceContainer
            ->set('ecomdev.phpspec.magento_di_adapter.code_generator.io', Argument::type(\Closure::class))
            ->shouldBeCalled();

        $this->serviceContainer
            ->set('ecomdev.phpspec.magento_di_adapter.vfs', Argument::type(\Closure::class))
            ->shouldBeCalled();

        $this->serviceContainer
            ->set('ecomdev.phpspec.magento_di_adapter.code_generator.defined_classes', Argument::type(\Closure::class))
            ->shouldBeCalled();


        $this->serviceContainer
            ->set('ecomdev.phpspec.magento_di_adapter.parameter_validator', Argument::type(\Closure::class))
            ->shouldBeCalled();
        
        $this->serviceContainer
            ->set('runner.maintainers.ecomdev_magento_collaborator', Argument::type(\Closure::class))
            ->shouldBeCalled();

        $this->load($this->serviceContainer);
    }
}
