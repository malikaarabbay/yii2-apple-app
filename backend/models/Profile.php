<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

class Profile extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;

    public function rules()
    {
        return [

            [['username', 'email', 'password'], 'required', 'on' => 'default'],
            [['password'], 'required'],
            [['username'], 'string' ],
            [['status'], 'integer' ],

            // email has to be a valid email address
            ['email', 'email'],

        ];
    }

    public function scenarios()
    {
        return [
            'default' => ['username', 'email', 'status','password'],
            'change' => ['username', 'email', 'status'],
        ];
    }


    public function saveProfile(){
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $this->status;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->save();
            return $user;
        }
        return null;
    }

    public function changeProfile($id)
    {
        if ($this->validate()) {
            $user = User::findOne($id);
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $this->status;
            if($user->save()){
                return true;
            }
            return false;
        }
        return false;
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя',
            'email' => 'Email',
            'status' => 'Статус',
            'password' => 'Пароль',
        ];
    }

}
