{*
* Cart Gift Card Application Template
*}
<div class="cart-giftcard-container" id="cart-giftcard">
    <div class="card mt-3">
        <div class="card-header">
            <h4 class="card-title mb-0">
                <i class="material-icons">card_giftcard</i>
                {l s='Have a Gift Card?' mod='mlab_product_gift_card'}
            </h4>
        </div>
        <div class="card-body">
            {if $giftcard_applied}
                <div class="alert alert-success giftcard-applied">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{l s='Gift Card Applied!' mod='mlab_product_gift_card'}</strong><br>
                            <small>{l s='Code:' mod='mlab_product_gift_card'} <strong>{$giftcard_code|escape:'htmlall':'UTF-8'}</strong></small><br>
                            <small>{l s='Amount:' mod='mlab_product_gift_card'} <strong>{$giftcard_amount}</strong></small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-giftcard">
                            {l s='Remove' mod='mlab_product_gift_card'}
                        </button>
                    </div>
                </div>
            {else}
                <form id="giftcard-form" class="giftcard-form">
                    <div class="form-group mb-3">
                        <label for="giftcard_code">{l s='Enter your gift card code' mod='mlab_product_gift_card'}</label>
                        <div class="input-group">
                            <input type="text" 
                                   name="giftcard_code" 
                                   id="giftcard_code" 
                                   class="form-control" 
                                   placeholder="{l s='GC-XXXXXXXXXXXX' mod='mlab_product_gift_card'}"
                                   maxlength="50"
                                   required>
                            <button type="submit" class="btn btn-primary">
                                {l s='Apply' mod='mlab_product_gift_card'}
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            {l s='The gift card discount will be applied to your order total' mod='mlab_product_gift_card'}
                        </small>
                    </div>
                </form>
                
                <div id="giftcard-message" class="mt-2" style="display: none;"></div>
            {/if}
        </div>
    </div>
</div>

<script type="text/javascript">
(function() {
    'use strict';
    
    const applyUrl = '{$apply_url|escape:'javascript':'UTF-8'}';
    
    // Apply gift card
    const form = document.getElementById('giftcard-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const codeInput = document.getElementById('giftcard_code');
            const code = codeInput.value.trim();
            
            if (!code) {
                showMessage('error', '{l s='Please enter a gift card code' mod='mlab_product_gift_card' js=1}');
                return;
            }
            
            // Show loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = '{l s='Applying...' mod='mlab_product_gift_card' js=1}';
            
            fetch(applyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=apply&code=' + encodeURIComponent(code)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showMessage('error', data.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                showMessage('error', '{l s='An error occurred. Please try again.' mod='mlab_product_gift_card' js=1}');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }
    
    // Remove gift card
    const removeBtn = document.querySelector('.remove-giftcard');
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            if (!confirm('{l s='Remove gift card from cart?' mod='mlab_product_gift_card' js=1}')) {
                return;
            }
            
            this.disabled = true;
            this.textContent = '{l s='Removing...' mod='mlab_product_gift_card' js=1}';
            
            fetch(applyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=remove'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                alert('{l s='An error occurred. Please try again.' mod='mlab_product_gift_card' js=1}');
                this.disabled = false;
                this.textContent = '{l s='Remove' mod='mlab_product_gift_card' js=1}';
            });
        });
    }
    
    function showMessage(type, message) {
        const messageDiv = document.getElementById('giftcard-message');
        messageDiv.className = 'alert alert-' + (type === 'error' ? 'danger' : 'success');
        messageDiv.textContent = message;
        messageDiv.style.display = 'block';
        
        setTimeout(function() {
            messageDiv.style.display = 'none';
        }, 5000);
    }
})();
</script>

<style>
    .cart-giftcard-container {
        margin: 20px 0;
    }
    
    .cart-giftcard-container .card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
    }
    
    .cart-giftcard-container .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 15px;
    }
    
    .cart-giftcard-container .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .cart-giftcard-container .material-icons {
        font-size: 24px;
    }
    
    .cart-giftcard-container .giftcard-applied {
        margin-bottom: 0;
    }
    
    .cart-giftcard-container .input-group button {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
</style>
