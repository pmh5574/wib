/**
 * 추가 스크립트 - 추가적인 javascript 는 여기에 작성을 해주세요.
 *
 */
$(function(){
    //협력사 검색
    $('.js-artners-check').on('click', function(e){
        e.preventDefault();
        $('input[name="scmNmSearch"]').css('border-color', '#D5D5D5');
        var scmNmSearch = $('input[name="scmNmSearch"]').val();
        
        $.ajax({
            url : '../partners/partners_ps.php',
            type : 'post',
            data : {
                'mode' : 'checkScmManage',
                'managerId' : scmNmSearch
            },
            success : function(data){

                if(data.result == '1'){
                    alert(data.msg);
                }else{
                    $('input[name="scmNmSearch"]').css('border-color', '#137FD8');
                }
            }
        });
    });
    
    $('.js-partners-toggle').click(function(e){
        if($('.partners-tg').hasClass('dn')){
            $('.partners-tg').removeClass('dn');
            if($('.no-data').length > 0){
                var thLength = $('thead tr th').length;
                $('.no-data').attr('colspan', thLength);
            }
        }else{
            $('.partners-tg').addClass('dn');
            if($('.no-data').length > 0){
                var thLength = $('thead tr th').not('.partners-tg').length;
                $('.no-data').attr('colspan', thLength);
            }
        }

    });
});