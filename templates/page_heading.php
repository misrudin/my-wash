 <!-- Page Heading -->
<?php if ($title) { ?>
  <h1 class="h3 mb-4 text-gray-800"><?php echo $title; ?></h1>
<?php } ?>

<?php if(isset($_COOKIE['message'])) {
    $msg = $_COOKIE['message']; ?>
    <div class="alert alert-dismissible fade show alert-<?= explode("-", $msg)[1] ?>" role="alert">
     <?php echo explode("-", $msg)[0]; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php } ?>