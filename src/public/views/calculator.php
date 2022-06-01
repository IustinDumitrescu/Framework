<?php include 'globals/header.php' ?>
<style>
    .custom_padding-all {
        padding: 30px;
    }
</style>


<div class="container">
    <div style="margin: 2em auto; text-align: center">
        <h1>Calculator</h1>
    </div>

    <div class="container pt-1">
      <div class="col-xl-6 col-lg-7 col-md-9 col-12 mx-auto">
          <div class='bg-white shadow rounded custom_padding-all'>
          <?php if (isset($flash)) {
              if ($flash["flashType"]) {
                  echo "
                    <div class=\"alert alert-success\" role=\"alert\">
                    {$flash["flashString"]}
                    </div>
                    ";
              } else {
                  echo "<div class=\"alert alert-danger\" role=\"alert\">
                     {$flash["flashString"]}
                    </div>
                    ";
              }
          } ?>
        <?php echo $formCalculator["form"]; ?>
      </div>
      </div>
    </div>

</div>

<?php include 'globals/footer.php' ?>