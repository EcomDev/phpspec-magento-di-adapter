<?php

namespace spec\EcomDev\PHPSpec\MagentoDiAdapter\Runner;

use EcomDev\PHPSpec\MagentoDiAdapter\ParameterValidator;
use PhpSpec\Loader\Node\SpecificationNode;
use PhpSpec\ObjectBehavior;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\SpecificationInterface;
use Prophecy\Argument;

class CollaboratorMaintainerSpec extends ObjectBehavior
{
    /**
     * Parameter validator
     *
     * @var ParameterValidator
     */
    private $parameterValidator;

    function let(ParameterValidator $parameterValidator)
    {
        $this->parameterValidator = $parameterValidator;
        $this->beConstructedWith($this->parameterValidator);
    }

    function it_should_implement_maintainer_interface()
    {
        $this->shouldImplement(MaintainerInterface::class);
    }

    function it_should_support_any_example_node(ExampleNode $example)
    {
        $this->supports($example)->shouldReturn(true);
    }

    function it_has_higher_priority_than_current_collaborator_maintainer()
    {
        $this->getPriority()->shouldReturn(49);
    }


    function it_does_pass_regular_example_into_parameter_validator(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators,
        SpecificationNode $specificationNode,
        \ReflectionClass $reflectionClass
    )
    {

        $example->getSpecification()->willReturn($specificationNode);
        $specificationNode->getClassReflection()->willReturn($reflectionClass);
        $reflectionClass->hasMethod('let')->willReturn(false);

        $exampleClosureReflection = new \ReflectionFunction(function () {});
        $example->getFunctionReflection()->willReturn($exampleClosureReflection)->shouldBeCalled();
        $this->parameterValidator->validate($exampleClosureReflection)->shouldBeCalled();

        $this->prepare($example, $context, $matchers, $collaborators)->shouldReturn($this);
    }

    function it_does_pass_regular_example_into_parameter_validator_and_let_method_if_they_are_defined(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators,
        SpecificationNode $specificationNode,
        \ReflectionClass $reflectionClass
    )
    {
        $example->getSpecification()->willReturn($specificationNode);
        $specificationNode->getClassReflection()->willReturn($reflectionClass);
        $reflectionClass->hasMethod('let')->willReturn(true);

        $exampleClosureReflection = new \ReflectionFunction(function () {});
        $letClosureReflection = new \ReflectionFunction(function () {});
        $reflectionClass->getMethod('let')->willReturn($letClosureReflection)->shouldBeCalled();
        $example->getFunctionReflection()->willReturn($exampleClosureReflection)->shouldBeCalled();

        $this->parameterValidator->validate($letClosureReflection)->shouldBeCalled();
        $this->parameterValidator->validate($exampleClosureReflection)->shouldBeCalled();

        $this->prepare($example, $context, $matchers, $collaborators)->shouldReturn($this);
    }



    function it_does_not_have_anything_to_cleanup_on_teardown(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    )
    {
        $this->teardown($example, $context, $matchers, $collaborators)->shouldReturn($this);
    }
}
