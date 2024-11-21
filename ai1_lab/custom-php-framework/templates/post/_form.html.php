<?php
    /** @var $post ?Post */

use App\Model\Post;

?>

<div class="form-group">
    <label for="subject">Subject</label>
    <input type="text" id="subject" name="post[subject]" value="<?= $post ? $post->getSubject() : '' ?>">
</div>

<div class="form-group">
    <label for="content">Content</label>
    <textarea id="content" name="post[content]"><?= $post? $post->getContent() : '' ?></textarea>
</div>

<div class="form-group">
    <label></label>
    <input type="submit" value="Submit">
</div>
