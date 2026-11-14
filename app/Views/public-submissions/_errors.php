<?php if ($errors !== []): ?>
    <div class="form-errors" role="alert" aria-labelledby="formErrorsTitle">
        <h2 id="formErrorsTitle">Please correct the following</h2>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
