<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sqs extends CI_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('Sqs_model');
    }

    function index(){
        redirect(base_url('sqs/create/'));
    }

    function create($type = null,$message = null){
        $result = null;
        if( $type && $message ){
            $result['message'] = $this->message($type,$message);
        }
        $this->load->view('queues_view',$result);
    }

    public function createMessage()
    {
        $message_url = null;
        if( $this->input->post('name') ) {
            $post = $this->input->post('name');
            $send = $this->Sqs_model->sendMessage($post);
            if($send == true){
                $message_url = $this->message_url('success','Data has been sent to queue');
            }else{
                $message_url = $this->message_url('error',json_encode($send));
            }
        }
        redirect(base_url('sqs/create'.$message_url));
    }

    public function queue_list()
    {
        $receive = $this->Sqs_model->getList();
        $result["allreceive"] = $receive;
        $this->load->view('queue_list',$result);
    }

    private function message_url($type,$message){
        return '/'.$type.'/'.base64_encode($message);
    }

    private function message($type,$message){
        return '<div class="alert alert-'.$type.' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>'.ucwords($type).'!</strong> '.base64_decode($message).'</div>';
    }
}