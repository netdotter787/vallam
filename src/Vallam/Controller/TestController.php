<?php


namespace Vallam\Controller;


use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function index()
    {
        return new Response('Nope, this is not a leap year.');
    }
}