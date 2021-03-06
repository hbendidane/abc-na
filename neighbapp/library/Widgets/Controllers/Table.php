<?php

class Widgets_Controllers_Table extends Widgets_Controllers_Abstract {

    /**
     * Construit les tableaux de reporting
     * 
     * exemple de data attendu :
     * 
     * data = array(
     *      0 =>
     *      array(
     *          'champ1' => '00 - DEMO - IPHONE1',
     *          'champ2' => 'CPe- Direct Sold',
     *          'champ3' => 'blabla',
     *      ),
     *      1 =>
     *      array(
     *          'champ1' => '00 - DEMO - IPHONE1',
     *          'champ2' => 'CPe- Direct Sold',
     *          'champ3' => 'blabla',                
     *      ),
     *      2 =>
     *      array(
     *          'champ1' => '00 - DEMO - IPHONE1',
     *          'champ2' => 'CPe- Direct Sold',
     *          'champ3' => 'blabla',                
     *      ),
     *  );
     * 
     * 
     * @return string
     */
    protected $css = array(
        '/assets/css/jquery.dataTables.css'
    );
    protected $js = array(
        '/assets/js/jquery.dataTables.min.js',
        '/assets/js/jquery.dataTables.extend.js'
    );

    public function render() {

        parent::render();

        $return = '';



        $active_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $active_action = ($active_action != 'index') ? $active_action : Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $active_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        if (empty($this->data)) {
            $return .= $this->view->partial('actions/add.phtml', array('active_controller' => $active_controller, 'active_action' => $active_action));
            $return .= $this->view->partial('message.phtml', array("message" => "NO DATA FOUND"));
            return $return;
        }
        // Traitement des champs
        if (isset($this->data[0]) && is_array($this->data[0])) {
            $fields = array();

            // @todo : ajouter les labels
            // traitement préalable de la table
            foreach ($this->data[0] as $key => $row) {
                if ((!(isset($this->_options['fields'])))) { // on prend tous les champs
                    $fields[] = $key;
                } else { // on selectionne seulement ceux choisi
                    if (in_array($key, $this->_options['fields'])) {
                        $fields[] = $key;
                    }
                }
            }
            if (isset($this->_options['crud'])) {
                $fields[] = 'actions';
            }
        } else {
            return;
        }

        // ajout des actions crud
        if (isset($this->_options['crud'])) {


            $this->_user_info = Zend_Auth::getInstance()->getStorage()->read();
            //$acl = $this->_user_info->acl;

            $user = Zend_Auth::getInstance()->getStorage()->read();
            $user_right = $user->type;
            foreach ($this->data as $key => $value) {

                $view_actions = '';

                // if($acl->isAllowed($user_right, $active_controller, 'update' )){
                if (in_array('update', $this->_options['action'])) {
                    $view_actions .= $this->view->partial('actions/update.phtml', array('active_controller' => $active_controller, 'active_action' => $active_action, 'id' => $value['id']));
                }
                // }
                // if($acl->isAllowed($user_right, $active_controller, 'delete' )){
                if (in_array('delete', $this->_options['action'])) {
                    $view_actions .= $this->view->partial('actions/delete.phtml', array('active_controller' => $active_controller, 'active_action' => $active_action, 'id' => $value['id']));
                }
                // }

                $this->data[$key]['actions'] = $view_actions;
            }

            // if($acl->isAllowed($user_right, $active_controller, 'add' )){
            if (in_array('add', $this->_options['action'])) {
                $return .= $this->view->partial('actions/add.phtml', array('active_action' => $active_action,'active_controller' => $active_controller));
            }
            // }
        }

        $return .= $this->view->partial('table.phtml', array('fields' => $fields, 'alldata' => $this->data, 'id_instance' => self::$id_instance, 'options' => $this->_options));

        //ici : logique pour vérifier que champs sont ok (correspondance) , etc....
        return $return;
    }

}
