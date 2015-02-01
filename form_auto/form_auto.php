<?php
/**
 * form_auto
 * @author Vladimir Shestakov
 * @version 1.0
 */
namespace boolive\forms\form_auto;

use boolive\basic\widget_autolist\widget_autolist;
use boolive\core\data\Data;
use boolive\core\data\Entity;
use boolive\core\functions\F;
use boolive\core\request\Request;
use boolive\core\session\Session;
use boolive\core\values\Rule;

class form_auto extends widget_autolist
{
    const FROM_RESULT_NO = 0;
    const FORM_RESULT_ERROR = 1;
    const FORM_RESULT_OK = 2;

    private $_token;
    private $_base_uri;
    private $_result = self::FROM_RESULT_NO;

    function startRule()
    {
        return parent::startRule()->mix(
            Rule::arrays([
                'REQUEST' => Rule::arrays([
                    'form' => Rule::eq($this->uri())->default(false)->required()
                ]),
                'COOKIE' => Rule::arrays([
                    'token' => Rule::string()->max(32)->default(false)->required()
                ])
            ])
        );
    }

    function work(Request $request)
    {
        $this->_base_uri = $request['REQUEST']['object']->uri();
        $request->mix([
            'REQUEST' => [
                'base_uri' => $this->_base_uri
                ]
        ]);
        if ($request['REQUEST']['form']!==false){
            // Обработка формы
            $session = array();
            try{
                // Вызов полей для свойств объекта
                $request->mix(['REQUEST'=> ['call' => 'check']]);
                $list = $this->getList($request);
                if (is_array($list)){
                    $input = $request->getInput();
                    $views = $this->linked()->views->linked();
                    foreach ($list as $obj){
                        $name = preg_replace('/'.preg_quote($this->_base_uri.'/','/').'/u', '', $obj->uri());
                        $obj_input = [];
                        if (isset($input['REQUEST'][$name])){
                            $obj_input['REQUEST']['value'] = $input['REQUEST'][$name];
                        }
                        if (isset($input['FILES'][$name])){
                            $obj_input['FILES']['value'] = $input['FILES'][$name];
                        }
                        $obj_input['REQUEST']['object'] = $obj;
                        $views->start($request->mix($obj_input));
                    }
                }
                if (!$request['REQUEST']['object']->errors()->isExist()){
                    // Выполнение действия
                    $this->process();
                    $this->_result = self::FORM_RESULT_OK;
                    if (!($redirect = $this->getCommands('redirect'))){
                        $redirect = $this->redirect->inner();
                        if (!$redirect->is_draft() && $redirect->value()!=''){
                            $request->redirect(Request::url($redirect->value()));
                        }
                    }
                }else{
                    $this->_result = self::FORM_RESULT_ERROR;
                }
            }catch (\Exception $error){
                $this->_result = self::FORM_RESULT_ERROR;
            }
            $session['result'] = $this->_result;
            if ($this->_result == self::FORM_RESULT_ERROR){
                $session['object'] = $request['REQUEST']['object']->toArray();
                // @todo Для ajax запросов нужна развернутая информация об ошибках для каждого поля
                $session['message'] = 'Ошибки';
            }else
            if ($this->_result == self::FORM_RESULT_ERROR){
                $session['message'] = 'Успех';
            }
            // @todo Для ajax запросов в сессию сохранять нет смысла
            Session::set('form', array($this->uri().$this->getToken() => $session));
            setcookie('token', $this->getToken(), 0, '/');
            return $session;
        }else{
            // Отображение формы
            $v = array();
            if (isset($request['COOKIE']['token']) && Session::isExist('form')){
                $form = Session::get('form');
                if (isset($form[$this->uri().$request['COOKIE']['token']])){
                    $form = $form[$this->uri().$request['COOKIE']['token']];
                    Session::remove('form');
                }
                if (isset($form['object'])){
                    $request['REQUEST']['object'] = Entity::fromArray($form['object']);
                }
                if (isset($form['result'])){
                    $this->_result = $form['result'];
                }
            }
            return $this->show($v, $request);
        }
    }


    function show($v, Request $request)
    {

        $v['object'] = $request['REQUEST']['object'];

        $v['title'] = $this->title->inner()->value();
        $v['result'] = $this->_result;
        if ($this->_result == self::FORM_RESULT_ERROR){
            $v['message'] = 'Ошибки при проверки формы';//$this->message_error->inner()->value();
        }else
        if ($this->_result == self::FORM_RESULT_OK){
            $v['message'] = 'Успешное сохранение';//$this->message_ok->inner()->value();
        }
        /** @var Entity $obj */
        $obj = $request['REQUEST']['object'];
        if ($obj->is_exists()){
            $v['object'] = $obj->uri();
        }else{
            $v['object'] = array();
            if ($p = $obj->proto()) $v['object']['proto'] = $p;
            if ($p = $obj->parent()) $v['object']['parent'] = $p;
            $v['object'] = F::toJSON($v['object'], false);
        }
        return parent::show($v, $request);
    }

    function getList(Request $request, $cond = [])
    {
        $cond = Data::unionCond($cond, [
            'from' => $request['REQUEST']['object'],
            'select' => 'properties',
            'depth' => 1
        ]);
        $props = Data::find($cond);
        foreach ($props as $p){
            $request['REQUEST']['object']->__set($p->name(), $p);
        }
        return $props;
    }

    function process()
    {
        return true;
    }

    /**
     * Токен для сохранения в сессию ошибочных данных формы
     * @param bool $remake
     * @return string
     */
    function getToken($remake = false)
    {
        if (!isset($this->_token) || $remake){
            $this->_token = uniqid('', true);
        }
        return (string)$this->_token;
    }
} 