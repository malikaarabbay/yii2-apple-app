<?php

namespace backend\controllers;

use common\models\User;
use common\models\search\UserSearch;
use backend\models\Profile;
use backend\models\PasswordChangeForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete', 'change-password'],
                        'allow' => false,
                        'verbs' => ['POST'],
                        'matchCallback' => function ($rule, $action) {
                            // return Yii::$app->user->identity->isVisitor;
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Profile();
        $model->scenario = 'default';

        if ($model->load(Yii::$app->request->post())) {

            if($this->checkEmail($model->email)){
                Yii::$app->getSession()->setFlash('error', 'Этот email занят');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $user = $model->saveProfile();
            return $this->redirect(['view', 'id' => $user->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $passwordChangeForm = new PasswordChangeForm();
        $model = new Profile();
        $model->scenario = 'change';

        $model->username = $user->username;
        $model->email = $user->email;
        $model->status = $user->status;

        $currentEmail = $user->email;

        if ($model->load(Yii::$app->request->post())) {

            if($this->checkEmailForUpdate($currentEmail, $model->email)){
                Yii::$app->getSession()->setFlash('error', 'Этот email уже занят');
                return $this->render('update', [
                    'model' => $model,
                    'passwordChangeForm' => $passwordChangeForm,
                    'user' => $user
                ]);
            }

            if($model->changeProfile($id)){

                Yii::$app->getSession()->setFlash('success', 'Данные сохранены');
                return $this->redirect(['view', 'id' => $id]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Возникла ошибка');
                return $this->render('update', [
                    'model' => $model,
                    'passwordChangeForm' => $passwordChangeForm,
                    'user' => $user
                ]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
                'passwordChangeForm' => $passwordChangeForm,
                'user' => $user
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Check email
     * 
     * @param string $email
     * @return bool
     */
    public function checkEmail($email){
        $user = User::find()->where(['email' => $email])->one();
        if($user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check email for update
     * 
     * @param string $oldEmail
     * @param string $email
     * @return bool
     */
    public function  checkEmailForUpdate($oldEmail, $newEmail) {
        if($oldEmail == $newEmail){
            return false;
        }
        $user = User::find()->where(['email' => $newEmail])->one();

        if($user){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Change password
     * 
     * @return string|\yii\web\Response
     */
    public function actionChangePassword()
    {
        $passwordChangeForm = new PasswordChangeForm();

        if ($passwordChangeForm->load(Yii::$app->request->post())) {
            if($passwordChangeForm->changePassword()) {
                Yii::$app->getSession()->setFlash('success', 'Пароль успешно изменен!');
                return $this->redirect(['view', 'id' => $passwordChangeForm->userid]);
            }
            else {
                Yii::$app->getSession()->setFlash('danger', 'Возникла ошибка!');
                return $this->redirect(['view', 'id' => $id]);
            }
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
