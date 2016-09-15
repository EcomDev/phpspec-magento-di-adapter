<?php

namespace EcomDev\PHPSpec\MagentoDiAdapter\Runner;

use EcomDev\PHPSpec\MagentoDiAdapter\ParameterValidator;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Runner\Maintainer\Maintainer;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Specification;

/**
 * Collaborator maintainer for Magento DI classes auto-generation
 */
class CollaboratorMaintainer implements Maintainer
{
    /**
     * Parameter Validator instance
     *
     * @var ParameterValidator
     */
    private $parameterValidator;

    /**
     * Configures our collaborator maintainer
     *
     * @param ParameterValidator $parameterValidator
     */
    public function __construct(ParameterValidator $parameterValidator)
    {
        $this->parameterValidator = $parameterValidator;
    }

    /**
     * Supports all example nodes
     *
     * @param ExampleNode $example
     *
     * @return bool
     */
    public function supports(ExampleNode $example)
    {
        return true;
    }

    /**
     * Generates DI related stuff via parameter validator
     *
     * @param ExampleNode $example
     * @param Specification $context
     * @param MatcherManager $matchers
     * @param CollaboratorManager $collaborators
     *
     * @return $this
     */
    public function prepare(
        ExampleNode $example,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
        if ($example->getSpecification()->getClassReflection()->hasMethod('let')) {
            $this->parameterValidator->validate($example->getSpecification()->getClassReflection()->getMethod('let'));
        }
        $this->parameterValidator->validate($example->getFunctionReflection());
        return $this;
    }

    /**
     * It does nothing on teardown...
     *
     * @param ExampleNode $example
     * @param Specification $context
     * @param MatcherManager $matchers
     * @param CollaboratorManager $collaborators
     *
     * @return $this
     */
    public function teardown(
        ExampleNode $example,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
    
        return $this;
    }

    /**
     * Priority of maintainer to put it before native collaborator maintainer
     *
     * @return int
     */
    public function getPriority()
    {
        return 51;
    }
}
