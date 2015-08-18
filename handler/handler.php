<?php
/**
 * Обработчик форм
 * Принимает запросы от форм. Во входящих данных должен быть параметр form с uri формы (виджета).
 * Клиенту отвечает по умолчанию редиректом на текущий запрос
 * @version 1.0
 */
namespace boolive\forms\handler;


use boolive\basic\controller\controller;
use boolive\core\functions\F;
use boolive\core\request\Request;
use boolive\core\values\Rule;

class handler extends controller
{
    function startRule()
    {
        return Rule::arrays([
            'REQUEST' => Rule::arrays([
                'handler' => Rule::entity()->required()
            ]),
            //'previous' => Rule::not(true)
        ]);
    }

    function startCheck(Request $request)
    {
        $input = $request->getInput();
        if (!isset($input['REQUEST']['handler']) && isset($input['REQUEST']['form'])){
            $request->mix(['REQUEST' => ['handler' => $input['REQUEST']['form']]]);
        }
        return parent::startCheck($request);
    }

    function work(Request $request)
    {
        $out = $request['REQUEST']['handler']->start($request);
        if ($redirect = $request->getCommands('redirect')){
            header("HTTP/1.1 303 See Other");
            header('Location: '.$redirect[0][0]);
        }else{
            if ($out != false){
                if (is_array($out)){
                    header('Content-Type: application/json');
                    echo F::toJSON($out,false);
                }else{
                    echo $out;
                }
            }else {
                header('Location: ' . Request::url());//текущий адрес без аргументов
            }
        }
    }
}