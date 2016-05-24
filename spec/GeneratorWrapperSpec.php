<?php

namespace spec\EcomDev\PHPSpec\MagentoDiAdapter;

use Magento\Framework\Code\Generator\EntityAbstract;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GeneratorWrapperSpec extends ObjectBehavior
{
    /**
     * @var EntityAbstract
     */
    private $entityGenerator;

    function let(EntityAbstract $entityGenerator)
    {
        $this->entityGenerator = $entityGenerator;
        $this->beConstructedWith(
            function () {},
            'entity'
        );
    }

    function it_should_support_class_that_is_prefixed_with_suffix_in_argument()
    {
        // Supporting
        $this->supports('Some\Class\Entity')->shouldReturn(true);
        $this->supports('Some\ClassEntity')->shouldReturn(true);

        // Not supporting
        $this->supports('Some\ClassFactory')->shouldReturn(false);
        $this->supports('Some\Class\Factory')->shouldReturn(false);

        // Should not allow word parts
        $this->supports('Some\Classentity')->shouldReturn(false);
        $this->supports('Some\EntityClass')->shouldReturn(false);
    }


    function it_generates_a_class_with_created_generator()
    {
        $this->entityGenerator->init('Some\Class', 'Some\ClassEntity')->shouldBeCalled();
        $this->entityGenerator->generate()->willReturn('path/to/SomeClassEntity.php')->shouldBeCalled();

        $this->beConstructedWith($this->entityFactoryClosure(), 'entity');
        $this->generate('Some\ClassEntity')->shouldReturn('path/to/SomeClassEntity.php');
    }

    function it_allows_suffixed_class_name_in_subnamespace()
    {
        $this->entityGenerator->init('Some\Class', 'Some\ClassEntity')->shouldBeCalled();
        $this->beConstructedWith($this->entityFactoryClosure(), 'entity');
        $this->createGenerator('Some\ClassEntity')->shouldReturn($this->entityGenerator);
    }

    function it_allows_separated_class_name_in_subnamespace()
    {
        $this->entityGenerator->init('Some\Class', 'Some\Class\Entity')->shouldBeCalled();
        $this->beConstructedWith($this->entityFactoryClosure(), 'entity');
        $this->createGenerator('Some\Class\Entity')->shouldReturn($this->entityGenerator);
    }

    private function entityFactoryClosure()
    {
        return function ($sourceClass, $originalClass) {
            $generator = $this->entityGenerator->getWrappedObject();
            $generator->init($sourceClass, $originalClass);
            return $generator;
        };
    }
}
