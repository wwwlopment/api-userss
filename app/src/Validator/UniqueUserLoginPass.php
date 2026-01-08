<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class UniqueUserLoginPass extends Constraint
{
    public string $message = 'The pair login/password must be unique.';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
