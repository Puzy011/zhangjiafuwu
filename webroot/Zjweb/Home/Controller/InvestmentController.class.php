<?php
namespace Home\Controller;

class InvestmentController extends HomeController
{
    public function index()
    {
        $this->display("investment");
    }
    public function index_en()
    {
        $this->display("investment_en");
    }
}

?>