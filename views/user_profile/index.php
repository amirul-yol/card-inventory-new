<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<div class="content">
    <h1>User Profile</h1>

    <?php if ($userProfile): ?>
        <div class="profile">
            <img src="<?= $userProfile['profile_picture'] ?>" alt="Profile Picture" style="width: 100px; height: 100px; border-radius: 50%;">
            <p><strong>Name:</strong> <?= $userProfile['name'] ?></p>
            <p><strong>Email:</strong> <?= $userProfile['email'] ?></p>
            <p><strong>Phone:</strong> <?= $userProfile['phone'] ?></p>
        </div>
    <?php else: ?>
        <p>No user profile data found.</p>
    <?php endif; ?>
</div>

<?php include 'views/includes/footer.php'; ?>
