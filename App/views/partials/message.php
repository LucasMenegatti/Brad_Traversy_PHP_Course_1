<?php
use Framework\Session;

$successMessage = Session::getFlashMessage('success_message');
$errorMessage = Session::getFlashMessage('error_message');
?>

<?php if(null !== $successMessage) : ?>
    <div class="message bg-green-100 p-3 my-3">
        <?= $successMessage; ?>
    </div>
<?php endif; ?>

<?php if(null !== $errorMessage) : ?>
    <div class="message bg-red-100 p-3 my-3">
        <?= $errorMessage ?>
    </div>
<?php endif; ?>