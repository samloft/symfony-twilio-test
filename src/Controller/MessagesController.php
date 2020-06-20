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
        $messages = $this->getDoctrine()->getRepository('App:Message')->show();

        return $this->render('messages/index.html.twig', [
            'messages' => $messages,
        ]);
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
