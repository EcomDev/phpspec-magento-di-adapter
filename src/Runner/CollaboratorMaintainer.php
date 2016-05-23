<?php

namespace EcomDev\PHPSpec\MagentoDiAdapter\Runner;

use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\SpecificationInterface;

class CollaboratorMaintainer implements MaintainerInterface
{
    public function supports(ExampleNode $example)
    {
        return true;
    }

    public function prepare(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
    
        // TODO: Implement prepare() method.
    }

    public function teardown(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
    
        // TODO: Implement teardown() method.
    }

    public function getPriority()
    {
        return 49;
    }
}
