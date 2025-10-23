{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='My Gift Cards' mod='mlab_product_gift_card'}
{/block}

{block name='page_content'}
    <div class="giftcard-balance-page">
        
        {* Check Gift Card by Code *}
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="material-icons">search</i>
                    {l s='Check Gift Card Balance' mod='mlab_product_gift_card'}
                </h3>
            </div>
            <div class="card-body">
                <form method="post" action="{$check_url}" class="giftcard-check-form">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="giftcard_code">{l s='Gift Card Code' mod='mlab_product_gift_card'}</label>
                                <input type="text" 
                                       name="giftcard_code" 
                                       id="giftcard_code" 
                                       class="form-control" 
                                       placeholder="{l s='GC-XXXXXXXXXXXX' mod='mlab_product_gift_card'}"
                                       required>
                                <small class="form-text text-muted">
                                    {l s='Enter your gift card code to check the balance' mod='mlab_product_gift_card'}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <button type="submit" name="check_code" class="btn btn-primary btn-block">
                                <i class="material-icons">check_circle</i>
                                {l s='Check Balance' mod='mlab_product_gift_card'}
                            </button>
                        </div>
                    </div>
                </form>

                {if $check_error}
                    <div class="alert alert-danger mt-3">
                        <i class="material-icons">error</i>
                        {$check_error}
                    </div>
                {/if}

                {if $checked_card}
                    <div class="alert alert-success mt-3">
                        <h4 class="alert-heading">
                            <i class="material-icons">check_circle</i>
                            {l s='Gift Card Found!' mod='mlab_product_gift_card'}
                        </h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{l s='Code:' mod='mlab_product_gift_card'}</strong> {$checked_card.code}</p>
                                <p><strong>{l s='Status:' mod='mlab_product_gift_card'}</strong> 
                                    <span class="badge badge-{$checked_card.status}">{$checked_card.status}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{l s='Original Amount:' mod='mlab_product_gift_card'}</strong> {displayPrice price=$checked_card.amount}</p>
                                <p><strong>{l s='Remaining Balance:' mod='mlab_product_gift_card'}</strong> 
                                    <span class="text-success font-weight-bold">{displayPrice price=$checked_card.remaining_amount}</span>
                                </p>
                                {if $checked_card.expiry_date}
                                    <p><strong>{l s='Expires on:' mod='mlab_product_gift_card'}</strong> {$checked_card.expiry_date}</p>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>

        {* My Gift Cards List *}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="material-icons">card_giftcard</i>
                    {l s='My Received Gift Cards' mod='mlab_product_gift_card'}
                </h3>
            </div>
            <div class="card-body">
                {if $giftcards|count > 0}
                    <div class="table-responsive">
                        <table class="table table-striped giftcard-table">
                            <thead>
                                <tr>
                                    <th>{l s='Code' mod='mlab_product_gift_card'}</th>
                                    <th>{l s='Status' mod='mlab_product_gift_card'}</th>
                                    <th>{l s='Original Amount' mod='mlab_product_gift_card'}</th>
                                    <th>{l s='Remaining' mod='mlab_product_gift_card'}</th>
                                    <th>{l s='Received' mod='mlab_product_gift_card'}</th>
                                    <th>{l s='Expires' mod='mlab_product_gift_card'}</th>
                                    <th>{l s='Actions' mod='mlab_product_gift_card'}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$giftcards item=card}
                                    <tr class="giftcard-row {if $card.is_expired}expired{/if}">
                                        <td>
                                            <code class="giftcard-code">{$card.code}</code>
                                            {if $card.sender_name}
                                                <br><small class="text-muted">
                                                    <i class="material-icons small">person</i>
                                                    {l s='From:' mod='mlab_product_gift_card'} {$card.sender_name}
                                                </small>
                                            {/if}
                                        </td>
                                        <td>
                                            <span class="badge badge-{$card.status_class}">
                                                {$card.status_label}
                                            </span>
                                            {if $card.is_expired}
                                                <br><span class="badge badge-danger">{l s='Expired' mod='mlab_product_gift_card'}</span>
                                            {/if}
                                        </td>
                                        <td>{$card.amount_formatted}</td>
                                        <td>
                                            <strong class="text-{if $card.remaining_amount > 0}success{else}muted{/if}">
                                                {$card.remaining_amount_formatted}
                                            </strong>
                                        </td>
                                        <td>
                                            <small>{$card.date_add_formatted}</small>
                                        </td>
                                        <td>
                                            <small class="{if $card.is_expired}text-danger{/if}">
                                                {$card.expiry_date_formatted}
                                            </small>
                                        </td>
                                        <td>
                                            {if $card.can_use}
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary use-giftcard" 
                                                        data-code="{$card.code}"
                                                        data-amount="{$card.remaining_amount}">
                                                    <i class="material-icons">shopping_cart</i>
                                                    {l s='Use' mod='mlab_product_gift_card'}
                                                </button>
                                            {else}
                                                <span class="text-muted">
                                                    <i class="material-icons">block</i>
                                                    {l s='Not Available' mod='mlab_product_gift_card'}
                                                </span>
                                            {/if}
                                            
                                            {if $card.message}
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary view-message ml-1" 
                                                        data-message="{$card.message|escape:'html':'UTF-8'}"
                                                        title="{l s='View Message' mod='mlab_product_gift_card'}">
                                                    <i class="material-icons">mail</i>
                                                </button>
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>

                    {* Summary *}
                    <div class="giftcard-summary mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="summary-card bg-light p-3 rounded">
                                    <h5>{l s='Total Cards' mod='mlab_product_gift_card'}</h5>
                                    <p class="h3 mb-0">{$giftcards|count}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card bg-success text-white p-3 rounded">
                                    <h5>{l s='Active Cards' mod='mlab_product_gift_card'}</h5>
                                    <p class="h3 mb-0">
                                        {assign var="active_count" value=0}
                                        {foreach from=$giftcards item=card}
                                            {if $card.can_use}{assign var="active_count" value=$active_count+1}{/if}
                                        {/foreach}
                                        {$active_count}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card bg-primary text-white p-3 rounded">
                                    <h5>{l s='Total Balance' mod='mlab_product_gift_card'}</h5>
                                    <p class="h3 mb-0">
                                        {assign var="total_balance" value=0}
                                        {foreach from=$giftcards item=card}
                                            {if $card.can_use}
                                                {assign var="total_balance" value=$total_balance+$card.remaining_amount}
                                            {/if}
                                        {/foreach}
                                        {displayPrice price=$total_balance}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                {else}
                    <div class="alert alert-info">
                        <i class="material-icons">info</i>
                        {l s='You have not received any gift cards yet.' mod='mlab_product_gift_card'}
                    </div>
                {/if}
            </div>
        </div>

        {* Message Modal *}
        <div class="modal fade" id="messageModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="material-icons">mail</i>
                            {l s='Personal Message' mod='mlab_product_gift_card'}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="messageContent" class="message-content"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {l s='Close' mod='mlab_product_gift_card'}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
{/block}
