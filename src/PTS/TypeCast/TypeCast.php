<?php

namespace PTS\TypeCast;

use PTS\Tools\DeepArray;
use PTS\Tools\RegExpFactory;
use PTS\TypeCast\Types\BoolType;
use PTS\TypeCast\Types\DateTimeType;

class TypeCast
{
    /** @var string */
    protected $paramDelimiter = ':';
    /** @var string */
    protected $keysDelimiter = '.';

    /** @var array */
    protected $types = [];

    /** @var DeepArray */
    protected $deepArrayService;
    /** @var RegExpFactory */
    protected $regExpFactory;
    /** @var PropException */
    protected $notExistValue;

    public function __construct(DeepArray $deepArrayService, RegExpFactory $regExpFactory)
    {
        $this->notExistValue = new PropException('Value is not exists');
        $this->deepArrayService = $deepArrayService;
        $this->regExpFactory = $regExpFactory;

        $this->registerType('string', $this->setType('string'))
            ->registerType('int', $this->setType('int'))
            ->registerType('float', $this->setType('float'))
            ->registerType('bool', new BoolType)
            ->registerType('array', $this->setType('array'))
            ->registerType('object', $this->setType('object'))
            ->registerType('null', $this->setType('null'))
            ->registerType('datetime', new DateTimeType)
            ->registerType('datetimeFormat', [$this, 'datetimeFormat'])
            ->registerType('numbers', $regExpFactory->create('~[^\d+]~', ''))
            ->registerType('each', [$this, 'eachHandlerType']);
    }

    public function getRegExpFactory(): RegExpFactory
    {
        return $this->regExpFactory;
    }

    protected function datetimeFormat(\DateTime $value, string $format = 'd-m-Y'): string
    {
        return $value->format($format);
    }

    /**
     * @param array $coll
     * @param array $rules
     * @return array
     * @throws PropException
     */
    protected function eachHandlerType(array $coll, array $rules): array
    {
        foreach ($coll as &$value) {
            $value = $this->castValue($value, $rules);
        }

        return $coll;
    }

    /**
     * @param string $type
     * @return callable
     */
    protected function setType(string $type): callable
    {
        return function($value) use ($type) {
            settype($value, $type);

            return $value;
        };
    }

    /**
     * @param string $name
     * @param callable $handler
     * @return $this
     */
    public function registerType(string $name, callable $handler): self
    {
        $this->types[$name] = $handler;
        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param array $data
     * @param array $rules
     * @return array
     * @throws PropException
     */
    public function cast(array $data, array $rules): array
    {
        foreach ($rules as $name => $attrRules) {
            $value = $this->getValue($name, $data, $this->notExistValue);

            if (!($value instanceof $this->notExistValue)) {
                $value = $this->castValue($value, $attrRules);
                $this->setValue($name, $data, $value);
            }
        }

        return $data;
    }

    /**
     * @param mixed $value
     * @param array $rules
     * @return mixed
     * @throws PropException
     */
    protected function castValue($value, array $rules)
    {
        foreach ($rules as $rule) {
            list($handlerAlias, $params) = is_string($rule)
                ? $this->extractStringRule($rule)
                : $this->extractArrayRule($rule);

            $value = $this->applyCastType($value, $params, $handlerAlias);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param array $params
     * @param string $handlerAlias
     * @return mixed
     * @throws PropException
     */
    protected function applyCastType($value, array $params, string $handlerAlias)
    {
        $handler = $this->types[$handlerAlias] ?? null;
        if (!$handler) {
            throw new PropException("Handler not found for alias: {$handlerAlias}");
        }

        $params = $handlerAlias === 'each' ? [$params] : $params;
        return $handler($value, ...$params);
    }

    /**
     * @param string $name
     * @param array $data
     * @param mixed $default
     * @return mixed
     */
    protected function getValue(string $name, array $data, $default)
    {
        $names = explode($this->keysDelimiter, $name);
        return $this->deepArrayService->getAttr($names, $data, $default);
    }

    protected function setValue(string $name, array &$data, $value)
    {
        $names = explode($this->keysDelimiter, $name);
        $this->deepArrayService->setAttr($names, $data, $value);
    }

    public function extractArrayRule(array $rule): array
    {
        return [key($rule), current($rule)];
    }

    public function extractStringRule(string $rule): array
    {
        $params = explode($this->paramDelimiter, $rule);
        $handlerAlias = array_shift($params);

        return [$handlerAlias, (array) $params];
    }
}
