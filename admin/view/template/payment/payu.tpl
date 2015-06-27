<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
<div class="heading">
    <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
    </div>
</div>
<div class="content">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
<table class="form">
<tr>
    <td><?php echo $entry_status; ?></td>
    <td><select name="payu_status">
        <?php if ($payu_status) { ?>
        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
        <option value="0"><?php echo $text_disabled; ?></option>
        <?php } else { ?>
        <option value="1"><?php echo $text_enabled; ?></option>
        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
        <?php } ?>
    </select></td>
</tr>
<!-- sort order -->
<tr>
    <td><?php echo $entry_sort_order; ?></td>
    <td><input type="text" name="payu_sort_order" value="<?php echo $payu_sort_order; ?>" size="5" />
        <?php if ($error_sort_order) { ?>
        <span class="error"><?php echo $error_sort_order; ?></span>
        <?php } ?>
    </td>
</tr>
<!-- Sandbox -->
<tr>
    <td><?php echo $entry_test; ?></td>
    <td><?php if ($payu_test) { ?>
        <input type="radio" name="payu_test" value="1"  checked="checked"/>
        <?php echo $text_yes; ?>
        <input type="radio" name="payu_test" value="0" />
        <?php echo $text_no; ?>
        <?php } else { ?>
        <input type="radio" name="payu_test" value="1" />
        <?php echo $text_yes; ?>
        <input type="radio" name="payu_test" value="0" checked="checked"/>
        <?php echo $text_no; ?>
        <?php } ?>
    </td>
</tr>
<tr>
    <td colspan="2"></td>
</tr>
<!-- merchantId -->
<tr>
    <td><span class="required">*</span> <?php echo $entry_merchantId; ?></td>
    <td><input type="text" name="payu_merchantId" value="<?php echo $payu_merchantId; ?>" size="40" />
        <?php if ($error_merchantId) { ?>
        <span class="error"><?php echo $error_merchantId; ?></span>
        <?php } ?>
    </td>
</tr>
<!-- apiLogin -->
<tr>
    <td><span class="required">*</span> <?php echo $entry_apiLogin; ?></td>
    <td><input type="text" name="payu_apiLogin" value="<?php echo $payu_apiLogin; ?>" size="40" />
        <?php if ($error_apiLogin) { ?>
        <span class="error"><?php echo $error_apiLogin; ?></span>
        <?php } ?>
    </td>
</tr>
<!-- apiKey -->
<tr>
    <td><span class="required">*</span> <?php echo $entry_apiKey; ?></td>
    <td><input type="text" name="payu_apiKey" value="<?php echo $payu_apiKey; ?>" size="40" />
        <?php if ($error_apiKey) { ?>
        <span class="error"><?php echo $error_apiKey; ?></span>
        <?php } ?>
    </td>
</tr>
<!-- accountId -->
<tr>
    <td><span class="required">*</span> <?php echo $entry_accountId; ?></td>
    <td><input type="text" name="payu_accountId" value="<?php echo $payu_accountId; ?>" size="40" />
        <?php if ($error_accountId) { ?>
        <span class="error"><?php echo $error_accountId; ?></span>
        <?php } ?>
    </td>
</tr>
<!-- minimumTotal -->
<tr>
    <td><?php echo $entry_total; ?></td>
    <td><input type="text" name="payu_total" value="<?php echo $payu_total; ?>" /></td>
</tr>
<!-- maxInstallments -->
<tr>
    <td><span class="required">* </span> <?php echo $entry_maxInstallments; ?></td>
    <td><select name="payu_maxInstallments">
        <?php for ($i = 1; $i <= 12; $i++) { ?>
            <?php if ($payu_maxInstallments == $i) { ?>
            <option value="<?php echo $i ?>" selected="selected"><?php echo $i; ?></option>
            <?php } else { ?>
            <option value="<?php echo $i ?>"><?php echo $i; ?></option>
            <?php } ?>
        <?php } ?>
    </select>
    <?php if ($error_maxInstallments) { ?>
    <span class="error"><?php echo $error_maxInstallments; ?></span>
    <?php } ?>
    </td>
</tr>
<!-- Geolocalization -->
<tr>
    <td><?php echo $entry_geo_zone; ?></td>
    <td><select name="payu_geo_zone_id">
        <option value="0"><?php echo $text_all_zones; ?></option>
        <?php foreach ($geo_zones as $geo_zone) { ?>
        <?php if ($geo_zone['geo_zone_id'] == $payu_geo_zone_id) { ?>
        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
        <?php } ?>
        <?php } ?>
      </select></td>
</tr>
<!--Statuses config -->
<tr>
    <td><?php echo $entry_approved_status; ?></td>
    <td><select name="payu_approved_status">
        <?php foreach ($order_statuses as $order_status) { ?>
        <?php if ($order_status['order_status_id'] == $payu_approved_status) { ?>
        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
        <?php } ?>
        <?php } ?>
    </select></td>
</tr>
<tr>
    <td><?php echo $entry_processed_status; ?></td>
    <td><select name="payu_processed_status">
        <?php foreach ($order_statuses as $order_status) { ?>
        <?php if ($order_status['order_status_id'] == $payu_processed_status) { ?>
        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
        <?php } ?>
        <?php } ?>
    </select></td>
</tr>
<tr>
    <td><?php echo $entry_rejected_status; ?></td>
    <td><select name="payu_rejected_status">
        <?php foreach ($order_statuses as $order_status) { ?>
        <?php if ($order_status['order_status_id'] == $payu_rejected_status) { ?>
        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
        <?php } ?>
        <?php } ?>
    </select></td>
</tr>
</table>
</form>
</div>
</div>


<?php echo $footer; ?>