<?php
namespace User\Form;

use Components\Form\AbstractBaseForm;
use Laminas\Form\Element\Text;

class RoleForm extends AbstractBaseForm
{
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
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'PARENT',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Parent Role',
            ],
        ],['priority' => 100]);
    }
}