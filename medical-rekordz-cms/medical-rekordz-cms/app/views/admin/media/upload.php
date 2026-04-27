<div class="panel">
    <h2>Upload Media</h2>

    <form method="POST" action="<?= url('/admin/media/store') ?>" enctype="multipart/form-data">
        <?= \Core\Csrf::field() ?>

        <div class="form-group">
            <label for="file_type">File Type</label>
            <select id="file_type" name="file_type">
                <option value="image">Image</option>
                <option value="audio">Audio</option>
                <option value="video">Video</option>
                <option value="document">Document</option>
            </select>
        </div>

        <div class="form-group">
            <label for="file">File (max 50 MB)</label>
            <input type="file" id="file" name="file" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Upload</button>
            <a href="<?= url('/admin/media') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
