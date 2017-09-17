<?php
declare(strict_types = 1);

namespace PTS\TypeCast\Types;

class DateTimeType
{
   public function __invoke($value): \DateTime
   {
       return new \DateTime($value);
   }
}
