# PipeChain
A linear chainable pipeline pattern implementation with fallbacks.

## What is a linear pipeline

A linear pipeline is a object that stacks callbacks and provides a
processing logic to process a payload thru the callback stack,
form the first to the last.

## Processors

PipeChain comes out of the box with 4 different processors:

### The common invoker processor

The common invoker processor implements a processing of the
queue that considers the provided fallback callback as the
successor of a failed stage callback.

The common invoker processor is the default processor.

Example:

```php
<?php declare(strict_types=1);

use PipeChain\{
    PipeChain
};

$pipeline = new PipeChain();

$pipeline->pipe(
    # the stage callback
    function(string $inbound) {
        return 'something';
    },
    # the fallback callback
    function(int $inbound) {
        return ++$inbound;
    }
);

var_dump(
    $pipeline->process(100) // int(3) => 101
);

```

### The naive invoker processor

The naive invoker processor implements a processing of the
queue that ignores the provided fallback callback.

Example:

```php
<?php declare(strict_types=1);

use PipeChain\{
    PipeChain,
    InvokerProcessors\NaiveInvokerProcessor
};

$pipeline = new PipeChain(new NaiveInvokerProcessor());

$pipeline->pipe(function(int $inbound) {
    return ++$inbound;
});

var_dump(
    $pipeline->process(100) // int(3) => 101
);

```

### The loyal interface invoker processor

The loyal interface invoker processor implements a processing
of the queue that acts like a common invoker processor with
an additional interface check of each payload change.

The interface check has 2 modes:
- `LoyalInterfaceInvokerProcessor::INTERRUPT_ON_MISMATCH` forces
  the processor to pass the throwables to the next scope.
- `LoyalInterfaceInvokerProcessor::RESET_ON_MISMATCH` forces
  the processor to use the previous payload instead.

Example:

```php
<?php declare(strict_types=1);

use PipeChain\{
    PipeChain,
    InvokerProcessors\LoyalInterfaceInvokerProcessor
};

$pipeline = new PipeChain(
    new LoyalInterfaceInvokerProcessor(
        DateTimeInterface::class,
        LoyalInterfaceInvokerProcessor::RESET_ON_MISMATCH
    )
);

$prior = function($inbound) {
    if ( ! $inbound instanceof DateTime ) {
        throw new Exception('Whats this?');
    }
    
    return $inbound->modify('+10 hours');
};

$failure = function (string $inbound) use ($prior) {
    return $prior(date_create($inbound));
};

$pipeline->pipe($prior, $failure);

var_dump(
    $pipeline
        ->process('2017-10-12 18:00:00')
        ->format('Y-m-d') // string(10) "2017-10-13"
);

```

### The loyal type invoker processor

The loyal type invoker processor implements a processing
of the queue that acts like the loyal interface invoker
processor but with type checking of each payload change.

The type check has 2 modes:
- `LoyalInterfaceInvokerProcessor::INTERRUPT_ON_MISMATCH` forces
  the processor to pass the throwables to the next scope.
- `LoyalInterfaceInvokerProcessor::RESET_ON_MISMATCH` forces
  the processor to use the previous payload instead.

Example:

```php
<?php declare(strict_types=1);

use PipeChain\{
    PipeChain,
    InvokerProcessors\LoyalTypeInvokerProcessor
};

$pipeline = new PipeChain(
    new LoyalTypeInvokerProcessor(
        'string',
        LoyalTypeInvokerProcessor::RESET_ON_MISMATCH
    )
);

$pipeline->pipe(function(string $name) {
    return strtolower($name);
});
$pipeline->pipe(function(string $name) {
    return ucfirst($name);
});

var_dump(
    $pipeline->process('jOhN') // string(4) "John"
);

```

## Booting own implementations

```php
<?php declare(strict_types=1);

namespace Somewhat;

use PipeChain\{
    PipeChain,
    InvokerProcessorInterface as Processor,
    PipeChainCollectionInterface as Container
};

class MyFirstPipeline extends PipeChain {
    
    protected function boot(Container $container, Processor $processor = null): Processor
    {
        # pipe something. The signature of PipeChainCollectionInterface::attach() is equal
        # to PipeChain::pipe(..).
        
        $container->attach(
            # stage callable
            function() {
                
            },
            # fallback callable (optional)
            function() {
                
            }
        );
        
        # ...
        
        # Don't forget to call the parent, the parent method ensures
        # that a processor will be created when $processor is null.
        
        return parent::boot($container, $processor);
    }
}

# later ...

echo MyFirstPipeline::create()->process('I can get no satisfaction!');

```

## Chaining Pipelines

```php
<?php declare(strict_types=1);

$pipeline = MyFirstPipeline::create()->chain(
    MySecondPipeline::create()
)->chain(
    MyThirdPipeline::create()
);

$pipeline->process('This is awesome.');
```

## Maintainer, State of this Package and License

Those are the Maintainer of this Package:

- [Matthias Kaschubowski](https://github.com/nhlm)

This package is released under the MIT license. A copy of the license
is placed at the root of this repository.

The State of this Package is unstable unless unit tests are added.

## Todo

- Adding Unit Tests