<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1>User Management</h1>
        <a class="btn" href="index.php?path=user/addUser">Add New User</a>
    </div>
    <?php if (!empty($users)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['user_id'] ?></td>
                        <td><?= $user['user_name'] ?></td>
                        <td><?= $user['user_email'] ?></td>
                        <td><?= $user['user_phone'] ?></td>
                        <td><?= $user['role_name'] ?></td>
                        <td>
                            <!-- Edit Button -->
                            <a href="index.php?path=user/editUser&id=<?= $user['user_id'] ?>" class="btn">Edit</a>
                            
                            <!-- Delete Button with Confirmation Prompt -->
                            <a href="index.php?path=user/deleteUser&id=<?= $user['user_id'] ?>"
                            class="btn"
                            onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>

<?php include 'views/includes/footer.php'; ?>
