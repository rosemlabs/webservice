<?php

namespace True\DI\Binding;

use ReflectionFunction;
use SplFixedArray;
use True\DI\AbstractBinding;
use True\DI\AbstractContainer;
use True\DI\ReflectedBuildTrait;

class FunctionBinding extends AbstractBinding
{
    use ReflectedBuildTrait;

    /**
     * @var ReflectionFunction
     */
    protected $reflector;

    /**
     * @var SplFixedArray
     */
    protected $stack = [];

    public function __construct(AbstractContainer $container, string $abstract, $concrete, array $args = [])
    {
        parent::__construct($container, $abstract, $concrete, $args);

        $this->reflector = new ReflectionFunction($this->concrete);

        if ($params = SplFixedArray::fromArray($this->reflector->getParameters())) {
            $this->stack = $this->getStack($params);
        }
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        return $this->reflector->invokeArgs($this->build($this->stack, reset($args) ?: $this->args));
    }
}