<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace JsonApi\Request;

// Mocks.
use Codeception\Util\Stub;
// Request.
use GoIntegro\Hateoas\JsonApi\Request\ActionParser,
    GoIntegro\Hateoas\JsonApi\Request\RequestAction,
    GoIntegro\Hateoas\JsonApi\Request\Parser;

class ActionParserTest extends \PHPUnit_Framework_TestCase
{
    const API_BASE_URL = '/api/v1',
        RESOURCE_TYPE = 'users',
        HTTP_PUT_BODY = <<<'JSON'
{
    "users": {
        "id": "7",
        "name": "John",
        "surname": "Connor"
    }
}
JSON;

    public function testParsingSingleUpdateActionRequest()
    {
        // Given...
        $queryOverrides = [
            'getContent' => function() { return self::UPDATE_BODY; }
        ];
        $jsonCoder = Stub::makeEmpty(
            'GoIntegro\\Json\\JsonCoder',
            ['decode' => json_decode(self::HTTP_PUT_BODY, TRUE)]
        );
        $request = self::createRequest(
            '/api/v1/users',
            $queryOverrides,
            Parser::HTTP_PUT,
            self::HTTP_PUT_BODY
        );
        $params = Stub::makeEmpty(
            'GoIntegro\\Hateoas\\JsonApi\\Request\\Params',
            [
                'primaryIds' => ['27'],
                'primaryType' => 'users'
            ]
        );
        $parser = new ActionParser($jsonCoder, self::createMetadataMiner());
        // When...
        $action = $parser->parse($request, $params);
        // Then...
        $this->assertSame(RequestAction::ACTION_UPDATE, $action->name);
        $this->assertSame(RequestAction::TYPE_SINGLE, $action->type);
        $this->assertSame(RequestAction::TARGET_RESOURCE, $action->target);
    }

    /**
     * @param string $pathInfo
     * @param array $queryOverrides
     * @param string $method
     * @param string $body
     * @return Request
     */
    private static function createRequest(
        $pathInfo,
        array $queryOverrides,
        $method = Parser::HTTP_GET,
        $body = NULL
    )
    {
        $defaultOverrides = [
            'getIterator' => function() { return new \ArrayIterator([]); }
        ];
        $queryOverrides = array_merge($defaultOverrides, $queryOverrides);
        $query = Stub::makeEmpty(
            'Symfony\Component\HttpFoundation\ParameterBag',
            $queryOverrides
        );
        $request = Stub::makeEmpty(
            'Symfony\Component\HttpFoundation\Request',
            [
                'query' => $query,
                'getPathInfo' => $pathInfo,
                'getMethod' => $method,
                'getContent' => $body
            ]
        );

        return $request;
    }

    /**
     * @return \GoIntegro\Hateoas\Metadata\Resource\MetadataMinerInterface
     */
    private static function createMetadataMiner()
    {
        $metadata = Stub::makeEmpty(
            'GoIntegro\\Hateoas\\Metadata\\Resource\\ResourceMetadata',
            ['isRelationship' => TRUE, 'isLinkOnlyRelationship' => FALSE]
        );

        return Stub::makeEmpty(
            'GoIntegro\\Hateoas\\Metadata\\Resource\\MetadataMinerInterface',
            ['mine' => $metadata]
        );
    }
}
