<?php

declare(strict_types=1);

namespace MarioDevv\Criteria;

use MarioDevv\Criteria\Utils\Collection;

final class Filters extends Collection
{
    private string $type;

    public const TYPE_AND = 'AND';
    public const TYPE_OR  = 'OR';

    /**
     * @param Filter[]|Filters[] $items
     * @param string $type
     */
    public function __construct(array $items = [], string $type = self::TYPE_AND)
    {
        foreach ($items as $item) {
            if (!($item instanceof Filter || $item instanceof Filters)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The object <%s> is not an instance of <%s> or <%s>',
                        is_object($item) ? get_class($item) : gettype($item),
                        Filter::class,
                        Filters::class
                    )
                );
            }
        }
        parent::__construct($items);
        $this->type = $type;
    }

    /**
     * Retrocompatibilidad: crea un grupo AND con filtros simples
     */
    public static function fromPrimitives(array $values): self
    {
        return new self(array_map(self::filterBuilder(), $values), self::TYPE_AND);
    }

    /**
     * Crea un grupo AND explícito (puede contener Filter o Filters anidados)
     * @param array<Filter|Filters> $items
     */
    public static function and(array $items): self
    {
        return new self($items, self::TYPE_AND);
    }

    /**
     * Crea un grupo OR (puede contener Filter o Filters anidados)
     * @param array<Filter|Filters> $items
     */
    public static function or(array $items): self
    {
        return new self($items, self::TYPE_OR);
    }

    /**
     * Builder funcional (lambda)
     */
    private static function filterBuilder(): callable
    {
        return fn(array $values): Filter => Filter::fromPrimitives($values);
    }

    /**
     * Añade un filtro (inmutable)
     */
    public function add(Filter $filter): self
    {
        return new self(array_merge($this->items(), [$filter]), $this->type);
    }

    /**
     * @return Filter[]|Filters[]
     */
    public function filters(): array
    {
        return $this->items();
    }

    /**
     * Tipo de grupo (AND u OR)
     */
    public function logicType(): string
    {
        return $this->type;
    }

    /**
     * Serializa el grupo de filtros, con soporte para anidados
     */
    public function serialize(): string
    {
        return array_reduce(
            $this->items(),
            function (string $accumulate, $filter): string {
                $serialized = $filter instanceof self
                    ? '(' . $filter->serialize() . ')'
                    : $filter->serialize();
                return $accumulate === '' ? $serialized : sprintf('%s^%s', $accumulate, $serialized);
            },
            ''
        );
    }

    /**
     * Restringe el tipo de elementos permitidos en la colección
     * (puedes dejarlo como Filter si tu Collection lo requiere,
     * pero idealmente deberías aceptar también Filters para permitir la anidación).
     */
    protected function type(): array
    {
        return [Filter::class, Filters::class];
    }
}
