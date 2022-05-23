<?php

namespace App\Controller\Api;

use App\Models\CustomJsonError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Add error method for Api.
 */
class JsonController extends AbstractController
{
    /** code 404
     *
     * @param ConstraintViolationListInterface $errors
     * @return JsonResponse
     */
    public function json404(string $message): JsonResponse
    {
        // our object custom give a error message .
        $error = new CustomJsonError();
        $error->errorCode = Response::HTTP_NOT_FOUND;
        $error->message = $message;

        return $this->json(
            $error,
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * code 422
     *
     * @param ConstraintViolationListInterface $errors
     * @return JsonResponse
     */
    public function json422($errors): JsonResponse
    {
        // our object custom give a error message .
        $error = new CustomJsonError();
        $error->errorCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        $error->setErrorValidation($errors);
        $error->message = "Erreurs sur la validation de l'objet";

        // I send a JsonResponse
        // so that it is compatible with all API methods
        return $this->json(
            $error,
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * returns an HTTP_OK response with data and serialization groups
     *
     * @param mixed $data data to serialize
     * @param array $groups serialization groups : ["group1", "group2"]
     * @return JsonResponse
     */
    public function json200($data, array $groups = []): JsonResponse
    {
        return $this->json(
            // data
            $data,
            // http code
            Response::HTTP_OK,
            // no additional header
            [],
            // we specify the serialization groups
            [
                "groups" => $groups
            ]
        );
    }

    /**
     * returns an HTTP_CREATED response with data and serialization groups
     *
     * @param mixed $data data to serialize
     * @param array $groups serialization groups : ["group1", "group2"]
     * @return JsonResponse
     */
    public function json201($data, array $groups = []): JsonResponse
    {
        return $this->jsonByCode(
            Response::HTTP_CREATED,
            $data,
            $groups
        );
    }

    /**
     * generic version for all codes
     *
     * @param integer $httpCode
     * @param mixed $data
     * @param array $groups
     * @return JsonResponse
     */
    private function jsonByCode(int $httpCode, $data, array $groups = []): JsonResponse
    {
        return $this->json(
            $data,
            $httpCode,
            // no additional header
            [],
            // we specify the serialization groups
            [
                "groups" => $groups
            ]
        );
    }
}