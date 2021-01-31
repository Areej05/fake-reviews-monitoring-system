<?php include 'include/header.php';
if (isset($_SESSION['user'])) {
  header("Location: index.php");
}


$email = $password = "";

if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  if (empty($_POST['email'])) {
    Notification("Email Field is Empty !", "3", $notify_icons['error']);
  } elseif (empty($_POST['password'])) {
    Notification("Password Field is Empty !", "3", $notify_icons['error']);
  } else {

    $query = "SELECT * FROM users WHERE email = '$email'";
    $run = mysqli_query($con, $query);
    $row = mysqli_fetch_array($run);
    $status = $row['status'];
    if (mysqli_num_rows($run) != 1) {
      Notification("Email Does Not Exist. Please Register First", "3", $notify_icons['error']);
    } else {
      $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
      if ($status == 0) {
        Notification("Your Registration Request is Pending !", "3", $notify_icons['error']);
      } else {
        $run = mysqli_query($con, $query);
        if (mysqli_num_rows($run) == 1) {
          $row = mysqli_fetch_array($run);
          $_SESSION['user'] = $row['id'];
          $_SESSION['name'] = $row['name'];
          header("Location: index.php?login=1");
        } else {
          Notification("Email or Password is Incorrect !", "3", $notify_icons['error']);
        }
      }
    }
  }
}
?>

<div class="row my-5 justify-content-center">
  <div class="col-md-6">
    <div class="card bounceIn">
      <h4 class="card-header bg-success text-center text-white font-weight-bold ">Login</h4>
      <div class="card-body">
        <form class="form" action="" method="post">
          <div class="row justify-content-center">
            <div class="col-md-12">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo $email; ?>" autofocus>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" value="<?php echo $password; ?>">
              </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-5 col-8 mt-2">
              <button type="submit" name="login" class="btn btn-outline-success btn-block mybtn">Login&nbsp;<i class="fas fa-sign-in-alt"></i></button>
            </div>
            <div class="col-md-12 text-center mx-auto mt-3">
              <span class="span">Not Yet Registered ? <a href="register.php">Click Here</a> to Register</span>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<?php include 'include/footer.php'; ?>