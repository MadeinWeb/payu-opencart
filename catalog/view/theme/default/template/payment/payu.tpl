<h3><?php echo $payu_title; ?></h3>
<br/>
<form id="card-form" action="#" method="POST">
    <input class='tab' type="hidden" name="method" checked='checked' value="cc">
    <div id="cc" class='tab-c'>
        <?php echo $entry_cc_name ?>:<br>
        <input name="cc_name" class="large-field" type="text" size="20" value="">
        <br>
        <br>
        <?php echo $entry_cc_doc ?>:<br>
        <input name="cc_doc" class="large-field" type="text" size="20" value="">
        <br>
        <br>
        <?php echo $entry_cc_number ?>:<br>
        <input name="cc_number" class="large-field" type="text" size="20" value="">
        <br>
        <br>
        <?php echo $entry_cc_flag ?>:<br>
        <select name="cc_flag" class="" >
            <option value=""><?php echo $empty_flag; ?></option>
            <?php foreach($flags as $flag){ ?>
            <option value="<?php echo $flag['value']; ?>"><?php echo $flag['text']; ?></option>
            <?php } ?>
        </select>
        <br>
        <br>
        <?php echo $entry_cc_securityCode ?>: <span class="tooltip" style="font-style: italic;">O que é isto?</span>
        <div class="tooltip" style="display:none; position: absolute; z-index: 10; background-color: white; border: solid 2px #eee; padding: 5px 9px; width: 180px; top: 5px; left: 110px;">O CVV é o codigo de segurança, geralmente de 3 digitos que fica no verso do cartão, com exceção dos cartões American Express, que possuem 4 digitos e esta situado na face do cartão.</div><br>
        <input name="cc_securityCode" class="small-field" type="text" maxlength="4" size="4" value="">
        <br>
        <br>
        <?php echo $entry_cc_expirationDate ?>:<br>
        <input name="cc_expirationDate" class="small-field" type="text" size="2" value="">
        <input name="cc_expirationMonth" type="hidden">
        <input name="cc_expirationYear" type="hidden">
        <br>
        <br>
        <?php echo $entry_cc_installments ?>:<br>
        <select name="cc_installments" class="" >
            <?php for($i = 1; $i <= $cc_maxInstallments; $i++){ ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php } ?>
        </select>
    </div>
    <br>
    <input class="button" type='button' id='send-payment' value='<?php echo $button_confirm; ?>' />
</form>

<script src="/catalog/view/javascript/payu.js"></script>
<script src="/catalog/view/javascript/jquery.mask.js"></script>
<script>
    $(function(){

        $('#send-payment').on('click', send_payment);

        var luhn_valid = false;

        // se descobrir a bandeira do cartão, seleciona ela.
        $('[name=cc_number]').validateCreditCard(function(result){
            $('[name=cc_number]').val(result.number);
            if(result.card_type != null){
                $('[name=cc_flag] option').each(function(){
                    if($(this).val() == result.card_type.name.toUpperCase()){
                        $(this).parent().val(result.card_type.name.toUpperCase());
                    };
                });
                luhn_valid = result.luhn_valid
            }else{
                $('[name=cc_flag]').val('');
            }
        });

        //tool tip cvv
        $('span.tooltip').on('mouseover', function(e){
            $(this).next('.tooltip').css({ left: e.pageX, top: e.pageY }).show();
        }).on('mouseout', function(){
            $(this).next('.tooltip').hide();
        });

        //mascaras
        var options = {removeIfNotMatch: true};
        // use $.extend(options, {new: 'properties'}) para adicionar propriedades especificas.
        //$('[name=cc_doc]').mask('000.000.000-00', options);
        $('[name=cc_expirationDate]').mask('00/00', options);

    });

    function validateCpf(cpf) {
        return true;
        cpf = cpf.replace(/[^\d]+/g,'');
        if(cpf == '') return false;
        // Elimina CPFs invalidos conhecidos
        if (cpf.length != 11 ||
                cpf == "00000000000" ||
                cpf == "11111111111" ||
                cpf == "22222222222" ||
                cpf == "33333333333" ||
                cpf == "44444444444" ||
                cpf == "55555555555" ||
                cpf == "66666666666" ||
                cpf == "77777777777" ||
                cpf == "88888888888" ||
                cpf == "99999999999")
            return false;
        // Valida 1o digito
        var add = 0;
        var rev;
        for (i=0; i < 9; i ++)
            add += parseInt(cpf.charAt(i)) * (10 - i);  rev = 11 - (add % 11);
        if (rev == 10 || rev == 11)
            rev = 0;
        if (rev != parseInt(cpf.charAt(9)))
            return false;
        // Valida 2o digito
        add = 0;
        for (i = 0; i < 10; i ++)
            add += parseInt(cpf.charAt(i)) * (11 - i);  rev = 11 - (add % 11);
        if (rev == 10 || rev == 11)
            rev = 0;
        if (rev != parseInt(cpf.charAt(10)))
            return false;
        return true;
    }

    function send_payment(){
        luhn_valid = false;
        error = [];
        $('.warning, .error').remove();

        // valida os dados antes de enviar
        if($('[name=cc_flag]').val() == ''){
            error.push({field: 'cc_flag', error: '<?php echo $required_field; ?>'});
        }

//    if($('[name=cc_flag]').val() != 'ELO' && $('[name=cc_flag]').val() != '' && $('[name=cc_number]').val() != ''){
//        if(!luhn_valid){
//            error.push({field: 'cc_number', error: '<?php echo $invalid_number; ?>'});
//        }
//    }

        if($('[name=cc_number]').val() == ''){
            error.push({field: 'cc_number', error: '<?php echo $required_field; ?>'});
        }

        if($('[name=cc_name]').val() == ''){
            error.push({field: 'cc_name', error: '<?php echo $required_field; ?>'});
        }

        if($('[name=cc_doc]').val() == ''){
            error.push({field: 'cc_doc', error: '<?php echo $required_field; ?>'});
        }else if(!validateCpf($('[name=cc_doc]').val())){
            error.push({field: 'cc_doc', error: '<?php echo $invalid_doc; ?>'});
        }

        if(!($('[name=cc_securityCode]').val().length >= 3 && $('[name=cc_securityCode]').val().length <= 4)){
            error.push({field: 'cc_securityCode', error: '<?php echo $invalid_cvv; ?>'});
        }

        if($('[name=cc_expirationDate]').val() == ''){
            error.push({field: 'cc_expirationDate', error: '<?php echo $required_field_month_year_format; ?>'});
        }else{
            date = $('[name=cc_expirationDate]').val().split('/');
            $('[name=cc_expirationMonth]').val(date[0]);
            $('[name=cc_expirationYear]').val('20' + date[1]);
        }

        // se não tiver nenhum erro continua
        if(error.length){
            for(key in error){
                $('#card-form [name='+error[key].field+']').after('<span class="error">' + error[key].error + '</span>');
            }
            return false;
        }

        $.ajax({
            url: 'index.php?route=payment/payu/send',
            type: 'post',
            data: $('#card-form').serialize(),
            dataType: 'json',
            beforeSend: function() {
            },
            complete: function() {
            },
            success: function(json) {
                $('.warning, .error').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    $('.payment').append('<div class="warning" style="display: none; margin-top: 20px;">' + json['error'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                    $('.warning').fadeIn('fast');
                } else {
                    location = json['success'];
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
</script>