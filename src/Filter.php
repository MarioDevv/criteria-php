<?php

declare(strict_types=1);

namespace MarioDevv\Criteria;

final readonly class Filter
{
    private FilterField    $field;
    private FilterOperator $operator;
    private FilterValue    $value;

    public function __construct(FilterField $field, FilterOperator $operator, FilterValue $value)
    {
        $this->field    = $field;
        $this->operator = $operator;
        $this->value    = $value;
    }

    /**
     * Construye un filtro desde un array primitivo
     */
    public static function fromPrimitives(array $values): self
    {
        return new self(
            new FilterField($values['field']),
            FilterOperator::tryFrom($values['operator']),
            new FilterValue($values['value'])
        );
    }

    /**
     * Construye un filtro desde una cadena (por ejemplo: "status::=::pending")
     */
    public static function fromString(string $filter): self
    {
        $parts = explode('::', $filter);

        if (count($parts) === 3) {
            [$field, $operator, $value] = $parts;
            return new self(
                new FilterField($field),
                FilterOperator::tryFrom($operator),
                new FilterValue($value)
            );
        }

        throw new \InvalidArgumentException(sprintf('Invalid filter format: %s', $filter));
    }

    /**
     * Serializa el filtro para uso en URL o cadena
     */
    public function serialize(): string
    {
        return sprintf(
            '%s::%s::%s',
            $this->field()->value(),
            $this->operator()->value,
            $this->value()->value()
        );
    }

    public function field(): FilterField
    {
        return $this->field;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }

    public function value(): FilterValue
    {
        return $this->value;
    }

}
