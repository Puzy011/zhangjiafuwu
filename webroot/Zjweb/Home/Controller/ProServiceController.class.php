<?php
namespace Home\Controller;

class ProServiceController extends HomeController
{
    public function index()
    {
        $this->display("proService");
    }
    public function index_en()
    {
        $this->display("proService_en");
    }
}

?>