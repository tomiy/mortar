<?php#1538749324?>
<input type="hidden" name="_token" value="<?= hash_hmac('sha256', CURRENT_URI, $_SESSION['csrf_token']); ?>"/>
