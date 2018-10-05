<?php#1538743711?>
<input type="hidden" name="token" value="<?= hash_hmac('sha256', CURRENT_URI, $_SESSION['csrf_token']); ?>"/>
