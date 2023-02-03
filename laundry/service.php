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
  $tbl = new Service($conn);
  $rows = $tbl->get();

  //tambah
  if(isset($_POST['tambah'])) {
    if($tbl->create($_POST)) {
      setMessage("Berhasil menambah data!-success");
      header("Location: service.php");
    } else {
      setMessage("Gagal menambah data!-danger");
    }
  }

    //tambah
    if(isset($_POST['edit'])) {
      if($tbl->update($_POST)) {
        setMessage("Berhasil update data!-success");
        header("Location: service.php");
      } else {
        setMessage("Gagal update data!-danger");
      }
    }

        //hapus
        if(isset($_POST['delete'])) {
          if($tbl->delete($_POST['id'])) {
            setMessage("Berhasil hapus data!-success");
            header("Location: service.php");
          } else {
            setMessage("Gagal hapus data!-danger");
          }
        }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php $title = "Service"; include "../templates/header.php";?>

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
                              <h6 class="m-0 font-weight-bold text-primary">Data Service</h6>
                              <div class="d-flex gap-2">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modal-laundry">+ Tambah</button>
                              </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px; text-align: center">No</th>
                                            <th>Service</th>
                                            <th>Harga</th>
                                            <th>Estimasi</th>
                                            <th style="width: 200px; text-align: center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($rows as $index=>$item) {?>
                                        <tr>
                                            <td style="width: 100px; text-align: center"><?= $index + 1; ?></td>
                                            <td><?= $item['name']; ?></td>
                                            <td><?= rupiah($item['price']); ?></td>
                                            <td><?= $item['estimate']; ?></td>
                                            <td style="width: 200px; text-align: center">
                                                <button id="tombol-edit" class="btn btn-sm btn-warning mr-2" data-toggle="modal" data-target="#modal-laundry-update"
                                                data-id="<?= $item['id']; ?>"
                                                data-name="<?= $item['name']; ?>"
                                                data-price="<?= $item['price']; ?>"
                                                data-estimate="<?= $item['estimate']; ?>"
                                                >Edit</button>
                                                <button id="tombol-delete" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-delete" data-id="<?= $item['id']; ?>">Hapus</button>
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

    <div class="modal fade" id="modal-laundry" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Tambah Service</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="" method="post" class="user needs-validation">
          <div class="modal-body">
              <div class="form-group mb-3">
                <label for="name">Service</label>
                <input type="text" class="form-control form-control-user" name='name' placeholder="Masukan nama service">
              </div>

              <div class="form-group mb-3">
                <label for="price">Harga <small>(Rp)</small></label>
                <input type="number" class="form-control form-control-user" name='price' placeholder="Masukan harga">
              </div>

              <div class="form-group mb-3">
                <label for="price">Estimasi <small>(Jam)</small></label>
                <input type="number" class="form-control form-control-user" name='estimate' placeholder="Masukan waktu pengerjaan">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal-laundry-update" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Update Service</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="" method="post" class="user">
          <div class="modal-body">
              <input type="hidden" id='id-laundry' name="id">

              <div class="form-group mb-3">
                <label for="name">Jenis</label>
                <input type="text" class="form-control form-control-user" id="name" name='name' placeholder="Masukan jenis">
              </div>

              <div class="form-group mb-3">
                <label for="price">Harga <small>(Rp)</small></label>
                <input type="number" class="form-control form-control-user" id="price" name='price' placeholder="Masukan harga">
              </div>

              <div class="form-group mb-3">
                <label for="price">Estimasi <small>(Jam)</small></label>
                <input type="number" class="form-control form-control-user" id="estimate" name='estimate' placeholder="Masukan waktu pengerjaan">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" name="edit" class="btn btn-primary">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Hapus Data</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="" method="post">
            <div class="modal-body">
            <input type="hidden" id='id-delete' name="id">
            Apakah kamu yakin akan melanjutkan hapus data?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php include "../templates/script.php";?>

    <script>
      $(document).on('click', '#tombol-edit', function() {
        const id = $(this).data('id')
        const name = $(this).data('name')
        const estimate = $(this).data('estimate')
        const price = $(this).data('price')

        $("#id-laundry").val(id)
        $('.modal-body #name').val(name)
        $('.modal-body #price').val(price)
        $('.modal-body #estimate').val(estimate)
      })

      $(document).on('click', '#tombol-delete', function() {
        const id = $(this).data('id')
        $("#id-delete").val(id)
      })
    </script>
</body>

</html>