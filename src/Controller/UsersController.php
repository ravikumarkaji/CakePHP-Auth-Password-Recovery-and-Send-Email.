<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Utility\Security;
use Cake\Mailer\Email;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    /**
     * Parent beforeFilter()
     */
    

    /**
     * Custom login function to allow for cookies.
     * @return
     */
    function login() {
        // $this->viewBuilder()->setLayout('default-signup-login');
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect(['controller'=>'Users','action'=>'index']);
            } else {
                $this->Flash->error(__('Username or password is incorrect'));
            }
        }
    }

    public function logout()
    {
        $this->Flash->success('You are now logged out.');
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Allow a user to request a password reset.
     * @return
     */
    function forgotPassword() {
        // $this->viewBuilder()->setLayout('default-signup-login');
        $data = $this->request->getData();
        if (!empty($data)) {
            $user = $this->Users->newEntity();
            $userD = $this->Users->findByEmail($data['email']);
            foreach ($userD as $key => $ud) {
            }
            $user = $ud;
            //print_r(json_encode($user)); die();
            if (empty($user)) {
                //echo "Empty";die();
                $this->Flash->error('Sorry, the username entered was not found.');
                $this->redirect(['controller'=>'Users','action'=>'forgotPassword']);

            } else {
                //echo "not Empty";die();
                $user = $this->__generatePasswordToken($user);
                if ($this->Users->save($user) && $this->__sendForgotPasswordEmail($user)) {
                    $this->Flash->success('Password reset instructions have been sent to your email id : '.$user->email.',
                        You have 24 hours to complete the request.');
                    $this->redirect(['controller'=>'Users','action'=>'login']);
                }else{
                    $this->Flash->error('Sorry, something is going on, Please try later.');
                    $this->redirect(['controller'=>'Users','action'=>'forgotPassword']);
                }
            }
        }
    }

    /**
     * Allow user to reset password if $token is valid.
     * @return
     */
    function resetPassword($reset_password_token = null) {
        $data = $this->request->getData();
        if (empty($data)) {
            $userD = $this->Users->findByResetPasswordToken($reset_password_token);
            foreach ($userD as $key => $ud) {
            }
            $data = $ud;

            if (!empty($data['reset_password_token']) && !empty($data['token_created_at']) &&
            $this->__validToken($data['token_created_at'])) {
                $data['id'] = null;
                $this->session->write('token',$reset_password_token);
                $this->set(compact('reset_password_token'));
            } else {
                $this->Flash->error('The password reset request has either expired or is invalid.');
               $this->redirect(['controller'=>'Users','action'=>'login']);
            }
        } else {
            if ($data['reset_password_token'] != $this->session->read('token')) {
                $this->Flash->error('The password reset request has either expired or is invalid.');
                $this->redirect(['controller'=>'Users','action'=>'login']);
            }

            $userD = $this->Users->findByResetPasswordToken($data['reset_password_token']);
            foreach ($userD as $key => $ud) {
            }
            $user = $this->Users->newEntity();
            $user = $ud;
            $user->password = $data['new_password'];
                $user->reset_password_token = $user->token_created_at = null;
                if ($this->Users->save($user) && $this->__sendPasswordChangedEmail($user)) {
                    $this->session->delete('token');
                    $this->Flash->success('Your password was changed successfully. Please login to continue.');
                    $this->redirect(['controller'=>'Users','action'=>'login']);
                }
            // }
        }
    }

    /**
     * Generate a unique hash / token.
     * @param Object User
     * @return Object User
     */
    function __generatePasswordToken($user) {
        if (empty($user)) {
            return null;
        }

        // Generate a random string 100 chars in length.
        $token = "";
        for ($i = 0; $i < 100; $i++) {
            $d = rand(1, 100000) % 2;
            $d ? $token .= chr(rand(33,79)) : $token .= chr(rand(80,126));
        }

        (rand(1, 100000) % 2) ? $token = strrev($token) : $token = $token;

        // Generate hash of random string
        $hash = Security::hash($token, 'sha256', true);
        for ($i = 0; $i < 20; $i++) {
            $hash = Security::hash($hash, 'sha256', true);
        }

        $user->reset_password_token = $hash;
        $user->token_created_at = date('Y-m-d H:i:s');

        return $user;
    }

    /**
     * Validate token created at time.
     * @param String $token_created_at
     * @return Boolean
     */
    function __validToken($token_created_at) {
        $expired = strtotime($token_created_at) + 86400;
        $time = strtotime("now");
        if ($time < $expired) {
            return true;
        }
        return false;
    }

    /**
     * Sends password reset email to user's email address.
     * @param $id
     * @return
     */
    function __sendForgotPasswordEmail($user) {
        if (!empty($user)) {
            $email = new Email('default');
            $email
                ->setTemplate('reset_password_request')
                ->setEmailFormat('html')
                ->setViewVars(['user'=>$user])
                ->setFrom(['info@ravikumarkazi.com' => 'www.ravikumarkazi.com'])
                ->setTo($user->email)
                ->setSubject('Password Reset Request - DO NOT REPLY')
                ->send();

            return true;
        }
        return false;
    }

    /**
     * Notifies user their password has changed.
     * @param $id
     * @return
     */
    function __sendPasswordChangedEmail($user) {
        if (!empty($user)) {
            $email = new Email('default');
            $email
                ->setTemplate('password_reset_success')
                ->setEmailFormat('html')
                ->setViewVars(['user'=>$user])
                ->setFrom(['info@ravikumarkazi.com' => 'www.ravikumarkazi.com'])
                ->setTo($user->email)
                ->setSubject('Password Changed - DO NOT REPLY')
                ->send();

            return true;
        }
        return false;
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->viewBuilder()->setLayout('default');
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->setLayout('default');
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // Set the layout.
       // $this->viewBuilder()->setLayout('default-signup-login');
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['controller'=>'Users','action' => 'login']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }



    public function contactUs()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if ($this->__sendQueryEmail($data)==true) {
                $this->Flash->success(__('Got your query. We contact you soon....'));
                return $this->redirect(['controller'=>'Users','action' => 'contactUs']);
            }else{
                $this->Flash->error(__('Server Error! Try after some time.'));
            }
        }
    }

    function __sendQueryEmail($data) {
        if (!empty($data)) {
            $email = new Email('default');
            $email
                ->setFrom([$data['email'] => $data['name']])
                ->setTo('info@ravikumarkazi.com')
                ->setSubject($data['subject'])
                ->send($data['message']);

            return true;
        }
        return false;
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->setLayout('default');
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function initialize()
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('default-signup-login');
        $this->session = $this->request->getSession();
        $this->loadComponent('Auth', [
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login',
            ],
            'authError' => 'Did you really think you are allowed to see that?',
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'email',
                                'password'=>'password'
                                ]
                ]
            ],
            'storage' => 'Session'
        ]);

        $this->Auth->allow(['add','forgotPassword','contactUs','resetPassword']);
    }
}
