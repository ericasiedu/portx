<?php
use Lib\ACL;
$system_object="excel-upload";
ACl::verifyRead($system_object);
$title = ' Excel Reader'; ?>
<?php $vesselAndVoyages = 'active open'; ?>
<?php $excelReader = 'active'; ?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php'); ?>

    <!-- Main container -->
    <main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Excel</strong> File Upload</h4>
            <div class="card-body">

                <?php if (!empty($msg)){ echo $msg; }  ?>

        <form action="/api/excel_upload/upload" method="POST" enctype="multipart/form-data">

               <div class="row">
                 <div class="col-md-6">

                      <select name="voyage_list" class="form-control" data-provide="selectpicker">
                          <option value="">Select</option>
                          <option value="vessel">Vessel</option>
                          <option value="container">Container</option>
                      </select>

                 </div>


           <div class="col-md-6">
               <div class="input-group file-group">
                   <input type="text" class="form-control file-value" placeholder="Choose file..." readonly>
                   <input type="file" name="file_upload" multiple>
                   <span class="input-group-btn">
                    <button class="btn btn-light file-browser" type="button"><i class="fa fa-upload"></i></button>
                  </span> </div>
           </div>
       </div><!-- end of row -->

        <div class="row" style="padding:20px 0">
            <div class="col-md-12">
                <button class="btn btn-primary" type="submit" name="submit">Upload File</button>
            </div>

        </form>

              </div><!-- end of card-body -->
            </div><!-- end of card -->
    </div><!-- end of main content -->

<?php require_once('includes/footer.php') ?>

        <script>
            system_object='<?php echo $system_object?>';
        </script>
