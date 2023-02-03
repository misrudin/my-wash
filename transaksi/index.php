<?php
require_once('../funtion.php');
require_once('../lib/Connection.php');
if(!isset($_COOKIE['cookie_admin'])){
    header("Location: login.php");
    exit();
} else {
  $db = new Connection();
  $conn = $db->connect();

  //get list
  $tbl = new Transaction($conn);
  $rows = $tbl->get();
}
?>

<!DOCTYPE html>
<html lang="en">

<?php $title = "Transaksi"; include "../templates/header.php";?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "../templates/sidebar.php";?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "../templates/navbar.php";?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <?php include "../templates/page_heading.php";?>
                          <!-- DataTales Example -->
                      <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between">
                              <h6 class="m-0 font-weight-bold text-primary">Data Transaction</h6>
                              <div class="d-flex">
                                <a href="<?= base_url ?>transaksi/tambah.php">
                                  <button class="btn btn-primary">+ Tambah</button>
                                </a>
                                <button class="btn btn-info ml-2" data-toggle="modal" data-target="#modal-laundry">Laporan</button>
                              </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px; text-align: center">No</th>
                                            <th>Kwintansi</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Service</th>
                                            <th>Total Bayar</th>
                                            <th style="width: 170px; text-align: center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($rows as $index=>$item) {?>
                                        <tr>
                                            <td style="width: 80px; text-align: center"><?= $index + 1; ?></td>
                                            <td><?= $item['no_kwitansi']; ?></td>
                                            <td><?= date('Y/m/d', strtotime($item['created_at'])); ?></td>
                                            <td><?= $item['status'] == 'DONE' ? 'Selesai' : 'Dalam Proses'; ?></td>
                                            <td><?= $item['service']; ?></td>
                                            <td><?= rupiah($item['price']); ?></td>
                                            <td style="width: 200px; text-align: center">
                                                <button id="tombol-update" class="btn btn-sm btn-warning mr-2 <?= $item['status'] == 'DONE' ? 'invisible' : 'visible'; ?>" data-id="<?= $item['id']; ?>">Selesaikan</button>
                                                <button data-id="<?= $item['id']; ?>" id="tombol-view" class="btn btn-sm btn-danger">View</button>
                                            </td>
                                        </tr>
                                    <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include "../templates/footer.php";?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="modal-update" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Transaksi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="" method="post">
            <div class="modal-body">
            <input type="hidden" id='id-update' name="id">
                Apakah laundry ini sudah selesai?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Belum</button>
              <button type="submit" name="done" class="btn btn-danger">Selesai</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php include "../templates/script.php";?>

    <script>
        $(document).on('click', '#tombol-update', function() {
            const id = $(this).data('id')
            $("#id-update").val(id)
        })

        $(document).on('click', '#tombol-view', function() {
            const id = $(this).data('id')
            $("#id-view").val(id)
        })
    </script>
</body>

</html>