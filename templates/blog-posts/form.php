<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?= $isEdit ? 'Edit' : 'Add' ?> Blog Post</h2>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?= htmlspecialchars($blogPost['title'] ?? '') ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please provide a title.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug *</label>
                            <input type="text" 
                                class="form-control" 
                                id="slug" 
                                name="slug" 
                                value="<?= htmlspecialchars($blogPost['slug'] ?? '') ?>"
                                required>
                            <div class="form-text">URL-friendly version of the title</div>
                            <div class="invalid-feedback">
                                Please provide a slug.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Content *</label>
                            <textarea class="form-control" 
                                      id="body" 
                                      name="body" 
                                      rows="10" 
                                      required><?= htmlspecialchars($blogPost['body'] ?? '') ?></textarea>
                            <div class="invalid-feedback">
                                Please provide content for the blog post.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="seo_keywords" class="form-label">SEO Keywords</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="seo_keywords" 
                                   name="seo_keywords" 
                                   value="<?= htmlspecialchars($blogPost['seo_keywords'] ?? '') ?>"
                                   placeholder="keyword1, keyword2, keyword3">
                            <div class="form-text">Comma-separated keywords for SEO</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Author</label>
                                <?php if ($currentUserEmail === 'josh+123@udev.com'): ?>
                                    <select class="form-select" id="user_id" name="user_id">
                                        <option value="">Select an author</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['id'] ?>" 
                                                <?= ($post['user_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <?php 
                                        $currentUser = array_filter($users, function($user) use ($currentUserEmail) {
                                            return $user['email'] === $currentUserEmail;
                                        });
                                        $currentUser = reset($currentUser);
                                    ?>
                                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?>" disabled>
                                    <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?= htmlspecialchars($currentUser['id']) ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mb-2">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_published" 
                                           name="is_published" 
                                           <?= ($blogPost['published_at'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_published">Published (date/time)</label>
                                    <span class="form-text mb-2">Saved as draft if unpublished</span>
                                </div>
                                
                                <div id="published_at_container" style="display: <?= ($blogPost['published_at'] ?? false) ? 'block' : 'none' ?>;">
                                    <input type="datetime-local" 
                                           class="form-control" 
                                           id="published_at" 
                                           name="published_at" 
                                           value="<?= ($blogPost['published_at'] ?? '') ? date('Y-m-d\TH:i', strtotime($blogPost['published_at']) + date('Z')) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/blog-posts" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <?= $isEdit ? 'Update' : 'Create' ?> Blog Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE -->
<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/ralw0bifs7ebx4om9620xzcxv3xfjvw7vo8m4rixaxa3788e/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()

// Function to create slug from text
function createSlug(text, maxLength = 100) {
    let slug = text
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
    
    if (slug.length > maxLength) {
        slug = slug.substr(0, maxLength);
        const lastHyphen = slug.lastIndexOf('-');
        if (lastHyphen !== -1) {
            slug = slug.substr(0, lastHyphen);
        }
    }
    
    return slug;
}

// Auto-generate slug from title
['input', 'keyup'].forEach(event => {
    document.getElementById('title').addEventListener(event, function(e) {
    <?php if ($isEdit): ?>
        if (!document.getElementById('slug').value) {
            document.getElementById('slug').value = createSlug(e.target.value);
        }
    <?php else: ?>
        document.getElementById('slug').value = createSlug(e.target.value);
    <?php endif; ?>
    });
});

// Initialize TinyMCE for the body field
tinymce.init({
    selector: '#body',
    height: 500,
    menubar: false,
    plugins: [
        'advlist', 'autolink', 'emoticons', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount',

        // Your account includes a free trial of TinyMCE premium features
        // Try the most popular premium features until Mar 8, 2025:
        // 'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed',
        // 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable',
        // 'advcode', 'editimage', 'advtemplate', 'mentions', 'tinycomments', 'tableofcontents',
        // 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown',
        // 'importword', 'exportword', 'exportpdf'
    ],
    toolbar: [
        // 1st Row
        'undo redo | blocks fontfamily fontsize | ' +
        'bold italic | alignleft aligncenter alignright alignjustify | ',
        // 2nd Row
        'link image media table mergetags | ' +
        'spellcheckdialog a11ycheck typography | ' +
        'bullist numlist indent outdent | ' +
        'emoticons charmap | ' +
        'removeformat | ' +
        'help'
    ],
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; }',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    }
});

// Convert UTC to local time when loading the form
document.addEventListener('DOMContentLoaded', function() {
    const publishedAtInput = document.getElementById('published_at');
    if (publishedAtInput.value) {
        // Convert UTC to local time for display
        const utcDate = new Date(publishedAtInput.value + 'Z'); // Append Z to treat as UTC
        const localDateTime = new Date(utcDate).toISOString().slice(0, 16);
        publishedAtInput.value = localDateTime;
    }
});

// Handle published checkbox changes
document.getElementById('is_published').addEventListener('change', function() {
    const publishedAtContainer = document.getElementById('published_at_container');
    const publishedAtInput = document.getElementById('published_at');
    
    if (this.checked) {
        publishedAtContainer.style.display = 'block';
        // Set current local time if no date is set
        if (!publishedAtInput.value) {
            const now = new Date();
            publishedAtInput.value = now.toISOString().slice(0, 16);
        }
    } else {
        publishedAtContainer.style.display = 'none';
        publishedAtInput.value = ''; // Clear the date when unchecked
    }
});

// Handle manual date/time changes
document.getElementById('published_at').addEventListener('input', function() {
    const isPublishedCheckbox = document.getElementById('is_published');
    if (this.value) {
        isPublishedCheckbox.checked = true;
        document.getElementById('published_at_container').style.display = 'block';
    }
});

// Validate date before form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const publishedAtInput = document.getElementById('published_at');
    const isPublishedCheckbox = document.getElementById('is_published');

    if (!isPublishedCheckbox.checked) {
        publishedAtInput.value = ''; // Ensure the date is cleared if not published
    } else if (publishedAtInput.value) {
        try {
            // Validate that the date is correct
            const localDate = new Date(publishedAtInput.value);
            if (isNaN(localDate.getTime())) {
                throw new Error('Invalid date');
            }
        } catch (error) {
            e.preventDefault();
            alert('Please enter a valid date and time');
        }
    }
});
</script> 