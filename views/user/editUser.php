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
            <select name="role_id" required>
                <option value="1" <?= $user['role_id'] == 1 ? 'selected' : '' ?>>Admin</option>
                <option value="2" <?= $user['role_id'] == 2 ? 'selected' : '' ?>>Bank</option>
                <option value="3" <?= $user['role_id'] == 3 ? 'selected' : '' ?>>PO</option>
                <option value="4" <?= $user['role_id'] == 4 ? 'selected' : '' ?>>LO</option>
            </select>
        </div>
        <button type="submit">Update User</button>
    </form>
</div>

<?php include 'views/includes/footer.php'; ?>
