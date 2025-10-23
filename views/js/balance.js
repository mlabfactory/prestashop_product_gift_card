/**
 * Gift Card Balance Page - JavaScript
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        
        // View message button
        const viewMessageButtons = document.querySelectorAll('.view-message');
        viewMessageButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const message = this.getAttribute('data-message');
                const messageContent = document.getElementById('messageContent');
                
                if (messageContent) {
                    messageContent.textContent = message;
                    
                    // Show modal (Bootstrap 4/5 compatible)
                    const modal = document.getElementById('messageModal');
                    if (modal) {
                        if (typeof bootstrap !== 'undefined') {
                            // Bootstrap 5
                            const bsModal = new bootstrap.Modal(modal);
                            bsModal.show();
                        } else if (typeof $ !== 'undefined' && $.fn.modal) {
                            // Bootstrap 4 with jQuery
                            $(modal).modal('show');
                        }
                    }
                }
            });
        });

        // Use gift card button
        const useButtons = document.querySelectorAll('.use-giftcard');
        useButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                const amount = this.getAttribute('data-amount');
                
                // Copy code to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(code).then(function() {
                        showNotification('Gift card code copied to clipboard!', 'success');
                        
                        // Redirect to shop after a delay
                        setTimeout(function() {
                            window.location.href = '/';
                        }, 1500);
                    }).catch(function() {
                        // Fallback if clipboard API fails
                        showCodePrompt(code);
                    });
                } else {
                    // Fallback for browsers without clipboard API
                    showCodePrompt(code);
                }
            });
        });

        // Copy code when clicked
        const codeCells = document.querySelectorAll('.giftcard-code');
        codeCells.forEach(function(cell) {
            cell.style.cursor = 'pointer';
            cell.title = 'Click to copy';
            
            cell.addEventListener('click', function() {
                const code = this.textContent;
                
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(code).then(function() {
                        showNotification('Code copied!', 'success');
                    });
                } else {
                    // Select text fallback
                    const range = document.createRange();
                    range.selectNode(cell);
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);
                    
                    try {
                        document.execCommand('copy');
                        showNotification('Code copied!', 'success');
                    } catch (err) {
                        console.error('Copy failed', err);
                    }
                    
                    window.getSelection().removeAllRanges();
                }
            });
        });

        // Highlight expired cards
        const expiredRows = document.querySelectorAll('.giftcard-row.expired');
        expiredRows.forEach(function(row) {
            row.style.opacity = '0.6';
        });

    });

    /**
     * Show notification message
     */
    function showNotification(message, type) {
        type = type || 'info';
        
        // Try to use PrestaShop's notification system
        if (typeof prestashop !== 'undefined' && prestashop.emit) {
            prestashop.emit('showNotification', {
                message: message,
                type: type
            });
            return;
        }

        // Fallback: create custom notification
        const notification = document.createElement('div');
        notification.className = 'alert alert-' + type + ' giftcard-notification';
        notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideIn 0.3s;';
        notification.innerHTML = '<strong>' + message + '</strong>';
        
        document.body.appendChild(notification);
        
        setTimeout(function() {
            notification.style.animation = 'slideOut 0.3s';
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }

    /**
     * Show prompt with code (fallback)
     */
    function showCodePrompt(code) {
        const message = 'Your gift card code:\n\n' + code + '\n\nCopy this code to use at checkout.';
        
        if (confirm(message + '\n\nGo to shop now?')) {
            window.location.href = '/';
        }
    }

    /**
     * Add CSS animations
     */
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .giftcard-code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            user-select: all;
        }
        
        .giftcard-code:hover {
            background: #e9ecef;
        }
        
        .summary-card h5 {
            font-size: 0.9rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .summary-card .h3 {
            font-weight: bold;
        }
        
        .message-content {
            white-space: pre-wrap;
            font-style: italic;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
    `;
    document.head.appendChild(style);

})();
