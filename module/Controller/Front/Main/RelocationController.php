<?php

namespace Controller\Front\Main;


use Request;
use App;
use RuntimeException;
use Framework\StaticProxy\Proxy\Encryptor;
use Framework\Utility\ArrayUtils;

class RelocationController extends \Controller\Front\Controller
{
	public function index()
	{
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', 1024);

		/* //회원 비밀번호 테스트
		echo App::getInstance('password')->hash('!Admin1004');
		exit;
		*/

		$requestGetParmeter = Request::get()->all();
		
		$db					= App::load('DB');
		/*
		if ($requestGetParmeter['mode'] == 'getGodo5DB') {
			$arrayGodo5DefaultBoard = array(
				'es_bd_cooperation',
				'es_bd_event',
				'es_bd_goodsqa',
				'es_bd_goodsreview',
				'es_bd_notice',
				'es_bd_qa',
			);
			$db = new \mysqli('malldb50-189.godomall.com', 'artherot10', 'artherot10133639', 'artherot10_godomall_com');

			$tableResult		= $db->query('show tables');

			while($tableRow = $tableResult->fetch_array(MYSQLI_BOTH)) {
				$createTableResult	= $db->query('show create table '.$tableRow[0]);
				$createTableResult	= $createTableResult->fetch_array(MYSQLI_BOTH);
				if (preg_match('/^es_bd_.*$/', $tableRow[0])) {
					
					if (in_array($tableRow[0], $arrayGodo5DefaultBoard)) {
						$arrayTable[$tableRow[0]]['create'] = $createTableResult[1];
					}
				}
				else {
					$arrayTable[$tableRow[0]]['create'] = $createTableResult[1];
				}
			}
			
			foreach($arrayTable as $key => $value) {
				
				
				preg_match_all('/^\s+`([_a-zA-Z0-9-]+)` (.+)\n/m', $value['create'], $matches);
				
				$length	= count($matches[0]);
				for($i = 0; $i < $length; $i++) {
					$fieldname	= $matches[1][$i];
					$arrayTable[$key]['fields'][$fieldname] = preg_replace('/,?\n?$/', '', $matches[2][$i]);
				}

				preg_match_all('/^\s+KEY `([_a-zA-Z0-9-]+)` (.+)\n/m', $value['create'], $matches);
				$length	= count($matches[0]);

				for ($i=0; $i < $length; $i++) {
					$arrayTable[$key]['keys'][$matches[1][$i]]	= preg_replace('/,?\n?$/','',$matches[2][$i]);
				}
			}
			
			echo serialize($arrayTable);
			
			exit;
		}
		else 
		*/
		if ($requestGetParmeter['mode'] == 'getGodo5DB') {
			$postParmeter	= Request::post()->all();
			$type		= ($postParmeter['type']) ? $postParmeter['type'] : 'insert';
			?>
			<html>
				<head>
				<title> godomall5 Data Setting </title>
					
				</head>

				<body>
				<form name="versionForm" method="POST" action="<?=getenv('PHP_SELF')?>">
				<input type="hidden" name="type" value="insert" />
			<?php
			
			switch ($type) {
				case('insert') :
					$data = file_get_contents("http://hym1987.godomall.com/main/relocation.php?mode=getGodo5DB&deliveryUpdate=y&adminMenu=y&escode=y");
					$arrayData			= unserialize($data);
					$arrayQuerys		= array();
						
					$arrayTables		= array();
					$arrayKeyType		= array();
					$arrayBoardTables	= array();
					$result		= $db->query('show tables');

					$arrayTargetTable = array();
					$arrayQuerys['adminMenu'][] = "Delete From es_adminMenu Where adminMenuProductCode = 'godomall'";
					while($row = $db->fetch($result, 'array')) {
						$arrayTargetTable[] = $row[0];
						$sub_result	= $db->query('show create table ' . $row[0]);
						$sub_result	= $db->fetch($sub_result, 'array');
						
						if(preg_match('/^es_bd_.*$/', $row[0])) {
							$arrayBoardTables[$row[0]]['create'] = $sub_result[1];
						}
						
						$arrayTables[$row[0]]['create']	= $sub_result[1];
					}
					
					foreach($arrayTables as $key => $value) {
						preg_match_all('/^\s+`([_a-zA-Z0-9-]+)` (.+)\n/m',$value['create'],$matches);

						$length = count($matches[0]);
						for($i = 0; $i < $length; $i++) {
							$fieldname	= $matches[1][$i];

							$arrayTables[$key]['fields'][$fieldname]		= preg_replace('/,?\n?$/','',$matches[2][$i]);
						}

						preg_match_all('/^\s+([_a-zA-Z]{4,}[[:space:]]|)KEY `([_a-zA-Z0-9-]+)` (.+)\n/m', $value['create'], $matches);
						$length	= count($matches[0]);
						for($i = 0; $i < $length; $i++) {
							$arrayTables[$key]['keys'][$matches[2][$i]] = preg_replace('/,?\n?$/','',$matches[3][$i]);
						}

						preg_match_all('/^\s+PRIMARY KEY (.+)\n/m', $value['create'], $matches);
						if (!empty($matches)) {
							$arrayTables[$key]['primaryKeys'] = preg_replace('/,?\n?$/','',$matches[1]);
						}
					}

					foreach($arrayBoardTables as $key => $value) {
						preg_match_all('/^\s+`([_a-zA-Z0-9-]+)` (.+)\n/m',$value['create'],$matches);

						$length = count($matches[0]);
						for($i = 0; $i < $length; $i++) {
							$fieldname	= $matches[1][$i];
							$arrayBoardTables[$key]['fields'][$fieldname]		= preg_replace('/,?\n?$/','',$matches[2][$i]);
						}

						preg_match_all('/^\s+([_a-zA-Z]{4,}[[:space:]]|)KEY `([_a-zA-Z0-9-]+)` (.+)\n/m', $value['create'], $matches);
						$length	= count($matches[0]);
						for($i = 0; $i < $length; $i++) {
							$arrayBoardTables[$key]['keys'][$matches[2][$i]] = preg_replace('/,?\n?$/','',$matches[3][$i]);
						}

						preg_match_all('/^\s+PRIMARY KEY (.+)\n/m', $value['create'], $matches);
						if (!empty($matches)) {
							$arrayBoardTables[$key]['primaryKeys'] = preg_replace('/,?\n?$/','',$matches[1]);
						}
					}
					
					$chk_prn_settleprice	= false;
					foreach($arrayData as $key => $value) {
						if(!$arrayTables[$key] && !preg_match('/^es_bd_notice.*$/', $key) && $key != 'deliveryUpdate' &&  $key != 'adminMenu' &&  $key != 'escode') {
							if ($key != 'tmp_brandcode' && $key != 'tmp_orderno') {
								$arrayQuerys['create'][] = $value['create'];
							}
						}
						else if(preg_match('/^es_bd_notice.*$/', $key)) {
							foreach($arrayBoardTables as $boardKey => $boardValue) {
								if (empty($arrayData[$boardKey]) || $boardKey == 'es_bd_notice') {
									if($result = $this->array_diff_assoc_ignorecase($value['fields'], $arrayBoardTables[$boardKey]['fields'])) {
										foreach($result as $fields => $f_value) {
											if($arrayBoardTables[$boardKey]['fields'][$fields]) {
												$arrayQuerys['f_modify'][] = 'Alter Table `' . $boardKey . '` Modify `' . $fields . '` ' . $value['fields'][$fields];
											}
											else if(!$arrayBoardTables[$boardKey]['fields'][$fields]) {
												$arrayQuerys['f_insert'][] = 'Alter Table `'.$boardKey.'` Add `'.$fields.'` '.$value['fields'][$fields];
											}
										}
									}
									
									if(!is_array($value['keys'])) $value['keys'] = array();
									if(!is_array($arrayBoardTables[$boardKey]['keys'])) $arrayBoardTables[$boardKey]['keys'] = array();
									/* 튜닝된 키(별동 생성한 키)값일때 삭제 하는 기능 이나 튜닝된 기능 삭제 시 문제 우려가 있어 주석처리
									if($result = array_diff_assoc($arrayBoardTables[$boardKey]['keys'], $value['keys'])) {
										foreach($result as $key_name => $key_value) {
											if($arrayBoardTables[$boardKey]['keys'][$key_name]) {
												$arrayQuerys['keys'][] = 'Alter Table `' . $boardKey . '` Drop KEY ' . $key_name;
											}
										}
									}
									*/

									if($result = array_diff_assoc($value['keys'], $arrayBoardTables[$boardKey]['keys'])) {
										foreach($result as $key_name => $key_value) {
											if($arrayBoardTables[$boardKey]['keys'][$key_name]) {
												$arrayQuerys['keys'][] = 'Alter Table `' . $boardKey . '` Drop KEY ' . $key_name;
											}
											$arrayQuerys['keys'][] = 'Alter Table `' . $boardKey . '` Add ' . $value['keyType'][$key_name] . ' KEY ' . $key_name . ' ' . $value['keys'][$key_name];
										}
									}

									if($result = array_diff_assoc($value['primaryKeys'], $arrayBoardTables[$boardKey]['primaryKeys'])) {
										foreach($result as $key_name => $key_value) {
											if($arrayBoardTables[$boardKey]['primaryKeys']) {
												$arrayQuerys['primaryKeys'][] = "Alter Table `" . $boardKey . "` Drop PRIMARY KEY";
											}
											$arrayQuerys['primaryKeys'][] = "Alter Table `" . $boardKey . "` Add PRIMARY KEY " . $key_value;
										}
									}
								}
							}
						}
						else if ($key == 'deliveryUpdate') {
							foreach ($value as $deliveryInfo) {
								$dataRow = $db->fetch($db->query("Select sno, companyName, companyKey, traceUrl, inicisCode, lguplusCode, naverPayCode, deliveryFl From es_manageDeliveryCompany Where sno = " . $deliveryInfo['sno']));
								$arrayUpdateData = array();
								foreach ($dataRow as $fieldName => $fieldValue) {
									if ($fieldName != 'sno' && $dataRow[$fieldName] != $deliveryInfo[$fieldName]) {
										$arrayUpdateData[$fieldName] = is_null($deliveryInfo[$fieldName]) ? null : $deliveryInfo[$fieldName];
									}
								}
								if (!empty($arrayUpdateData)) {
									$arrayString = array();
									foreach ($arrayUpdateData as $fieldName => $fieldValue) {
										if (is_null($fieldValue)) {
											$arrayString[] = $fieldName . " = NULL";
										}
										else {
											$arrayString[] = $fieldName . " = '" . addslashes($fieldValue) . "'";
										}
									}
									$arrayQuerys['deliveryUpdate'][] = "Update es_manageDeliveryCompany Set " . implode(', ', $arrayString) . " Where sno = " . $deliveryInfo['sno'];
								}
							}
						}
						else if ($key == 'adminMenu') {
							foreach ($value as $adminMenuInsertQuery) {
								$arrayQuerys['adminMenu'][] = $adminMenuInsertQuery;
							}
						}
						else if ($key == 'escode') {
							foreach ($value as $itemCd => $secondArray) {
								foreach ($secondArray as $groupCd => $insertQuery) {
									$escodeResult = $db->query("Select count(*) codeCnt From es_code Where itemCd = '{$itemCd}' and groupCd = '{$groupCd}'");
									$dbRow = $db->fetch($escodeResult, 'array');
									if (!$dbRow['codeCnt']) {
										$arrayQuerys['escode'][] = $insertQuery;
									}
								}
							}
						}
						else {
							if($result = $this->array_diff_assoc_ignorecase($value['fields'],$arrayTables[$key]['fields'])) {	
								foreach($result as $fields => $f_value) {
									
									if($arrayTables[$key]['fields'][$fields]) {
										$arrayQuerys['f_modify'][] = 'Alter Table `' . $key . '` Modify `' . $fields . '` ' . $value['fields'][$fields];
									}
									else if(!$arrayTables[$key]['fields'][$fields]) {
										$arrayQuerys['f_insert'][] = 'Alter Table `'.$key.'` Add `'.$fields.'` '.$value['fields'][$fields];
									}
								}
							}
							
							if(!is_array($value['keys'])) $value['keys'] = array();
							if(!is_array($arrayTables[$key]['keys'])) $arrayTables[$key]['keys'] = array();
							/* 튜닝된 키(별동 생성한 키)값일때 삭제 하는 기능 이나 튜닝된 기능 삭제 시 문제 우려가 있어 주석처리
							if($result = array_diff_assoc($arrayTables[$key]['keys'], $value['keys'])) {
								foreach($result as $key_name => $key_value) {
									if($arrayTables[$key]['keys'][$key_name]) {
										$arrayQuerys['keys'][] = 'Alter Table `' . $key . '` Drop KEY ' . $key_name;
									}
								}
							}
							*/

							if($result = array_diff_assoc($value['keys'], $arrayTables[$key]['keys'])) {
								foreach($result as $key_name => $key_value) {
									if($arrayTables[$key]['keys'][$key_name]) {
										$arrayQuerys['keys'][] = 'Alter Table `' . $key . '` Drop KEY ' . $key_name;
									}
									$arrayQuerys['keys'][] = 'Alter Table `' . $key . '` Add ' . $value['keyType'][$key_name] . ' KEY ' . $key_name . ' ' . $value['keys'][$key_name];
								}
							}
							
							if($result = array_diff_assoc($value['primaryKeys'], $arrayTables[$key]['primaryKeys'])) {
								foreach($result as $key_name => $key_value) {
									if($arrayTables[$key]['primaryKeys']) {
										$arrayQuerys['primaryKeys'][] = "Alter Table `" . $key . "` Drop PRIMARY KEY";
									}
									$arrayQuerys['primaryKeys'][] = "Alter Table `" . $key . "` Add PRIMARY KEY " . $key_value;
								}
							}
						}
					}

					// 쿼리 구분
					$arrayQueryMode = array(
						'create'	=> iconv('euc-kr', 'utf-8', '테이블 추가'),
						'f_insert'	=> iconv('euc-kr', 'utf-8', '필드 추가'),
						'f_modify'	=> iconv('euc-kr', 'utf-8', '필드 수정'),
						'f_drop'	=> iconv('euc-kr', 'utf-8', '필드 삭제'),
						'keys'		=> iconv('euc-kr', 'utf-8', '인덱스(키)'),
						'primaryKeys'		=> iconv('euc-kr', 'utf-8', '프라이머리(기본키)'),
						'deliveryUpdate'	=> iconv('euc-kr', 'utf-8', '배송사 등록/수정'),
						'adminMenu'	=> iconv('euc-kr', 'utf-8', '관리자 메뉴 변경등록'),
						'escode' => iconv('euc-kr', 'utf-8', '코드관리 추가등록'),
					);
					?>
					<div style='display:none;' id='queryCopyArea'>
					<?php
						foreach($arrayQueryMode as $qKey => $qVal)
						{
							if(count($arrayQuerys[$qKey]))
							{
								foreach($arrayQuerys[$qKey] as $key => $value) {
									echo $value . ';<br/>';
								}
							}
						}
					?>
					</div>
						<table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse" bordercolor="#999999">
						<tr>
							<td colspan="2">&nbsp; <b><?=iconv('euc-kr', 'utf-8', '쿼리선택')?></b></td>
						</tr>
						<?php
							foreach($arrayQueryMode as $qKey => $qVal)
							{
								if(count($arrayQuerys[$qKey]))
								{
						?>
						<tr>
							<td width="110" nowrap valign="top"><?php echo $qVal?></td>
							<td width="100%">
								<table width="100%" border="0" cellspacing="0" cellpadding="3">
						<?php
								foreach($arrayQuerys[$qKey] as $key => $value) {
						?>
								<tr>
									<td width="150" nowrap valign="top">
										<input type="radio" name="<?php echo $qKey?>[<?php echo $key?>][yn]" value="y" checked /><?=iconv('euc-kr', 'utf-8', '추가')?> &nbsp;
										<input type="radio" name="<?php echo $qKey?>[<?php echo $key?>][yn]" value="n" /><?=iconv('euc-kr', 'utf-8', '추가안함')?>
									</td>
									<td width="100%">
						<?php
									if($qKey == 'create') {
						?>
										<textarea style="width:100%;height:200px;font-size:9pt" name="<?php echo $qKey?>[<?php echo $key?>][sql]" readonly ><?php echo htmlspecialchars($value)?></textarea>
						<?php
									} else {
						?>
										<input type="text" style="width:100%;font-size:9pt" name="<?php echo $qKey?>[<?php echo  $key?>][sql]" value="<?php echo htmlspecialchars($value)?>" readonly class="box">
						<?php
									}
						?>
									</td>
								</tr>
						<?php
								}
						?>
								</table>
							</td>
						</tr>
						<?php
								}

							}
						?>
						</table>
						<br>
						<center>
							<input type="button" value="<?=iconv('euc-kr', 'utf-8', '쿼리복사')?>" style="cursor:pointer" onclick="window.clipboardData.setData('text', document.getElementById('queryCopyArea').innerText);">
							<input type="submit" value="<?=iconv('euc-kr', 'utf-8', '쿼리실행')?>" onclick="this.form.type.value='exec'">
						</center>
						<?php
							break;
						case('exec') :
							$ar_querys = array();
							foreach($postParmeter as $q_type => $value) {
								if(is_array($value)) {
									foreach($value as $key => $sub_value) {
										if($sub_value['yn']=='y') {
											$ar_querys[]	= array('sql' => stripslashes($sub_value['sql']), 'done' => false);
										}
									}
								}
							}

							foreach($ar_querys as $key => $value) {
								if($db->query($value['sql'])) {
									$ar_querys[$key]['done']	= true;
								} else {
									$ar_querys[$key]['done']	= false;
								}
							}
						?>
						<table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse" bordercolor="#999999">
						<tr>
							<td colspan="2" >&nbsp; <b><?=iconv('euc-kr', 'utf-8', '쿼리 결과')?></b></td>
						</tr>
						<?php
							if(count($ar_querys)) {
								foreach($ar_querys as $key => $value) {
						?>
						<tr>
							<td width="100%">
								<?php if($value['done']) {?><font color="#0000CC"><?php } else {?><font color="#CC0000"><?php } ?>
								<pre><?php echo htmlspecialchars($value['sql'])?></pre>
								</font>
							</td>
						</tr>
						<?php
								}
								flush();
							}
						?>
						</table>
						<?php
							break;
						?>
						</form>
						</body>
					</html>
					<?php
				
					exit;
			}
		}
		else if ($requestGetParmeter['mode'] == 'sleepMemberUpdate') {
			$arrayTargetData = array(
				'ex1',
				'ex2',
				'ex3',
				'ex4',
				'ex5',
				'ex6',
				'nickNm',
				'memNm',
				'address',
				'email',
				'recommId',
				'company',
			);

			$sleepMemberResult = $db->query("Select sleepNo, encryptData, memId From es_memberSleep order by sleepNo");
			while ($sleepMemberRow = $db->fetch($sleepMemberResult, 'array')) {
				if (!unserialize($sleepMemberRow['encryptData'])) {
				}
				else {
					$updateQuery = "Update es_memberSleep Set encryptData = '" . Encryptor::mysqlAesEncrypt($sleepMemberRow['encryptData']) . "' Where sleepNo=" . $sleepMemberRow['sleepNo'];

					if (!$db->query($updateQuery)) {
						echo '<div style="color:red">' . iconv('euc-kr', 'utf-8', '업데이트 실패 : ') . $updateQuery . '</div>';
						exit;
					}
					else {
						echo '<div style="color:blue">' . iconv('euc-kr', 'utf-8', '업데이트 성공') . '</div>';
					}
				}
			}
			
			$serializeFl = false;
			$sleepMemberResult = $db->query("Select sleepNo, encryptData, memId From es_memberSleep where encryptData like '%\"%'");
			while ($sleepMemberRow = $db->fetch($sleepMemberResult, 'array')) {
				foreach ($arrayTargetData as $targetData) {
					if (preg_match_all("/\"{$targetData}\"\;s\:([[:digit:]]{1,})\:\"?([^\;]+)/", $sleepMemberRow['encryptData'], $result)) {
						if (preg_match_all("/(\'|\"|\\\|\♥|\r\n|\r|\n|\^)+/", substr($result[2][0], 0, (strlen($result[2][0]) - 1)), $subResult)) {
							preg_match_all("/(\"{$targetData}\"\;s\:)([[:digit:]]{1,})(\:[\"][^\;]+)/", $sleepMemberRow['encryptData'], $ttt);
							$stringLen = $result[1][0] - 1;
							$sleepMemberRow['encryptData'] = preg_replace("/(\"{$targetData}\"\;s\:)([[:digit:]]{1,})(\:[\"][^\;]+)/", "\"{$targetData}\";s:{$stringLen}$3", $sleepMemberRow['encryptData']);
							if (unserialize($sleepMemberRow['encryptData'])) {
								$serializeFl = true;
							}
						}
					}
				}

				if (!$serializeFl) {
					foreach ($arrayTargetData as $targetData) {
						if (preg_match_all("/\"{$targetData}\"\;s\:([[:digit:]]{1,})\:\"?([^\;]+)/", $sleepMemberRow['encryptData'], $result)) {
							if (preg_match_all("/(\'|\"|\\\|\♥|\r\n|\r|\n|\^)+/", substr($result[2][0], 0, (strlen($result[2][0]) - 1)), $subResult)) {
								$stringLen = $result[1][0] - 2;
								$encryptData = preg_replace("/(\"{$targetData}\"\;s\:)([[:digit:]]{1,})(\:[\"][^\;]+)/", "\"{$targetData}\";s:{$stringLen}$3", $sleepMemberRow['encryptData']);
								if (unserialize($encryptData)) {
									$sleepMemberRow['encryptData'] = $encryptData;
								}
							}
						}
					}
				}
				
				if (unserialize($sleepMemberRow['encryptData'])) {
					$updateQuery = "Update es_memberSleep Set encryptData = '" . Encryptor::mysqlAesEncrypt($sleepMemberRow['encryptData']) . "' Where sleepNo=" . $sleepMemberRow['sleepNo'];

					if (!$db->query($updateQuery)) {
						echo '<div style="color:red">' . iconv('euc-kr', 'utf-8', '업데이트 실패 : ') . $updateQuery . '</div>';
						exit;
					}
					else {
						echo '<div style="color:blue">' . $sleepMemberRow['memId'] . ' : ' . iconv('euc-kr', 'utf-8', 'unserialize 실패 수정 업데이트 성공') . '</div>';
					}
				}
				else {
					echo '<div style="color:red">' . $sleepMemberRow['memId'] . ' : ' . iconv('euc-kr', 'utf-8', 'unserialize 실패 수정 업데이트 실패') . '</div>';
				}
			}
			
		}
		else if ($requestGetParmeter['mode'] == 'godomall5RelocationSleepMemberUpdate') {//24802
			$oldKeyPath = 'license_key_old.php';
			$oldKey = file($oldKeyPath);
			$oldKey = explode(PHP_EOL, $oldKey[2]);
			$oldKey = md5($oldKey[0]);

			$sleepMemberResult = $db->query("Select sleepNo, encryptData, memId From es_memberSleep order by sleepNo");
			while ($sleepMemberRow = $db->fetch($sleepMemberResult, 'array')) {
				$encryptData = Encryptor::mysqlAesDecrypt($sleepMemberRow['encryptData'], $oldKey);
				if (unserialize($encryptData)) {
					$updateQuery = "Update es_memberSleep Set encryptData = '" . Encryptor::mysqlAesEncrypt($encryptData) . "' Where sleepNo=" . $sleepMemberRow['sleepNo'];
					
					if (!$db->query($updateQuery)) {
						echo $updateQuery . '<br/>';
						exit;
					}
					else {
						echo '<div style="color:blue">' . iconv('euc-kr', 'utf-8', '업데이트 성공') . '</div>';
					}
				}
				else {
					$encryptData = Encryptor::mysqlAesDecrypt($sleepMemberRow['encryptData']);
					if (unserialize($encryptData)) {
						echo '<div style="color:blue">' . iconv('euc-kr', 'utf-8', '이미 업데이트 완료 된 휴면회원ID : ' . $sleepMemberRow['memId']) . '</div>';
					}
					else {
						echo '<div style="color:red">' . iconv('euc-kr', 'utf-8', '업데이트 되지 않은 휴면회원ID : ' . $sleepMemberRow['memId']) . '</div>';
					}
				}
			}
			unlink($oldKeyPath);
		}
		else if ($requestGetParmeter['mode'] == 'sleepMemberUpdateCheck') {
			$cnt = 0;
			$noCnt = 0;
			$noUpdateMember = array();
			$sleepMemberResult = $db->query("Select sleepNo, encryptData, memId From es_memberSleep order by sleepNo");
			while ($sleepMemberRow = $db->fetch($sleepMemberResult, 'array')) {
				$encryptData = Encryptor::mysqlAesDecrypt($sleepMemberRow['encryptData']);
				if (unserialize($encryptData)) {
					echo '<div style="color:blue">' . iconv('euc-kr', 'utf-8', '업데이트 완료 된 휴면회원ID : ' . $sleepMemberRow['memId']) . '</div>';
					$cnt++;
				}
				else {
					echo '<div style="color:red">' . iconv('euc-kr', 'utf-8', '업데이트 되지 않은 휴면회원ID : ' . $sleepMemberRow['memId']) . '</div>';
					$noUpdateMember[] = $sleepMemberRow['memId'];
					$noCnt++;
				}
			}
			echo '<div style="color:blue;font-weight:bold;">' . iconv('euc-kr', 'utf-8', '업데이트 완료 된 휴면회원 수 : ' . $cnt) . '</div>';
			echo '<div style="color:red;font-weight:bold;">' . iconv('euc-kr', 'utf-8', '업데이트 되지 않은 휴면 회원 수 : ' . $noCnt . ' : <br/>') . implode(', ', $noUpdateMember) . '</div>';
		}
		else if (strpos($requestGetParmeter['mode'], 'dataReplace') !== false) {
			$chDomain = explode('|', $requestGetParmeter['mode']);

			$changeBoardResult = $db->query("Select bdId From es_board order by sno");
			while ($boardRow = $db->fetch($changeBoardResult, 'array')) $updateQuery[] = "update es_bd_" . $boardRow['bdId'] . " set contents = replace(contents, 'http://" . $chDomain[1] . "' , '');";
			
			$updateQuery[] = "update es_goodsImage set imageName = replace(imageName, 'http://" . $chDomain[1] . "' , '');";
			$updateQuery[] = "update es_goods set goodsDescription = replace(goodsDescription, 'http://" . $chDomain[1] . "' , '');";

			foreach ($updateQuery as $dataQuery) {

				if (!$db->query($dataQuery)) {
					echo $dataQuery . '<br/>';
					exit;
				}
				else {
					echo '<div style="color:blue">' . iconv('euc-kr', 'utf-8', '업데이트 성공') . '</div>';
				}
			}
		}
		else if ($requestGetParmeter['mode'] == 'tableInit') {
			$arrayInsertTable = array(
				'es_board',
				'es_boardTheme',
				'es_buyerInform',
				'es_categoryTheme',
				'es_code',
				'es_config',
				'es_designBanner',
				'es_designBannerGroup',
				'es_designSliderBanner',
				'es_displayTheme',
				'es_displayThemeConfig',
				'es_displayThemeMobile',
				'es_excelForm',
				'es_manageDeliveryCompany',
				'es_manageGoodsIcon',
				'es_memberGroup',
				'es_orderDownloadForm',
				'es_scmDeliveryBasic',
				'es_scmDeliveryCharge',
				'es_scmManage',
				'es_smsContents',
			);
			$tableResult = $db->query("show tables");

			while ($tableRow = $db->fetch($tableResult, 'array')) {
				if (!preg_match('/^[tmp_]/i', $tableRow[0])) {
					echo 'truncate table ' . $tableRow[0] . ';';
				}
			}
		}
		else if ($requestGetParmeter['mode'] == 'godomall5MemberMileageUpdate') {
			$arrayMinusMaxDate = array();
			$arrayMinusMileage = array();
			$MemberResult = $db->query("Select a.memNo, a.mileage, totMileage, minusMileage From es_member a left join (Select sum(mileage) totMileage, memNo From es_memberMileage Group by memNo) b on a.memNo = b.memNo left join (Select sum(mileage) minusMileage, memNo From es_memberMileage Where mileage < 0 Group by memNo) c on a.memNo = c.memNo");
			while ($MemberRow = $db->fetch($MemberResult, 'array')) {
				$arrayMinusMaxDate[$MemberRow['memNo']] = $MemberRow['maxDate'];
				if($MemberRow['mileage'] == 0 && $MemberRow['totMileage'] == '') continue;
				
				$arrayMinusMileage[$MemberRow['memNo']] = ($MemberRow['minusMileage']) ? $MemberRow['minusMileage'] : 0;
					
				if($MemberRow['mileage'] != $MemberRow['totMileage']){
					$diffMileage = $MemberRow['mileage'] - $MemberRow['totMileage'];

					if($diffMileage < 0) $arrayMinusMileage[$MemberRow['memNo']] += $diffMileage;

					$contents = iconv('euc-kr', 'utf-8', "데이터 이전 : 사용가능 적립금과 적립금 내역 동기화 목적 추가 로그");
					
					$insertQuery = "Insert Into es_memberMileage Set memNo = '" . $MemberRow['memNo'] ."', handleCd = '', handleMode = 'm', reasonCd = '01005011', mileage = '" . $diffMileage . "', contents = '" . $contents . "', regDt = now(), deleteFl = 'n', deleteScheduleDt = '9999-12-31 00:00:00';";

					if (!$db->query($insertQuery)) {
							echo $insertQuery . '<br/>';
							exit;
						}
					else {
						echo '<div style="color:blue">' . iconv('euc-kr', 'utf-8', 'insert 성공') . '</div>';
					}
				}
			}
			
			$arrayBeforeMileage		= array();
			$arrayAfterMileage		= array();
			$targetFl				= array();
			$checkFl				= array();
			$arrayTotalusemileage	= array();
			$arrayHistory			= array();
			$changeMileageResult = $db->query("Select sno, memNo, mileage, regDt From es_memberMileage order by sno");
			while ($mileageRow = $db->fetch($changeMileageResult, 'array')) {

				$afterMileage = 0;
				$useHistory = '{}';

				if (!$arrayBeforeMileage[$mileageRow['memNo']]) $arrayBeforeMileage[$mileageRow['memNo']] = 0;
				
				$afterMileage = $arrayBeforeMileage[$mileageRow['memNo']] + $mileageRow['mileage'];
				
				if ($mileageRow['mileage'] > 0) $arrayAfterMileage[$mileageRow['memNo']] += $mileageRow['mileage'];
				
				
				if ($arrayAfterMileage[$mileageRow['memNo']] + $arrayMinusMileage[$mileageRow['memNo']] == 0) {
					if ($mileageRow['mileage'] < 0) {
						$deleteFl = 'n';
					}
					else {
						$deleteFl = 'complete';
					}
					$checkFl[$mileageRow['memNo']] = true;
				}
				else if (!$checkFl[$mileageRow['memNo']] && !$targetFl[$mileageRow['memNo']] && $arrayAfterMileage[$mileageRow['memNo']] + $arrayMinusMileage[$mileageRow['memNo']] > 0 && $arrayMinusMileage[$mileageRow['memNo']]) {
					$targetFl[$mileageRow['memNo']] = true;
					$deleteFl = 'use';

					if(empty($arrayTotalusemileage[$mileageRow['memNo']])){
						$arrayHistory['use'][0] = array('sno' => '', 'mileage' => str_replace('-', '', $arrayMinusMileage[$mileageRow['memNo']]));
						$arrayHistory['totalUseMileage'] = str_replace('-', '', $arrayMinusMileage[$mileageRow['memNo']]);
					}else{
						$arrayHistory['use'][0] = array('sno' => '', 'mileage' => str_replace('-', '', $arrayTotalusemileage[$mileageRow['memNo']]));
						$arrayHistory['totalUseMileage'] = str_replace('-', '', $arrayTotalusemileage[$mileageRow['memNo']]);
					}

					$useHistory	= json_encode($arrayHistory);
				}
				else if ($arrayAfterMileage[$mileageRow['memNo']] + $arrayMinusMileage[$mileageRow['memNo']] < 0) {
					$arrayTotalusemileage[$mileageRow['memNo']] = $arrayAfterMileage[$mileageRow['memNo']] + $arrayMinusMileage[$mileageRow['memNo']];
					if ($mileageRow['mileage'] < 0) {
						$deleteFl = 'n';
					}
					else {
						$deleteFl = 'complete';
					}
				}else{
					$deleteFl = 'n';

				}

				$updateQuery ="update es_memberMileage set beforeMileage = '" . $arrayBeforeMileage[$mileageRow['memNo']] . "', afterMileage = '" . $afterMileage . "', mileage = '" . $mileageRow['mileage'] . "', deleteFl = '" . $deleteFl . "', useHistory = '" . $useHistory . "' where sno = '" . $mileageRow['sno'] . "';";
				
				if (!$db->query($updateQuery)) {
						echo $updateQuery . '<br/>';
						exit;
					}
				else {
					echo '<div style="color:blue">' . iconv('euc-kr', 'utf-8', 'update 성공') . '</div>';
				}
				
				$arrayBeforeMileage[$mileageRow['memNo']] = $afterMileage;
			}
		}
		else {
			header("Content-type: text/xml; charset=EUC-KR");
			echo '<?xml version="1.0" encoding="EUC-KR"?>';
			
			$requestParmeter	= Request::post()->all();
			
			echo '<Root>';
			switch ($requestParmeter['mode']) {	
				case 'getFieldName' :
					foreach ($requestParmeter['tableName'] as $tableName) {
						$testResult = $db->query("show create table " . $tableName);
						$createTableRow = $db->fetch($testResult, 'array');
						
						preg_match_all('/^\s+`([_a-zA-Z0-9-]+)` (.+)\n/m', $createTableRow['Create Table'], $matches);
						
						$length	= count($matches[0]);
						echo '<requestTable name="' . $tableName . '">';
							for($i = 0; $i < $length; $i++) {
								$fieldname	= $matches[1][$i];
							?>
								<fieldName><?=urlencode(stripslashes($fieldname))?></fieldName>
							<?php
							
							}
						echo '</requestTable>';
					}
					break;
				case 'boardDefaultSkin' :
					$configDataResult = $db->query("Select `data` From es_config Where groupCode='design' And code='skin'");
					list($skinConfig) = $db->fetch($configDataResult, 'row');
					$arraySkinConfig = json_decode($skinConfig);
					$frontLive = $arraySkinConfig->frontLive;
					$mobileLive = $arraySkinConfig->mobileLive;
					$frontDefaultSkinResult = $db->query("Select sno, themeId From es_boardTheme Where liveSkin='" . $frontLive . "' and bdMobileFl = 'n'");
					echo '<frontDefaultSkin>';
					while ($frontDefaultSkinRow = $db->fetch($frontDefaultSkinResult, 'array')) {
					?>
						<<?=$frontDefaultSkinRow['themeId']?>><?=$frontDefaultSkinRow['sno']?></<?=$frontDefaultSkinRow['themeId']?>>
					<?php
					}
					echo '</frontDefaultSkin>';
					$mobileDefaultSkinResult = $db->query("Select sno, themeId From es_boardTheme Where liveSkin='" . $mobileLive . "' and bdMobileFl = 'y'");
					echo '<mobileDefaultSkin>';
					while ($mobileDefaultSkinRow = $db->fetch($mobileDefaultSkinResult, 'array')) {
					?>
						<<?=$mobileDefaultSkinRow['themeId']?>><?=$mobileDefaultSkinRow['sno']?></<?=$mobileDefaultSkinRow['themeId']?>>
					<?php
					}
					echo '</mobileDefaultSkin>';
					break;
				case 'getTableConfig' :
					$groupCode	= $requestParmeter['groupCode'];
					$code		= $requestParmeter['code'];

					$configDataResult = $db->query("Select data From es_config Where groupCode='" . $groupCode . "' And code='" . $code . "'");

					while($configDataRow = $db->fetch($configDataResult, 'array')) {
						?>
							<configData><?=urlencode(stripslashes($configDataRow['data']))?></configData>
						<?php
					}
					
					
					break;
				case 'memberCheck' :
					if ($requestParmeter['memberDeleteFlag']) {
						$memberDeleteNo = ($requestParmeter['memberDeleteNo'] === '') ? $requestParmeter['memberDeleteNo'] : '0';
						$memberQuery = "Select memNo, memId From es_member Where memNo <= " . $memberDeleteNo . " order by entryDt";
					}
					else {
						$memberQuery = "Select memNo, memId From es_member order by entryDt";
					}
					$memberResult = $db->query($memberQuery);
					while ($memberRow = $db->fetch($memberResult, 'array')) {
						?>
							<memberData memNo="<?=$memberRow['memNo']?>"><![CDATA[<?=urlencode(iconv('UTF8', 'EUCKR', $memberRow['memId']))?>]]></memberData>
						<?php
					}
					break;
				case 'getGoodsRelationData' :
					$createTableRow = $db->fetch($db->query("Show Create Table es_categoryGoods"), 'array');
					?>
						<dataRow>
							<result>1</result>
							<query><?=urlencode(stripslashes(iconv('UTF8', 'EUCKR', $createTableRow['Create Table'])))?></query>
						</dataRow>
					<?php
					$createTableRow = $db->fetch($db->query("Show Create Table es_categoryBrand"), 'array');
					?>
						<dataRow>
							<result>1</result>
							<query><?=urlencode(stripslashes(iconv('UTF8', 'EUCKR', $createTableRow['Create Table'])))?></query>
						</dataRow>
					<?php
					$createTableRow = $db->fetch($db->query("Show Create Table es_scmManage"), 'array');
					?>
						<dataRow>
							<result>1</result>
							<query><?=urlencode(stripslashes(iconv('UTF8', 'EUCKR', str_replace('json', 'text', $createTableRow['Create Table']))))?></query>
						</dataRow>
					<?php
					$createTableRow = $db->fetch($db->query("Show Create Table es_scmDeliveryBasic"), 'array');
					?>
						<dataRow>
							<result>1</result>
							<query><?=urlencode(stripslashes(iconv('UTF8', 'EUCKR', $createTableRow['Create Table'])))?></query>
						</dataRow>
					<?php
					$createTableRow = $db->fetch($db->query("Show Create Table es_scmDeliveryCharge"), 'array');
					?>
						<dataRow>
							<result>1</result>
							<query><?=urlencode(stripslashes(iconv('UTF8', 'EUCKR', $createTableRow['Create Table'])))?></query>
						</dataRow>
					<?php
					$createTableRow = $db->fetch($db->query("Show Create Table es_addGoods"), 'array');
					?>
						<dataRow>
							<result>1</result>
							<query><?=urlencode(stripslashes(iconv('UTF8', 'EUCKR', $createTableRow['Create Table'])))?></query>
						</dataRow>
					<?php
					$selectCategoryResult = $db->query("Select * From es_categoryGoods order by sno");
					while ($selectCategoryRow = $db->fetch($selectCategoryResult, 'array')) {
						$arrayString = array();
						
						foreach ($selectCategoryRow as $fieldName => $value) {
							if (!preg_match( '/^[0-9]{1,}+/', $fieldName)) {
								$arrayString[] = "$fieldName = '" . addslashes(iconv('UTF8', 'EUCKR', $value)) . "'";
							}
							
						}
						?>
							<dataRow>
								<result>1</result>
								<query><?=urlencode("Insert Into es_categoryGoods set " . implode(" , ", $arrayString))?></query>
							</dataRow>
						<?php
					}
						
					$selectBrandResult = $db->query("Select * From es_categoryBrand order by sno");
					while ($selectBrandRow = $db->fetch($selectBrandResult, 'array')) {
						$arrayString = array();
						
						foreach ($selectBrandRow as $fieldName => $value) {
							if (!preg_match( '/^[0-9]{1,}+/', $fieldName)) {
								$arrayString[] = "$fieldName = '" . addslashes(iconv('UTF8', 'EUCKR', $value)) . "'";
							}
						}
						?>
							<dataRow>
								<result>1</result>
								<query><?=urlencode("Insert Into es_categoryBrand set " . implode(" , ", $arrayString))?></query>
							</dataRow>
						<?php
					}
					
					$selectScmManageResult = $db->query("Select * From es_scmManage order by scmNo");
					while ($selectScmManageRow = $db->fetch($selectScmManageResult, 'array')) {
						$arrayString = array();
						
						foreach ($selectScmManageRow as $fieldName => $value) {
							if (!preg_match( '/^[0-9]{1,}+/', $fieldName)) {
								$arrayString[] = "$fieldName = '" . addslashes(iconv('UTF8', 'EUCKR', $value)) . "'";
							}
						}
						?>
							<dataRow>
								<result>1</result>
								<query><?=urlencode("Insert Into es_scmManage set " . implode(" , ", $arrayString))?></query>
							</dataRow>
						<?php
					}

					$selectDeliveryBasicResult = $db->query("Select * From es_scmDeliveryBasic order by sno");
					while ($selectDeliveryBasicRow = $db->fetch($selectDeliveryBasicResult, 'array')) {
						$arrayString = array();
						
						foreach ($selectDeliveryBasicRow as $fieldName => $value) {
							if (!preg_match( '/^[0-9]{1,}+/', $fieldName)) {
								$arrayString[] = "$fieldName = '" . addslashes(iconv('UTF8', 'EUCKR', $value)) . "'";
							}
						}
						?>
							<dataRow>
								<result>1</result>
								<query><?=urlencode("Insert Into es_scmDeliveryBasic set " . implode(" , ", $arrayString))?></query>
							</dataRow>
						<?php
					}

					$selectDeliveryChargeResult = $db->query("Select * From es_scmDeliveryCharge order by sno");
					while ($selectDeliveryChargeRow = $db->fetch($selectDeliveryChargeResult, 'array')) {
						$arrayString = array();
						
						foreach ($selectDeliveryChargeRow as $fieldName => $value) {
							if (!preg_match( '/^[0-9]{1,}+/', $fieldName)) {
								$arrayString[] = "$fieldName = '" . addslashes(iconv('UTF8', 'EUCKR', $value)) . "'";
							}
						}
						?>
							<dataRow>
								<result>1</result>
								<query><?=urlencode("Insert Into es_scmDeliveryCharge set " . implode(" , ", $arrayString))?></query>
							</dataRow>
						<?php
					}
					
					break;

				case 'getSelectTableRow' : 
					$createTableRow = $db->fetch($db->query("Show Create Table " . $requestParmeter['selectTable']), 'array');
					?>
						<dataRow>
							<result>1</result>
							<query><?=urlencode(stripslashes(iconv('UTF8', 'EUCKR', $createTableRow['Create Table'])))?></query>
						</dataRow>
					<?php

					$selectTableResult = $db->query("Select * From " . $requestParmeter['selectTable'] . " order by " . $requestParmeter['orderByField']);
					while ($selectTableRow = $db->fetch($selectTableResult, 'array')) {
						$arrayString = array();
						
						foreach ($selectTableRow as $fieldName => $value) {
							if (!preg_match( '/^[0-9]{1,}+/', $fieldName)) {
								if ($value != 'null') {
									$arrayString[] = "$fieldName = '" . addslashes(iconv('UTF8', 'EUCKR', $value)) . "'";
								}
								else {
									$arrayString[] = "$fieldName = " . iconv('UTF8', 'EUCKR', $value) . "";
								}
							}
						}
						?>
							<dataRow>
								<result>1</result>
								<query><?=urlencode("Insert Into " . $requestParmeter['selectTable'] . " set " . implode(" , ", $arrayString))?></query>
							</dataRow>
						<?php
					}
					break;
				case 'getDeliveryAddSno' :
					$deliverySno = 0;
					$deliveryBasicResult = $db->query("Select min(sno) From es_scmDeliveryBasic Where collectFl = 'later' and fixFl = 'price'");
					list($deliverySno) = $db->fetch($deliveryBasicResult, 'row');
					$comment = iconv('EUCKR', 'UTF8', '기본 - 착불금액별배송비');
					if (!$deliverySno) {
						$db->query("Insert Into es_scmDeliveryBasic Set managerNo=1, scmNo=1, method='" . $comment . "', description='" . $comment . "', deleteFl='y', defaultFl='y', collectFl='later', fixFl='price', freeFl='n', goodsDeliveryFl='n', areaFl='n', regDt=now()");
						$deliverySno = $db->insert_id();
						$db->query("Insert Into es_scmDeliveryCharge Set scmNo=1, basicKey=" . $deliverySno .", regDt=now()");
					}
					?>
						<deliverySno><?=$deliverySno?></deliverySno>
					<?php
					break;
				case 'getOrderTempNo' :
					$tempOrderNoResult = $db->query("Select sno, enamooOrderNo, godo5OrderNo, regDt From tmp_orderno");
					if ($tempOrderNoResult) {
						echo '<tempOrderNoResult>1</tempOrderNoResult>';
						while ($tempOrderNoRow = $db->fetch($tempOrderNoResult, 'array')) {
							echo '<godo5OrderNo sno = "' . $tempOrderNoRow['sno'] . '" enamooOrderNo="' . $tempOrderNoRow['enamooOrderNo'] . '" regDt = "' . $tempOrderNoRow['regDt'] . '">' . $tempOrderNoRow['godo5OrderNo'] . '</godo5OrderNo>';
						}
					}
					else {
						echo '<tempOrderNoResult>0</tempOrderNoResult>';
					}
					break;
				case 'getRelocationOrderTempNo' :
					$tempOrderNoResult = $db->query("Select originalOrderNo, godo5OrderNo From tmp_orderno");
					if ($tempOrderNoResult) {
						echo '<tempOrderNoResult>1</tempOrderNoResult>';
						while ($tempOrderNoRow = $db->fetch($tempOrderNoResult, 'array')) {
							echo '<godo5OrderNo originalOrderNo="' . $tempOrderNoRow['originalOrderNo'] . '">' . $tempOrderNoRow['godo5OrderNo'] . '</godo5OrderNo>';
						}
					}
					else {
						echo '<tempOrderNoResult>0</tempOrderNoResult>';
					}
					break;
				case 'getBoardList' : 
					$boardListResult = $db->query("Select bdId From es_board");

					while($boardListRow = $db->fetch($boardListResult, 'array')) {
						?>
							<boardList><?=urlencode(stripslashes($boardListRow['bdId']))?></boardList>
						<?php
					}
					break;
				default :
					break;
			}
			echo '</Root>';
		}
		$requestData = ['shopId' => getenv('SERVER_NAME')] ;
		$this->postCurl('http://relocation.godo.co.kr/_systool/company_relocation/_upload_check.php', $requestData);
		exit;
		/*
		$testResult = $db->query("select password('test')");
		
		*/
	}

	function array_diff_assoc_ignorecase($array1 , $array2)
	{
		$ar2_keys	= array();
		foreach($array2 as $key => $value)
		{
			$ar2_keys[strtolower($key)]	= $value;
		}

		foreach($array1 as $key => $value)
		{
			if(str_replace('varchar','char',$ar2_keys[strtolower($key)]) == str_replace('varchar', 'char', $value))
			{
				unset($array1[$key]);
			}
		}

		return $array1;
	}

	function replace_emptydefault($value)
	{
		$value = trim(str_replace("default ''","",$value));
		$value = trim(str_replace("default '0'","",$value));
		$value = trim(str_replace("default '0000-00-00'","",$value));
		$value = trim(str_replace("default '0000-00-00 00:00:00'","",$value));

		return $value;
	}

	function xmlPrint($msg) {
		echo '<msg>' . $msg . '</msg>';
	}

	function postCurl($url, $param) {	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_URL, $url);	
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  1);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  

		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);  
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);	

		$content = curl_exec($ch);
		return $content;
	}
}
?>