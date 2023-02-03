<?php
require_once('../funtion.php');
require_once('../lib/Connection.php');
$msg = '';
$isError = false;
if(!isset($_COOKIE['cookie_admin'])){
    header("Location: login.php");
    exit();
} else {
  $db = new Connection();
  $conn = $db->connect();

  // get list transaction
  $tbl = new Transaction($conn);
  $rows = $tbl->get();

  // get list of laundry service
  $serviceTbl = new Service($conn);
  $services = $serviceTbl->get();

  // get list of jenis laundry
  $launddryTbl = new Laundry($conn);
  $laundryList = $launddryTbl->get();

  // tambah transaksi
  $no_kwitansi = generateRandomString();
  $error = '';
  $id_service = '';
  $customer_phone = '';
  $customer_name = '';
  $laundry = '';

    //tambah
    if(isset($_POST['tambah'])) {
      $id_service = $_POST['id_service'];
      $customer_phone = $_POST['customer_phone'];
      $customer_name = $_POST['customer_name'];
      $laundry = $_POST['laundry'];

      if ($id_service == '') {
          $error .= "<li>Silakan pilih service!</li>";
      }
      if ($customer_phone == '') {
        $error .= "<li>Silakan masukan nomor hp pelanggan!</li>";
      }
      if ($customer_name == '') {
        $error .= "<li>Silakan masukan nama pelanggan!</li>";
      }
      function filter($var) {
        return($var['id'] == '' || $var['qty'] < 1);
      }
      $errValidation = array_filter($laundry, "filter");
      if (count($errValidation) != 0) {
        $error .= "<li>Silakan masukan data cucian!</li>";
      }
      if( $id_service != '' && $customer_phone != '' &&  $customer_name != '' && count($errValidation) == 0) {
        if($tbl->create($_POST)) {
          setcookie('kwitansi', null, -1, '/');
          setMessage("Berhasil menambah data!-success");
          header("Location: index.php");
        } else {
          setMessage("Gagal menambah data!-danger");
        }
      }
    } else {
      $error = '';
    }

    $cust_name = '';
    $cust_phone = '';
    $cust_address = null;
    //check customer
    if(isset($_POST['customer_phone'])) {
      $tblCust = new Customer($conn);
      $cust = $tblCust->findByPhone($_POST['customer_phone']);
      if(!empty($cust)) {
        $cust_name = $cust['name'];
        $cust_phone = $cust['phone_number'];
        $cust_address = $cust['address'];
      } else {
        $cust_name = '';
        $cust_address = null;
      }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php $title = "Transaksi Baru"; include "../templates/header.php";?>

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
                    <!-- heading end -->

                    <div class="card shadow mb-4" style="max-width: 700px;">
                      <div class="card-body">
                      <form action="" method="post">
                        <div class="form-group">
                          <label for="no_kwitansi">No. Kwitansi</label>
                          <input name='no_kwitansi' type="text" value="<?= $no_kwitansi; ?>" readonly class="form-control" id="no_kwitansi" aria-describedby="emailHelp">
                        </div>
                        <div class="form-group">
                          <label for="date">Tanggal</label>
                          <input type="text" value="<?= getTimeNow() ?>" readonly class="form-control" id="date">
                        </div>

                        <div class="p-2 rounded-3 bg-gray-100 mb-3">
                          <p class="mb-1">Detail Pelanggan</p>
                          <div class="form-group">
                            <label for="customer_phone">No. Hp</label>
                            <input type="text" name='customer_phone' value="<?= $cust_phone; ?>" onblur="this.form.submit()" class="form-control" id="customer_phone">
                          </div>

                          <div class="form-group">
                            <label for="customer_name">Nama</label>
                            <input <?php if($cust_name !== '') { echo('readonly'); } ?> type="text" value="<?= $cust_name; ?>" name='customer_name' class="form-control" id="customer_name">
                          </div>

                          <div class="form-group">
                            <label for="customer_address">Alamat</label>
                            <textarea <?= !empty($cust_address) ? 'readonly' : '' ?> name='customer_address' class="form-control" style="resize: none;" id="customer_address" rows="3"><?= $cust_address; ?></textarea>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="date">Service</label>
                          <select name='id_service' class="form-control" id="exampleFormControlSelect1">
                          <?php foreach ($services as $index=>$service) {?>
                            <option value="<?= $service['id']; ?>"><?= $service['name']; ?></option>
                          <?php }?>
                          </select>
                        </div>

                        <div class="form-group">
                          <label for="date">Cucian</label>
                          <div id='input-laundry'>
                            <div class="d-flex">
                              <select class="form-control mr-2" id="exampleFormControlSelect1" name="laundry[0][id]" onchange="onSelect(event)">
                                <option value="">Pilih Jenis Laundry</option>
                                <?php foreach ($laundryList as $index=>$laundry) {?>
                                  <option data-price="<?= $laundry['price'] ?>" value="<?= $laundry['id'] ?>"><?= $laundry['name'] ." - (" . ($laundry['type'] === 'SATUAN' ? "Satuan" : "Kiloan") .")"; ?></option>
                                <?php }?>
                              </select>
                              <input type="hidden" name="laundry[0][price]">
                              <input value="1" style="width: 190px;" type="number" class="form-control" name="laundry[0][qty]">
                            </div>
                          </div>
                          <div class="d-flex justify-content-end mt-2">
                            <button id='first-add' onclick="handleAdd()" name="tambah" type='button' class="btn btn-outline-primary btn-sm">+ Tambah</button>
                            </div>
                        </div>
                        <?php if($error) { ?>
                                            <div class="alert alert-danger mt-3" role="alert">
                                                <ul class="m-0 px-3"><?php echo $error ?></ul>
                                            </div>
                                        <?php } ?>
                        <div class="d-flex justify-content-end mt-3 border-top pt-3">
                          <button name='tambah' type="submit" class="btn btn-primary">Proses Transaksi</button>
                        </div>
                      </form>
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

    <?php include "../templates/script.php";?>
    <script>
      let index = 0;
      let nextIndex = 1;
      function handleAdd() {
        const html = `
                            <div class="d-flex mt-2" id='input-${index}'>
                              <select class="form-control mr-2" id="exampleFormControlSelect1" onchange="onSelect(event)" name="laundry[${nextIndex}][id]">
                                <option value="">Pilih Jenis Laundry</option>
                                <?php foreach ($laundryList as $index=>$laundry) {?>
                                  <option data-price="<?= $laundry['price'] ?>" value="<?= $laundry['id'] ?>"><?= $laundry['name'] ." - (" . ($laundry['type'] === 'SATUAN' ? "Satuan" : "Kiloan") .")"; ?></option>
                                <?php }?>
                              </select>
                              <input type="hidden" name="laundry[${nextIndex}][price]">
                              <input value="1" style="width: 150px;" type="number" class="form-control" name="laundry[${nextIndex}][qty]">
                              <button id='button-${index}' onclick="handleDelete(event)" name="hapus" type='button' class="btn btn-danger ml-2">-</button>
                            </div>
      `
        $('#input-laundry').append(html)
        index += 1;
        nextIndex += 1;
      }

      function handleDelete(e) {
        $(`#input-laundry #input-${e.target.id.split("-")[1]}`).remove();
        nextIndex -= 1;
      }

      function onSelect(e) {
        $(e.target.nextElementSibling).val(e.target.selectedOptions[0].getAttribute('data-price'))
      }
    </script>
</body>

</html>