/**
 * Bank Reports Modal JavaScript
 * 
 * This file contains all the JavaScript functionality for the Bank Reports Modal component.
 * It handles AJAX form submissions, button clicks, and dynamic content loading.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initial call to set up event listeners
    initializeModalEventListeners();
});

/**
 * Initialize all event listeners for the modal
 * This function is called when the document loads and after any AJAX content replacement
 */
function initializeModalEventListeners() {
    console.log('Initializing modal event listeners');
    
    // Initialize different components
    initializeBankSelection();
    initializeWithdrawalSection();
}

/**
 * Initialize bank selection related event listeners
 */
function initializeBankSelection() {
    // Bank selection form handling
    const viewReportsBtn = document.getElementById('viewReportsBtn');
    if (viewReportsBtn) {
        console.log('Found viewReportsBtn, attaching listener');
        // Remove any existing event listeners first to prevent duplicates
        viewReportsBtn.replaceWith(viewReportsBtn.cloneNode(true));
        
        // Get the fresh reference after replacement
        const freshViewReportsBtn = document.getElementById('viewReportsBtn');
        freshViewReportsBtn.addEventListener('click', function() {
            console.log('View Reports button clicked');
            const bankId = document.getElementById('bankSelect').value;
            if (!bankId) {
                alert('Please select a bank');
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('selected_bank_id', bankId);
            
            // Send AJAX request
            sendAjaxRequest(formData);
        });
    }
    
    // Change bank button handling
    const changeBankBtn = document.getElementById('changeBankBtn');
    if (changeBankBtn) {
        console.log('Found changeBankBtn, attaching listener');
        // Remove any existing event listeners first to prevent duplicates
        changeBankBtn.replaceWith(changeBankBtn.cloneNode(true));
        
        // Get the fresh reference after replacement
        const freshChangeBankBtn = document.getElementById('changeBankBtn');
        freshChangeBankBtn.addEventListener('click', function() {
            console.log('Change Bank button clicked');
            // Create form data
            const formData = new FormData();
            formData.append('change_bank', '1');
            
            // Send AJAX request
            sendAjaxRequest(formData);
        });
    }
}

/**
 * Initialize withdrawal section related event listeners
 */
function initializeWithdrawalSection() {
    // Withdraw Card button handling
    const withdrawCardBtn = document.getElementById('withdrawCardBtn');
    if (withdrawCardBtn) {
        console.log('Found withdrawCardBtn, attaching listener');
        // Remove any existing event listeners first to prevent duplicates
        withdrawCardBtn.replaceWith(withdrawCardBtn.cloneNode(true));
        
        // Get the fresh reference after replacement
        const freshWithdrawCardBtn = document.getElementById('withdrawCardBtn');
        freshWithdrawCardBtn.addEventListener('click', function() {
            console.log('Withdraw Card button clicked');
            const bankId = this.getAttribute('data-bank-id');
            
            // Show withdrawal section, hide reports section
            document.getElementById('reportsSection').style.display = 'none';
            document.getElementById('withdrawalSection').style.display = 'block';
            
            // Add class to modal for state management
            document.querySelector('#bankReportsModal').classList.add('modal-in-withdrawal-mode');
            
            // In the future, we'll load the actual withdrawal content here via AJAX
            console.log('Will load withdrawal interface for bank ID:', bankId);
            
            // For now, just show a placeholder message
            document.getElementById('withdrawalContent').innerHTML = `
                <div class="alert alert-info">
                    <p>Withdrawal interface for bank ID ${bankId} will be loaded here.</p>
                    <p>This is a placeholder. The actual withdrawal interface will be implemented in the next step.</p>
                </div>
            `;
        });
    }
    
    // Back to Reports button handling
    const backToReportsBtn = document.getElementById('backToReportsBtn');
    if (backToReportsBtn) {
        console.log('Found backToReportsBtn, attaching listener');
        // Remove any existing event listeners first to prevent duplicates
        backToReportsBtn.replaceWith(backToReportsBtn.cloneNode(true));
        
        // Get the fresh reference after replacement
        const freshBackToReportsBtn = document.getElementById('backToReportsBtn');
        freshBackToReportsBtn.addEventListener('click', function() {
            console.log('Back to Reports button clicked');
            
            // Show reports section, hide withdrawal section
            document.getElementById('reportsSection').style.display = 'block';
            document.getElementById('withdrawalSection').style.display = 'none';
            
            // Remove class from modal for state management
            document.querySelector('#bankReportsModal').classList.remove('modal-in-withdrawal-mode');
        });
    }
}

/**
 * Helper function to send AJAX requests and update modal content
 * @param {FormData} formData - The form data to send
 */
function sendAjaxRequest(formData) {
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        // Replace modal content
        const modalContent = document.querySelector('#bankReportsModal .modal-content');
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const newModalContent = tempDiv.querySelector('#bankReportsModal .modal-content');
        if (newModalContent) {
            modalContent.innerHTML = newModalContent.innerHTML;
            // Re-initialize event listeners
            initializeModalEventListeners();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
