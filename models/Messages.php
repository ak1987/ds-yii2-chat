<?php
namespace app\models;

class Messages extends \yii\redis\ActiveRecord
{
    /**
     *      * @return array the list of attributes for this record
     *           */
    public function attributes()
    {
        return ['id' , 'user_name', 'timestamp' , 'text', 'type'];
    }

    public static function addTestData() {
        $msg = new Messages();
        $msg->user_name = 'Шынгыс';
        $msg->timestamp = time();
        $msg->type = 1;
        $msg->text = "Привет, username".rand(1,10);
        return($msg->save());
    }
}