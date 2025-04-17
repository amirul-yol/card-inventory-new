<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1>Add New User</h1>
    </div>
    <form action="index.php?path=user/storeUser" method="POST">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="role_id">Role:</label>
            <select id="role_id" name="role_id" required onchange="toggleBankField()">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Bank selection dropdown, only visible when Bank role is selected -->
        <div id="bank_selection" style="display: none;">
            <label for="bank_id">Bank:</label>
            <select id="bank_id" name="bank_id">
                <option value="">Select Bank</option>
                <?php foreach ($banks as $bank): ?>
                    <option value="<?= $bank['id'] ?>"><?= $bank['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <p class="field-hint">This field is required for Bank users to restrict their access to their own bank's data.</p>
        </div>
        <div>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone">
        </div>
        <button type="submit">Add User</button>
    </form>
</div>

<script>
    // Function to show/hide bank selection based on role
    function toggleBankField() {
        const roleSelect = document.getElementById('role_id');
        const bankSelection = document.getElementById('bank_selection');
        const bankSelect = document.getElementById('bank_id');
        
        // Show bank selection only for Bank role (role_id = 2)
        if (roleSelect.value == '2') {
            bankSelection.style.display = 'block';
            bankSelect.required = true;
        } else {
            bankSelection.style.display = 'none';
            bankSelect.required = false;
            bankSelect.value = ''; // Clear selection when hidden
        }
    }
    
    // Call the function on page load to set initial state
    document.addEventListener('DOMContentLoaded', toggleBankField);
</script>

<style>
    .field-hint {
        font-size: 0.85em;
        font-style: italic;
        color: #555;
        margin-top: 2px;
    }
</style>

<?php include 'views/includes/footer.php'; ?>
