<div class="panel">
    <h2>System Information</h2>

    <table class="table">
        <tbody>
            <?php foreach ($info as $label => $value): ?>
                <tr>
                    <td><strong><?= e($label) ?></strong></td>
                    <td><?= e($value) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="panel">
    <h2>Loaded Extensions</h2>
    <div class="extension-list">
        <?php foreach ($extensions as $ext): ?>
            <span class="badge"><?= e($ext) ?></span>
        <?php endforeach; ?>
    </div>
</div>
