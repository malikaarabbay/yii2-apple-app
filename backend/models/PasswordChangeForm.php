<?php

namespace backend\models;

use Yii;
use common\models\User;
use yii\base\Model;


class PasswordChangeForm extends Model
{

    public $password;
    public $passwordRepeat;
    public $userid;


    public function rules()
    {
        return [
            [['userid'], 'integer'],
            [['password'], 'string', 'min' => 6],
            [['password', 'passwordRepeat'], 'required'],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'password' => 'Новый пароль',
            'passwordRepeat' => 'Повтор пароля',
        ];
    }

    public function changePassword()
    {
        if ($this->validate()) {
            $user = User::findOne($this->userid);
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user = $user->save();
            return $user;
        }
        return null;
    }

}