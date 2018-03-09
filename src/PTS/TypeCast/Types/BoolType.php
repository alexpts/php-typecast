<?php
declare(strict_types=1);

namespace PTS\TypeCast\Types;

class BoolType
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function __invoke($value): bool
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($value === null) {
            throw new \InvalidArgumentException('Can`t cast to boolean');
        }

        return $value;
    }
}
