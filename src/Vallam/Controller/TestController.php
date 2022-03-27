<?php


namespace Vallam\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function index(Request $request)
    {
        return 'Here with Test Controller';
    }
}