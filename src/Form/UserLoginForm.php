<?php 
namespace User\Form;

use Laminas\Form\Form;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Hidden;

class UserLoginForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'REFERRING_URL',
            'type' => Hidden::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'REFERRING_URL',
            ],
        ]);
        
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
        ]);
        
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
        ]);
        
        $this->add(new Csrf('SECURITY'));
        
        $this->add([
            'name' => 'SUBMIT',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-primary',
                'id' => 'SUBMIT',
            ],
        ]);
    }
}