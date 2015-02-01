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
                'form' => Rule::entity()->required()
            ]),
            //'previous' => Rule::not(true)
        ]);
    }

    function work(Request $request)
    {
        $out = $request['REQUEST']['form']->start($request);
        header("HTTP/1.1 303 See Other");
        if ($redirect = $request->getCommands('redirect')){
            header('Location: '.$redirect[0][0]);
        }else{
            header('Location: '.Request::url());//текущий адрес без аргументов
        }
        if ($out != false){
            echo is_array($out)? F::toJSON($out,false) : $out;
        }
    }
}