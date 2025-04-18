<?php include 'views/includes/header.php'; ?>
<?php include 'views/includes/sidebar.php'; ?>

<style>
    .edit-profile-form {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #495057;
    }
    
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 16px;
    }
    
    .form-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .form-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }
    
    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
    }
    
    .current-image {
        margin-bottom: 10px;
    }
    
    .current-image img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f0f0f0;
    }
</style>

<div class="content">
    <h1>Edit Profile</h1>
    
    <?php if (isset($_GET['error']) && $_GET['error'] === 'update_failed'): ?>
        <div class="alert alert-danger">
            Failed to update profile. Please try again.
        </div>
    <?php endif; ?>
    
    <div class="edit-profile-form">
        <form action="index.php?path=user_profile/update" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($userProfile['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($userProfile['email']) ?>" disabled>
                <small class="form-text">Email cannot be changed</small>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($userProfile['phone']) ?>">
            </div>
            
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <?php if ($userProfile['profile_picture']): ?>
                    <div class="current-image">
                        <p>Current image:</p>
                        <img src="<?= htmlspecialchars($userProfile['profile_picture']) ?>" alt="Current Profile Picture">
                    </div>
                <?php endif; ?>
                <input type="file" id="profile_picture" name="profile_picture" class="form-control">
                <small class="form-text">Upload a new profile picture (JPG, PNG, or GIF)</small>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($userProfile['role_name']) ?>" disabled>
            </div>
            
            <?php if ($userProfile['bank_name']): ?>
            <div class="form-group">
                <label>Bank</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($userProfile['bank_name']) ?>" disabled>
            </div>
            <?php endif; ?>
            
            <div class="form-buttons">
                <a href="index.php?path=user_profile" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include 'views/includes/footer.php'; ?> 