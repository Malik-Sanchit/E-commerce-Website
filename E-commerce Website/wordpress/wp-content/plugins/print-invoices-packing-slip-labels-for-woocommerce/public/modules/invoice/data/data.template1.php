<div class="wfte_rtl_main wfte_invoice-main">
  <div class="wfte_invoice-header clearfix">
      <div class="wfte_invoice-header_top clearfix">
        <div class="wfte_company_logo float_left">
          <div class="wfte_company_logo_img_box">
            <img src="[wfte_company_logo_url]" class="wfte_company_logo_img">
          </div>
          <div class="wfte_company_name wfte_hidden">[wfte_company_name]</div>
          <div class="wfte_company_logo_extra_details">__[]__</div>
        </div>
        <div class="wfte_addrss_fields wfte_from_address float_right wfte_text_left">
          <div class="wfte_address-field-header wfte_from_address_label">__[From Address:]__</div>
          <div class="wfte_from_address_val">[wfte_from_address]</div>
        </div>
      </div>
      <div class="wfte_addrss_field_main clearfix">        
         <div class="wfte_invoice_data float_left">
            <div class="wfte_invoice_number">
              <span class="wfte_invoice_number_label">__[INVOICE:]__</span> [wfte_invoice_number]
            </div>
            <div class="wfte_order_number">
              <span class="wfte_order_number_label">__[Order No:]__</span> [wfte_order_number]
            </div>
            <div class="wfte_invoice_date" data-invoice_date-format="d/M/Y">
              <span class="wfte_invoice_date_label">__[Invoice Date:]__</span> [wfte_invoice_date]
            </div>
            <div class="wfte_order_date" data-order_date-format="m/d/Y">
              <span class="wfte_order_date_label">__[Date:]__</span> [wfte_order_date]
            </div>
            <div class="wfte_email">
              <span class="wfte_email_label">__[Email:]__</span>
              <span>[wfte_email]</span>
            </div>
            <div class="wfte_tel">
              <span class="wfte_tel_label">__[Phone number:]__</span>
              <span>[wfte_tel]</span>
            </div>
            <div class="wfte_order_item_meta">[wfte_order_item_meta]</div>
            [wfte_extra_fields]
          </div>
         <div class="wfte_addrss_fields wfte_billing_address float_left wfte_text_left">
           <div class="wfte_address-field-header wfte_billing_address_label">__[Billing Address:]__</div>
           [wfte_billing_address]
         </div>
         <div class="wfte_addrss_fields wfte_shipping_address float_right wfte_text_left">
           <div class="wfte_address-field-header wfte_shipping_address_label">__[Shipping Address:]__</div>
           [wfte_shipping_address]
         </div>
      </div>
  </div>
  <div class="wfte_invoice-body clearfix">
    [wfte_product_table_start]
    <table class="wfte_product_table">
        <thead class="wfte_product_table_head wfte_table_head_color wfte_product_table_head_bg wfte_text_center">
          <tr>
            <th class="wfte_product_table_head_sku" col-type="sku">__[SKU]__</th>
            <th class="wfte_product_table_head_product" col-type="product">__[Product]__</th>
            <th class="wfte_product_table_head_quantity" col-type="quantity">__[Quantity]__</th>
            <th class="wfte_product_table_head_price" col-type="price">__[Price]__</th>
            <th class="wfte_product_table_head_total_price wfte_right_column" col-type="total_price">__[Total Price]__</th>     
          </tr>
        </thead>
        <tbody class="wfte_product_table_body wfte_table_body_color">
        </tbody>
      </table>   
  [wfte_product_table_end]   
  <table class="wfte_payment_summary_table wfte_product_table">
    <tbody class="wfte_payment_summary_table_body wfte_table_body_color">
      <tr class="wfte_payment_summary_table_row wfte_product_table_subtotal">
        <td class="wfte_product_table_subtotal_label wfte_text_right">__[Subtotal]__</td>
        <td class="wfte_right_column">[wfte_product_table_subtotal]</td>
      </tr>
      <tr class="wfte_payment_summary_table_row wfte_product_table_shipping">
        <td class="wfte_product_table_shipping_label wfte_text_right">__[Shipping]__</td>
        <td class="wfte_right_column">[wfte_product_table_shipping]</td>
      </tr>
      <tr class="wfte_payment_summary_table_row wfte_product_table_cart_discount">
        <td class="wfte_product_table_cart_discount_label wfte_text_right">__[Cart Discount]__</td>
        <td class="wfte_right_column">[wfte_product_table_cart_discount]</td>
      </tr>
      <tr class="wfte_payment_summary_table_row wfte_product_table_order_discount">
        <td class="wfte_product_table_order_discount_label wfte_text_right">__[Order Discount]__</td>
        <td class="wfte_right_column">[wfte_product_table_order_discount]</td>
      </tr>
      <tr data-row-type="wfte_tax_items" class="wfte_payment_summary_table_row wfte_product_table_tax_item">
        <td class="wfte_product_table_tax_item_label wfte_text_right">[wfte_product_table_tax_item_label]</td>
        <td class="wfte_right_column">[wfte_product_table_tax_item]</td>
      </tr>
      <tr class="wfte_payment_summary_table_row wfte_product_table_fee">
        <td class="wfte_product_table_fee_label wfte_text_right">__[Fee]__</td>
        <td class="wfte_right_column">[wfte_product_table_fee]</td>
      </tr>
      <tr class="wfte_payment_summary_table_row wfte_product_table_payment_total">
        <td class="wfte_product_table_payment_total_label wfte_text_right">__[Total]__</td>
        <td class="wfte_product_table_payment_total_val wfte_right_column">[wfte_product_table_payment_total]</td>
      </tr>
    </tbody>
  </table>
    <div class="wfte_footer clearfix wfte_text_left">
      [wfte_footer]
    </div>
  </div>
