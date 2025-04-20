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
                bankData.cards.forEach(card => {
                    if (card.card_id == cardId) {
                        cardData = card;
                    }
                });
                
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
                
                // Find the card data
                let cardData = null;
                bankData.cards.forEach(card => {
                    if (card.card_id == cardId) {
                        cardData = card;
                    }
                });
                
                if (!cardData) {
                    return;
                }
                
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
                                            <a href="index.php?path=card/editTransactionForm&transaction_id=${transaction.id}" class="edit-transaction-btn">
                                                Edit
                                            </a>
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
            
            // Function to show deposit popup
            function showDepositPopup(cardId) {
                const depositPopup = document.getElementById('depositCardPopup');
                const depositCardIdInput = document.getElementById('deposit_card_id');
                const quantityInput = document.getElementById('quantity');
                const closePopupBtn = document.querySelector('.close-popup');
                const cancelBtn = document.getElementById('cancelDepositBtn');
                
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
                
                // Handle form submission (optional - we're using the form's action attribute)
                const depositForm = document.getElementById('depositCardForm');
                depositForm.addEventListener('submit', function(e) {
                    // Form validation here if needed
                    // e.preventDefault(); // Remove this if you want the form to submit normally
                    
                    // If you want to handle form submission via AJAX instead:
                    /*
                    e.preventDefault();
                    const formData = new FormData(depositForm);
                    
                    fetch('index.php?path=card/processDepositCard', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeDepositPopup();
                            // Refresh transactions
                            fetchAndShowTransactions(cardId, bankData);
                        } else {
                            alert(data.error || 'Failed to deposit card');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                    */
                });
            }
        }

        // Add Card Popup functionality
        const addCardBtn = document.getElementById('addCardBtn');
        const addCardPopup = document.getElementById('addCardPopup');
        const closeAddCardPopupBtns = addCardPopup ? addCardPopup.querySelectorAll('.close-popup, #cancelAddCardBtn') : [];
        
        if (addCardBtn && addCardPopup) {
            // Show popup when Add Card button is clicked
            addCardBtn.addEventListener('click', function() {
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
                // Show Add Bank popup when button is clicked
                showAddBankBtn.addEventListener('click', function() {
                    // Hide the All Banks popup temporarily
                    allBanksPopup.classList.remove('show');
                    
                    // Show the Add Bank popup
                    addBankPopup.classList.add('show');
                    
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
            }
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
    });
    </script>
</body>
</html>

