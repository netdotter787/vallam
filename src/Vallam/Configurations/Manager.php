<?php
namespace Vallam\Configurations;

use Vallam\Engine\AbstractVallamSingleton;

class Manager extends AbstractVallamSingleton
{
    public function hoistManager()
    {
        echo "Manager is hoisted";
    }
}