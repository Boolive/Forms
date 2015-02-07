<?php
/**
 * field
 * @author Vladimir Shestakov
 * @version 1.0
 */
namespace boolive\forms\field;

use boolive\basic\widget\widget;
use boolive\core\data\Entity;
use boolive\core\errors\Error;
use boolive\core\request\Request;
use boolive\core\values\Rule;

class field extends widget
{
    function startRule()
    {
        return Rule::arrays([
            'REQUEST' => Rule::arrays([
                'object' => Rule::entity()->required(),
                'value' => Rule::scalar()->default(''),
                'base_uri' => Rule::string()->default('')->required(),
                'call' => Rule::string()->default('show')->required(),
            ]),
            'FILES' => Rule::arrays([
                'value' => Rule::arrays(Rule::string()) // файл, загружаемый в объект
            ]),
        ]);
    }

    function work(Request $request)
    {
        switch ($request['REQUEST']['call']){
            case 'check':
                return $this->processCheck($request);
                break;
            case 'save':
                if ($this->processCheck($request) === true){
                    return $this->process($request);
                }else{
                    return false;
                }
                break;
            default:
                return parent::work($request);
        }
    }

    function show($v, Request $request)
    {
        /** @var Entity $obj */
        $obj = $request['REQUEST']['object'];
        //$check = $this->processCheck();
        $v['error'] = $obj->errors()->isExist()? $obj->errors()->getUserMessage(true) : false;
        $v['uri'] = preg_replace('/'.preg_quote($request['REQUEST']['base_uri'].'/','/').'/u', '', $obj->uri());
        if (empty($v['title'])) $v['title'] = $obj->title->inner()->value();
        $v['value'] = $obj->value();
        $v['id'] = $v['uri'];
        return parent::show($v, $request);
    }

    function processCheck(Request $request)
    {
        /** @var Entity $obj */
        $obj = $request['REQUEST']['object'];
        $check = false;
        if (isset($request['REQUEST']['value'])) {
            $obj->value($request['REQUEST']['value']);
            $check = true;
        }
        if (isset($request['FILES']['value'])){
            $obj->file($request['FILES']['value']);
            $check = true;
        }
        if ($check){
            /** @var $error Error */
            $error = null;
            if (!$obj->check()){
                return array('error' => $obj->errors()->getUserMessage(true));
            }
        }
        return true;
    }

    function process(Request $request)
    {
        return false;
    }
}