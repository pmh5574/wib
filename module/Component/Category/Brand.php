<?php
namespace Component\Category;

use Component\Database\DBTableField;
use Component\Storage\Storage;
use Framework\Utility\ArrayUtils;
use Framework\Utility\StringUtils;
use Globals;
use Request;
use Session;

/**
 * @package Bundle\Component\Category
 * @author  Dong-Gi Kim <zeumad@naver.com>
 */
class Brand extends \Bundle\Component\Category\Brand
{
    /** 카테고리 추출 **/
    public function getBrandCateData($divBrandCd,$cateCd,$brandDiv)
    {
		$cateField = " B.cateCd, B.cateNm, A.goodsCnt ";
		$whereStr = " WHERE A.cateCd LIKE '" . $divBrandCd . "%' AND length(A.cateCd) = " . ($brandDiv + DEFAULT_LENGTH_CATE);
		$orderByStr = " ORDER BY B.cateCd ASC ";
		$cateTable = " (SELECT b.cateCd, count(b.cateCd) AS goodsCnt FROM es_goods a LEFT JOIN es_goodsLinkCategory b ON a.goodsNo = b.goodsNo WHERE a.brandCd = ". $cateCd ." GROUP BY b.cateCd) A LEFT JOIN es_categoryGoods B ON A.cateCd = B.cateCd ";
		
		$strSQL = " SELECT " . $cateField . " FROM " . $cateTable . $whereStr . $orderByStr;

        $getData = $this->db->query_fetch($strSQL);
		
        if ($debug === true) echo $strSQL;

        return gd_htmlspecialchars_stripslashes($getData);
    }

    /** Controller와 연결되는 부분 **/
    public function getBrandCate($cateCd,$brandCateCd)
    {
		$brandDiv = [];
		for($i = 0; $i <= strlen($brandCateCd) / DEFAULT_LENGTH_CATE; $i++)
		{
			array_push($brandDiv, $i * DEFAULT_LENGTH_CATE);
		}

		foreach ($brandDiv as $key => $val) {
			if (!$divBrandCd[$key]) {
				$divBrandCd[$key] = [];
			}
			$divBrandCd[$key] = substr($brandCateCd, 0, $val);
		}
		
		foreach ($divBrandCd as $key => $val) {
		$resCategory[$key] = $this->getBrandCateData($divBrandCd[$key],$cateCd,$brandDiv[$key]);
		}

        return $resCategory;
    }

    /** location 내 Selected 값 추출 **/
    public function getBrandSelectedCate($cateCd, $cateType)
    {
		if($cateType == 'location'){
			$cateTable = " es_categoryGoods ";
		} else {
			$cateTable = " es_categoryBrand ";
		}
		$cateField = " cateCd, cateNm ";
		$whereStr = " WHERE cateCd = " . $cateCd;
		$orderByStr = " ORDER BY cateCd ASC ";

		$strSQL = "SELECT " . $cateField . " FROM ". $cateTable . $whereStr . $orderByStr;
		$getData = $this->db->fetch($strSQL);
		
        if ($debug === true) echo $strSQL;

        return gd_htmlspecialchars_stripslashes($getData);
	}
}