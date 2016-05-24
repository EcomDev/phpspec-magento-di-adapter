<?php

namespace spec\EcomDev\PHPSpec\MagentoDiAdapter;

use EcomDev\PHPSpec\MagentoDiAdapter\Generator\SimplifiedDefinedClasses;
use Magento\Framework\ObjectManager\Code\Generator;
use Magento\Framework\Code\Generator\Io;
use Magento\Framework\Filesystem\Driver\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\Loader\Transformer\TypeHintIndex;
use spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture\Catcher;
use spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture\SignatureClass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture\TypeHintClass;
use spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture\ValidClass;

class ParameterValidatorSpec extends ObjectBehavior
{
    /**
     * Root virtual directories
     *
     * @var vfsStreamDirectory
     */
    private $vfs;

    /**
     * Io for code generation
     *
     * @var Io
     */
    private $io;

    /**
     * Definer classes
     *
     * @var SimplifiedDefinedClasses
     */
    private $definedClasses;

    /**
     * Class reflection for autoload tests
     *
     * @var \ReflectionClass
     */
    private $classReflection;

    /**
     * @var TypeHintIndex
     */
    private $typeHintIndex;

    function let(TypeHintIndex $typeHintIndex)
    {
        $this->vfs = vfsStream::setup('testcase');
        $this->io = new Io(new File(), $this->vfs->url());
        $this->definedClasses = new SimplifiedDefinedClasses();
        $this->classReflection = new \ReflectionClass(SignatureClass::class);
        $this->typeHintIndex = $typeHintIndex;

        $this->beConstructedWith($this->io, $this->definedClasses, $this->typeHintIndex);
    }

    function it_is_possible_to_add_multiple_entity_generators()
    {
        $this->addGenerator(Generator\Factory::class, Generator\Factory::ENTITY_TYPE)->shouldReturn($this);
        $this->addGenerator(Generator\Repository::class, Generator\Repository::ENTITY_TYPE)->shouldReturn($this);
        
        $this->generator('ClassFactory')->shouldImplement(Generator\Factory::class);
        $this->generator('ClassRepository')->shouldImplement(Generator\Repository::class);
    }

    function it_is_possible_to_add_custom_generator_creation_closure(Catcher $catcher)
    {
        $catcher->invoke([
            'Some\Class',
            'Some\ClassFactory',
            $this->io,
            $this->definedClasses,
            Generator\Factory::class
        ])->shouldBeCalled();

        $this->addGenerator(Generator\Factory::class, Generator\Factory::ENTITY_TYPE, function () use ($catcher) {
            $catcher = $catcher->getWrappedObject();
            $catcher->invoke(func_get_args());
            return $catcher;
        })->shouldReturn($this);

        $this->generator('Some\ClassFactory')->shouldBe($catcher);
    }

    function it_returns_false_if_generator_does_not_exists_for_a_class()
    {
        $this->generator('Some\ClassFactory')->shouldReturn(false);
    }

    function it_generates_a_class_via_generator_for_parameter_that_does_not_exits()
    {
        $functionReflection = $this->classReflection->getMethod('valid_class_factory_param');

        $this->addGenerator(Generator\Factory::class, Generator\Factory::ENTITY_TYPE)->shouldReturn($this);

        $this->validate($functionReflection)->shouldReturn($this);

        $this->shouldCreateFile($this->vfs->url() . '/spec/EcomDev/PHPSpec/MagentoDiAdapter/Fixture/ValidClassFactory.php');
    }

    function it_supports_type_hint_index_method_data_retrieval()
    {
        $this->typeHintIndex->lookup(SignatureClass::class, 'type_hint_index_resolved_class', '$parameter')
            ->willReturn(TypeHintClass::class . 'Factory')
            ->shouldBeCalled();

        $this->addGenerator(Generator\Factory::class, Generator\Factory::ENTITY_TYPE)->shouldReturn($this);

        $functionReflection = $this->classReflection->getMethod('type_hint_index_resolved_class');

        $this->validate($functionReflection)->shouldReturn($this);

        $this->shouldCreateFile($this->vfs->url() . '/spec/EcomDev/PHPSpec/MagentoDiAdapter/Fixture/TypeHintClassFactory.php');
    }

    function it_does_not_generate_a_class_for_which_we_do_not_have_a_rule()
    {
        $functionReflection = $this->classReflection->getMethod('non_existent_class_param');

        $this->validate($functionReflection)->shouldReturn($this);

        $this->shouldNotCreateFile($this->vfs->url() . '/spec/EcomDev/PHPSpec/MagentoDiAdapter/Fixture/InvalidClass.php');
    }



}
