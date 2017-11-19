<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Result;
use AppBundle\Entity\User;
use AppBundle\Entity\Message;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiResultController
 *
 * @package AppBundle\Controller
 *
 * @Route(ApiResultController::RUTA_API)
 */
class ApiResultController extends Controller
{
    const RUTA_API = 'api/v1/results';

    /**
     * Summary: Returns all results
     *
     * @return JsonResponse
     * @Route("", name="miw_cget_results")
     * @Method(Request::METHOD_GET)
     */
    public function cgetResultAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Result');
        $results = $repo->findAll();

        return empty($results)
            ? new JsonResponse(
                new Message(
                    Response::HTTP_NOT_FOUND,
                    Response::$statusTexts[404]
                ),
                Response::HTTP_NOT_FOUND
            )
            : new JsonResponse(['results' => $results]);
    }

    /**
     * Summary: Returns a user based on a single ID
     * Notes: Returns the user identified by &#x60;userId&#x60;.
     *
     * @param int $resultId Result id
     *
     * @return JsonResponse
     *
     * @Route("/{resultId}", name="miw_get_result", requirements={"resultId": "\d+"})
     * @Method(Request::METHOD_GET)
     */
    public function getResultAction(int $resultId)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Result');
        $result = $repo->findOneBy(['id' => $resultId]);

        return empty($result)
            ? new JsonResponse(
                new Message(Response::HTTP_NOT_FOUND, Response::$statusTexts[404]),
                Response::HTTP_NOT_FOUND
            )
            : new JsonResponse($result);
    }

    /**
     * Creates a new result entity.
     *
     * Returns the result identified by &#x60;userId&#x60;.
     *
     * @param Request $request request
     * @return JsonResponse
     *
     * @Method(Request::METHOD_POST)
     * @Route("", name="miw_post_results")
     *
     */
    public function postResultAction(Request $request)
    {
        $body = $request->getContent(false);
        $postData = json_decode($body, true);

        if (!isset($postData['users_id'], $postData['result'])) { // 422 - Unprocessable Entity Faltan datos

            return new JsonResponse(
                new Message(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[422]
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /* @var User $user */
        $entityManager = $this->getDoctrine()->getManager();
        $entityRepository = $entityManager->getRepository(User::__CLASS__);
        $user = $entityRepository->findOneBy(array(User::ID => $postData['users_id']));

        $result = new Result(
            $postData['result'],
            $user,
            new \DateTime()
        );
        $entityManager->persist($result);
        $entityManager->flush();

        return new JsonResponse($result, Response::HTTP_CREATED);
    }


    /**
     * Summary: Updates a result
     * Notes: Updates the result identified by &#x60;userId&#x60;.
     *
     * @param Request $request request
     * @param int     $resultId  Result id
     *
     * @return JsonResponse
     *
     * @Route("/{resultId}", name="miw_put_results", requirements={"resultId": "\d+"})
     * @Method(Request::METHOD_PUT)
     */
    public function putResultAction(Request $request, int $resultId)
    {
        $body = $request->getContent(false);
        $postData = json_decode($body, true);

        $entityManager = $this->getDoctrine()->getManager();
        $result = $entityManager
            ->getRepository(Result::class)
            ->findOneBy(['id' => $resultId]);

        if (null == $result) {    // 404 - Not Found
            return new JsonResponse(
                new Message(Response::HTTP_NOT_FOUND, Response::$statusTexts[404]),
                Response::HTTP_NOT_FOUND
            );
        }
        $entityRepository = $entityManager->getRepository(User::__CLASS__);
        $user = $entityRepository->findOneBy(array(User::ID => $postData['users_id']));

        if (null == $user) {    // 404 - Not Found
            return new JsonResponse(
                new Message(Response::HTTP_NOT_FOUND,
                   'Id user no encontrado'
                ),
                Response::HTTP_NOT_FOUND
            );
        }

        if (!isset($postData['users_id'], $postData['result'])) { // 422 - Unprocessable Entity Faltan datos

            return new JsonResponse(
                new Message(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[422]
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (isset($postData['users_id'])) {
            $result->setUser($user);
        }

        if (isset($postData['result'])) {
            $result->setResult($postData['result']);
        }

        $result->setTime(new \DateTime());

        $entityManager->merge($result);
        $entityManager->flush();


        return new JsonResponse($result, 209);
    }

    /**
     * Summary: Deletes a result entity.
     * Notes: Returns the user identified by &#x60;userId&#x60;.
     *
     * @param int $resultId Result id
     *
     * @return Response
     *
     * @Route("/{resultId}", name="miw_delete_result", requirements={"resultId": "\d+"})
     * @Method(Request::METHOD_DELETE)
     */
    public function deleteResultAction(int $resultId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $result = $entityManager
            ->getRepository(Result::class)
            ->findOneBy(['id' => $resultId]);

        if (empty($result)) {   // 404 - Not Found
            return new JsonResponse(
                new Message(
                    Response::HTTP_NOT_FOUND,
                    Response::$statusTexts[404]
                ),
                Response::HTTP_NOT_FOUND
            );
        }

        $entityManager->remove($result);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Summary: Provides the list of HTTP supported methods
     * Notes: Return a &#x60;Allow&#x60; header with a list of HTTP supported methods.
     *
     * @param int $resultId Result id
     *
     * @return JsonResponse
     *
     * @Route(
     *     "/{resultId}",
     *     name = "miw_options_results",
     *     defaults = {"resultId" = 0},
     *     requirements = {"resultId": "\d+"}
     *     )
     * @Method(Request::METHOD_OPTIONS)
     */
    public function optionsResultAction(int $resultId)
    {
        $methods = ($resultId)
            ? ['GET', 'PUT', 'DELETE']
            : ['GET', 'POST'];

        return new JsonResponse(
            null,
            Response::HTTP_OK,
            ['Allow' => implode(', ', $methods)]
        );
    }
}
