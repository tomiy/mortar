<?php#1545222432?>
<?=hash_hmac('sha256', CURRENT_URI, $_SESSION['csrf_token'])?><br>
<?=hash_hmac('sha256', CURRENT_URI, $_SESSION['csrf_token'])?><br>

