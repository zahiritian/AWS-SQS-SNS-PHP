<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sns extends CI_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('Sns_model');
    }

    function index(){
        redirect(base_url('sns/createnotification/'));
    }

    public function createsubscription($type = null,$message = null){
        $result = null;
        if( $type && $message ){
            $result['message'] = $this->message($type,$message);
        }
        $this->load->view('sns_subscription_view',$result);
    }

    public function addsubscription()
    {
        if( $this->input->post('subscriber') ) {
            $message_url = null;
            $endpoint = $this->input->post('subscriber');
            $result = $this->Sns_model->subscribe($endpoint);
            if($result == true){
                $message_url = $this->message_url('success','Successfully');
            }else{
                $message_url = $this->message_url('error',json_encode($result));
            }
            redirect(base_url('sns/createsubscription'.$message_url));
        }
    }

    public function createnotification($type = null,$message = null){
        $result = null;
        if( $type && $message ){
            $result['message'] = $this->message($type,$message);
        }
        $this->load->view('sns_notification_view',$result);
    }

    public function sendnotification()
    {
        if( $this->input->post('message') ) {
            $message_url = null;
            $subject = 'SNS Notification';
            $message = $this->input->post('message');
            $result = $this->Sns_model->pushNotification($subject, $message);
            if($result == true){
                $message_url = $this->message_url('success','Notification has been sent.');
            }else{
                $message_url = $this->message_url('error',json_encode($result));
            }
        redirect(base_url('sns/createnotification'.$message_url));
        }
    }

    public function listallsubscription()
    {
        $subscriber = $this->Sns_model->listSubscriptions();
        $result["allsubscriber"] = $subscriber;
        $this->load->view('sns_subscriptions_list',$result);
    }

    private function message_url($type,$message){
        return '/'.$type.'/'.base64_encode($message);
    }

    private function message($type,$message){
        return '<div class="alert alert-'.$type.' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>'.ucwords($type).'!</strong> '.base64_decode($message).'</div>';
    }
}