<?php require_once "../middlewares/auth.middleware.php"; ?>
<!DOCTYPE html>
<html lang="zxx" class="js">
<?php include 'head.php' ?>

<body class="nk-body ui-rounder has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <?php include 'sidebar.php' ?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <?php include 'header.php' ?>
                <!-- main header @e -->
                <!-- content @s -->
                <?php if (isset($_GET['item'])): ?>
                    <?php include('./resource/item.php') ?>
                <?php else: ?>
                    <?php include('main.php') ?>
                <?php endif; ?>
                <!-- content @e -->
                <!-- footer @s -->
                <?php include 'footer.php' ?>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- select region modal -->

    <!-- JavaScript -->
    <!-- Sebelum closing body tag -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>tr
    <script src="./assets/js/bundle.js?ver=3.2.2"></script>
    <script src="./assets/js/scripts.js?ver=3.2.2"></script>
    <script src="./assets/js/charts/gd-campaign.js?ver=3.2.2"></script>
</body>

</html>