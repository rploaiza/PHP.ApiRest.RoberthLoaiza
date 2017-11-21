<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;


/**
 * Class ApiResultControllerTest
 *
 * @package AppBundle\Tests\Controller
 */
class ApiResultControllerTest extends WebTestCase
{
    const RUTA_API = \AppBundle\Controller\ApiResultController::RUTA_API;
    const RUTA_APIS = \AppBundle\Controller\ApiUserController::RUTA_API;

    /**
     * Client
     * @var Client $_clientOptions
     */
    private static $_clientOptions;
    private static $_client;
    private static $_clientPostUser;
    private static $_clientGetId;
    private static $_clientGet;
    private static $_clientPost201;
    private static $_clientPut;
    private static $_clientDelete;
    private static $_clientDelete404;
    private static $_clientGet404;
    private static $_clientPut404;

    private static $user;

    /**
     * This method is called before the first test of this test class is run.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$_client = static::createClient();
        self::$_clientOptions = static::createClient();
        self::$_clientPostUser = static::createClient();
        self::$_clientPost201 = static::createClient();
        self::$_clientGetId = static::createClient();
        self::$_clientGet = static::createClient();
        self::$_clientPut = static::createClient();
        self::$_clientDelete = static::createClient();
        self::$_clientDelete404 = static::createClient();
        self::$_clientGet404 = static::createClient();
        self::$_clientPut404 = static::createClient();

        $rand_num = mt_rand(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            'username' => $nombre,
            'email' => 'email' . $rand_num . '@example.com',
            'password' => 'P4ssW0r4 Us3r P0ST * ñ?¿ áËì·' . $rand_num,
            'enabled' => mt_rand(0, 2),
            'isAdmin' => mt_rand(0, 2)
        ];

        // 201
        self::$_clientPostUser->request(
            Request::METHOD_POST, self::RUTA_APIS,
            [], [], [], json_encode($p_data)
        );

        $response = self::$_clientPostUser->getResponse();
        self::$user = json_decode($response->getContent(), true);
    }

    /**
     * This method is called after the last test of this test class is run.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    /**
     * Test OPTIONS /results[/resultId] 200 Ok
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiResultController::optionsResultAction()
     */
    public function testOptionsResultAction200()
    {
        self::$_clientOptions->request(Request::METHOD_OPTIONS, self::RUTA_API);
        $response = self::$_clientOptions->getResponse();

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertNotEmpty($response->headers->get('Allow'));

        self::$_clientOptions->request(
            Request::METHOD_OPTIONS,
            self::RUTA_API . '/' . mt_rand(0, 1000000)
        );

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertNotEmpty($response->headers->get('Allow'));
    }

    /**
     * Test POST /results 201 Created
     *
     *
     * @covers \AppBundle\Controller\ApiResultController::postResultAction()
     */
    public function testPostResultAction201()
    {
        $rand_num = mt_rand(0, 1000000);

        $p_data = [
            'users_id' => self::$user['user']['id'],
            'result' => $rand_num
        ];

        // 201
        self::$_clientPost201->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );

