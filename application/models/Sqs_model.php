<?php

class Sqs_model extends CI_Model {
    /**
     * @var \Aws\Sqs\SqsClient
     */
    protected $sqsClient;

    /**
     * @var string
     */
    protected $queueName = 'your_queue_name';

    /**
     * Sqs constructor.
     */
    public function __construct()
    {
        $this->load->helper("path");
        require_once set_realpath('vendor/autoload.php');

        $this->sqsClient = Aws\Sqs\SqsClient::factory(array(
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => array(
                'key' => 'your_key',
                'secret' => 'your_secret_id',
            )
        ));
    }

    /**
     * @param $arguments
     * @return \Guzzle\Service\Resource\Model
     */
    public function sendMessage($arguments)
    {
        // Get the queue URL from the queue name.
        $result = $this->sqsClient->getQueueUrl(array('QueueName' => $this->queueName));
        $queue_url = $result->get('QueueUrl');

        // Send the message
        $return = $this->sqsClient->sendMessage(array(
            'QueueUrl' => $queue_url,
            'MessageBody' => json_encode($arguments)
        ));
        return $return;
    }

    /**
     * @return array
     */
    public function getList()
    {
        $body = array();
        // Get the queue URL from the queue name.
        $result = $this->sqsClient->getQueueUrl(array('QueueName' => $this->queueName));
        $queue_url = $result->get('QueueUrl');
        /*var_dump($queue_url);
        die();*/
        $queueAttributes = $this->sqsClient->getQueueAttributes(array(
            'QueueUrl' => $queue_url,
            'AttributeNames' => array('ApproximateNumberOfMessages'),
        ));

        $qsize = $queueAttributes->get('Attributes');
        $qsize = $qsize['ApproximateNumberOfMessages'];

        for ($j = 0; $j < $qsize; ++$j) {
            // Receive a message from the queue using the AWS SDK for PHP function, receive_message.
            $result = $this->sqsClient->receiveMessage(array(
                'QueueUrl' => $queue_url,
            ));
            $message = $result->get('Messages');
            if( isset($message['0']['Body']) && !empty($message['0']['Body']) ){
                $body[] = $message['0']['Body'];
            }
        }
        return $body;
    }
}