/**
 * Gift Card Module - Frontend JavaScript
 */

(function() {
    'use strict';

    /**
     * Initialize gift card functionality
     */
    function initGiftCard() {
        // Get all gift card forms
        const giftCardForms = document.querySelectorAll('.giftcard-options');
        
        giftCardForms.forEach(function(form) {
            const productId = form.id.replace('giftcard-options-', '');
            
            // Amount selector
            const amountSelect = form.querySelector('.giftcard-amount');
            if (amountSelect) {
                amountSelect.addEventListener('change', function() {
                    handleAmountChange(productId, this.value);
                });
            }
            
            // Email validation
            const emailInput = form.querySelector('input[type="email"]');
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                    validateEmail(this);
                });
                
                emailInput.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateEmail(this);
                    }
                });
            }
            
            // Message character counter
            const messageTextarea = form.querySelector('textarea');
            if (messageTextarea) {
                addCharacterCounter(messageTextarea);
            }
            
        });
    }

    /**
     * Handle amount selection change
     */
    function handleAmountChange(productId, amount) {
        if (!amount) return;
        
        console.log('Gift card amount selected:', amount, 'for product:', productId);
        
        // Update product price if needed
        const priceElement = document.querySelector('.product-price');
        if (priceElement && window.prestashop) {
            prestashop.emit('updateProduct', {
                eventType: 'updatedProductCombination',
                id_product_attribute: 0,
                id_product: parseInt(productId),
                price: parseFloat(amount)
            });
        }
    }

    /**
     * Validate email address
     */
    function validateEmail(input) {
        const email = input.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            input.classList.add('is-invalid');
            showFieldError(input, 'Please enter a valid email address');
            return false;
        } else {
            input.classList.remove('is-invalid');
            removeFieldError(input);
            return true;
        }
    }

    /**
     * Add character counter to textarea
     */
    function addCharacterCounter(textarea) {
        const maxLength = textarea.getAttribute('maxlength') || 500;
        const counter = document.createElement('small');
        counter.className = 'form-text text-muted character-counter';
        counter.style.textAlign = 'right';
        counter.style.display = 'block';
        
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = remaining + ' characters remaining';
            
            if (remaining < 50) {
                counter.style.color = '#dc3545';
            } else if (remaining < 100) {
                counter.style.color = '#ffc107';
            } else {
                counter.style.color = '#6c757d';
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        textarea.parentNode.appendChild(counter);
        updateCounter();
    }

    /**
     * Show error message for a field
     */
    function showFieldError(input, message) {
        removeFieldError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.style.display = 'block';
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
    }

    /**
     * Remove error message from a field
     */
    function removeFieldError(input) {
        const existingError = input.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * Validate gift card form
     */
    function validateGiftCardForm(form) {
        let isValid = true;
        
        // Validate amount
        const amountSelect = form.querySelector('.giftcard-amount');
        if (amountSelect && !amountSelect.value) {
            amountSelect.classList.add('is-invalid');
            showFieldError(amountSelect, 'Please select an amount');
            isValid = false;
        }
        
        // Validate email
        const emailInput = form.querySelector('input[type="email"]');
        if (emailInput) {
            if (!emailInput.value.trim()) {
                emailInput.classList.add('is-invalid');
                showFieldError(emailInput, 'Email is required');
                isValid = false;
            } else if (!validateEmail(emailInput)) {
                isValid = false;
            }
        }
        
        return isValid;
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGiftCard);
    } else {
        initGiftCard();
    }

    // Re-initialize on AJAX page updates (for some themes)
    if (window.prestashop) {
        prestashop.on('updatedProduct', function() {
            setTimeout(initGiftCard, 100);
        });
    }

})();
