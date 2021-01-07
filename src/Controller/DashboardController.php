<?php
namespace User\Controller;

use Files\Model\FilesModel;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Model\UserModel;
use Application\Model\Entity\UserEntity;

class DashboardController extends AbstractActionController
{
    use AdapterAwareTrait;
    
    /**
     * @var FilesModel
     */
    private $files;
    public $emp_adapter;
    
    
    public function indexAction()
    {
        $view = new ViewModel();
        
        /**
         * @var $user UserModel
         */
        $user = $this->currentUser();
        $user_entity = new UserEntity($this->adapter);
        $user_entity->employee->setDbAdapter($this->emp_adapter);
        $user_entity->getUser($user->UUID);
        
        $w2 = [];
        $f1095c = [];
        $files = $this->files->FindFiles($user_entity->employee->UUID);
        
        /**
         * Parse array to only include required fields for specific files.
         * Can utilize ACL field to sort.
         */
        foreach ($files as $file) {
            if (substr($file['NAME'], 0, 2) === "W2") {
                $w2[] = [
                    'UUID' => $file['UUID'],
                    'Filename' => $file['NAME'],
                    'Uploaded' => $file['DATE_CREATED']
                ];
            }
            
            if (substr($file['NAME'], 0, 5) === "1095C") {
                $f1095c[] = [
                    'UUID' => $file['UUID'],
                    'Filename' => $file['NAME'],
                    'Uploaded' => $file['DATE_CREATED']
                ];
            }
        }
        
        
        $view->setVariable('w2', $w2);
        $view->setVariable('f1095c', $f1095c);
        $view->setVariable('employee', $user_entity->employee->UUID);
        
        
        return $view;
    }
    
    public function getFiles()
    {
        return $this->files;
    }
    
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }
}