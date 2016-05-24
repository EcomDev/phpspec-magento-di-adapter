# PHPSpec Magento 2.0 DI Adapter [![Build Status](https://travis-ci.org/EcomDev/phpspec-magento-di-adapter.svg?branch=develop)](https://travis-ci.org/EcomDev/phpspec-magento-di-adapter?branch=develop)  [![Coverage Status](https://coveralls.io/repos/github/EcomDev/phpspec-magento-di-adapter/badge.svg?branch=develop)](https://coveralls.io/github/EcomDev/phpspec-magento-di-adapter?branch=develop)

This small PHPSpec extension allows you to test Magento 2.0 modules much more easier by utilizing Factory generators of Magento\Framework\ObjectManager.

## Installation

1. Install via composer

    ```bash
    composer require --dev ecomdev/phpspec-magento-di-adapter
    ```

2. Add to your phpspec.yml

    ```yaml
    extensions:
       - EcomDev\PHPSpec\MagentoDiAdapter\Extension
    ```

## Usage

Make sure that when you write examples to specify fully qualified class name for auto-generated class. 

```php
<?php

namespace spec\Acme\CustomMagentoModule\Model;

use Magento\Catalog\Model\Product;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProductManagerSpec extends ObjectBehavior
{
    private $productFactory; 
    
    function let(ProductFactory $factory) {
        $this->productFactory = $factory;    
        $this->beConstructedWith($factory);
    }
    
    function it_creates_items_via_product_factory(Product $product)
    {
        $this->productFactory->create()->willReturn($product)->shouldBeCalled();
        $this->someCreationLogic();
    }
}
```

This approach will not get you a desired result, as PHP by default looks for undefined classes within the same namespace.
So instead of `Magento\Catalog\Model\ProductFactory` it will generate a class `spec\Acme\CustomMagentoModule\Model\ProductFactory`, that is definitely not a desired behavior.

In order to fix that make sure to specify fully qualified name in method signature or via `use` operator in the file header.

```php
<?php

namespace spec\Acme\CustomMagentoModule\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory; // This class will be automatically generated
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProductManagerSpec extends ObjectBehavior
{
    private $productFactory; 
    
    function let(ProductFactory $factory) {
        $this->productFactory = $factory;    
        $this->beConstructedWith($factory);
    }
    
    function it_creates_items_via_product_factory(Product $product)
    {
        $this->productFactory->create()->willReturn($product)->shouldBeCalled();
        $this->someCreationLogic();
    }
}
```

## Contribution
Make a pull request based on develop branch
