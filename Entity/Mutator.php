<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Hateoas\Entity;

// JSON-API.
use GoIntegro\Hateoas\JsonApi\Request\Params,
    GoIntegro\Hateoas\JsonApi\ResourceEntityInterface;

class Mutator
{
    const DEFAULT_MUTATOR = 'default',
        DUPLICATED_MUTATOR = "A mutator for the resource type \"%s\" is already registered.";

    /**
     * @var array
     */
    private $mutators = [];

    /**
     * @param Params $params
     * @param ResourceEntityInterface $entity
     * @param array $fields
     * @param array $relationships
     * @param array $metadata
     * @return \GoIntegro\Hateoas\JsonApi\ResourceEntityInterface
     */
    public function update(
        Params $params,
        ResourceEntityInterface $entity,
        array $fields,
        array $relationships = [],
        array $metadata = []
    )
    {
        $mutator = isset($this->mutators[$params->primaryType])
            ? $this->mutators[$params->primaryType]
            : $this->mutators[self::DEFAULT_MUTATOR];

        return $mutator->update($entity, $fields, $relationships, $metadata);
    }

    /**
     * @param MutatorInterface
     */
    public function addMutator(MutatorInterface $mutator, $resourceType)
    {
        if (isset($this->mutators[$resourceType])) {
            $message = sprintf(self::DUPLICATED_MUTATOR, $resourceType);
            throw new \ErrorException($message);
        }

        $this->mutators[$resourceType] = $mutator;

        return $this;
    }
}
