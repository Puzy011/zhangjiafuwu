<?php
namespace Home\Controller;

class AboutUsController extends HomeController
{
    public function index()
    {
        $this->display("aboutUs");
    }
    public function job()
    {
        $this->display("job");
    }
    public function index_en()
    {
        $this->display("aboutUs_en");
    }
    public function job_en()
    {
        $this->display("job_en");
    }
}

?>