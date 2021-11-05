<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Partners;

class PartnersListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('partners', 'partners', 'partners_list');
    }
}
