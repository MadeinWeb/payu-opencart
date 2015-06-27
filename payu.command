cd "$(dirname "$BASH_SOURCE")" || {
    echo "Error getting script directory" >&2
    exit 1
}
COPYFILE_DISABLE=1 tar -czvf payu.tar.gz catalog/view/javascript/jquery.mask.js catalog/view/theme/default/template/payment/payu.tpl catalog/view/javascript/payu.js catalog/view/javascript/jquery.mask.js catalog/model/payment/payu.php admin/controller/payment/payu.php catalog/language/portuguese-br/payment/payu.php catalog/language/english/payment/payu.php catalog/controller/payment/payu.php admin/view/template/payment/payu.tpl admin/language/english/payment/payu.php admin/language/portuguese-br/payment/payu.php admin/view/image/payment/payu.png system/helper/payu.php admin/model/payment/payu.php