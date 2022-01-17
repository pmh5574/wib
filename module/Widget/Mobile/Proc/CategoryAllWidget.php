<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Widget\Mobile\Proc;

use Session;
/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Outline
 * @author  Young Eun Jung <atomyang@godo.co.kr>
 */
class CategoryAllWidget extends \Bundle\Widget\Mobile\Proc\CategoryAllWidget
{
    public function post()
    {
        $categoryData = $this->getData('data');
        
        $cateCd = Session::get('WIB_SHOP_NUM');

        $data = [];
        //2차 카테고리부터 노출
        foreach ($categoryData as $value){
            
            if($cateCd != '1'){ // 프리미엄 멀티샵, 뉴니아
                
                if($value['cateCd'] == $cateCd){
                    $data = $value['children'];
                }
                
            }else if($cateCd == '1'){ //블루
                
                foreach ($value['children'] as $key => $val) {
                    $data[] = $val;
                }
                
            }
            
        }

        $this->setData('data', $data);
    }
}