<?php

declare(strict_types=1);

namespace MarioDevv\Criteria\Utils;

use InvalidArgumentException;

final class Assert
{
    /**
     * @param string|array $class
     * @param array $items
     */
    public static function arrayOf(string|array $class, array $items): void
    {
        foreach ($items as $item) {
            $ok = false;
            foreach ((array)$class as $type) {
                if ($item instanceof $type) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) {
                throw new InvalidArgumentException(sprintf(
                    'The object <%s> is not an instance of any allowed type: [%s]',
                    is_object($item) ? get_class($item) : gettype($item),
                    is_array($class) ? implode(', ', $class) : $class
                ));
            }
        }
    }

    public static function instanceOf(string $class, mixed $item): void
    {
        if (!$item instanceof $class) {
            throw new InvalidArgumentException(sprintf(
                'The object <%s> is not an instance of <%s>',
                is_object($item) ? get_class($item) : gettype($item),
                $class
            ));
        }
    }
}
