<?php

namespace common\models;

use Yii;
use Yii\db\ActiveRecord;
use backend\jobs\AppleRottenJob;

/**
 * This is the model class for table "apple".
 *
 * @property int $id
 * @property string $color
 * @property int $created_at
 * @property int|null $fell_at
 * @property int $status
 * @property int|null $eaten
 * @property integer $size
 * @property float|null $size
 * @property integer $part
 * 
 */
class Apple extends \yii\db\ActiveRecord
{
    const STATUS_ON_TREE = 1;
    const STATUS_FALL = 2;
    const STATUS_ROTTEN = 3; 

    public $part;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color'], 'required'],
            [['created_at', 'fell_at', 'status', 'eaten', 'part'], 'integer'],
            [['color'], 'string', 'max' => 255],
            [['size'], 'double', 'integerOnly' => false],
            [['status'], 'default', 'value'=> self::STATUS_ON_TREE],
        ];
    }

    /**
    * Apple constructor
    * @param string $color
    */
    public function __construct($color = null)
    {
        parent::__construct();
        $this->color = $color;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color' => 'Цвет',
            'created_at' => 'Дата появления',
            'fell_at' => 'Дата падения',
            'status' => 'Статус',
            'eaten' => 'Сколько сьели',
            'size' => 'Размер',
            'part' => 'Кусок',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    // ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
            ],
        ];

    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {

        if ($this->isNewRecord) {
            $this->status = self::STATUS_ON_TREE;
        }

        return parent::beforeSave($insert);
    }

    /**
     * Список состояния яблок
     *
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ON_TREE => 'Висит на дереве',
            self::STATUS_FALL => 'Упало/лежит на земле',
            self::STATUS_ROTTEN => 'Гнилое яблоко'
        ];
    }

    /**
     * Упасть на землю
     *
     * @return bool 
     */
    public function fallToGround()
    {
        if (!$this->validate() && $this->status !== self::STATUS_ON_TREE) {
            return false;
        }
        
        $this->status = self::STATUS_FALL;
        $this->fell_at = time();

        return $this->save(false) && $this->rottenJob();
    }

    /**
     * Съесть
     *
     * @param integer $part
     * @return bool 
     */
    public function eat($part)
    {
        
        if (!$this->validate() && $this->status !== self::STATUS_FALL) {
            return false;
        }
        
        $this->size = $this->size - ($part / 100);
        $this->eaten = $this->eaten + $part;

        if ($this->size < 0) {
            return false;
        }

        return $this->save();
    }

    /**
     * Отправка на воркер после лежания 5 часов
     *
     * @return bool 
     */
    public function rottenJob()
    {
        $id = Yii::$app->queue->delay(300 * 60)->push(new AppleRottenJob([
            'apple_id' => $this->id,
            'apple_status' => self::STATUS_ROTTEN,
        ]));

        return Yii::$app->queue->isWaiting($id);
    }
}