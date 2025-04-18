<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<style>
    .profile-container {
        display: flex;
        flex-direction: column;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .profile-picture {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f0f0f0;
        margin-right: 30px;
    }
    
    .profile-name {
        font-size: 24px;
        font-weight: bold;
        margin: 0 0 5px 0;
    }
    
    .profile-role {
        color: #007bff;
        font-size: 16px;
        margin: 0;
    }
    
    .profile-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .detail-group {
        margin-bottom: 20px;
    }
    
    .detail-label {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .detail-value {
        font-size: 16px;
        font-weight: 500;
    }
    
    .profile-joined {
        font-size: 14px;
        color: #6c757d;
        margin-top: 30px;
        text-align: center;
    }
    
    .edit-profile-btn {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        margin-top: 20px;
        text-align: center;
        transition: background-color 0.2s;
    }
    
    .edit-profile-btn:hover {
        background-color: #0069d9;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>

<div class="content">
    <h1>User Profile</h1>
    
    <?php if (isset($_GET['success']) && $_GET['success'] === 'profile_updated'): ?>
        <div class="alert alert-success">
            Your profile has been updated successfully.
        </div>
    <?php endif; ?>

    <?php if ($userProfile): ?>
        <div class="profile-container">
            <div class="profile-header">
                <?php if ($userProfile['profile_picture']): ?>
                    <img src="<?= htmlspecialchars($userProfile['profile_picture']) ?>" alt="Profile Picture" class="profile-picture">
                <?php else: ?>
                    <img src="assets/images/default-profile.png" alt="Default Profile" class="profile-picture">
                <?php endif; ?>
                
                <div>
                    <h2 class="profile-name"><?= htmlspecialchars($userProfile['name']) ?></h2>
                    <p class="profile-role"><?= htmlspecialchars($userProfile['role_name']) ?></p>
                    <?php if ($userProfile['bank_name']): ?>
                        <p class="profile-role"><?= htmlspecialchars($userProfile['bank_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="profile-details">
                <div class="detail-group">
                    <p class="detail-label">Email</p>
                    <p class="detail-value"><?= htmlspecialchars($userProfile['email']) ?></p>
                </div>
                
                <div class="detail-group">
                    <p class="detail-label">Phone</p>
                    <p class="detail-value"><?= htmlspecialchars($userProfile['phone']) ?></p>
                </div>
                
                <div class="detail-group">
                    <p class="detail-label">User ID</p>
                    <p class="detail-value"><?= htmlspecialchars($userProfile['id']) ?></p>
                </div>
            </div>
            
            <p class="profile-joined">Member since <?= date('F j, Y', strtotime($userProfile['joined_date'])) ?></p>
            
            <a href="index.php?path=user_profile/edit" class="edit-profile-btn">Edit Profile</a>
        </div>
    <?php else: ?>
        <p>No user profile data found.</p>
    <?php endif; ?>
</div>

<?php include 'views/includes/footer.php'; ?>
