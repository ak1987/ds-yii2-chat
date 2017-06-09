<?php

use app\models\Messages;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

/*$messages = Messages::find()->all();
var_dump($messages);*/
?>
    <style>
        .chat-msg {
            padding-bottom: 5px;
        }
    </style>
    <div class="site-index">
        <div class="row">
            <div class="col-sm-12">
                <h1>Yii2 + Redis chat</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="loginform">
                    Please, introduce yourself:
                    <input type="text" class="form-control" id="user" placeholder="UserName">
                    <br/>
                    <a id="login" class="btn btn-primary">Join Chat</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="chatscreen" style="display: none">
                <div class="col-sm-9">
                    <div id="chatwindow"
                         style="border: 1px solid #ccc; border-radius: 8px; padding: 10px; height: 200px; overflow-y: scroll"></div>
                    <br/>
                    <div>
                        Message:
                        <textarea class="form-control" rows="3" id="msg">Hello!</textarea>
                        <br/>
                        <a id="send-msg" class="btn btn-primary">Send</a>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div id="userlist"></div>
                </div>
            </div>
        </div>
    </div>
<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/main.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>