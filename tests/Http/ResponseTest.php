<?php

namespace Foris\LaExtension\Tests\Http;

use Foris\LaExtension\Http\Response;
use Foris\LaExtension\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class ResponseTest
 */
class ResponseTest extends TestCase
{
    /**
     * Test get response instance
     */
    public function testGetResponseInstance()
    {
        $this->assertInstanceOf(Response::class, app('app-ext.response'));
    }

    /**
     * Test get success response
     */
    public function testSuccessResponse()
    {
        $response = Response::success();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_OK, $response->getStatusCode());

        $expect = [
            'code' => $this->app['config']->get('app-ext.api_response_code.success'),
            'message' => 'success',
            'data' => [],
        ];
        $this->assertEquals($expect, $response->getData(true));
    }

    /**
     * Test get failure response
     */
    public function testFailureResponse()
    {
        $response = Response::failure();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_OK, $response->getStatusCode());

        $expect = [
            'code' => $this->app['config']->get('app-ext.api_response_code.failure'),
            'message' => 'failure',
            'data' => [],
        ];
        $this->assertEquals($expect, $response->getData(true));
    }

    /**
     * Test get forbidden response
     */
    public function testForbiddenResponse()
    {
        $response = Response::forbidden();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_FORBIDDEN, $response->getStatusCode());

        $expect = [
            'code' => $this->app['config']->get('app-ext.api_response_code.forbidden'),
            'message' => 'forbidden',
            'data' => [],
        ];
        $this->assertEquals($expect, $response->getData(true));
    }

    /**
     * Test get unauthorized response
     */
    public function testUnauthorizedResponse()
    {
        $response = Response::unauthorized();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $expect = [
            'code' => $this->app['config']->get('app-ext.api_response_code.unauthorized'),
            'message' => 'unauthorized',
            'data' => [],
        ];
        $this->assertEquals($expect, $response->getData(true));
    }

    /**
     * Test get exception response
     */
    public function testExceptionResponse()
    {
        $response = Response::exception();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $expect = [
            'code' => $this->app['config']->get('app-ext.api_response_code.exception'),
            'message' => 'exception',
            'data' => [],
        ];
        $this->assertEquals($expect, $response->getData(true));
    }

    /**
     * Test get 404 not found response
     */
    public function testNotFoundResponse()
    {
        $response = Response::notFound();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_NOT_FOUND, $response->getStatusCode());

        $expect = [
            'code' => $this->app['config']->get('app-ext.api_response_code.notFound'),
            'message' => '404 not found',
            'data' => [],
        ];
        $this->assertEquals($expect, $response->getData(true));
    }
}
