<div style="padding:20px;max-width:800px;margin:auto;background: #eee;">
    <h1>Edit Sample</h1>
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="/edit?id=<?= htmlspecialchars($data['id']) ?>">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($data['name']) ?>" required><br><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required><br><br>
        
        <input type="submit" value="Save Changes">
    </form>
</div>