# PHPSpec Magento 2.0 DI Adapter [![Build Status](https://travis-ci.org/EcomDev/phpspec-magento-di-adapter.svg?branch=develop)](https://travis-ci.org/EcomDev/phpspec-magento-di-adapter?branch=develop)  [![Coverage Status](https://coveralls.io/repos/github/EcomDev/phpspec-magento-di-adapter/badge.svg?branch=develop)](https://coveralls.io/github/EcomDev/phpspec-magento-di-adapter?branch=develop)

This small PHPSpec extension allows you to test Magento 2.0 modules much more easier by utilizing generators of `Magento\Framework\ObjectManager`.

## Why?
Reasons why not to use `ObjectManager` in PHPSpec examples:

1. It is heavy and requires stubbing full file system in order to run a simple spec example.
2. Depending on ObjectManager is a bad idea, as you don't want to test some-else DI overrides.
3. Simple modules that do not require database do not need fully functional object manager
4. Adapting your business logic to another framework will require from you only to materialize generated classes, instead of depending on the whole ObjectManager library.

## Supported Generators

* Factory
* Repository
* Converter
* Persistor
* Mapper
* SearchResults

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
