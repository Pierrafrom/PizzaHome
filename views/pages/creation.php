<div class="form-container">
    <div class="welcome">
        <img src="/img/logo.svg" alt="Logo">
        <h1><?php echo $this->viewData['title']; ?></h1>
    </div>
    <form action="/createObject" method="post">
        <?php echo $this->viewData['form'] ?>
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? ''); ?>">
        <input type="hidden" name="class" value="<?php echo htmlspecialchars($_GET['class'] ?? ''); ?>">
        <div class="submit-group">
            <button id="signin-btn" class="btn-primary" type="submit">Create</button>
            <button class="btn-error cancel-btn">Cancel</button>
        </div>
    </form>
</div>