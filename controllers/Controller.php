<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;

class Controller extends \yii\web\Controller
{
    public $sizes = [20,50,100,200];
    protected function ajaxTotalCount($dataProvider)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            Yii::$app->response->statusCode = 403;
            return json_encode([
                "code" => 403,
                "message" => "You're not allowed"
            ]);
        }
        $queryParams = Yii::$app->request->queryParams;
        $isGetTotalCount = (isset($queryParams['scenario'])) && $queryParams['scenario']=="get_count";
        if ($isGetTotalCount) {
            return $dataProvider->getTotalCount();
        }
    }
    protected function initPageSize($dataProvider)
    {
        $params = Yii::$app->request->queryParams;
        $dataProvider->pagination->pageSize = isset($params["pageSize"]) ? $params["pageSize"] : $dataProvider->pagination->pageSize;
        return $dataProvider;
    }
}
