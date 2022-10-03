<?php
namespace User\Form;

use Components\Form\AbstractBaseForm;
use Laminas\Form\Element\Text;
use Components\Form\Element\DatabaseSelect;
use Laminas\Db\Adapter\AdapterAwareTrait;

class RoleForm extends AbstractBaseForm
{
    use AdapterAwareTrait;
    
    public function init()
    {
        parent::init();
        
        $this->add([
            'name' => 'ROLENAME',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'ROLENAME',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Role Name',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'PARENT',
            'type' => DatabaseSelect::class,
            'attributes' => [
                'class' => 'form-select',
                'id' => 'PARENT',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Parent Role',
                'database_adapter' => $this->adapter,
                'database_table' => 'roles',
                'database_id_column' => 'ROLENAME',
                'database_value_columns' => [
                    'ROLE' => 'ROLENAME',
                    'PRIORITY',
                ],
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'PRIORITY',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'PRIORITY',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Priority',
            ],
        ],['priority' => 100]);
    }
}