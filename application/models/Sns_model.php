<?php

class Sns_model extends CI_Model {

    /**
     * @var \Aws\Sns\SnsClient
     */
    protected $snsClient;

    /**
     * @var string
     */

    protected $topicArn = "your topicArn";

    /**
     * Sns constructor.
     */
    public function __construct()
    {
        $this->load->helper("path");
        require_once set_realpath('vendor/autoload.php');

        $this->snsClient = Aws\Sns\SnsClient::factory(array(
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => array(
                'key' => 'your_key',
                'secret' => 'your_secret_id',
            )
        ));
    }

    public function createTopic()
    {
        $result = $this->snsClient->createTopic(array(
            // Name is required
            'Name' => 'your_queue_name',
        ));

        return $result;
    }

    public function subscribe($endpoint = '')
    {
        $result = $this->snsClient->subscribe([
            'Endpoint' => $endpoint,
            'Protocol' => 'email', // REQUIRED
            'TopicArn' => $this->topicArn, // REQUIRED
        ]);

        return $result;
    }

    public function listSubscriptions()
    {
        $result = $this->snsClient->listSubscriptionsByTopic([
            'NextToken' => '',
            'TopicArn' => $this->topicArn, // REQUIRED
        ])->getAll();

        return $result;
    }

    public function pushNotification($subject, $message)
    {
        $result = $this->snsClient->publish([
            'Message' => $message,
            'Subject' => $subject,
            'TopicArn' => $this->topicArn,
        ]);

        return $result;
    }
}