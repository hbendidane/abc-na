<?php

/**
 * Module Backoffice Numbate
 * Users controller 
 * @author romain.causse@surikate.com  
 */


class Backoffice_UserController extends Class_Controller_BackofficeAction
{
    /**
     * API user
     */
    protected $_userApi;


    public function preDispatch() {
        parent::preDispatch();
        
        $this->_userApi = new Api_User();
        $this->view->sidebar = array('display' => 'horizontal');
    }
    
    
    /**
     * Show all user
     */
    public function indexAction()
    {   
        $this->view->headTitle(_("User list"));
        
        // champs a afficher
        $fields = array(
            'id',
            'email',
            'lastname',
            'gender',
            'firstname'
        );

        $this->view->table = $this->view->widget('Table', $this->_userApi->GetAllUsers(), array('crud' => true, 'fields' => $fields,"action" => array("add","update","delete")));
    }
        
    /**
     * add a user
     */
    public function useraddAction()
    {   
        $this->view->headTitle(_("Add user"));
        
        $form = new Class_Form_Bootstrap_User();
        $this->view->form = $form;

        $request = $this->getRequest();
        if($request->isPost()){
            if( $form->isValid($request->getPost())){
                    
                 $data = $request->getPost('data');
                 
                 $this->_userApi->CreateUaccount($data);
                 
                 $this->_helper->redirector('index', 'user', 'Backoffice');
            }
            
        }
    }
    
    /**
     * update a user
     */
    public function userupdateAction()
    {   
        $user_data = $this->_userApi->GetUserById($this->_request->getParam('id'));
                
        $this->view->headTitle(_("User : ") . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $form = new Class_Form_Bootstrap_User();
        $data = array('data' => $user_data);
        $form->populate($data);
        
        $form->getElement('password')->setOptions(array('required' => false));
        
        $this->view->form = $form;

        $request = $this->getRequest();
        if($request->isPost()){
            if( $form->isValid($request->getPost())){
                    
                 $data = $request->getPost('data');

                 $this->_userApi->UpdateUaccount($data,$this->_request->getParam('id'));
                 
                 $this->_helper->redirector('index', 'user', 'Backoffice');
            }
            
        }
        
        
    }
    
    /**
     * delete a user
     */
    public function userdeleteAction()
    {   
        
        $id = $this->_request->getParam('id');
        $this->view->id = $id;
        if($this->_request->isPost()){
            if($this->_request->getParam('valide')){
                $this->_userApi->DeleteUser($this->_request->getParam('id'));
                $this->_helper->redirector('index', 'user', 'Backoffice');
            }else{
                $this->_helper->redirector('index', 'user', 'Backoffice');
            }
        }
    }
    
    /**
     * Disconnect and clear session
     */
    public function disconnectAction(){
        Zend_Auth::getInstance()->clearIdentity();
        //redirects
        $this->_helper->redirector('index', 'login', 'Backoffice');
    }
}