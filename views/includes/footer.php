    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Card Inventory Management</p>
        </div>
    </footer>

    <!-- Add some JavaScript for interactive elements -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Bank cards expand/collapse functionality
        const bankCards = document.querySelectorAll('.bank-card');
        bankCards.forEach(card => {
            card.addEventListener('click', function () {
                const bankId = this.getAttribute('data-bank-id');
                const table = document.getElementById('bank-' + bankId);
                const icon = this.querySelector('.expand-icon');

                if (table.style.display === 'none' || table.style.display === '') {
                    table.style.display = 'block';
                    icon.classList.add('open');
                } else {
                    table.style.display = 'none';
                    icon.classList.remove('open');
                }
            });
        });

        // Add smooth hover effect for stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 8px 15px rgba(0, 0, 0, 0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                if (!this.classList.contains('clickable')) {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                }
            });
        });
        
        // Bank carousel functionality
        const carousel = document.querySelector('.bank-carousel');
        const prevArrow = document.querySelector('.prev-arrow');
        const nextArrow = document.querySelector('.next-arrow');
        
        if (carousel && prevArrow && nextArrow) {
            const bankCards = carousel.querySelectorAll('.bank-card-item');
            const cardWidth = 180; // width of each card plus gap
            const gap = 16; // gap between cards (1rem)
            const totalWidth = cardWidth + gap;
            const visibleCards = Math.floor(carousel.offsetWidth / totalWidth);
            
            let currentPosition = 0;
            
            // Update arrow state
            function updateArrows() {
                prevArrow.style.opacity = currentPosition <= 0 ? '0.5' : '1';
                prevArrow.style.cursor = currentPosition <= 0 ? 'default' : 'pointer';
                
                const maxPosition = bankCards.length - visibleCards;
                nextArrow.style.opacity = currentPosition >= maxPosition ? '0.5' : '1';
                nextArrow.style.cursor = currentPosition >= maxPosition ? 'default' : 'pointer';
            }
            
            // Initialize arrows
            updateArrows();
            
            // Scroll to previous set of cards
            prevArrow.addEventListener('click', () => {
                if (currentPosition > 0) {
                    currentPosition--;
                    carousel.style.transform = `translateX(-${currentPosition * totalWidth}px)`;
                    updateArrows();
                }
            });
            
            // Scroll to next set of cards
            nextArrow.addEventListener('click', () => {
                if (currentPosition < bankCards.length - visibleCards) {
                    currentPosition++;
                    carousel.style.transform = `translateX(-${currentPosition * totalWidth}px)`;
                    updateArrows();
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                const newVisibleCards = Math.floor(carousel.offsetWidth / totalWidth);
                if (newVisibleCards !== visibleCards && currentPosition > bankCards.length - newVisibleCards) {
                    currentPosition = Math.max(0, bankCards.length - newVisibleCards);
                    carousel.style.transform = `translateX(-${currentPosition * totalWidth}px)`;
                }
                updateArrows();
            });
        }
        
        // Reports carousel functionality
        const reportsCarousel = document.querySelector('.reports-carousel');
        const reportsPrevArrow = document.querySelector('.reports-prev-arrow');
        const reportsNextArrow = document.querySelector('.reports-next-arrow');
        
        if (reportsCarousel && reportsPrevArrow && reportsNextArrow) {
            const reportCards = reportsCarousel.querySelectorAll('.report-carousel-item');
            const cardWidth = 280; // width of each report card
            const gap = 16; // gap between cards (1rem)
            const totalWidth = cardWidth + gap;
            const visibleCards = Math.floor(reportsCarousel.offsetWidth / totalWidth);
            
            let currentReportPosition = 0;
            
            // Update arrow state
            function updateReportArrows() {
                reportsPrevArrow.style.opacity = currentReportPosition <= 0 ? '0.5' : '1';
                reportsPrevArrow.style.cursor = currentReportPosition <= 0 ? 'default' : 'pointer';
                
                const maxPosition = reportCards.length - visibleCards;
                reportsNextArrow.style.opacity = currentReportPosition >= maxPosition ? '0.5' : '1';
                reportsNextArrow.style.cursor = currentReportPosition >= maxPosition ? 'default' : 'pointer';
            }
            
            // Initialize arrows
            updateReportArrows();
            
            // Scroll to previous set of cards
            reportsPrevArrow.addEventListener('click', () => {
                if (currentReportPosition > 0) {
                    currentReportPosition--;
                    reportsCarousel.style.transform = `translateX(-${currentReportPosition * totalWidth}px)`;
                    updateReportArrows();
                }
            });
            
            // Scroll to next set of cards
            reportsNextArrow.addEventListener('click', () => {
                if (currentReportPosition < reportCards.length - visibleCards) {
                    currentReportPosition++;
                    reportsCarousel.style.transform = `translateX(-${currentReportPosition * totalWidth}px)`;
                    updateReportArrows();
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                const newVisibleCards = Math.floor(reportsCarousel.offsetWidth / totalWidth);
                if (newVisibleCards !== visibleCards && currentReportPosition > reportCards.length - newVisibleCards) {
                    currentReportPosition = Math.max(0, reportCards.length - newVisibleCards);
                    reportsCarousel.style.transform = `translateX(-${currentReportPosition * totalWidth}px)`;
                }
                updateReportArrows();
            });
        }
        
        // Modal functionality for bank cards
        const bankCardItems = document.querySelectorAll('.bank-card-item');
        const modal = document.getElementById('cardDetailsModal');
        const closeModal = document.querySelector('.close-modal');
        const modalBankName = document.getElementById('modalBankName');
        const cardsTableContainer = document.getElementById('cardsTableContainer');
        
        if (bankCardItems.length && modal) {
            // Show modal when bank card is clicked
            bankCardItems.forEach(card => {
                card.addEventListener('click', function() {
                    const bankId = this.getAttribute('data-bank-id');
                    const bankData = banksWithCards[bankId];
                    
                    if (bankData) {
                        // Set bank name in modal header
                        modalBankName.textContent = bankData.bank_name;
                        
                        // Generate the cards table
                        let tableHTML = '';
                        if (bankData.cards && bankData.cards.length > 0) {
                            tableHTML = `
                                <table class="cards-table">
                                    <thead>
                                        <tr>
                                            <th>Card Name</th>
                                            <th>Association</th>
                                            <th>Chip Type</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Expiration Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;
                            
                            bankData.cards.forEach(card => {
                                tableHTML += `
                                    <tr>
                                        <td>${card.card_name}</td>
                                        <td>${card.association}</td>
                                        <td>${card.chip_type}</td>
                                        <td>${card.card_type}</td>
                                        <td class="card-quantity">${card.card_quantity}</td>
                                        <td class="card-expiry">${card.expired_at}</td>
                                        <td class="action-buttons">
                                            <button class="action-btn view" title="View Details" data-card-id="${card.card_id}">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button class="action-btn report" title="View Transactions" data-card-id="${card.card_id}">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                            
                            tableHTML += `
                                    </tbody>
                                </table>
                            `;
                        } else {
                            tableHTML = '<div class="no-cards-message">No cards available for this bank.</div>';
                        }
                        
                        cardsTableContainer.innerHTML = tableHTML;
                        
                        // Add event listeners to view detail buttons
                        const viewButtons = cardsTableContainer.querySelectorAll('.action-btn.view');
                        viewButtons.forEach(button => {
                            button.addEventListener('click', function(e) {
                                e.preventDefault();
                                const cardId = this.getAttribute('data-card-id');
                                showCardDetails(cardId, bankData);
                            });
                        });
                        
                        // Add event listeners to view transactions buttons
                        const transButtons = cardsTableContainer.querySelectorAll('.action-btn.report');
                        transButtons.forEach(button => {
                            button.addEventListener('click', function(e) {
                                e.preventDefault();
                                const cardId = this.getAttribute('data-card-id');
                                fetchAndShowTransactions(cardId, bankData);
                            });
                        });
                        
                        // Show modal with animation
                        modal.classList.add('show');
                        modal.style.display = 'block';
                        document.body.style.overflow = 'hidden'; // Prevent scrolling behind modal
                        
                        // Add keyboard support for ESC key to close modal
                        document.addEventListener('keydown', handleEscKey);
                    }
                });
            });
            
            // Close modal when X is clicked
            if (closeModal) {
                closeModal.addEventListener('click', closeCardModal);
            }
            
            // Close modal when clicking outside the modal content
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeCardModal();
                }
            });
            
            // Function to handle ESC key press
            function handleEscKey(event) {
                if (event.key === 'Escape') {
                    closeCardModal();
                }
            }
            
            // Function to close modal
            function closeCardModal() {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = ''; // Re-enable scrolling
                    // Remove keyboard listener when modal is closed
                    document.removeEventListener('keydown', handleEscKey);
                    // Reset to cards table view when modal is closed
                    switchToCardsTable();
                }, 300); // Wait for the transition to complete
            }
            
            // Function to show card details
            function showCardDetails(cardId, bankData) {
                const cardsTableContainer = document.getElementById('cardsTableContainer');
                const cardDetailContainer = document.getElementById('cardDetailContainer');
                const cardDetailContent = cardDetailContainer.querySelector('.card-detail-content');
                
                // Find the card data
                let cardData = null;
                if (bankData && bankData.cards) {
                    bankData.cards.forEach(card => {
                        if (card.card_id == cardId) {
                            cardData = card;
                        }
                    });
                }
                
                if (!cardData) {
                    return;
                }
                
                // Create card details HTML
                const cardDetailsHTML = `
                    <div class="card-details">
                        <div class="card-title">
                            <div class="card-title-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h3>${cardData.card_name}</h3>
                        </div>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">Card ID</div>
                                <div class="detail-value">#${cardData.card_id}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Bank</div>
                                <div class="detail-value">${bankData.bank_name}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Association</div>
                                <div class="detail-value">${cardData.association}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Chip Type</div>
                                <div class="detail-value">${cardData.chip_type}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Card Type</div>
                                <div class="detail-value">${cardData.card_type}</div>
                            </div>
                            <div class="detail-item highlight">
                                <div class="detail-label">Quantity</div>
                                <div class="detail-value">${cardData.card_quantity}</div>
                            </div>
                            <div class="detail-item secondary">
                                <div class="detail-label">Expiration Date</div>
                                <div class="detail-value">${cardData.expired_at}</div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Insert the HTML
                cardDetailContent.innerHTML = cardDetailsHTML;
                
                // Switch views
                cardsTableContainer.style.display = 'none';
                cardDetailContainer.style.display = 'block';
                
                // Setup back button
                const backBtn = document.getElementById('backToTableBtn');
                if (backBtn) {
                    backBtn.addEventListener('click', switchToCardsTable);
                }
            }
            
            // Function to switch back to cards table
            function switchToCardsTable() {
                const cardsTableContainer = document.getElementById('cardsTableContainer');
                const cardDetailContainer = document.getElementById('cardDetailContainer');
                const cardTransactionsContainer = document.getElementById('cardTransactionsContainer');
                
                if (cardsTableContainer && cardDetailContainer && cardTransactionsContainer) {
                    cardsTableContainer.style.display = 'block';
                    cardDetailContainer.style.display = 'none';
                    cardTransactionsContainer.style.display = 'none';
                }
            }
            
            // Function to fetch and show transactions
            function fetchAndShowTransactions(cardId, bankData) {
                const cardsTableContainer = document.getElementById('cardsTableContainer');
                const cardTransactionsContainer = document.getElementById('cardTransactionsContainer');
                const transactionsContent = cardTransactionsContainer.querySelector('.transactions-content');
                
                // Display a loading state
                transactionsContent.innerHTML = '<div class="loading">Loading transactions...</div>';
                
                // Show the transactions container while loading
                cardsTableContainer.style.display = 'none';
                cardTransactionsContainer.style.display = 'block';
                
                // Setup back button
                const backBtn = document.getElementById('backToTableFromTransBtn');
                if (backBtn) {
                    backBtn.addEventListener('click', switchToCardsTable);
                }
                
                // Fetch transaction data from the server
                fetch(`index.php?path=card/getTransactionsJson&card_id=${cardId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            transactionsContent.innerHTML = `<div class="error-message">${data.error}</div>`;
                            return;
                        }
                        
                        const card = data.card;
                        const transactions = data.transactions;
                        
                        // Create transactions HTML
                        let transactionsHTML = `
                            <div class="transactions-header">
                                <div class="card-info">
                                    <div class="card-info-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="card-info-text">
                                        <h3>${card.name}</h3>
                                        <p>Transaction History</p>
                                    </div>
                                </div>
                                <button class="deposit-btn" data-card-id="${card.id}">
                                    <i class="fas fa-plus-circle"></i> Deposit Card
                                </button>
                            </div>
                        `;
                        
                        if (transactions.length > 0) {
                            transactionsHTML += `
                                <table class="transactions-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Quantity</th>
                                            <th>Date</th>
                                            <th>Remarks</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;
                            
                            transactions.forEach(transaction => {
                                transactionsHTML += `
                                    <tr>
                                        <td>#${transaction.id}</td>
                                        <td class="transaction-quantity">${transaction.quantity}</td>
                                        <td class="transaction-date">${transaction.transaction_date}</td>
                                        <td class="transaction-remarks">${transaction.remarks}</td>
                                        <td>
                                            <button class="edit-transaction-btn" data-transaction-id="${transaction.id}" data-quantity="${transaction.quantity}">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                            
                            transactionsHTML += `
                                    </tbody>
                                </table>
                            `;
                        } else {
                            transactionsHTML += `
                                <div class="no-transactions">
                                    <p>No transactions found for this card.</p>
                                </div>
                            `;
                        }
                        
                        // Insert the HTML
                        transactionsContent.innerHTML = transactionsHTML;
                        
                        // Add event listener to deposit button
                        const depositBtn = transactionsContent.querySelector('.deposit-btn');
                        if (depositBtn) {
                            depositBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                const cardId = this.getAttribute('data-card-id');
                                showDepositPopup(cardId);
                            });
                        }
                        
                        // Add event listeners to edit transaction buttons
                        const editTransactionBtns = transactionsContent.querySelectorAll('.edit-transaction-btn');
                        if (editTransactionBtns.length > 0) {
                            editTransactionBtns.forEach(btn => {
                                btn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const transactionId = this.getAttribute('data-transaction-id');
                                    const quantity = this.getAttribute('data-quantity');
                                    showEditTransactionPopup(transactionId, quantity, card.id);
                                });
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching transactions:', error);
                        transactionsContent.innerHTML = `
                            <div class="error-message">
                                <p>Failed to load transactions. Please try again later.</p>
                            </div>
                        `;
                    });
            }
            
            // Function to show edit transaction popup
            function showEditTransactionPopup(transactionId, quantity, cardId) {
                const editTransactionPopup = document.getElementById('editTransactionPopup');
                const editTransactionForm = document.getElementById('editTransactionForm');
                const transactionIdInput = document.getElementById('edit_transaction_id');
                const cardIdInput = document.getElementById('edit_card_id');
                const quantityInput = document.getElementById('edit_quantity');
                const closePopupBtn = editTransactionPopup.querySelector('.close-popup');
                const cancelBtn = document.getElementById('cancelEditTransactionBtn');
                
                // Set form values
                transactionIdInput.value = transactionId;
                cardIdInput.value = cardId;
                quantityInput.value = quantity;
                
                // Show popup
                editTransactionPopup.classList.add('show');
                
                // Focus quantity input for better UX
                setTimeout(() => {
                    quantityInput.focus();
                    quantityInput.select(); // Select the text for easy editing
                }, 100);
                
                // Function to close popup
                function closeEditTransactionPopup() {
                    editTransactionPopup.classList.remove('show');
                    document.removeEventListener('keydown', handleEditTransactionEscKey);
                }
                
                // Close popup events
                closePopupBtn.addEventListener('click', closeEditTransactionPopup);
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', closeEditTransactionPopup);
                }
                
                // Also close when clicking outside
                editTransactionPopup.addEventListener('click', function(e) {
                    if (e.target === editTransactionPopup) {
                        closeEditTransactionPopup();
                    }
                });
                
                // Add ESC key support
                function handleEditTransactionEscKey(e) {
                    if (e.key === 'Escape') {
                        closeEditTransactionPopup();
                    }
                }
                
                document.addEventListener('keydown', handleEditTransactionEscKey);
            }
            
            // Function to show deposit popup
            function showDepositPopup(cardId) {
                const depositPopup = document.getElementById('depositCardPopup');
                const depositCardIdInput = document.getElementById('deposit_card_id');
                const quantityInput = document.getElementById('quantity');
                const closePopupBtn = document.querySelector('.close-popup');
                const cancelBtn = document.getElementById('cancelDepositBtn');
                const cardTransactionsContainer = document.getElementById('cardTransactionsContainer');
                
                // Set the card ID in the form
                depositCardIdInput.value = cardId;
                
                // Reset quantity input
                quantityInput.value = '';
                
                // Show popup
                depositPopup.classList.add('show');
                
                // Focus quantity input for better UX
                setTimeout(() => {
                    quantityInput.focus();
                }, 100);
                
                // Function to close popup
                function closeDepositPopup() {
                    depositPopup.classList.remove('show');
                }
                
                // Close popup events
                closePopupBtn.addEventListener('click', closeDepositPopup);
                cancelBtn.addEventListener('click', closeDepositPopup);
                
                // Also close when clicking outside
                depositPopup.addEventListener('click', function(e) {
                    if (e.target === depositPopup) {
                        closeDepositPopup();
                    }
                });
                
                // Add ESC key support
                function handlePopupEscKey(e) {
                    if (e.key === 'Escape') {
                        closeDepositPopup();
                        document.removeEventListener('keydown', handlePopupEscKey);
                    }
                }
                
                document.addEventListener('keydown', handlePopupEscKey);
                
                // Handle form submission via AJAX
                const depositForm = document.getElementById('depositCardForm');
                depositForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Create form data
                    const formData = new FormData(this);
                    const currentCardId = depositCardIdInput.value;
                    
                    // Send AJAX request
                    fetch('index.php?path=card/processDepositCard', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close the deposit popup
                            closeDepositPopup();
                            
                            // Show success notification
                            showNotification(`Successfully deposited ${data.quantity} cards`, 'success');
                            
                            // Refresh transactions view if it's open
                            if (cardTransactionsContainer && cardTransactionsContainer.style.display === 'block') {
                                fetchAndShowTransactions(currentCardId, null);
                            }
                        } else {
                            // Display error notification
                            showNotification(data.error || 'Failed to deposit card. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred. Please try again.', 'error');
                    });
                });
            }
        }

        // Add Card Popup functionality
        const addCardBtn = document.getElementById('addCardBtn');
        const addCardPopup = document.getElementById('addCardPopup');
        const closeAddCardPopupBtns = addCardPopup ? addCardPopup.querySelectorAll('.close-popup, #cancelAddCardBtn') : [];
        const addCardForm = document.getElementById('addCardForm');
        
        if (addCardBtn && addCardPopup && addCardForm) {
            // Show popup when Add Card button is clicked
            addCardBtn.addEventListener('click', function() {
                // Reset the form before showing the popup
                addCardForm.reset();
                
                addCardPopup.classList.add('show');
                
                // Focus the first input for better UX
                setTimeout(() => {
                    const firstInput = addCardPopup.querySelector('input, select');
                    if (firstInput) firstInput.focus();
                }, 100);
                
                // Add ESC key support
                document.addEventListener('keydown', handleAddCardEscKey);
            });
            
            // Function to close popup
            function closeAddCardPopup() {
                addCardPopup.classList.remove('show');
                document.removeEventListener('keydown', handleAddCardEscKey);
            }
            
            // Close popup when X or Cancel is clicked
            closeAddCardPopupBtns.forEach(btn => {
                btn.addEventListener('click', closeAddCardPopup);
            });
            
            // Close when clicking outside
            addCardPopup.addEventListener('click', function(e) {
                if (e.target === addCardPopup) {
                    closeAddCardPopup();
                }
            });
            
            // ESC key handler
            function handleAddCardEscKey(e) {
                if (e.key === 'Escape') {
                    closeAddCardPopup();
                }
            }
            
            // Handle form submission via AJAX
            addCardForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Create form data
                const formData = new FormData(this);
                
                // Send AJAX request
                fetch('index.php?path=card/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset the form
                        this.reset();
                        
                        // Close the Add Card popup
                        closeAddCardPopup();
                        
                        // Show success notification
                        showNotification('Card added successfully!', 'success');
                        
                        // Show the Card Details Modal with the bank's data
                        showCardDetailsModal(data.bank_id, data.bank_name);
                    } else {
                        // Display error notification
                        showNotification(data.error || 'Failed to add card. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            });
            
            // Function to show the Card Details Modal for a specific bank
            function showCardDetailsModal(bankId, bankName) {
                const modal = document.getElementById('cardDetailsModal');
                const modalBankName = document.getElementById('modalBankName');
                const cardsTableContainer = document.getElementById('cardsTableContainer');
                
                if (modal && modalBankName) {
                    // Display loading state
                    modalBankName.textContent = bankName || 'Loading...';
                    cardsTableContainer.innerHTML = '<div class="loading">Loading cards...</div>';
                    
                    // Show modal with animation
                    modal.classList.add('show');
                    modal.style.display = 'block';
                    document.body.style.overflow = 'hidden'; // Prevent scrolling behind modal
                    
                    // Add keyboard support for ESC key to close modal
                    document.addEventListener('keydown', handleEscKey);
                    
                    // Fetch fresh data from the server
                    fetch(`index.php?path=card/getBankWithCardsJson&bank_id=${bankId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                cardsTableContainer.innerHTML = `<div class="error-message">${data.error}</div>`;
                                return;
                            }
                            
                            const bankData = data.bank;
                            
                            // Set bank name in modal header
                            modalBankName.textContent = bankData.bank_name;
                            
                            // Generate the cards table
                            let tableHTML = '';
                            if (bankData.cards && bankData.cards.length > 0) {
                                tableHTML = `
                                    <table class="cards-table">
                                        <thead>
                                            <tr>
                                                <th>Card Name</th>
                                                <th>Association</th>
                                                <th>Chip Type</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                                <th>Expiration Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                `;
                                
                                bankData.cards.forEach(card => {
                                    tableHTML += `
                                        <tr>
                                            <td>${card.card_name}</td>
                                            <td>${card.association}</td>
                                            <td>${card.chip_type}</td>
                                            <td>${card.card_type}</td>
                                            <td class="card-quantity">${card.card_quantity}</td>
                                            <td class="card-expiry">${card.expired_at}</td>
                                            <td class="action-buttons">
                                                <button class="action-btn view" title="View Details" data-card-id="${card.card_id}">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                <button class="action-btn report" title="View Transactions" data-card-id="${card.card_id}">
                                                    <i class="fas fa-file-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                });
                                
                                tableHTML += `
                                        </tbody>
                                    </table>
                                `;
                            } else {
                                tableHTML = '<div class="no-cards-message">No cards available for this bank.</div>';
                            }
                            
                            cardsTableContainer.innerHTML = tableHTML;
                            
                            // Add event listeners to view detail buttons
                            const viewButtons = cardsTableContainer.querySelectorAll('.action-btn.view');
                            viewButtons.forEach(button => {
                                button.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const cardId = this.getAttribute('data-card-id');
                                    showCardDetails(cardId, bankData);
                                });
                            });
                            
                            // Add event listeners to view transactions buttons
                            const transButtons = cardsTableContainer.querySelectorAll('.action-btn.report');
                            transButtons.forEach(button => {
                                button.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const cardId = this.getAttribute('data-card-id');
                                    fetchAndShowTransactions(cardId, bankData);
                                });
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            cardsTableContainer.innerHTML = `
                                <div class="error-message">
                                    <p>Failed to load cards. Please try again later.</p>
                                </div>
                            `;
                        });
                }
            }
        }

        // Notification system functions
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');
            const notificationProgress = document.getElementById('notification-progress');
            
            // Set message
            notificationMessage.textContent = message;
            
            // Set notification type
            notification.className = 'notification show ' + type;
            
            // Setup progress bar animation
            let width = 100;
            const duration = 10000; // 10 seconds
            const interval = 50; // update interval (ms)
            const step = (interval / duration) * 100;
            
            // Clear any existing timer
            if (window.notificationTimer) {
                clearInterval(window.notificationTimer);
            }
            
            // Set progress animation
            notificationProgress.style.width = '100%';
            window.notificationTimer = setInterval(() => {
                width -= step;
                notificationProgress.style.width = width + '%';
                
                if (width <= 0) {
                    clearInterval(window.notificationTimer);
                    hideNotification();
                }
            }, interval);
        }
        
        function hideNotification() {
            const notification = document.getElementById('notification');
            notification.classList.remove('show');
            
            if (window.notificationTimer) {
                clearInterval(window.notificationTimer);
            }
        }
        
        // Setup notification close button
        const notificationCloseBtn = document.getElementById('notification-close');
        if (notificationCloseBtn) {
            notificationCloseBtn.addEventListener('click', hideNotification);
        }

        // View All Banks Popup functionality
        const viewAllBanksBtn = document.getElementById('viewAllBanksBtn');
        const allBanksPopup = document.getElementById('allBanksPopup');
        const closeAllBanksPopupBtns = allBanksPopup ? allBanksPopup.querySelectorAll('.close-popup') : [];
        
        // Add Bank Popup functionality
        const showAddBankBtn = document.getElementById('showAddBankBtn');
        const addBankPopup = document.getElementById('addBankPopup');
        const closeAddBankPopupBtns = addBankPopup ? addBankPopup.querySelectorAll('.close-popup, #cancelAddBankBtn') : [];
        const backToBanksBtn = document.getElementById('backToBanksBtn');
        
        if (viewAllBanksBtn && allBanksPopup) {
            // Show popup when View All Banks button is clicked
            viewAllBanksBtn.addEventListener('click', function(e) {
                e.preventDefault();
                allBanksPopup.classList.add('show');
                
                // Add ESC key support
                document.addEventListener('keydown', handleAllBanksEscKey);
            });
            
            // Function to close popup
            function closeAllBanksPopup() {
                allBanksPopup.classList.remove('show');
                document.removeEventListener('keydown', handleAllBanksEscKey);
            }
            
            // Close popup when X is clicked
            closeAllBanksPopupBtns.forEach(btn => {
                btn.addEventListener('click', closeAllBanksPopup);
            });
            
            // Close when clicking outside
            allBanksPopup.addEventListener('click', function(e) {
                if (e.target === allBanksPopup) {
                    closeAllBanksPopup();
                }
            });
            
            // ESC key handler
            function handleAllBanksEscKey(e) {
                if (e.key === 'Escape') {
                    closeAllBanksPopup();
                }
            }
            
            // Add Bank functionality
            if (showAddBankBtn && addBankPopup) {
                const addBankForm = document.getElementById('addBankForm');
                
                // Show Add Bank popup when button is clicked
                showAddBankBtn.addEventListener('click', function() {
                    // Reset the form before showing
                    if (addBankForm) {
                        addBankForm.reset();
                    }
                    
                    // Hide the All Banks popup temporarily
                    allBanksPopup.classList.remove('show');
                    
                    // Show the Add Bank popup
                    addBankPopup.classList.add('show');
                    
                    // Focus the first input for better UX
                    setTimeout(() => {
                        const firstInput = addBankPopup.querySelector('input');
                        if (firstInput) firstInput.focus();
                    }, 100);
                    
                    // Add ESC key support
                    document.addEventListener('keydown', handleAddBankEscKey);
                });
                
                // Function to close Add Bank popup
                function closeAddBankPopup() {
                    addBankPopup.classList.remove('show');
                    document.removeEventListener('keydown', handleAddBankEscKey);
                }
                
                // Close Add Bank popup when X or Cancel is clicked
                closeAddBankPopupBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        closeAddBankPopup();
                        // Show All Banks popup again
                        allBanksPopup.classList.add('show');
                    });
                });
                
                // Back button functionality
                if (backToBanksBtn) {
                    backToBanksBtn.addEventListener('click', function() {
                        closeAddBankPopup();
                        // Show All Banks popup again
                        allBanksPopup.classList.add('show');
                    });
                }
                
                // Close when clicking outside
                addBankPopup.addEventListener('click', function(e) {
                    if (e.target === addBankPopup) {
                        closeAddBankPopup();
                        // Show All Banks popup again
                        allBanksPopup.classList.add('show');
                    }
                });
                
                // ESC key handler
                function handleAddBankEscKey(e) {
                    if (e.key === 'Escape') {
                        closeAddBankPopup();
                        // Show All Banks popup again
                        allBanksPopup.classList.add('show');
                    }
                }
                
                // Handle form submission - clear the form after submit
                if (addBankForm) {
                    addBankForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Create form data for AJAX submission
                        const formData = new FormData(this);
                        
                        // Send AJAX request
                        fetch('index.php?path=bank/store', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Reset the form
                                this.reset();
                                
                                // Close the Add Bank popup
                                closeAddBankPopup();
                                
                                // Show success notification
                                showNotification('Bank added successfully!', 'success');
                                
                                // Refresh the banks list in the all banks popup
                                refreshBanksList();
                                
                                // Show All Banks popup again
                                allBanksPopup.classList.add('show');
                            } else {
                                // Display error notification
                                showNotification(data.error || 'Failed to add bank. Please try again.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('An error occurred. Please try again.', 'error');
                        });
                    });
                    
                    // Function to refresh the banks list
                    function refreshBanksList() {
                        fetch('index.php?path=bank/getBanksJson')
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    showNotification(data.error, 'error');
                                    return;
                                }
                                
                                const allBanks = data.banks;
                                const banksGrid = allBanksPopup.querySelector('.banks-grid-items');
                                
                                if (banksGrid && allBanks && allBanks.length > 0) {
                                    // Clear existing content
                                    banksGrid.innerHTML = '';
                                    
                                    // Configuration for pagination
                                    const banksPerPage = 8; // 2 rows of 4 banks
                                    const totalBanks = allBanks.length;
                                    const totalPages = Math.ceil(totalBanks / banksPerPage);
                                    
                                    // Update pagination elements
                                    if (currentPageEl) {
                                        currentPageEl.textContent = '1';
                                    }
                                    
                                    // Add bank items to the grid
                                    allBanks.forEach((bank, index) => {
                                        const bankPage = Math.floor(index / banksPerPage) + 1;
                                        const bankHtml = `
                                            <div class="bank-grid-item" data-page="${bankPage}" style="display: ${bankPage === 1 ? 'flex' : 'none'}">
                                                <div class="bank-logo-container">
                                                    <img src="${bank.bank_logo}" alt="${bank.bank_name}" class="bank-card-logo">
                                                </div>
                                                <div class="bank-card-info">
                                                    <div class="bank-card-title">${bank.bank_name}</div>
                                                    <div class="bank-card-stats">
                                                        <span class="card-count">
                                                            <i class="fas fa-credit-card"></i>
                                                            ${bank.card_count} card${bank.card_count != 1 ? 's' : ''}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        banksGrid.insertAdjacentHTML('beforeend', bankHtml);
                                    });
                                    
                                    // Also refresh the carousel on the main page if it exists
                                    refreshBankCarousel(allBanks);
                                    
                                    // Update pagination controls
                                    if (prevPageBtn && nextPageBtn) {
                                        prevPageBtn.disabled = true;
                                        nextPageBtn.disabled = totalPages <= 1;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error refreshing banks:', error);
                                showNotification('Failed to refresh banks list.', 'error');
                            });
                    }
                    
                    // Function to refresh the bank carousel on the main page
                    function refreshBankCarousel(banks) {
                        const carousel = document.querySelector('.bank-carousel');
                        if (carousel && banks && banks.length > 0) {
                            // Clear existing content
                            carousel.innerHTML = '';
                            
                            // Add bank items to the carousel
                            banks.forEach(bank => {
                                const bankHtml = `
                                    <div class="bank-card-item" data-bank-id="${bank.bank_id}">
                                        <div class="bank-logo-container">
                                            <img src="${bank.bank_logo}" alt="${bank.bank_name}" class="bank-card-logo">
                                        </div>
                                        <div class="bank-card-info">
                                            <div class="bank-card-title">${bank.bank_name}</div>
                                            <div class="bank-card-stats">
                                                <span class="card-count">
                                                    <i class="fas fa-credit-card"></i>
                                                    ${bank.card_count} card${bank.card_count != 1 ? 's' : ''}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                carousel.insertAdjacentHTML('beforeend', bankHtml);
                            });
                            
                            // Reinitialize carousel event listeners
                            const bankCardItems = carousel.querySelectorAll('.bank-card-item');
                            bankCardItems.forEach(card => {
                                card.addEventListener('click', function() {
                                    const bankId = this.getAttribute('data-bank-id');
                                    const bankData = banksWithCards[bankId];
                                    
                                    if (bankData) {
                                        // Show modal with bank details (reusing existing code)
                                        modalBankName.textContent = bankData.bank_name;
                                        
                                        // Generate and display cards table
                                        // ... (existing code will handle this)
                                        
                                        modal.classList.add('show');
                                        modal.style.display = 'block';
                                        document.body.style.overflow = 'hidden';
                                        document.addEventListener('keydown', handleEscKey);
                                    }
                                });
                            });
                            
                            // Reset carousel position
                            currentPosition = 0;
                            carousel.style.transform = 'translateX(0)';
                            updateArrows();
                        }
                    }
                }
            }
        }

        // Handle edit transaction form submission
        const editTransactionForm = document.getElementById('editTransactionForm');
        if (editTransactionForm) {
            editTransactionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Create form data
                const formData = new FormData(this);
                const transactionId = document.getElementById('edit_transaction_id').value;
                const cardId = document.getElementById('edit_card_id').value;
                
                // Send AJAX request
                fetch('index.php?path=card/processEditTransaction', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset the form
                        this.reset();
                        
                        // Close the Edit Transaction popup
                        const editTransactionPopup = document.getElementById('editTransactionPopup');
                        if (editTransactionPopup) {
                            editTransactionPopup.classList.remove('show');
                        }
                        
                        // Show success notification
                        showNotification(`Transaction updated successfully to ${data.quantity}`, 'success');
                        
                        // Refresh transactions view if it's open
                        if (cardTransactionsContainer && cardTransactionsContainer.style.display === 'block') {
                            fetchAndShowTransactions(cardId, null);
                        }
                    } else {
                        // Display error notification
                        showNotification(data.error || 'Failed to update transaction. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            });
        }

        // Pagination functionality
        const prevPageBtn = allBanksPopup.querySelector('.prev-page');
        const nextPageBtn = allBanksPopup.querySelector('.next-page');
        const currentPageEl = allBanksPopup.querySelector('#currentPage');
        const bankItems = allBanksPopup.querySelectorAll('.bank-grid-item');
        
        if (prevPageBtn && nextPageBtn && currentPageEl) {
            let currentPage = 1;
            const totalPages = Math.ceil(bankItems.length / 8); // 8 items per page (4 columns x 2 rows)
            
            // Update page display
            function updatePageControls() {
                currentPageEl.textContent = currentPage;
                
                // Disable/enable buttons based on current page
                prevPageBtn.disabled = currentPage === 1;
                nextPageBtn.disabled = currentPage === totalPages;
                
                // Hide/show bank items based on current page
                bankItems.forEach(item => {
                    const itemPage = parseInt(item.getAttribute('data-page'), 10);
                    if (itemPage === currentPage) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
            
            // Initial setup
            updatePageControls();
            
            // Previous page button
            prevPageBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    updatePageControls();
                }
            });
            
            // Next page button
            nextPageBtn.addEventListener('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePageControls();
                }
            });
        }
        
        // Reports Section Functionality
        const viewAllReportsBtn = document.getElementById('viewAllReportsBtn');
        const withdrawCardBtn = document.getElementById('withdrawCardBtn');
        const allReportsPopup = document.getElementById('allReportsPopup');
        const withdrawCardPopup = document.getElementById('withdrawCardPopup');
        const reportCards = document.querySelectorAll('.report-card');
        const reportDetailsModal = document.getElementById('reportDetailsModal');
        
        // View All Reports functionality
        if (viewAllReportsBtn && allReportsPopup) {
            // Show popup when View All Reports button is clicked
            viewAllReportsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                allReportsPopup.classList.add('show');
                
                // Add ESC key support
                document.addEventListener('keydown', handleReportsEscKey);
            });
            
            // Close popup buttons
            const closeReportsPopupBtns = allReportsPopup.querySelectorAll('.close-popup');
            closeReportsPopupBtns.forEach(btn => {
                btn.addEventListener('click', closeAllReportsPopup);
            });
            
            // Close when clicking outside
            allReportsPopup.addEventListener('click', function(e) {
                if (e.target === allReportsPopup) {
                    closeAllReportsPopup();
                }
            });
            
            // Function to close popup
            function closeAllReportsPopup() {
                allReportsPopup.classList.remove('show');
                document.removeEventListener('keydown', handleReportsEscKey);
            }
            
            // ESC key handler
            function handleReportsEscKey(e) {
                if (e.key === 'Escape') {
                    closeAllReportsPopup();
                }
            }
        }
        
        // Withdraw Card functionality
        if (withdrawCardBtn && withdrawCardPopup) {
            // Show popup when Withdraw Card button is clicked
            withdrawCardBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // If all reports popup is shown, hide it
                if (allReportsPopup) {
                    allReportsPopup.classList.remove('show');
                }
                
                withdrawCardPopup.classList.add('show');
                
                // Add ESC key support
                document.addEventListener('keydown', handleWithdrawEscKey);
            });
            
            // Back to reports button
            const backToReportsBtn = document.getElementById('backToReportsBtn');
            if (backToReportsBtn) {
                backToReportsBtn.addEventListener('click', function() {
                    closeWithdrawCardPopup();
                    // Show All Reports popup again
                    if (allReportsPopup) {
                        allReportsPopup.classList.add('show');
                    }
                });
            }
            
            // Cancel button
            const cancelWithdrawCardBtn = document.getElementById('cancelWithdrawCardBtn');
            if (cancelWithdrawCardBtn) {
                cancelWithdrawCardBtn.addEventListener('click', closeWithdrawCardPopup);
            }
            
            // Close popup buttons
            const closeWithdrawPopupBtns = withdrawCardPopup.querySelectorAll('.close-popup');
            closeWithdrawPopupBtns.forEach(btn => {
                btn.addEventListener('click', closeWithdrawCardPopup);
            });
            
            // Close when clicking outside
            withdrawCardPopup.addEventListener('click', function(e) {
                if (e.target === withdrawCardPopup) {
                    closeWithdrawCardPopup();
                }
            });
            
            // Function to close popup
            function closeWithdrawCardPopup() {
                withdrawCardPopup.classList.remove('show');
                document.removeEventListener('keydown', handleWithdrawEscKey);
                
                // Reset form
                const withdrawCardForm = document.getElementById('withdrawCardForm');
                if (withdrawCardForm) {
                    withdrawCardForm.reset();
                }
                
                // Hide card selection and details containers
                const cardSelectionContainer = document.getElementById('card-selection-container');
                const withdrawDetailsContainer = document.getElementById('withdraw-details-container');
                if (cardSelectionContainer) {
                    cardSelectionContainer.style.display = 'none';
                }
                if (withdrawDetailsContainer) {
                    withdrawDetailsContainer.style.display = 'none';
                }
                
                // Disable submit button
                const submitWithdrawBtn = document.getElementById('submitWithdrawBtn');
                if (submitWithdrawBtn) {
                    submitWithdrawBtn.disabled = true;
                }
            }
            
            // ESC key handler
            function handleWithdrawEscKey(e) {
                if (e.key === 'Escape') {
                    closeWithdrawCardPopup();
                }
            }
            
            // Bank selection change handler
            const withdrawBankSelect = document.getElementById('withdraw-bank');
            if (withdrawBankSelect) {
                withdrawBankSelect.addEventListener('change', function() {
                    const bankId = this.value;
                    const cardSelectionContainer = document.getElementById('card-selection-container');
                    
                    if (bankId) {
                        // Show card selection container
                        if (cardSelectionContainer) {
                            cardSelectionContainer.style.display = 'block';
                        }
                        
                        // In a real implementation, you would load the cards for the selected bank here
                        // For now, we'll just show the container with dummy options
                        const withdrawCardSelect = document.getElementById('withdraw-card');
                        if (withdrawCardSelect) {
                            // Clear previous options except the first one
                            while (withdrawCardSelect.options.length > 1) {
                                withdrawCardSelect.remove(1);
                            }
                            
                            // Add dummy options
                            for (let i = 1; i <= 3; i++) {
                                const option = document.createElement('option');
                                option.value = i;
                                option.textContent = `Demo Card ${i}`;
                                withdrawCardSelect.appendChild(option);
                            }
                        }
                    } else {
                        // Hide card selection container if no bank selected
                        if (cardSelectionContainer) {
                            cardSelectionContainer.style.display = 'none';
                        }
                    }
                });
            }
            
            // Card selection change handler
            const withdrawCardSelect = document.getElementById('withdraw-card');
            if (withdrawCardSelect) {
                withdrawCardSelect.addEventListener('change', function() {
                    const cardId = this.value;
                    const withdrawDetailsContainer = document.getElementById('withdraw-details-container');
                    const submitWithdrawBtn = document.getElementById('submitWithdrawBtn');
                    
                    if (cardId) {
                        // Show withdraw details container
                        if (withdrawDetailsContainer) {
                            withdrawDetailsContainer.style.display = 'block';
                        }
                        
                        // Enable submit button
                        if (submitWithdrawBtn) {
                            submitWithdrawBtn.disabled = false;
                        }
                    } else {
                        // Hide withdraw details container if no card selected
                        if (withdrawDetailsContainer) {
                            withdrawDetailsContainer.style.display = 'none';
                        }
                        
                        // Disable submit button
                        if (submitWithdrawBtn) {
                            submitWithdrawBtn.disabled = true;
                        }
                    }
                });
            }
        }
        
        // Show Withdraw Card popup from All Reports popup
        const showWithdrawCardBtn = document.getElementById('showWithdrawCardBtn');
        if (showWithdrawCardBtn && withdrawCardPopup && allReportsPopup) {
            showWithdrawCardBtn.addEventListener('click', function() {
                // Hide All Reports popup
                allReportsPopup.classList.remove('show');
                
                // Show Withdraw Card popup
                withdrawCardPopup.classList.add('show');
                
                // Add ESC key support
                document.addEventListener('keydown', handleWithdrawEscKey);
            });
        }
        
        // Report Card Click Functionality
        if (reportCards.length && reportDetailsModal) {
            const closeReportModalBtns = reportDetailsModal.querySelectorAll('.close-modal');
            
            // Show report details modal when a report card is clicked
            reportCards.forEach(card => {
                card.addEventListener('click', function() {
                    const reportId = this.getAttribute('data-report-id');
                    
                    // In a real implementation, you would load the report data from the server
                    // Now we'll be setting up for that - we'd eventually make an AJAX call like:
                    // fetch(`index.php?path=report/getReportDetailsJson&report_id=${reportId}`)
                    
                    // For now, we'll just use the status from the card
                    const statusEl = this.querySelector('.report-status');
                    const isPending = statusEl.classList.contains('status-pending');
                    const bankNameEl = this.querySelector('.report-bank-name');
                    const bankName = bankNameEl ? bankNameEl.textContent.trim() : 'Unknown Bank';
                    const dateEl = this.querySelector('.report-date');
                    const reportDate = dateEl ? dateEl.textContent.trim() : '-';
                    
                    // Update report title and ID
                    const reportBankName = document.getElementById('reportBankName');
                    const reportIdEl = document.getElementById('reportId');
                    if (reportBankName) reportBankName.textContent = bankName;
                    if (reportIdEl) reportIdEl.textContent = '#' + reportId;
                    
                    // Update report date
                    const reportDateEl = document.getElementById('reportDate');
                    if (reportDateEl) reportDateEl.textContent = reportDate;
                    
                    // Update modal content based on report status
                    const reportStatusBadge = document.getElementById('reportStatusBadge');
                    if (reportStatusBadge) {
                        const status = statusEl.textContent.trim();
                        reportStatusBadge.textContent = status;
                        
                        // Set appropriate color based on status
                        if (status === 'Pending') {
                            reportStatusBadge.style.backgroundColor = '#f5a623';
                        } else if (status === 'Verified') {
                            reportStatusBadge.style.backgroundColor = '#47c98e';
                        } else if (status === 'Rejected') {
                            reportStatusBadge.style.backgroundColor = '#f44336';
                        }
                    }
                    
                    // Update action buttons based on user role and report status
                    const verifyBtn = document.getElementById('switchToVerifyViewBtn');
                    if (verifyBtn) {
                        // Check if user is Production Officer (in a real app, check from server data)
                        const isPO = verifyBtn.classList.contains('btn-verify');
                        verifyBtn.style.display = (isPending && isPO) ? 'flex' : 'none';
                    }
                    
                    // Show modal
                    reportDetailsModal.classList.add('show');
                    reportDetailsModal.style.display = 'block';
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                    
                    // Add keyboard support for ESC key
                    document.addEventListener('keydown', handleReportModalEscKey);
                });
            });
            
            // Also attach event listeners to carousel items
            const reportCarouselItems = document.querySelectorAll('.report-carousel-item');
            reportCarouselItems.forEach(card => {
                card.addEventListener('click', function() {
                    const reportId = this.getAttribute('data-report-id');
                    
                    // In a real implementation, you would load the report data from the server
                    // Now we'll be setting up for that - we'd eventually make an AJAX call like:
                    // fetch(`index.php?path=report/getReportDetailsJson&report_id=${reportId}`)
                    
                    // For now, we'll just use the status from the card
                    const statusEl = this.querySelector('.report-status');
                    const isPending = statusEl.classList.contains('status-pending');
                    const bankNameEl = this.querySelector('.report-bank-name');
                    const bankName = bankNameEl ? bankNameEl.textContent.trim() : 'Unknown Bank';
                    const dateEl = this.querySelector('.report-date');
                    const reportDate = dateEl ? dateEl.textContent.trim() : '-';
                    
                    // Update report title and ID
                    const reportBankName = document.getElementById('reportBankName');
                    const reportIdEl = document.getElementById('reportId');
                    if (reportBankName) reportBankName.textContent = bankName;
                    if (reportIdEl) reportIdEl.textContent = '#' + reportId;
                    
                    // Update report date
                    const reportDateEl = document.getElementById('reportDate');
                    if (reportDateEl) reportDateEl.textContent = reportDate;
                    
                    // Update modal content based on report status
                    const reportStatusBadge = document.getElementById('reportStatusBadge');
                    if (reportStatusBadge) {
                        const status = statusEl.textContent.trim();
                        reportStatusBadge.textContent = status;
                        
                        // Set appropriate color based on status
                        if (status === 'Pending') {
                            reportStatusBadge.style.backgroundColor = '#f5a623';
                        } else if (status === 'Verified') {
                            reportStatusBadge.style.backgroundColor = '#47c98e';
                        } else if (status === 'Rejected') {
                            reportStatusBadge.style.backgroundColor = '#f44336';
                        }
                    }
                    
                    // Update action buttons based on user role and report status
                    const verifyBtn = document.getElementById('switchToVerifyViewBtn');
                    if (verifyBtn) {
                        // Check if user is Production Officer (in a real app, check from server data)
                        const isPO = verifyBtn.classList.contains('btn-verify');
                        verifyBtn.style.display = (isPending && isPO) ? 'flex' : 'none';
                    }
                    
                    // Show modal
                    reportDetailsModal.classList.add('show');
                    reportDetailsModal.style.display = 'block';
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                    
                    // Add keyboard support for ESC key
                    document.addEventListener('keydown', handleReportModalEscKey);
                });
            });
            
            // Close modal functions
            if (closeReportModalBtns.length) {
                closeReportModalBtns.forEach(btn => {
                    btn.addEventListener('click', closeReportModal);
                });
            }
            
            // Close when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === reportDetailsModal) {
                    closeReportModal();
                }
            });
            
            // Function to close modal
            function closeReportModal() {
                reportDetailsModal.classList.remove('show');
                setTimeout(() => {
                    reportDetailsModal.style.display = 'none';
                    document.body.style.overflow = ''; // Re-enable scrolling
                    
                    // Switch back to the overview view
                    switchToOverviewView();
                    
                    // Remove keyboard listener
                    document.removeEventListener('keydown', handleReportModalEscKey);
                }, 300);
            }
            
            // ESC key handler
            function handleReportModalEscKey(e) {
                if (e.key === 'Escape') {
                    closeReportModal();
                }
            }
            
            // Switch between report views
            const reportOverviewContainer = document.getElementById('reportOverviewContainer');
            const reportVerificationContainer = document.getElementById('reportVerificationContainer');
            const rejectCardContainer = document.getElementById('rejectCardContainer');
            const switchToVerifyViewBtn = document.getElementById('switchToVerifyViewBtn');
            const backToReportOverviewBtn = document.getElementById('backToReportOverviewBtn');
            
            // Function to switch to overview view
            function switchToOverviewView() {
                if (reportOverviewContainer && reportVerificationContainer && rejectCardContainer) {
                    reportOverviewContainer.style.display = 'block';
                    reportVerificationContainer.style.display = 'none';
                    rejectCardContainer.style.display = 'none';
                }
            }
            
            // Switch to verification view
            if (switchToVerifyViewBtn && reportVerificationContainer) {
                switchToVerifyViewBtn.addEventListener('click', function() {
                    reportOverviewContainer.style.display = 'none';
                    reportVerificationContainer.style.display = 'block';
                });
            }
            
            // Back to overview from verification view
            if (backToReportOverviewBtn) {
                backToReportOverviewBtn.addEventListener('click', switchToOverviewView);
            }
            
            // Reject card functionality
            const rejectButtons = reportDetailsModal.querySelectorAll('.btn-reject');
            const backToVerificationBtn = document.getElementById('backToVerificationBtn');
            
            // Show reject card form when a reject button is clicked
            if (rejectButtons.length && rejectCardContainer) {
                rejectButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const transactionId = this.getAttribute('data-transaction-id');
                        
                        // In a real implementation, you would get the card details from the server
                        // For this demo, we'll use placeholder values
                        const cardNameInput = document.getElementById('reject-card-name');
                        const originalQuantityInput = document.getElementById('reject-original-quantity');
                        const transactionIdInput = document.getElementById('reject-transaction-id');
                        
                        if (cardNameInput && originalQuantityInput && transactionIdInput) {
                            const row = this.closest('tr');
                            const cardName = row.cells[0].textContent;
                            const quantity = row.cells[1].textContent;
                            
                            cardNameInput.value = cardName;
                            originalQuantityInput.value = quantity;
                            transactionIdInput.value = transactionId;
                        }
                        
                        // Show reject view
                        reportVerificationContainer.style.display = 'none';
                        rejectCardContainer.style.display = 'block';
                    });
                });
            }
            
            // Back to verification view from reject card form
            if (backToVerificationBtn) {
                backToVerificationBtn.addEventListener('click', function() {
                    rejectCardContainer.style.display = 'none';
                    reportVerificationContainer.style.display = 'block';
                });
            }
            
            // Cancel reject card form
            const cancelRejectBtn = document.getElementById('cancelRejectBtn');
            if (cancelRejectBtn) {
                cancelRejectBtn.addEventListener('click', function() {
                    rejectCardContainer.style.display = 'none';
                    reportVerificationContainer.style.display = 'block';
                });
            }
        }
    });
    </script>
</body>
</html>


