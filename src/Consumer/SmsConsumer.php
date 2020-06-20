<?php

namespace App\Consumer;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class SmsConsumer implements ConsumerInterface
{
    private $em;

    private $params;

    /**
     * SmsConsumer constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $params
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->params = $params;
    }

    public function execute(AMQPMessage $msg)
    {
        $id = json_decode($msg->body, true)['id'] ?? null;

        $sms_message = $this->getMessage($id);

        if ($sms_message) {
            try {
                $client = new Client($this->params->get('TWILIO_SID'), $this->params->get('TWILIO_AUTH_TOKEN'));

                $response = $client->messages->create($sms_message->getMobileNumber(), [
                    'from' => $this->params->get('TWILIO_NUMBER'),
                    'body' => $sms_message->getMessage(),
                    'statusCallback' => $this->params->get('TWILIO_CALLBACK').$sms_message->getId(),
                ]);

                $this->updateStatus($sms_message, $response->status);
            } catch (TwilioException $e) {
                $this->updateStatus($sms_message, 'failed');
            }
        }
    }

    /**
     * @param $id
     *
     * @return object|null
     */
    public function getMessage($id)
    {
        if ($id) {
            return $this->em->getRepository(Message::class)->findOneBy(['id' => $id]);
        }

        return null;
    }

    /**
     * Update the status for the given message object.
     *
     * @param \App\Entity\Message $message
     * @param string $status
     */
    public function updateStatus(Message $message, string $status): void
    {
        $message->setStatus($status);

        $this->em->flush();
    }
}