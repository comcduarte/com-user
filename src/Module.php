<?php 
namespace User;

class Module
{
    const TITLE = "User Module";
    const VERSION = "v1.0.5";
    
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}