        $responses = self::$_clientPost201->getResponse();
        self::assertEquals(Response::HTTP_CREATED, $responses->getStatusCode());
        self::assertTrue($responses->isSuccessful());
        self::assertJson($responses->getContent());
        $result = json_decode($responses->getContent(), true);
        return $result;
    }

    /**
     * Test POST /results 422 Unprocessable Entity
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiResultController::postResultAction()
     */
    public function testPostResultAction422()
    {
        $rand_num = mt_rand(0, 1000000);

        $p_datas = [
            'users_id' => self::$user['user']['id'],
            // 'result' => $rand_num
        ];

        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_datas)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $response->getStatusCode()
        );
        $r_body = (string)$response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[422],
            $r_data['message']
        );
    }

    /**
     * Test GET /results 200 Ok
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiResultController::cgetResultAction()
     */
    public function testCGetAction200()
    {
        self::$_clientGet->request(Request::METHOD_GET, self::RUTA_API);
        $response = self::$_clientGet->getResponse();
        self::assertTrue($response->isSuccessful());
        self::assertJson($response->getContent());
        $result = json_decode($response->getContent(), true);
        self::assertArrayHasKey('results', $result);
    }

    /**
     * Test GET /results/resultId 200 Ok
     *
     * @param array $result returned by testPostResultAction201()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::getResultAction()
     * @depends testPostResultAction201
     */
    public function testGetResultAction200(array $result)
    {
        self::$_clientGetId->request(
            Request::METHOD_GET,
            self::RUTA_API . '/' . $result['id']
        );
        $response = self::$_clientGetId->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertJson((string)$response->getContent());
    }

    /**
     * Test PUT /results/resultId 209 Content Returned
     *
     * @param array $result returned by testPostResultAction201()
     *
     * @return array modified results data
     *
     * @covers  \AppBundle\Controller\ApiResultController::putResultAction()
     * @depends testPostResultAction201
     */
    public function testPutResultAction209(array $result)
    {

        $rand_num = mt_rand(0, 1000000);

        $p_data = [
            'users_id' => self::$user['user']['id'],
            'result' => $rand_num
        ];

        self::$_clientPut->request(
            Request::METHOD_PUT, self::RUTA_API . '/' . $result['id'],
            [], [], [], json_encode($p_data)
        );
        $response = self::$_clientPut->getResponse();

        self::assertEquals(209, $response->getStatusCode());
        self::assertJson((string)$response->getContent());
        $user_aux = json_decode((string)$response->getContent(), true);
        self::assertEquals($result['id'], $user_aux['id']);
        self::assertEquals($p_data['result'], $user_aux['result']);

        return $user_aux;
    }

    /**
     * Test DELETE /results/resultId 204 No Content
     *
     * @param array $result returned by testPostResultAction201()
     *
     * @return int resultId
     *
     * @covers  \AppBundle\Controller\ApiResultController::deleteResultAction()
     * @depends testPostResultAction201
     * @depends testGetResultAction200
     */
    public function testDeleteResultAction204(array $result)
    {
        self::$_clientDelete->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/' . $result['id']
        );
        $response = self::$_clientDelete->getResponse();

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertEmpty((string)$response->getContent());

        return $result['id'];
    }

    /**
     * Test DELETE /results/resultId 404 Not Found
     *
     * @param int $resultId id. returned by testDeleteResultAction204
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::deleteResultAction()
     * @depends testDeleteResultAction204
     */
    public function testDeleteResultAction404(int $resultId)
    {
        self::$_clientDelete404->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/' . $resultId
        );
        $response = self::$_clientDelete404->getResponse();

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $r_body = (string)$response->getContent();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_NOT_FOUND, $r_data['code']);
        self::assertEquals(Response::$statusTexts[404], $r_data['message']);
    }

    /**
     * Test GET /results/resultId 404 Not Found
     *
     * @param int $resultId id. returned by testDeleteResultAction204()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::getResultAction()
     * @depends testDeleteResultAction204
     */
    public function testGetResultAction404(int $resultId)
    {
        self::$_clientGet404->request(Request::METHOD_GET, self::RUTA_API . '/' . $resultId);
        $response = self::$_clientGet404->getResponse();

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $r_body = (string)$response->getContent();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_NOT_FOUND, $r_data['code']);
        self::assertEquals(Response::$statusTexts[404], $r_data['message']);
    }

    /**
     * Test PUT /results/resultId 404 Not Found
     *
     * @param int $resultId id. returned by testDeleteResultAction204()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::putResultAction()
     * @depends testDeleteResultAction204
     */
    public function testPutResultAction404(int $resultId)
    {
        self::$_clientPut404->request(Request::METHOD_PUT, self::RUTA_API . '/' . $resultId);
        $response = self::$_clientPut404->getResponse();

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $r_body = (string)$response->getContent();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_NOT_FOUND, $r_data['code']);
        self::assertEquals(Response::$statusTexts[404], $r_data['message']);
    }
}
