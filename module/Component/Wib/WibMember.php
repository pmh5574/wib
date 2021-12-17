<?php

namespace Component\Wib;

use Component\Wib\WibSql;
use Request;


class WibMember
{
    public $wibSql;
    
    public function __construct() 
    {
        $this->wibSql = new WibSql;
    }
    
    public function getMemberOrderCount($member) 
    {
        $query = "SELECT COUNT(*) cnt FROM es_order WHERE memNo = {$member} AND orderStatus NOT LIKE 'f%' ";
        $data = $this->wibSql->WibNobind($query)['cnt'];
        
        return $data;
    }
    
    public function saveMember($userInfo,$properties) 
    {       
        $request = \App::getInstance('request');
        
        if(strpos($userInfo['kakao_account']['phone_number'], '+82 ') !== false){
            $cellPhone = str_replace('+82 ', '0', $userInfo['kakao_account']['phone_number']);
        }else{
            $cellPhone = $userInfo['kakao_account']['phone_number'];
        }
        
        $params = [
            'state' => $request->get()->get('state'),
            'id' => $userInfo['id'],
            'adultFl' => 'n',
            'agreementInfoFl' => 'y',
            'privateApprovalFl' => 'y',
            'kakaoToken' => $properties,
            'email' => $userInfo['kakao_account']['email'],
            'memId' => 'kko'.$userInfo['id'],
            'memNm' => $userInfo['properties']['nickname'],
            'cellPhone' => $cellPhone,
            'memPw' => '',
            'userData' => $userInfo,
        ];
        
        $query = [
            'mallSno' => [1,'i'],
            'memId' => [$params['memId'],'s'],
            'groupSno' => [1,'i'],
            'groupModDt' => ['0000-00-00 00:00:00','s'],
            'groupValidDt' => ['0000-00-00 00:00:00','s'],
            'memNm' => [$params['memNm'],'s'],
            'memPw' => ['','s'],
            'appFl' => ['y','s'],
            'approvalDt' => [date('Y-m-d H:i:s'),'s'],
            'memberFl' => ['personal','s'],
            'email' => [$params['email'],'s'],
            'phoneCountryCode' => ['kr','s'],
            'cellPhone' => [$params['cellPhone'],'s'],
            'entryDt' => [date('Y-m-d H:i:s'),'s'],
            'regDt' => [date('Y-m-d H:i:s'),'s'],
        ];

        $data = [
            'es_member',
            $query
        ];
        
        $memberData = [
            'mallSno' => 1,
            'memId' => $params['memId'],
            'groupSno' => 1,
            'groupModDt' => '',
            'groupValidDt' => '',
            'memNm' => $params['memNm'],
            'memPw' => '',
            'pronounceName' => '',
            'nickNm' => '',
            'appFl' => 'y',
            'approvalDt' => date('Y-m-d H:i:s'),
            'memberFl' => 'personal',
            'email' => gd_isset($params['email']),
            'phoneCountryCode' => 'kr',
            'cellPhone' => gd_isset($params['cellPhone']),
            'entryDt' => date('Y-m-d H:i:s'),
            'regDt' => date('Y-m-d H:i:s'),
        ];
        
        $memNo = $this->wibSql->WibInsert($data);
        
        $memberData['memNo'] = $memNo;
        
        $member = \App::load('\\Component\\Member\\Member');
        
        $member->benefitJoin(new \Component\Member\MemberVO($memberData));
        
        
        return $memNo;
        
        
    }
}

