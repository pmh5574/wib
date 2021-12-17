<?php
namespace Controller\Admin\Share;


class GoodsSettingPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $wibGoods = \App::load('\\Component\\Wib\\WibGoods');
        
        $cnt = $wibGoods->setGoodsPmsNna();
        print_r($cnt);
        exit();
    }
}