 <?php include_once 'php_action/core.php';
  @$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1 "));

  ?>

 <head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta name="description" content="a software deleveloped by Sam'z Creations">
   <meta name="author" content="Samz Creations">
   <link rel="icon" href="img/logo/<?= $get_company['logo'] ?>" type="image/gif" sizes="16x16">
   <title><?= $get_company['name'] ?></title>
   <!-- Simple bar CSS -->
   <link rel="stylesheet" href="css/simplebar.css">
   <!-- Fonts CSS -->
   <!--   <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> -->
   <!-- Icons CSS -->
   <link rel="stylesheet" href="css/fontawesome.min.css">
   <link rel="stylesheet" href="css/feather.css">
   <link rel="stylesheet" href="css/phoenix-sans.css">
   <link rel="stylesheet" href="css/select2.css">
   <link rel="stylesheet" href="css/dropzone.css">
   <link rel="stylesheet" href="css/uppy.min.css">
   <link rel="stylesheet" href="css/jquery.steps.css">
   <link rel="stylesheet" href="css/jquery.timepicker.css">
   <link rel="stylesheet" href="css/quill.snow.css">
   <!-- Date Range Picker CSS -->
   <link rel="stylesheet" href="css/daterangepicker.css">
   <!-- App CSS -->
   <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
   <link rel="stylesheet" href="css/app-dark.css" id="darkTheme" disabled>
   <script src="js/sweetalert2.min.js"></script>
   <script src="js/all.min.js"></script>
   <link rel="stylesheet" href="css/sweetalert2.min.css">
   <link rel="stylesheet" href="css/dataTables.bootstrap4.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <script>
     if (window.history.replaceState) {
       window.history.replaceState(null, null, window.location.href);
     }
   </script>
   <style type="text/css">
     .btn-admin,
     .bg-admin {
       color: #ffffff;
       background-color: #ff1a1a;
       border-color: #ff1a1a;
     }

     .btn-admin2,
     .bg-admin2 {
       color: white;
       background-color: black;
       border-color: black;
     }

     .card-bg {

       background-color: black;

     }

     .card-text {
       color: white;
     }
  /* Wrapper for spacing */
  .dropdown-wrapper {
    display: flex;
    flex-direction: column;
    gap: 4px;
    max-width: 200px;
  }

  /* Label styling */
  .dropdown-label {
    font-weight: 500;
    margin-bottom: 4px;
    font-size: 14px;
  }

  /* Custom dropdown */
  .custom-dropdown {
    appearance: none;
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    color: #333;
    cursor: pointer;
    outline: none;
    background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%204%205'%3E%3Cpath%20fill='%23333'%20d='M2%205L0%200h4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 8px 10px;
  }

  /* On focus or hover */
  .custom-dropdown:focus,
  .custom-dropdown:hover {
    border-color: #007bff;
    background-color: #e6f0ff;
  }
</style>
 </head>