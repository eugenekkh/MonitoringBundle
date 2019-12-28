<?php

namespace StudioSite\MonitoringBundle\Parameter;

use InvalidArgumentException;
use LogicException;
use ReflectionClass;

/**
 * Collection of the monitored parameters
 */
class ParameterCollection
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Add the monitoring parameter to the collection
     *
     * @param object $service
     * @param string $method
     * @param string $key
     */
    public function addParameter($service, string $method, string $key): void
    {
        if (!is_object($service)) {
            throw new LogicException('Service must be object');
        }

        if (!is_callable([$service, $method])) {
            throw new LogicException(sprintf(
                'Method "%s" not callable',
                $method
            ));
        }

        if (isset($this->parameters[$key])) {
            throw new LogicException(sprintf('Key with name "%s" already added', $key));
        }

        $this->parameters[$key] = [
            $service,
            $method
        ];
    }

    /**
     * Get list of argument of the monitoring parameter by name
     *
     * @param string $key
     *
     * @return string[]
     */
    public function getArguments(string $key): array
    {
        if (!isset($this->parameters[$key])) {
            throw new InvalidArgumentException('');
        }

        $call = $this->parameters[$key];
        $serviceReflection = new ReflectionClass($call[0]);
        $methodReflection = $serviceReflection->getMethod($call[1]);
        $reflectionParameters = $methodReflection->getParameters();

        $parameters = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $parameters[] = $reflectionParameter->getName();
        }

        return $parameters;
    }

    /**
     * Get list of monitoring parameter and their arguments
     *
     * @return string[]
     */
    public function getList(): array
    {
        $result = [];
        foreach ($this->parameters as $key => $parameter) {
            $result[$key] = $this->getArguments($key);
        }

        ksort($result);

        return $result;
    }

    /**
     * Get value of the monitoring parameter
     *
     * @return string[]
     */
    public function getValue(string $key, array $arguments = []): string
    {
        if (!isset($this->parameters[$key])) {
            throw new InvalidArgumentException(sprintf('Parameter %s not found', $key));
        }

        return (string) call_user_func_array($this->parameters[$key], $arguments);
    }
}
