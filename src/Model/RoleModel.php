<?php 
namespace User\Model;

use Components\Model\AbstractBaseModel;

class RoleModel extends AbstractBaseModel
{
    public $ROLENAME;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        
        $this->setTableName('roles');
    }
}