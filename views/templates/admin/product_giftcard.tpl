{*
* Admin Product Gift Card Configuration Template
*}
<div class="panel product-tab" id="product-giftcard">
    <div class="panel-heading">
        <i class="icon-gift"></i>
        {l s='Gift Card Settings' mod='mlab_product_gift_card'}
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label col-lg-3">
                <span class="label-tooltip" data-toggle="tooltip" 
                      title="{l s='Enable this product as a gift card' mod='mlab_product_gift_card'}">
                    {l s='Is Gift Card' mod='mlab_product_gift_card'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="is_giftcard" id="is_giftcard_on" value="1" {if $is_giftcard}checked="checked"{/if}>
                    <label for="is_giftcard_on">{l s='Yes' mod='mlab_product_gift_card'}</label>
                    <input type="radio" name="is_giftcard" id="is_giftcard_off" value="0" {if !$is_giftcard}checked="checked"{/if}>
                    <label for="is_giftcard_off">{l s='No' mod='mlab_product_gift_card'}</label>
                    <a class="slide-button btn"></a>
                </span>
                <p class="help-block">
                    {l s='Enable this option to make this product a gift card' mod='mlab_product_gift_card'}
                </p>
            </div>
        </div>

        <div class="form-group" id="giftcard_amounts_group">
            <label class="control-label col-lg-3">
                <span class="label-tooltip" data-toggle="tooltip" 
                      title="{l s='Custom amounts available for this gift card (comma-separated)' mod='mlab_product_gift_card'}">
                    {l s='Custom Amounts' mod='mlab_product_gift_card'}
                </span>
            </label>
            <div class="col-lg-9">
                <input type="text" 
                       name="giftcard_custom_amounts" 
                       id="giftcard_custom_amounts" 
                       value="{$custom_amounts|escape:'htmlall':'UTF-8'}" 
                       class="form-control"
                       placeholder="{l s='e.g., 25,50,100,150,200' mod='mlab_product_gift_card'}">
                <p class="help-block">
                    {l s='Leave empty to use default amounts from module configuration' mod='mlab_product_gift_card'}
                </p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        function toggleGiftCardOptions() {
            var isGiftCard = $('#is_giftcard_on').is(':checked');
            if (isGiftCard) {
                $('#giftcard_amounts_group').show();
            } else {
                $('#giftcard_amounts_group').hide();
            }
        }

        $('input[name="is_giftcard"]').on('change', function() {
            toggleGiftCardOptions();
        });

        toggleGiftCardOptions();
    });
</script>

<style>
    #product-giftcard .panel-heading {
        font-weight: bold;
    }
    #giftcard_amounts_group {
        margin-top: 20px;
    }
</style>
