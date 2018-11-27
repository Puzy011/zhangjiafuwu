<?php
namespace Home\Controller;

class ZJstoreController extends HomeController
{
    public function index()
    {
        $this->display("ZJstore");
    }
    public function index_en()
    {
        $this->display("ZJstore_en");
    }
}

?>