<?php

class MY_Controller extends CI_Controller
{
    # Parameter reference
    public $params = array();

    public $cookies = array();

    public function __construct()
    {

        parent::__construct();
        # Parameter
        $this->params = $this->getParams();
        $this->cookies = $this->getCookies();
        $this->config->load('page_constants');
    }

    private function getParams()
    {

        $aParams = array_merge($this->doGet(), $this->doPost());

        //$this->sql_injection_filter($aParams);

        return $aParams;
    }


    private function getCookies()
    {

        return $this->doCookie();
    }


    private function doGet()
    {
        $aGetData = $this->input->get(NULL, TRUE);
        return (empty($aGetData)) ? array() : $aGetData;
    }

    private function doPost()
    {
        $aPostData = $this->input->post(NULL, TRUE);
        return (empty($aPostData)) ? array() : $aPostData;
    }

    private function doCookie()
    {
        $aCookieData = $this->input->cookie(NULL, TRUE);

        return (empty($aCookieData)) ? array() : $aCookieData;
    }

    public function js($file, $v = '')
    {
        if (is_array($file)) {
            foreach ($file as $iKey => $sValue) {
                $this->optimizer->setJs($sValue, $v);
            }
        } else {
            $this->optimizer->setJs($file, $v);
        }
    }

    public function externaljs($file)
    {
        if (is_array($file)) {
            foreach ($file as $iKey => $sValue) {
                $this->optimizer->setExternalJs($sValue);
            }
        } else {
            $this->optimizer->setExternalJs($file);
        }
    }

    public function css($file, $v = '')
    {
        if (is_array($file)) {
            foreach ($file as $iKey => $sValue) {
                $this->optimizer->setCss($sValue, $v);
            }
        } else {
            $this->optimizer->setCss($file, $v);
        }
    }

    /**
     *  변수 셋팅
     */
    public function setVars($arr = array())
    {
        foreach ($arr as $val) {
            $aVars;
        }

        $this->load->vars($aVars);
    }

    /**
     *  공통 전역 변수 셋팅
     */
    public function setCommonVars()
    {
        $aVars = array();

        $aVars['test'] = array("test1" => "test1");

        $this->load->vars($aVars);
    }


    // NOTE: render
    public function render($title_key, $view_key, $data = []) {
        $view_names = $this->config->item('view_names');
        $page_titles = $this->config->item('page_titles');


        //view,title keyword path -> \application\config\page_constants.php
        if (!isset($view_names[$view_key]) || !isset($page_titles[$title_key])) {
            show_error("잘못된 view_key 또는 title_key 입니다.");
            return;
        }

        $response = [
            'page_title' => $page_titles[$title_key],
            'view_name' => $view_names[$view_key],
            
        ];

        if (is_array($data)) {  
        // $response 배열에 $data 배열의 키/값 병합
        $response = array_merge($response, $data);
        }


        $this->load->view('layout', $response);
    }

    //NOTE: init Response data
    public function json_response($success, $data , $message){
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => $success,
                'data' => $data,
                'message' => $message
            ]));



    }

    
    //NOTE: alter 호출뒤 랜더링되는 매서드 추가
    protected function redirect_with_alert($message = null ,$redirect_url=null) {
        
        if(!is_null( $message)) {
        $this->session->set_flashdata('alert_message', $message);
        }
        if(is_null($redirect_url)) {
            $redirect_url = $this->agent->referrer() ?? site_url();
            return redirect($redirect_url);
        }else{

             //view keyword path -> \application\config\page_constants.php
            $view_names = $this->config->item('view_names');
            if(!isset($view_names[$redirect_url])){
                show_error("잘못된 redirect_url 입니다.");
                return;
            }

            return redirect($view_names[$redirect_url]);

        }
        
    }
}