</div>
<style type="text/css">
body, html{margin:0px; padding:0px;}
.clearfix::after {
    display: block;
    clear: both;
  content: "";}
.wfte_invoice-main{ color:#73879C; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif; font-size:12px; font-weight:400; line-height:18px; box-sizing:border-box; width:100%; margin:0px;}
.wfte_invoice-main *{ box-sizing:border-box;}
.wfte_invoice-header{ background:#fff; color:#000; padding:10px 20px; width:100%; }
.wfte_invoice-header_top{ padding:15px 0px; padding-bottom:10px; width:100%;}
.wfte_company_logo{ float:left; max-width:40%; }
.wfte_company_logo_img{ width:150px; max-width:100%; }
.wfte_company_name{ font-size:18px; font-weight:bold; }
.wfte_company_logo_extra_details{ font-size:12px; }
.wfte_barcode{ width:100%; height:auto; margin-top:10px; }
.wfte_invoice_number{ font-size:14px; font-weight:bold; }
.wfte_order_number{ font-size:14px; font-weight:bold; }
.wfte_invoice_date{ font-size:14px;}
.wfte_order_date{ font-size:14px;}
.wfte_invoice_data{width:33%; line-height:16px; }
.wfte_addrss_field_main{ width:100%; font-size:12px; padding-top:5px; }
.wfte_addrss_fields{ width:33%; line-height:16px;}
.wfte_address-field-header{ font-weight:bold; }

.wfte_invoice-body{background:#ffffff; color:#23272c; padding:10px 20px; width:100%;}
.wfte_product_table{ width:100%; border-collapse:collapse;}
.wfte_product_table_head{background-color:#212529; color:#ffffff;}
.wfte_product_table .wfte_right_column{ width:15%; }
.wfte_payment_summary_table .wfte_left_column{ width:60%; }
.wfte_product_table_body td{padding:8px 5px;}
.wfte_payment_summary_table_body td{padding:8px 5px;}
.wfte_product_table_head{background-color:#212529;}
.wfte_product_table_head th{border:solid 1px #212529; height:36px; padding:0px; color:inherit; font-size:.75rem; line-height:10px;}

.wfte_product_table_body td, .wfte_payment_summary_table_body td{text-align:center; font-size:12px; line-height:10px; border:solid 1px #dadada; }
.wfte_payment_summary_table_row{font-weight:bold;}
.wfte_payment_summary_table_body .wfte_right_column{font-weight:normal;}
.wfte_product_table_body tr:last-child td{ border-bottom:none; }

.wfte_product_table_payment_total{font-size:14px;}
td.wfte_product_table_payment_total_label{ text-align:right;}
.wfte_product_table_payment_total_val{}
.wfte_return_policy{ width:100%; height:auto; border-top:solid 1px #dfd5d5; padding:5px 0px; margin-top:10px; }
.wfte_footer{width:100%; height:auto; padding:5px 0px; margin-top:20px; font-size:12px;}

.float_left{ float:left; }
.float_right{ float:right; }
</style>