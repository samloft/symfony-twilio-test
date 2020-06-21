<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MessagesController extends AbstractController
{
    /**
     * Retrieve all messages ordered by newest first.
     */
    public function index(): Response
    {
        return $this->render('messages/index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show(): JsonResponse
    {
        $messages = $this->getDoctrine()->getRepository('App:Message')->show();

        $data = [];

        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'name' => $message->getUser()->getName(),
                'number' => $message->getMobileNumber(),
                'message' => $message->getMessage(),
                'status' => $message->getStatus(),
                'created' => $message->getCreatedAt(),
                'updated' => $message->getUpdatedAt(),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Allow a webhook to update the status for the given ID.
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function webhook($id): JsonResponse
    {
        $sid = $_REQUEST['AccountSid'];
        $status = $_REQUEST['MessageStatus'];

        if ($this->getParameter('TWILIO_SID') === $sid) {
            $entityManager = $this->getDoctrine()->getManager();

            $message = $this->getDoctrine()->getRepository('App:Message')->byId($id);

            if ($message) {
                $message->setStatus($status);

                $entityManager->flush();

                return new JsonResponse([
                    'completed' => true,
                ], Response::HTTP_OK);
            }

            return new JsonResponse([
                'completed' => false,
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'completed' => false,
        ], Response::HTTP_FORBIDDEN);
    }
}
