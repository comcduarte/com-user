<?php
namespace User\Form;

use Components\Form\AbstractBaseForm;
use Components\Form\Element\DatabaseSelect;
use Laminas\Form\Element\Hidden;
use Laminas\Db\Adapter\AdapterAwareTrait;

class UserRolesForm extends AbstractBaseForm
{
    use AdapterAwareTrait;
    
    public function init()
    {
        parent::init();
        
        $this->add([
            'name' => 'USER',
            'type' => Hidden::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'USER',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'User',
            ],
        ]);
        
        $this->add([
            'name' => 'ROLE',
            'type' => DatabaseSelect::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'ROLE',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Role',
                'database_adapter' => $this->adapter,
                'database_table' => 'roles',
                'database_id_column' => 'UUID',
                'database_value_columns' => [
                    'ROLENAME'
                ],
            ],
        ]);
        
        $this->remove('STATUS');
    }
}