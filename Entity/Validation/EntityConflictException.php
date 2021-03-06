<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Hateoas\Entity\Validation;

// Exceptions.
use GoIntegro\Hateoas\JsonApi\Exception\ConflictException;

class EntityConflictException
    extends ConflictException
    implements EntityConflictExceptionInterface
{
}
