<?php
namespace User\Form;

use Components\Form\AbstractBaseForm;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Text;

class UserRegisterForm extends AbstractBaseForm
{
    public function init()
    {
        parent::init();
        
        $this->add([
            'name' => 'USERNAME',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'USERNAME',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Username',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'FNAME',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'FNAME',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'First Name',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'LNAME',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'LNAME',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Last Name',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'EMAIL',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'EMAIL',
                'placeholder' => '',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Email Address',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'PASSWORD',
            'type' => Password::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'PASSWORD',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Password',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'CONFIRM_PASSWORD',
            'type' => Password::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'CONFIRM_PASSWORD',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Confirm Password',
            ],
        ],['priority' => 100]);
    }
}
