<?php include "include/header.php";
include "include/UserInfo.php";
include 'include/SentimentAnalyzer.php';

$review = "";
if (isset($_SESSION['user'])) {
  $user_id = $_SESSION['user'];
}
if (isset($_GET['pid'])) {
  $pid = $_GET['pid'];

  $user_ip = UserInfo::get_ip();
  $user_device = UserInfo::get_device();
  $user_OS = UserInfo::get_os();
  $user_browser = UserInfo::get_browser();

  if (isset($_POST['submit'])) {
    $review = $_POST['review'];

    if (empty($_POST['review'])) {
      Notification("Please Write Something About Product !", "3", $notify_icons['error']);
    } else {
      // detect review sentiment
      $sat = new SentimentAnalyzerTest(new SentimentAnalyzer());
      $sat->trainAnalyzer('trainingSet/data.neg', 'negative', 5000); //training with negative data
      $sat->trainAnalyzer('trainingSet/data.pos', 'positive', 5000); //trainign with positive data
      $sentimentAnalysisOfSentence = $sat->analyzeSentence($review);
      $status = strtolower($sentimentAnalysisOfSentence['sentiment']);

      $query  = "INSERT INTO feedback VALUES(NULL, '$review','$pid','$user_id','$user_ip','$user_device','$user_OS','$user_browser','$status', NOW(), NOW())";
      if (mysqli_query($con, $query)) {
        Notification("Thanks For Your Review !", "2", $notify_icons['success']);
        $review = "";
      } else {
        Notification("Something Went Wrong !", "4", $notify_icons['error']);
      }
    }
  }
}

?>
<div class="row justify-content-center my-3">
  <div class="col-md-12 shadow-sm p-0">
    <div class="card">
      <h4 class="card-header bg-success text-center text-white">Product Details</h4>
      <div class="card-body">
        <?php
        $query = "SELECT p.*, c.name AS category FROM products p JOIN category c ON p.cat_id = c.id WHERE p.id = '$pid'";
        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_array($result);
          $pid = $row['id'];
          $name = $row['name'];
          $image = "admin/assets/imgs/products/" . $row['image'];
          $category = $row['category'];
          $company_name = $row['company_name'];
          $quality = $row['quality'];
          $description = $row['description'];
        } else {
          header("Location: all_products.php");
        }
        ?>
        <div class="row">
          <div class="col-md-12 text-center mb-4 text-success">
            <span class="h4 font-weight-bold text-uppercase  my-font-size"><?php echo $name; ?></span>
          </div>
          <div class="col-md-8" id="myOrder2">
            <div class="row">
              <div class="col-md-6 col-6 myspace">
                <span class="text-dark h5 font-weight-bold my-font-size">Company: </span>
              </div>
              <div class="col-md-6 col-6 myspace">
                <p class="text-center h5 text-capitalize my-font-size"><?php echo $company_name; ?></p>
                <br class="d-none d-sm-inline">
              </div>
              <div class="col-md-6 col-6 myspace">
                <span class="text-dark h5 font-weight-bold my-font-size">Category: </span>
              </div>
              <div class="col-md-6 col-6 myspace">
                <p class="text-center h5 text-capitalize my-font-size"><?php echo $category; ?></p>
                <br class="d-none d-sm-inline">
              </div>
              <div class="col-md-6 col-6 myspace">
                <span class="text-dark h5 font-weight-bold my-font-size">Quality: </span>
              </div>
              <div class="col-md-6 col-6 myspace">
                <p class="text-center h5 text-capitalize my-font-size"><?php echo $quality; ?></p>
                <br class="d-none d-sm-inline">
              </div>
              <div class="col-md-12 myspace  mb-1">
                <span class="text-dark h5 font-weight-bold my-font-size">Description: </span>
              </div>
              <div class="col-md-12">
                <p class="text-break text-capitalize my-font-size"><?php echo $description; ?></p>
                <br class="d-none d-sm-inline">
              </div>
              <div class="col-md-12 myspace  mb-1">
                <span class="text-dark h5 font-weight-bold my-font-size">Reviews From Users: </span>
              </div>
              <br class="d-none d-sm-inline">

              <?php
              $query = "SELECT f.*, u.name FROM feedback f JOIN users u ON f.user_id = u.id WHERE f.p_id = '$pid'";
              $result = mysqli_query($con, $query);
              if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                  $count = 1;
                  $feedback = $row['feedback'];
                  $name = $row['name'];
              ?>
                  <br class="d-none d-sm-inline">
                  <div class="col-md-12 myspace  mb-1">
                    <span class="text-dark h6 font-weight-bold my-font-size"><?php echo $name; ?>: </span>
                  </div>
                  <br class="d-none d-sm-inline">
                  <div class="col-md-12">
                    <p class="text-break text-capitalize my-font-size"><?php echo $feedback; ?></p>
                    <hr class="mb-1 mt-1">
                  </div>

                <?php
                  $count++;
                }
              } else {
                ?>
                <div class="col-md-12 myspace  mb-1">
                  <hr class="mb-1 mt-1">
                  <span class="text-dark h6 font-weight-bold my-font-size">No Reviews Yet ! </span>
                </div>
              <?php
              }
              ?>
            </div>
          </div>
          <div class="col-md-4" id="myOrder1">
            <img src="<?php echo $image; ?>" alt="Product Image" class="img-fluid img-thumbnail myspace" alt="">
          </div>
        </div>
        <?php
        if (isset($_SESSION['user'])) {; ?>
          <form action="" class="form" method="POST">
            <div class="row justify-content-center">
              <div class="col-md-12">
                <br class="d-none d-sm-inline">

                <div class="form-group">
                  <label for="review" class="h5">Give Your Review</label>
                  <textarea name="review" id="review" class="form-control" rows="5" cols="40" style="resize: none;"><?php echo $review; ?></textarea>
                </div>
              </div>
              <div class="col-md-3 col-sm-4 col-7">
                <button type="submit" name="submit" class="btn btn-outline-success mybtn btn-block">Submit&nbsp;<i class="fas fa-paper-plane"></i></button>
              </div>
            </div>
          </form>
        <?php }; ?>
      </div>
    </div>
  </div>
</div>

<?php include "include/footer.php"; ?>