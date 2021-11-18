/**
 * 추가 스크립트 - 추가적인 javascript 는 여기에 작성을 해주세요.
 *
 */
$(function(){
    
    //211117 디자인위브 mh 협력사 검색
    $('.js-artners-check').on('click', function(e){
        e.preventDefault();
        var _this = this;
        $('input[name="scmNmSearch"]').css('border-color', '#D5D5D5');
        var scmNmSearch = $('input[name="scmNmSearch"]').val();
        
        //이전 검색에서 input값에 scmNo가 있으면 제거 후 추가
        if($('.hideScm').length > 0){
            $('.hideScm').remove();
        }
        
        $.ajax({
            url : '../partners/partners_ps.php',
            type : 'post',
            data : {
                'mode' : 'checkScmManage',
                'managerId' : scmNmSearch
            },
            success : function(data){

                if(data.result == '0'){
                    
                    $('input[name="scmNmSearch"]').css('border-color', '#117EF9');

                    $(_this).closest('form').append('<input type="hidden" class="hideScm" name="scmNo[]" value="'+data.data.scmNo+'">');
                    $(_this).closest('form').append('<input type="hidden" class="hideScm" name="scmNoNm[]" value="'+data.data.companyNm+'">');
                }else if(data.result == '1'){
                    alert(data.msg);
                }
            }
        });
    });
    
    //211117 디자인위브 mh 협력사 접기/펼치기
    $('.js-partners-toggle').click(function(e){
        if($('.partners-tg').hasClass('dn')){
            
            $('.partners-tg').removeClass('dn');
            
            // 검색 결과 없을때 colspan값 맞춰주기
            if($('.no-data').length > 0){
                var thLength = $('thead tr th').length;
                $('.no-data').attr('colspan', thLength);
            }
        }else{
            
            $('.partners-tg').addClass('dn');
            
            // 검색 결과 없을때 colspan값 맞춰주기
            if($('.no-data').length > 0){
                var thLength = $('thead tr th').not('.partners-tg').length;
                $('.no-data').attr('colspan', thLength);
            }
        }

    });
});