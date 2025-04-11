<footer>
        <p>&copy; <?php echo date('Y'); ?> Card Inventory Management</p>
    </footer>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });
</script>

