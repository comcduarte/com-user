<?php
namespace User\Form;

use Components\Form\AbstractBaseForm;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Text;

class UserForm extends AbstractBaseForm
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
            'name' => 'ADDR1',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'ADDR1',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Address',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'ADDR2',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'ADDR2',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Address 2',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'CITY',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'CITY',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'City',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'STATE',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'STATE',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'State',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'ZIP',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'ZIP',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Zip Code',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'PHONE',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'PHONE',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Phone',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'EMAIL',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'EMAIL',
                'placeholder' => '',
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