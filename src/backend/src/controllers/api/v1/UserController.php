<?php

namespace backend\controllers\api\v1;
use Yii;
use backend\db\models\User;
use backend\db\repositories\FileRepositoryInterface;
use backend\db\repositories\UserRepositoryInterface;
use backend\exceptions\FormValidationException;
use backend\forms\api\ChangePasswordFormModel;
use backend\forms\api\DirectChangePasswordFormModel;
use backend\forms\api\ForgotPasswordFormModel;
use backend\forms\api\LoginFormModel;
use backend\forms\api\LoginRefreshFormModel;
use backend\forms\api\RegistrationFormModel;
use backend\forms\api\UserUpdateFormModel;
use backend\forms\common\FileFormModel;
use backend\services\JwtService\JwtBearerAuth;
use backend\services\UserService\UserService;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class UserController
 * @package backend\controllers\v1
 *
 * @property \backend\db\repositories\UserRepositoryInterface $userRepository
 */
class UserController extends BaseController
{
    public function getVerbs(): array
    {
        return [
            'list' => ['POST', 'OPTIONS'],
            'get' => ['POST', 'OPTIONS'],
            'remove' => ['POST', 'OPTIONS'],
            'login' => ['POST', 'OPTIONS'],
            'logout' => ['POST', 'OPTIONS'],
            'hold' => ['POST', 'OPTIONS'],
            'login-refresh' => ['POST', 'OPTIONS'],
            'detail' => ['POST', 'OPTIONS'],
            'update' => ['POST', 'OPTIONS'],
            'registration' => ['POST', 'OPTIONS'],
            'forgot-password' => ['POST', 'OPTIONS'],
            'change-password' => ['POST', 'OPTIONS'],
            'direct-change-password' => ['POST', 'OPTIONS'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
            'only' => ['detail', 'update', 'list', 'get', 'remove', 'registration', 'logout', 'hold'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['remove', 'direct-change-password', 'registration'],
                    'allow' => true,
                    'roles' => ['1'],
                ],
                [
                    'actions' => ['detail', 'update', 'get', 'list', 'logout', 'hold'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'actions' => [
                        'login',
                        'login-refresh',
                        'forgot-password',
                        'change-password',
                    ],
                    'allow' => true,
                    'roles' => ['?', '@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionGet()
    {
        $id = $this->request->get('id');

        /** @var \backend\services\UserService\UserRepositoryInterface $userService */
        $userService = \Yii::$container->get(UserRepositoryInterface::class);

        $result = $userService->findByID($id); 
        if(!$result) throw new HttpException(418, 'User not found.');

        $userOnline = \Yii::$app->session->getSessionsById($result->id);

        $user = $result->publicBundle();
        $user['isOnline'] = count($userOnline);

        return $this->asJson($user);
    }

    public function actionHold()
    {
        $id = $this->request->get('id');
        
        /** @var \backend\services\UserService\UserRepositoryInterface $userService */
        $userService = \Yii::$container->get(UserRepositoryInterface::class);

        $user = $userService->findByID($id);
        $id = $user->id; 
    
        $result = \Yii::$app->session->destroyUserSessions($id);

        return $this->asJson($result);
    }

    public function actionLogout()
    {
        $id = \Yii::$app->user->id;
        \Yii::$app->user->logout();
        
        $result = \Yii::$app->session->destroyUserSessions($id);

        return $this->asJson($result);
    }

    public function actionRemove()
    {
        $users = json_decode($this->request->post('users'));

        foreach ($users as &$id) {
            $id = preg_replace('#\-[^-]*$#', '', $id);
        }

        /** @var \backend\services\FileService\FileRepositoryInterface $fileService */
        $fileService = \Yii::$container->get(FileRepositoryInterface::class);
        $fileService->removeByUsers($users);

        /** @var \backend\services\UserService\UserRepositoryInterface $userService */
        $userService = \Yii::$container->get(UserRepositoryInterface::class);

        if (!$userService->remove($users)) {
            throw new HttpException(418, 'Error deleting users from DB.');
        }

        $result = array();
        $result['code'] = '0';
        $result['status'] = 'success';
        return $this->asJson(['result' => $result]);
    }

    public function actionList()
    {
        $usersOnline = \Yii::$app->session->getOnlineUsers();
        $filter = $this->request->get('filter');
        $sort = $this->request->get('sort');
        $offset = (int) $this->request->get('offset', 0);
        $limit = (int) $this->request->get('limit', 25);

        $filter = empty($filter) ? null : Json::decode($filter);
        $sort = empty($sort) ? null : Json::decode($sort);

        /** @var \backend\services\UserService\UserService $userService */
        $userService = \Yii::$container->get(UserService::class);

        $total = $userService->countAllUsers($filter);

        $this->response->headers->add('X-Pagination-Total', $total);

        $response = $userService->getAllUsers($limit, $offset, $filter, $sort);

        $groups = array();

        foreach ($response as &$data) {
            $data = (array) $data;

            $data['isOnline'] = array_key_exists($data['id'], $usersOnline)?$usersOnline[$data['id']]['active']:0;

            if (!array_key_exists('group_' . $data['groupId'], $groups) && $data['roleId'] != 1) {
                $groups['group_' . $data['groupId']] = array('name' => $data['groupName'], 'users' => array());
            }elseif(!array_key_exists('group_0', $groups) && $data['roleId'] == 1){
 
                $groups['group_0'] = array('name' => 'ADMINS', 'users' => array());

            }

            unset($data['secret']);
            unset($data['password']);
            unset($data['passwordHash']);

            if ($data['roleId'] == 1) {
                $groups['group_0']['users'][] = $data;
            } else {
                $groups['group_' . $data['groupId']]['users'][] = $data;
            }

        }
        
        ksort($groups);
        return $this->asJson($groups);
    }

    public function actionLogin()
    {
        sleep($this->getSleepSeconds());

        /** @var \backend\forms\api\LoginFormModel $Form */
        $Form = \Yii::$container->get(LoginFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }
        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionLoginRefresh()
    {
        /** @var \backend\forms\api\LoginRefreshFormModel $Form */
        $Form = \Yii::$container->get(LoginRefreshFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionRegistration()
    {
        sleep($this->getSleepSeconds());

        $avatarId = 0;
        if (!empty($_FILES)) {
            /** @var \backend\forms\common\FileFormModel $Form */
            $Form = \Yii::$container->get(FileFormModel::class);

            $Form->path = 'assets/avatars/';
            $Form->extansions = 'png, jpg, jpeg';
            $Form->file = UploadedFile::getInstanceByName('file');

            if (!$Form->validate()) {
                throw new FormValidationException($Form);
            }

            $avatar = $Form->upload();
            $avatarId = $avatar['id'];
        }

        /** @var \backend\forms\api\RegistrationFormModel $Form */
        $Form = \Yii::$container->get(RegistrationFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        $Form->avatarId = $avatarId;

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionForgotPassword()
    {

        /** @var \backend\forms\api\ForgotPasswordFormModel $Form */
        $Form = \Yii::$container->get(ForgotPasswordFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionChangePassword()
    {
        /** @var \backend\forms\api\ChangePasswordFormModel $Form */
        $Form = \Yii::$container->get(ChangePasswordFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionDirectChangePassword()
    {
        /** @var \backend\forms\api\DirectChangePasswordFormModel $Form */
        $Form = \Yii::$container->get(DirectChangePasswordFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionDetail()
    {
        /** @var \backend\db\models\User $user */
        $user = \Yii::$app->user->identity;

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $result = [
            'user' => $user->publicBundle(),
        ];

        return $this->asJson(['result' => $result]);
    }

    public function actionUpdate()
    {

        sleep($this->getSleepSeconds());

        $avatar = false;
        $avatarId = 0;

        if (!empty($_FILES)) {

            /** @var \backend\forms\common\FileFormModel $Form */
            $Form = \Yii::$container->get(FileFormModel::class);

            $Form->path = 'assets/avatars/';
            $Form->extansions = 'png, jpg, jpeg';
            $Form->file = UploadedFile::getInstanceByName('file');

            if (!$Form->validate()) {
                throw new FormValidationException($Form);
            }

            $avatar = $Form->upload();
            $avatarId = $avatar['id'];
        }

        /** @var UserUpdateFormModel $Form */
        $Form = \Yii::$container->get(UserUpdateFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        $Form->avatarId = $avatarId;
        if ($avatar) {
            $Form->avatarName = $avatar['name'];
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    /**
     * @return \backend\db\repositories\UserRepositoryInterface
     */
    private function getUserRepository()
    {
        return \Yii::$container->get(UserRepositoryInterface::class);
    }

}
