<?php
require_once('../funtion.php');
require_once('../lib/Connection.php');
if(!isset($_COOKIE['cookie_admin'])){
    header("Location: login.php");
    exit();
} else {
  $db = new Connection();
  $conn = $db->connect();

  $list_cucian = [];
  $detail = null;
  //get list
  $tbl = new Transaction($conn);
  if(isset($_GET['no_kwitansi'])) {
    $detail = $tbl->detail($_GET);
  }

  if(isset($_GET['id'])) {
    $list_cucian = $tbl->detailListCucian($_GET);
  }

    // update status
    if(isset($_POST['done'])) {
      if($tbl->setToDone($_POST)) {
        setMessage("Berhasil update status!-success");
        header("Location: index.php");
      } else {
        setMessage("Gagal update status!-danger");
      }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php $title = "Detail Transaksi"; include "../templates/header.php";?>

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

                    <div class="card shadow mb-4" style="max-width: 800px">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderles" id="dataTable" width="100%" cellspacing="0">
                                    <tbody>
                                      <tr>
                                        <td class="border-0">No. Kwitansi</td>
                                        <td style="max-width: 5px" class="px-0 border-0">:</td>
                                        <td class="border-0"><?= $detail['no_kwitansi']; ?></td>
                                      </tr>

                                      <tr>
                                        <td class="border-0">Tanggal</td>
                                        <td style="max-width: 5px" class="px-0 border-0">:</td>
                                        <td class="border-0"><?= date('Y/m/d H:i', strtotime($detail['created_at'])); ?></td>
                                      </tr>

                                      <tr>
                                        <td class="border-0">Tanggal Ambil</td>
                                        <td style="max-width: 5px" class="px-0 border-0">:</td>
                                        <td class="border-0"><?= date('Y/m/d H:i', strtotime($detail['tanggal_ambil'])); ?> <span>(<?= $detail['estimate']; ?> Jam)</span></td>
                                      </tr>

                                      <tr>
                                        <td class="border-0">Tanggal Ambil</td>
                                        <td style="max-width: 5px" class="px-0 border-0">:</td>
                                        <td class="border-0"><?= date('Y/m/d H:i', strtotime($detail['tanggal_ambil'])); ?> <span>(<?= $detail['estimate']; ?> Jam)</span></td>
                                      </tr>

                                      <tr>
                                        <td class="border-0">Status</td>
                                        <td style="max-width: 5px" class="px-0 border-0">:</td>
                                        <td class="border-0">
                                          <span class="badge <?= $detail['status'] == 'DONE' ? 'badge-success' : 'badge-warning'; ?>"><?= $detail['status'] == 'DONE' ? 'Selesai' : 'Dalam Proses'; ?></span>
                                        </td>
                                      </tr>

                                      <tr>
                                        <td class="border-0">Service</td>
                                        <td style="max-width: 5px" class="px-0 border-0">:</td>
                                        <td class="border-0">
                                          <?= $detail['service']; ?> (<?= rupiah($detail['service_price']); ?>)
                                        </td>
                                      </tr>

                                      <tr>
                                        <td class="border-0">Total Bayar</td>
                                        <td style="max-width: 5px" class="px-0 border-0">:</td>
                                        <td class="border-0"><?= rupiah($detail['price']); ?></td>
                                      </tr>
                                    </tbody>
                                </table>

                                <hr>
                                <h5 class="mt-3">List Cucian</h5>
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px; text-align: center">No</th>
                                            <th>Nama</th>
                                            <th>Harga</th>
                                            <th>Qty</th>
                                            <th>Total Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($list_cucian as $index=>$item) {?>
                                        <tr>
                                            <td style="width: 80px; text-align: center"><?= $index + 1; ?></td>
                                            <td><?= $item['name']; ?></td>
                                            <td><?= rupiah($item['total_price']); ?></td>
                                            <td><?= $item['qty']; ?></td>
                                            <td><?= rupiah($item['total_price']); ?></td>
                                        </tr>
                                      <?php }?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                              <a class="btn btn-secondary mr-2" href="<?= base_url ?>transaksi">Kembali Ke Transaksi</a>
                              <button id="tombol-update" class="btn btn-sm btn-success mr-2 <?= $detail['status'] == 'DONE' ? 'invisible' : 'visible'; ?>" data-id="<?= $detail['id']; ?>" data-toggle="modal" data-target="#modal-update">Selesaikan</button>
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
              <button type="submit" name="done" class="btn btn-success">Ya, Selesai</button>
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
    </script>
</body>

</html>