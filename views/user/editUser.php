<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1>Edit User</h1>
    </div>
    <form action="index.php?path=user/updateUser" method="POST">
        <input type="hidden" name="id" value="<?= $user['id'] ?>" />
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="<?= $user['name'] ?>" required />
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" required />
        </div>
        <div>
            <label>Phone:</label>
            <input type="text" name="phone" value="<?= $user['phone'] ?>" required />
        </div>
        <div>
            <label>Role:</label>
            <select name="role_id" id="role_id" required onchange="toggleBankField()">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>><?= $role['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Bank selection dropdown, only visible when Bank role is selected -->
        <div id="bank_selection" style="display: <?= $user['role_id'] == 2 ? 'block' : 'none' ?>;">
            <label for="bank_id">Bank:</label>
            <select id="bank_id" name="bank_id" <?= $user['role_id'] == 2 ? 'required' : '' ?>>
                <option value="">Select Bank</option>
                <?php foreach ($banks as $bank): ?>
                    <option value="<?= $bank['id'] ?>" <?= $user['bank_id'] == $bank['id'] ? 'selected' : '' ?>><?= $bank['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <p class="field-hint">This field is required for Bank users to restrict their access to their own bank's data.</p>
        </div>
        <button type="submit">Update User</button>
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
