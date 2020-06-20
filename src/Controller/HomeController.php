<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\SmsFormType;
use App\Producer\SmsProducer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class HomeController extends AbstractController
{
    /**
     * Display the home page with form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Producer\SmsProducer $sms
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, SmsProducer $sms): Response
    {
        $form = $this->createForm(SmsFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = [
                'mobile_number' => $form->get('mobile_number')->getData(),
                'message' => $form->get('message')->getData(),
            ];

            $message = $this->store($data);

            $sms->publish(json_encode($message));

            $this->addFlash('success', 'Your SMS message request has been sent');

            unset($form);
            $form = $this->createForm(SmsFormType::class);
        }

        return $this->render('home/index.html.twig', [
            'smsForm' => $form->createView(),
            'errors' => $form->getErrors(true),
        ]);
    }

    /**
     * Persist the message in the database.
     *
     * @param $data
     *
     * @return \App\Entity\Message
     */
    public function store($data): Message
    {
        $entityManager = $this->getDoctrine()->getManager();

        $message = new Message();
        $message->setUser($this->get('security.token_storage')->getToken()->getUser());
        $message->setMobileNumber($data['mobile_number']);
        $message->setMessage($data['message']);
        $message->setStatus('queued');

        $entityManager->persist($message);

        $entityManager->flush();

        return $message;
    }
}